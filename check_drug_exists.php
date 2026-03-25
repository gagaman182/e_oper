<?php
// check_drug_exists.php
header('Content-Type: application/json; charset=utf-8');

// เชื่อมต่อฐานข้อมูล MySQL
include('db/connection.php'); // ควรมี $conn (mysqli)

$response = array();

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    $response['status'] = 'error';
    $response['message'] = 'ไม่สามารถเชื่อมต่อฐานข้อมูล';
    echo json_encode($response);
    exit;
}

// ดึงรหัสที่มีใน pmk_items ที่ type = 'D' (D = Drug)
$sql = "SELECT code FROM pmk_items WHERE type = 'D'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    $response['status'] = 'error';
    $response['message'] = 'เกิดข้อผิดพลาดในการ query ฐานข้อมูล';
    echo json_encode($response);
    exit;
}

$codes = array();
while ($row = mysqli_fetch_assoc($result)) {
    $codes[] = $row['code'];
}

// ส่งกลับข้อมูล JSON
$response['status'] = 'success';
$response['codes'] = $codes;

echo json_encode($response);

// ปิดการเชื่อมต่อ
mysqli_free_result($result);
mysqli_close($conn);
?>
