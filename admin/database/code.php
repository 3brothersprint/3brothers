<?php
session_start();
include 'db.php';

// Add Categories
if(isset($_POST['add_cat'])){
    $cat_name = $_POST['cat_name'];
    $cat_no = "3BP" . rand(100000,999999);

    $query = "INSERT INTO category (cat_no, cat_name) 
    VALUES ('$cat_no', '$cat_name')";
    $run_query = mysqli_query($conn, $query);

    if($run_query){
        $_SESSION['message'] = "Category Inserted Successfully";
        $_SESSION['msg_type'] = "success";
        header("Location: ../products.php");
    }
}