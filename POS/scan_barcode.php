<?php
include '../database/db.php';

$code = $_GET['code'];

$q = $conn->query("
    SELECT p.name, v.price, v.stock
    FROM product_variants v
    JOIN products p ON p.id = v.product_id
    WHERE v.barcode = '$code'
");

echo json_encode($q->fetch_assoc());