<?php include 'includes/header.php'; ?>
<main class="col-md-9 col-lg-10 px-4 py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Print Order Request</h3>
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
    SELECT 
        id,
        request_no,
        full_name,
        status,
        print_type,
        created_at
    FROM print_requests
    ORDER BY created_at DESC
";

$result = $conn->query($query);
?>

    <!-- Print Orders Table -->
    <div class="card table-card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Print Request No.</th>
                            <th></th>
                            <th></th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
$statusMap = [
    'Order Placed' => [
        'row'   => 'table-info',
        'badge' => 'primary'
    ],
    'Pending' => [
        'row'   => 'table-warning',
        'badge' => 'warning text-dark'
    ],
    'Approved' => [
        'row'   => 'table-success',
        'badge' => 'success'
    ],
    'Printing' => [
        'row'   => 'table-info',
        'badge' => 'primary'
    ],
    'Ready for Pickup' => [
        'row'   => 'table-info',
        'badge' => 'primary'
    ],
    'Completed' => [
        'row'   => 'table-success',
        'badge' => 'success'
    ],
    'Cancelled' => [
        'row'   => 'table-danger',
        'badge' => 'danger'
    ],
    'Rejected' => [
        'row'   => 'table-danger',
        'badge' => 'danger'
    ],
];

$status = $row['status'];
$rowClass = $statusMap[$status]['row'] ?? '';
$badgeClass = $statusMap[$status]['badge'] ?? 'secondary';
?>


                        <tr class="<?= $rowClass ?> bg-opacity-25">

                            <!-- NAME -->
                            <td>
                                <div class="fw-semibold">
                                    <?= htmlspecialchars($row['full_name']) ?>
                                </div>
                                <small class="text-muted">
                                    <?= date('M d, Y', strtotime($row['created_at'])) ?>
                                </small>
                            </td>

                            <!-- REQUEST NO -->
                            <td><?= htmlspecialchars($row['request_no']) ?></td>

                            <!-- SERVICE -->
                            <td><?= htmlspecialchars($row['print_type']) ?></td>

                            <!-- EMPTY COLUMN (OPTIONAL) -->
                            <td>—</td>

                            <!-- STATUS -->

                            <td>
                                <span class="badge bg-<?= $badgeClass ?>">
                                    <?= htmlspecialchars($status) ?>
                                </span>
                            </td>


                            <!-- ACTION -->
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="viewOrder(<?= (int)$row['id'] ?>)">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <button class="btn btn-sm btn-outline-success"
                                    onclick="printReceipt(<?= (int)$row['id'] ?>)">
                                    <i class="bi bi-printer"></i>
                                </button>

                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="deleteOrder(<?= (int)$row['id'] ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>

                        </tr>

                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No print orders found
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <script>
                    function printReceipt(orderId) {
                        window.open(
                            'print/print_receipt.php?id=' + orderId,
                            '_blank',
                            'width=380,height=600'
                        );
                    }
                    </script>


                </table>
            </div>

        </div>
    </div>

</main>
</div>
</div>
<!-- ================= VIEW ORDER MODAL ============= -->
<div class="modal fade" id="viewProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--brand-gradient); color: white;">
                <h5 class="modal-title">Print Request Details</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewProductContent">
                <div class="text-center py-5 text-muted">Loading...</div>
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
                                    <option value="Printing">Printing</option>
                                    <option value="Stationery">Stationery</option>
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
function deleteOrder(orderId) {
    Swal.fire({
        title: 'Delete this request?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('print/delete_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + orderId
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
        }
    });
}
</script>

<script src="print/js/ajax.js"></script>
<?php include 'includes/footer.php'; ?>