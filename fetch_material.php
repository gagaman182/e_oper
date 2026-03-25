<?php
include('connect_oracle.php');

$group = isset($_POST['group']) ? $_POST['group'] : '';
$map = [
  'lab' => "SELECT LABCODE AS CODE, LABNAME AS NAME, '' AS REQUESTED_UNIT, REF_CODE, FUND_UNIT_PRICE, MIN_PRICE AS MAX_PRICE FROM LABCODES WHERE DEL_FLAG IS NULL",
  'xray' => "SELECT XRAY_CODE AS CODE, NAME, '' AS REQUESTED_UNIT, REF_CODE, FUND_UNIT_PRICE, MAX_PRICE FROM XRAY_CODES WHERE DEL_FLAG IS NULL",
  'drug' => "SELECT CODE, NAME, DUC1_UNIT_CODE AS REQUESTED_UNIT, REF_CODE, FUND_UNIT_PRICE, SELL_UNIT_PRICE AS MAX_PRICE FROM DRUGCODES WHERE DEL_FLAG IS NULL",
  'surgery' => "SELECT OPER_CODE AS CODE, NAME, '' AS REQUESTED_UNIT, REF_CODE, FUND_UNIT_PRICE, MAX_PRICE FROM OPERATION_CODES WHERE DEL_FLAG IS NULL",
  'service' => "SELECT CODE, NAME, '' AS REQUESTED_UNIT, REF_CODE, FUND_UNIT_PRICE, MAX_PRICE FROM SERVICE_CODES WHERE DEL_FLAG IS NULL",
  'other' => "SELECT MISC_CODE AS CODE, NAME, '' AS REQUESTED_UNIT, REF_CODE, FUND_UNIT_PRICE, MAX_PRICE FROM MISC_CODES WHERE DEL_FLAG IS NULL"
];

if (!isset($map[$group])) {
  echo '<tr><td colspan="6" class="text-center text-warning">กลุ่มรายการไม่ถูกต้อง</td></tr>';
  exit;
}

$sql = $map[$group];
$stm = oci_parse($conn_ora, $sql);
oci_execute($stm);

while ($row = oci_fetch_assoc($stm)) {
  echo '<tr>';
  echo '<td>' . htmlspecialchars($row['CODE']) . '</td>';
  echo '<td>' . htmlspecialchars($row['NAME']) . '</td>';
  echo '<td>' . htmlspecialchars($row['REQUESTED_UNIT']) . '</td>';
  echo '<td>' . htmlspecialchars($row['REF_CODE']) . '</td>';
  echo '<td class="text-end">' . number_format($row['FUND_UNIT_PRICE'], 2) . '</td>';
  echo '<td class="text-end">' . number_format($row['MAX_PRICE'], 2) . '</td>';
  echo '</tr>';
}
oci_free_statement($stm);
oci_close($conn_ora);
?>
