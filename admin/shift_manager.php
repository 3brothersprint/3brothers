<?php
include 'database/db.php';
include 'includes/header.php';

$admins = $conn->query("
    SELECT id, full_name
    FROM users
    WHERE role = 'admin'
    ORDER BY full_name
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id   = $_POST['admin_id'];
    $shift_name = $_POST['shift_name'];
    $start      = $_POST['start_time'];
    $end        = $_POST['end_time'];

    $stmt = $conn->prepare("
        INSERT INTO admin_shifts (admin_id, shift_name, start_time, end_time)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            shift_name = VALUES(shift_name),
            start_time = VALUES(start_time),
            end_time = VALUES(end_time)
    ");
    $stmt->bind_param("isss", $admin_id, $shift_name, $start, $end);
    $stmt->execute();

    $success = true;
}
?>
<main class="col-md-9 col-lg-10 px-4 py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Shift Manager Management</h3>
    </div>
    <div class="container py-4">

        <?php if (!empty($success)): ?>
        <div class="alert alert-success">Shift saved successfully.</div>
        <?php endif; ?>

        <form method="POST" class="card shadow-sm border-0 rounded-4 p-4">
            <div class="mb-3">
                <label class="form-label">Admin</label>
                <select name="admin_id" class="form-select" required>
                    <option value="">Select admin</option>
                    <?php while ($a = $admins->fetch_assoc()): ?>
                    <option value="<?= $a['id'] ?>">
                        <?= htmlspecialchars($a['full_name']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Shift Name</label>
                <input type="text" name="shift_name" class="form-control" placeholder="Morning Shift" required>
            </div>

            <div class="row">
                <div class="col">
                    <label class="form-label">Start Time</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="col">
                    <label class="form-label">End Time</label>
                    <input type="time" name="end_time" class="form-control" required>
                </div>
            </div>

            <button class="btn btn-primary mt-4">
                <i class="bi bi-save"></i> Save Shift
            </button>
        </form>
    </div>
</main>


<?php include 'includes/footer.php'; ?>