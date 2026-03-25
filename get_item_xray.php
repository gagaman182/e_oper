<?php
header('Content-Type: application/json; charset=utf-8');

include('db/connection.php'); // กำหนด $conn เป็น mysqli

$response = array();

// if (!$conn) {
//     $response['status'] = 'error';
//     $response['message'] = 'ไม่สามารถเชื่อมต่อฐานข้อมูล';
//     echo json_encode($response);
//     exit;
// }

$code = isset($_POST['code']) ? trim($_POST['code']) : '';

if ($code === '') {
    $response['status'] = 'error';
    $response['message'] = 'ไม่มีรหัสรายการ';
    echo json_encode($response);
    exit;
}

$code_safe = mysqli_real_escape_string($conn, $code);

// ดึงข้อมูลทุกฟิลด์จาก pmk_items ตาม code
$sql = "SELECT * FROM pmk_items WHERE code = '$code_safe'  AND type = 'X' LIMIT 1";

$result = mysqli_query($conn, $sql);

if (!$result) {
    $response['status'] = 'error';
    $response['message'] = 'Query ผิดพลาด: ' . mysqli_error($conn);
    echo json_encode($response);
    exit;
}

if (mysqli_num_rows($result) === 0) {
    $response['status'] = 'error';
    $response['message'] = 'ไม่พบข้อมูลรายการ';
    echo json_encode($response);
    exit;
}

// ดึงข้อมูลรายการ
$row = mysqli_fetch_assoc($result);

// จัดรูปแบบข้อมูลสำหรับตอบกลับ
$response = [
    'status' => 'success',
    'data' => [
        'code'               => $row['code'],
        'ref_code'           => $row['ref_code'],
        'name'               => $row['name'],
        'fund_price'         => $row['fund_price'],
        'copay_price'        => $row['copay_price'],
        'min_price'          => $row['min_price'],
        'max_price'          => $row['max_price'],
        'unit'               => $row['unit'],
        'indication'         => $row['indication'],
        'price_officer_opd'  => $row['price_officer_opd'],
        'price_officer_ipd'  => $row['price_officer_ipd'],
        'price_uc_opd'       => $row['price_uc_opd'],
        'price_uc_ipd'       => $row['price_uc_ipd'],
        'price_sss_opd'      => $row['price_sss_opd'],
        'price_sss_ipd'      => $row['price_sss_ipd'],
        'price_foreign_opd'  => $row['price_foreign_opd'],
        'price_foreign_ipd'  => $row['price_foreign_ipd'],
        'price_sks'          => $row['price_sks'],
        'type'               => $row['type'],
        'icd_9_code'               => $row['icd_9_code']
    ]
];

// ส่งข้อมูลกลับ
echo json_encode($response);

// ปิดการเชื่อมต่อ
mysqli_free_result($result);
mysqli_close($conn);
?>
