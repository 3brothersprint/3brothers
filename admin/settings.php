<?php include 'includes/header.php';
include 'database/db.php'; ?>
<main class="col-md-9 col-lg-10 px-4 py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Settings</h3>
    </div>

    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h5 class="card-title mb-3">📢 Announcements List</h5>

                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Message</th>
                                <th>Display On</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                    $announcements = $conn->query("SELECT * FROM announcements ORDER BY id DESC");
                    while ($row = $announcements->fetch_assoc()):
                        $locations = [];
                        if ($row['show_all']) $locations[] = 'All';
                        else {
                            if ($row['show_home']) $locations[] = 'Home';
                            if ($row['show_product']) $locations[] = 'Product';
                            if ($row['show_checkout']) $locations[] = 'Checkout';
                            if ($row['show_order']) $locations[] = 'Product Order Details';
                        }
                    ?>
                            <tr>
                                <td><?= htmlspecialchars($row['message']) ?></td>
                                <td><?= implode(', ', $locations) ?></td>
                                <td>
                                    <span class="badge <?= $row['is_enabled'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $row['is_enabled'] ? 'Enabled' : 'Disabled' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editAnnouncementModal" data-id="<?= $row['id'] ?>"
                                        data-message="<?= htmlspecialchars($row['message']) ?>"
                                        data-home="<?= $row['show_home'] ?>" data-product="<?= $row['show_product'] ?>"
                                        data-checkout="<?= $row['show_checkout'] ?>" data-all="<?= $row['show_all'] ?>"
                                        data-enabled="<?= $row['is_enabled'] ?>">
                                        Edit
                                    </button>

                                    <a href="settings/delete_setting.php?type=announcement&id=<?= $row['id'] ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Delete this announcement?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h5 class="card-title mb-3">⚡ Flash Sales</h5>

                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                    $sales = $conn->query("SELECT * FROM flash_sales ORDER BY id DESC");
                    while ($row = $sales->fetch_assoc()):
                    ?>
                            <tr>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= date("M d, Y h:i A", strtotime($row['start_datetime'])) ?></td>
                                <td><?= date("M d, Y h:i A", strtotime($row['end_datetime'])) ?></td>
                                <td>
                                    <span class="badge <?= $row['is_enabled'] ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $row['is_enabled'] ? 'Active' : 'Disabled' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editFlashModal" data-id="<?= $row['id'] ?>"
                                        data-title="<?= htmlspecialchars($row['title']) ?>"
                                        data-start="<?= $row['start_datetime'] ?>"
                                        data-end="<?= $row['end_datetime'] ?>" data-enabled="<?= $row['is_enabled'] ?>">
                                        Edit
                                    </button>

                                    <a href="settings/delete_setting.php?type=flash_sale&id=<?= $row['id'] ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Delete flash sale?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        <!-- ANNOUNCEMENT SETTINGS -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">📢 Announcement</h5>
                    <p class="text-muted small">
                        Create announcements and choose where they appear.
                    </p>

                    <form method="POST" action="settings/save_settings.php">

                        <input type="hidden" name="type" value="announcement">

                        <div class="mb-3">
                            <label class="form-label">Announcement Message</label>
                            <textarea class="form-control" name="message" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Display On</label>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_home" id="showHome"
                                    value="1">
                                <label class="form-check-label">Homepage</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_product" id="showProduct"
                                    value="1">
                                <label class="form-check-label">Product Details Page</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_checkout" id="showCheckout"
                                    value="1">
                                <label class="form-check-label">Checkout Page</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_order" id="showOrder"
                                    value="1">
                                <label class="form-check-label">Product Order Page</label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_all" id="showAll" value="1">
                                <label class="form-check-label">All Pages</label>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_enabled" value="1" checked>
                            <label class="form-check-label">Enable Announcement</label>
                        </div>

                        <button class="btn btn-primary w-100">Save Announcement</button>
                    </form>

                </div>
            </div>
        </div>
        <script>
        document.getElementById("showAll").addEventListener("change", function() {
            const others = ["showHome", "showProduct", "showCheckout", "showOrder"];
            others.forEach(id => {
                document.getElementById(id).disabled = this.checked;
                if (this.checked) document.getElementById(id).checked = false;
            });
        });
        </script>


        <!-- FLASH SALE SETTINGS -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">⚡ Flash Sale</h5>
                    <p class="text-muted small">
                        Control flash sale banner, discount, & countdown timer.
                    </p>

                    <form method="POST" action="settings/save_settings.php">

                        <input type="hidden" name="type" value="flash_sale">

                        <!-- TITLE -->
                        <div class="mb-3">
                            <label class="form-label">Flash Sale Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <!-- DISCOUNT TYPE -->
                        <div class="mb-3">
                            <label class="form-label">Discount Type</label>
                            <select name="discount_type" class="form-select" required>
                                <option value="">Select discount type</option>
                                <option value="percent">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (₱)</option>
                            </select>
                        </div>

                        <!-- DISCOUNT VALUE -->
                        <div class="mb-3">
                            <label class="form-label">Discount Value</label>
                            <input type="number" name="discount_value" class="form-control" placeholder="e.g. 20 or 150"
                                min="1" required>
                            <small class="text-muted">
                                % for percentage or ₱ amount for fixed discount
                            </small>
                        </div>

                        <!-- START -->
                        <div class="mb-3">
                            <label class="form-label">Start Date & Time</label>
                            <input type="datetime-local" name="start_datetime" class="form-control" required>
                        </div>

                        <!-- END -->
                        <div class="mb-3">
                            <label class="form-label">End Date & Time</label>
                            <input type="datetime-local" name="end_datetime" class="form-control" required>
                        </div>

                        <!-- ENABLE -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_enabled" value="1">
                            <label class="form-check-label">
                                Enable Flash Sale
                            </label>
                        </div>

                        <button class="btn btn-danger w-100">
                            Save Flash Sale
                        </button>
                    </form>

                </div>
            </div>
        </div>



        <!-- PAYMENT METHOD SETTINGS -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">💳 Payment Methods</h5>
                    <p class="text-muted small">Add, enable, or remove payment options.</p>

                    <form id="paymentForm">
                        <div id="paymentList">

                            <!-- ITEM -->
                            <div class="d-flex align-items-center mb-2 payment-item">
                                <input type="text" class="form-control me-2" placeholder="Payment name (e.g. GCash)"
                                    required>
                                <input type="checkbox" class="form-check-input me-2" checked>
                                <button type="button" class="btn btn-outline-danger btn-sm remove-payment">
                                    ✕
                                </button>
                            </div>

                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addPaymentMethod()">
                            + Add Payment Method
                        </button>

                        <button class="btn btn-primary w-100 mt-3">
                            Save Payment Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <script>
        function addPaymentMethod() {
            const container = document.getElementById("paymentList");

            const div = document.createElement("div");
            div.className = "d-flex align-items-center mb-2 payment-item";

            div.innerHTML = `
        <input type="text" class="form-control me-2"
               placeholder="Payment name" required>
        <input type="checkbox" class="form-check-input me-2" checked>
        <button type="button" class="btn btn-outline-danger btn-sm remove-payment">
            ✕
        </button>
    `;

            container.appendChild(div);
        }

        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("remove-payment")) {
                e.target.closest(".payment-item").remove();
            }
        });
        </script>



        <!-- SHIPPING METHOD SETTINGS -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">🚚 Shipping Methods</h5>
                    <p class="text-muted small">Manage delivery methods and fees.</p>

                    <form id="shippingForm">
                        <div id="shippingList">

                            <!-- ITEM -->
                            <div class="row g-2 align-items-center mb-2 shipping-item">
                                <div class="col-5">
                                    <input type="text" class="form-control" placeholder="Method name (e.g. Express)">
                                </div>
                                <div class="col-4">
                                    <input type="number" class="form-control" placeholder="Fee (₱)">
                                </div>
                                <div class="col-2 text-center">
                                    <input type="checkbox" class="form-check-input" checked>
                                </div>
                                <div class="col-1 text-end">
                                    <button type="button"
                                        class="btn btn-outline-danger btn-sm remove-shipping">✕</button>
                                </div>
                            </div>

                        </div>

                        <button type="button" class="btn btn-outline-success btn-sm mt-2" onclick="addShippingMethod()">
                            + Add Shipping Method
                        </button>

                        <button class="btn btn-success w-100 mt-3">
                            Save Shipping Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <script>
        function addShippingMethod() {
            const container = document.getElementById("shippingList");

            const div = document.createElement("div");
            div.className = "row g-2 align-items-center mb-2 shipping-item";

            div.innerHTML = `
        <div class="col-5">
            <input type="text" class="form-control" placeholder="Method name">
        </div>
        <div class="col-4">
            <input type="number" class="form-control" placeholder="Fee (₱)">
        </div>
        <div class="col-2 text-center">
            <input type="checkbox" class="form-check-input" checked>
        </div>
        <div class="col-1 text-end">
            <button type="button"
                    class="btn btn-outline-danger btn-sm remove-shipping">✕</button>
        </div>
    `;

            container.appendChild(div);
        }

        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("remove-shipping")) {
                e.target.closest(".shipping-item").remove();
            }
        });
        </script>


        <!-- SYSTEM SETTINGS -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">⚙️ System Settings</h5>
                    <p class="text-muted small">
                        Control order processing behavior.
                    </p>

                    <form>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="autoApprove">
                            <label class="form-check-label" for="autoApprove">
                                Auto-approve print requests
                            </label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="requirePayment">
                            <label class="form-check-label" for="requirePayment">
                                Require payment before printing
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="notifyCustomer" checked>
                            <label class="form-check-label" for="notifyCustomer">
                                Notify customers on status update
                            </label>
                        </div>

                        <button class="btn btn-dark mt-2">
                            Save System Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h5 class="card-title mb-3">🔔 Notifications</h5>

                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Message</th>
                                <th>Recipient</th>
                                <th>Link</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                   $notifs = $conn->query("
                        SELECT 
                            n.id,
                            n.user_id,
                            n.title,
                            n.message,
                            n.link,
                            n.created_at,
                            u.full_name,
                            u.account_no
                        FROM notifications n
                        LEFT JOIN users u ON u.id = n.user_id
                        ORDER BY n.id DESC
                    ");

                    while ($n = $notifs->fetch_assoc()):
                    ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($n['title']) ?></td>
                                <td><?= htmlspecialchars($n['message']) ?></td>
                                <td>
                                    <?php if ($n['user_id']): ?>
                                    <span class="badge bg-primary">User: <?= htmlspecialchars($n['full_name']) ?></span>
                                    <?php else: ?>
                                    <span class="badge bg-success">All Users</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?= htmlspecialchars($n['title']) ?></div>
                                    <div class="text-muted small">
                                        <?= htmlspecialchars($n['message']) ?>
                                    </div>

                                    <?php if (!empty($n['link'])): ?>
                                    <a href="<?= htmlspecialchars($n['link']) ?>" class="small text-decoration-none">
                                        Open link →
                                    </a>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small">
                                    <?php if (!empty($n['created_at']) && $n['created_at'] !== '0000-00-00 00:00:00'): ?>
                                    <?= date("M d, Y h:i A", strtotime($n['created_at'])) ?>
                                    <?php else: ?>
                                    —
                                    <?php endif; ?>
                                </td>

                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h5 class="card-title">📨 Send Notification</h5>
                    <p class="text-muted small">
                        Send to all users or a specific customer.
                    </p>

                    <form method="POST" action="settings/send_notification.php">

                        <!-- RECIPIENT -->
                        <div class="mb-3">
                            <label class="form-label">Send To</label>
                            <select name="user_id" class="form-select">
                                <option value="">All Users</option>
                                <?php
                        $users = $conn->query("SELECT id, full_name FROM users ORDER BY full_name");
                        while ($u = $users->fetch_assoc()):
                        ?>
                                <option value="<?= $u['id'] ?>">
                                    <?= htmlspecialchars($u['full_name']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- TITLE -->
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <!-- MESSAGE -->
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" rows="4" class="form-control" required></textarea>
                        </div>
                        <!-- LINK -->
                        <div class="mb-3">
                            <label class="form-label">
                                Link (optional)
                                <small class="text-muted">(e.g. orders.php?id=123)</small>
                            </label>
                            <input type="text" name="link" class="form-control">
                        </div>

                        <button class="btn btn-primary w-100">
                            Send Notification
                        </button>

                    </form>

                </div>
            </div>
        </div>

    </div>

</main>
<div class="modal fade" id="editAnnouncementModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST" action="settings/update_setting.php" class="modal-content">

            <input type="hidden" name="type" value="announcement">
            <input type="hidden" name="id" id="editId">

            <div class="modal-header">
                <h5 class="modal-title">Edit Announcement</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea class="form-control" name="message" id="editMessage" rows="4"></textarea>
                </div>

                <label class="form-label">Display On</label>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="show_home" id="editHome" value="1">
                    <label class="form-check-label">Homepage</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="show_product" id="editProduct" value="1">
                    <label class="form-check-label">Product Page</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="show_checkout" id="editCheckout" value="1">
                    <label class="form-check-label">Checkout Page</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="show_all" id="editAll" value="1">
                    <label class="form-check-label">All Pages</label>
                </div>

                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="is_enabled" id="editEnabled" value="1">
                    <label class="form-check-label">Enabled</label>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">Update</button>
            </div>

        </form>
    </div>
</div>
<script>
document.getElementById('editAnnouncementModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;

    document.getElementById('editId').value = btn.dataset.id;
    document.getElementById('editMessage').value = btn.dataset.message;

    document.getElementById('editHome').checked = btn.dataset.home == 1;
    document.getElementById('editProduct').checked = btn.dataset.product == 1;
    document.getElementById('editCheckout').checked = btn.dataset.checkout == 1;
    document.getElementById('editAll').checked = btn.dataset.all == 1;
    document.getElementById('editEnabled').checked = btn.dataset.enabled == 1;
});
</script>
<div class="modal fade" id="editFlashModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST" action="settings/update_setting.php" class="modal-content">

            <input type="hidden" name="type" value="flash_sale">
            <input type="hidden" name="id" id="flashId">

            <div class="modal-header">
                <h5 class="modal-title">Edit Flash Sale</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" id="flashTitle" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Start</label>
                    <input type="datetime-local" name="start_datetime" id="flashStart" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">End</label>
                    <input type="datetime-local" name="end_datetime" id="flashEnd" class="form-control">
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_enabled" id="flashEnabled" value="1">
                    <label class="form-check-label">Enabled</label>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger">Update</button>
            </div>

        </form>
    </div>
</div>
<script>
document.getElementById('editFlashModal').addEventListener('show.bs.modal', e => {
    const b = e.relatedTarget;
    flashId.value = b.dataset.id;
    flashTitle.value = b.dataset.title;
    flashStart.value = b.dataset.start;
    flashEnd.value = b.dataset.end;
    flashEnabled.checked = b.dataset.enabled == 1;
});
</script>

<?php include 'includes/footer.php'; ?>