<?php
session_start();
require "db.php"; // your DB connection

use PHPMailer\PHPMailer\PHPMailer;
require "vendor/autoload.php";

/* ================= REGISTER ================= */
if (isset($_POST['submit'])) {
    $account_no = "20" . rand(100000, 999999);
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $cpass = $_POST['confirm_password'];

    if ($pass !== $cpass) {
        $_SESSION['error'] = "Passwords do not match";
        header("Location: ../auth/auth.php");
        exit();
    }

    $hash = password_hash($pass, PASSWORD_BCRYPT);
    $code = rand(100000, 999999);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['error'] = "Email already exists";
        header("Location: ../auth/auth.php");
        exit();
    }

    mysqli_query($conn, "INSERT INTO users 
        (account_no, full_name, email, password, verify_code) 
        VALUES ('$account_no', '$name','$email','$hash','$code')");

    // SEND EMAIL
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = '3brothersprintservices@gmail.com';
    $mail->Password = 'txie xjlc lura tvls';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('3brothersprintservices@gmail.com', '3 Brothers Print Services');
    $mail->addAddress($email);
    $mail->Subject = "Your Verification Code | 3 Brothers Print Services";
    $mail->isHTML(true);
$mail->Body = "
<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Email Verification</title>
</head>
<body style='margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;'>

  <table width='100%' cellpadding='0' cellspacing='0' style='padding:24px 0;'>
    <tr>
      <td align='center'>

        <table width='100%' cellpadding='0' cellspacing='0'
          style='max-width:520px; background:#ffffff; border-radius:10px;
                 box-shadow:0 8px 24px rgba(0,0,0,0.08); overflow:hidden;'>

          <!-- HEADER -->
          <tr>
            <td style='background:#0d6efd; padding:24px; text-align:center; color:#ffffff;'>
              <h1 style='margin:0; font-size:22px; font-weight:600;'>
                3 Brothers Print Services
              </h1>
              <p style='margin:6px 0 0; font-size:14px; opacity:0.9;'>
                Email Verification Code
              </p>
            </td>
          </tr>

          <!-- CONTENT -->
          <tr>
            <td style='padding:32px 28px; color:#333333;'>

              <p style='margin:0 0 16px; font-size:15px;'>
                Hello,
              </p>

              <p style='margin:0 0 20px; font-size:14px; line-height:1.6;'>
                We received a request to verify your email address.
                Please use the verification code below to continue.
                This code will expire in <strong>5 minutes</strong>.
              </p>

              <!-- OTP -->
              <div style='text-align:center; margin:32px 0;'>
                <div style='display:inline-block;
                            font-size:30px;
                            font-weight:700;
                            letter-spacing:8px;
                            padding:16px 28px;
                            background:#f1f3f5;
                            color:#0d6efd;
                            border-radius:8px;'>
                  $code
                </div>
              </div>

              <p style='margin:0 0 16px; font-size:13px; color:#6c757d; line-height:1.5;'>
                If you did not request this verification, you can safely ignore this email.
                No further action is required.
              </p>

              <p style='margin:0; font-size:13px; color:#6c757d;'>
                Thank you,<br>
                <strong>3 Brothers Print Services Team</strong>
              </p>

            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td style='background:#f8f9fa; padding:16px; text-align:center;
                       font-size:12px; color:#6c757d;'>
              © ".date('Y')." 3 Brothers Print Services. All rights reserved.
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>

</body>
</html>
";
    $mail->send();

    $_SESSION['verify_email'] = $email;
header("Location: ../auth/otp-login.php"); // or verify.php if OTP already sent
exit();

}

/* ================= PASSWORD LOGIN ONLY ================= */
if (isset($_POST['login'])) {

    $email    = trim($_POST['email']);
    $pass     = $_POST['password'];
    $remember = isset($_POST['remember']); // checkbox

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($query) == 0) {
        $_SESSION['error'] = "Invalid email or password";
        header("Location: ../auth/login.php");
        exit();
    }

    $user = mysqli_fetch_assoc($query);

    if (!password_verify($pass, $user['password'])) {
        $_SESSION['error'] = "Invalid email or password";
        header("Location: ../auth/login.php");
        exit();
    }

    // Force OTP if not verified
    if ($user['is_verified'] == 0) {
        $_SESSION['verify_email'] = $email;
        header("Location: ../auth/verify.php");
        exit();
    }

    // NORMAL LOGIN
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name']    = $user['full_name'];

    /* ===== REMEMBER ME TOKEN ===== */
    if ($remember) {
        $token = bin2hex(random_bytes(32)); // secure
        $hash  = hash('sha256', $token);

        mysqli_query($conn,"
            UPDATE users 
            SET remember_token='$hash'
            WHERE id='{$user['id']}'
        ");

        setcookie(
            "remember_token",
            $token,
            time() + (60 * 60 * 24 * 30), // 30 days
            "/",
            "",
            true,  // secure (HTTPS)
            true   // httponly
        );
    }

    header("Location: ../");
    exit();
}