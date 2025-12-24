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