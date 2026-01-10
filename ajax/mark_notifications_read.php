<?php
session_start();
require "../database/db.php";

if (!isset($_SESSION['user_id'])) exit;

$user_id  = (int) $_SESSION['user_id'];
$notif_id = (int) ($_POST['id'] ?? 0);

if (!$notif_id) exit;

/*
  Mark as read if:
  - Notification belongs to the user
  - OR notification is for ALL users (user_id IS NULL)
*/
$stmt = $conn->prepare("
    UPDATE notifications
    SET is_read = 1
    WHERE id = ?
      AND (user_id = ? OR user_id IS NULL)
");
$stmt->bind_param("ii", $notif_id, $user_id);
$stmt->execute();