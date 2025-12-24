<?php
include '../database/db.php';

$id = (int) $_GET['id'];

$product = $conn->query("
    SELECT name, barcode 
    FROM products 
    WHERE id = $id
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
    @page {
        size: A4;
        margin: 8mm;
    }

    body {
        margin: 0;
        font-family: Arial, sans-serif;
    }

    /* A4 GRID */
    .sheet {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8mm;
    }

    /* LABEL */
    .label {
        width: 50mm;
        height: 35mm;
        text-align: center;
        padding: 4mm;
        box-sizing: border-box;
        border: 1px dashed #ccc;
        /* remove if not needed */
    }

    .label img {
        width: 100%;
        height: 18mm;
        object-fit: contain;
    }

    .name {
        font-size: 10px;
        font-weight: bold;
        margin-top: 2mm;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .code {
        font-size: 9px;
        letter-spacing: 1px;
    }

    @media print {
        .label {
            page-break-inside: avoid;
        }
    }
    </style>
</head>

<body onload="window.print()">

    <div class="sheet">
        <?php
        // 24 labels per A4 page
        for ($i = 0; $i < 24; $i++):
        ?>
        <div class="label">
            <img src="barcodes/<?= $barcode ?>.png">
            <div class="name"><?= htmlspecialchars($name) ?></div>
            <div class="code"><?= $barcode ?></div>
        </div>
        <?php endfor; ?>
    </div>

</body>

</html>