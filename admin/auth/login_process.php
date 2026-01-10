<?php
session_start();
require "../database/db.php";

if (isset($_POST['admin_login'])) {

    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    $stmt = $conn->prepare("
        SELECT id, full_name, password, is_banned, role
        FROM users
        WHERE email = ? AND role = 'admin'
        LIMIT 1
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Unauthorized admin access";
        header("Location: login.php");
        exit();
    }

    $admin = $result->fetch_assoc();

    if (!password_verify($pass, $admin['password'])) {
        $_SESSION['error'] = "Invalid credentials";
        header("Location: login.php");
        exit();
    }

    if ($admin['is_banned'] == 1) {
        $_SESSION['error'] = "Admin account disabled";
        header("Location: login.php");
        exit();
    }

    // ADMIN SESSION
    $_SESSION['admin_id']   = $admin['id'];
    $_SESSION['admin_name'] = $admin['full_name'];
    $_SESSION['role']       = 'admin';

    header("Location: ../");
    exit();
}