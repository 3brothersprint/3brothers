<?php
session_start();
include 'includes/header.php';
include 'database/db.php';

/* =====================
   AUTH CHECK
===================== */
$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0) {
    header("Location: login.php");
    exit;
}
?>

<div class="container py-5">

    <h3 class="mb-4 fw-bold">My Orders</h3>

    <!-- Tabs -->
    <ul class="nav nav-pills mb-4" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#printOrders">
                🖨 Print Orders
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#productOrders">
                📦 Product Orders
            </button>
        </li>
    </ul>

    <div class="tab-content">

        <!-- ================= PRINT ORDERS ================= -->
        <div class="tab-pane fade show active" id="printOrders">
            <div class="card shadow-sm mb-4">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Print Request #</th>
                                <th>Type</th>
                                <th>Qty</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                        $stmt = $conn->prepare("
                            SELECT *
                            FROM print_requests
                            WHERE user_id = ?
                            ORDER BY created_at DESC
                        ");
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows):
                            $i = 1;
                            while ($row = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td class="text-center"><?= htmlspecialchars($row['request_no']) ?></td>
                                <td><?= htmlspecialchars($row['print_type']) ?></td>
                                <td><?= (int)$row['copies'] ?></td>
                                <td>
                                    <?php
                                        $statusColors = [
                                            'Order Placed'     => 'secondary',
                                            'Pending'          => 'warning',
                                            'Approved'         => 'success',
                                            'Printing'         => 'primary',
                                            'Ready for Pickup' => 'info',
                                            'Completed'        => 'success',
                                            'Cancelled'        => 'danger',
                                        ];

                                        $badgeColor = $statusColors[$row['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $badgeColor ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <a href="track_order.php?request_no=<?= urlencode($row['request_no']) ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        Track
                                    </a>

                                    <?php if ($row['status'] === 'Order Placed'): ?>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="cancelOrder(<?= (int)$row['id'] ?>)">
                                        Cancel
                                    </button>
                                    <?php endif; ?>

                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No print orders found
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= PRODUCT ORDERS ================= -->
        <div class="tab-pane fade" id="productOrders">

            <?php
    $stmt = $conn->prepare("
        SELECT o.*
        FROM orders o
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $orders = $stmt->get_result();

    if ($orders->num_rows):
        while ($order = $orders->fetch_assoc()):
    ?>

            <div class="card shadow-sm mb-4 rounded-4">
                <div class="card-body">

                    <!-- HEADER -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="fw-semibold">
                                Order #<?= htmlspecialchars($order['order_no']) ?>
                            </span>
                            <div class="text-muted small">
                                <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?>
                            </div>
                        </div>

                        <?php
                $statusColors = [
                    'Pending'   => 'warning',
                    'Paid'      => 'primary',
                    'Shipped'   => 'info',
                    'Delivered' => 'success',
                    'Cancelled' => 'danger'
                ];
                $badge = $statusColors[$order['status']] ?? 'secondary';
                ?>
                        <span class="badge bg-<?= $badge ?>">
                            <?= htmlspecialchars($order['status']) ?>
                        </span>
                    </div>

                    <hr>

                    <!-- ITEMS -->
                    <?php
            $itemStmt = $conn->prepare("
                SELECT *
                FROM order_items
                WHERE order_id = ?
            ");
            $itemStmt->bind_param("i", $order['id']);
            $itemStmt->execute();
            $items = $itemStmt->get_result();

            while ($item = $items->fetch_assoc()):
            ?>
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <img src="<?= htmlspecialchars($item['product_image']) ?>" class="rounded border" width="70"
                            height="70" style="object-fit: cover">

                        <div class="flex-grow-1">
                            <div class="fw-semibold">
                                <?= htmlspecialchars($item['product_name']) ?>
                            </div>

                            <div class="text-muted small">
                                <?= htmlspecialchars($item['variant']) ?>
                            </div>

                            <div class="text-muted small">
                                x<?= (int)$item['quantity'] ?>
                            </div>
                        </div>

                        <div class="fw-semibold text-danger">
                            ₱<?= number_format($item['price'] * $item['quantity'], 2) ?>
                        </div>
                    </div>
                    <?php endwhile; ?>

                    <hr>

                    <!-- FOOTER -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Payment: <?= strtoupper($order['payment_method']) ?>
                        </div>

                        <div class="fw-semibold">
                            Total:
                            <span class="text-danger">
                                ₱<?= number_format($order['total_amount'], 2) ?>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="order_details.php?order_no=<?= urlencode($order['order_no']) ?>"
                            class="btn btn-outline-primary btn-sm">
                            View Details
                        </a>

                        <?php if ($order['status'] === 'Order Placed'): ?>
                        <button class="btn btn-outline-danger btn-sm"
                            onclick="cancelProductOrder(<?= (int)$order['id'] ?>)">
                            Cancel
                        </button>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <?php endwhile; else: ?>

            <div class="text-center text-muted py-5">
                <i class="bi bi-box-seam fs-1 mb-3"></i>
                <div>No product orders yet</div>
            </div>

            <?php endif; ?>

        </div>

    </div>
</div>
<script>
function cancelOrder(requestId) {
    Swal.fire({
        title: "Cancel this print request?",
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, cancel it"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("orders/cancel_order.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "request_id=" + encodeURIComponent(requestId)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire("Cancelled!", data.message, "success")
                            .then(() => location.reload());
                    } else {
                        Swal.fire("Error", data.message, "error");
                    }
                })
                .catch(() => {
                    Swal.fire("Error", "Server error occurred", "error");
                });
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>