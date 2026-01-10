<?php
session_start();
require "../database/db.php";
date_default_timezone_set('Asia/Manila');

$error = null;

if (!isset($_SESSION['verify_email'])) {
    header("Location: auth.php");
    exit();
}

$email = $_SESSION['verify_email'];

/* FETCH USER */
$q = mysqli_query($conn, "
    SELECT 
        verify_code,
        otp_attempts,
        otp_blocked_until,
        TIMESTAMPDIFF(SECOND, NOW(), otp_expires) AS remaining_seconds
    FROM users 
    WHERE email='$email'
");

$user = mysqli_fetch_assoc($q);

if (!$user) {
    header("Location: auth.php");
    exit();
}

/* BLOCK CHECK */
if ($user['otp_blocked_until'] && strtotime($user['otp_blocked_until']) > time()) {
    $error = "Too many attempts. Try again later.";
}

/* TIMER */
$remaining = 0;

if ($user['remaining_seconds'] !== null) {
    if ($user['remaining_seconds'] > 0) {
        $remaining = (int) $user['remaining_seconds'];
    } else {
        $error = "OTP expired. Please request a new code.";
    }
}



function maskEmail($email) {
    [$name, $domain] = explode("@", $email);
    return substr($name, 0, 4) . str_repeat("*", max(0, strlen($name) - 4)) . "@" . $domain;
}
$maskedEmail = maskEmail($email);

/* VERIFY OTP */
if (isset($_POST['verify']) && empty($error)) {

    $code = trim($_POST['code']);

    if (
    !empty($user['verify_code']) &&
    hash_equals($user['verify_code'], $code) &&
    !empty($user['otp_expires']) &&
    strtotime($user['otp_expires']) > time()
)
 {
    mysqli_query($conn, "
        UPDATE users SET
            is_verified = 1,
            verify_code = NULL,
            otp_expires = NULL,
            otp_attempts = 0,
            otp_blocked_until = NULL
        WHERE email='$email'
    ");

    unset($_SESSION['verify_email']);
    $_SESSION['success'] = "Account verified. Please login.";
    header("Location: auth.php");
    exit();
}
 else {

        $attempts = (int)$user['otp_attempts'] + 1;

        if ($attempts >= 5) {
            mysqli_query($conn, "
                UPDATE users SET
                    otp_attempts = $attempts,
                    otp_blocked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
                WHERE email='$email'
            ");
            $error = "Too many failed attempts. Locked for 15 minutes.";
        } else {
            mysqli_query($conn, "
                UPDATE users SET otp_attempts = $attempts
                WHERE email='$email'
            ");
            $error = "Invalid OTP ($attempts / 5)";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OTP Verification</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/style.css" />
</head>

<body class="bg-light" style="background: var(--brand-gradient);">

    <section class="min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card shadow-lg border-0 p-4" style="max-width: 420px">
            <h4 class="fw-bold mb-2">OTP Verification</h4>
            <p class="text-muted mb-2">
                Enter the 6-digit code sent to your email
            </p>
            <p class="text-muted mb-2">
                Code sent to <strong><?= $maskedEmail ?></strong>
            </p>

            <div class="d-flex justify-content-center mb-3">
                <div class="otp-timer">
                    <i class="bi bi-clock"></i>
                    <span id="timer">--:--</span>
                </div>
            </div>


            <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" id="otpForm">

                <input type="hidden" name="code" id="otpCode">

                <div class="d-flex gap-2 justify-content-center mb-3">
                    <?php for($i=0;$i<6;$i++): ?>
                    <input type="text" pattern="[0-9]*" maxlength="1" class="form-control text-center otp" required
                        inputmode="numeric">
                    <?php endfor; ?>
                </div>

                <button type="submit" name="verify" id="verifyBtn" class="btn btn-brand w-100" disabled>
                    Verify & Login
                </button>


                <a href="#" id="resend" class=""
                    style="text-decoration: none; pointer-events:none; text-align: center;">
                    Resend Code
                </a>
                <style>
                @keyframes shake {
                    0% {
                        transform: translateX(0);
                    }

                    25% {
                        transform: translateX(-6px);
                    }

                    50% {
                        transform: translateX(6px);
                    }

                    75% {
                        transform: translateX(-6px);
                    }

                    100% {
                        transform: translateX(0);
                    }
                }

                .shake {
                    animation: shake 0.4s;
                }

                .otp-timer {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    padding: 10px 18px;
                    border-radius: 30px;
                    background: #f1f3f5;
                    font-weight: 600;
                    font-size: 14px;
                    color: #495057;
                }

                .otp-timer i {
                    color: #0d6efd;
                    font-size: 16px;
                }

                .otp-timer.expired {
                    background: #fff5f5;
                    color: #dc3545;
                }
                </style>
            </form>
        </div>
    </section>

    <script>
    const inputs = document.querySelectorAll('.otp');
    const hidden = document.getElementById("otpCode");
    const button = document.getElementById("verifyBtn");
    const resend = document.getElementById("resend");
    const card = document.querySelector(".card");

    function buildOTP() {
        let code = '';
        let filled = true;

        inputs.forEach(i => {
            if (!i.value) filled = false;
            code += i.value;
        });

        hidden.value = code;
        button.disabled = !filled;
    }
    /* OTP INPUT */
    inputs.forEach((input, i) => {

        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '');

            if (input.value && i < inputs.length - 1) {
                inputs[i + 1].focus();
            }
            buildOTP();
        });

        input.addEventListener('keydown', e => {
            if (e.key === "Backspace" && !input.value && i > 0) {
                inputs[i - 1].focus();
            }
        });

    });



    /* TIMER */
    let timeLeft = <?= (int)$remaining ?>;
    const timerEl = document.getElementById("timer");

    function tick() {
        if (timeLeft <= 0) {
            timerEl.textContent = "Expired";
            timerEl.parentElement.classList.add("expired");
            resend.style.pointerEvents = "auto";
            clearInterval(timerInterval);
            button.disabled = true;
            return;
        }

        const m = Math.floor(timeLeft / 60);
        const s = timeLeft % 60;
        timerEl.textContent = `${m}:${s.toString().padStart(2,'0')}`;
        timeLeft--;
    }

    tick();
    const timerInterval = setInterval(tick, 1000);


    /* RESEND */
    resend.onclick = e => {
        e.preventDefault();
        window.location.href = "resend.php";
    };

    /* SHAKE */
    <?php if (!empty($error)): ?>
    card.classList.add("shake");
    setTimeout(() => card.classList.remove("shake"), 400);
    <?php endif; ?>
    </script>

</body>

</html>