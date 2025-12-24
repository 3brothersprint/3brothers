<?php
require 'database/db.php';
$data = json_decode(file_get_contents("php://input"), true);

$request_id = (int)($data['request_id'] ?? 0);
$method = $data['payment_method'] ?? '';

if (!$request_id || !$method) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$stmt = $conn->prepare("
    UPDATE print_requests
    SET payment_method = ?,
        payment_status = 'Pending Payment Verification',
        status = 'Pending Payment Verification'
    WHERE id = ?
");
$stmt->bind_param("si", $method, $request_id);
$stmt->execute();


echo json_encode(['success' => true]);