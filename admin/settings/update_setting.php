<?php
require '../database/db.php';

if ($_POST['type'] === 'announcement') {

    $id = (int) $_POST['id'];

    $message       = $_POST['message'];
    $show_home     = isset($_POST['show_home']) ? 1 : 0;
    $show_product  = isset($_POST['show_product']) ? 1 : 0;
    $show_checkout = isset($_POST['show_checkout']) ? 1 : 0;
    $show_all      = isset($_POST['show_all']) ? 1 : 0;
    $is_enabled    = isset($_POST['is_enabled']) ? 1 : 0;

    $stmt = $conn->prepare("
        UPDATE announcements SET
            message = ?,
            show_home = ?,
            show_product = ?,
            show_checkout = ?,
            show_all = ?,
            is_enabled = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "siiiiii",
        $message,
        $show_home,
        $show_product,
        $show_checkout,
        $show_all,
        $is_enabled,
        $id
    );

    $stmt->execute();
}

header("Location: ../settings.php");