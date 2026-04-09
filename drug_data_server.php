<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("./db/connect_pmk.php");
header('Content-Type: application/json');
$columns = array(
    0 => "CODE",
    1 => "CODE",
    2 => "REF_CODE",
    3 => "CODE_30",
    4 => "DRUG_STD_CODE",
    5 => "NAME",
    6 => "CODE", // CHECK button index
    7 => "FUND_UNIT_PRICE",
    8 => "INTEND_FUND_UNIT_PRICE",
    9 => "SELL_UNIT_PRICE",
    10 => "IPD_SELL_UNIT_PRICE"
);

// ใช้ isset แทน ?? เพื่อรองรับ PHP5
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$search = isset($_GET['search']['value']) ? strtoupper($_GET['search']['value']) : '';
$order_col = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 1;
$order_dir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';
$order_by = $columns[$order_col];

// รับค่าจากตัวกรองคอลัมน์
$filterRefCode = isset($_GET['filterRefCode']) ? strtoupper($_GET['filterRefCode']) : '';
$filterCode30 = isset($_GET['filterCode30']) ? strtoupper($_GET['filterCode30']) : '';
$filterEmptyRef = isset($_GET['filterEmptyRef']) ? $_GET['filterEmptyRef'] : '';
$filterEmptyCode30 = isset($_GET['filterEmptyCode30']) ? $_GET['filterEmptyCode30'] : '';
$filterEmptyDrugStd = isset($_GET['filterEmptyDrugStd']) ? $_GET['filterEmptyDrugStd'] : '';

// กรอง DEL_FLAG
$where = "WHERE DEL_FLAG IS NULL";
if (!empty($search)) {
    $where .= " AND (UPPER(CODE) LIKE '%" . $search . "%' OR UPPER(NAME) LIKE '%" . $search . "%')";
}

// กรองตามคอลัมน์รหัสกรมบัญชีกลาง
if (!empty($filterRefCode)) {
    $where .= " AND UPPER(REF_CODE) LIKE '%" . $filterRefCode . "%'";
}

// กรองตามคอลัมน์รหัสสปสช
if (!empty($filterCode30)) {
    $where .= " AND UPPER(CODE_30) LIKE '%" . $filterCode30 . "%'";
}

// กรองค่าว่างรหัสกรมบัญชีกลาง
if ($filterEmptyRef === 'true') {
    $where .= " AND (REF_CODE IS NULL OR REF_CODE = '')";
}

// กรองค่าว่างรหัสสปสช
if ($filterEmptyCode30 === 'true') {
    $where .= " AND (CODE_30 IS NULL OR CODE_30 = '')";
}

// กรองค่าว่างรหัสยา 24 หลัก
if ($filterEmptyDrugStd === 'true') {
    $where .= " AND (DRUG_STD_CODE IS NULL OR DRUG_STD_CODE = '')";
}

// นับจำนวนทั้งหมด
$sql_total = "SELECT COUNT(*) AS CNT FROM DRUGCODES $where";
$stmt_total = oci_parse($objConnect, $sql_total);
oci_execute($stmt_total);
$row_total = oci_fetch_array($stmt_total, OCI_ASSOC);
$totalData = isset($row_total['CNT']) ? intval($row_total['CNT']) : 0;

$sql = "
SELECT * FROM (
 SELECT ROWNUM AS RN, A.* FROM (
   SELECT CODE, NAME, FUND_UNIT_PRICE, INTEND_FUND_UNIT_PRICE, SELL_UNIT_PRICE, IPD_SELL_UNIT_PRICE, REF_CODE, CODE_30, DRUG_STD_CODE
        FROM DRUGCODES 
        $where
        ORDER BY $order_by $order_dir
    ) A WHERE ROWNUM <=:maxrow
) WHERE RN > :minrow";

$stmt = oci_parse($objConnect, $sql);
$maxrow = $start + $length;
$minrow = $start;
oci_bind_by_name($stmt, ":maxrow", $maxrow);
oci_bind_by_name($stmt, ":minrow", $minrow);
oci_execute($stmt);

$data = array();
$i = $start + 1;
while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $data[] = array(
        0 => str_pad($i++, 5, "0", STR_PAD_LEFT),
        1 => isset($row['CODE']) ? $row['CODE'] : '',
        2 => isset($row['REF_CODE']) ? $row['REF_CODE'] : '',
        3 => isset($row['CODE_30']) ? $row['CODE_30'] : '',
        4 => isset($row['DRUG_STD_CODE']) ? $row['DRUG_STD_CODE'] : '',
        5 => isset($row['NAME']) ? $row['NAME'] : '',
        6 => isset($row['FUND_UNIT_PRICE']) ? number_format((float)$row['FUND_UNIT_PRICE'], 2) : '',
        7 => isset($row['INTEND_FUND_UNIT_PRICE']) ? number_format((float)$row['INTEND_FUND_UNIT_PRICE'], 2) : '',
        8 => isset($row['SELL_UNIT_PRICE']) ? number_format((float)$row['SELL_UNIT_PRICE'], 2) : '',
        9 => isset($row['IPD_SELL_UNIT_PRICE']) ? number_format((float)$row['IPD_SELL_UNIT_PRICE'], 2) : ''
    );
}

// ตอบกลับ JSON
echo json_encode(array(
    "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
    "recordsTotal" => $totalData,
    "recordsFiltered" => $totalData,
    "data" => $data
));
?>