<?php
require '../database/db.php';

$query = "
    SELECT *
    FROM orders
    ORDER BY created_at DESC
";

$result = $conn->query($query);

$orders = [];

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $orders
]);