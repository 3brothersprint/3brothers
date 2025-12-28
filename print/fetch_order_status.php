<?php
require '../database/db.php';

$request_id = (int)($_GET['request_id'] ?? 0);

if (!$request_id) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $conn->prepare("
    SELECT status
    FROM print_requests
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $request_id);
$stmt->execute();

$request = $stmt->get_result()->fetch_assoc();

if (!$request) {
    echo json_encode(['success' => false]);
    exit;
}

$steps = [
    'Order Placed' => 'Order Placed',
    'Pending Payment Verification' => 'Pending',
    'Approved' => 'Approved',
    'Printing' => 'Printing',
    'Ready for Pickup' => 'Ready for Pickup',
    'Completed' => 'Completed',
    'Cancelled' => 'Cancelled'
];

$currentStatus = trim($request['status']);
$stepKeys = array_keys($steps);
$currentIndex = array_search($currentStatus, $stepKeys, true);

if ($currentStatus === 'Cancelled') {
    $progressPercent = 100;
} elseif ($currentIndex === false) {
    $progressPercent = 0;
} else {
    $progressPercent = ($currentIndex / (count($stepKeys) - 1)) * 100;
}

echo json_encode([
    'success' => true,
    'currentStatus' => $currentStatus,
    'steps' => $steps,
    'currentIndex' => $currentIndex,
    'progress' => $progressPercent
]);