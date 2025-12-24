<?php
session_start();
require "../database/db.php";

$user_id = $_SESSION['user_id'];
$cart_id = intval($_POST['cart_id']);
$qty = intval($_POST['qty']);

if (isset($_POST['increase'])) $qty++;
if (isset($_POST['decrease'])) $qty--;

$qty = max(1, $qty);

$stmt = $conn->prepare("
    UPDATE cart SET quantity = ?
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("iii", $qty, $cart_id, $user_id);
$stmt->execute();

header("Location: ../cart.php");
exit;