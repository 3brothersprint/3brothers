<?php
session_start();
require "admin/database/db.php";

$user_id = $_SESSION['user_id'] ?? null;
$items = [];
$subtotal = 0;

$stmt = $conn->prepare("
    SELECT ci.*
    FROM checkout c
    JOIN checkout_items ci ON ci.checkout_id = c.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $items[] = $row;
    $subtotal += $row['price'] * $row['quantity'];
}
?>


<?php include 'includes/header.php'; ?>
<?php
$defaultAddress = null;

$stmt = $conn->prepare("
    SELECT *
    FROM user_addresses
    WHERE user_id = ? AND is_default = 1
    LIMIT 1
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $defaultAddress = $result->fetch_assoc();
}
?>

<!-- Checkout Content -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Left: Checkout Form -->
            <div class="col-lg-8">
                <!-- Delivery Address -->
                <div class="card border-0 shadow-sm mb-2">
                    <div class="card-body px-4 py-3">

                        <!-- Header -->
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                            <span class="fw-semibold text-danger">Delivery Address</span>
                        </div>

                        <?php if ($defaultAddress): ?>
                        <!-- Address Row -->
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div class="d-flex flex-wrap align-items-center gap-2">

                                <span class="fw-semibold">
                                    <?= htmlspecialchars($defaultAddress['full_name']) ?>
                                    (+63) <?= htmlspecialchars($defaultAddress['phone']) ?>
                                </span>

                                <span class="text-muted">
                                    <?= htmlspecialchars($defaultAddress['address']) ?>,
                                    <?= htmlspecialchars($defaultAddress['barangay_name']) ?>,
                                    <?= htmlspecialchars($defaultAddress['city_name']) ?>,
                                    <?= htmlspecialchars($defaultAddress['province_name']) ?>
                                    <?= htmlspecialchars($defaultAddress['zip_code']) ?>
                                </span>

                                <span class="badge border border-danger text-danger fw-normal">
                                    Default
                                </span>
                            </div>

                            <a href="addresses.php" class="text-primary fw-semibold text-decoration-none">
                                Change
                            </a>
                        </div>

                        <?php else: ?>
                        <!-- NO DEFAULT ADDRESS -->
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">No default address set</span>
                            <a href="addresses.php" class="text-primary fw-semibold text-decoration-none">
                                Add Address
                            </a>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>


                <?php foreach ($items as $item): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body px-4 py-3">

                        <div class="d-flex align-items-start gap-3">

                            <!-- Product Image -->
                            <img src="<?= htmlspecialchars($item['product_image']) ?>"
                                alt="<?= htmlspecialchars($item['product_name']) ?>" class="border rounded" width="80"
                                height="80" />

                            <!-- Product Info -->
                            <div class="flex-grow-1">
                                <div class="fw-normal text-dark">
                                    <?= htmlspecialchars($item['product_name']) ?>
                                </div>

                                <div class="text-muted small">
                                    <?= htmlspecialchars($item['variant_type']) ?>
                                </div>

                                <div class="text-muted small mt-1">
                                    x<?= (int)$item['quantity'] ?>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="text-end">
                                <div class="fw-semibold text-danger">
                                    ₱<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <?php endforeach; ?>



                <?php
$allowedSameDayCities = ['Cebu City', 'Consolacion', 'Liloan'];

