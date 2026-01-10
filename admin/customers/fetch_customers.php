<?php
require '../database/db.php';

$stmt = $conn->prepare("
    SELECT *
    FROM users WHERE role = 'user'
    ORDER BY id DESC
");
$stmt->execute();
$result = $stmt->get_result();

$i = 1;
while ($row = $result->fetch_assoc()):
$isBanned = (int)$row['is_banned'] === 1;
$rowClass = $isBanned ? 'table-danger banned-row' : 'table-primary active-row';
?>
<style>
:root {
    --brand-primary-start: #fa9d75;
    /* rgba(250,157,117) */
    --brand-primary-end: #f59f53;
    /* rgba(245,159,83) */
    --brand-gradient: linear-gradient(90deg,
            var(--brand-primary-start) 0%,
            var(--brand-primary-end) 100%);

    --brand-dark: #5a2f1d;

    --brand-accent: #ffb703;
    /* Buttons, badges, CTA */
    --brand-accent-soft: #fff2e8;
    /* Background highlights */
    --brand-bg: #fffaf6;
    /* Page background */
    --brand-surface: #ffffff;
    /* Cards */
    --brand-border: #f1d5c5;
    /* Dividers */
    --status-success: #2d6a4f;
    /* Muted green */
    --status-warning: #f4a261;
    /* Warm amber */
    --status-danger: #b23a2f;
    /* Brick red */
    --status-info: #457b9d;

}

/* Subtle hover effect */
.table tbody tr:hover {
    background-color: var(--brand-primary-start);
    transition: background-color 0.2s ease-in-out;
}

/* Muted banned text */
.banned-row td {
    color: #6c757d;
}

.active-row {
    border-left: 4px solid var(--status-warning);
}

.banned-row {
    background-color: #fff1f1;
    border-left: 4px solid var(--status-danger);
}

/* Base badge */
.badge-brand {
    padding: 0.45em 0.7em;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 999px;
    letter-spacing: 0.3px;
}

/* Gradient badge (Primary) */
.badge-brand-gradient {
    background: var(--brand-gradient);
    color: #fff;
}

/* Active */
.badge-active {
    background-color: var(--status-success);
    color: #fff;
}

/* Banned */
.badge-banned {
    background-color: var(--status-danger);
    color: #fff;
}

/* Soft versions (optional) */
.badge-soft {
    background-color: var(--brand-accent-soft);
    color: var(--brand-dark);
}
</style>
<tr class="<?= $rowClass ?>">
    <td><?= htmlspecialchars($row['account_no']) ?></td>
    <td><?= htmlspecialchars($row['full_name']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td>
        <?php if ($row['is_banned']): ?>
        <span class="badge badge-brand badge-banned">Banned</span>
        <?php else: ?>
        <span class="badge badge-brand badge-active">Active</span>
        <?php endif; ?>
    </td>
    <td class="text-end">
        <?php if ($row['is_banned']): ?>
        <button class="btn btn-sm btn-success" onclick="toggleBan(<?= $row['id'] ?>)">
            <i class="bi bi-person-x-fill"></i>
        </button>
        <?php else: ?>
        <button class="btn btn-sm btn-danger" onclick="toggleBan(<?= $row['id'] ?>)">
            <i class="bi bi-ban"></i>
        </button>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>