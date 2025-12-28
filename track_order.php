<?php
session_start();
include 'database/db.php';
include 'includes/header.php';

$request_no = $_GET['request_no'] ?? '';

if (!$request_no) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Invalid request.</div></div>";
    include '../includes/footer.php';
    exit;
}

/* ===============================
   FETCH PRINT REQUEST
================================ */
$stmt = $conn->prepare("
    SELECT *
    FROM print_requests
    WHERE request_no = ? ORDER BY created_at DESC
");
$stmt->bind_param("s", $request_no);
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
    FROM print_request_files
    WHERE request_id = ?
");
$fileStmt->bind_param("i", $request['id']);
$fileStmt->execute();
$files = $fileStmt->get_result()->fetch_all(MYSQLI_ASSOC);


/* ===============================
   FETCH STATUS LOGS
================================ */
$logStmt = $conn->prepare("
    SELECT status, remark, created_at
    FROM print_request_logs
    WHERE request_id = ?
    ORDER BY created_at DESC
");
$logStmt->bind_param("i", $request['id']);
$logStmt->execute();
$logs = $logStmt->get_result()->fetch_all(MYSQLI_ASSOC);


?>

<div class="container py-5">
    <h4 class="fw-bold mb-4"><i class="bi bi-box-seam"></i> Track Print Order</h4>

    <!-- ORDER INFO -->
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <div class="row align-items-end">

                <!-- REQUEST NO -->
                <div class="col-md-4 mb-3">
                    <div class="text-uppercase text-muted small fw-semibold">
                        Print Request No
                    </div>
                    <div class="fs-5 fw-bold">
                        <?= htmlspecialchars($request['request_no']) ?>
                    </div>
                </div>

                <!-- ORDER DATE -->
                <div class="col-md-4 mb-3">
                    <div class="text-uppercase text-muted small fw-semibold">
                        Order Date
                    </div>
                    <div class="fs-5">
                        <?= date('M d, Y · h:i A', strtotime($request['created_at'])) ?>
                    </div>
                </div>

                <!-- PRICE -->
                <div class="col-md-4 mb-3">
                    <div class="text-uppercase text-muted small fw-semibold">
                        Price
                    </div>

                    <?php if (!empty($request['price']) && $request['price'] > 0): ?>
                    <div class="fs-4 fw-bold text-success">
                        ₱<?= number_format($request['price'], 2) ?>
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
                Paid via <?= strtoupper(str_replace('_', ' ', htmlspecialchars($request['payment_method'] ?? ''))) ?>

            </span>
            <?php endif; ?>

            <?php if ($request['status'] === 'Pending Payment Verification'): ?>
            <hr>

            <div>
                <div class="fw-semibold mb-2">💳 Select Payment Method</div>

                <form id="paymentForm" class="d-flex flex-wrap gap-3">

                    <!-- COD -->
                    <label class="border rounded-3 p-3 d-flex align-items-center gap-2 cursor-pointer">
                        <input type="radio" name="payment_method" value="COD">
                        <span class="fw-semibold">Cash on Delivery</span>
                    </label>

                    <!-- GCASH -->
                    <label class="border rounded-3 p-3 d-flex align-items-center gap-2 cursor-pointer">
                        <input type="radio" name="payment_method" value="GCash">
                        <span class="fw-semibold text-primary">GCash</span>
                    </label>

                    <!-- PAYMAYA -->
                    <label class="border rounded-3 p-3 d-flex align-items-center gap-2 cursor-pointer">
                        <input type="radio" name="payment_method" value="PayMaya">
                        <span class="fw-semibold text-success">PayMaya</span>
                    </label>

                    <input type="hidden" id="requestId" value="<?= (int)$request['id'] ?>">
                </form>

                <button class="btn btn-primary mt-3" onclick="submitPayment()">
                    Confirm Payment Method
                </button>

                <small class="text-muted d-block mt-2">
                    Payment is required before printing starts.
                </small>
            </div>
            <?php endif; ?>

        </div>
    </div>
    <div class="modal fade" id="qrModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrTitle">Scan to Pay</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    <img id="qrImage" src="" class="img-fluid mb-3" style="max-width:220px;">
                    <p class="fw-semibold">Amount: ₱<?= number_format($request['price'], 2) ?></p>

                    <small class="text-muted">
                        After payment, please wait for admin confirmation.
                    </small>
                </div>
            </div>
        </div>
    </div>
    <script>
    function submitPayment() {
        const method = document.querySelector('input[name="payment_method"]:checked');
        const requestId = document.getElementById('requestId').value;

        if (!method) {
            Swal.fire('Select payment method', '', 'warning');
            return;
        }

        fetch('save_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    request_id: requestId,
                    payment_method: method.value
                })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    Swal.fire('Error', data.message, 'error');
                    return;
                }

                if (method.value === 'COD') {
                    Swal.fire('Saved', 'Cash on Delivery selected', 'success')
                        .then(() => location.reload());
                } else {
                    showQR(method.value);
                }
            });
    }

    function showQR(method) {
        const qrImage = document.getElementById('qrImage');
        const qrTitle = document.getElementById('qrTitle');

        if (method === 'GCash') {
            qrTitle.innerText = 'GCash Payment';
            qrImage.src = 'assets/Gcash.jpg'; // <-- YOUR QR
        } else {
            qrTitle.innerText = 'PayMaya Payment';
            qrImage.src = 'assets/Paymaya.jpg'; // <-- YOUR QR
        }

        new bootstrap.Modal(document.getElementById('qrModal')).show();
    }
    </script>


    <!-- FILE LIST -->
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark"></i> Files</h6>
            <ul class="list-group list-group-flush">
                <?php if ($files): foreach ($files as $file): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span><?= htmlspecialchars($file['file_name']) ?></span>
                    <small class="text-muted">
                        <?= round($file['file_size'] / 1024, 1) ?> KB
                    </small>
                </li>
                <?php endforeach; else: ?>
                <li class="list-group-item text-muted">No files uploaded</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-4"><i class="bi bi-truck"></i> Order Status</h6>

            <div class="tracker-wrapper position-relative">

                <div class="tracker-line bg-light">
                    <div class="tracker-line-progress" id="trackerProgress"></div>
                </div>

                <div class="d-flex justify-content-between text-center position-relative" id="trackerSteps">
                </div>

            </div>
        </div>
    </div>

    <!-- TIMELINE -->
    <div class="card shadow-sm rounded-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history"></i> Order Timeline</h6>
            <ul class="timeline" id="timeline"></ul>

        </div>
    </div>

