<?php include 'includes/header.php'; ?>
    <!-- Cart Content -->
    <section class="py-5">
      <div class="container">
        <div class="row g-4">
          <!-- Cart Items -->
          <div class="col-lg-8">
            <!-- Cart Item -->
            <div class="card border-0 shadow-sm mb-3">
              <div class="card-body">
                <div class="row align-items-center gy-3">
                  <!-- Product Image -->
                  <div class="col-4 col-md-2">
                    <img
                      src="assets/download (1).jpg"
                      class="img-fluid rounded"
                      alt="Product"
                    />
                  </div>

                  <!-- Product Info -->
                  <div class="col-8 col-md-4">
                    <h6 class="mb-1 fw-semibold">A4 Document Printing</h6>
                    <small class="text-muted"> Paper: A4 · Color: B&W </small>
                  </div>

                  <!-- Quantity -->
                  <div class="col-6 col-md-3">
                    <div class="input-group input-group-sm">
                      <button class="btn btn-outline-secondary">−</button>
                      <input
                        type="number"
                        class="form-control text-center"
                        value="1"
                        min="1"
                      />
                      <button class="btn btn-outline-secondary">+</button>
                    </div>
                  </div>

                  <!-- Price -->
                  <div class="col-4 col-md-2 text-md-end">
                    <strong>₱50.00</strong>
                  </div>

                  <!-- Remove -->
                  <div class="col-2 col-md-1 text-end">
                    <button class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State (optional) -->
            <!--
        <div class="text-center py-5">
          <i class="bi bi-cart-x fs-1 text-muted"></i>
          <p class="mt-3">Your cart is empty</p>
        </div>
        -->
          </div>

          <!-- Order Summary -->
          <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 90px">
              <div class="card-body">
                <h5 class="fw-semibold mb-3">Order Summary</h5>

                <ul class="list-group list-group-flush mb-3">
                  <li class="list-group-item d-flex justify-content-between">
                    <span>Subtotal</span>
                    <strong>₱50.00</strong>
                  </li>
                  <li class="list-group-item d-flex justify-content-between">
                    <span>Discount</span>
                    <span class="text-success">− ₱0.00</span>
                  </li>
                  <li class="list-group-item d-flex justify-content-between">
                    <span>Estimated Total</span>
                    <strong>₱50.00</strong>
                  </li>
                </ul>

                <button class="btn btn-brand w-100 mb-2">
                  <i class="bi bi-credit-card"></i> Buy Now
                </button>

                <a href="products.html" class="btn btn-outline-secondary w-100">
                  Continue Shopping
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
<?php include 'includes/footer.php'; ?>
