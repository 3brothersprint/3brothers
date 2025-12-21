<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>