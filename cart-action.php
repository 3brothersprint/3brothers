<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$cartItem = [
    'product_id' => (int)$_POST['product_id'],
    'name'       => $_POST['name'],
    'type'       => $_POST['type'],
    'price'      => (float)$_POST['price'],
    'qty'        => (int)$_POST['qty'],
    'image'      => $_POST['image']
];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* 🔁 If same product + same variant, increase qty */
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if (
        $item['product_id'] == $cartItem['product_id'] &&
        $item['type'] == $cartItem['type']
    ) {
        $item['qty'] += $cartItem['qty'];
        $found = true;
        break;
    }
}

if (!$found) {
    $_SESSION['cart'][] = $cartItem;
}

/* BUY NOW → go to checkout */
if ($_POST['action'] === 'buy_now') {
    $_SESSION['buy_now'] = [$cartItem];
    header("Location: checkout.php");
    exit;
}

/* ADD TO CART → go to cart page */
header("Location: cart.php");
exit;