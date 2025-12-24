<?php
require "../database/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$order_id = intval($data['id'] ?? 0);
$status   = trim($data['status'] ?? '');
$remarks  = trim($data['remark'] ?? '');

if ($order_id <= 0 || $status === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input'
    ]);
    exit;
}

/* ===============================
   UPDATE ORDER STATUS
================================ */
$stmt = $conn->prepare("
    UPDATE orders
    SET status = ?
    WHERE id = ?
");
$stmt->bind_param("si", $status, $order_id);
$stmt->execute();
$stmt->close();

/* ===============================
   INSERT ORDER LOG
================================ */
$stmt = $conn->prepare("
    INSERT INTO order_logs (order_id, status, remarks)
    VALUES (?, ?, ?)
");
$stmt->bind_param("iss", $order_id, $status, $remarks);
$stmt->execute();
$stmt->close();

echo json_encode([
    'success' => true,
    'message' => 'Order status updated'
]);