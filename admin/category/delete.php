<?php
session_start();
include '../database/db.php';

if(isset($_GET['id'])){
$id = $_GET['id'];
$sql = "DELETE FROM category WHERE id = $id";
$sql_run = mysqli_query($conn, $sql);

if($sql_run){
    $_SESSION['message'] = "Category Deleted Successfully";
    $_SESSION['msg_type'] = "success";
    header("Location: ../products.php");
}
  
}