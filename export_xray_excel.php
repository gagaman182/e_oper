<?php
require_once("./db/connect_pmk.php");

require __DIR__ . '/vendor/autoload.php'; // หากใช้ Composer โหลด PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$conn = oci_connect($oracle_username, $oracle_password, $oracle_server_name, 'AL32UTF8');
if (!$conn) {
    die("Oracle connection failed: " . oci_error());
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Xray List');

// ตั้งหัวคอลัมน์
$headers = ['ลำดับ', 'รหัส', 'ชื่อรายการ', 'ราคาเริ่มต้น', 'ราคาสูงสุด'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

// ดึงข้อมูล
$sql = "SELECT XRAY_CODE, NAME, MIN_PRICE, MAX_PRICE 
        FROM XRAY_CODES 
        WHERE DEL_FLAG IS NULL 
        ORDER BY NAME";
$stm = oci_parse($conn, $sql);
oci_execute($stm);

$row = 2;
$index = 1;

while ($data = oci_fetch_array($stm, OCI_ASSOC)) {
    $sheet->setCellValue("A$row", str_pad($index++, 5, "0", STR_PAD_LEFT));
    $sheet->setCellValue("B$row", $data['XRAY_CODE']);
    $sheet->setCellValue("C$row", $data['NAME']);
    $sheet->setCellValue("D$row", $data['MIN_PRICE']);
    $sheet->setCellValue("E$row", $data['MAX_PRICE']);
    $row++;
}

oci_free_statement($stm);
oci_close($conn);

// ตั้งค่าหัวข้อไฟล์ดาวน์โหลด
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="xray_export.xlsx"');
header('Cache-Control: max-age=0');

// ส่งออก Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
