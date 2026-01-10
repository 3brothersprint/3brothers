<?php
session_start();
require "../database/db.php";

if (!isset($_SESSION['user_id'])) exit;

$user_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT COUNT(*) total
    FROM notifications
    WHERE is_read = 0
      AND (user_id = ? OR user_id IS NULL)
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();

echo $total;