<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login | 3 Brothers Print Services</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
    :root {
        --brand-primary: #fa9d75;
        /* rgba(250,157,117) */
        --brand-primary-end: #f59f53;
        /* rgba(245,159,83) */
        --brand-gradient: linear-gradient(90deg,
                var(--brand-primary-start) 0%,
                var(--brand-primary-end) 100%);

        --brand-dark: #5a2f1d;

        --brand-accent: #ffb703;
        /* Buttons, badges, CTA */
        --brand-accent-soft: #fff2e8;
        /* Background highlights */
        --brand-bg: #fffaf6;
        /* Page background */
        --brand-surface: #ffffff;
        /* Cards */
        --brand-border: #f1d5c5;
        /* Dividers */
        --status-success: #2d6a4f;
        /* Muted green */
        --status-warning: #f4a261;
        /* Warm amber */
        --status-danger: #b23a2f;
        /* Brick red */
        --status-info: #457b9d;

    }

    body {
        background: var(--brand-primary);
        font-family: 'Inter', system-ui, sans-serif;
    }

    .admin-card {
        max-width: 420px;
    }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="card admin-card shadow-lg border-0">
        <div class="card-header text-center text-white py-4" style="background:var(--brand-primary-end)">
            <h5 class="fw-bold mb-1">
                <i class="bi bi-shield-lock-fill"></i> Admin Panel
            </h5>
            <small>Authorized access only</small>
        </div>

        <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger rounded-0 text-center fw-semibold mb-0">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']);
        endif; ?>

        <div class="card-body p-4">
            <form action="login_process.php" method="POST">

                <div class="mb-3">
                    <label class="form-label">Admin Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" name="admin_login" class="btn w-100"
                    style="background: var(--brand-primary); color: white;">
                    <i class="bi bi-box-arrow-in-right"></i> Login as Admin
                </button>

            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>