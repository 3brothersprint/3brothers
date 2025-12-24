<?php
session_start();
include '../database/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
  INSERT INTO user_addresses (
    user_id, label, full_name, phone, address,
    region_code, region_name,
    province_code, province_name,
    city_code, city_name,
    barangay_code, barangay_name,
    zip_code, is_default
  ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");

$is_default = isset($_POST['is_default']) ? 1 : 0;

$stmt->bind_param(
  "isssssssssssssi",
  $user_id,
  $_POST['label'],
  $_POST['full_name'],
  $_POST['phone'],
  $_POST['address'],
  $_POST['region_code'],
  $_POST['region_name'],
  $_POST['province_code'],
  $_POST['province_name'],
  $_POST['city_code'],
  $_POST['city_name'],
  $_POST['barangay_code'],
  $_POST['barangay_name'],
  $_POST['zip_code'],
  $is_default
);

$stmt->execute();
header("Location: ../addresses.php");