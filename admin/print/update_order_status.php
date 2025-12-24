<?php
require "../database/db.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$id     = (int)($data['id'] ?? 0);
$status = trim($data['status'] ?? '');
$price  = isset($data['price']) ? (float)$data['price'] : null;
$remark = trim($data['remark'] ?? '');

if (!$id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$conn->begin_transaction();

try {

    /* ===============================
       STATUS LOGIC
    =============================== */

    if ($status === 'Approved') {
        $stmt = $conn->prepare("
            UPDATE print_requests
            SET status = 'Approved',
                price = ?,
                payment_status = 'Paid',
                paid_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param("di", $price, $id);
    }
    elseif ($status === 'Rejected') {
        $stmt = $conn->prepare("
            UPDATE print_requests
            SET status = 'Rejected',
                payment_status = 'Rejected'
            WHERE id = ?
        ");
        $stmt->bind_param("i", $id);
    }
    else {
        $stmt = $conn->prepare("
            UPDATE print_requests
            SET status = ?, price = ?
            WHERE id = ?
        ");
        $stmt->bind_param("sdi", $status, $price, $id);
    }

    $stmt->execute();
    $stmt->close();

    /* ===============================
       INSERT LOG
    =============================== */
    $log = $conn->prepare("
        INSERT INTO print_request_logs (request_id, status, remark)
        VALUES (?, ?, ?)
    ");
    $log->bind_param("iss", $id, $status, $remark);
    $log->execute();
    $log->close();

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Order updated successfully'
    ]);

} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
}