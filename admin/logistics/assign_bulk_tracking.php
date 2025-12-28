<?php
include '../database/db.php';

$order_ids = $_POST['order_ids'] ?? [];

foreach ($order_ids as $id) {

    $id = (int)$id;
    $tracking = $_POST['tracking'][$id] ?? '';
    $courier  = $_POST['courier'][$id] ?? '';

    if (!$tracking || !$courier) continue;

    /* 1️⃣ INSERT LOGISTICS SHIPMENT */
    $conn->query("
        INSERT INTO logistics_shipments (order_id, courier, tracking_number)
        VALUES ('$id', '$courier', '$tracking')
    ");
    
    /* 3 UPDATE ORDER STATUS */
    $conn->query("
        UPDATE orders
        SET status = 'To Transit'
        WHERE id = '$id'
    ");

    /* 3️⃣ INSERT ORDER LOG */
    $conn->query("
        INSERT INTO order_logs (order_id, status, remarks)
        VALUES ('$id', 'To Transit', 'Order has been picked up by logistics')
    ");

    /* 4️⃣ (OPTIONAL) INSERT LOGISTICS LOG */
    $conn->query("
        INSERT INTO logistics_logs (order_id, status, remark)
        VALUES ('$id', 'To Ship', 'Tracking number assigned')
    ");
}

header("Location: ../logistics.php?success=1");
exit;