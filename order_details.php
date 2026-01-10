<?php
session_start();
include 'database/db.php';
include 'includes/header.php';

$order_no = $_GET['order_no'] ?? '';

if (!$order_no) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Invalid request.</div></div>";
    include '../includes/footer.php';
    exit;
}

/* ===============================
   FETCH PRODUCT REQUEST
================================ */
$stmt = $conn->prepare("
    SELECT 
        o.*,
        ls.tracking_number,
        ls.courier
    FROM orders o
    LEFT JOIN logistics_shipments ls
        ON ls.order_id = o.id
    WHERE o.order_no = ?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("s", $order_no);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();

if (!$request) {
    echo "<div class='container py-5'><div class='alert alert-warning'>Order not found.</div></div>";
    include 'includes/footer.php';
    exit;
}

/* ===============================
   FETCH FILES (MULTI FILE SUPPORT)
================================ */
$fileStmt = $conn->prepare("
    SELECT *
    FROM order_items
    WHERE order_id = ?
");
$fileStmt->bind_param("i", $request['id']);
$fileStmt->execute();
$files = $fileStmt->get_result()->fetch_all(MYSQLI_ASSOC);


/* ===============================
   FETCH STATUS LOGS
================================ */
$logStmt = $conn->prepare("
    SELECT *
    FROM order_logs
    WHERE order_id = ?
    ORDER BY created_at DESC
");
$logStmt->bind_param("i", $request['id']);
$logStmt->execute();
$logs = $logStmt->get_result()->fetch_all(MYSQLI_ASSOC);


?>

<div class="container py-5">
    <h4 class="fw-bold mb-4"><i class="bi bi-box-seam"></i> Track Product Order</h4>

    <!-- ORDER INFO -->
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <div class="row align-items-end">

                <!-- ORDER / TRACKING INFO -->
                <div class="col-md-3 mb-3">
                    <div class="text-uppercase text-muted small fw-semibold">
                        <?php if (!empty($request['tracking_number']) && in_array($request['status'], [
                        'To Transit',
                        'Out for Delivery',
                        'Delivered'
                    ])): ?>
                        Tracking No
                        <?php else: ?>
                        Order No
                        <?php endif; ?>
                    </div>

                    <div class="fs-5 fw-bold">
                        <?php if (!empty($request['tracking_number']) && in_array($request['status'], [
                            'To Transit',
                            'Out for Delivery',
                            'Delivered'
                        ])): ?>
                        <?= htmlspecialchars($request['tracking_number']) ?>
                        <?php else: ?>
                        <?= htmlspecialchars($request['order_no']) ?>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($request['courier']) && !empty($request['tracking_number'])): ?>
                    <div class="text-muted small mt-1">
                        Courier: <strong><?= htmlspecialchars($request['courier']) ?></strong>
                    </div>
                    <?php endif; ?>
                </div>


                <!-- ORDER DATE -->
                <div class="col-md-3 mb-3">
                    <div class="text-uppercase text-muted small fw-semibold">
                        Order Date
                    </div>
                    <div class="fs-5">
                        <?= date('M d, Y · h:i A', strtotime($request['created_at'])) ?>
                    </div>
                </div>

                <!-- SHIPPING METHOD -->
                <div class="col-md-3 mb-3">
                    <div class="text-uppercase text-muted small fw-semibold">
                        Shipping Method
                    </div>
                    <div class="fs-5 fw-semibold">
                        <?= strtoupper(str_replace('_', ' ', htmlspecialchars($request['delivery_type'] ?? ''))) ?>
                    </div>
                </div>

                <!-- PRICE -->
                <div class="col-md-3 mb-3">
                    <div class="text-uppercase text-muted small fw-semibold">
                        Price
                    </div>

                    <?php if (!empty($request['total_amount']) && $request['total_amount'] > 0): ?>
                    <div class="fs-4 fw-bold text-success">
                        ₱<?= number_format($request['total_amount'], 2) ?>
                    </div>
                    <?php else: ?>
                    <div class="fs-5 fw-semibold text-warning">
                        Processing
                    </div>
                    <?php endif; ?>
                </div>

            </div>

            <?php if (!empty($request['payment_method'])): ?>
            <span class="badge bg-success">
                Paid via <?= strtoupper(str_replace('_', ' ', htmlspecialchars($request['payment_method']))) ?>
            </span>
            <?php endif; ?>
        </div>
    </div>


    <!-- FILE LIST -->
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-box-seam"></i> Order Items</h6>

            <ul class="list-group list-group-flush">
                <?php if (!empty($files)): ?>
                <?php foreach ($files as $file): ?>
                <li class="list-group-item px-0">
                    <div class="d-flex align-items-start gap-3">

                        <!-- Product Image -->
                        <img src="<?= htmlspecialchars($file['product_image']) ?>"
                            alt="<?= htmlspecialchars($file['product_name']) ?>" class="border rounded" width="60"
                            height="60">

                        <!-- Product Info -->
                        <div class="flex-grow-1">
                            <div class="fw-semibold">
                                <?= htmlspecialchars($file['product_name']) ?>
                            </div>

                            <div class="text-muted small">
                                <?php
        if (!empty($file['variant'])) {
            $variants = json_decode($file['variant'], true);

            if (is_array($variants)) {
                foreach ($variants as $type => $data) {

                    // { "Size": { "value": "A4" } }
                    if (is_array($data) && isset($data['value'])) {
                        echo "<div>{$type}: " . htmlspecialchars($data['value']) . "</div>";

                    // { "Size": "A4" }
                    } elseif (is_string($data)) {
                        echo "<div>{$type}: " . htmlspecialchars($data) . "</div>";
                    }
                }
            }
        }
    ?>
                            </div>


                            <div class="text-muted small">
                                Qty: <?= (int)$file['quantity'] ?>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="text-end">
                            <div class="fw-semibold text-danger">
                                ₱<?= number_format($file['subtotal'], 2) ?>
                            </div>
                            <small class="text-muted">
                                ₱<?= number_format($file['price'], 2) ?> each
                            </small>
                        </div>

                    </div>
                </li>
                <?php endforeach; ?>
                <?php else: ?>
                <li class="list-group-item text-muted text-center py-4">
                    No order items found
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-4"><i class="bi bi-truck"></i> Order Status</h6>

            <div class="tracker-wrapper position-relative">

                <div class="tracker-line bg-light">
                    <div id="trackerProgress" class="tracker-line-progress"></div>
                </div>

                <div id="trackerSteps" class="d-flex justify-content-between text-center position-relative">
                    <div class="text-muted small">Loading status...</div>
                </div>

            </div>
        </div>
    </div>

    <div class="card shadow-sm rounded-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history"></i> Order Timeline</h6>
            <ul id="orderTimeline" class="timeline">
                <li class="text-muted">Loading timeline...</li>
            </ul>
        </div>
    </div>
</div>

</div>
<script>
document.addEventListener("DOMContentLoaded", () => {
    loadOrderStatus();
    setInterval(loadOrderStatus, 10000); // auto refresh
});

function loadOrderStatus() {
    fetch("ajax/fetch_order_status.php?order_id=<?= (int)$request['id'] ?>")
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;

            renderTracker(data);
            renderTimeline(data.logs);
        });
}

