<?php
require "../database/db.php";

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "<div class='alert alert-danger'>Invalid order</div>";
    exit;
}

/* ===============================
   FETCH ORDER
================================ */
$stmt = $conn->prepare("
    SELECT *
    FROM orders
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "<div class='alert alert-warning'>Order not found</div>";
    exit;
}

/* ===============================
   FETCH ORDER ITEMS
================================ */
$stmt = $conn->prepare("
    SELECT *
    FROM order_items
    WHERE order_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$items = $stmt->get_result();
$stmt->close();

/* ===============================
   FETCH LOGS (OPTIONAL)
================================ */
$logs = $conn->query("
    SELECT status, remarks, created_at
    FROM order_logs
    WHERE order_id = $id
    ORDER BY created_at DESC
");
?>

<div class="row g-3">

    <div class="col-md-6">
        <strong>Order No:</strong><br>
        <?= htmlspecialchars($order['order_no']) ?>
    </div>

    <div class="col-md-6">
        <strong>Customer:</strong><br>
        <?= htmlspecialchars($order['recipient_name']) ?>
    </div>

    <div class="col-md-6">
        <strong>Payment:</strong><br>
        <?= htmlspecialchars($order['payment_method']) ?>
    </div>
    <?php
$allowedStatuses = [
    'Order Placed',
    'To Ship',
    'To Transit',
    'Out for Delivery',
    'Delivered',
    'Cancelled'
];
?>

    <div class="col-md-6">
        <strong>Status:</strong><br>
        <span class="badge bg-primary"><?= htmlspecialchars($order['status']) ?></span>
    </div>
    <hr>

    <!-- UPDATE STATUS -->
    <div class="col-12">
        <h6 class="fw-bold mb-2">🔄 Update Order Status</h6>

        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select" id="orderStatus">
                    <option value="">-- Select Status --</option>
                    <?php foreach ($allowedStatuses as $status): ?>
                    <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>>
                        <?= $status ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Remark</label>
                <select class="form-select" id="orderRemark">
                    <option value="">-- Select Remark --</option>
                    <option value="Order has been packed">Order has been packed</option>
                    <option value="Handed to courier">Handed to courier</option>
                    <option value="Out for delivery">Out for delivery</option>
                    <option value="Successfully delivered">Successfully delivered</option>
                    <option value="Order cancelled by admin">Order cancelled by admin</option>
                </select>
            </div>

            <div class="col-12">
                <button class="btn btn-primary w-100" onclick="updateOrderStatus(<?= (int)$order['id'] ?>)">
                    <i class="bi bi-arrow-repeat"></i> Update Status
                </button>
            </div>

        </div>
    </div>

    <div class="col-12">
        <strong>Delivery Address:</strong>
        <div class="border rounded p-2 mt-1 text-muted">
            <?= htmlspecialchars($order['delivery_address']) ?>,
            <?= htmlspecialchars($order['city']) ?>,
            <?= htmlspecialchars($order['province']) ?>
        </div>
    </div>

    <hr>

    <!-- ORDER ITEMS -->
    <div class="col-12">
        <h6 class="fw-bold mb-2">📦 Order Items</h6>

        <ul class="list-group">
            <?php while ($item = $items->fetch_assoc()): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold"><?= htmlspecialchars($item['product_name']) ?></div>
                    <small class="text-muted">
                        Variant: <?= htmlspecialchars($item['variant']) ?> × <?= (int)$item['quantity'] ?>
                    </small>
                </div>
                <strong>₱<?= number_format($item['subtotal'], 2) ?></strong>
            </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <hr>

    <!-- ORDER LOGS -->
    <div class="col-12">
        <h6 class="fw-bold mb-2">📝 Order Logs</h6>

        <?php if ($logs->num_rows): ?>
        <ul class="list-group">
            <?php while ($log = $logs->fetch_assoc()): ?>
            <li class="list-group-item">
                <strong><?= htmlspecialchars($log['status']) ?></strong><br>
                <small class="text-muted"><?= htmlspecialchars($log['remarks']) ?></small>
                <div class="small text-muted">
                    <?= date('M d, Y h:i A', strtotime($log['created_at'])) ?>
                </div>
            </li>
            <?php endwhile; ?>
        </ul>
        <?php else: ?>
        <div class="text-muted">No logs yet</div>
        <?php endif; ?>
    </div>

</div>