<?php
session_start();
require "admin/database/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* ===============================
   1. GET POST DATA
   =============================== */
$delivery_type  = $_POST['delivery_type'] ?? 'standard';
$payment_method = $_POST['payment_method'] ?? 'cod';

$voucher_type  = $_POST['voucher_type'] ?? null;
$voucher_value = floatval($_POST['voucher_value'] ?? 0);

$subtotal      = floatval($_POST['subtotal']);
$shipping_fee  = floatval($_POST['shipping_fee']);
$discount      = floatval($_POST['discount']);
$total_amount  = floatval($_POST['total_amount']);
$order_no = strtoupper(uniqid());

/* ===============================
   2. GET DEFAULT ADDRESS (SNAPSHOT)
   =============================== */
$stmt = $conn->prepare("
    SELECT * FROM user_addresses
    WHERE user_id = ? AND is_default = 1
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$address = $stmt->get_result()->fetch_assoc();

if (!$address) {
    die("No default address found.");
}

/* ===============================
   3. GET CHECKOUT ITEMS
   =============================== */
$stmt = $conn->prepare("
    SELECT ci.*
    FROM checkout c
    JOIN checkout_items ci ON ci.checkout_id = c.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$items = $stmt->get_result();

if ($items->num_rows === 0) {
    die("No checkout items found.");
}

/* ===============================
   4. INSERT ORDER
   =============================== */
$stmt = $conn->prepare("
    INSERT INTO orders (
    order_no,
        user_id,
        recipient_name,
        recipient_phone,
        delivery_address,
        barangay,
        city,
        province,
        zip_code,
        delivery_type,
        payment_method,
        voucher_type,
        voucher_value,
        subtotal,
        shipping_fee,
        discount,
        total_amount
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sissssssssssddddd",
    $order_no,
    $user_id,
    $address['full_name'],
    $address['phone'],
    $address['address'],
    $address['barangay_name'],
    $address['city_name'],
    $address['province_name'],
    $address['zip_code'],
    $delivery_type,
    $payment_method,
    $voucher_type,
    $voucher_value,
    $subtotal,
    $shipping_fee,
    $discount,
    $total_amount
);


$stmt->execute();
$order_id = $stmt->insert_id;

/* ===============================
   5. INSERT ORDER ITEMS
   =============================== */
$itemStmt = $conn->prepare("
    INSERT INTO order_items (
        order_id,
        product_id,
        product_name,
        product_image,
        variant,
        price,
        quantity,
        subtotal
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
$stockStmt = $conn->prepare("
    UPDATE product_variants
    SET stock = stock - ?
    WHERE product_id = ?
      AND type = ?
      AND value = ?
      AND stock >= ?
");


$conn->begin_transaction();

while ($item = $items->fetch_assoc()) {

    $itemSubtotal = $item['price'] * $item['quantity'];

    // Insert order item
    $itemStmt->bind_param(
        "iisssdid",
        $order_id,
        $item['product_id'],
        $item['product_name'],
        $item['product_image'],
        $item['variant_type'],
        $item['price'],
        $item['quantity'],
        $itemSubtotal
    );
    $itemStmt->execute();

    /* ===============================
       DEDUCT PRODUCT STOCK
       =============================== */
    $qty = (int)$item['quantity'];
$pid = (int)$item['product_id'];

/* Decode variant JSON */
$variantData = json_decode($item['variant_type'], true);

if (!$variantData || !is_array($variantData)) {
    die("Invalid variant data for product ID: $pid");
}

/* Deduct stock PER VARIANT */
foreach ($variantData as $type => $data) {

    $value = $data['value'];

    $stockStmt->bind_param(
        "iissi",
        $qty,
        $pid,
        $type,
        $value,
        $qty
    );
    $stockStmt->execute();

    if ($stockStmt->affected_rows === 0) {
        die("Insufficient stock for product ID: $pid ($type: $value)");
    }
}

}

/* ===============================
   6. CLEAR CHECKOUT
   =============================== */
$conn->query("
    DELETE ci FROM checkout_items ci
    JOIN checkout c ON ci.checkout_id = c.id
    WHERE c.user_id = $user_id
");

$conn->query("
    DELETE FROM checkout WHERE user_id = $user_id
");
// Clear cart (flat cart table)
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$logStmt = $conn->prepare("
    INSERT INTO order_logs (order_id, status, remarks)
    VALUES (?, ?, ?)
");

$status  = 'Order Placed';
$remarks = 'Order successfully created by customer';

$logStmt->bind_param("iss", $order_id, $status, $remarks);
$logStmt->execute();

$conn->commit();


/* ===============================
   7. REDIRECT SUCCESS
   =============================== */
header("Location: order_success.php?order_id=" . $order_id);
exit;