<?php
ini_set('display_errors', 0); // ปิด Warning/Notice เพื่อไม่ให้ JSON พัง
require_once("./db/connect_pmk.php");
header('Content-Type: application/json; charset=utf-8');

$columns = array(
    0 => "XRAY_CODE",
    1 => "XRAY_CODE",
    2 => "REF_CODE",
    3 => "CODE_30",
    4 => "NAME",
    5 => "XRAY_CODE", // CHECK button index
    6 => "FUND_UNIT_PRICE",
    7 => "COPAY_UNIT_PRICE",
    8 => "MIN_PRICE",
    9 => "MAX_PRICE"
);

$start      = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length     = isset($_GET['length']) ? intval($_GET['length']) : 10;
$search     = isset($_GET['search']['value']) ? strtoupper($_GET['search']['value']) : '';
$order_col  = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 1;
$order_dir  = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';
$order_by   = $columns[$order_col];

// เงื่อนไขกรอง
$where = "WHERE DEL_FLAG IS NULL";
if (!empty($search)) {
    $where .= " AND (UPPER(XRAY_CODE) LIKE '%" . $search . "%' OR UPPER(NAME) LIKE '%" . $search . "%')";
}

// ---------- นับจำนวน ----------
$sql_total = "SELECT COUNT(*) AS CNT FROM XRAY_CODES $where";
$stmt_total = oci_parse($objConnect, $sql_total);
if (!$stmt_total || !oci_execute($stmt_total)) {
    $e = oci_error($stmt_total ?: $objConnect);
    echo json_encode(["status"=>"error","message"=>$e['message']]);
    exit;
}
$row_total = oci_fetch_array($stmt_total, OCI_ASSOC);
$totalData = isset($row_total['CNT']) ? intval($row_total['CNT']) : 0;

// ---------- ดึงข้อมูล ----------
$sql = "
    SELECT * FROM (
        SELECT ROWNUM AS RN, A.* FROM (
            SELECT XRAY_CODE, NAME, FUND_UNIT_PRICE, COPAY_UNIT_PRICE, MIN_PRICE, MAX_PRICE, REF_CODE, CODE_30
            FROM XRAY_CODES
            $where
            ORDER BY $order_by $order_dir
        ) A WHERE ROWNUM <= :maxrow
    ) WHERE RN > :minrow
";
$stmt = oci_parse($objConnect, $sql);
if (!$stmt) {
    $e = oci_error($objConnect);
    echo json_encode(["status"=>"error","message"=>$e['message']]);
    exit;
}
$maxrow = $start + $length;
$minrow = $start;
oci_bind_by_name($stmt, ":maxrow", $maxrow);
oci_bind_by_name($stmt, ":minrow", $minrow);

if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    echo json_encode(["status"=>"error","message"=>$e['message']]);
    exit;
}

$data = array();
$i = $start + 1;
while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $data[] = array(
        str_pad($i++, 5, "0", STR_PAD_LEFT),
        isset($row['XRAY_CODE']) ? $row['XRAY_CODE'] : '',
        isset($row['REF_CODE']) ? $row['REF_CODE'] : '',
        isset($row['CODE_30']) ? $row['CODE_30'] : '',
        isset($row['NAME']) ? $row['NAME'] : '',
        // ปุ่ม CHECK จะ render ใน JS
        isset($row['FUND_UNIT_PRICE']) ? number_format((float)$row['FUND_UNIT_PRICE'], 2) : '',
        isset($row['COPAY_UNIT_PRICE']) ? number_format((float)$row['COPAY_UNIT_PRICE'], 2) : '',
        isset($row['MIN_PRICE']) ? number_format((float)$row['MIN_PRICE'], 2) : '',
        isset($row['MAX_PRICE']) ? number_format((float)$row['MAX_PRICE'], 2) : ''
    );
}

// ---------- ตอบกลับ JSON ----------
echo json_encode(array(
    "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
    "recordsTotal" => $totalData,
    "recordsFiltered" => $totalData,
    "data" => $data
));
?>
