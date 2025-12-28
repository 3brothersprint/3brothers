<?php
session_start();
include 'includes/header.php';

$uid = null;

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    // user is logged in
}
?>

<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-4">

            <?php
$now = time();

$flashSale = $conn->query("
    SELECT *
    FROM flash_sales
    WHERE is_enabled = 1
      AND start_datetime <= NOW()
      AND end_datetime >= NOW()
    LIMIT 1
")->fetch_assoc();

$remainingSeconds = 0;

if ($flashSale) {
    $endTimestamp = strtotime($flashSale['end_datetime']);
    $remainingSeconds = max(0, $endTimestamp - $now);
}
$bannerCol = $flashSale ? 'col-lg-7' : 'col-lg-12';

?>
            <!-- LEFT : BANNER -->
            <div class="<?= $bannerCol ?>">
                <div class="hero-banner">
                    <img src="https://images.unsplash.com/photo-1581090700227-1e37b190418e" alt="Printing Services" />
                    <div class="hero-overlay"></div>
                    <div class="hero-text">
                        <h1>Professional Printing Solutions</h1>
                        <p class="lead">High-quality print services you can trust.</p>
                        <a href="#" class="btn btn-brand mt-3">Our Services</a>
                    </div>
                </div>
            </div>

            <!-- RIGHT : FLASH SALE -->
            <?php if ($flashSale): ?>
            <div class="col-lg-5">
                <div class="flash-sale-card">

                    <h4 class="flash-title">⚡ <?= htmlspecialchars($flashSale['title']) ?></h4>
                    <p class="flash-subtitle">Limited time discounts for <strong>PRODUCTS</strong> only</p>
                    <?php if ($flashSale['discount_type'] === 'percent'): ?>
                    <span class="badge bg-danger">
                        <?= $flashSale['discount_value'] ?>% OFF
                    </span>
                    <?php else: ?>
                    <span class="badge bg-danger">
                        ₱<?= number_format($flashSale['discount_value'], 2) ?> OFF
                    </span>
                    <?php endif; ?>

                    <div class="countdown">
                        <div class="time-box">
                            <span id="days">00</span>
                            <small>Days</small>
                        </div>
                        <div class="time-box">
                            <span id="hours">00</span>
                            <small>Hours</small>
                        </div>
                        <div class="time-box">
                            <span id="minutes">00</span>
                            <small>Minutes</small>
                        </div>
                        <div class="time-box">
                            <span id="seconds">00</span>
                            <small>Seconds</small>
                        </div>
                    </div>

                    <a href="shop.php" class="btn btn-danger w-100 mt-4">
                        Shop Flash Deals
                    </a>

                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<style>
.hero-section {
    padding: 60px 0;
    background: #f8f9fa;
}

/* LEFT BANNER */
.hero-banner {
    position: relative;
    border-radius: 18px;
    overflow: hidden;
    min-height: 420px;

}

.col-lg-12 .hero-text h1 {
    font-size: 3rem;
}

.hero-banner img {
    width: 100%;
    height: 420px;
    object-fit: cover;
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to right,
            rgba(0, 0, 0, 0.6),
            rgba(0, 0, 0, 0.15));
}

.hero-text {
    position: absolute;
    top: 50%;
    left: 40px;
    transform: translateY(-50%);
    color: #fff;
    max-width: 70%;
}

.hero-text h1 {
    font-weight: 700;
}

/* FLASH SALE CARD */
.flash-sale-card {
    background: var(--brand-gradient);
    border-radius: 18px;
    padding: 30px;
    text-align: center;
    color: #fff;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.18);
}

/* TITLE & SUBTITLE */
.flash-title {
    font-weight: 800;
    color: #fff;
    letter-spacing: 0.5px;
}

.flash-subtitle {
    color: rgba(255, 255, 255, 0.85);
    margin-bottom: 20px;
}

/* TIMER */
.countdown {
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

.time-box {
    background: rgba(0, 0, 0, 0.35);
    backdrop-filter: blur(6px);
    color: #fff;
    border-radius: 14px;
    padding: 16px 10px;
    flex: 1;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
}

.time-box span {
    display: block;
    font-size: 26px;
    font-weight: 800;
    line-height: 1;
}

.time-box small {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.75);
}

