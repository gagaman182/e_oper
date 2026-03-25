<?php
header('Content-Type: application/json; charset=utf-8');
include('./db/connection.php'); // MySQL connection $conn (mysqli)

$code = isset($_POST['code']) ? $_POST['code'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : '';

if ($code == '' || $type == '') {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM pmk_items WHERE code = ? AND type = ?");
$stmt->bind_param('ss', $code, $type);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'code' => $row['code'],
        'type' => $row['type'],
        'name' => $row['name'],
        'fund_price' => number_format((float)$row['fund_price'], 2),
        'copay_price' => number_format((float)$row['copay_price'], 2),
        'min_price' => number_format((float)$row['min_price'], 2),
        'max_price' => number_format((float)$row['max_price'], 2),
        'opd_price' => number_format((float)$row['opd_price'], 2),
        'ipd_price' => number_format((float)$row['ipd_price'], 2),
        'uc_price' => number_format((float)$row['uc_price'], 2),
        'gov_price' => number_format((float)$row['gov_price'], 2),
        'sso_price' => number_format((float)$row['sso_price'], 2),
        'foreigner_price' => number_format((float)$row['foreigner_price'], 2),
        'unit' => $row['unit'],
        'ref_code' => $row['ref_code'],
        'indication' => $row['indication'],
        'note' => $row['note'],
        'created_at' => $row['created_at'],
        'updated_at' => $row['updated_at'],
    ];
}

echo json_encode($data);
