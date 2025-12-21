<?php include 'includes/header.php'; ?>
    <!-- Profile Content -->
    <section class="py-5">
      <div class="container">
        <div class="row g-4">
          <!-- LEFT MENU -->
          <div class="col-lg-3">
            <div class="card border-0 shadow-sm">
              <div class="list-group list-group-flush profile-menu">
                <a class="list-group-item active" href="#">
                  <i class="bi bi-person"></i> Profile
                </a>
                <a class="list-group-item" href="orders.html">
                  <i class="bi bi-receipt"></i> My Orders
                </a>
                <a class="list-group-item" href="addresses.html">
                  <i class="bi bi-geo-alt"></i> Addresses
                </a>
                <a class="list-group-item text-danger" href="#">
                  <i class="bi bi-box-arrow-right"></i> Logout
                </a>
              </div>
            </div>
          </div>

          <!-- MAIN CONTENT -->
          <div class="col-lg-9">
            <!-- Profile Info -->
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-body">
                <div
                  class="d-flex justify-content-between align-items-center mb-3"
                >
                  <h5 class="fw-semibold mb-0">Personal Information</h5>
                  <button class="btn btn-sm btn-outline-primary">Edit</button>
                </div>

                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input
                      type="text"
                      class="form-control"
                      value="Juan Dela Cruz"
                      disabled
                    />
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input
                      type="email"
                      class="form-control"
                      value="juan@email.com"
                      disabled
                    />
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input
                      type="tel"
                      class="form-control"
                      value="+63 900 000 0000"
                      disabled
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Orders -->
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <h5 class="fw-semibold mb-3">Recent Orders</h5>

                <div class="table-responsive">
                  <table class="table align-middle">
                    <thead>
                      <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>#1024</td>
                        <td>Mar 18, 2025</td>
                        <td><span class="badge bg-success">Completed</span></td>
                        <td>₱250.00</td>
                      </tr>
                      <tr>
                        <td>#1023</td>
                        <td>Mar 15, 2025</td>
                        <td>
                          <span class="badge bg-warning">Processing</span>
                        </td>
                        <td>₱150.00</td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <a href="orders.html" class="btn btn-link p-0"
                  >View all orders</a
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
<?php include 'includes/footer.php'; ?>