<?php
session_start();
include '../../database/db.php';

if (!isset($_POST['id'])) {
    echo "Invalid request";
    exit;
}

$id = intval($_POST['id']);
$res = $conn->query("SELECT barcode FROM products WHERE id = $id");
if ($row = $res->fetch_assoc()) {
    $barcode = $row['barcode'];
    @unlink("../products/barcodes/$barcode.png");
}

/* DELETE RELATED DATA FIRST */
$conn->query("DELETE FROM product_variants WHERE product_id = $id");
$conn->query("DELETE FROM product_specs WHERE product_id = $id");
$conn->query("DELETE FROM product_images WHERE product_id = $id");

/* DELETE PRODUCT */
$conn->query("DELETE FROM products WHERE id = $id");

echo "success";