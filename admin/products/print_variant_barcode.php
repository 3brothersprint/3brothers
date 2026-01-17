<?php
include '../database/db.php';

$id = (int) $_GET['id'];

$variant = $conn->query("
    SELECT v.barcode, v.value, v.price, p.name
    FROM product_variants v
    JOIN products p ON p.id = v.product_id
    WHERE v.id = $id
")->fetch_assoc();

if (!$variant) {
    die("Variant not found");
}

$barcode = $variant['barcode'];
$name    = $variant['name'];
$price   = number_format($variant['price'], 2);
$value   = $variant['value'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Print Variant Barcode</title>

    <style>
    @page {
        size: 3in 2in;
        margin: 0;
    }

    body {
        margin: 0;
        font-family: Arial, Helvetica, sans-serif;
        background: #fff;
    }

    .sheet {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0;
        /* no spacing between labels */
        padding: 0;
    }

    .label {
        width: 3in;
        height: 2in;
        padding: 0;
        box-sizing: border-box;
        border: 1px solid #000;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .name {
        font-weight: bold;
        font-size: 16px;
        /* bigger */
        text-transform: uppercase;
        margin: 2px 0 0 0;
    }

    .barcode img {
        width: 100%;
        height: 50px;
        object-fit: contain;
        margin: 0;
    }

    .code {
        font-size: 10px;
        margin: 0;
    }

    .price {
        font-weight: bold;
        font-size: 14px;
        /* bigger */
        margin: 2px 0;
    }

    @media print {
        body {
            margin: 0;
        }

        .sheet {
            gap: 0;
        }

        .label {
            page-break-inside: avoid;
        }
    }
    </style>
</head>

<body onload="window.print()">

    <div class="sheet">
        <?php for ($i = 0; $i < 24; $i++): ?>
        <div class="label">
            <div class="name"><?= htmlspecialchars($name) ?></div>
            <div class="barcode">
                <img src="barcodes/<?= $barcode ?>.png" alt="<?= $barcode ?>">
                <div class="code"><?= $barcode ?></div>
            </div>
            <div class="price">₱<?= $price ?></div>
        </div>
        <?php endfor; ?>
    </div>

</body>

</html>