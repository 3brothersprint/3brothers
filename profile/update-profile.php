<?php
session_start();
include '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['update_profile'])) {

    $user_id   = $_SESSION['user_id'];
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);

    $query = "UPDATE users SET 
                full_name = '$full_name',
                email = '$email'
              WHERE id = '$user_id'";

    mysqli_query($conn, $query);
if (mysqli_query($conn, $query)) {
    header("Location: ../profile.php?updated=1");
} else {
    header("Location: ../profile.php?error=1");
}
exit;

}