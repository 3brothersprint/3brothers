<?php include 'includes/header.php'; ?>
<main class="col-md-9 col-lg-10 px-4 py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Product Orders</h3>
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
        *
    FROM orders
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
                            <th>Order No.</th>
                            <th>Price</th>
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
    'To Ship' => [
        'row'   => 'table-warning',
        'badge' => 'warning text-dark'
    ],
    'To Transit' => [
        'row'   => 'table-success',
        'badge' => 'success'
    ],
    'Out for Delivery' => [
        'row'   => 'table-info',
        'badge' => 'primary'
    ],
    'Delivered' => [
        'row'   => 'table-info',
        'badge' => 'primary'
    ],
    'Cancelled' => [
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
                                    <?= htmlspecialchars($row['recipient_name']) ?>
                                </div>
                                <small class="text-muted">
                                    <?= date('M d, Y', strtotime($row['created_at'])) ?>
                                </small>
                            </td>

                            <!-- REQUEST NO -->
                            <td><?= htmlspecialchars($row['order_no']) ?></td>

                            <!-- SERVICE -->
                            <td>₱<?= htmlspecialchars($row['total_amount']) ?></td>

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
                            'get-product/print_receipt.php?id=' + orderId,
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

<script src="get-product/js/ajax.js"></script>
<?php include 'includes/footer.php'; ?>