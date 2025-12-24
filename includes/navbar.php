    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="./">
                <img src="assets/Logo.png" alt="3 Brothers Logo" />
                <span>3 Brothers Print Services</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <!-- Center Search -->
                <form class="d-flex mx-lg-auto my-3 my-lg-0" role="search" style="max-width: 420px; width: 100%">
                    <input class="form-control rounded-start-pill" type="search"
                        placeholder="Search products or services..." aria-label="Search" />
                    <button class="btn btn-light rounded-end-pill px-3" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>

                <!-- Right Icons -->
                <ul class="navbar-nav ms-lg-auto align-items-lg-center">
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link position-relative" href="#" id="notifDropdown" role="button"
                            data-bs-toggle="dropdown">

                            <i class="bi bi-bell fs-5"></i>
                            <span id="notifBadge"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                                0
                            </span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-0" style="width: 320px;">
                            <li class="px-3 py-2 fw-semibold border-bottom">Notifications</li>
                            <div id="notifList" style="max-height:300px; overflow-y:auto;">
                                <li class="px-3 py-3 text-muted small text-center">
                                    No notifications
                                </li>
                            </div>
                        </ul>
                    </li>


                    <li class="nav-item me-2">
                        <a class="nav-link position-relative" href="cart.php" aria-label="Cart">
                            <i class="bi bi-cart3 fs-5"></i>
                            <?php
                                $cartCount = 0;

                                if (isset($_SESSION['user_id'])) {
                                    $uid = $_SESSION['user_id'];

                                    $stmt = $conn->prepare("
                                        SELECT COUNT(*) AS total_qty FROM cart WHERE user_id = ?
                                    ");
                                    $stmt->bind_param("i", $uid);
                                    $stmt->execute();

                                    $cartCount = $stmt->get_result()->fetch_assoc()['total_qty'] ?? 0;
                                }
                            ?>
                            <?php if ($cartCount > 0): ?>
                            <span class="position-absolute top-0 badge rounded-pill bg-danger">
                                <?= $cartCount ?>
                            </span>
                            <?php endif; ?>

                        </a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" id="userDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">

                            <i class="bi bi-person-circle fs-5"></i>
                            <span class="d-none d-md-inline">
                                <?= htmlspecialchars($_SESSION['name']) ?>
                            </span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="userDropdown">

                            <li class="px-3 py-2 text-muted small">
                                Signed in as<br>
                                <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="profile.php">
                                    <i class="bi bi-person"></i> Profile
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="orders.php">
                                    <i class="bi bi-bag"></i> My Orders
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a class="dropdown-item text-danger d-flex align-items-center gap-2"
                                    href="auth/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/auth.php">
                            <i class="bi bi-person-circle fs-5"></i>
                        </a>
                    </li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </nav>