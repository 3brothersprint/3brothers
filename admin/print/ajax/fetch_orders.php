<?php
require "../../database/db.php";

$query = "
    SELECT 
        id,
        request_no,
        full_name,
        status,
        print_type,
        created_at
    FROM print_requests
    ORDER BY created_at DESC
";

$result = $conn->query($query);

$statusMap = [
    'Order Placed' => ['row' => 'table-info', 'badge' => 'primary'],
    'Pending' => ['row' => 'table-warning', 'badge' => 'warning text-dark'],
    'Approved' => ['row' => 'table-success', 'badge' => 'success'],
    'Printing' => ['row' => 'table-info', 'badge' => 'primary'],
    'Ready for Pickup' => ['row' => 'table-info', 'badge' => 'primary'],
    'Completed' => ['row' => 'table-success', 'badge' => 'success'],
    'Cancelled' => ['row' => 'table-danger', 'badge' => 'danger'],
    'Rejected' => ['row' => 'table-danger', 'badge' => 'danger'],
];

if ($result && $result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
        $status = $row['status'];
        $rowClass   = $statusMap[$status]['row'] ?? '';
        $badgeClass = $statusMap[$status]['badge'] ?? 'secondary';
?>
<tr class="<?= $rowClass ?> bg-opacity-25">
    <td>
        <div class="fw-semibold"><?= htmlspecialchars($row['full_name']) ?></div>
        <small class="text-muted">
            <?= date('M d, Y', strtotime($row['created_at'])) ?>
        </small>
    </td>

    <td><?= htmlspecialchars($row['request_no']) ?></td>
    <td><?= htmlspecialchars($row['print_type']) ?></td>
    <td>—</td>

    <td>
        <span class="badge bg-<?= $badgeClass ?>">
            <?= htmlspecialchars($status) ?>
        </span>
    </td>

    <td class="text-end">
        <button class="btn btn-sm btn-outline-primary" onclick="viewOrder(<?= (int)$row['id'] ?>)">
            <i class="bi bi-eye"></i>
        </button>

        <button class="btn btn-sm btn-outline-success" onclick="printReceipt(<?= (int)$row['id'] ?>)">
            <i class="bi bi-printer"></i>
        </button>

        <button class="btn btn-sm btn-outline-danger" onclick="deleteOrder(<?= (int)$row['id'] ?>)">
            <i class="bi bi-trash"></i>
        </button>
    </td>
</tr>
<?php endwhile; else: ?>
<tr>
    <td colspan="6" class="text-center text-muted py-4">
        No print orders found
    </td>
</tr>
<?php endif; ?>