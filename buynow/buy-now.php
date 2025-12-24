<?php
session_start();
require "../admin/database/db.php";

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: ../login.php");
    exit;
}

// Clean previous checkout
$conn->query("DELETE FROM checkout WHERE user_id = $user_id");

// Create checkout
$stmt = $conn->prepare("
    INSERT INTO checkout (user_id, source)
    VALUES (?, 'buy_now')
");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$checkout_id = $conn->insert_id;

// Insert checkout item
$item = $conn->prepare("
    INSERT INTO checkout_items
    (checkout_id, product_id, product_name, product_image, variant_type, price, quantity)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$item->bind_param(
    "iisssdi",
    $checkout_id,
    $_POST['product_id'],
    $_POST['name'],
    $_POST['image'],
    $_POST['type'],
    $_POST['price'],
    $_POST['qty']
);

$item->execute();

header("Location: ../checkout.php");
exit;