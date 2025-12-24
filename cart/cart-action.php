<?php
session_start();
require "../database/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_POST['action'] === 'add_to_cart') {

    $product_id = intval($_POST['product_id']);
    $name = $_POST['name'];
    $image = $_POST['image'];
    $type = $_POST['type'];
    $price = floatval($_POST['price']);
    $qty = intval($_POST['qty']);

    // Check if item already exists
    $check = $conn->prepare("
        SELECT id, quantity 
        FROM cart 
        WHERE user_id = ? AND product_id = ? AND variant_type = ?
    ");
    $check->bind_param("iis", $user_id, $product_id, $type);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update quantity
        $row = $result->fetch_assoc();
        $new_qty = $row['quantity'] + $qty;

        $update = $conn->prepare("
            UPDATE cart 
            SET quantity = ? 
            WHERE id = ?
        ");
        $update->bind_param("ii", $new_qty, $row['id']);
        $update->execute();
    } else {
        // Insert new cart item
        $insert = $conn->prepare("
            INSERT INTO cart 
            (user_id, product_id, product_name, product_image, variant_type, price, quantity)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $insert->bind_param(
            "iisssdi",
            $user_id,
            $product_id,
            $name,
            $image,
            $type,
            $price,
            $qty
        );
        $insert->execute();
    }

    header("Location: ../cart.php");
    exit;
}