$cityName = $defaultAddress['city_name'] ?? '';
$isSameDayAllowed = in_array($cityName, $allowedSameDayCities);
?>
                <!-- Delivery Option -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body px-4 py-3">
                        <div class="fw-semibold mb-3">Delivery Option</div>

                        <!-- STANDARD -->
                        <label class="delivery-option d-flex justify-content-between align-items-start mb-2">
                            <input type="radio" name="delivery" value="STANDARD" checked>

                            <div>
                                <div class="fw-semibold">Standard Delivery</div>
                                <div class="text-muted small">
                                    Estimated delivery: 3–5 days
                                </div>
                            </div>

                            <div class="fw-semibold text-danger">₱38</div>
                        </label>

                        <!-- EXPRESS -->
                        <label class="delivery-option d-flex justify-content-between align-items-start mb-2">
                            <input type="radio" name="delivery" value="EXPRESS">

                            <div>
                                <div class="fw-semibold">Express Delivery</div>
                                <div class="text-muted small">
                                    Estimated delivery: 1–2 days
                                </div>
                            </div>

                            <div class="fw-semibold text-danger">₱99</div>
                        </label>

                        <!-- SAME DAY EXPRESS -->
                        <label class="delivery-option d-flex justify-content-between align-items-start
                   <?= !$isSameDayAllowed ? 'opacity-50' : '' ?>">

                            <input type="radio" name="delivery" value="SAME_DAY"
                                <?= !$isSameDayAllowed ? 'disabled' : '' ?>>

                            <div>
                                <div class="fw-semibold">
                                    Same-Day Express
                                    <?php if (!$isSameDayAllowed): ?>
                                    <span class="badge bg-secondary ms-1">Unavailable</span>
                                    <?php endif; ?>
                                </div>

                                <div class="text-muted small">
                                    <?php if ($isSameDayAllowed): ?>
                                    Delivered within today (order before 2PM)
                                    <?php else: ?>
                                    Available only in Cebu City, Consolacion, Liloan
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="fw-semibold text-danger">₱149</div>
                        </label>

                    </div>
                </div>


                <!-- Payment Method -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body px-4 py-3">
                        <div class="fw-semibold mb-3">Payment Method</div>

                        <!-- COD -->
                        <label class="payment-option d-flex justify-content-between align-items-center mb-2">
                            <input type="radio" name="payment" value="COD" checked>

                            <div class="d-flex align-items-center gap-2">
                                <img src="https://via.placeholder.com/32" alt="COD">
                                <span class="fw-semibold">Cash on Delivery</span>
                            </div>

                            <span class="text-muted small">Recommended</span>
                        </label>

                        <!-- PAYMAYA -->
                        <label class="payment-option d-flex justify-content-between align-items-center mb-2">
                            <input type="radio" name="payment" value="PAYMAYA">

                            <div class="d-flex align-items-center gap-2">
                                <img src="https://via.placeholder.com/32" alt="PayMaya">
                                <span class="fw-semibold">PayMaya</span>
                            </div>

                            <span class="text-muted small">Fast & Secure</span>
                        </label>

                        <!-- GCASH -->
                        <label class="payment-option d-flex justify-content-between align-items-center">
                            <input type="radio" name="payment" value="GCASH">

                            <div class="d-flex align-items-center gap-2">
                                <img src="https://via.placeholder.com/32" alt="GCash">
                                <span class="fw-semibold">GCash</span>
                            </div>

                            <span class="text-muted small"></span>
                        </label>
                    </div>
                </div>

            </div>
            <div class="modal fade" id="walletConfirmModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4">
                        <div class="modal-header border-0">
                            <h5 class="modal-title fw-semibold">Confirm Payment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body text-center">
                            <p class="mb-2">
                                You selected <strong id="walletName"></strong>.
                            </p>
                            <p class="text-muted small">
                                You will be redirected to complete the payment.
                            </p>
                        </div>

                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light" id="cancelWallet">
                                Cancel
                            </button>
                            <button type="button" class="btn btn-danger" id="confirmWallet">
                                Continue
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
            let selectedPayment = 'COD';
            const modal = new bootstrap.Modal(document.getElementById('walletConfirmModal'));

            document.querySelectorAll('input[name="payment"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'GCASH' || this.value === 'PAYMAYA') {
                        selectedPayment = this.value;
                        document.getElementById('walletName').innerText =
                            this.value === 'GCASH' ? 'GCash' : 'PayMaya';
                        modal.show();
                    } else {
                        selectedPayment = 'COD';
                    }
                });
            });

            // Cancel → revert to COD
            document.getElementById('cancelWallet').addEventListener('click', () => {
                modal.hide();
                document.querySelector('input[value="cod"]').checked = true;
                selectedPayment = 'COD';
            });

            // Confirm → keep wallet
            document.getElementById('confirmWallet').addEventListener('click', () => {
                modal.hide();
            });
            </script>

            <!-- Right: Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 90px">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">Order Summary</h5>

                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?= count($items) ?> item(s)</span>
                                <strong id="subtotal">₱<?= number_format($subtotal, 2) ?></strong>
                            </li>


                            <li class="list-group-item d-flex justify-content-between">
                                <span>Shipping Fee</span>
                                <strong id="shippingFee">₱38.00</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between d-none" id="discountRow">
                                <span>Voucher Discount</span>
                                <strong class="text-success" id="discountAmount">-₱0.00</strong>
                            </li>
                            <script>
                            const subtotalAmount = <?= number_format($subtotal, 2, '.', '') ?>;
                            </script>
                            <li class="list-group-item d-flex justify-content-between fs-6">
                                <strong>Total</strong>
                                <strong class="text-danger" id="grandTotal">₱88.00</strong>
                            </li>
                        </ul>

                        <!-- Voucher -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body px-4 py-3 d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Voucher</span>

                                <a href="#" class="text-primary fw-semibold text-decoration-none" data-bs-toggle="modal"
                                    data-bs-target="#voucherModal">
                                    Select Voucher
                                </a>
                            </div>
                        </div>
                        <div id="appliedVoucher" class="text-success small d-none mt-1">
                            Voucher applied
                        </div>
                        <script>
                        const appliedVoucher = document.getElementById('appliedVoucher');

                        if (discount > 0) {
                            appliedVoucher.classList.remove('d-none');
                        } else {
                            appliedVoucher.classList.add('d-none');
                        }
                        </script>
                        <script>
                        document.querySelector('.btn-brand').addEventListener('click', () => {
                            document.getElementById('delivery_type').value =
                                document.querySelector('input[name="delivery"]:checked').value;

                            document.getElementById('payment_method').value =
                                document.querySelector('input[name="payment"]:checked').value;

                            document.getElementById('shipping_fee').value =
                                document.getElementById('shippingFee').innerText.replace(/[₱,]/g, '');

                            document.getElementById('discount').value =
                                document.getElementById('discountAmount').innerText.replace(/[₱,\- ]/g, '');

                            document.getElementById('total_amount').value =
                                document.getElementById('grandTotal').innerText.replace(/[₱,]/g, '');

                            document.getElementById('checkoutForm').submit();
                        });
                        </script>

                        <form method="POST" action="place_order.php" id="checkoutForm">
                            <input type="hidden" name="delivery_type" id="delivery_type">
                            <input type="hidden" name="payment_method" id="payment_method">
                            <input type="hidden" name="voucher_type" id="voucher_type">
                            <input type="hidden" name="voucher_value" id="voucher_value">
                            <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
                            <input type="hidden" name="shipping_fee" id="shipping_fee">
                            <input type="hidden" name="discount" id="discount">
                            <input type="hidden" name="total_amount" id="total_amount">


                            <button class="btn btn-brand w-100" id="placeOrderBtn">
                                <i class="bi bi-lock"></i> Place Order
                            </button>

                            <small class="d-block text-center text-muted mt-3">
                                Secure checkout · SSL encrypted
                            </small>
                        </form>
                        <script>
                        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
                            e.preventDefault(); // STOP default submit

                            const delivery = document.querySelector('input[name="delivery"]:checked');
                            const payment = document.querySelector('input[name="payment"]:checked');

                            if (!delivery || !payment) {
                                alert('Please select delivery and payment method.');
                                return;
                            }

                            // ✅ RAW VALUES (NOT TEXT)
                            const shipping = shippingRates[delivery.value];
                            const total = calculateTotal(); // we’ll add this below

                            document.getElementById('delivery_type').value = delivery.value;
                            document.getElementById('payment_method').value = payment.value;
                            document.getElementById('shipping_fee').value = shipping;
                            document.getElementById('discount').value = currentDiscount;
                            document.getElementById('total_amount').value = total;

                            this.submit(); // ✅ NOW submit
                        });
                        </script>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<script>
