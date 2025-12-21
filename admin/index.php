<?php include 'includes/header.php'; ?>
    <!-- Main -->
    <main class="col-md-9 col-lg-10 ms-sm-auto px-4 py-4">

      <!-- Topbar -->
      <div class="topbar d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Dashboard</h3>
      </div>

      <!-- Stats -->
      <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
          <div class="card stat-card">
            <div class="card-body">
              <h6>Total Orders</h6>
              <h3>1,245</h3>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card stat-card">
            <div class="card-body">
              <h6>Revenue</h6>
              <h3>₱245,800</h3>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card stat-card">
            <div class="card-body">
              <h6>Customers</h6>
              <h3>534</h3>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card stat-card">
            <div class="card-body">
              <h6>Pending</h6>
              <h3>18</h3>
            </div>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="card table-card">
        <div class="card-body">
          <h5 class="mb-3">Recent Orders</h5>

          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Status</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#10231</td>
                  <td>Juan Dela Cruz</td>
                  <td><span class="badge bg-success">Completed</span></td>
                  <td>₱1,200</td>
                </tr>
                <tr>
                  <td>#10232</td>
                  <td>Maria Santos</td>
                  <td><span class="badge bg-warning text-dark">Pending</span></td>
                  <td>₱850</td>
                </tr>
                <tr>
                  <td>#10233</td>
                  <td>Pedro Reyes</td>
                  <td><span class="badge bg-danger">Cancelled</span></td>
                  <td>₱540</td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>

    </main>
<?php include 'includes/footer.php'; ?>