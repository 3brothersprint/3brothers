<?php
session_start();
include 'database/db.php';

$user_id  = $_SESSION['user_id'] ?? 0;
$order_id = $_POST['order_id'] ?? 0;
$ratings  = $_POST['rating'] ?? [];
$reviews  = $_POST['review'] ?? [];

if (!$user_id || !$order_id) {
    die('Invalid request');
}

foreach ($ratings as $item_id => $rating) {

    $comment = trim($reviews[$item_id] ?? '');
$check = $conn->prepare("
    SELECT 1 FROM product_reviews
    WHERE order_id = ? AND user_id = ?
    LIMIT 1
");
$check->bind_param("ii", $order_id, $user_id);
$check->execute();

if ($check->get_result()->num_rows) {
    header("Location: my_orders.php");
    exit;
}
    $stmt = $conn->prepare("
        INSERT INTO product_reviews
        (user_id, order_id, order_item_id, rating, review)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iiiis",
        $user_id,
        $order_id,
        $item_id,
        $rating,
        $comment
    );

    $stmt->execute();
}

$_SESSION['review_success'] = true;
header("Location: my_orders.php");
exit;