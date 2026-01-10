<?php
session_start();
require "../database/db.php";

if (!isset($_SESSION['user_id'])) exit;

$user_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare("
    UPDATE notifications
    SET is_read = 1
    WHERE user_id = ? OR user_id IS NULL
");
$stmt->bind_param("i", $user_id);
$stmt->execute();