<?php 
session_start();
include 'includes/header.php'; ?>
<?php
$cart = $_SESSION['cart'] ?? [];
?>

<!-- Cart Content -->
<!-- Cart Content -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">

            <!-- Cart Items -->
            <div class="col-lg-8">

                <?php if (empty($cart)): ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <i class="bi bi-cart-x fs-1 text-muted"></i>
                    <p class="mt-3">Your cart is empty</p>
                </div>
                <?php else: ?>

                <?php $subtotal = 0; foreach ($cart as $index => $item): 
              $itemTotal = $item['price'] * $item['qty'];
              $subtotal += $itemTotal;
          ?>
                <!-- Cart Item -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row align-items-center gy-3">

                            <!-- Product Image -->
                            <div class="col-4 col-md-2">
                                <img src="assets/download (1).jpg" class="img-fluid rounded" alt="Product" />
                            </div>

                            <!-- Product Info -->
                            <div class="col-8 col-md-4">
                                <h6 class="mb-1 fw-semibold">
                                    <?= htmlspecialchars($item['name']) ?>
                                </h6>
                                <small class="text-muted">
                                    <?= htmlspecialchars($item['type']) ?>
                                </small>
                            </div>

                            <!-- Quantity -->
                            <div class="col-6 col-md-3">
                                <form method="POST" action="update-cart.php" class="input-group input-group-sm">
                                    <input type="hidden" name="index" value="<?= $index ?>">
                                    <button class="btn btn-outline-secondary" name="decrease">−</button>
                                    <input type="number" class="form-control text-center" name="qty"
                                        value="<?= $item['qty'] ?>" min="1" />
                                    <button class="btn btn-outline-secondary" name="increase">+</button>
                                </form>
                            </div>

                            <!-- Price -->
                            <div class="col-4 col-md-2 text-md-end">
                                <strong>₱<?= number_format($itemTotal, 2) ?></strong>
                            </div>

                            <!-- Remove -->
                            <div class="col-2 col-md-1 text-end">
                                <form method="POST" action="remove-cart.php">
                                    <input type="hidden" name="index" value="<?= $index ?>">
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php endif; ?>

            </div>
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 90px">
                    <div class="card-body">
                        <h5 class="fw-semibold mb-3">Order Summary</h5>

                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Subtotal</span>
                                <strong>₱<?= number_format($subtotal ?? 0, 2) ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Discount</span>
                                <span class="text-success">− ₱0.00</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Estimated Total</span>
                                <strong>₱<?= number_format($subtotal ?? 0, 2) ?></strong>
                            </li>
                        </ul>

                        <?php if (!empty($cart)): ?>
                        <a href="checkout.php" class="btn btn-brand w-100 mb-2">
                            <i class="bi bi-credit-card"></i> Checkout
                        </a>
                        <?php endif; ?>

                        <a href="./" class="btn btn-outline-secondary w-100">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>