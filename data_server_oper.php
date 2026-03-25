<?php
require_once("./db/connect_pmk.php");

$draw = $_GET['draw'];
$start = $_GET['start'];
$length = $_GET['length'];
$search = strtoupper($_GET['search']['value']);

$query_base = "SELECT OPER_CODE, NAME, MIN_PRICE, MAX_PRICE FROM OPERATION_CODES WHERE DEL_FLAG IS NULL";
$query_filtered = $query_base;
$query_total = $query_base;

if (!empty($search)) {
    $query_filtered .= " AND (UPPER(OPER_CODE) LIKE '%$search%' OR UPPER(NAME) LIKE '%$search%')";
}

$sql_total = oci_parse($objConnect, $query_total);
oci_execute($sql_total);
$totalRecords = 0;
while (oci_fetch_array($sql_total)) {
    $totalRecords++;
}

$sql_filtered = oci_parse($objConnect, $query_filtered);
oci_execute($sql_filtered);

$data = [];
$i = $start + 1;

while ($row = oci_fetch_array($sql_filtered, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $data[] = [
        str_pad($i++, 5, "0", STR_PAD_LEFT),
        $row['OPER_CODE'],
        $row['NAME'],
        isset($row['FUND_UNIT_PRICE']) ? number_format((float)$row['FUND_UNIT_PRICE'], 2) : '',
        isset($row['COPAY_UNIT_PRICE']) ? number_format((float)$row['COPAY_UNIT_PRICE'], 2) : '',
        isset($row['MIN_PRICE']) ? number_format((float)$row['MIN_PRICE'], 2) : '',
        isset($row['MAX_PRICE']) ? number_format((float)$row['MAX_PRICE'], 2) : ''    ];
}

$response = [
    "draw" => intval($draw),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => count($data),
    "data" => $data
];

header('Content-Type: application/json');
echo json_encode($response);
?>