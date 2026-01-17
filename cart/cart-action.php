<?php
session_start();
require "../database/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/auth.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_POST['action'] === 'add_to_cart') {

    $product_id = (int)$_POST['product_id'];
    $name       = $_POST['name'];
    $image      = $_POST['image'];
    $qty        = max(1, (int)$_POST['qty']);

    // 🔥 NEW FIELDS
    $variantData = $_POST['variant_data'] ?? '{}';
    $unitPrice   = (float)$_POST['unit_price'];
    $totalPrice  = (float)$_POST['total_price'];

    // Encode variant safely
    $variantJson = json_encode(json_decode($variantData, true));

    /* ===============================
       CHECK EXISTING CART ITEM
    ================================ */
    $check = $conn->prepare("
        SELECT id, quantity 
        FROM cart 
        WHERE user_id = ? 
          AND product_id = ?
          AND variant_type = ?
    ");
    $check->bind_param("iis", $user_id, $product_id, $variantJson);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();
        $newQty = $row['quantity'] + $qty;
        $newTotal = $unitPrice * $newQty;

        $update = $conn->prepare("
            UPDATE cart 
            SET quantity = ?, price = ?
            WHERE id = ?
        ");
        $update->bind_param("idi", $newQty, $newTotal, $row['id']);
        $update->execute();

    } else {

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
            $variantJson,
            $totalPrice,
            $qty
        );
        $insert->execute();
    }

    header("Location: ../cart.php");
    exit;
}