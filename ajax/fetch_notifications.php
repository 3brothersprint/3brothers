<?php
session_start();
require "../database/db.php";

if (!isset($_SESSION['user_id'])) exit;

$user_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT id, title, message, link, created_at, is_read
    FROM notifications
    WHERE user_id = ? OR user_id IS NULL
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode($rows);