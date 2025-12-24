<?php
function sendNotification($user_id, $request_no, $status, $price = 0)
{
    global $conn;

    $message = ($status === 'Approved')
        ? "Your print request #$request_no has been approved. Total price: ₱" . number_format($price, 2)
        : "Your print request #$request_no has been rejected. Please contact support.";

    $link = "orders.php";

    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, message, link)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("iss", $user_id, $message, $link);
    $stmt->execute();
    $stmt->close();
}