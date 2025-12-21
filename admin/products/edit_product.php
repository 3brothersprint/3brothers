<?php
include '../database/db.php';

$id = (int)($_GET['id'] ?? 0);

$product = $conn->query("SELECT * FROM products WHERE id='$id'")->fetch_assoc();
if (!$product) exit('Product not found');
?>

<form method="POST" action="update_product.php">

    <input type="hidden" name="id" value="<?= $product['id'] ?>">

    <div class="row g-3">

        <div class="col-md-6">
            <label>Product Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>"
                required>
        </div>

        <div class="col-md-3">
            <label>Price</label>
            <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" required>
        </div>

        <div class="col-md-3">
            <label>Stock</label>
            <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" required>
        </div>

        <div class="col-md-3">
            <label>Status</label>
            <select name="status" class="form-select">
                <option <?= $product['status']=="Active"?"selected":"" ?>>Active</option>
                <option <?= $product['status']=="Inactive"?"selected":"" ?>>Inactive</option>
            </select>
        </div>

        <div class="col-12">
            <button class="btn btn-primary mt-3">
                <i class="bi bi-save"></i> Update Product
            </button>
        </div>

    </div>
</form>