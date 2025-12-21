<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login / Sign Up | 3 Brothers Print Services</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css" />
</head>

<body class="bg-light">

    <!-- Auth Wrapper -->
    <section class="min-vh-100 d-flex align-items-center justify-content-center">
        <div class="auth-card card border-0 shadow-lg overflow-hidden">

            <!-- Header -->
            <div class="auth-header text-white text-center p-4" style="background: var(--brand-gradient)">
                <h4 class="fw-bold mb-1">3 Brothers Print Services</h4>
                <small>Welcome back! Please login or create an account</small>
            </div>

            <!-- Tabs -->
            <div class="card-body p-4">
                <ul class="nav nav-pills nav-justified mb-4" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#loginTab">
                            Login
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#signupTab">
                            Sign Up
                        </button>
                    </li>
                </ul>

                <div class="tab-content">

                    <!-- LOGIN -->
                    <div class="tab-pane fade show active" id="loginTab">
                        <form action="../database/code.php" method="POST">

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="you@email.com"
                                    required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required />
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember">
                                    <label class="form-check-label">Remember me</label>
                                </div>

                                <a href="forgot.php" class="small">Forgot password?</a>
                            </div>

                            <button type="submit" name="login" class="btn btn-brand w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>

                        </form>
                    </div>


                    <!-- SIGN UP -->
                    <div class="tab-pane fade" id="signupTab">
                        <form action="../database/code.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" required />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required />
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control" required />
                                </div>
                            </div>

                            <div class="form-check my-3">
                                <input class="form-check-input" type="checkbox" required />
                                <label class="form-check-label">
                                    I agree to the Terms & Privacy Policy
                                </label>
                            </div>

                            <button type="submit" name="submit" class="btn btn-brand w-100">
                                <i class="bi bi-person-plus"></i> Create Account
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>