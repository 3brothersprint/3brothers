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
                    <tbody id="ordersTableBody">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Loading orders...
                            </td>
                        </tr>
                    </tbody>
                    <script>
                    function fetchOrders() {
                        fetch('print/ajax/fetch_orders.php')
                            .then(res => res.text())
                            .then(html => {
                                document.getElementById('ordersTableBody').innerHTML = html;
                            });
                    }

                    // Initial load
                    fetchOrders();

                    // Auto-refresh every 10 seconds (optional)
                    setInterval(fetchOrders, 10000);
                    </script>

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