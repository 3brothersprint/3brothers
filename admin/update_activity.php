<?php
session_start();
require 'database/db.php';

date_default_timezone_set('Asia/Manila');
$today = date('Y-m-d');

if (!isset($_SESSION['admin_id'])) exit;

$stmt = $conn->prepare("
    UPDATE admin_attendance
    SET last_activity = NOW()
    WHERE admin_id = ? AND login_date = ?
");
$stmt->bind_param("is", $_SESSION['admin_id'], $today);
$stmt->execute();