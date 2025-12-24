<?php
session_start();

if (empty($_POST['type']) || empty($_POST['price'])) {
    header('Location: ../product-details.php?id=' . $_POST['product_id']);
    exit;
}

$product = [
    'product_id' => (int)$_POST['product_id'],
    'name' => $_POST['product_name'],
    'type' => $_POST['type'],
    'price' => (float)$_POST['price'],
    'qty' => (int)$_POST['qty']
];

if (isset($_POST['add_to_cart'])) {
    $_SESSION['cart'][] = $product;
    header('Location: ../cart.php');
    exit;
}

if (isset($_POST['buy_now'])) {
    $_SESSION['buy_now'] = $product;
    header('Location: checkout.php');
    exit;
}