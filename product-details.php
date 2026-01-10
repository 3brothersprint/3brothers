<?php include 'includes/header.php'; ?>
<?php
include 'admin/database/db.php';

$id = intval($_GET['id'] ?? 0);

$product = $conn->query("
    SELECT * FROM products WHERE id = $id AND status = 'Active'
")->fetch_assoc();

if (!$product) {
    die("Product not found");
}

/* IMAGES */
$images = $conn->query("
    SELECT * FROM product_images 
    WHERE product_id = $id 
    ORDER BY sort_order ASC
");
$imagesArr = [];
while ($img = $images->fetch_assoc()) {
    $imagesArr[] = $img;
}

$firstImage = $imagesArr[0]['image'] ?? 'placeholder.png';


/* VARIANTS */
$variants = $conn->query("
    SELECT * FROM product_variants 
    WHERE product_id = $id
");

/* SPECS */
$specs = $conn->query("
    SELECT * FROM product_specs 
    WHERE product_id = $id
");
?>

<!-- Product View -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Product Image -->
            <!-- IMAGES -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <img id="mainImage" src="admin/products/uploads/<?= $firstImage ?>" class="img-fluid rounded-4">
                </div>

                <div class="d-flex gap-2 mt-3">
                    <?php foreach ($imagesArr as $img): ?>
                    <img src="admin/products/uploads/<?= $img['image'] ?>" class="img-thumbnail thumb"
                        onclick="document.getElementById('mainImage').src=this.src">
                    <?php endforeach; ?>

                </div>
            </div>

            <!-- Product Details -->
            <div class="col-lg-6">
                <h2 class="fw-bold"><?= htmlspecialchars($product['name']) ?></h2>
                <p class="text-muted mb-2">Category: <?= $product['category'] ?></p>

                <!-- Rating -->
                <div class="mb-3">
                    <i class="bi bi-star-fill text-warning"></i>
                    <i class="bi bi-star-fill text-warning"></i>
                    <i class="bi bi-star-fill text-warning"></i>
                    <i class="bi bi-star-fill text-warning"></i>
                    <i class="bi bi-star text-warning"></i>
                    <span class="ms-2 text-muted">(24 reviews)</span>
                </div>
                <?php 
                   $variantsResult = $conn->query("
                        SELECT * FROM product_variants 
                        WHERE product_id = $id
                    ");

                    $variants = [];
                    while ($v = $variantsResult->fetch_assoc()) {
                        $variants[] = $v;
                    }

                    if (empty($variants)) {
                        die("No variants available for this product.");
                    }

                    $prices = array_column($variants, 'price');
                    $minPrice = min($prices);
                    $maxPrice = max($prices);

                    /* Group variants by type */
                    $groupedVariants = [];
                    foreach ($variants as $v) {
                        $groupedVariants[$v['type']][] = $v;
                    }
                    $typeRow = $conn->query("
                        SELECT type 
                        FROM product_variants 
                        WHERE product_id = $id
                        LIMIT 1
                    ")->fetch_assoc();

                    $typeLabel = $typeRow['type'] ?? 'Variant';
                    $groupedVariants = [];

                    foreach ($variants as $v) {
                        $groupedVariants[$v['type']][] = $v;
                    }
                    /* 🔥 FIRST VARIANT GROUP ONLY */
                    $firstVariantType = array_key_first($groupedVariants);
                    $firstVariantPrices = array_column($groupedVariants[$firstVariantType], 'price');

                    $displayMin = min($firstVariantPrices);
                    $displayMax = max($firstVariantPrices);
                ?>
                <!-- Price -->
                <h3 class="text-brand fw-bold mb-3" id="productPrice">
                    ₱<?= number_format($displayMin, 2) ?>
                    <?php if ($displayMin != $displayMax): ?>
                    – ₱<?= number_format($displayMax, 2) ?>
                    <?php endif; ?>
                </h3>


                <!-- Description -->
                <p class="text-muted">
                    <?= nl2br($product['small_description']) ?>
                </p>
                <!-- Options -->
                <div class="row g-3 mb-4">

                    <?php foreach ($groupedVariants as $type => $values): ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <?= htmlspecialchars($type) ?>
                        </label>

                        <div class="variant-group">
                            <?php foreach ($values as $v): ?>
                            <button type="button" class="variant-btn" data-price="<?= (float)$v['price'] ?>"
                                data-id="<?= (int)$v['id'] ?>" data-type="<?= htmlspecialchars($type) ?>"
                                data-value="<?= htmlspecialchars($v['value']) ?>" onclick="selectVariant(this)">
                                <?= htmlspecialchars($v['value']) ?>
                            </button>

                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <!-- QUANTITY -->
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control" min="1" value="1" id="qty">
                    </div>

                </div>

                <div class="d-flex flex-wrap gap-3">
                    <!-- ADD TO CART -->
                    <form action="cart/cart-action.php" method="POST" class="variant-form" id="addToCartForm">

                        <input type="hidden" name="action" value="add_to_cart">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']) ?>">
                        <input type="hidden" name="image"
                            value="admin/products/uploads/<?= htmlspecialchars($firstImage) ?>">

                        <!-- 🔥 MULTI VARIANT SUPPORT -->
                        <input type="hidden" name="variant_data" id="variant_data">
                        <input type="hidden" name="unit_price" id="unit_price">
                        <input type="hidden" name="total_price" id="total_price">
                        <input type="hidden" name="qty" id="qty_input" value="1">

                        <button type="submit" class="btn btn-brand px-4">
                            <i class="bi bi-cart-plus me-2"></i>Add to Cart
                        </button>
                    </form>

                    <!-- BUY NOW -->
                    <form action="buynow/buy-now.php" method="POST" class="variant-form" id="buyNowForm">

                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']) ?>">
                        <input type="hidden" name="image"
                            value="admin/products/uploads/<?= htmlspecialchars($firstImage) ?>">

                        <!-- 🔥 MULTI VARIANT SUPPORT -->
                        <input type="hidden" name="variant_data" id="variant_data_buy">
                        <input type="hidden" name="unit_price" id="unit_price_buy">
                        <input type="hidden" name="total_price" id="total_price_buy">
                        <input type="hidden" name="qty" id="qty_input_buy" value="1">

                        <button type="submit" class="btn btn-dark px-4">
                            <i class="bi bi-bag-check me-2"></i> Buy Now
                        </button>
                    </form>

                </div>


                <div class="modal fade" id="bulkOrderModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                        <div class="modal-content border-0 rounded-4 overflow-hidden">
                            <!-- Header -->
                            <div class="modal-header text-white" style="background: var(--brand-gradient)">
                                <div>
                                    <h5 class="modal-title mb-0">Bulk Order Request</h5>
                                    <small class="opacity-75">Best pricing for large quantities</small>
                                </div>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>

                            <!-- Body -->
                            <form>
                                <div class="modal-body p-4">
                                    <!-- Info Banner -->
                                    <div class="alert alert-light border d-flex gap-3 align-items-start">
                                        <i class="bi bi-info-circle-fill text-warning fs-5"></i>
                                        <div>
                                            <strong>Bulk Discount Available</strong>
                                            <div class="small text-muted">
                                                Orders above 100+ units qualify for special pricing.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-4 mt-2">
                                        <!-- Product -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Product</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-box"></i>
                                                </span>
                                                <input type="text" class="form-control" value="A4 Printing Service"
                                                    readonly />
                                            </div>
                                        </div>

                                        <!-- Quantity -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Estimated Quantity</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-hash"></i>
                                                </span>
                                                <input type="number" class="form-control" placeholder="e.g. 1000"
                                                    min="100" required />
                                            </div>
                                        </div>

                                        <!-- Variant -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Paper Type</label>
                                            <select class="form-select">
                                                <option>Standard</option>
                                                <option>Premium</option>
                                                <option>Recycled</option>
                                            </select>
                                        </div>

                                        <!-- Delivery -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Delivery Method</label>
                                            <select class="form-select">
                                                <option>Pickup</option>
                                                <option>Delivery</option>
                                            </select>
                                        </div>

                                        <!-- Notes -->
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Additional Notes</label>
                                            <textarea class="form-control" rows="3"
                                                placeholder="Deadlines, file details, special requests..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="modal-footer bg-light">
                                    <button type="submit" class="btn btn-brand px-4 d-flex align-items-center gap-2">
                                        <i class="bi bi-send"></i>
                                        Request Quotation
                                    </button>

                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Extra Info -->
                <ul class="list-unstyled mt-4 small text-muted">
                    <li>✔ Available for bulk orders</li>
                    <li>✔ Same-day printing available</li>
                    <li>✔ Pickup & delivery options</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Product Tabs -->
<section class="pb-5">
    <div class="container">

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#description">
                    Description
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#specs">
                    Specifications
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews">
                    Reviews
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="description">
                <?= nl2br($product['description']) ?>
            </div>

            <div class="tab-pane fade" id="specs">
                <ul>
                    <?php while ($s = $specs->fetch_assoc()): ?>
                    <li><?= htmlspecialchars($s['spec_name']) ?>:
                        <?= htmlspecialchars($s['spec_value']) ?></li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <div class="tab-pane fade" id="reviews">
                <p class="text-muted">No reviews yet.</p>
            </div>
        </div>
    </div>
</section>
<script>
let selectedVariants = {}; // { type: { value, price } }

function selectVariant(btn) {
    const type = btn.dataset.type;
    const value = btn.dataset.value;
    const price = parseFloat(btn.dataset.price);

    // Toggle active state per variant type
    document.querySelectorAll(`[data-type="${type}"]`)
        .forEach(b => b.classList.remove("active"));

    btn.classList.add("active");

    // Save variant selection
    selectedVariants[type] = {
        value: value,
        price: price
    };

    updatePrice();
}

function updatePrice() {
    const qty = parseInt(document.getElementById("qty").value || 1);

    // 🔥 ADD ALL VARIANT PRICES
    let unitPrice = 0;
    Object.values(selectedVariants).forEach(v => {
        unitPrice += v.price;
    });

    const total = unitPrice * qty;

    // Update price display
    document.getElementById("productPrice").innerHTML =
        "₱" + unitPrice.toFixed(2) +
        (qty > 1 ? ` <small class="text-muted">(₱${total.toFixed(2)} total)</small>` : "");

    // Sync forms (Add to Cart)
    document.getElementById("variant_data").value =
        JSON.stringify(selectedVariants);

    document.getElementById("unit_price").value = unitPrice;
    document.getElementById("total_price").value = total;

    // Sync Buy Now
    document.getElementById("variant_data_buy").value =
        JSON.stringify(selectedVariants);

    document.getElementById("unit_price_buy").value = unitPrice;
    document.getElementById("total_price_buy").value = total;

    document.getElementById("qty_input").value = qty;
    document.getElementById("qty_input_buy").value = qty;
}

// Quantity change
document.getElementById("qty").addEventListener("input", updatePrice);
</script>

<?php include 'includes/footer.php'; ?>