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
    VALUES (?, 'cart')
");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$checkout_id = $conn->insert_id;

// Fetch cart items
$cart = $conn->query("SELECT * FROM cart WHERE user_id = $user_id");

while ($c = $cart->fetch_assoc()) {
    $item = $conn->prepare("
        INSERT INTO checkout_items
        (checkout_id, product_id, product_name, product_image, variant_type, price, quantity)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $item->bind_param(
        "iisssdi",
        $checkout_id,
        $c['product_id'],
        $c['product_name'],
        $c['product_image'],
        $c['variant_type'],
        $c['price'],
        $c['quantity']
    );

    $item->execute();
}

header("Location: ../checkout.php");
exit;