/* ================= TRACKER ================= */

function renderTracker(data) {
    const stepsEl = document.getElementById("trackerSteps");
    const progressEl = document.getElementById("trackerProgress");

    stepsEl.innerHTML = "";
    progressEl.style.width = data.progress + "%";
    progressEl.style.background = "var(--brand-gradient)";

    const keys = Object.keys(data.steps);

    keys.forEach((key, index) => {
        const step = data.steps[key];

        const active =
            data.currentStatus === "Cancelled" ?
            key === "Cancelled" :
            index <= data.currentIndex;

        const completed = index < data.currentIndex;

        stepsEl.innerHTML += `
            <div class="flex-fill tracker-step">
                <div class="tracker-circle ${active ? "active" : ""}">
                    ${completed ? "✔" : `<i class="bi ${step.icon}"></i>`}
                </div>
                <small class="${active ? "fw-bold" : "text-muted"}">
                    ${step.label}
                </small>
            </div>
        `;
    });
}

/* ================= TIMELINE ================= */

function renderTimeline(logs) {
    const timeline = document.getElementById("orderTimeline");
    timeline.innerHTML = "";

    if (!logs.length) {
        timeline.innerHTML = `
            <li class="text-muted text-center py-3">
                No status updates yet.
            </li>`;
        return;
    }

    let lastStatus = null;

    logs.forEach(log => {
        const isNewStatus = log.status !== lastStatus;
        const date = new Date(log.created_at);

        timeline.innerHTML += `
            <li class="timeline-item active">
                <div class="timeline-time">
                    <div>${date.toLocaleDateString()}</div>
                    <div>${date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                </div>

                <div class="timeline-dot">
                    <span class="dot"></span>
                </div>

                <div class="timeline-content">
                    ${isNewStatus ? `<strong>${escapeHtml(log.status)}</strong>` : ""}
                    ${log.remarks ? `<div class="text-muted small mt-1">${escapeHtml(log.remarks)}</div>` : ""}
                </div>
            </li>
        `;

        lastStatus = log.status;
    });
}

