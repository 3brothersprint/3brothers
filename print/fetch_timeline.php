<?php
require '../database/db.php';

$request_id = (int)($_GET['request_id'] ?? 0);

if (!$request_id) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT status, remark, created_at
    FROM print_request_logs
    WHERE request_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $request_id);
$stmt->execute();

$logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($logs);