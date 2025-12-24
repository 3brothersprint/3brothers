<?php
session_start();
include 'includes/header.php';
include 'database/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* FETCH ADDRESSES */
$stmt = $conn->prepare("
    SELECT * FROM user_addresses
    WHERE user_id = ?
    ORDER BY is_default DESC, id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$addresses = $stmt->get_result();
?>

<section class="py-5">
    <div class="container">
        <div class="row g-4">

            <!-- LEFT MENU -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="list-group list-group-flush profile-menu">
                        <a class="list-group-item" href="profile.php">
                            <i class="bi bi-person"></i> Profile
                        </a>
                        <a class="list-group-item" href="orders.php">
                            <i class="bi bi-receipt"></i> My Orders
                        </a>
                        <a class="list-group-item active" style="background: var(--brand-gradient); color: white;"
                            href="addresses.php">
                            <i class="bi bi-geo-alt"></i> Addresses
                        </a>

                        <a class="list-group-item text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-semibold mb-0">My Addresses</h5>
                            <button class="btn btn-sm" style="background: var(--brand-gradient); color: white;"
                                data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                + Add Address
                            </button>
                        </div>

                        <?php if ($addresses->num_rows == 0): ?>
                        <div class="alert alert-info">No saved addresses yet.</div>
                        <?php endif; ?>

                        <div class="row g-3">
                            <?php while ($row = $addresses->fetch_assoc()): ?>
                            <div class="col-md-6">
                                <div class="border rounded-4 p-3 h-100">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= htmlspecialchars($row['label']) ?></strong>
                                        <?php if ($row['is_default']): ?>
                                        <span class="badge"
                                            style="background: var(--brand-gradient); color: white;">Default</span>
                                        <?php endif; ?>
                                    </div>

                                    <p class="mb-1"><?= htmlspecialchars($row['full_name']) ?></p>
                                    <p class="mb-1"><?= htmlspecialchars($row['phone']) ?></p>
                                    <p class="text-muted small mb-2">
                                        <?= htmlspecialchars($row['address']) ?>,
                                        <?= htmlspecialchars($row['city_name']) ?>,
                                        <?= htmlspecialchars($row['province_name']) ?>,
                                        <?= htmlspecialchars($row['zip_code']) ?>
                                    </p>

                                    <div class="d-flex gap-2">
                                        <?php if (!$row['is_default']): ?>
                                        <button class="btn btn-sm"
                                            style="background: var(--brand-gradient); color: white;"
                                            onclick="setDefaultAddress(<?= $row['id'] ?>)">
                                            Set Default
                                        </button>
                                        <?php endif; ?>

                                        <a href="address/delete.php?id=<?= $row['id'] ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Delete this address?')">
                                            Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ADD ADDRESS MODAL -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content border-0 rounded-4 shadow" method="POST" action="address/add.php">

            <!-- HEADER -->
            <div class="modal-header text-white rounded-top-4" style="background: var(--brand-gradient); color: white;">
                <h5 class="modal-title fw-semibold">
                    <i class="bi bi-geo-alt-fill me-2"></i> Add New Address
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-4">

                <!-- BASIC INFO -->
                <h6 class="fw-semibold mb-3 text-primary">Contact Information</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label small">Label</label>
                        <input type="text" name="label" class="form-control rounded-3" placeholder="Home, Office"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Full Name</label>
                        <input type="text" name="full_name" class="form-control rounded-3" placeholder="Juan Dela Cruz"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Phone Number</label>
                        <input type="text" name="phone" class="form-control rounded-3" placeholder="09xxxxxxxxx"
                            required>
                    </div>
                </div>

                <!-- LOCATION -->
                <h6 class="fw-semibold mb-3 text-primary">Location Details</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small">Region</label>
                        <select id="region" name="region_code" class="form-select rounded-3">
                            <option value="">Select Region</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small">Province</label>
                        <select id="province" name="province_code" class="form-select rounded-3" disabled>
                            <option value="">Select Province</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small">City / Municipality</label>
                        <select id="city" name="city_code" class="form-select rounded-3" disabled>
                            <option value="">Select City / Municipality</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small">Barangay</label>
                        <select id="barangay" name="barangay_code" class="form-select rounded-3" disabled>
                            <option value="">Select Barangay</option>
                        </select>
                    </div>
                </div>

                <!-- ADDRESS -->
                <h6 class="fw-semibold mb-3 text-primary">Complete Address</h6>
                <div class="mb-3">
                    <label class="form-label small">Street / House No.</label>
                    <textarea name="address" rows="2" class="form-control rounded-3"
                        placeholder="House No., Street Name" required></textarea>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-6">
                        <label class="form-label small">ZIP Code</label>
                        <input type="text" id="zip" name="zip_code" class="form-control rounded-3 bg-light" readonly>
                    </div>

                    <div class="col-md-6 mt-4 mt-md-0">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_default" value="1"
                                id="defaultAddress">
                            <label class="form-check-label small" for="defaultAddress">
                                Set as default address
                            </label>
                        </div>
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer bg-light rounded-bottom-4">
                <button class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button class="btn rounded-3 px-4" style="background: var(--brand-gradient); color: white;"
                    type="submit">
                    <i class="bi bi-check-circle me-1"></i> Save Address
                </button>
            </div>

            <!-- HIDDEN NAMES -->
            <input type="hidden" name="region_name" id="region_name">
            <input type="hidden" name="province_name" id="province_name">
            <input type="hidden" name="city_name" id="city_name">
            <input type="hidden" name="barangay_name" id="barangay_name">

        </form>
    </div>
</div>

<script>
function setName(selectEl, hiddenInputId) {
    const opt = selectEl.options[selectEl.selectedIndex];
    document.getElementById(hiddenInputId).value = opt ? opt.text : "";
}

region.addEventListener("change", () => setName(region, "region_name"));
province.addEventListener("change", () => setName(province, "province_name"));
city.addEventListener("change", () => setName(city, "city_name"));
barangay.addEventListener("change", () => setName(barangay, "barangay_name"));
</script>
<script>
function setDefaultAddress(id) {
    fetch("address/set-default.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `id=${id}`
        })
        .then(res => res.text())
        .then(res => {
            if (res === "success") {
                location.reload();
            } else {
                alert("Failed to set default address");
            }
        })
        .catch(() => alert("Server error"));
}
</script>

