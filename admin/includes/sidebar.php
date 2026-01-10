<?php
$pageName = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], "/") +1);
?>
<aside class="col-md-3 col-lg-2 sidebar p-4 d-flex flex-column position-sticky top-0 vh-100">

    <h4 class="logo mb-4 text-center">3 BROTHERS PRINT</h4>

    <!-- Navigation -->
    <ul class="nav flex-column gap-2 flex-grow-1 overflow-auto">
        <li class="nav-item">
            <a class="nav-link <?= $pageName == 'index.php' ? 'active':'' ?>" href="./">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a href="shift_manager.php" class="nav-link <?= $pageName == 'shift_manager.php' ? 'active':'' ?>">
                <i class="bi bi-calendar2-week"></i> Shift Manager
            </a>

        </li>

        <li class="nav-item">
            <a class="nav-link <?= $pageName == 'product-orders.php' ? 'active':'' ?>" href="product-orders.php">
                <i class="bi bi-bag-check me-2"></i> Product Orders
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= $pageName == 'print-orders.php' ? 'active':'' ?>" href="print-orders.php">
                <i class="bi bi-printer me-2"></i> Print Orders
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= $pageName == 'products.php' ? 'active':'' ?>" href="products.php">
                <i class="bi bi-box-seam me-2"></i> Products
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= $pageName == 'customers.php' ? 'active':'' ?>" href="customers.php">
                <i class="bi bi-people me-2"></i> Customers
            </a>
        </li>

        <!-- 🔁 REPLACED -->
        <li class="nav-item">
            <a class="nav-link <?= $pageName == 'inventory.php' ? 'active':'' ?>" href="inventory.php">
                <i class="bi bi-boxes me-2"></i> Inventory Management
            </a>
        </li>

        <!-- 🔁 REPLACED -->
        <li class="nav-item">
            <a class="nav-link <?= $pageName == 'logistics.php' ? 'active':'' ?>" href="logistics.php">
                <i class="bi bi-truck me-2"></i> Logistics Management
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= $pageName == 'settings.php' ? 'active':'' ?>" href="settings.php">
                <i class="bi bi-gear me-2"></i> Settings
            </a>
        </li>
    </ul>

    <!-- Profile Dropdown -->
    <div class="profile-dropdown dropdown mt-auto">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
            data-bs-toggle="dropdown">

            <img src="css/Admin.png" class="rounded-circle me-2" alt="Admin" style="width: 40px; height: 40px;" />
            <?php 
            $lateToday = false;

            if (isset($attendance) && is_array($attendance)) {
                $lateToday = ($attendance['status'] === 'Late');
            }
            ?>
            <div class="text-start">
                <div class="fw-semibold d-flex align-items-center gap-1">
                    <?= htmlspecialchars($_SESSION['admin_name']) ?>

                    <?php if ($lateToday): ?>
                    <span class="badge bg-danger">Late</span>
                    <?php endif; ?>
                </div>

                <small class="text-white-50">Administrator</small>
            </div>
        </a>

        <ul class="dropdown-menu dropdown-menu-dark shadow mt-2">
            <li>
                <a class="dropdown-item" href="profile.php">
                    <i class="bi bi-person me-2"></i> Profile
                </a>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                <a class="dropdown-item text-danger" href="auth/logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>


</aside>