<?php
session_start();
require "../database/db.php";

$result = $conn->query("
    SELECT 
        u.id,
        u.full_name,
        TIMESTAMPDIFF(MINUTE, att.last_activity, NOW()) AS diff
    FROM users u
    LEFT JOIN admin_attendance att
        ON att.admin_id = u.id
        AND att.login_date = CURDATE()
    WHERE u.role = 'admin'
");

$data = [];

while ($row = $result->fetch_assoc()) {
    $online = $row['diff'] !== null && $row['diff'] <= 5;

    $data[] = [
        'id' => (int)$row['id'],
        'name' => $row['full_name'],
        'online' => $online,
        'offlineMinutes' => $online ? 0 : (int)$row['diff']
    ];
}

echo json_encode($data);