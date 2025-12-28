<?php
require "../database/db.php";

$id = (int)($_GET['id'] ?? 0);
if (!$id) die("Invalid request");

/* FETCH ORDER */
$stmt = $conn->prepare("
    SELECT *
    FROM print_requests
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) die("Order not found");

/* FETCH FILES */
$stmt = $conn->prepare("
    SELECT file_name
    FROM print_request_files
    WHERE request_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$files = $stmt->get_result();
$stmt->close();

$queueNo = date('His'); // e.g. 145233 (time-based, avoids duplicates)

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Print Receipt</title>

    <style>
    @page {
        size: auto;
        margin: 10mm;
    }

    body {
        font-family: monospace;
        font-size: 12px;
        margin: 0;
        padding: 0;
    }

    /* LEFT ALIGN PAGE */
    .print-area {
        display: flex;
        justify-content: flex-start;
    }

    /* RECEIPT BOX */
    .receipt {
        width: 58mm;
        border: 1px solid #000;
        padding: 6px;
        margin-bottom: 8mm;
    }

    /* TEXT HELPERS */
    .center {
        text-align: center;
    }

    .right {
        text-align: right;
    }

    /* DASHED SEPARATOR */
    hr {
        border: none;
        border-top: 1px dashed #000;
        margin: 6px 0;
    }

    /* CUT LINE */
    .cut-line {
        font-size: 11px;
        margin: 6mm 0;
    }

    .cut-line::before {
        content: "✂️ --------------------------------";
    }
    </style>
</head>

<body onload="window.print()">

    <div class="print-area">

        <!-- ================= STORE COPY ================= -->
        <div class="receipt">

            <div class="center">
                <strong>3 BROTHERS PRINT SERVICES</strong><br>
                SERVICE RECEIPT<br>
                <strong>STORE COPY</strong>
            </div>

            <hr>

            Queue No: <strong><?= $queueNo ?></strong><br>
            Order #: <?= htmlspecialchars($order['request_no']) ?><br>
            Date: <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?><br>
            Customer: <?= htmlspecialchars($order['full_name']) ?><br>

            <hr>

            Service: <?= htmlspecialchars($order['print_type']) ?><br>
            Paper: <?= htmlspecialchars($order['paper_size']) ?><br>
            Copies: <?= (int)$order['copies'] ?><br>
            Color: <?= htmlspecialchars($order['color']) ?><br>

            <hr>

            <strong>FILES</strong><br>
            <?php while ($f = $files->fetch_assoc()): ?>
            - <?= htmlspecialchars($f['file_name']) ?><br>
            <?php endwhile; ?>

            <hr>

            Status: <?= htmlspecialchars($order['status']) ?><br>
            Amount:
            <?= !empty($order['price']) ? '₱'.number_format($order['price'],2) : 'Processing' ?>

            <hr>

            <div class="center">
                Staff Signature: ___________<br>
            </div>

        </div>

    </div>

    <!-- ✂️ CUT LINE -->
    <div class="cut-line"></div>

    <div class="print-area">

        <!-- ================= CUSTOMER COPY ================= -->
        <div class="receipt">

            <div class="center">
                <strong>3 BROTHERS PRINT SERVICES</strong><br>
                SERVICE RECEIPT<br>
                <strong>CUSTOMER COPY</strong>
            </div>

            <hr>

            Queue No: <strong><?= $queueNo ?></strong><br>
            Order #: <?= htmlspecialchars($order['request_no']) ?><br>
            Date: <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?><br>

            <hr>

            Service: <?= htmlspecialchars($order['print_type']) ?><br>
            Copies: <?= (int)$order['copies'] ?><br>

            <hr>

            Status: <?= htmlspecialchars($order['status']) ?><br>
            Amount:
            <?= !empty($order['price']) ? '₱'.number_format($order['price'],2) : 'Processing' ?>

            <hr>

            <div class="center">
                Thank you!<br>
                Please keep this receipt
            </div>

        </div>

    </div>

</body>

</html>