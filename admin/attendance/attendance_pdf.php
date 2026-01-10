<?php
require '../database/db.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$month = $_GET['month'] ?? date('Y-m-d');

$rows = $conn->query("
    SELECT 
        u.full_name,
        a.login_date,
        a.login_time,
        a.logout_time,
        a.status
    FROM admin_attendance a
    JOIN users u ON u.id = a.admin_id
    WHERE DATE_FORMAT(a.login_date, '%Y-%m') = '$month'
    ORDER BY a.login_date, u.full_name
");

ob_start();
?>

<!DOCTYPE html>
<html>

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

    /* WATERMARK */
    .watermark {
        position: fixed;
        top: 45%;
        left: 50%;
        width: 320px;
        transform: translate(-50%, -50%);
        opacity: 0.06;
        z-index: -1;
    }

    /* HEADER */
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

    /* META */
    .meta {
        margin-bottom: 10px;
        font-size: 10px;
    }

    /* TABLE */
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
        color: #000;
        border-left: 4px solid #000;
    }

    .status-ontime {
        font-weight: bold;
    }

    /* FOOTER */
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
                <td style="text-align:left"><?= htmlspecialchars($r['full_name']) ?></td>
                <td><?= date('M d, Y', strtotime($r['login_date'])) ?></td>
                <td><?= $r['login_time'] ? date('h:i A', strtotime($r['login_time'])) : '-' ?></td>
                <td><?= $r['logout_time'] ? date('h:i A', strtotime($r['logout_time'])) : '-' ?></td>
                <td class="<?= $r['status'] === 'Late' ? 'status-late' : 'status-ontime' ?>">
                    <?= strtoupper($r['status']) ?>
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
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('Letter', 'portrait');
$dompdf->render();

$dompdf->stream(
    "Admin Attendance - " . $month . ".pdf",
    ["Attachment" => true]
);