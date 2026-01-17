<?php
require '../database/db.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Now all date() and time() functions will use PH time
echo date('h:i A');  // Will print 12:48 PM instead of 05:47 AM

// Month parameter, default to current month
$month = $_GET['month'] ?? date('Y-m');

// Sanitize input
$month = date('Y-m', strtotime($month));

// Fetch attendance data
$stmt = $conn->prepare("
    SELECT 
        u.full_name,
        a.login_date,
        a.login_time,
        a.logout_time,
        a.status
    FROM admin_attendance a
    JOIN users u ON u.id = a.admin_id
    WHERE DATE_FORMAT(a.login_date, '%Y-%m') = ?
    ORDER BY a.login_date, u.full_name
");
$stmt->bind_param("s", $month);
$stmt->execute();
$rows = $stmt->get_result();

// Start HTML buffer
ob_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
    @page {
        margin: 25mm;
    }

    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 11px;
        color: #000;
    }

    .watermark {
        position: fixed;
        top: 45%;
        left: 50%;
        width: 320px;
        transform: translate(-50%, -50%);
        opacity: 0.06;
        z-index: -1;
    }

    .header {
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .header table {
        width: 100%;
    }

    .header img {
        height: 60px;
    }

    .header h2 {
        margin: 0;
        font-size: 16px;
        letter-spacing: 1px;
    }

    .header small {
        font-size: 10px;
        color: #555;
    }

    .meta {
        margin-bottom: 10px;
        font-size: 10px;
    }

    table.report {
        width: 100%;
        border-collapse: collapse;
    }

    table.report th,
    table.report td {
        border: 1px solid #000;
        padding: 6px;
        text-align: center;
    }

    table.report th {
        background: #f0f0f0;
        font-size: 10px;
        text-transform: uppercase;
    }

    .status-late {
        font-weight: bold;
        color: #d9534f;
        border-left: 4px solid #d9534f;
    }

    .status-ontime {
        font-weight: bold;
        color: #198754;
    }

    .footer {
        margin-top: 40px;
    }

    .signatures {
        width: 100%;
        margin-top: 40px;
    }

    .signatures td {
        text-align: center;
        padding-top: 40px;
    }

    .line {
        border-top: 1px solid #000;
        margin-top: 30px;
    }
    </style>
</head>

<body>

    <div class="watermark">
        <img src="Logo.png" width="100%">
    </div>

    <!-- HEADER -->
    <div class="header">
        <table>
            <tr>
                <td>
                    <h2>ADMIN ATTENDANCE REPORT</h2>
                    <small>3 Brothers Print Services</small>
                </td>
            </tr>
        </table>
    </div>

    <!-- META -->
    <div class="meta">
        <strong>Month:</strong> <?= date('F Y', strtotime($month)) ?><br>
        <strong>Generated:</strong> <?= date('M d, Y h:i A') ?>
    </div>

    <!-- TABLE -->
    <table class="report">
        <thead>
            <tr>
                <th width="22%">Admin Name</th>
                <th width="14%">Date</th>
                <th width="14%">Login</th>
                <th width="14%">Logout</th>
                <th width="10%">Status</th>
                <th width="26%">Signature</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($r = $rows->fetch_assoc()): ?>
            <tr>
                <td style="text-align:left"><?= htmlspecialchars($r['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= date('M d, Y', strtotime($r['login_date'])) ?></td>
                <td><?= $r['login_time'] ? date('h:i A', strtotime($r['login_time'])) : '-' ?></td>
                <td><?= $r['logout_time'] ? date('h:i A', strtotime($r['logout_time'])) : '-' ?></td>
                <td class="<?= $r['status'] === 'Late' ? 'status-late' : 'status-ontime' ?>">
                    <?= htmlspecialchars(strtoupper($r['status']), ENT_QUOTES, 'UTF-8') ?>
                </td>
                <td></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- SIGNATURES -->
    <table class="signatures">
        <tr>
            <td>
                Prepared by
                <div class="line"></div>
                <strong>Admin / HR</strong>
            </td>
            <td>
                Verified by
                <div class="line"></div>
                <strong>Supervisor</strong>
            </td>
            <td>
                Approved by
                <div class="line"></div>
                <strong>Manager</strong>
            </td>
        </tr>
    </table>

</body>

</html>

<?php
$html = ob_get_clean();

$options = new Options();
$options->set('defaultFont', 'DejaVu Sans'); // UTF-8 supported font
$options->set('isRemoteEnabled', true);
$options->set('chroot', realpath('../')); // optional for security

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8'); // explicitly set UTF-8
$dompdf->setPaper('Letter', 'portrait');
$dompdf->render();

// Stream PDF to browser
$dompdf->stream("Admin Attendance " . $month . ".pdf", ["Attachment" => true]);