<?php
require '../database/db.php';

$order_id = (int)($_GET['order_id'] ?? 0);
if (!$order_id) {
    echo json_encode(['success' => false]);
    exit;
}

/* FETCH CURRENT STATUS */
$stmt = $conn->prepare("SELECT status FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

/* FETCH LOGS */
$logStmt = $conn->prepare("
    SELECT status, remarks, created_at
    FROM order_logs
    WHERE order_id = ?
    ORDER BY created_at DESC
");
$logStmt->bind_param("i", $order_id);
$logStmt->execute();
$logs = $logStmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* STATUS STEPS */
$steps = [
    'Order Placed' => ['label' => 'Order Placed', 'icon' => 'bi-receipt'],
    'To Ship' => ['label' => 'To Ship', 'icon' => 'bi-box-seam'],
    'To Transit' => ['label' => 'To Transit', 'icon' => 'bi-truck'],
    'Out for Delivery' => ['label' => 'Out for Delivery', 'icon' => 'bi-truck'],
    'Delivered' => ['label' => 'Delivered', 'icon' => 'bi-check-circle'],
    'Cancelled' => ['label' => 'Cancelled', 'icon' => 'bi-x-circle']
];

$keys = array_keys($steps);
$currentIndex = array_search($order['status'], $keys, true);

$progress = ($order['status'] === 'Cancelled')
    ? 100
    : (($currentIndex !== false)
        ? ($currentIndex / (count($keys) - 1)) * 100
        : 0);

echo json_encode([
    'success' => true,
    'currentStatus' => $order['status'],
    'currentIndex' => $currentIndex,
    'progress' => round($progress),
    'steps' => $steps,
    'logs' => $logs
]);