</div>

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



<script>
document.addEventListener("DOMContentLoaded", () => {
    loadOrderStatus();
});

function loadOrderStatus() {
    fetch("print/fetch_order_status.php?request_id=<?= (int)$request['id'] ?>")
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;

            const stepsEl = document.getElementById("trackerSteps");
            const progressEl = document.getElementById("trackerProgress");

            stepsEl.innerHTML = "";
            progressEl.style.width = data.progress + "%";
            progressEl.style.background = "var(--brand-gradient)";

            const stepKeys = Object.keys(data.steps);

            stepKeys.forEach((key, index) => {
                const active =
                    data.currentStatus === "Cancelled" ?
                    key === "Cancelled" :
                    data.currentIndex !== false && index <= data.currentIndex;

                stepsEl.innerHTML += `
                    <div class="flex-fill tracker-step">
                        <div class="tracker-circle ${active ? "active" : ""}">
                            ${active ? "✔" : ""}
                        </div>
                        <small class="${active ? "fw-bold" : "text-muted"}">
                            ${data.steps[key]}
                        </small>
                    </div>
                `;
            });
        });
}


document.addEventListener("DOMContentLoaded", () => {
    loadTimeline();
});

function loadTimeline() {
    fetch("print/fetch_timeline.php?request_id=<?= (int)$request['id'] ?>")
        .then(res => res.json())
        .then(logs => {
            const timeline = document.getElementById("timeline");
            timeline.innerHTML = "";

            if (!logs.length) {
                timeline.innerHTML = `
                    <li class="timeline-item">
                        <div class="timeline-content text-muted">
                            No status updates yet.
                        </div>
                    </li>`;
                return;
            }

            logs.forEach(log => {
                const date = new Date(log.created_at);

                timeline.innerHTML += `
                    <li class="timeline-item active">
                        <div class="timeline-time">
                            <div>${date.toLocaleDateString()}</div>
                            <div>${date.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}</div>
                        </div>

                        <div class="timeline-dot">
                            <span class="dot"></span>
                        </div>

                        <div class="timeline-content">
                            <strong>${escapeHtml(log.status)}</strong>
                            ${log.remark ? `<div class="text-muted small mt-1">${escapeHtml(log.remark)}</div>` : ""}
                        </div>
                    </li>
                `;
            });
        });
}

function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

setInterval(() => {
    loadOrderStatus();
    loadTimeline();
}, 10000);
</script>

<?php include 'includes/footer.php'; ?>