<script>
const region = document.getElementById("region");
const province = document.getElementById("province");
const city = document.getElementById("city");
const barangay = document.getElementById("barangay");
const zip = document.getElementById("zip");

fetch("https://psgc.gitlab.io/api/regions/")
    .then(res => res.json())
    .then(data => {
        const region = document.getElementById("region");
        data.forEach(r => {
            region.innerHTML += `<option value="${r.code}">${r.name}</option>`;
        });
    });
/* REGION → PROVINCE */
region.addEventListener("change", () => {
    province.innerHTML = `<option value="">Loading provinces...</option>`;
    province.disabled = true;

    if (!region.value) return;

    fetch(`https://psgc.gitlab.io/api/regions/${region.value}/provinces/`)
        .then(res => res.json())
        .then(data => {
            province.innerHTML = `<option value="">Select Province</option>`;
            data.forEach(p => {
                province.innerHTML += `<option value="${p.code}">${p.name}</option>`;
            });
            province.disabled = false;
        });
});
/* PROVINCE → CITY */
province.addEventListener("change", () => {
    city.innerHTML = `<option value="">Loading cities...</option>`;
    city.disabled = true;

    if (!province.value) return;

    fetch(`https://psgc.gitlab.io/api/provinces/${province.value}/cities-municipalities/`)
        .then(res => res.json())
        .then(data => {
            city.innerHTML = `<option value="">Select City / Municipality</option>`;
            data.forEach(c => {
                city.innerHTML += `<option value="${c.code}">${c.name}</option>`;
            });
            city.disabled = false;
        });
});

/* CITY → BARANGAY */
city.addEventListener("change", () => {
    barangay.innerHTML = `<option value="">Loading barangays...</option>`;
    barangay.disabled = true;

    if (!city.value) return;

    fetch(`https://psgc.gitlab.io/api/cities-municipalities/${city.value}/barangays/`)
        .then(res => res.json())
        .then(data => {
            barangay.innerHTML = `<option value="">Select Barangay</option>`;
            data.forEach(b => {
                barangay.innerHTML += `<option value="${b.code}">${b.name}</option>`;
            });
            barangay.disabled = false;
        });
});

/* BARANGAY → ZIP */
barangay.addEventListener("change", () => {
    const opt = barangay.options[barangay.selectedIndex];
    zip.value = opt?.dataset.zip || "";
});
</script>

<?php include 'includes/footer.php'; ?>