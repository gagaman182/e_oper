<?php
header('Content-Type: application/json; charset=utf-8');

include('./db/connect_pmk.php');   // MySQL connection: $conn (mysqli procedural)
include('./db/connection.php');    // Oracle connection: $objConnect (oci_connect)

$code = isset($_POST['code']) ? trim($_POST['code']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '';

if ($code === '' || $type === '') {
    echo json_encode(array('status' => 'error', 'message' => 'กรุณาระบุรหัสรายการ (code) และประเภท (type)'));
    exit;
}

$data = array();
$found = false;

// ===== 1. เช็คข้อมูลจาก MySQL ก่อน =====
$sql = "SELECT * FROM pmk_items WHERE code = ? AND type = ? LIMIT 1";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "ss", $code, $type);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $data = $row;
        // ตรวจสอบว่ามีฟิลด์ icd_9_code หรือไม่
        if (!isset($data['icd_9_code'])) {
            $data['icd_9_code'] = '';
        }
        $found = true;
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Prepare statement (MySQL) ไม่สำเร็จ'));
    exit;
}

// ===== 2. ถ้าไม่พบข้อมูลใน MySQL ให้ไปดึงจาก Oracle =====
if (!$found && $objConnect) {
    switch ($type) {
        case 'L': // LAB
            $sql = "
             SELECT LABCODE AS CODE, LABNAME AS NAME, FUND_UNIT_PRICE, COPAY_UNIT_PRICE, MIN_PRICE, MAX_PRICE, REF_CODE,ICD9CM AS ICD_9_CODE FROM LABCODES WHERE LABCODE = :code";
            break;
        case 'X': // XRAY
            $sql = "SELECT XRAY_CODE AS CODE, NAME, FUND_UNIT_PRICE, COPAY_UNIT_PRICE, MIN_PRICE, MAX_PRICE, REF_CODE,ICD9CM AS ICD_9_CODE FROM XRAY_CODES  WHERE XRAY_CODE = :code";
            break;
        case 'P': // OPERATION
            $sql = "SELECT OPER_CODE AS CODE, NAME, FUND_UNIT_PRICE, COPAY_UNIT_PRICE, MIN_PRICE, MAX_PRICE, REF_CODE,ICD9_CODE AS ICD_9_CODE FROM OPERATION_CODES WHERE OPER_CODE = :code";
            break;
        case 'S': // SERVICE
            $sql = "SELECT CODE, NAME, FUND_UNIT_PRICE, COPAY_UNIT_PRICE, MIN_PRICE, MAX_PRICE, REF_CODE,ICD9CM AS ICD_9_CODE FROM SERVICE_CODES WHERE CODE = :code";
            break;
        case 'D': // DRUG
            $sql = "SELECT CODE, NAME, FUND_UNIT_PRICE, INTEND_FUND_UNIT_PRICE AS COPAY_UNIT_PRICE, SELL_UNIT_PRICE AS MIN_PRICE, IPD_SELL_UNIT_PRICE AS MAX_PRICE,
REF_CODE,'-' AS ICD_9_CODE FROM DRUGCODES WHERE DEL_FLAG IS NULL AND
 CODE = :code";
            break;
        case 'O': // MISC
            $sql = "SELECT MISC_CODE AS CODE, NAME, FUND_UNIT_PRICE, COPAY_UNIT_PRICE, MIN_PRICE, MAX_PRICE, REF_CODE,'-' AS ICD_9_CODE FROM MISC_CODES WHERE DEL_FLAG IS NULL AND MISC_CODE = :code";
            break;
        default:
            echo json_encode(array('status' => 'error', 'message' => 'ประเภทไม่ถูกต้อง'));
            exit;
    }

    $stmt = oci_parse($objConnect, $sql);
    if (!$stmt) {
        echo json_encode(array('status' => 'error', 'message' => 'Prepare statement (Oracle) ไม่สำเร็จ'));
        exit;
    }

    oci_bind_by_name($stmt, ":code", $code);
    oci_execute($stmt);

    if ($row = oci_fetch_assoc($stmt)) {
        $data = array(
            'code'               => isset($row['CODE']) ? $row['CODE'] : '',
            'type'               => $type,
            'name'               => isset($row['NAME']) ? $row['NAME'] : '',
            'fund_price'         => isset($row['FUND_UNIT_PRICE']) ? floatval($row['FUND_UNIT_PRICE']) : 0,
            'copay_price'        => isset($row['COPAY_UNIT_PRICE']) ? floatval($row['COPAY_UNIT_PRICE']) : 0,
            'min_price'          => isset($row['MIN_PRICE']) ? floatval($row['MIN_PRICE']) : 0,
            'max_price'          => isset($row['MAX_PRICE']) ? floatval($row['MAX_PRICE']) : 0,
            'ref_code'           => isset($row['REF_CODE']) ? $row['REF_CODE'] : '',
            'unit'               => '',
            'indication'         => '',
            'price_uc_opd'       => 0,
            'price_uc_ipd'       => 0,
            'price_officer_opd'  => 0,
            'price_officer_ipd'  => 0,
            'price_sss_opd'      => 0,
            'price_sss_ipd'      => 0,
            'price_foreign_opd'  => 0,
            'price_foreign_ipd'  => 0,
            'price_sks'          => 0,
            'icd_9_code'         => isset($row['ICD_9_CODE']) ? $row['ICD_9_CODE'] : ''
        );
        $found = true;
    }
    oci_free_statement($stmt);
}

// ===== 3. ส่งผลลัพธ์ =====
if ($found) {
    echo json_encode(array('status' => 'success', 'data' => $data));
} else {
    echo json_encode(array('status' => 'error', 'message' => 'ไม่พบข้อมูลรายการ'));
}
?>