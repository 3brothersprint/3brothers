<?php
session_start();
require '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];
$address_id = intval($_POST['id'] ?? 0);

if ($address_id <= 0) {
    http_response_code(400);
    exit('Invalid address');
}

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("
        UPDATE user_addresses
        SET is_default = 0
        WHERE user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $stmt = $conn->prepare("
        UPDATE user_addresses
        SET is_default = 1
        WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param("ii", $address_id, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new Exception("Address not found");
    }

    $conn->commit();
    echo "success";
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo "error";
}