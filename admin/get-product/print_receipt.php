<?php
require "../database/db.php";
require "vendor/autoload.php";

use Picqer\Barcode\BarcodeGeneratorPNG;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;


$id = intval($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) die("Invalid order");
function getHubCode($address) {
    $map = [
        'MANILA' => 'MNL',
        'QUEZON' => 'QC',
        'MAKATI' => 'MKT',
        'PASAY'  => 'PSY',
        'CEBU'   => 'CEB',
        'DAVAO'  => 'DVO'
    ];

    foreach ($map as $city => $hub) {
        if (stripos($address, $city) !== false) {
            return $hub;
        }
    }
    return 'GEN'; // fallback hub
}

$hub = getHubCode($order['delivery_address']);
/* ===== GET TRACKING NUMBER ===== */
$trackStmt = $conn->prepare("
    SELECT tracking_number 
    FROM logistics_shipments
    WHERE order_id = ?
    LIMIT 1
");
$trackStmt->bind_param("i", $order['id']);
$trackStmt->execute();
$track = $trackStmt->get_result()->fetch_assoc();

$trackingNumber = $track['tracking_number'] ?? null;

// Barcode should ALWAYS be tracking number if exists
$barcodeValue = $trackingNumber ?: $order['order_no'];

/* ===== BARCODE ===== */
$barcodeGen = new BarcodeGeneratorPNG();
$barcode = base64_encode(
    $barcodeGen->getBarcode(
        $barcodeValue,
        $barcodeGen::TYPE_CODE_128,
        3,
        70
    )
);


/* ===== QR CODE ===== */
$qrCode = new QrCode($order['order_no']);
$qrCode->setSize(180);
$qrCode->setMargin(0);
$qrCode->setEncoding(new Encoding('UTF-8'));

$writer = new PngWriter();
$result = $writer->write($qrCode);

$qrBase64 = base64_encode($result->getString());


/* ===== WATERMARK ===== */
$watermark = strtoupper($order['payment_method']) === 'COD' ? 'COD' : 'PAID';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
    @page {
        size: A4 portrait;
        margin: 0;
    }

    * {
        box-sizing: border-box;
    }

    html,
    body {
        width: 8.27in;
        height: 11.69in;
        margin: 0;
        padding: 0;
        font-family: Arial, Helvetica, sans-serif;
    }

    /* PAGE */
    .page {
        width: 100%;
        height: 100%;
        padding: 8px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    /* WAYBILL (LANDSCAPE DESIGN) */
    .waybill {
        width: 100%;
        height: 50%;
        border: 2px solid #000;
        padding: 10px;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    /* COPY LABEL */
    .copy-label {
        position: absolute;
        top: 6px;
        right: 10px;
        border: 2px solid #000;
        font-size: 14px;
        font-weight: 900;
        padding: 3px 12px;
    }

    /* WATERMARK */
    .watermark {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 90px;
        font-weight: 900;
        opacity: 0.06;
        transform: rotate(-25deg);
        pointer-events: none;
    }

    /* HEADER */
    .header {
        border-bottom: 3px solid #000;
        padding-bottom: 6px;
    }

    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        font-size: 26px;
        font-weight: 900;
    }

    .hub {
        border: 4px solid #000;
        padding: 6px 18px;
        font-size: 24px;
        font-weight: 900;
    }

    /* BARCODE */
    .barcode {
        text-align: center;
        margin: 6px 0;
    }

    .order-no {
        text-align: center;
        font-size: 22px;
        font-weight: 900;
    }

    /* BODY ROWS */
    .block {
        border-bottom: 2px solid #000;
        padding: 6px 0;
    }

    .title {
        font-size: 16px;
        font-weight: 900;
    }

    .big {
        font-size: 18px;
        font-weight: 700;
    }

    .info {
        font-size: 15px;
    }

    /* FOOTER */
    .footer {
        display: flex;
        justify-content: space-between;
        margin-top: auto;
    }

    .qr img {
        width: 110px;
        height: 110px;
    }

    /* SCAN LOG */
    .scan-log table {
        border-collapse: collapse;
    }

    .scan-log td {
        border: 1px solid #000;
        padding: 6px;
        text-align: center;
        font-size: 14px;
    }
    </style>

</head>

<body onload="window.print()">

    <div class="page">

        <!-- SELLER COPY -->
        <div class="waybill">
            <div class="copy-label">SELLER COPY</div>
            <div class="watermark"><?= $watermark ?></div>

            <div class="header">
                <div class="header-row">
                    <div class="logo">3BPS&ES</div>
                </div>
            </div>

            <div class="barcode">
                <img src="data:image/png;base64,<?= $barcode ?>">
            </div>
            <div class="order-no">
                <?= htmlspecialchars($barcodeValue) ?><br>
            </div>


            <div class="block">
                <div class="title">BUYER</div>
                <div class="big"><?= htmlspecialchars($order['recipient_name']) ?></div>
                <div class="info"><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></div>
                <div class="big"><?= htmlspecialchars($order['recipient_phone']) ?></div>
            </div>

            <div class="block">
                <div class="title">SELLER</div>
                <div class="big">Print Shop Name</div>
                <div class="info">123 Printing Street, City</div>
            </div>

            <div class="block info">
                <strong>PAYMENT:</strong> <?= strtoupper($order['payment_method']) ?><br>
                <strong>TOTAL:</strong> ₱<?= number_format($order['total_amount'], 2) ?><br>
                <strong>DATE:</strong> <?= date("m/d/Y", strtotime($order['created_at'])) ?>
            </div>

            <div class="footer">
                <div class="scan-log">
                    <strong>COURIER SCAN LOG</strong>
                    <table>
                        <tr>
                            <td>Picked Up</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>In Transit</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Delivered</td>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <div class="qr">
                    <img src="data:image/png;base64,<?= $qrBase64 ?>">
                </div>
            </div>
        </div>

        <!-- CUSTOMER COPY -->
        <div class="waybill">
            <div class="copy-label">CUSTOMER COPY</div>
            <div class="watermark"><?= $watermark ?></div>

            <!-- SAME CONTENT -->
            <?php /* duplicate content intentionally for print consistency */ ?>
            <div class="header">
                <div class="header-row">
                    <div class="logo">3BPS&ES</div>
                </div>
            </div>

            <div class="barcode">
                <img src="data:image/png;base64,<?= $barcode ?>">
            </div>
            <div class="order-no">
                <?= htmlspecialchars($barcodeValue) ?><br>
            </div>

            <div class="block">
                <div class="title">BUYER</div>
                <div class="big"><?= htmlspecialchars($order['recipient_name']) ?></div>
                <div class="info"><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></div>
                <div class="big"><?= htmlspecialchars($order['recipient_phone']) ?></div>
            </div>

            <div class="block">
                <div class="title">SELLER</div>
                <div class="big">Print Shop Name</div>
                <div class="info">123 Printing Street, City</div>
            </div>

            <div class="block info">
                <strong>PAYMENT:</strong> <?= strtoupper($order['payment_method']) ?><br>
                <strong>TOTAL:</strong> ₱<?= number_format($order['total_amount'], 2) ?><br>
                <strong>DATE:</strong> <?= date("m/d/Y", strtotime($order['created_at'])) ?>
            </div>

            <div class="footer">
                <div class="scan-log">
                    <strong>COURIER SCAN LOG</strong>
                    <table>
                        <tr>
                            <td>Picked Up</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>In Transit</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Delivered</td>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <div class="qr">
                    <img src="data:image/png;base64,<?= $qrBase64 ?>">
                </div>
            </div>
            <?= ob_get_clean(); ?>
        </div>

    </div>

</body>


</html>