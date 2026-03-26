<?php
ob_start();
require_once("./db/connect_pmk.php");
ob_clean();
header('Content-Type: application/json; charset=utf-8');

// รับค่าจาก DataTables
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$searchValue = isset($_GET['search']['value']) ? trim($_GET['search']['value']) : '';
$orderColumnIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 2;
$orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';

// รับค่าจากตัวกรองคอลัมน์
$filterRefCode = isset($_GET['filterRefCode']) ? strtoupper($_GET['filterRefCode']) : '';
$filterCode30 = isset($_GET['filterCode30']) ? strtoupper($_GET['filterCode30']) : '';
$filterEmptyRef = isset($_GET['filterEmptyRef']) ? $_GET['filterEmptyRef'] : '';
$filterEmptyCode30 = isset($_GET['filterEmptyCode30']) ? $_GET['filterEmptyCode30'] : '';

// Mapping คอลัมน์จาก DataTables มาเป็นชื่อคอลัมน์จริงใน Oracle
$columns = [
    0 => 'OPER_CODE',
    1 => 'OPER_CODE',
    2 => 'REF_CODE',
    3 => 'CODE_30',
    4 => 'NAME',
    5 => 'OPER_CODE', // ปุ่ม CHECK ไม่มีจริง
    6 => 'FUND_UNIT_PRICE',
    7 => 'COPAY_UNIT_PRICE',
    8 => 'MIN_PRICE',
    9 => 'MAX_PRICE'
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

// กรองตามคอลัมน์รหัสกรมบัญชีกลาง
if (!empty($filterRefCode)) {
    $searchSQL .= " AND UPPER(REF_CODE) LIKE '%$filterRefCode%'";
}

// กรองตามคอลัมน์รหัสสปสช
if (!empty($filterCode30)) {
    $searchSQL .= " AND UPPER(CODE_30) LIKE '%$filterCode30%'";
}

// กรองค่าว่างรหัสกรมบัญชีกลาง
if ($filterEmptyRef === 'true') {
    $searchSQL .= " AND (REF_CODE IS NULL OR REF_CODE = '')";
}

// กรองค่าว่างรหัสสปสช
if ($filterEmptyCode30 === 'true') {
    $searchSQL .= " AND (CODE_30 IS NULL OR CODE_30 = '')";
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
            SELECT OPER_CODE, NAME, FUND_UNIT_PRICE, COPAY_UNIT_PRICE, MIN_PRICE, MAX_PRICE, REF_CODE, CODE_30
            FROM OPERATION_CODES
            WHERE DEL_FLAG IS NULL $searchSQL
            ORDER BY $orderColumn $orderDir
        ) a
        WHERE ROWNUM <= " . ($start + $length) . "
    )
    WHERE rnum > $start
";

$objParse = oci_parse($objConnect, $sqlMain);
if (!oci_execute($objParse)) {
    $e = oci_error($objParse);
    echo json_encode(["draw" => $draw, "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => $e['message']]);
    exit;
}

$data = [];
$i = $start + 1;
while ($rs = oci_fetch_array($objParse, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $data[] = [
        str_pad($i++, 5, "0", STR_PAD_LEFT),
        htmlspecialchars($rs['OPER_CODE']),
        htmlspecialchars(isset($rs['REF_CODE']) ? $rs['REF_CODE'] : ''),
        htmlspecialchars(isset($rs['CODE_30']) ? $rs['CODE_30'] : ''),
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
