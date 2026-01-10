<?php
require "../database/db.php";

$title   = trim($_POST['title']);
$message = trim($_POST['message']);
$link    = trim($_POST['link'] ?? '');
$user_id = $_POST['user_id'] !== "" ? intval($_POST['user_id']) : null;

if ($user_id === null) {
    // ✅ ALL USERS
    $stmt = $conn->prepare("
        INSERT INTO notifications (title, message, link, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->bind_param("sss", $title, $message, $link);
} else {
    // ✅ SPECIFIC USER
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, title, message, link, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("isss", $user_id, $title, $message, $link);
}

$stmt->execute();

header("Location: ../settings.php");
exit;