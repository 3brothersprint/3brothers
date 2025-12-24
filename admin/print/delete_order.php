<?php
session_start();
require "../database/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

/* ===============================
   DELETE FILES FIRST
   =============================== */
$stmt = $conn->prepare("
    SELECT file_path FROM print_request_files WHERE request_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    if (file_exists($row['file_path'])) {
        unlink($row['file_path']);
    }
}
$stmt->close();

/* ===============================
   DELETE FILE RECORDS
   =============================== */
$stmt = $conn->prepare("
    DELETE FROM print_request_files WHERE request_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

/* ===============================
   DELETE MAIN REQUEST
   =============================== */
$stmt = $conn->prepare("
    DELETE FROM print_requests WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode([
        'success' => true,
        'message' => 'Print request deleted successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Request not found'
    ]);
}

$stmt->close();