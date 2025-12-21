<?php
include '../database/db.php';
$items = $conn->query("
    SELECT p.name, v.barcode
    FROM product_variants v
    JOIN products p ON p.id = v.product_id
");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Print Barcodes</title>
    <style>
    @media print {
        body {
            margin: 0
        }
    }

    .label {
        width: 48mm;
        height: 25mm;
        border: 1px solid #000;
        float: left;
        padding: 3mm;
        margin: 1mm;
        text-align: center;
        font-size: 10px;
    }

    .label img {
        width: 100%;
        height: 30px;
    }
    </style>
</head>

<body>

    <?php while($row = $items->fetch_assoc()): ?>
    <div class="label">
        <div><?= $row['name'] ?></div>
        <img src="barcodes/<?= $row['barcode'] ?>.png">
        <div><?= $row['barcode'] ?></div>
    </div>
    <?php endwhile; ?>

    <script>
    window.print()
    </script>
</body>

</html>