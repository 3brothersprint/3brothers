<?php
session_start();
require '../database/db.php';

date_default_timezone_set('Asia/Manila');

if (isset($_SESSION['admin_id'])) {

    $admin_id = $_SESSION['admin_id'];
    $today    = date('Y-m-d');

    $stmt = $conn->prepare("
        UPDATE admin_attendance
        SET 
            logout_time = CURTIME(),
            last_activity = NULL
        WHERE admin_id = ? AND login_date = ?
    ");
    $stmt->bind_param("is", $admin_id, $today);
    $stmt->execute();
}

session_destroy();
header("Location: ../auth/login.php");
exit;