let currentDiscount = 0;
let currentShipping = 38;
let currentTotal = 0;

const shippingRates = {
    STANDARD: 38,
    EXPRESS: 99,
    SAME_DAY: 149
};

let selectedVoucher = null;

function formatPeso(amount) {
    return '₱' + amount.toFixed(2);
}

function calculateTotal() {
    return Math.max(subtotalAmount + currentShipping - currentDiscount, 0);
}

function updateSummary() {
    const delivery = document.querySelector('input[name="delivery"]:checked').value;

    currentShipping = shippingRates[delivery];
    currentDiscount = 0;

    if (selectedVoucher) {
        const type = selectedVoucher.dataset.type;
        const value = parseFloat(selectedVoucher.dataset.value);
        const minSpend = parseFloat(selectedVoucher.dataset.min || 0);

        if (subtotalAmount >= minSpend) {
            if (type === 'fixed') {
                currentDiscount = value;
            }

            if (type === 'shipping' && delivery !== 'SAME_DAY') {
                currentDiscount = Math.min(value, currentShipping);
            }
        }
    }

    currentTotal = calculateTotal();

    // UI update only
    document.getElementById('shippingFee').innerText = formatPeso(currentShipping);
    document.getElementById('grandTotal').innerText = formatPeso(currentTotal);

    if (currentDiscount > 0) {
        document.getElementById('discountRow').classList.remove('d-none');
        document.getElementById('discountAmount').innerText =
            '- ' + formatPeso(currentDiscount);
        document.getElementById('appliedVoucher').classList.remove('d-none');
    } else {
        document.getElementById('discountRow').classList.add('d-none');
        document.getElementById('appliedVoucher').classList.add('d-none');
    }
}


