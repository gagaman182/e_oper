<?php
require_once('./db/connect_pmk.php');

$type = isset($_GET['type']) ? $_GET['type'] : '';
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$search = isset($_GET['search']['value']) ? strtoupper($_GET['search']['value']) : '';

// กำหนดการ mapping ชื่อตาราง, CODE, NAME
$table_map = [
    'L' => ['table' => 'LABCODES', 'code' => 'LABCODE', 'name' => 'LABNAME'],
    'X' => ['table' => 'XRAY_CODES', 'code' => 'XRAY_CODE', 'name' => 'NAME'],
    'P' => ['table' => 'OPERATION_CODES', 'code' => 'OPER_CODE', 'name' => 'NAME'],
    'S' => ['table' => 'SERVICE_CODES', 'code' => 'CODE', 'name' => 'NAME'],
    'D' => ['table' => 'DRUGCODES', 'code' => 'CODE', 'name' => 'NAME'],
    'O' => ['table' => 'MISC_CODES', 'code' => 'MISC_CODE', 'name' => 'NAME']
];

if (!isset($table_map[$type])) {
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => []
    ]);
    exit;
}

$map = $table_map[$type];
$table = $map['table'];
$code_col = $map['code'];
$name_col = $map['name'];

// ฟิลด์ copay อาจเปลี่ยนใน DRUGCODES
$copay_field = ($type === 'D') ? 'INTEND_FUND_UNIT_PRICE' : 'COPAY_UNIT_PRICE';
// ฟิลด์ min/max อาจเปลี่ยนใน DRUGCODES
$min_field = ($type === 'D') ? 'SELL_UNIT_PRICE' : 'MIN_PRICE';
$max_field = ($type === 'D') ? 'IPD_SELL_UNIT_PRICE' : 'MAX_PRICE';

$base_query = "
    SELECT $code_col AS CODE, REF_CODE, $name_col AS NAME,
           FUND_UNIT_PRICE, $copay_field AS COPAY_UNIT_PRICE, $min_field AS MIN_PRICE, $max_field AS MAX_PRICE
    FROM $table
    WHERE DEL_FLAG IS NULL
";

if (!empty($search)) {
    $search_esc = '%' . $search . '%';
    $base_query .= " AND (UPPER($code_col) LIKE :search OR UPPER($name_col) LIKE :search)";
}

// นับทั้งหมด
$sql_count = oci_parse($objConnect, "SELECT COUNT(*) AS TOTAL FROM ({$base_query})");

if (!empty($search)) {
    oci_bind_by_name($sql_count, ':search', $search_esc);
}
oci_execute($sql_count);
$row_total = oci_fetch_array($sql_count, OCI_ASSOC + OCI_RETURN_NULLS);
$totalRecords = intval($row_total['TOTAL']);

// ทำการแบ่งหน้า
$sql_page = "
    SELECT * FROM (
        SELECT ROWNUM RN, A.* FROM (
            {$base_query} ORDER BY $code_col
        ) A WHERE ROWNUM <= :max_row
    ) WHERE RN > :min_row
";

$stmt = oci_parse($objConnect, $sql_page);
$max_row = $start + $length;
$min_row = $start;

oci_bind_by_name($stmt, ':max_row', $max_row);
oci_bind_by_name($stmt, ':min_row', $min_row);
if (!empty($search)) {
    oci_bind_by_name($stmt, ':search', $search_esc);
}
oci_execute($stmt);

$data = [];
$i = $start + 1;

while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $data[] = [
        'no' => str_pad($i++, 5, "0", STR_PAD_LEFT),
        'code' => $row['CODE'],
        'ref_code' => isset($row['REF_CODE']) ? $row['REF_CODE'] : '',
        'name' => $row['NAME'],
        'fund_price' => number_format((float)$row['FUND_UNIT_PRICE'], 2),
        'copay_price' => number_format((float)$row['COPAY_UNIT_PRICE'], 2),
        'min_price' => number_format((float)$row['MIN_PRICE'], 2),
        'max_price' => number_format((float)$row['MAX_PRICE'], 2),
        'type' => $type
    ];
}

echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data
]);
?>
