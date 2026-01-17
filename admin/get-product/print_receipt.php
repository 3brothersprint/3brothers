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
/* ===== ORDERED PRODUCTS ===== */
$itemStmt = $conn->prepare("
    SELECT product_name, quantity
    FROM order_items
    WHERE order_id = ?
");
$itemStmt->bind_param("i", $order['id']);
$itemStmt->execute();
$items = $itemStmt->get_result();

$productText = [];
$productQRText = "ORDER NO: {$order['order_no']}\n";

while ($row = $items->fetch_assoc()) {
    $line = $row['product_name'] . " x" . $row['quantity'];
    $productText[] = $line;
    $productQRText .= $line . "\n";
}

$productList = implode("<br>", array_map('htmlspecialchars', $productText));

/* SAFETY FALLBACK */
if (trim($productQRText) === "ORDER NO: {$order['order_no']}") {
    $productQRText .= "NO ITEMS FOUND";
}


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

/* ===== QR WRITER ===== */
$writer = new PngWriter();

/* ===== PRODUCT QR CODE ===== */
$productQr = new QrCode($productQRText);
$productQr->setSize(130);
$productQr->setMargin(0);
$productQr->setEncoding(new Encoding('UTF-8'));

$productQrResult = $writer->write($productQr);
$productQrBase64 = base64_encode($productQrResult->getString());

$result = $writer->write($qrCode);

$qrBase64 = base64_encode($result->getString());


/* ===== WATERMARK ===== */
$watermark = strtoupper($order['payment_method']) === 'COD' ? 'COD' : 'PAID';

$productList = implode("<br>", array_map('htmlspecialchars', $productText));

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

    body {
        width: 105mm;
        height: 148mm;
        margin: 0;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
    }

    .awb {
        border: 2px solid #000;
        padding: 6mm;
        height: 100%;
        position: relative;
    }

    .row {
        display: flex;
        justify-content: space-between;
    }

    .bold {
        font-weight: 900;
    }

    .center {
        text-align: center;
    }

    .section {
        margin-top: 6px;
    }

    .line {
        border-bottom: 1px solid #000;
        margin: 4px 0;
    }

    .barcode img {
        width: 100%;
        height: 45px;
    }

    .qr img {
        width: 65px;
        height: 65px;
    }

    .label {
        font-size: 10px;
        font-weight: 700;
    }

    .value {
        font-size: 12px;
        font-weight: 700;
    }

    .watermark {
        position: absolute;
        inset: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 48px;
        font-weight: 900;
        opacity: 0.06;
        transform: rotate(-25deg);
    }
    </style>

</head>

<body>

    <div class="awb">

        <div class="watermark"><?= $watermark ?></div>

        <!-- HEADER -->
        <div class="row bold">
            <div>3BPS&ES</div>
            <div>ORDER NO: <?= htmlspecialchars($order['order_no']) ?></div>
        </div>

        <div class="line"></div>

        <!-- BARCODE -->
        <div class="center barcode">
            <img src="data:image/png;base64,<?= $barcode ?>">
        </div>

        <div class="center value">
            <?= htmlspecialchars($barcodeValue) ?>
        </div>

        <div class="line"></div>

        <!-- SENDER (1) -->
        <div class="section">
            <div class="bold">SENDER</div>
            <div class="row">
                <div>
                    <div class="label">NAME</div>
                    <div class="value"><?= htmlspecialchars($order['recipient_name']) ?></div>
                </div>
                <div>
                    <div class="label">MOBILE</div>
                    <div class="value"><?= htmlspecialchars($order['recipient_phone']) ?></div>
                </div>
            </div>
            <div class="label">ADDRESS</div>
            <div class="value"><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></div>
        </div>

        <div class="line"></div>

        <!-- RECEIVER -->
        <div class="section">
            <div class="bold">RECEIVER</div>

            <div class="row">
                <div>
                    <div class="label">NAME</div>
                    <div class="value"><?= htmlspecialchars($order['recipient_name']) ?></div>
                </div>
                <div>
                    <div class="label">MOBILE</div>
                    <div class="value"><?= htmlspecialchars($order['recipient_phone']) ?></div>
                </div>
            </div>

            <div class="label">ADDRESS</div>
            <div class="value"><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></div>
        </div>

        <div class="line"></div>

        <!-- ITEM DESCRIPTION (BOTTOM COPY) -->
        <div class="section">
            <div class="bold">ITEM DESCRIPTION</div>

            <div class="row">
                <!-- PRODUCT LIST -->
                <div style="flex:2;">
                    <div class="label">ORDERED PRODUCTS</div>
                    <div class="value">
                        <?= $productList ?: '—' ?>
                    </div>
                </div>

                <!-- QR CODE -->
                <div style="flex:1; text-align:center;">
                    <img src="data:image/png;base64,<?= $productQrBase64 ?>" style="width:65px;height:65px;">
                </div>
            </div>
        </div>


        <!-- FOOTER -->
        <div class="row">
            <div>
                <strong>PAYMENT:</strong> <?= strtoupper($order['payment_method']) ?><br>
                <strong>TOTAL:</strong> ₱<?= number_format($order['total_amount'], 2) ?>
            </div>
        </div>

        <div class="line"></div>

        <!-- SENDER (REPEATED — DOCX STYLE) -->
        <div class="section">
            <div class="bold">3 BROTHERS PRINT SERVICES AND EDUCATIONAL SUPPLY</div>
            <div>CALL:</div>
            <div>VISIT:</div>
            <div>MESSAGE:</div>
        </div>

    </div>

</body>



</html>