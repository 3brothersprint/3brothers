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
        size: 105mm 148mm;
        margin: 0;
    }

    * {
        box-sizing: border-box;
    }

    html,
    body {
        width: 105mm;
        height: 148mm;
        margin: 0;
        padding: 0;
        font-family: Arial, Helvetica, sans-serif;
    }

    /* AWB CONTAINER */
    .awb {
        width: 100%;
        height: 100%;
        border: 2px solid #000;
        padding: 6mm;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    /* WATERMARK */
    .watermark {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        font-weight: 900;
        opacity: 0.06;
        transform: rotate(-25deg);
        pointer-events: none;
    }

    /* HEADER */
    .header {
        border-bottom: 2px solid #000;
        padding-bottom: 4px;
        margin-bottom: 4px;
    }

    .logo {
        font-size: 18px;
        font-weight: 900;
    }

    /* BARCODE */
    .barcode {
        text-align: center;
        margin: 4px 0;
    }

    .barcode img {
        width: 100%;
        height: 40px;
    }

    .order-no {
        text-align: center;
        font-size: 14px;
        font-weight: 900;
        margin-bottom: 4px;
    }

    /* BLOCKS */
    .block {
        border-bottom: 1px solid #000;
        padding: 4px 0;
    }

    .title {
        font-size: 11px;
        font-weight: 900;
    }

    .big {
        font-size: 13px;
        font-weight: 700;
    }

    .info {
        font-size: 11px;
    }

    /* FOOTER */
    .footer {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: auto;
    }

    .qr img {
        width: 70px;
        height: 70px;
    }

    /* SCAN LOG */
    .scan-log table {
        border-collapse: collapse;
        font-size: 10px;
    }

    .scan-log td {
        border: 1px solid #000;
        padding: 3px;
        text-align: center;
    }
    </style>


</head>

<body>

    <div class="awb">

        <div class="watermark"><?= $watermark ?></div>

        <div class="header">
            <div class="logo">3BPS&ES</div>
        </div>

        <div class="barcode">
            <img src="data:image/png;base64,<?= $barcode ?>">
        </div>

        <div class="order-no">
            <?= htmlspecialchars($barcodeValue) ?>
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
                <strong>SCAN</strong>
                <table>
                    <tr>
                        <td>Pickup</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Transit</td>
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

</body>


</html>