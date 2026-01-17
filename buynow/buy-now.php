<?php
session_start();
require "../admin/database/db.php";

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: ../auth/auth.php");
    exit;
}

$product_id  = (int)$_POST['product_id'];
$name        = $_POST['name'];
$image       = $_POST['image'];
$qty         = max(1, (int)$_POST['qty']);
$unitPrice   = (float)$_POST['unit_price'];
$totalPrice  = (float)$_POST['total_price'];
$variantData = $_POST['variant_data'] ?? '{}';

$variantJson = json_encode(json_decode($variantData, true));

/* ===============================
   RESET CHECKOUT
================================ */
$conn->query("DELETE FROM checkout WHERE user_id = $user_id");

/* ===============================
   CREATE CHECKOUT
================================ */
$stmt = $conn->prepare("
    INSERT INTO checkout (user_id, source)
    VALUES (?, 'buy_now')
");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$checkout_id = $conn->insert_id;

/* ===============================
   INSERT ITEM
================================ */
$item = $conn->prepare("
    INSERT INTO checkout_items
    (checkout_id, product_id, product_name, product_image, variant_type, price, quantity)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$item->bind_param(
    "iisssdi",
    $checkout_id,
    $product_id,
    $name,
    $image,
    $variantJson,
    $totalPrice,
    $qty
);

$item->execute();

header("Location: ../checkout.php");
exit;