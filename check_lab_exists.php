<?php
// check_lab_exists.php
header('Content-Type: application/json; charset=utf-8');

// เชื่อมต่อฐานข้อมูล MySQL (PHP5-compatible)
include('db/connection.php'); // ต้องกำหนด $conn เป็น mysqli

$response = array();

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    $response['status'] = 'error';
    $response['message'] = 'ไม่สามารถเชื่อมต่อฐานข้อมูล';
    echo json_encode($response);
    exit;
}

// ดึงรหัส code ทั้งหมดจาก pmk_items
$sql = "SELECT code FROM pmk_items";

$result = mysqli_query($conn, $sql);

if (!$result) {
    $response['status'] = 'error';
    $response['message'] = 'เกิดข้อผิดพลาดในการ query ฐานข้อมูล';
    echo json_encode($response);
    exit;
}

$codes = array();
while ($row = mysqli_fetch_assoc($result)) {
    $codes[] = $row['code']; // เก็บรหัสรายการ
}

// ส่งผลลัพธ์กลับเป็น JSON
$response['status'] = 'success';
$response['codes'] = $codes;

echo json_encode($response);

// ปิดการเชื่อมต่อ
mysqli_free_result($result);
mysqli_close($conn);
?>
