<?php
include '../database/db.php';

$month = $_GET['month'] ?? date('Y-m');

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
?>

<!DOCTYPE html>
<html>

<head>
    <title>Attendance Sheet</title>
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

    @page {
        size: Letter;
        margin: 20mm;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #000;
    }

    .header {
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .header img {
        height: 70px;
    }

    .header .title {
        flex: 1;
    }

    .header h2 {
        margin: 0;
        font-size: 18px;
        letter-spacing: 1px;
    }

    .header small {
        font-size: 11px;
        color: #555;
    }

    .meta {
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        font-size: 11px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 6px;
        vertical-align: middle;
        background-color: var(--brand-gradient);
    }

    th {
        background: #f0f0f0;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 11px;
    }

    td {
        height: 28px;
    }

    .status-late {
        font-weight: bold;
    }

    .status-ontime {
        font-weight: bold;
    }

    .footer {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
        font-size: 12px;
    }

    .sign {
        text-align: center;
        width: 30%;
    }

    .sign .line {
        border-top: 1px solid #000;
        margin-top: 40px;
    }

    /* PRINT SAFE MODE */
    @media print {
        body {
            color: #000;
            background: #fff;
        }

        th {
            background: #eaeaea !important;
            color: #000 !important;
        }

        td {
            background: #fff !important;
        }

        .status-late {
            color: #000;
            border-left: 4px solid #000;
            padding-left: 6px;
        }

        .status-ontime {
            color: #000;
        }

        .header {
            border-bottom: 2px solid #000;
        }
    }
    </style>

</head>

<body>
    <a href="attendance_pdf.php?month=<?= $month ?>" class="btn btn-danger">
        📄 Download PDF
    </a>

    <div class="header">
        <img src="../../assets/logo.png" alt="Company Logo">
        <div class="title">
            <h2>ADMIN ATTENDANCE REPORT</h2>
            <small>3 Brothers Print Services</small>
        </div>
    </div>

    <div class="meta">
        <div>
            <strong>Month:</strong> <?= date('F Y', strtotime($month)) ?>
        </div>
        <div>
            <strong>Generated:</strong> <?= date('M d, Y h:i A') ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:20%">Admin Name</th>
                <th style="width:15%">Date</th>
                <th style="width:15%">Login</th>
                <th style="width:15%">Logout</th>
                <th style="width:10%">Status</th>
                <th style="width:25%">Signature</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($r = $rows->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($r['full_name']) ?></td>
                <td><?= date('M d, Y', strtotime($r['login_date'])) ?></td>
                <td><?= $r['login_time'] ? date('h:i A', strtotime($r['login_time'])) : '-' ?></td>
                <td><?= $r['logout_time'] ? date('h:i A', strtotime($r['logout_time'])) : '-' ?></td>
                <td class="<?= $r['status'] === 'Late' ? 'status-late' : 'status-ontime' ?>">
                    <?= $r['status'] ?>
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