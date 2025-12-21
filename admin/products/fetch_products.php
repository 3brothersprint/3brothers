<?php
include '../database/db.php';

$search   = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "
SELECT p.*,
(SELECT image FROM product_images WHERE product_id=p.id LIMIT 1) image
FROM products p
WHERE 1
";

if ($search) {
    $sql .= " AND (p.name LIKE '%$search%' OR p.sku LIKE '%$search%' OR p.barcode LIKE '%$search%')";
}

if ($category) {
    $sql .= " AND p.category='$category'";
}

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "
    <tr>
        <td>{$row['name']}<br><small>SKU: {$row['sku']}</small></td>
        <td>{$row['category']}</td>
        <td>₱{$row['price']}</td>
        <td>{$row['stock']}</td>
        <td>{$row['status']}</td>
        <td class='text-end'>
            <button class='btn btn-sm btn-primary'>Edit</button>
        </td>
    </tr>
    ";
}