<?php
// get_item_lab.php
header('Content-Type: application/json; charset=utf-8');

// เชื่อมต่อฐานข้อมูล MySQL (mysqli)
include('db/connection.php'); // ต้องมี $conn เป็น mysqli

$response = array();

// รับค่าจาก POST
$code = isset($_POST['code']) ? trim($_POST['code']) : '';

// ตรวจสอบว่าได้รับรหัสหรือไม่
if ($code === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'กรุณาระบุรหัสรายการ (code)'
    ]);
    exit;
}

// ป้องกัน SQL Injection
$code_safe = mysqli_real_escape_string($conn, $code);

// คิวรีข้อมูลจาก pmk_items เฉพาะรายการ LAB (type = 'L')
$sql = "SELECT * FROM pmk_items WHERE code = '$code_safe' AND type = 'L' LIMIT 1";
$result = mysqli_query($conn, $sql);

// ตรวจสอบผลลัพธ์
if (!$result) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ไม่สามารถดึงข้อมูลจากฐานข้อมูล'
    ]);
    exit;
}

if (mysqli_num_rows($result) == 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ไม่พบข้อมูลรายการ'
    ]);
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
        'code_30'            => $row['code_30'],
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
