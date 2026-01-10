<?php
require '../database/db.php';

$id = (int)($_POST['id'] ?? 0);
if (!$id) exit(json_encode(['success' => false]));

$stmt = $conn->prepare("
    UPDATE products
    SET status = IF(status='Inactive','Active','Inactive')
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode(['success' => true]);