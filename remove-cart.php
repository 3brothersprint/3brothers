<?php
session_start();

$index = $_POST['index'];

if (isset($_SESSION['cart'][$index])) {
    unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex
}

header('Location: cart.php');
exit;