<?php
session_start();
require '../database/db.php';

$admin_id = $_SESSION['admin_id'];

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=attendance.xls");

echo "Date\tLogin\tLogout\tStatus\n";

$stmt = $conn->prepare("
    SELECT login_date, login_time, logout_time, status
    FROM admin_attendance
    WHERE admin_id = ?
");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    echo "{$row['login_date']}\t{$row['login_time']}\t{$row['logout_time']}\t{$row['status']}\n";
}