/* BUTTON */
.flash-sale-card .btn-danger {
    background: #fff;
    color: #dc3545;
    font-weight: 700;
    border: none;
}

.flash-sale-card .btn-danger:hover {
    background: #f8f9fa;
    color: #b02a37;
}
</style>
<script>
let remaining = <?= $remainingSeconds ?>;

if (remaining > 0) {
    const timer = setInterval(() => {

        if (remaining <= 0) {
            clearInterval(timer);
            return;
        }

        const days = Math.floor(remaining / (60 * 60 * 24));
        const hours = Math.floor((remaining % (60 * 60 * 24)) / (60 * 60));
        const minutes = Math.floor((remaining % (60 * 60)) / 60);
        const seconds = Math.floor(remaining % 60);

        document.getElementById("days").innerText = String(days).padStart(2, "0");
        document.getElementById("hours").innerText = String(hours).padStart(2, "0");
        document.getElementById("minutes").innerText = String(minutes).padStart(2, "0");
        document.getElementById("seconds").innerText = String(seconds).padStart(2, "0");

        remaining--;

    }, 1000);
}
</script>
<!-- Announcement Banner -->
<div class="container mt-3">

    <?php
$announcement = $conn->query("
    SELECT * FROM announcements
    WHERE is_enabled = 1 AND (show_home = 1 OR show_all = 1)
    LIMIT 1
")->fetch_assoc();

if ($announcement):
?>
    <div class="alert alert-primary alert-dismissible fade show
                d-flex align-items-start gap-3
                rounded-3 shadow-sm px-4 py-3" role="alert">

        <!-- ICON -->
        <div class="fs-5">📢</div>

        <!-- MESSAGE -->
        <div class="flex-grow-1">
            <strong class="d-block mb-1">Announcement</strong>
            <div class="small">
                <?= nl2br(htmlspecialchars($announcement['message'])) ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
<style>
.announcement-banner {
    background: var(--brand-gradient);
    color: #fff;
}

.announcement-banner .btn-close {
    filter: invert(1);
}
</style>


</div>

<!-- Categories -->
<section class="section-padding py-5 pt-3">
    <div class="container">
        <h2 class="section-title text-center mb-4">Categories</h2>

        <div class="row justify-content-center g-4">
            <div class="col-6 col-md-3">
                <a href="#" class="category-link" data-bs-toggle="modal" data-bs-target="#printingModal">
                    <div class="category-item text-center">
                        <div class="category-icon">
                            <i class="bi bi-printer"></i>
                        </div>
                        <h6 class="mt-3 mb-0">Printing</h6>
                    </div>
                </a>
            </div>

            <!-- Education -->
            <div class="col-6 col-md-3">
                <a href="education.html" class="category-link">
                    <div class="category-item text-center">
                        <div class="category-icon">
                            <i class="bi bi-book"></i>
                        </div>
                        <h6 class="mt-3 mb-0">Education</h6>
                    </div>
                </a>
            </div>

            <!-- Business -->
            <div class="col-6 col-md-3">
                <a href="business.html" class="category-link">
                    <div class="category-item text-center">
                        <div class="category-icon">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <h6 class="mt-3 mb-0">Business</h6>
                    </div>
                </a>
            </div>

            <!-- Supplies -->
            <div class="col-6 col-md-3">
                <a href="supplies.html" class="category-link">
                    <div class="category-item text-center">
                        <div class="category-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h6 class="mt-3 mb-0">Supplies</h6>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>


<!-- Printing Request Modal -->
<div class="modal fade" id="printingModal" tabindex="-1" aria-labelledby="printingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header text-white" style="background: var(--brand-gradient)">
                <h5 class="modal-title" id="printingModalLabel">
                    Printing Request Form
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="print/index.php" method="POST" enctype="multipart/form-data" id="printWizardForm">
                <div class="modal-body p-4">
                    <!-- Wizard Progress Steps -->
                    <div class="wizard-steps mb-4">
                        <div class="wizard-progress"></div>

                        <div class="step active">
                            <div class="step-circle">1</div>
                            <span>Service</span>
                        </div>
                        <div class="step">
                            <div class="step-circle">2</div>
                            <span>Details</span>
                        </div>
                        <div class="step">
                            <div class="step-circle">3</div>
                            <span>Upload</span>
                        </div>
                        <div class="step">
                            <div class="step-circle">4</div>
                            <span>Review</span>
                        </div>
                    </div>


                    <div class="wizard-step active">
                        <div class="wizard-step-inner">
                            <h5 class="mb-3">Step 1: Choose Printing Services</h5>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Printing Service</label>
                                <select class="form-select" name="print_type" id="printType" required>
                                    <option value="" disabled selected>
                                        Select service
                                    </option>
                                    <option value="Print">Print</option>
                                    <option value="Xerox">Xerox</option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="wizard-step" data-required-step="0">
                        <div class="wizard-step-inner">
                            <h5 class="mb-3">Step 2: Print Details</h5>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Paper Size</label>
                                    <select class="form-select" name="paper_size" id="paperSize" required>
                                        <option value="" disabled selected>
                                            Select Paper Size
                                        </option>
                                        <option>A4</option>
                                        <option>Letter (Short)</option>
                                        <option>Legal (8.5 x 13") (Long)</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Copies</label>
                                    <input type="number" class="form-control" name="copies" min="1" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Color</label>
                                    <select class="form-select" name="color">
                                        <option value="" disabled selected>
                                            Select Color
                                        </option>
                                        <option>Black & White</option>
                                        <option>Color</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wizard-step">
                        <div class="wizard-step-inner">
                            <h5 class="mb-3">Step 3: Upload File & Notes</h5>

                            <div class="upload-zone" id="uploadZone">
                                <input type="file" id="fileInput" name="files[]" class="file-input"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple required />

                                <div class="upload-content text-center">
                                    <div class="upload-icon">📤</div>
                                    <p class="mb-1 fw-semibold">Drag & drop your file here</p>
                                    <small class="text-muted">
                                        or click to browse (PDF, DOC, JPG, PNG)
                                    </small>
                                </div>
                            </div>

                            <div class="file-preview mt-3 d-none" id="filePreview"></div>
                            <textarea class="form-control mt-3" rows="3" placeholder="Additional instructions..."
                                name="notes"></textarea>
                        </div>
                    </div>
                    <?php
$defaultAddress = '—';

$region = $province = $city = $barangay = $street = '';

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];

    $stmt = $conn->prepare("
        SELECT 
            region_name,
            province_name,
            city_name,
            barangay_name,
            address
        FROM user_addresses
        WHERE user_id = ? AND is_default = 1
        LIMIT 1
    ");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $stmt->bind_result(
        $region,
        $province,
        $city,
        $barangay,
        $street
    );

    if ($stmt->fetch()) {
        $defaultAddress = trim("
            $street
            $barangay,
            $city
            $province
            $region
        ");
    }
}
?>


                    <div class="wizard-step">
                        <div class="wizard-step-inner">
                            <h5 class="mb-3">Review & Submit</h5>

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Service</span>
                                    <strong id="summaryService">—</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Paper Size</span>
                                    <strong id="summaryPaper">—</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Quantity</span>
                                    <strong id="summaryQty">—</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Color</span>
                                    <strong id="summaryColor">—</strong>
                                </li>
                                <li class="list-group-item">
                                    <span>Notes</span>
                                    <div class="text-muted mt-1" id="summaryNotes">—</div>
                                </li>
                                <li class="list-group-item">
                                    <span>Delivery Address</span>
                                    <div class="text-muted mt-1" id="summaryAddress">
                                        <?= nl2br(htmlspecialchars($defaultAddress)) ?>
                                    </div>
                                </li>

                            </ul>

                            <p class="text-muted mt-3">
                                Please confirm all details before submitting your request.
                            </p>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="delivery_address" value="<?= htmlspecialchars($defaultAddress) ?>">

                <!-- Footer Buttons -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="prevBtn">
                        Back
                    </button>
                    <button type="button" class="btn btn-brand" id="nextBtn">
                        Next
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php
include 'admin/database/db.php';
$flashSale = $conn->query("
    SELECT *
    FROM flash_sales
    WHERE is_enabled = 1
      AND start_datetime <= NOW()
      AND end_datetime >= NOW()
    LIMIT 1
")->fetch_assoc();

$products = $conn->query("
    SELECT 
        p.id,
        p.name,
        p.price,
        (
            SELECT image 
            FROM product_images 
            WHERE product_id = p.id 
            ORDER BY sort_order ASC 
            LIMIT 1
        ) AS image
    FROM products p
    WHERE p.status = 'Active'
    ORDER BY p.id DESC
    LIMIT 8
");

function getDiscountedPrice($price, $sale) {
    if (!$sale) return $price;

    if ($sale['discount_type'] === 'percent') {
        return $price - ($price * ($sale['discount_value'] / 100));
    }

    return max(0, $price - $sale['discount_value']);
}

?>

<!-- Products -->
<section class="section-padding py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Featured Products</h2>
            <a href="404.php" class="btn btn-sm btn-brand">View All</a>
        </div>

        <div class="row g-4">
            <?php while ($row = $products->fetch_assoc()): ?>

            <?php
            /* ===============================
               PRICE RANGE (VARIANTS)
            ================================ */
            $stmt = $conn->prepare("
                SELECT MIN(price) AS min_price, MAX(price) AS max_price
                FROM product_variants
                WHERE product_id = ?
            ");
            $stmt->bind_param("i", $row['id']);
            $stmt->execute();
            $priceRange = $stmt->get_result()->fetch_assoc();

            $minPrice = $priceRange['min_price'] ?? $row['price'];
            $maxPrice = $priceRange['max_price'] ?? $row['price'];

            $originalMin = $minPrice;
            $originalMax = $maxPrice;

            if ($flashSale) {
                $minPrice = getDiscountedPrice($minPrice, $flashSale);
                $maxPrice = getDiscountedPrice($maxPrice, $flashSale);
            }
            ?>

            <div class="col-6 col-md-3">
                <a href="product-details.php?id=<?= $row['id'] ?>" class="product-link text-decoration-none">
                    <div class="product-card h-100">

                        <!-- IMAGE -->
                        <div class="product-img">
                            <img src="admin/products/uploads/<?= $row['image'] ?? 'placeholder.png' ?>"
                                alt="<?= htmlspecialchars($row['name']) ?>">
                        </div>

                        <!-- BODY -->
                        <div class="product-body">
                            <h6 class="product-title">
                                <?= htmlspecialchars($row['name']) ?>
                            </h6>

                            <div class="product-price">

                                <?php if ($flashSale): ?>
                                <span class="badge bg-danger mb-1 d-inline-block">
                                    <?php if ($flashSale['discount_type'] === 'percent'): ?>
                                    <?= $flashSale['discount_value'] ?>% OFF
                                    <?php else: ?>
                                    ₱<?= number_format($flashSale['discount_value'], 0) ?> OFF
                                    <?php endif; ?>
                                </span>
                                <?php endif; ?>

                                <!-- PRICE RANGE -->
                                <div>
                                    <span class="text-danger fw-bold">
                                        ₱<?= number_format($minPrice, 2) ?>
                                        <?php if ($minPrice != $maxPrice): ?>
                                        – ₱<?= number_format($maxPrice, 2) ?>
                                        <?php endif; ?>
                                    </span>

                                    <?php if ($flashSale): ?>
                                    <div class="small text-muted text-decoration-line-through">
                                        ₱<?= number_format($originalMin, 2) ?>
                                        <?php if ($originalMin != $originalMax): ?>
                                        – ₱<?= number_format($originalMax, 2) ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>

                    </div>
                </a>
            </div>
            <?php endwhile; ?>

            <?php if ($products->num_rows == 0): ?>
            <div class="col-12 text-center text-muted">
                No products available
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- IMAGE FIX STYLES -->
<style>
.product-img {
    position: relative;
    width: 100%;
    aspect-ratio: 1 / 1;
    /* perfect square */
    overflow: hidden;
    background: #fff;
}

.product-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    /* 🔥 fixes stretching */
}

.product-price .badge {
    font-size: 11px;
}

.product-price {
    line-height: 1.2;
}
</style>


<?php include 'includes/footer.php'; ?>