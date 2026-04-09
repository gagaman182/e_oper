<?php
ob_start();
require_once("./db/connect_pmk.php");
ob_clean();

$searchValue   = isset($_GET['search'])              ? strtoupper(trim($_GET['search']))        : '';
$filterRefCode = isset($_GET['filterRefCode'])       ? strtoupper(trim($_GET['filterRefCode'])) : '';
$filterCode30  = isset($_GET['filterCode30'])        ? strtoupper(trim($_GET['filterCode30']))  : '';
$filterEmptyRef    = isset($_GET['filterEmptyRef'])    ? $_GET['filterEmptyRef']    : '';
$filterEmptyCode30 = isset($_GET['filterEmptyCode30']) ? $_GET['filterEmptyCode30'] : '';

$searchSQL = "";
if ($searchValue != '') {
    $searchSQL .= " AND (UPPER(OPER_CODE) LIKE '%$searchValue%' OR UPPER(NAME) LIKE '%$searchValue%')";
}
if (!empty($filterRefCode)) {
    $searchSQL .= " AND UPPER(REF_CODE) LIKE '%$filterRefCode%'";
}
if (!empty($filterCode30)) {
    $searchSQL .= " AND UPPER(CODE_30) LIKE '%$filterCode30%'";
}
if ($filterEmptyRef === 'true') {
    $searchSQL .= " AND (REF_CODE IS NULL OR REF_CODE = '')";
}
if ($filterEmptyCode30 === 'true') {
    $searchSQL .= " AND (CODE_30 IS NULL OR CODE_30 = '')";
}

$sql = "SELECT OPER_CODE, REF_CODE, CODE_30, NAME, FUND_UNIT_PRICE, COPAY_UNIT_PRICE, MIN_PRICE, MAX_PRICE
        FROM OPERATION_CODES
        WHERE DEL_FLAG IS NULL $searchSQL
        ORDER BY REF_CODE ASC";

$stmt = oci_parse($objConnect, $sql);
if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    die('Query Error: ' . $e['message']);
}

$filename = 'export_oper_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, array('#', 'รหัส', 'รหัสกรมบัญชีกลาง', 'รหัสสปสช', 'ชื่อรายการ', 'ราคาทุน', 'ร่วมจ่าย', 'ต่ำสุด', 'สูงสุด'));

$i = 1;
while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $refCode = isset($row['REF_CODE']) ? $row['REF_CODE'] : '';
    $code30  = isset($row['CODE_30'])  ? $row['CODE_30']  : '';
    fputcsv($output, array(
        $i++,
        isset($row['OPER_CODE']) ? $row['OPER_CODE'] : '',
        ($refCode !== '') ? '="' . $refCode . '"' : '',
        ($code30  !== '') ? '="' . $code30  . '"' : '',
        isset($row['NAME'])      ? $row['NAME']      : '',
        number_format((float)(isset($row['FUND_UNIT_PRICE'])  ? $row['FUND_UNIT_PRICE']  : 0), 2),
        number_format((float)(isset($row['COPAY_UNIT_PRICE']) ? $row['COPAY_UNIT_PRICE'] : 0), 2),
        number_format((float)(isset($row['MIN_PRICE'])        ? $row['MIN_PRICE']        : 0), 2),
        number_format((float)(isset($row['MAX_PRICE'])        ? $row['MAX_PRICE']        : 0), 2),
    ));
}

fclose($output);
exit;
?>
