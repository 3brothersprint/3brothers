<?php include 'includes/header.php'; ?>
<main class="col-md-9 col-lg-10 px-4 py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Products</h3>

        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                <i class="bi bi-tags"></i> Categories
            </button>

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                <i class="bi bi-plus-lg"></i> Add Product
            </button>
        </div>
    </div>
    <?php if(isset($_SESSION['message'])): ?>
    <div class="alert alert-<?=$_SESSION['msg_type']?> alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <?= $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php 
  unset($_SESSION['message']);
  unset($_SESSION['msg_type']);
endif; 
?>
    <?php
    include 'database/db.php';
$query = "
    SELECT p.*, 
           (SELECT image FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC LIMIT 1) AS image
    FROM products p
    ORDER BY p.id DESC
";
$result = $conn->query($query);
?>
    <div class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" id="search" class="form-control" placeholder="Search name, SKU, barcode">
        </div>

        <div class="col-md-3">
            <select id="categoryFilter" class="form-select">
                <option value="">All Categories</option>
                <option value="Printing">Printing</option>
                <option value="Stationery">Stationery</option>
            </select>
        </div>
    </div>

    <!-- Product Table -->
    <div class="card table-card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>

                        <?php
            // Stock badge logic
            if ($row['stock'] <= 0) {
                $stockBadge = '<span class="badge bg-danger">Out of Stock</span>';
            } elseif ($row['stock'] <= 10) {
                $stockBadge = '<span class="badge bg-warning text-dark">Low Stock</span>';
            } else {
                $stockBadge = '<span class="badge bg-success">In Stock</span>';
            }

            $image = $row['image'] 
                ? "products/uploads/" . $row['image'] 
                : "https://via.placeholder.com/50";
        ?>

                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= $image ?>" class="rounded" width="50" height="50" />
                                    <div>
                                        <div class="fw-semibold"><?= htmlspecialchars($row['name']) ?></div>
                                        <small class="text-muted">SKU: <?= htmlspecialchars($row['sku']) ?></small>
                                    </div>
                                </div>
                            </td>

                            <td><?= htmlspecialchars($row['category']) ?></td>

                            <td>₱<?= number_format($row['price'], 2) ?></td>

                            <td><?= $row['stock'] ?></td>

                            <td>
                                <?= $row['status'] === 'Active'
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>' ?>
                            </td>

                            <td class="text-end">

                                <button class="btn btn-sm btn-light" onclick="editProduct(<?= (int)$row['id'] ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>


                                <button class="btn btn-sm btn-light text-danger"
                                    onclick="deleteProduct(<?= $row['id'] ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>



                                <!-- PRINT BARCODE -->
                                <button class="btn btn-sm btn-light text-primary"
                                    onclick="printBarcode(<?= $row['id'] ?>)">
                                    <i class="bi bi-upc-scan"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="viewProduct(<?= (int)$row['id'] ?>)">
                                    <i class="bi bi-eye"></i>
                                </button>

                            </td>

                        </tr>

                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No products found
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</main>
</div>
</div>

<!-- ================= VIEW PRODUCT MODAL ============= -->
<div class="modal fade" id="viewProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header" style="background: var(--brand-gradient); color: white;">
                <h5 class="modal-title">Product Details</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="viewProductContent">
                <div class="text-center py-5 text-muted">
                    Loading product...
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ================= EDIT PRODUCT MODAL ================= -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header" style="background: var(--brand-gradient); color: white;">
                <h5 class="modal-title">Edit Product</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="editProductContent">
                <div class="text-center py-5 text-muted">Loading...</div>
            </div>

        </div>
    </div>
</div>


