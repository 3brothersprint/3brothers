<!-- Footer -->
<footer class="footer">
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
                <p class="footer-text mb-1">📞 +63 900 000 0000</p>
                <p class="footer-text mb-1">✉️ info@3brothersprint.com</p>
                <p class="footer-text">📍 Your City, Philippines</p>
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
<!-- Custom JS -->
<script src="js/script.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ===============================
   SINGLE SOURCE OF TRUTH
================================ */
let selectedVariant = null;

/* ===============================
   VARIANT CLICK
================================ */
window.selectVariant = function(btn) {
    const type = btn.getAttribute('data-type');
    const price = btn.getAttribute('data-price');

    if (!type || !price) return;

    selectedVariant = {
        type,
        price
    };

    // UI
    document.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Price display
    document.getElementById('productPrice').innerText =
        '₱' + parseFloat(price).toFixed(2);

    // Fill ALL forms
    document.querySelectorAll('.variant-form').forEach(form => {
        form.querySelector('input[name="type"]').value = type;
        form.querySelector('input[name="price"]').value = price;
    });
};

/* ===============================
   QUANTITY SYNC
================================ */
document.getElementById('qty').addEventListener('input', function() {
    let qty = parseInt(this.value) || 1;
    if (qty < 1) qty = 1;

    document.querySelectorAll('.variant-form').forEach(form => {
        form.querySelector('input[name="qty"]').value = qty;
    });
});

/* ===============================
   VALIDATION (FINAL)
================================ */
document.querySelectorAll('.variant-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const type = form.querySelector('input[name="type"]').value;
        const price = form.querySelector('input[name="price"]').value;

        if (!type || !price || price <= 0) {
            e.preventDefault();
            alert('Please select a variant first.');
        }
    });
});
</script>

</body>

</html>