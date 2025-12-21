<?php
session_start();
include 'db.php';

function getMessage($message, $url){
    $_SESSION['message'] = $message;
    header("Location: $url");
}

// Register Account
if(isset($_POST['submit'])){
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $account_no = "3BP" . rand(10000, 99999);

    if($password == $confirm_password){
        $query = "INSERT INTO users (account_no, full_name, email, password) 
        VALUES ('$account_no', '$full_name', '$email', '$password')";
        $run_query = mysqli_query($conn, $query);

        if($run_query){
            getMessage("Account has been successfully created", "../");
        }else{
            getMessage("There is an error creating your account", "../auth/auth.php");
        }
    }
}