<?php
header('Content-Type: application/json; charset=utf-8');
include('./db/connection.php'); // MySQL connection $conn (mysqli)

$code = isset($_POST['code']) ? $_POST['code'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : '';

if (empty($code) || empty($type)) {
    echo json_encode(['exists' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM pmk_items WHERE code = ? AND type = ?");
$stmt->bind_param("ss", $code, $type);
$stmt->execute();
$stmt->bind_result($cnt);
$stmt->fetch();
$stmt->close();

echo json_encode(['exists' => $cnt > 0]);
