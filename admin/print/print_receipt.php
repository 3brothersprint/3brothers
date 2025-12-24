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
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Print Receipt</title>

    <style>
    @media print {
        body {
            width: 58mm;
            margin: 0;
        }
    }

    body {
        font-family: monospace;
        font-size: 12px;
        width: 58mm;
        margin: auto;
    }

    .center {
        text-align: center;
    }

    .right {
        text-align: right;
    }

    hr {
        border: none;
        border-top: 1px dashed #000;
        margin: 6px 0;
    }
    </style>
</head>

<body onload="window.print()">

    <div class="center">
        <strong>3 BROTHERS PRINT SERVICES</strong><br>
        SERVICE RECEIPT<br>
        ----------------------
    </div>

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

    <?php if (!empty($order['price'])): ?>
    Amount: ₱<?= number_format($order['price'], 2) ?><br>
    <?php else: ?>
    Amount: Processing<br>
    <?php endif; ?>

    <hr>

    <div class="center">
        Thank you!<br>
        Please keep this receipt
    </div>

</body>

</html>