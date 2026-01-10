<?php include 'includes/header.php'; ?>
<?php
include 'database/db.php';

$products = $conn->query("
    SELECT 
        pv.id AS variant_id,
        p.name AS product_name,
        p.product_no,
        pv.type,
        p.sku,
        pv.price,
        pv.stock,
        p.status
    FROM product_variants pv
    INNER JOIN products p ON p.id = pv.product_id
    ORDER BY p.name ASC, pv.type ASC
");

?>

<main class="col-md-9 col-lg-10 px-4 py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Inventory Management</h3>

        <input type="text" id="searchInventory" class="form-control w-25" placeholder="Search product...">
    </div>

    <!-- Inventory Table -->
    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="inventoryTable">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Variant Type</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($products && $products->num_rows > 0): ?>
                        <?php while ($row = $products->fetch_assoc()): ?>
                        <?php
                            $lowStock = ($row['stock'] <= ($row['reorder_level'] ?? 5));
                        ?>
                        <tr class="<?= $lowStock ? 'table-warning' : '' ?>">

                            <!-- PRODUCT -->
                            <td>
                                <div class="fw-semibold">
                                    <?= htmlspecialchars($row['product_name']) ?>
                                </div>
                                <small class="text-muted">
                                    ID: <?= $row['product_no'] ?>
                                </small>
                            </td>

                            <!-- VARIANT -->
                            <td>
                                <?= htmlspecialchars($row['type']) ?>
                            </td>

                            <!-- SKU -->
                            <td>
                                <code><?= htmlspecialchars($row['sku']) ?></code>
                            </td>

                            <!-- PRICE -->
                            <td>
                                ₱<?= number_format($row['price'], 2) ?>
                            </td>

                            <!-- STOCK -->
                            <td>
                                <span class="badge bg-<?= $lowStock ? 'danger' : 'success' ?>">
                                    <?= (int)$row['stock'] ?>
                                </span>
                                <?php if ($lowStock): ?>
                                <small class="text-danger d-block">Low stock</small>
                                <?php endif; ?>
                            </td>

                            <!-- STATUS -->
                            <td>
                                <span class="badge bg-<?= $row['status'] === 'Active' ? 'success' : 'secondary' ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>

                            <!-- ACTIONS -->
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary"
                                    onclick="printStock(<?= (int)$row['variant_id'] ?>)" title="Print Inventory">
                                    <i class="bi bi-printer"></i>
                                </button>

                                <button class="btn btn-sm btn-outline-secondary"
                                    onclick="viewVariant(<?= (int)$row['variant_id'] ?>)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>

                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No variants found
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</main>

<script>
function printStock(id) {
    window.open(
        'inventory/print_stock.php?id=' + id,
        '_blank',
        'width=380,height=600'
    );
}
</script>


<script>
/* ================= SEARCH ================= */
document.getElementById('searchInventory').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    document.querySelectorAll('#inventoryTable tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ?
            '' :
            'none';
    });
});

/* ================= MODAL ================= */
function editStock(id) {
    document.getElementById('stockProductId').value = id;
    new bootstrap.Modal(document.getElementById('stockModal')).show();
}

function saveStock() {
    const id = document.getElementById('stockProductId').value;
    const qty = document.getElementById('stockQty').value;

    fetch('inventory/update_stock.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id=${id}&stock=${qty}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
}

function viewProduct(id) {
    window.location.href = 'products.php?view=' + id;
}
</script>

<?php include 'includes/footer.php'; ?>