<?php
require_once("./db/connect_pmk.php");
header('Content-Type: application/json; charset=utf-8');

// รับค่าจาก DataTables
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$searchValue = isset($_GET['search']['value']) ? trim($_GET['search']['value']) : '';
$orderColumnIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 2;
$orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';

// Mapping คอลัมน์จาก DataTables มาเป็นชื่อคอลัมน์จริงใน Oracle
$columns = [
    0 => 'OPER_CODE',
    1 => 'OPER_CODE',
    2 => 'NAME',
    3 => 'OPER_CODE', // ตรวจสอบ ไม่มีจริง
    4 => 'FUND_UNIT_PRICE',
    5 => 'COPAY_UNIT_PRICE',
    6 => 'MIN_PRICE',
    7 => 'MAX_PRICE'
];

$orderColumn = $columns[$orderColumnIndex];

// เงื่อนไขค้นหา
$searchSQL = "";
if ($searchValue != '') {
    $searchText = strtoupper($searchValue);
    $searchSQL = "AND (
        UPPER(OPER_CODE) LIKE '%$searchText%' OR 
        UPPER(NAME) LIKE '%$searchText%'
    )";
}

// Query สำหรับนับจำนวนทั้งหมด
$sqlTotal = "SELECT COUNT(*) AS CNT FROM OPERATION_CODES WHERE DEL_FLAG IS NULL";
$objTotal = oci_parse($objConnect, $sqlTotal);
oci_execute($objTotal);
$totalRecords = oci_fetch_assoc($objTotal)['CNT'];

// Query สำหรับนับจำนวนที่ค้นหาแล้ว
$sqlFiltered = "SELECT COUNT(*) AS CNT FROM OPERATION_CODES WHERE DEL_FLAG IS NULL $searchSQL";
$objFiltered = oci_parse($objConnect, $sqlFiltered);
oci_execute($objFiltered);
$filteredRecords = oci_fetch_assoc($objFiltered)['CNT'];

// Query หลัก (มีค้นหา + order + paging)
$sqlMain = "
    SELECT * FROM (
        SELECT a.*, ROWNUM rnum FROM (
            SELECT OPER_CODE, NAME, FUND_UNIT_PRICE, COPAY_UNIT_PRICE, MIN_PRICE, MAX_PRICE, REF_CODE
            FROM OPERATION_CODES
            WHERE DEL_FLAG IS NULL $searchSQL
            ORDER BY $orderColumn $orderDir
        ) a
        WHERE ROWNUM <= " . ($start + $length) . "
    )
    WHERE rnum > $start
";

$objParse = oci_parse($objConnect, $sqlMain);
oci_execute($objParse);

$data = [];
$i = $start + 1;
while ($rs = oci_fetch_array($objParse, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $data[] = [
        str_pad($i++, 5, "0", STR_PAD_LEFT),
        htmlspecialchars($rs['OPER_CODE']),
        htmlspecialchars($rs['NAME']),
        // ปุ่ม CHECK จะ render ใน JS (sys_process_pmk_oper.php) โดยอิงจากรหัส
        number_format((float)$rs['FUND_UNIT_PRICE'], 2),
        number_format((float)$rs['COPAY_UNIT_PRICE'], 2),
        number_format((float)$rs['MIN_PRICE'], 2),
        number_format((float)$rs['MAX_PRICE'], 2)
    ];
}

// ส่งกลับผลลัพธ์ในรูปแบบ DataTables
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($filteredRecords),
    "data" => $data
]);
?>
