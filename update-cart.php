<?php
session_start();

$index = $_POST['index'];

if (isset($_SESSION['cart'][$index])) {
    if (isset($_POST['increase'])) {
        $_SESSION['cart'][$index]['qty']++;
    }

    if (isset($_POST['decrease']) && $_SESSION['cart'][$index]['qty'] > 1) {
        $_SESSION['cart'][$index]['qty']--;
    }

    if (isset($_POST['qty'])) {
        $_SESSION['cart'][$index]['qty'] = max(1, (int)$_POST['qty']);
    }
}

header('Location: cart.php');
exit;