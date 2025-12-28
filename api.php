<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache');

require 'database/db.php';

try {
    // Example: simulate processing delay (remove in production)
    usleep(800000); // 0.8s

    // Example query
    $stmt = $conn->prepare("
        SELECT id, request_no, status, created_at
        FROM print_requests
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
}