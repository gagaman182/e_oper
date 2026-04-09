<?php
ob_start();
require_once("./db/connect_pmk.php");
ob_clean();

$searchValue     = isset($_GET['search'])          ? strtoupper(trim($_GET['search']))       : '';
$filterExtra     = isset($_GET['filterExtra'])     ? strtoupper(trim($_GET['filterExtra']))  : '';
$filterEmptyCode = isset($_GET['filterEmptyCode']) ? $_GET['filterEmptyCode']                : '';

$searchSQL = "";
if ($searchValue != '') {
    $searchSQL .= " AND (UPPER(MISC_CODE) LIKE '%$searchValue%' OR UPPER(NAME) LIKE '%$searchValue%')";
}
if (!empty($filterExtra)) {
    $searchSQL .= " AND (UPPER(MISC_CODE) LIKE '%$filterExtra%' OR UPPER(NAME) LIKE '%$filterExtra%')";
}
if ($filterEmptyCode === 'true') {
    $searchSQL .= " AND (MISC_CODE IS NULL OR MISC_CODE = '')";
}

$sql = "SELECT MISC_CODE, NAME, FUND_UNIT_PRICE, COPAY_UNIT_PRICE, MIN_PRICE, MAX_PRICE
        FROM MISC_CODES
        WHERE DEL_FLAG IS NULL $searchSQL
        ORDER BY NAME ASC";

$stmt = oci_parse($objConnect, $sql);
if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    die('Query Error: ' . $e['message']);
}

$filename = 'export_misc_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, array('#', 'รหัส', 'ชื่อรายการ', 'ราคาทุน', 'ร่วมจ่าย', 'ต่ำสุด', 'สูงสุด'));

$i = 1;
while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)) {
    fputcsv($output, array(
        $i++,
        isset($row['MISC_CODE']) ? $row['MISC_CODE'] : '',
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
