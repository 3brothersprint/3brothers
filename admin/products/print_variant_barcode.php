<?php
include '../database/db.php';

$id = (int) $_GET['id'];

$variant = $conn->query("
    SELECT v.barcode, v.value, p.name
    FROM product_variants v
    JOIN products p ON p.id = v.product_id
    WHERE v.id = $id
")->fetch_assoc();

if (!$variant) {
    die("Variant not found");
}

$barcode = $variant['barcode'];
$name    = $variant['name'];
$value   = $variant['value'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Print Variant Barcode</title>

    <style>
    @page {
        size: A4;
        margin: 6mm;
    }

    body {
        margin: 0;
        font-family: Arial, Helvetica, sans-serif;
        background: #fff;
    }

    /* A4 GRID */
    .sheet {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 6mm;
    }

    /* GROCERY LABEL */
    .label {
        width: 50mm;
        height: 32mm;
        padding: 2mm;
        box-sizing: border-box;
        text-align: center;
        background: #fff;
    }

    .barcode {
        width: 100%;
        height: 18mm;
        object-fit: contain;
    }

    .name {
        font-size: 10px;
        font-weight: 700;
        line-height: 1.1;
        margin-top: 1mm;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .variant {
        font-size: 9px;
        color: #333;
        margin-top: 0.5mm;
    }

    .code {
        font-size: 9px;
        letter-spacing: 1.5px;
        margin-top: 0.5mm;
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
        <?php for ($i = 0; $i < 24; $i++): ?>
        <div class="label">
            <img class="barcode" src="barcodes/<?= htmlspecialchars($barcode) ?>.png">
            <div class="name"><?= htmlspecialchars($name) ?></div>
            <div class="variant"><?= htmlspecialchars($value) ?></div>
            <div class="code"><?= htmlspecialchars($barcode) ?></div>
        </div>
        <?php endfor; ?>
    </div>

</body>

</html>