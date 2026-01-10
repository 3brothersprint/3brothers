<?php
include "../database/db.php";

$query = "
    SELECT p.id, p.name,
           (SELECT image FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC LIMIT 1) AS image
    FROM products p
    WHERE p.status = 'Active'
    ORDER BY p.id DESC
    LIMIT 8
";

$products = $conn->query($query);

if ($products->num_rows === 0) {
    echo '<div class="col-12 text-center text-muted">No products available</div>';
    exit;
}
while ($row = $products->fetch_assoc()) {

    /* ===== GET FIRST VARIANT TYPE ===== */
    $stmt = $conn->prepare("
        SELECT type
        FROM product_variants
        WHERE product_id = ?
        ORDER BY id ASC
        LIMIT 1
    ");
    $stmt->bind_param("i", $row['id']);
    $stmt->execute();
    $firstVariant = $stmt->get_result()->fetch_assoc();

    if (!$firstVariant) {
        continue; // product has no variants
    }

    $firstVariantType = $firstVariant['type'];

    /* ===== PRICE RANGE FOR FIRST VARIANT ONLY ===== */
    $stmt = $conn->prepare("
        SELECT MIN(price) AS min_price, MAX(price) AS max_price
        FROM product_variants
        WHERE product_id = ? AND type = ?
    ");
    $stmt->bind_param("is", $row['id'], $firstVariantType);
    $stmt->execute();
    $range = $stmt->get_result()->fetch_assoc();

    $minPrice = $range['min_price'];
    $maxPrice = $range['max_price'];

    if (!$minPrice) continue;

    /* Flash sale support */
    $originalMin = $minPrice;
    $originalMax = $maxPrice;

    if (!empty($flashSale)) {
        $minPrice = getDiscountedPrice($minPrice, $flashSale);
        $maxPrice = getDiscountedPrice($maxPrice, $flashSale);
    }
    ?>

<div class="col-6 col-md-3">
    <a href="product-details.php?id=<?= $row['id'] ?>" class="product-link text-decoration-none">
        <div class="product-card h-100">

            <div class="product-img">
                <img src="admin/products/uploads/<?= $row['image'] ?? 'placeholder.png' ?>"
                    alt="<?= htmlspecialchars($row['name']) ?>">
            </div>

            <div class="product-body">
                <h6 class="product-title">
                    <?= htmlspecialchars($row['name']) ?>
                </h6>

                <div class="product-price">

                    <?php if (!empty($flashSale)): ?>
                    <span class="badge bg-danger mb-1 d-inline-block">
                        <?= $flashSale['discount_type'] === 'percent'
                                ? $flashSale['discount_value'].'% OFF'
                                : '₱'.number_format($flashSale['discount_value'],0).' OFF' ?>
                    </span>
                    <?php endif; ?>

                    <div>
                        <span class="text-danger fw-bold">
                            ₱<?= number_format($minPrice, 2) ?>
                            <?php if ($minPrice != $maxPrice): ?>
                            – ₱<?= number_format($maxPrice, 2) ?>
                            <?php endif; ?>
                        </span>

                        <?php if (!empty($flashSale)): ?>
                        <div class="small text-muted text-decoration-line-through">
                            ₱<?= number_format($originalMin, 2) ?>
                            <?php if ($originalMin != $originalMax): ?>
                            – ₱<?= number_format($originalMax, 2) ?>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="small text-muted">
                            Based on <?= htmlspecialchars($firstVariantType) ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </a>
</div>

<?php } ?>