<?php
require '../database/db.php';

$id    = intval($_POST['id'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

$stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
$stmt->bind_param("ii", $stock, $id);
$stmt->execute();

echo json_encode(['success' => true]);