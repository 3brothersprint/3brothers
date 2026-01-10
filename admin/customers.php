<?php include 'includes/header.php'; ?>
<?php include 'database/db.php'; ?>

<main class="col-md-9 col-lg-10 px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Customer Management</h3>
    </div>

    <div class="card shadow-sm rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody id="customersTable">
                        <!-- AJAX loaded -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main>

<script>
function loadCustomers() {
    fetch('customers/fetch_customers.php')
        .then(res => res.text())
        .then(data => {
            document.getElementById('customersTable').innerHTML = data;
        });
}

function toggleBan(userId) {
    fetch('customers/ban_customer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'user_id=' + userId
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                loadCustomers();
            } else {
                alert(response.message);
            }
        });
}

// Initial load
loadCustomers();

// Auto refresh every 5 seconds
setInterval(loadCustomers, 5000);
</script>

<?php include 'includes/footer.php'; ?>