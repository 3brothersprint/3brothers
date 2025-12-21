<?php 
include 'includes/header.php'; 
?>
<!-- Carousel -->
<div id="mainCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active position-relative">
            <img src="https://images.unsplash.com/photo-1581090700227-1e37b190418e" class="d-block w-100"
                alt="Printing Services" />
            <div class="hero-overlay"></div>
            <div class="hero-text">
                <h1>Professional Printing Solutions</h1>
                <p class="lead">High-quality print services you can trust.</p>
                <a href="#" class="btn btn-brand mt-3">Our Services</a>
            </div>
        </div>

        <div class="carousel-item position-relative">
            <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f" class="d-block w-100"
                alt="Educational Supplies" />
            <div class="hero-overlay"></div>
            <div class="hero-text">
                <h1>Educational Supplies</h1>
                <p class="lead">Supporting learning and growth.</p>
            </div>
        </div>
    </div>
</div>
<!-- Info Alert -->
<div class="container mb-4">
    <div class="alert alert-primary d-flex align-items-center py-2 px-3 mb-0" role="alert">
        <svg class="bi flex-shrink-0 me-2" width="16" height="16" role="img" aria-label="Info:">
            <use xlink:href="#info-fill" />
        </svg>
        <span>
            To print a document, select the <strong>PRINTING</strong> category.
        </span>
    </div>
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

            <form id="printWizardForm">
                <div class="modal-body p-4">
                    <!-- Wizard Progress Steps -->
                    <div class="wizard-steps mb-4">
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

                            <div class="custom-select" data-target="printType">
                                <button type="button" class="custom-select-btn">
                                    Select service
                                    <span class="arrow"></span>
                                </button>
                                <ul class="custom-select-options">
                                    <li data-value="Print">Print</li>
                                    <li data-value="Xerox">Xerox</li>
                                </ul>
                            </div>

                            <input type="hidden" id="printType" name="print_type" required />
                        </div>
                    </div>

                    <div class="wizard-step" data-required-step="0">
                        <div class="wizard-step-inner">
                            <h5 class="mb-3">Step 2: Print Details</h5>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Paper Size</label>
                                    <select class="form-select" required>
                                        <option>A4</option>
                                        <option>Letter</option>
                                        <option>Legal (8.5 x 13")</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Copies</label>
                                    <input type="number" class="form-control" min="1" required />
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Color</label>
                                    <select class="form-select">
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
                                <input type="file" id="fileInput" class="file-input" accept=".pdf,.doc,.docx,.jpg,.png"
                                    required />

                                <div class="upload-content text-center">
                                    <div class="upload-icon">📤</div>
                                    <p class="mb-1 fw-semibold">Drag & drop your file here</p>
                                    <small class="text-muted">
                                        or click to browse (PDF, DOC, JPG, PNG)
                                    </small>
                                </div>
                            </div>

                            <div class="file-preview mt-3 d-none" id="filePreview">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <strong id="fileName"></strong>
                                        <div class="text-muted small" id="fileSize"></div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="removeFile">
                                        Remove
                                    </button>
                                </div>
                            </div>

                            <textarea class="form-control mt-3" rows="3"
                                placeholder="Additional instructions..."></textarea>
                        </div>
                    </div>

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
                            </ul>

                            <p class="text-muted mt-3">
                                Please confirm all details before submitting your request.
                            </p>
                        </div>
                    </div>
                </div>

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
?>

<!-- Products -->
<section class="section-padding py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Featured Products</h2>
            <a href="products.html" class="btn btn-sm btn-brand">View All</a>
        </div>

        <div class="row g-4">
            <?php while ($row = $products->fetch_assoc()): ?>
            <div class="col-6 col-md-3">
                <a href="product-details.php?id=<?= $row['id'] ?>" class="product-link text-decoration-none">
                    <div class="product-card h-100">

                        <div class="product-img">
                            <img src="admin/products/uploads/<?= $row['image'] ?? 'placeholder.png' ?>"
                                alt="<?= htmlspecialchars($row['name']) ?>" />
                        </div>

                        <div class="product-body">
                            <h6 class="product-title">
                                <?= htmlspecialchars($row['name']) ?>
                            </h6>
                            <p class="product-price">
                                ₱<?= number_format($row['price'], 2) ?>
                            </p>
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
<?php include 'includes/footer.php'; ?>