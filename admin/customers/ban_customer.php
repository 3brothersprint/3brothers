<?php
require '../database/db.php';

$user_id = intval($_POST['user_id'] ?? 0);

if (!$user_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid user'
    ]);
    exit;
}

// Toggle ban status
$stmt = $conn->prepare("
    UPDATE users
    SET is_banned = IF(is_banned = 1, 0, 1)
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}