// Delivery change
document.querySelectorAll('input[name="delivery"]').forEach(radio => {
    radio.addEventListener('change', updateSummary);
});

// Voucher select
document.querySelectorAll('input[name="voucher"]').forEach(voucher => {
    voucher.addEventListener('change', function() {
        selectedVoucher = this;
    });
});

// Apply voucher button
document.querySelector('#voucherModal .btn-danger').addEventListener('click', updateSummary);

updateSummary();
</script>

<!-- Voucher Modal -->
<div class="modal fade" id="voucherModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-0">
            <!-- Header -->
            <div class="modal-header border-0">
                <h6 class="modal-title fw-semibold">Select Voucher</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body px-3">
                <label class="voucher-item mb-2">
                    <input type="radio" name="voucher" data-type="fixed" data-value="100" data-min="999">

                    <div class="voucher-content">
                        <div class="fw-semibold text-danger">₱100 OFF</div>
                        <div class="text-muted small">
                            Min. spend ₱999 · Valid today
                        </div>
                    </div>
                </label>

                <label class="voucher-item mb-2">
                    <input type="radio" name="voucher" data-type="shipping" data-value="50">

                    <div class="voucher-content">
                        <div class="fw-semibold text-danger">Free Shipping</div>
                        <div class="text-muted small">
                            Capped at ₱50 · No min. spend
                        </div>
                    </div>
                </label>

                <label class="voucher-item">
                    <input type="radio" name="voucher" data-type="fixed" data-value="50" data-min="500">

                    <div class="voucher-content">
                        <div class="fw-semibold text-danger">₱50 OFF</div>
                        <div class="text-muted small">
                            Min. spend ₱500
                        </div>
                    </div>
                </label>

            </div>

            <!-- Footer -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">
                    Apply Voucher
                </button>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>