<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("./db/connect_pmk.php");
header('Content-Type: application/json');
$columns = array(
    0 => "CODE",
    1 => "CODE",
    2 => "NAME",
    3 => "FUND_UNIT_PRICE",
    4 => "INTEND_FUND_UNIT_PRICE",
    5 => "SELL_UNIT_PRICE",
    6 => "IPD_SELL_UNIT_PRICE"
);

// ใช้ isset แทน ?? เพื่อรองรับ PHP5
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$search = isset($_GET['search']['value']) ? strtoupper($_GET['search']['value']) : '';
$order_col = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 1;
$order_dir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';
$order_by = $columns[$order_col];

// กรอง DEL_FLAG
$where = "WHERE DEL_FLAG IS NULL";
if (!empty($search)) {
    $where .= " AND (UPPER(CODE) LIKE '%" . $search . "%' OR UPPER(NAME) LIKE '%" . $search . "%')";
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
   SELECT CODE, NAME, FUND_UNIT_PRICE, INTEND_FUND_UNIT_PRICE, SELL_UNIT_PRICE, IPD_SELL_UNIT_PRICE
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
    $data[] =array(
        str_pad($i++, 5, "0", STR_PAD_LEFT),
        isset($row['CODE']) ? $row['CODE'] : '',
        isset($row['NAME']) ? $row['NAME'] : '',
        isset($row['FUND_UNIT_PRICE']) ? number_format((float)$row['FUND_UNIT_PRICE'], 2) : '',
        isset($row['INTEND_FUND_UNIT_PRICE']) ? number_format((float)$row['INTEND_FUND_UNIT_PRICE'], 2) : '',
        isset($row['SELL_UNIT_PRICE']) ? number_format((float)$row['SELL_UNIT_PRICE'], 2) : '',
        isset($row['IPD_SELL_UNIT_PRICE']) ? number_format((float)$row['IPD_SELL_UNIT_PRICE'], 2) : ''
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