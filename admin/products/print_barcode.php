<?php
include '../database/db.php';

$id = $_GET['id'];

$product = $conn->query("
    SELECT name, barcode 
    FROM products 
    WHERE id = '$id'
")->fetch_assoc();

if (!$product) {
    die("Product not found");
}

$barcode = $product['barcode'];
$name    = $product['name'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Print Barcode</title>

    <style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        text-align: center;
    }

    .label {
        width: 50mm;
        padding: 8px;
    }

    img {
        width: 100%;
        height: auto;
    }

    .name {
        font-size: 12px;
        font-weight: bold;
        margin-top: 5px;
    }

    .code {
        font-size: 11px;
        letter-spacing: 1px;
    }

    @media print {
        body {
            margin: 0
        }
    }
    </style>
</head>

<body onload="window.print()">

    <div class="label">
        <img src="barcodes/<?= $barcode ?>.png">
        <div class="name"><?= htmlspecialchars($name) ?></div>
        <div class="code"><?= $barcode ?></div>
    </div>

</body>

</html>