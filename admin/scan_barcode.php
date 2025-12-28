<?php
include 'database/db.php';
include 'includes/header.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['tracking'])) {

    $tracking = trim($_POST['tracking']);
    $city = strtoupper($_POST['scan_city'] ?? 'UNKNOWN LOCATION');
    $action   = $_POST['scan_action'] ?? 'arrived';

    /* ===============================
       FIND ORDER BY TRACKING
    =============================== */
    $stmt = $conn->prepare("
        SELECT order_id 
        FROM logistics_shipments 
        WHERE tracking_number = ?
    ");
    $stmt->bind_param("s", $tracking);
    $stmt->execute();
    $ship = $stmt->get_result()->fetch_assoc();

    if ($ship) {
        $order_id = $ship['order_id'];

        /* ===============================
           STATUS + REMARK LOGIC
        =============================== */
        if ($action === 'departed') {
            $status = 'To Transit';
            $remark = "Your order has departed from delivery hub: $city";
        } else {
            $status = 'To Transit';
            $remark = "Your order has arrived at delivery hub: $city";
        }

        /* ===============================
           UPDATE ORDER STATUS
        =============================== */
        $update = $conn->prepare("
            UPDATE orders 
            SET status = ? 
            WHERE id = ?
        ");
        $update->bind_param("si", $status, $order_id);
        $update->execute();

        /* ===============================
           LOGISTICS LOG
        =============================== */
        $log1 = $conn->prepare("
            INSERT INTO logistics_logs
            (order_id, status, remark, scanned_location)
            VALUES (?, ?, ?, ?)
        ");
        $log1->bind_param("isss", $order_id, $status, $remark, $city);
        $log1->execute();

        /* ===============================
           CUSTOMER ORDER LOG
        =============================== */
        $log2 = $conn->prepare("
            INSERT INTO order_logs
            (order_id, status, remarks)
            VALUES (?, ?, ?)
        ");
        $log2->bind_param("iss", $order_id, $status, $remark);
        $log2->execute();

       $_SESSION['scan_success'] = true;
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;


} else {
    echo "<script>alert('Tracking number not found');</script>";
}
}
?>

<main class="col-md-9 col-lg-10 px-3 py-4">

    <h4 class="mb-4">📦 Logistics – Scan Tracking</h4>
    <div class="d-flex justify-content-center align-items-center min-vh-100 bg-light">

        <form method="POST" class="card shadow-lg rounded-4 border-0 p-4 w-100" style="max-width:520px;">

            <!-- HEADER -->
            <div class="text-center mb-4">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                    style="width:64px;height:64px;">
                    <i class="bi bi-upc-scan fs-2"></i>
                </div>

                <h4 class="fw-bold mb-1">Logistics Scan</h4>
                <div class="text-muted small">
                    Scan barcode to update shipment movement
                </div>
            </div>

            <!-- SCAN TYPE -->
            <div class="mb-3">
                <label class="small fw-semibold text-uppercase text-muted mb-1">
                    Scan Type
                </label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light">
                        <i class="bi bi-arrow-left-right"></i>
                    </span>
                    <select name="scan_action" class="form-select" required>
                        <option value="arrived">📥 Arrived</option>
                        <option value="departed">📤 Departed</option>
                    </select>
                </div>
            </div>

            <!-- LOCATION -->
            <div class="mb-3">
                <label class="small fw-semibold text-uppercase text-muted mb-1">
                    Scan Location
                </label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light">
                        <i class="bi bi-geo-alt"></i>
                    </span>
                    <select name="scan_city" class="form-select" required>
                        <optgroup label="Cebu Province">
                            <option value="Cebu City">Cebu City</option>
                            <option value="Mandaue City">Mandaue City</option>
                            <option value="Consolacion">Consolacion</option>
                        </optgroup>

                        <optgroup label="Metro Manila">
                            <option value="Quezon City">Quezon City</option>
                            <option value="Manila">Manila</option>
                            <option value="Makati">Makati</option>
                            <option value="Pasig">Pasig</option>
                        </optgroup>

                        <optgroup label="Other Key Cities">
                            <option value="Davao City">Davao City</option>
                            <option value="Cagayan de Oro">Cagayan de Oro</option>
                            <option value="Bacolod">Bacolod</option>
                            <option value="Iloilo City">Iloilo City</option>
                        </optgroup>
                    </select>
                </div>
            </div>

            <!-- BARCODE INPUT -->
            <div class="mb-4">
                <label class="small fw-semibold text-uppercase text-muted mb-1">
                    Tracking Number
                </label>
                <input type="text" name="tracking" autofocus class="form-control form-control-lg text-center fw-bold"
                    placeholder="SCAN BARCODE" style="letter-spacing:2px;" required>
                <div class="form-text text-center">
                    Barcode scanner will auto-submit
                </div>
            </div>

            <!-- SUBMIT -->
            <button class="btn btn-primary btn-lg w-100 rounded-pill fw-semibold">
                <i class="bi bi-check-circle me-1"></i> Record Scan
            </button>

        </form>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
<?php if (!empty($_SESSION['scan_success'])): ?>
<script>
alert('Scan recorded successfully');
</script>
<?php unset($_SESSION['scan_success']); endif; ?>