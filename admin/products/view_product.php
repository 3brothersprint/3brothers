<?php
include '../database/db.php';

if (!isset($_GET['id'])) {
    exit('Invalid request');
}

$id = (int) $_GET['id'];

/* PRODUCT */
$product = $conn->query("
    SELECT * FROM products WHERE id = '$id'
")->fetch_assoc();

if (!$product) {
    exit('Product not found');
}

/* VARIANTS */
$variants = $conn->query("
    SELECT * FROM product_variants WHERE product_id = '$id'
");
?>

<div class="mb-3">
    <h5><?= htmlspecialchars($product['name']) ?></h5>
    <small class="text-muted">
        Product No: <?= $product['product_no'] ?> |
        SKU: <?= $product['sku'] ?> |
        Category: <?= $product['category'] ?>
    </small>
</div>

<hr>

<!-- PRODUCT BARCODE -->
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <strong>Barcode:</strong> <?= $product['barcode'] ?>
    </div>

    <button class="btn btn-sm btn-outline-primary" onclick="printBarcode(<?= $product['id'] ?>)">
        <i class="bi bi-printer"></i> Print
    </button>
</div>

<img src="/print/admin/products/barcodes/<?= $product['barcode'] ?>.png" class="img-fluid mb-3" style="max-width:250px">


<hr>

<h6>Pricing & Stock</h6>
<ul class="list-group mb-3">
    <li class="list-group-item">
        Price: ₱<?= number_format($product['price'], 2) ?>
    </li>
    <li class="list-group-item">
        Stock: <?= $product['stock'] ?>
    </li>
    <li class="list-group-item align-items-center">
        <span>Status: </span>

        <?php if ($product['status'] === 'Active'): ?>
        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
            <i class="bi bi-check-circle me-1"></i> Active
        </span>
        <?php else: ?>
        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
            <i class="bi bi-x-circle me-1"></i> Inactive
        </span>
        <?php endif; ?>
    </li>

</ul>

<hr>

<h6>Variants</h6>

<?php if ($variants->num_rows > 0): ?>
<?php while ($v = $variants->fetch_assoc()): ?>
<div class="border rounded p-2 mb-2">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <strong><?= htmlspecialchars($v['type']) ?>:</strong>
            <?= htmlspecialchars($v['value']) ?><br>
            ₱<?= number_format($v['price'], 2) ?> |
            Stock: <?= $v['stock'] ?><br>
            <small><?= $v['barcode'] ?></small>
        </div>

        <button class="btn btn-sm btn-outline-primary" onclick="printVariantBarcode(<?= $v['id'] ?>)">
            <i class="bi bi-printer"></i>
        </button>
    </div>

    <img src="/print/admin/products/barcodes/<?= $product['barcode'] ?>.png" class="img-fluid mb-3"
        style="max-width:250px">

</div>
<?php endwhile; ?>
<?php else: ?>
<div class="text-muted">No variants</div>
<?php endif; ?>