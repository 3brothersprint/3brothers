<?php
require "../database/db.php";

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "<div class='alert alert-danger'>Invalid request</div>";
    exit;
}

/* ===============================
   FETCH MAIN REQUEST
   =============================== */
$stmt = $conn->prepare("
    SELECT *
    FROM print_requests
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "<div class='alert alert-warning'>Request not found</div>";
    exit;
}

/* ===============================
   FETCH FILES
   =============================== */
$stmt = $conn->prepare("
    SELECT file_name, file_path, file_size
    FROM print_request_files
    WHERE request_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$files = $stmt->get_result();
$stmt->close();

$statuses = [
    'Pending Payment Verification',
    'Approved',
    'Printing',
    'Ready for Pickup',
    'Completed',
    'Rejected',
    'Cancelled'
];

?>

<div class="row g-3">
    <div class="col-md-6">
        <strong>Request No:</strong><br>
        <?= htmlspecialchars($order['request_no']) ?>
    </div>

    <div class="col-md-6">
        <strong>Customer Name:</strong><br>
        <?= htmlspecialchars($order['full_name']) ?>
    </div>

    <div class="col-md-6">
        <strong>Service:</strong><br>
        <?= htmlspecialchars($order['print_type']) ?>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Status</label>
        <select class="form-select" id="orderStatus">
            <?php foreach ($statuses as $status): ?>
            <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>>
                <?= $status ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>


    <div class="col-md-4">
        <strong>Paper Size:</strong><br>
        <?= htmlspecialchars($order['paper_size']) ?>
    </div>

    <div class="col-md-4">
        <strong>Copies:</strong><br>
        <?= (int)$order['copies'] ?>
    </div>

    <div class="col-md-4">
        <strong>Color:</strong><br>
        <?= htmlspecialchars($order['color']) ?>
    </div>

    <div class="col-12">
        <strong>Notes:</strong>
        <div class="border rounded p-2 mt-1 text-muted">
            <?= nl2br(htmlspecialchars($order['notes'] ?: '—')) ?>
        </div>
    </div>
    <hr>

    <div class="row g-3 mt-2">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Set Price (₱)</label>
            <input type="number" class="form-control" id="orderPrice" value="<?= $order['price'] ?? '' ?>" min="0"
                step="0.01">
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold">Admin Remark</label>
            <select class="form-select" id="orderRemark">
                <option value="">--Select Remark--</option>
                <option value="Pending payment for verification">Pending payment for verification</option>
                <option value="The payment has been approved and the file has been
                    downloaded.">The payment has been approved and the file has been
                    downloaded.</option>
                <option value="The file has been review and make an update for prices.">The file has been review and
                    make an update for prices.</option>
                <option value="The file has now printing.">The file has now printing.</option>
                <option value="The file has been printed and ready for delivery">The file has been printed and ready for
                    delivery</option>
                <option value="The file has been delivered">The file has been delivered</option>
            </select>
        </div>
        <?php if ($order['status'] === 'Pending Payment Verification'): ?>
        <hr>

        <div class="alert alert-info">
            💳 Payment Method:
            <strong><?= htmlspecialchars($order['payment_method']) ?></strong>
        </div>
        <?php endif; ?>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Admin Action</label>

            <select class="form-select" id="orderStatus" required>
                <option value="">-- Select Action --</option>

                <?php if ($order['status'] === 'Pending Payment Verification'): ?>
                <option value="Approved">Approve Payment</option>
                <option value="Rejected">Reject Payment</option>
                <?php else: ?>
                <?php foreach ($statuses as $status): ?>
                <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>>
                    <?= $status ?>
                </option>
                <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="col-md-6 d-flex align-items-end">
            <button class="btn btn-primary w-100" onclick="updateOrderStatus(<?= (int)$order['id'] ?>)">
                <i class="bi bi-arrow-repeat"></i> Update Status
            </button>
        </div>

    </div>

    <div class="col-12">
        <strong>Uploaded Files:</strong>

        <div class="d-flex justify-content-end mb-2">
            <?php if ($files->num_rows > 0): ?>
            <a href="print/download_zip.php?request_id=<?= (int)$order['id'] ?>"
                class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-archive"></i> Download All Files
            </a>
            <?php endif; ?>
        </div>

        <ul class="list-group">
            <?php if ($files->num_rows > 0): ?>
            <?php while ($f = $files->fetch_assoc()):
        $ext = strtolower(pathinfo($f['file_name'], PATHINFO_EXTENSION));
        $icon = match ($ext) {
            'pdf'  => 'bi-file-earmark-pdf text-danger',
            'doc', 'docx' => 'bi-file-earmark-word text-primary',
            'jpg', 'jpeg', 'png' => 'bi-file-earmark-image text-success',
            default => 'bi-file-earmark text-secondary'
        };
    ?>
            <li class="list-group-item d-flex align-items-center gap-2">
                <i class="bi <?= $icon ?> fs-4"></i>
                <div>
                    <div class="fw-semibold"><?= htmlspecialchars($f['file_name']) ?></div>
                    <small class="text-muted"><?= number_format($f['file_size'] / 1024, 1) ?> KB</small>
                </div>
            </li>
            <?php endwhile; ?>
            <?php else: ?>
            <li class="list-group-item text-muted">No files uploaded</li>
            <?php endif; ?>
        </ul>

    </div>

</div>