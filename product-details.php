<?php include 'includes/header.php'; ?>
    <!-- Product View -->
    <section class="py-5">
      <div class="container">
        <div class="row g-4">
          <!-- Product Image -->
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
              <img
                src="assets/download (1).jpg"
                class="img-fluid rounded-4"
                alt="Product Image"
              />
            </div>

            <!-- Thumbnail Images -->
            <div class="d-flex gap-2 mt-3">
              <img
                src="assets/download (1).jpg"
                class="img-thumbnail thumb active"
                alt=""
              />
              <img
                src="assets/download (1).jpg"
                class="img-thumbnail thumb"
                alt=""
              />
              <img
                src="assets/download (1).jpg"
                class="img-thumbnail thumb"
                alt=""
              />
            </div>
          </div>

          <!-- Product Details -->
          <div class="col-lg-6">
            <h2 class="fw-bold">A4 Printing Paper (500 Sheets)</h2>
            <p class="text-muted mb-2">Category: Printing Supplies</p>

            <!-- Rating -->
            <div class="mb-3">
              <i class="bi bi-star-fill text-warning"></i>
              <i class="bi bi-star-fill text-warning"></i>
              <i class="bi bi-star-fill text-warning"></i>
              <i class="bi bi-star-fill text-warning"></i>
              <i class="bi bi-star text-warning"></i>
              <span class="ms-2 text-muted">(24 reviews)</span>
            </div>

            <!-- Price -->
            <h3 class="text-brand fw-bold mb-3">₱250.00</h3>

            <!-- Description -->
            <p class="text-muted">
              High-quality A4 printing paper suitable for documents, reports,
              school projects, and office use. Smooth texture and jam-free
              printing.
            </p>

            <!-- Options -->
            <div class="row g-3 mb-4">
              <div class="mb-3">
                <label class="form-label fw-semibold">Paper Type</label>

                <div class="variant-group" data-name="paperType">
                  <button type="button" class="variant-btn active">
                    Standard
                  </button>
                  <button type="button" class="variant-btn">Premium</button>
                  <button type="button" class="variant-btn">Recycled</button>
                </div>

                <input type="hidden" name="paper_type" value="Standard" />
              </div>

              <div class="col-md-6">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control" value="1" min="1" />
              </div>
            </div>

            <!-- Actions -->
            <div class="d-flex flex-wrap gap-3">
              <button class="btn btn-brand px-4">
                <i class="bi bi-cart-plus me-2"></i>Add to Cart
              </button>

              <button class="btn btn-dark px-4">
                <i class="bi bi-bag-check me-2"></i>Buy Now
              </button>

              <button
                class="btn btn-outline-brand px-4 py-2 d-flex align-items-center gap-2"
                data-bs-toggle="modal"
                data-bs-target="#bulkOrderModal"
              >
                <i class="bi bi-box-seam me-2"></i>Bulk Order
              </button>
            </div>
            <div
              class="modal fade"
              id="bulkOrderModal"
              tabindex="-1"
              aria-hidden="true"
            >
              <div
                class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable"
              >
                <div class="modal-content border-0 rounded-4 overflow-hidden">
                  <!-- Header -->
                  <div
                    class="modal-header text-white"
                    style="background: var(--brand-gradient)"
                  >
                    <div>
                      <h5 class="modal-title mb-0">Bulk Order Request</h5>
                      <small class="opacity-75"
                        >Best pricing for large quantities</small
                      >
                    </div>
                    <button
                      type="button"
                      class="btn-close btn-close-white"
                      data-bs-dismiss="modal"
                    ></button>
                  </div>

                  <!-- Body -->
                  <form>
                    <div class="modal-body p-4">
                      <!-- Info Banner -->
                      <div
                        class="alert alert-light border d-flex gap-3 align-items-start"
                      >
                        <i class="bi bi-info-circle-fill text-warning fs-5"></i>
                        <div>
                          <strong>Bulk Discount Available</strong>
                          <div class="small text-muted">
                            Orders above 100+ units qualify for special pricing.
                          </div>
                        </div>
                      </div>

                      <div class="row g-4 mt-2">
                        <!-- Product -->
                        <div class="col-md-6">
                          <label class="form-label fw-semibold">Product</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="bi bi-box"></i>
                            </span>
                            <input
                              type="text"
                              class="form-control"
                              value="A4 Printing Service"
                              readonly
                            />
                          </div>
                        </div>

                        <!-- Quantity -->
                        <div class="col-md-6">
                          <label class="form-label fw-semibold"
                            >Estimated Quantity</label
                          >
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="bi bi-hash"></i>
                            </span>
                            <input
                              type="number"
                              class="form-control"
                              placeholder="e.g. 1000"
                              min="100"
                              required
                            />
                          </div>
                        </div>

                        <!-- Variant -->
                        <div class="col-md-6">
                          <label class="form-label fw-semibold"
                            >Paper Type</label
                          >
                          <select class="form-select">
                            <option>Standard</option>
                            <option>Premium</option>
                            <option>Recycled</option>
                          </select>
                        </div>

                        <!-- Delivery -->
                        <div class="col-md-6">
                          <label class="form-label fw-semibold"
                            >Delivery Method</label
                          >
                          <select class="form-select">
                            <option>Pickup</option>
                            <option>Delivery</option>
                          </select>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                          <label class="form-label fw-semibold"
                            >Additional Notes</label
                          >
                          <textarea
                            class="form-control"
                            rows="3"
                            placeholder="Deadlines, file details, special requests..."
                          ></textarea>
                        </div>
                      </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer bg-light">
                      <button
                        type="submit"
                        class="btn btn-brand px-4 d-flex align-items-center gap-2"
                      >
                        <i class="bi bi-send"></i>
                        Request Quotation
                      </button>

                      <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"
                      >
                        Cancel
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Extra Info -->
            <ul class="list-unstyled mt-4 small text-muted">
              <li>✔ Available for bulk orders</li>
              <li>✔ Same-day printing available</li>
              <li>✔ Pickup & delivery options</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- Product Tabs -->
    <section class="pb-5">
      <div class="container">
        <ul class="nav nav-tabs mb-3">
          <li class="nav-item">
            <button
              class="nav-link active"
              data-bs-toggle="tab"
              data-bs-target="#description"
            >
              Description
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link"
              data-bs-toggle="tab"
              data-bs-target="#specs"
            >
              Specifications
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link"
              data-bs-toggle="tab"
              data-bs-target="#reviews"
            >
              Reviews
            </button>
          </li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane fade show active" id="description">
            <p>
              This A4 paper is ideal for schools, offices, and businesses.
              Compatible with inkjet and laser printers.
            </p>
          </div>

          <div class="tab-pane fade" id="specs">
            <ul>
              <li>Size: A4</li>
              <li>Sheets: 500</li>
              <li>Weight: 80 GSM</li>
              <li>Color: White</li>
            </ul>
          </div>

          <div class="tab-pane fade" id="reviews">
            <p class="text-muted">No reviews yet.</p>
          </div>
        </div>
      </div>
    </section>
<?php include 'includes/footer.php'; ?>