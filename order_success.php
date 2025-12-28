<?php
session_start();
require "admin/database/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$orderId = (int)($_GET['order_id'] ?? 0);
$userId  = $_SESSION['user_id'];

if ($orderId <= 0) {
    header("Location: index.php");
    exit;
}

/* ===============================
   FETCH ORDER
================================ */
$stmt = $conn->prepare("
    SELECT *
    FROM orders
    WHERE id = ? AND user_id = ?
    LIMIT 1
");
$stmt->bind_param("ii", $orderId, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header("Location: index.php");
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
$stmt->bind_param("i", $orderId);
$stmt->execute();
$items = $stmt->get_result();
?>

<?php include 'includes/header.php'; ?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- SUCCESS CARD -->
                <div class="card border-0 shadow-sm text-center mb-4">
                    <div class="card-body py-5">

                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 64px;"></i>
                        </div>

                        <h4 class="fw-semibold mb-2">
                            Order Placed Successfully!
                        </h4>

                        <p class="text-muted mb-3">
                            Thank you for your purchase. Your order is now being processed.
                        </p>

                        <div class="badge bg-light text-dark px-3 py-2">
                            Order ID: <strong>#<?= htmlspecialchars($order['order_no'] ?? $order['id']) ?></strong>
                        </div>

                    </div>
                </div>

                <!-- ORDER DETAILS -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">

                        <h6 class="fw-semibold mb-3">Order Details</h6>

                        <?php while ($item = $items->fetch_assoc()): ?>
                        <div class="d-flex justify-content-between align-items-start mb-3">

                            <div class="d-flex gap-3">
                                <img src="<?= htmlspecialchars($item['product_image']) ?>" class="border rounded"
                                    width="60" height="60">

                                <div>
                                    <div class="fw-normal">
                                        <?= htmlspecialchars($item['product_name']) ?>
                                    </div>
                                    <div class="text-muted small">
                                        <?php
        if (!empty($item['variant'])) {
            $variants = json_decode($item['variant'], true);

            if (is_array($variants)) {
                foreach ($variants as $type => $data) {

                    // Case 1: { "Size": { "value": "A4" } }
                    if (is_array($data) && isset($data['value'])) {
                        echo "<div>{$type}: " . htmlspecialchars($data['value']) . "</div>";

                    // Case 2: { "Size": "A4" }
                    } elseif (is_string($data)) {
                        echo "<div>{$type}: " . htmlspecialchars($data) . "</div>";
                    }
                }
            }
        }
    ?>
                                        <div>x<?= (int)$item['quantity'] ?></div>
                                    </div>

                                </div>
                            </div>

                            <div class="fw-semibold text-danger">
                                ₱<?= number_format($item['price'] * $item['quantity'], 2) ?>
                            </div>
                        </div>
                        <?php endwhile; ?>

                        <hr>

                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span>₱<?= number_format($order['subtotal'], 2) ?></span>
                            </li>

                            <li class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Shipping Fee</span>
                                <span>₱<?= number_format($order['shipping_fee'], 2) ?></span>
                            </li>

                            <?php if ($order['discount'] > 0): ?>
                            <li class="d-flex justify-content-between mb-2 text-success">
                                <span>Voucher Discount</span>
                                <span>- ₱<?= number_format($order['discount'], 2) ?></span>
                            </li>
                            <?php endif; ?>

                            <li class="d-flex justify-content-between fw-semibold fs-6 mt-2">
                                <span>Total Paid</span>
                                <span class="text-danger">
                                    ₱<?= number_format($order['total_amount'], 2) ?>
                                </span>
                            </li>
                        </ul>

                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="d-flex gap-2 justify-content-center">
                    <a href="orders.php" class="btn btn-outline-secondary">
                        View My Orders
                    </a>

                    <a href="index.php" class="btn btn-danger">
                        Continue Shopping
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>