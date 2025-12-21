<?php
include '../database/db.php';

$id = $_GET['id'];

$variant = $conn->query("
    SELECT v.barcode, v.value, p.name
    FROM product_variants v
    JOIN products p ON p.id = v.product_id
    WHERE v.id = '$id'
")->fetch_assoc();

if (!$variant) {
    die("Variant not found");
}
?>

<!DOCTYPE html>
<html>

<body onload="window.print()">

    <div style="width:50mm;text-align:center">
        <img src="barcodes/<?= $variant['barcode'] ?>.png">
        <div><strong><?= htmlspecialchars($variant['name']) ?></strong></div>
        <div><?= htmlspecialchars($variant['value']) ?></div>
        <small><?= $variant['barcode'] ?></small>
    </div>

</body>

</html>