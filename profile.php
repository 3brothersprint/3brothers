<?php
session_start();
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/auth.php");
    exit;
}
?>

<!-- Profile Content -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- LEFT MENU -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="list-group list-group-flush profile-menu">
                        <a class="list-group-item active" style="background: var(--brand-gradient); color: white;"
                            href="profile.php">
                            <i class="bi bi-person"></i> Profile
                        </a>
                        <a class="list-group-item" href="orders.php">
                            <i class="bi bi-receipt"></i> My Orders
                        </a>
                        <a class="list-group-item" href="addresses.php">
                            <i class="bi bi-geo-alt"></i> Addresses
                        </a>
                        <a class="list-group-item text-danger" href="auth/logout.php">
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
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold mb-0">Personal Information</h5><button
                                class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#editProfileModal">
                                Edit
                            </button>
                        </div>
                        <?php 
                          include 'database/db.php';
                          $user_id = $_SESSION['user_id'];
                          $query = "SELECT * FROM users WHERE id = '$user_id'";
                          $query_run = mysqli_query($conn, $query);

                          if(mysqli_num_rows($query_run) > 0){
                          while ($row = mysqli_fetch_assoc($query_run)) {
                            ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" value="<?= $row['full_name'] ?>" disabled />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?= $row['email'] ?>" disabled />
                            </div>
                        </div>
                        <?php
                          }
                          }
                        ?>

                    </div>
                </div>
                <!-- Edit Profile Modal -->
                <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0">
                            <div class="modal-header" style="background: var(--brand-gradient); color: white;">
                                <h5 class="modal-title fw-semibold">Edit Personal Information</h5>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
                            </div>

                            <form action="profile/update-profile.php" method="POST">
                                <div class="modal-body">
                                    <?php
                                        $user_id = $_SESSION['user_id'];
                                        $query = "SELECT * FROM users WHERE id = '$user_id'";
                                        $query_run = mysqli_query($conn, $query);
                                        $row = mysqli_fetch_assoc($query_run);
                                      ?>

                                    <div class="mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="full_name" class="form-control"
                                            value="<?= $row['full_name'] ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control"
                                            value="<?= $row['email'] ?>" required>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                        Cancel
                                    </button>
                                    <button type="submit" style="background: var(--brand-gradient); color: white;"
                                        name="update_profile" class="btn">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
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

                        <a href="orders.html" class="btn btn-link p-0">View all orders</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php include 'includes/footer.php'; ?>