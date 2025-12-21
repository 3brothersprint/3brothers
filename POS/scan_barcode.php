<?php
include '../database/db.php';

$code = $_GET['code'];

/* CHECK VARIANT FIRST */
$q = $conn->query("
    SELECT v.*, p.name 
    FROM product_variants v
    JOIN products p ON p.id = v.product_id
    WHERE v.barcode = '$code'
");

if ($q->num_rows) {
    echo json_encode($q->fetch_assoc());
    exit;
}

/* CHECK PRODUCT */
$q = $conn->query("
    SELECT * FROM products WHERE barcode = '$code'
");

if (!$q->num_rows) {
    echo json_encode(null);
    exit;
}

$product = $q->fetch_assoc();

/* CHECK IF HAS VARIANTS */
$v = $conn->query("
    SELECT * FROM product_variants WHERE product_id = {$product['id']}
");

if ($v->num_rows) {
    echo json_encode([
        'type' => 'variants',
        'product_id' => $product['id'],
        'name' => $product['name']
    ]);
    exit;
}

echo json_encode($product);