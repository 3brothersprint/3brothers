<?php
session_start();

$items = $_SESSION['buy_now'] ?? $_SESSION['cart'] ?? [];


if (!$item) {
    header("Location: cart.php");
    exit;
}
?>
<?php include 'includes/header.php'; ?>

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

                        <!-- Address Row -->
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="fw-semibold">
                                    Earl Christian Tagalog (+63) 994 082 3693
                                </span>

                                <span class="text-muted">
                                    District 4, Purok Tandang Sora, Pulpogan, Consolacion,
                                    Visayas, Cebu 6001
                                </span>

                                <span class="badge border border-danger text-danger fw-normal">
                                    Default
                                </span>
                            </div>

                            <a href="#" class="text-primary fw-semibold text-decoration-none">
                                Change
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Ordered Product -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body px-4 py-3">
                        <!-- Product Item -->
                        <div class="d-flex align-items-start gap-3">

                            <!-- Product Image -->
                            <img src="assets/download (1).jpg" alt="Product" class="border rounded" width="80"
                                height="80" />

                            <!-- Product Info -->
                            <div class="flex-grow-1">
                                <div class="fw-normal text-dark">
                                    <?= htmlspecialchars($item['name']) ?>
                                </div>

                                <div class="text-muted small">
                                    <?= htmlspecialchars($item['type']) ?>
                                </div>

                                <div class="text-muted small mt-1">
                                    x<?= (int)$item['qty'] ?>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="text-end">
                                <div class="fw-semibold text-danger">
                                    ₱<?= number_format($item['price'] * $item['qty'], 2) ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <!-- Delivery Option -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body px-4 py-3">
                        <div class="fw-semibold mb-3">Delivery Option</div>

                        <!-- Option -->
                        <label class="delivery-option d-flex justify-content-between align-items-start mb-2">
                            <input type="radio" name="delivery" checked />

                            <div>
                                <div class="fw-semibold">Standard Delivery</div>
                                <div class="text-muted small">
                                    Estimated delivery: 3–5 days
                                </div>
                            </div>

                            <div class="fw-semibold text-danger">₱38</div>
                        </label>

                        <!-- Option -->
                        <label class="delivery-option d-flex justify-content-between align-items-start">
                            <input type="radio" name="delivery" />

                            <div>
                                <div class="fw-semibold">Express Delivery</div>
                                <div class="text-muted small">
                                    Estimated delivery: 1–2 days
                                </div>
                            </div>

                            <div class="fw-semibold text-danger">₱99</div>
                        </label>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body px-4 py-3">
                        <div class="fw-semibold mb-3">Payment Method</div>

                        <!-- Option -->
                        <label class="payment-option d-flex justify-content-between align-items-center mb-2">
                            <input type="radio" name="payment" checked />

                            <div class="d-flex align-items-center gap-2">
                                <img src="https://via.placeholder.com/32" alt="COD" />
                                <span class="fw-semibold">Cash on Delivery</span>
                            </div>

                            <span class="text-muted small">Recommended</span>
                        </label>

                        <!-- Option -->
                        <label class="payment-option d-flex justify-content-between align-items-center mb-2">
                            <input type="radio" name="payment" />

                            <div class="d-flex align-items-center gap-2">
                                <img src="https://via.placeholder.com/32" alt="ShopeePay" />
                                <span class="fw-semibold">ShopeePay</span>
                            </div>

                            <span class="text-muted small">₱50 Cashback</span>
                        </label>

                        <!-- Option -->
                        <label class="payment-option d-flex justify-content-between align-items-center">
                            <input type="radio" name="payment" />

                            <div class="d-flex align-items-center gap-2">
                                <img src="https://via.placeholder.com/32" alt="GCash" />
                                <span class="fw-semibold">GCash</span>
                            </div>

                            <span class="text-muted small"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 90px">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">Order Summary</h5>

                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>A4 Document Printing × 1</span>
                                <strong>₱50.00</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Delivery</span>
                                <span>Free</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Total</strong>
                                <strong>₱50.00</strong>
                            </li>
                        </ul>
                        <!-- Voucher -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body px-4 py-3 d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Shopee Voucher</span>

                                <a href="#" class="text-primary fw-semibold text-decoration-none" data-bs-toggle="modal"
                                    data-bs-target="#voucherModal">
                                    Select Voucher
                                </a>
                            </div>
                        </div>
                        <button class="btn btn-brand w-100">
                            <i class="bi bi-lock"></i> Place Order
                        </button>

                        <small class="d-block text-center text-muted mt-3">
                            Secure checkout · SSL encrypted
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
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
                <!-- Voucher Item -->
                <label class="voucher-item mb-2">
                    <input type="radio" name="voucher" checked />

                    <div class="voucher-content">
                        <div class="fw-semibold text-danger">₱100 OFF</div>
                        <div class="text-muted small">
                            Min. spend ₱999 · Valid today
                        </div>
                    </div>
                </label>

                <!-- Voucher Item -->
                <label class="voucher-item mb-2">
                    <input type="radio" name="voucher" />

                    <div class="voucher-content">
                        <div class="fw-semibold text-danger">Free Shipping</div>
                        <div class="text-muted small">
                            Capped at ₱50 · No min. spend
                        </div>
                    </div>
                </label>

                <!-- Voucher Item -->
                <label class="voucher-item">
                    <input type="radio" name="voucher" />

                    <div class="voucher-content">
                        <div class="fw-semibold text-danger">₱50 OFF</div>
                        <div class="text-muted small">Min. spend ₱500</div>
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