<?php
session_start();
require "../database/db.php";

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit;
}

$admin_id = (int)$_SESSION['admin_id'];

$stmt = $conn->prepare("
    UPDATE admin_attendance
    SET last_activity = NOW()
    WHERE admin_id = ? AND login_date = CURDATE()
");
$stmt->bind_param("i", $admin_id);
$stmt->execute();