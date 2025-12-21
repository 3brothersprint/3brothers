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
                    <li class="nav-item me-2">
                        <a class="nav-link position-relative" href="cart.php" aria-label="Cart">
                            <i class="bi bi-cart3 fs-5"></i>
                            <span class="position-absolute top-0 badge rounded-pill bg-danger">
                                0
                            </span>
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
                                <a class="dropdown-item d-flex align-items-center gap-2" href="/user/profile.php">
                                    <i class="bi bi-person"></i> Profile
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="/user/orders.php">
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