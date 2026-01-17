<?php
session_start();
require '../database/db.php';

if (!isset($_SESSION['admin_id'])) {
    exit('Unauthorized');
}

$admin_id   = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'] ?? 'Administrator';

$month = date('Y-m');

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Admin Attendance.xls");
header("Pragma: no-cache");
header("Expires: 0");

/* Attendance records */
$stmt = $conn->prepare("
    SELECT login_date, login_time, logout_time, status
    FROM admin_attendance
    WHERE admin_id = ?
    ORDER BY login_date DESC
");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$res = $stmt->get_result();

/* Monthly summary */
$summaryStmt = $conn->prepare("
    SELECT
        COUNT(*) AS total_days,
        SUM(status = 'On Time') AS ontime_days,
        SUM(status = 'Late') AS late_days
    FROM admin_attendance
    WHERE admin_id = ?
      AND DATE_FORMAT(login_date, '%Y-%m') = ?
");
$summaryStmt->bind_param("is", $admin_id, $month);
$summaryStmt->execute();
$summary = $summaryStmt->get_result()->fetch_assoc();
?>

<table border="1" cellpadding="6" cellspacing="0" width="100%">

    <!-- TITLE -->
    <tr style="background:#343a40; color:#ffffff;">
        <th colspan="4" style="font-size:16px; padding:10px; text-align:center;">
            ADMIN ATTENDANCE REPORT
        </th>
    </tr>

    <!-- ADMIN NAME -->
    <tr>
        <td colspan="4" style="text-align:center; font-weight:bold;">
            Admin: <?= htmlspecialchars($admin_name) ?>
        </td>
    </tr>

    <!-- DATE -->
    <tr>
        <td colspan="4" style="text-align:center; font-size:12px;">
            Generated on <?= date('F d, Y h:i A') ?>
        </td>
    </tr>

    <!-- SPACE -->
    <tr>
        <td colspan="4"></td>
    </tr>

    <!-- TABLE HEADER -->
    <tr style="background:#f1f1f1; font-weight:bold; text-align:center;">
        <th>Date</th>
        <th>Login Time</th>
        <th>Logout Time</th>
        <th>Status</th>
    </tr>

    <!-- DATA -->
    <?php while ($row = $res->fetch_assoc()): ?>
    <tr>
        <td><?= date('M d, Y', strtotime($row['login_date'])) ?></td>
        <td><?= date('h:i A', strtotime($row['login_time'])) ?></td>
        <td>
            <?= $row['logout_time']
                ? date('h:i A', strtotime($row['logout_time']))
                : '—'
            ?>
        </td>
        <td style="text-align:center;
            color:<?= $row['status'] === 'Late' ? '#dc3545' : '#198754' ?>;
            font-weight:bold;">
            <?= $row['status'] ?>
        </td>
    </tr>
    <?php endwhile; ?>

    <!-- SPACE -->
    <tr>
        <td colspan="4"></td>
    </tr>

    <!-- MONTHLY SUMMARY -->
    <tr style="background:#e9ecef; font-weight:bold; text-align:center;">
        <td>Total Days</td>
        <td>On Time</td>
        <td>Late</td>
        <td>Month</td>
    </tr>
    <tr style="text-align:center;">
        <td><?= $summary['total_days'] ?? 0 ?></td>
        <td style="color:#198754; font-weight:bold;">
            <?= $summary['ontime_days'] ?? 0 ?>
        </td>
        <td style="color:#dc3545; font-weight:bold;">
            <?= $summary['late_days'] ?? 0 ?>
        </td>
        <td><?= date('F Y') ?></td>
    </tr>

</table>