<?php
require '../database/db.php';

/* ===========================
   FETCH ALL INVENTORY (BY VARIANT)
   =========================== */
$result = $conn->query("
    SELECT 
        p.product_no,
        p.name AS product_name,
        p.sku AS product_sku,
        v.id AS variant_id,
        v.value AS variant_name,
        v.stock AS variant_stock,
        COALESCE(i.low_stock_threshold, 0) AS low_stock_threshold,
        i.updated_at
    FROM product_variants v
    JOIN products p ON p.id = v.product_id
    LEFT JOIN inventory i ON i.product_id = p.id
    ORDER BY p.name ASC, v.value ASC
");

if (!$result || $result->num_rows === 0) {
    die("No inventory data found");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inventory Report</title>

    <style>
    @page {
        size: Letter landscape;
        margin: 20mm;
    }

    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 13px;
        color: #222;
    }

    .container {
        width: 100%;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 4px solid #0d6efd;
        padding-bottom: 12px;
        margin-bottom: 20px;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .logo img {
        height: 60px;
    }

    .logo h2 {
        margin: 0;
        font-size: 22px;
        color: #0d6efd;
    }

    .header-info {
        text-align: right;
        font-size: 12px;
        color: #555;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    thead {
        background: #0d6efd;
        color: #fff;
    }

    th,
    td {
        border: 1px solid #ccc;
        padding: 8px 10px;
        text-align: left;
    }

    th {
        font-size: 13px;
        text-transform: uppercase;
    }

    tbody tr:nth-child(even) {
        background: #f8f9fa;
    }

    .stock {
        font-weight: bold;
        text-align: center;
    }

    .low {
        color: #dc3545;
        font-weight: bold;
    }

    .footer {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        font-size: 12px;
    }

    .signature {
        text-align: center;
    }

    .signature .line {
        width: 220px;
        border-top: 1px solid #000;
        margin: 40px auto 5px;
    }

    .muted {
        color: #666;
    }
    </style>
</head>

<body onload="window.print()">

    <div class="container">

        <!-- HEADER -->
        <div class="header">
            <div class="logo">
                <img src="../../assets/Logo.png" alt="Company Logo">
                <h2>3 BROTHERS PRINT SERVICES & EDUCATIONAL SUPPLIES</h2>
            </div>

            <div class="header-info">
                <div><strong>INVENTORY REPORT</strong></div>
                <div>Generated: <?= date('M d, Y h:i A') ?></div>
            </div>
        </div>

        <!-- INVENTORY TABLE -->
        <table>
            <thead>
                <tr>
                    <th>Product No</th>
                    <th>Product Name</th>
                    <th>Variant</th>
                    <th>Product SKU</th>
                    <th>Stock</th>
                    <th>Low Stock Alert</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['product_no']) ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= htmlspecialchars($row['variant_name']) ?></td>
                    <td><?= htmlspecialchars($row['product_sku']) ?></td>

                    <td class="stock <?= $row['variant_stock'] <= $row['low_stock_threshold'] ? 'low' : '' ?>">
                        <?= (int)$row['variant_stock'] ?>
                    </td>

                    <td class="text-center">
                        <?= (int)$row['low_stock_threshold'] ?>
                    </td>

                    <td>
                        <?= $row['updated_at']
                        ? date('M d, Y h:i A', strtotime($row['updated_at']))
                        : '—'
                    ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- FOOTER -->
        <div class="footer">
            <div class="muted">
                This document is system-generated and valid without signature.
            </div>

            <div class="signature">
                <div class="line"></div>
                <strong>Prepared by</strong><br>
                Admin / Inventory Officer
            </div>
        </div>

    </div>

</body>

</html>