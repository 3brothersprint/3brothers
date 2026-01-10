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
                    <tbody id="ordersTable">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Loading orders...
                            </td>
                        </tr>
                    </tbody>
                    <script>
                    document.addEventListener("DOMContentLoaded", fetchOrders);

                    function fetchOrders() {
                        fetch("get-product/fetch_orders.php")
                            .then(res => res.json())
                            .then(res => {
                                const table = document.getElementById("ordersTable");
                                table.innerHTML = "";

                                if (!res.success || res.data.length === 0) {
                                    table.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No product orders found
                        </td>
                    </tr>`;
                                    return;
                                }

                                res.data.forEach(order => {
                                    const statusMap = {
                                        "Order Placed": ["table-info", "primary"],
                                        "To Ship": ["table-warning", "warning text-dark"],
                                        "To Transit": ["table-success", "success"],
                                        "Out for Delivery": ["table-info", "primary"],
                                        "Delivered": ["table-info", "primary"],
                                        "Cancelled": ["table-danger", "danger"]
                                    };

                                    const status = order.status;
                                    const rowClass = statusMap[status]?. [0] ?? "";
                                    const badgeClass = statusMap[status]?. [1] ?? "secondary";

                                    table.innerHTML += `
                <tr class="${rowClass} bg-opacity-25">
                    <td>
                        <div class="fw-semibold">${escapeHtml(order.recipient_name)}</div>
                        <small class="text-muted">
                            ${new Date(order.created_at).toLocaleDateString()}
                        </small>
                    </td>

                    <td>${escapeHtml(order.order_no)}</td>
                    <td>₱${order.total_amount}</td>
                    <td>—</td>

                    <td>
                        <span class="badge bg-${badgeClass}">
                            ${escapeHtml(status)}
                        </span>
                    </td>

                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary"
                            onclick="viewOrder(${order.id})">
                            <i class="bi bi-eye"></i>
                        </button>

                        <button class="btn btn-sm btn-outline-success"
                            onclick="printReceipt(${order.id})">
                            <i class="bi bi-printer"></i>
                        </button>

                        <button class="btn btn-sm btn-outline-danger"
                            onclick="deleteOrder(${order.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
                                });
                            });
                    }

                    /* SECURITY: prevent XSS */
                    function escapeHtml(text) {
                        return text
                            .toString()
                            .replace(/&/g, "&amp;")
                            .replace(/</g, "&lt;")
                            .replace(/>/g, "&gt;")
                            .replace(/"/g, "&quot;")
                            .replace(/'/g, "&#039;");
                    }

                    setInterval(fetchOrders, 10000);
                    </script>

                    <script>
                    function printReceipt(orderId) {
                        const frame = document.getElementById("printFrame");

                        frame.src = 'get-product/print_receipt.php?id=' + orderId;

                        frame.onload = () => {
                            frame.contentWindow.focus();
                            frame.contentWindow.print();
                        };
                    }
                    </script>
                </table>
            </div>

        </div>
    </div>
    <iframe id="printFrame" style="display:none;"></iframe>

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
            fetch('get-product/delete_order.php', {
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