<?php
header('Content-Type: application/json; charset=utf-8');

// เชื่อมต่อฐานข้อมูล MySQL (mysqli)
include('db/connection.php'); // ต้องมีตัวแปร $conn

$response = array();

if (!$conn) {
    $response['status'] = 'error';
    $response['message'] = 'ไม่สามารถเชื่อมต่อฐานข้อมูล';
    echo json_encode($response);
    exit;
}

// ดึงเฉพาะรายการที่ type = 'O' (Miscellaneous)
$sql = "SELECT code FROM pmk_items WHERE type = 'O'";

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

$response['status'] = 'success';
$response['codes'] = $codes;

echo json_encode($response);

mysqli_free_result($result);
mysqli_close($conn);
?>
