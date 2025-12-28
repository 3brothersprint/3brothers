<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>404 · Page Not Found</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons (optional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
    :root {
        --brand-gradient: linear-gradient(90deg,
                rgba(250, 157, 117, 1) 0%,
                rgba(245, 159, 83, 1) 100%);
        --brand-dark: #5a2f1d;
    }

    body {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--brand-gradient);
    }

    .error-card {
        max-width: 520px;
        border-radius: 20px;
    }

    .error-code {
        font-size: 6rem;
        font-weight: 800;
        line-height: 1;
        background: var(--brand-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .error-icon {
        font-size: 3rem;
        color: var(--brand-dark);
    }
    </style>
</head>

<body>

    <div class="card shadow-lg border-0 error-card text-center p-4">
        <div class="card-body">

            <div class="error-icon mb-3">
                <i class="bi bi-exclamation-triangle"></i>
            </div>

            <div class=" error-code mb-2">404
            </div>

            <h4 class="fw-bold mb-2">Page Not Found</h4>

            <p class="text-muted mb-4">
                The page you’re looking for doesn’t exist or may have been moved.
            </p>

            <div class="d-flex justify-content-center gap-3">
                <a href="/print" class="btn rounded-pill px-4"
                    style=" background: var(--brand-gradient); color: white;">
                    <i class="bi bi-house-door"></i> Go Home
                </a>
                <button onclick="history.back()" class="btn btn-outline-secondary rounded-pill px-4">
                    Go Back
                </button>
            </div>

        </div>
    </div>

</body>

</html>