<!-- ================= CATEGORY MODAL ================= -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">

            <div class="modal-header" style="background: var(--brand-gradient); color: white;">
                <h5 class="modal-title">
                    <i class="bi bi-tags me-2"></i> Manage Categories
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- Add Category -->
                <form action="database/code.php" method="post">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Add New Category</label>
                        <div class="input-group">
                            <input type="text" name="cat_name" class="form-control"
                                placeholder="e.g. Printing, Stickers" />
                            <button type="submit" name="add_cat" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                        <small class="text-muted">
                            Categories help organize your products.
                        </small>
                    </div>
                </form>

                <!-- Category List -->
                <div>
                    <label class="form-label fw-semibold mb-2">Existing Categories</label>

                    <ul class="list-group category-list">
                        <?php 
                        include 'database/db.php';

                        $query = "SELECT * FROM category";
                        $run_query = mysqli_query($conn, $query);

                        if(mysqli_num_rows($run_query) > 0){
                            while ($row = mysqli_fetch_assoc($run_query)) {
                               ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= $row['cat_name'] ?>
                            <a href="products/delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-light text-danger">
                                <i class="bi bi-trash"></i>
                            </a>
                        </li>
                        <?php
                            }
                        }
                        ?>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>


<!-- ================= PRODUCT MODAL ================= -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content custom-modal">

            <!-- Header -->
            <div class="modal-header" style="background: var(--brand-gradient); color: white;">
                <h5 class="modal-title">
                    <i class="bi bi-box-seam me-2"></i> Add / Edit Product
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="productForm" method="POST" enctype="multipart/form-data" action="products/save_product.php">

                <!-- BODY -->
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

                    <!-- BASIC INFO -->
                    <div class="modal-section">
                        <h6 class="section-title">Basic Information</h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="name" id="productName" class="form-control" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">SKU</label>
                                <input type="text" name="sku" id="sku" class="form-control" readonly required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select" required>
                                    <option value="">Select</option>

                                    <?php 
                        include 'database/db.php';

                        $query = "SELECT * FROM category";
                        $run_query = mysqli_query($conn, $query);

                        if(mysqli_num_rows($run_query) > 0){
                            while ($row = mysqli_fetch_assoc($run_query)) {
                               ?>
                                    <option value="<?= $row['cat_name'] ?>"><?= $row['cat_name'] ?></option>
                                    <?php
                            }
                        }
                        ?>

                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- PRICING -->
                    <div class="modal-section">
                        <h6 class="section-title">Base Pricing & Inventory</h6>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Base Price (₱)</label>
                                <input type="number" name="price" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Base Stock</label>
                                <input type="number" name="stock" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- VARIANTS -->
                    <div class="modal-section">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="section-title mb-0">Variants (Price & Stock per Variant)</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addVariant()">
                                + Add Variant
                            </button>
                        </div>

                        <div id="variantWrapper"></div>
                    </div>

                    <!-- SPECIFICATIONS -->
                    <div class="modal-section">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="section-title mb-0">Specifications</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSpec()">
                                + Add Spec
                            </button>
                        </div>

                        <div id="specWrapper"></div>
                    </div>

                    <!-- IMAGES -->
                    <div class="modal-section">
                        <h6 class="section-title">Product Images (Max 5)</h6>
                        <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                        <div class="row mt-3" id="previewImages"></div>
                    </div>

                    <!-- DESCRIPTION -->
                    <div class="modal-section">
                        <h6 class="section-title">Description</h6>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>

                    <!-- SMALL DESCRIPTION -->
                    <div class="modal-section">
                        <h6 class="section-title">Small Description</h6>
                        <textarea name="small_description" class="form-control" rows="3"></textarea>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Product
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<script>
function generateSKU(length = 15) {
    const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    let sku = "";

    for (let i = 0; i < length; i++) {
        sku += chars.charAt(Math.floor(Math.random() * chars.length));
    }

    return "#" + sku;
}

// AUTO SKU GENERATION
document.getElementById("productName").addEventListener("input", () => {
    document.getElementById("sku").value = generateSKU();
});
</script>

<script src="products/js/ajax.js"></script>
<?php include 'includes/footer.php'; ?>