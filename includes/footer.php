<!-- Footer -->
<footer class="footer sticky">
    <div class="container">
        <div class="row gy-4">
            <!-- Brand -->
            <div class="col-md-4 text-center text-md-start">
                <h5 class="footer-title">3 Brothers Print Services</h5>
                <p class="footer-text">
                    Professional printing solutions and educational supplies you can
                    trust.
                </p>

                <!-- Social Media -->
                <div class="footer-social mt-3">
                    <a href="#" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" aria-label="Twitter">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                    <a href="#" aria-label="WhatsApp">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-md-4 text-center">
                <h6 class="footer-heading">Quick Links</h6>
                <ul class="footer-links list-unstyled">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Services</a></li>
                    <li><a href="#">Products</a></li>
                    <li><a href="#">About Us</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-md-4 text-center text-md-end">
                <h6 class="footer-heading">Contact Us</h6>
                <p class="footer-text mb-1"><i class="bi bi-telephone"></i> +63 994 082 3693</p>
                <p class="footer-text mb-1"><i class="bi bi-envelope"></i> 3brothersprintservices@gmail.com</p>
                <p class="footer-text"><i class="bi bi-geo"></i> Your City, Philippines</p>
            </div>
        </div>

        <hr class="footer-divider" />

        <div class="text-center small">
            © 2025 3 Brothers Print Services & Educational Supplies · All Rights
            Reserved
        </div>
    </div>
</footer>
<script>
function loadNotifications() {
    fetch("ajax/fetch_notifications.php")
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById("notifBadge");
            const list = document.getElementById("notifList");

            list.innerHTML = data.html;

            if (data.count > 0) {
                badge.textContent = data.count;
                badge.classList.remove("d-none");
            } else {
                badge.classList.add("d-none");
            }
        });
}

/* Load on page load */
document.addEventListener("DOMContentLoaded", loadNotifications);

/* Mark notifications as read when dropdown opens */
document.getElementById("notifDropdown").addEventListener("click", () => {
    fetch("ajax/mark_notifications_read.php", {
        method: "POST"
    });
    document.getElementById("notifBadge").classList.add("d-none");
});
</script>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
<?php if (isset($_SESSION['success'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: '<?= addslashes($_SESSION['success']) ?>',
    confirmButtonText: 'OK',
    confirmButtonColor: '#3bb273'
});
</script>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php if (isset($_GET['updated'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Profile Updated',
    text: 'Your personal information has been saved.',
    confirmButtonColor: 'var(--brand)',
    timer: 2000,
    showConfirmButton: false
});
</script>
<?php endif; ?>
<?php if (isset($_GET['error'])): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Update Failed',
    text: 'Something went wrong. Please try again.',
    confirmButtonColor: 'var(--brand)'
});
</script>
<?php endif; ?>

<script>
if (window.location.search.includes('updated')) {
    window.history.replaceState({}, document.title, window.location.pathname);
}
</script>
<?php
if(isset($_SESSION['message'])){
  ?>
alertify.set('notifier','position', 'top-center');
alertify.success('$_SESSION['message']');
<?php
}
?>
<script>
window.addEventListener("load", () => {
    document.getElementById("pageLoader").style.display = "none";
});
</script>

<!-- Custom JS -->
<script src="js/script.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
let selectedVariants = {};
const basePrice = <?= (float)$product['price'] ?>;

function selectVariant(btn) {
    const type = btn.dataset.type;
    const value = btn.dataset.value;
    const price = parseFloat(btn.dataset.price);

    selectedVariants[type] = {
        value,
        price
    };

    btn.closest('.variant-group')
        .querySelectorAll('.variant-btn')
        .forEach(b => b.classList.remove('active'));

    btn.classList.add('active');

    updatePrice();
}

function updatePrice() {
    let variantTotal = 0;

    Object.values(selectedVariants).forEach(v => {
        variantTotal += v.price;
    });

    const qty = parseInt(document.getElementById('qty').value || 1);
    const unitPrice = basePrice + variantTotal;
    const totalPrice = unitPrice * qty;

    // UI
    document.getElementById('productPrice').innerText =
        '₱' + totalPrice.toFixed(2);

    // Add to Cart
    document.getElementById('variant_data').value =
        JSON.stringify(selectedVariants);
    document.getElementById('unit_price').value = unitPrice.toFixed(2);
    document.getElementById('total_price').value = totalPrice.toFixed(2);
    document.getElementById('qty_input').value = qty;

    // Buy Now
    document.getElementById('variant_data_buy').value =
        JSON.stringify(selectedVariants);
    document.getElementById('unit_price_buy').value = unitPrice.toFixed(2);
    document.getElementById('total_price_buy').value = totalPrice.toFixed(2);
    document.getElementById('qty_input_buy').value = qty;
}

// Quantity change
document.getElementById('qty').addEventListener('input', updatePrice);
</script>

</body>

</html>