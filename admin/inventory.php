<?php include 'includes/header.php'; ?>
<?php
include 'database/db.php';

$products = $conn->query("
    SELECT 
        id,
        product_no,
        name,
        price,
        stock,
        status,
        created_at
    FROM products
    ORDER BY name ASC
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
                                $lowStock = ($row['stock'] <= 5);
                                ?>
                        <tr class="<?= $lowStock ? 'table-warning' : '' ?>">
                            <td>
                                <div class="fw-semibold">
                                    <?= htmlspecialchars($row['name']) ?>
                                </div>
                                <small class="text-muted">
                                    ID: <?= $row['product_no'] ?>
                                </small>
                            </td>

                            <td>₱<?= number_format($row['price'], 2) ?></td>

                            <td>
                                <span class="badge bg-<?= $lowStock ? 'danger' : 'success' ?>">
                                    <?= (int)$row['stock'] ?>
                                </span>
                                <?php if ($lowStock): ?>
                                <small class="text-danger d-block">
                                    Low stock
                                </small>
                                <?php endif; ?>
                            </td>

                            <td>
                                <span class="badge bg-<?= $row['status'] === 'Active' ? 'success' : 'secondary' ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>

                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary"
                                    onclick="printStock(<?= (int)$row['id'] ?>)" title="Print Inventory">
                                    <i class="bi bi-printer"></i>
                                </button>


                                <button class="btn btn-sm btn-outline-secondary"
                                    onclick="viewProduct(<?= $row['id'] ?>)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
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