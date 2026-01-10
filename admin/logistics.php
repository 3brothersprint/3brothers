<?php include 'includes/header.php'; ?>
<?php include 'database/db.php'; ?>

<main class="col-md-9 col-lg-10 px-3 py-4">


    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">📦 Logistics – Assign Tracking</h4>

        <div class="d-flex gap-2">
            <a href="scan_barcode.php" class="btn btn-primary">
                <i class="bi bi-upc-scan"></i>
                Logistics Scanning
            </a>
        </div>
    </div>
    <!-- TABS -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#printTab">
                🖨 Print Requests
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#orderTab">
                🧾 Orders
            </button>
        </li>
    </ul>

    <div class="tab-content">

        <!-- ================= PRINT REQUESTS ================= -->
        <div class="tab-pane fade show active" id="printTab">

            <div class="card shadow-sm">
                <div class="card-body">

                    <form action="logistics/assign_bulk_tracking.php" method="POST">

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th><input type="checkbox" id="checkAllPrint"></th>
                                        <th>Request ID</th>
                                        <th>Customer</th>
                                        <th>Courier</th>
                                        <th>Tracking No.</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                        $q = "
                                        SELECT pr.id, u.full_name
                                        FROM print_requests pr
                                        JOIN users u ON u.id = pr.user_id
                                        WHERE pr.status='Approved'
                                        AND pr.id NOT IN (SELECT order_id FROM logistics_shipments)
                                        ";
                                        $r = $conn->query($q);
                                        while($row = $r->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="order_ids[]" value="<?= $row['id'] ?>"
                                                class="print-check">
                                        </td>
                                        <td>#<?= $row['id'] ?></td>
                                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                                        <td>
                                            <select name="courier[<?= $row['id'] ?>]"
                                                class="form-select form-select-sm">
                                                <option>J&T</option>
                                                <option>LBC</option>
                                                <option>NinjaVan</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="tracking[<?= $row['id'] ?>]"
                                                class="form-control form-control-sm tracking-input" readonly>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>

                                </tbody>
                            </table>
                        </div>

                        <button class="btn btn-primary w-100 mt-3">
                            Assign Tracking (Print Requests)
                        </button>

                    </form>
                </div>
            </div>
        </div>

        <!-- ================= ORDERS ================= -->
        <div class="tab-pane fade" id="orderTab">

            <div class="card shadow-sm">
                <div class="card-body">

                    <form action="logistics/assign_bulk_tracking.php" method="POST">

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th><input type="checkbox" id="checkAllOrders"></th>
                                        <th>Order No.</th>
                                        <th>Customer</th>
                                        <th>Courier</th>
                                        <th>Tracking No.</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                        $q = "
                                        SELECT o.*, u.full_name
                                        FROM orders o
                                        JOIN users u ON u.id = o.user_id
                                        WHERE o.status='To Ship'
                                        AND o.id NOT IN (SELECT order_id FROM logistics_shipments)
                                        ";
                                        $r = $conn->query($q);
                                        while($row = $r->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="order_ids[]" value="<?= $row['id'] ?>"
                                                class="order-check">
                                        </td>
                                        <td><?= htmlspecialchars($row['order_no']) ?></td>
                                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                                        <td>
                                            <select name="courier[<?= $row['id'] ?>]"
                                                class="form-select form-select-sm">
                                                <option>J&T</option>
                                                <option>LBC</option>
                                                <option>NinjaVan</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="tracking[<?= $row['id'] ?>]"
                                                class="form-control form-control-sm tracking-input" readonly>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>

                                </tbody>
                            </table>
                        </div>

                        <button class="btn btn-success w-100 mt-3">
                            Assign Tracking (Orders)
                        </button>

                    </form>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- ================= SCRIPTS ================= -->
<script>
function generateTracking() {
    let n = '';
    for (let i = 0; i < 15; i++) n += Math.floor(Math.random() * 10);
    return n;
}
document.querySelectorAll('.tracking-input').forEach(i => i.value = generateTracking());

document.getElementById('checkAllPrint').onclick = e =>
    document.querySelectorAll('.print-check').forEach(c => c.checked = e.target.checked);

document.getElementById('checkAllOrders').onclick = e =>
    document.querySelectorAll('.order-check').forEach(c => c.checked = e.target.checked);
</script>

<?php include 'includes/footer.php'; ?>