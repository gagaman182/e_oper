<?php 
header('Content-Type: application/json; charset=utf-8');
include './db/connection.php'; // ใช้ตัวแปร $conn แบบ mysqli procedural

// ตรวจสอบการเชื่อมต่อ
if (!isset($conn) || !$conn) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถเชื่อมต่อฐานข้อมูล']);
    exit;
}

// กำหนดค่าเริ่มต้นและรับข้อมูลจาก POST
$fields = [
    'code' => '', 'ref_code' => '', 'type' => '', 'name' => '',
    'fund_price' => 0, 'copay_price' => 0, 'min_price' => 0, 'max_price' => 0,
    'price_uc_opd' => 0, 'price_uc_ipd' => 0, 'price_officer_opd' => 0, 'price_officer_ipd' => 0,
    'price_sss_opd' => 0, 'price_sss_ipd' => 0, 'price_foreign_opd' => 0, 'price_foreign_ipd' => 0,
    'price_sks' => 0, 'unit' => '', 'indication' => '', 'icd_9' => ''
];

foreach ($fields as $key => &$val) {
    if (isset($_POST[$key])) {
        $val = is_numeric($_POST[$key]) ? floatval($_POST[$key]) : trim($_POST[$key]);
    }
}
unset($val);

// ตรวจสอบค่าที่จำเป็น
if ($fields['code'] === '' || $fields['type'] === '' || $fields['name'] === '') {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน: รหัส, ประเภท, ชื่อรายการต้องระบุ']);
    exit;
}

$now = date('Y-m-d H:i:s');

// แยกตัวแปรสำหรับ bind_param
$code = $fields['code'];
$type = $fields['type'];
$name = $fields['name'];
$ref_code = $fields['ref_code'];
$fund_price = $fields['fund_price'];
$copay_price = $fields['copay_price'];
$min_price = $fields['min_price'];
$max_price = $fields['max_price'];
$price_uc_opd = $fields['price_uc_opd'];
$price_uc_ipd = $fields['price_uc_ipd'];
$price_officer_opd = $fields['price_officer_opd'];
$price_officer_ipd = $fields['price_officer_ipd'];
$price_sss_opd = $fields['price_sss_opd'];
$price_sss_ipd = $fields['price_sss_ipd'];
$price_foreign_opd = $fields['price_foreign_opd'];
$price_foreign_ipd = $fields['price_foreign_ipd'];
$price_sks = $fields['price_sks'];
$unit = $fields['unit'];
$indication = $fields['indication'];
$icd_9 = $fields['icd_9'];

// ตรวจสอบว่ามีอยู่แล้วในฐานข้อมูลหรือไม่
$sql_check = "SELECT id FROM pmk_items WHERE code = ? AND type = ?";
$stmt_check = mysqli_prepare($conn, $sql_check);
mysqli_stmt_bind_param($stmt_check, 'ss', $code, $type);
mysqli_stmt_execute($stmt_check);
mysqli_stmt_store_result($stmt_check);

// เตรียมคำสั่ง UPDATE หรือ INSERT
if (mysqli_stmt_num_rows($stmt_check) > 0) {
    // UPDATE
    $sql = "UPDATE pmk_items SET 
        name=?, ref_code=?, fund_price=?, copay_price=?, min_price=?, max_price=?,
        price_uc_opd=?, price_uc_ipd=?, price_officer_opd=?, price_officer_ipd=?,
        price_sss_opd=?, price_sss_ipd=?, price_foreign_opd=?, price_foreign_ipd=?, price_sks=?,
        unit=?, indication=?, updated_at=?, icd_9_code=?
        WHERE code=? AND type=?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        'ssddddddddddddddsssss',
        $name, $ref_code, $fund_price, $copay_price,
        $min_price, $max_price, $price_uc_opd, $price_uc_ipd,
        $price_officer_opd, $price_officer_ipd, $price_sss_opd, $price_sss_ipd,
        $price_foreign_opd, $price_foreign_ipd, $price_sks, $unit,
        $indication, $now, $icd_9, $code, $type
    );
    $action = 'แก้ไขข้อมูลสำเร็จ';
} else {
    // INSERT
    $sql = "INSERT INTO pmk_items (
        code, type, name, ref_code, fund_price, copay_price, min_price, max_price,
        price_uc_opd, price_uc_ipd, price_officer_opd, price_officer_ipd,
        price_sss_opd, price_sss_ipd, price_foreign_opd, price_foreign_ipd, price_sks,
        unit, indication, created_at, updated_at, icd_9_code
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        'ssssddddddddddddddssss',
        $code, $type, $name, $ref_code,
        $fund_price, $copay_price, $min_price, $max_price,
        $price_uc_opd, $price_uc_ipd, $price_officer_opd, $price_officer_ipd,
        $price_sss_opd, $price_sss_ipd, $price_foreign_opd, $price_foreign_ipd, $price_sks,
        $unit, $indication, $now, $now, $icd_9
    );
    $action = 'เพิ่มข้อมูลสำเร็จ';
}

// Execute statement
if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['status' => 'success', 'message' => $action]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถบันทึกข้อมูลได้: ' . mysqli_stmt_error($stmt)]);
}

// ปิด statement และ connection
mysqli_stmt_close($stmt_check);
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
