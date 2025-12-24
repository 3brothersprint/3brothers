<?php
session_start();
require "../database/db.php";

header('Content-Type: application/json');

$user_id    = $_SESSION['user_id'] ?? 0;
$request_id = intval($_POST['request_id'] ?? 0);

if ($user_id <= 0 || $request_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE print_requests
    SET status = 'Cancelled'
    WHERE id = ? AND user_id = ? AND status = 'Order Placed'
");
$stmt->bind_param("ii", $request_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode([
        'success' => true,
        'message' => 'Print request has been cancelled'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Unable to cancel this request'
    ]);
}