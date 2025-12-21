<?php
session_start();
require "../database/db.php";
require "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;

if (!isset($_SESSION['verify_email'])) exit();

$email = $_SESSION['verify_email'];

$user = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT otp_attempts, otp_last_sent FROM users WHERE email='$email'"
));

if ($user['otp_attempts'] >= 3 && strtotime($user['otp_last_sent']) > strtotime('-10 minutes')) {
    http_response_code(429);
    exit("Too many OTP requests. Try again later.");
}

if ($user['otp_last_sent'] && strtotime($user['otp_last_sent']) > strtotime('-60 seconds')) {
    http_response_code(429);
    exit("Please wait before resending OTP.");
}

$code = rand(100000,999999);
$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

mysqli_query($conn,"
    UPDATE users 
    SET verify_code='$code',
        otp_expires='$expiry',
        otp_attempts = otp_attempts + 1,
        otp_last_sent = NOW()
    WHERE email='$email'
");

/* EMAIL */
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
$mail->Subject = "Your New OTP Code";
$mail->Body = "OTP: $code (5 mins)";
$mail->send();

echo "sent";