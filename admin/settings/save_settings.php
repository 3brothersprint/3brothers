<?php
require "../database/db.php"; // your DB connection

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Invalid request");
}

$type = $_POST['type'] ?? '';

/* ===========================
   SAVE ANNOUNCEMENT
   =========================== */
if ($type === 'announcement') {

    $message = $_POST['message'];

    $show_home     = isset($_POST['show_home']) ? 1 : 0;
    $show_product  = isset($_POST['show_product']) ? 1 : 0;
    $show_checkout = isset($_POST['show_checkout']) ? 1 : 0;
    $show_all      = isset($_POST['show_all']) ? 1 : 0;
    $is_enabled    = isset($_POST['is_enabled']) ? 1 : 0;

    // Optional: clear previous announcement
    $conn->query("DELETE FROM announcements");

    $stmt = $conn->prepare("
        INSERT INTO announcements
        (message, show_home, show_product, show_checkout, show_all, is_enabled)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "siiiii",
        $message,
        $show_home,
        $show_product,
        $show_checkout,
        $show_all,
        $is_enabled
    );

    $stmt->execute();

    header("Location: settings.php?success=announcement");
    exit;
}

/* ===========================
   SAVE FLASH SALE (WITH DISCOUNT)
   =========================== */
if ($type === 'flash_sale') {

    $title           = trim($_POST['title']);
    $start_datetime  = $_POST['start_datetime'];
    $end_datetime    = $_POST['end_datetime'];
    $discount_type   = $_POST['discount_type'] ?? '';
    $discount_value  = floatval($_POST['discount_value'] ?? 0);
    $is_enabled      = isset($_POST['is_enabled']) ? 1 : 0;

    /* ---------- VALIDATION ---------- */
    if (!$title || !$start_datetime || !$end_datetime) {
        die("Missing required fields.");
    }

    if (!in_array($discount_type, ['percent', 'fixed'])) {
        die("Invalid discount type.");
    }

    if ($discount_value <= 0) {
        die("Discount value must be greater than zero.");
    }

    if ($discount_type === 'percent' && $discount_value > 100) {
        die("Percentage discount cannot exceed 100%.");
    }

    if (strtotime($end_datetime) <= strtotime($start_datetime)) {
        die("End time must be later than start time.");
    }

    /* ---------- ENFORCE SINGLE FLASH SALE ---------- */
    $conn->query("DELETE FROM flash_sales");

    /* ---------- INSERT ---------- */
    $stmt = $conn->prepare("
        INSERT INTO flash_sales
        (title, discount_type, discount_value, start_datetime, end_datetime, is_enabled)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssdssi",
        $title,
        $discount_type,
        $discount_value,
        $start_datetime,
        $end_datetime,
        $is_enabled
    );

    $stmt->execute();

    header("Location: ../settings.php?success=flash_sale");
    exit;
}