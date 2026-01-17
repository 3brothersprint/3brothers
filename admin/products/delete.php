<?php
session_start();
require '../database/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// Validate product ID
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['message'] = "Invalid product ID";
    $_SESSION['msg_type'] = "danger";
    header("Location: ../products.php");
    exit;
}

// Check if product exists
$stmt = $conn->prepare("SELECT * FROM category WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    $_SESSION['message'] = "Category not found";
    $_SESSION['msg_type'] = "warning";
    header("Location: ../products.php");
    exit;
}

// Delete product
$stmt = $conn->prepare("DELETE FROM category WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    $_SESSION['message'] = "Category deleted successfully";
    $_SESSION['msg_type'] = "success";
} else {
    $_SESSION['message'] = "Failed to delete category";
    $_SESSION['msg_type'] = "danger";
}
$stmt->close();

// Redirect back to products page
header("Location: ../products.php");
exit;