/* ================= SECURITY ================= */

function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}
</script>


<!-- STYLES -->
<style>
.tracker-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 6px;
    color: white;
}

.tracker-circle.active {
    background: var(--brand-gradient);
}

.timeline {
    list-style: none;
    padding-left: 0;
}

.timeline li {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
}

.dot {
    width: 12px;
    height: 12px;
    background: #adb5bd;
    border-radius: 50%;
    margin-top: 6px;
}

.dot.active {
    background: var(--brand-gradient);
}

/* ===== SHOPEE STYLE TRACKER ===== */
.tracker-wrapper {
    position: relative;
    padding-top: 20px;
}

.tracker-line {
    position: absolute;
    top: 35px;
    left: 100px;
    right: 100px;
    height: 4px;
    border-radius: 2px;
    overflow: hidden;
}

.tracker-line-progress {
    height: 100%;
    transition: width 0.4s ease;
}

.tracker-circle {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 6px;
    z-index: 1;
}

.tracker-circle.active {
    background: var(--brand-gradient);
    color: #fff;
}

.timeline {
    list-style: none;
    padding: 0;
    margin: 0;
}

.timeline-item {
    display: grid;
    grid-template-columns: 90px 30px 1fr;
    column-gap: 10px;
    position: relative;
    padding-bottom: 24px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

/* TIME (LEFT) */
.timeline-time {
    font-size: 0.8rem;
    color: #6c757d;
    text-align: right;
    line-height: 1.3;
}

/* DOT + LINE (CENTER) */
.timeline-dot {
    position: relative;
    display: flex;
    justify-content: center;
}

.timeline-dot::before {
    content: "";
    position: absolute;
    top: 10px;
    width: 2px;
    height: 250%;
    background: #dee2e6;
}

.timeline-item:last-child .timeline-dot::before {
    display: none;
}

.dot {
    width: 12px;
    height: 12px;
    background: var(--brand-gradient);
    border-radius: 50%;
    margin-top: 2px;
    z-index: 1;
}

/* CONTENT (RIGHT) */
.timeline-content {
    padding-bottom: 6px;
}

.timeline-content strong {
    display: block;
    font-size: 0.95rem;
}

/* ===============================
   MOBILE FIX: ORDER STATUS TRACKER
================================ */
@media (max-width: 768px) {

    .tracker-wrapper {
        padding-top: 0;
    }

    /* Hide horizontal line */
    .tracker-line {
        display: none;
    }

    /* Stack steps vertically */
    #trackerSteps {
        display: flex;
        flex-direction: column;
        gap: 16px;
        position: relative;
        padding-left: 20px;
    }

    /* Vertical line */
    #trackerSteps::before {
        content: "";
        position: absolute;
        left: 35px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #dee2e6;
        border-radius: 2px;
    }

    .tracker-step {
        display: flex;
        align-items: center;
        gap: 12px;
        text-align: left;
        position: relative;
    }

    .tracker-circle {
        width: 32px;
        height: 32px;
        margin: 0;
        z-index: 1;
        flex-shrink: 0;
    }

    .tracker-step small {
        font-size: 0.9rem;
    }

    .tracker-circle.active {
        background: var(--brand-gradient);
    }
}
</style>

<?php include 'includes/footer.php'; ?>