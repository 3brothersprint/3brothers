<?php
session_start();
require "../database/db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0, 'html' => '']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

/* Fetch latest notifications */
$stmt = $conn->prepare("
    SELECT id, message, link, is_read, created_at
    FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 10
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

/* Count unread */
$countStmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM notifications
    WHERE user_id = ? AND is_read = 0
");
$countStmt->bind_param("i", $user_id);
$countStmt->execute();
$unreadCount = $countStmt->get_result()->fetch_assoc()['total'];

$html = '';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $html .= '
        <li class="px-3 py-2 border-bottom notification-item ' . ($row['is_read'] ? '' : 'bg-light') . '">
            <a href="'.htmlspecialchars($row['link']).'"
               class="text-decoration-none text-dark d-block">
                <div class="small">'.htmlspecialchars($row['message']).'</div>
                <small class="text-muted">'.date("M d, Y h:i A", strtotime($row['created_at'])).'</small>
            </a>
        </li>';
    }
} else {
    $html = '<li class="px-3 py-3 text-muted small text-center">No notifications</li>';
}

echo json_encode([
    'count' => (int)$unreadCount,
    'html'  => $html
]);