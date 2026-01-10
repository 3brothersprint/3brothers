<?php
include '../database/db.php';

$month = $_GET['month'] ?? date('Y-m');

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Attendance Report</title>

    <style>
    @page {
        size: Letter;
        margin: 20mm;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #000;
        background: #fff;
    }

    body>*:not(.watermark) {
        position: relative;
        z-index: 1;
    }

    /* BUTTON */
    .download-btn {
        display: inline-block;
        margin-bottom: 12px;
        padding: 8px 14px;
        background: #b23a2f;
        color: #fff;
        text-decoration: none;
        font-size: 12px;
        border-radius: 4px;
    }

    /* HEADER */
    .header {
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 3px solid #000;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .header img {
        height: 70px;
    }

    .header h2 {
        margin: 0;
        font-size: 18px;
        letter-spacing: 1.2px;
    }

    .header small {
        font-size: 11px;
        color: #555;
    }

    /* META */
    .meta {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        margin-bottom: 10px;
    }

    /* TABLE */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 6px;
    }

    th {
        background: #f2f2f2;
        font-size: 11px;
        text-transform: uppercase;
    }

    td {
        background: #fff;
        height: 28px;
    }

    /* STATUS */
    .status-cell {
        text-align: center;
        font-weight: bold;
        letter-spacing: 0.5px;
    }

    .status-late {
        color: #b23a2f;
    }

    .status-ontime {
        color: #2d6a4f;
    }

    /* FOOTER */
    .footer {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
        font-size: 12px;
    }

    .sign {
        width: 30%;
        text-align: center;
    }

    .sign .line {
        border-top: 1px solid #000;
        margin-top: 40px;
    }

    /* WATERMARK */
    .watermark {
        position: fixed;
        top: 50%;
        left: 50%;
        width: 320px;
        height: 320px;
        transform: translate(-50%, -50%);
        opacity: 0.06;
        z-index: 0;
    }

    .watermark img {
        width: 100%;
    }

    /* PRINT */
    @media print {
        .download-btn {
            display: none;
        }

        .watermark {
            opacity: 0.05;
        }

        .status-late {
            color: #000;
            border-left: 4px solid #000;
            padding-left: 6px;
        }

        .status-ontime {
            color: #000;
        }
    }
    </style>
</head>

<body>

    <div class="watermark">
        <img src="../../assets/logo.png" alt="Company Stamp">
    </div>

    <a href="attendance_pdf.php?month=<?= htmlspecialchars($month) ?>" class="download-btn">
        📄 Download PDF
    </a>

    <div class="header">
        <img src="../../assets/logo.png" alt="Company Logo">
        <div>
            <h2>ADMIN ATTENDANCE REPORT</h2>
            <small>3 Brothers Print Services</small>
        </div>
    </div>

    <div class="meta">
        <div><strong>Month:</strong> <?= date('F Y', strtotime($month)) ?></div>
        <div><strong>Generated:</strong> <?= date('M d, Y h:i A') ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="20%">Admin Name</th>
                <th width="15%">Date</th>
                <th width="15%">Login</th>
                <th width="15%">Logout</th>
                <th width="10%">Status</th>
                <th width="25%">Signature</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($r = $rows->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($r['full_name']) ?></td>
                <td><?= date('M d, Y', strtotime($r['login_date'])) ?></td>
                <td><?= $r['login_time'] ? date('h:i A', strtotime($r['login_time'])) : '-' ?></td>
                <td><?= $r['logout_time'] ? date('h:i A', strtotime($r['logout_time'])) : '-' ?></td>
                <td class="status-cell <?= $r['status'] === 'Late' ? 'status-late' : 'status-ontime' ?>">
                    <?= strtoupper($r['status']) ?>
                </td>
                <td></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="sign">
            Prepared by
            <div class="line"></div>
            <strong>Admin / HR</strong>
        </div>

        <div class="sign">
            Verified by
            <div class="line"></div>
            <strong>Supervisor</strong>
        </div>

        <div class="sign">
            Approved by
            <div class="line"></div>
            <strong>Manager</strong>
        </div>
    </div>

</body>

</html>