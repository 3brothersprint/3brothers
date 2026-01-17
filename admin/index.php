<?php
include 'includes/header.php';
include 'database/db.php';

date_default_timezone_set('Asia/Manila');

/* DATE & TIME */
$today   = date('Y-m-d');
$timeNow = date('H:i:s');

/* ADMIN INFO */
$admin_id = $_SESSION['admin_id'];

/* ================= ATTENDANCE LOG ================= */
$statusStmt = $conn->prepare("
    SELECT last_activity
    FROM admin_attendance
    WHERE admin_id = ? AND login_date = ?
");
$statusStmt->bind_param("is", $admin_id, $today);
$statusStmt->execute();
$activity = $statusStmt->get_result()->fetch_assoc();

$isOnline = false;
$offlineMinutes = 0;

if (!empty($activity['last_activity'])) {
    $last = strtotime($activity['last_activity']);
    $now  = time();
    $diff = ($now - $last) / 60;

    if ($diff <= 5) {
        $isOnline = true;
    } else {
        $offlineMinutes = floor($diff);
    }
}


/* Grace period: 8:15 AM */
$lateAfter = strtotime('08:15:00');
$status = (strtotime($timeNow) > $lateAfter) ? 'Late' : 'On Time';

/* One attendance per day */
$check = $conn->prepare("
    SELECT id FROM admin_attendance
    WHERE admin_id = ? AND login_date = ?
");
$check->bind_param("is", $admin_id, $today);
$check->execute();

if ($check->get_result()->num_rows === 0) {
    $insert = $conn->prepare("
        INSERT INTO admin_attendance (admin_id, login_date, login_time, status)
        VALUES (?, ?, ?, ?)
    ");
    $insert->bind_param("isss", $admin_id, $today, $timeNow, $status);
    $insert->execute();
}

/* FETCH TODAY ATTENDANCE */
$todayStmt = $conn->prepare("
    SELECT login_time, status
    FROM admin_attendance
    WHERE admin_id = ? AND login_date = ?
");
$todayStmt->bind_param("is", $admin_id, $today);
$todayStmt->execute();
$attendance = $todayStmt->get_result()->fetch_assoc();

/* ================= MONTHLY STATS ================= */

$month = date('Y-m');

/* Count late days */
$lateCheck = $conn->prepare("
    SELECT COUNT(*) total
    FROM admin_attendance
    WHERE admin_id = ?
      AND status = 'Late'
      AND DATE_FORMAT(login_date, '%Y-%m') = ?
");
$lateCheck->bind_param("is", $admin_id, $month);
$lateCheck->execute();
$lateCount = $lateCheck->get_result()->fetch_assoc()['total'];

/* Auto-flag if 3+ late */
if ($lateCount >= 3) {
    $flag = $conn->prepare("
        UPDATE admin_attendance
        SET is_flagged = 1
        WHERE admin_id = ?
          AND DATE_FORMAT(login_date, '%Y-%m') = ?
    ");
    $flag->bind_param("is", $admin_id, $month);
    $flag->execute();
}

/* Monthly summary */
$report = $conn->prepare("
    SELECT 
        COUNT(*) total_days,
        SUM(status = 'Late') late_days,
        SUM(status = 'On Time') ontime_days
    FROM admin_attendance
    WHERE admin_id = ?
      AND DATE_FORMAT(login_date, '%Y-%m') = ?
");
$report->bind_param("is", $admin_id, $month);
$report->execute();
$summary = $report->get_result()->fetch_assoc();

$admins = $conn->query("
    SELECT 
        u.id,
        u.full_name,
        u.role,
        att.last_activity,
        att.login_time,
        att.logout_time
    FROM users u
    LEFT JOIN admin_attendance att
        ON att.admin_id = u.id
        AND att.login_date = CURDATE()
    WHERE u.role = 'admin'
    ORDER BY u.full_name
");
$shiftStmt = $conn->prepare("
    SELECT shift_name, start_time, end_time
    FROM admin_shifts
    WHERE admin_id = ?
");
$shiftStmt->bind_param("i", $admin_id);
$shiftStmt->execute();
$shift = $shiftStmt->get_result()->fetch_assoc();
$stats = [];

/* Products */
$stats['products'] = $conn->query("SELECT COUNT(*) total FROM products")
    ->fetch_assoc()['total'];

/* Pending orders */
$stats['pending_orders'] = $conn->query("
    SELECT COUNT(*) AS total
    FROM orders
    WHERE status IN ('Order Placed', 'To Ship', 'To Transit', 'Out for Delivery')
")->fetch_assoc()['total'];


/* Today's print jobs */
$stats['today_jobs'] = $conn->query("
    SELECT COUNT(*) total
    FROM print_requests
    WHERE DATE(created_at) = CURDATE()
")->fetch_assoc()['total'];

?>

<main class="col-md-9 col-lg-10 px-4 py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Admin Dashboard</h3>
        <span class="badge bg-dark px-3 py-2"><?= date('F d, Y') ?></span>
    </div>

    <!-- LIVE CLOCK -->
    <div class="mb-4">
        <div class="clock-card text-center rounded-4 p-3 shadow-sm">
            <div id="clockTime" class="fw-bold fs-2"></div>
            <div id="clockDate" class="small text-muted"></div>
        </div>
    </div>

    <!-- DASHBOARD CARDS -->
    <div class="row g-4 mb-4">
        <div class="mb-3 d-flex gap-2">
            <a href="attendance/export_excel.php" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="attendance/attendance_pdf.php" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            <a href="attendance/print_sheet.php" target="_blank" class="btn btn-dark btn-sm">
                <i class="bi bi-printer"></i> Print Attendance
            </a>

        </div>

        <!-- ATTENDANCE -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2">
                        <i class="bi bi-clock-history"></i> Attendance Today
                    </h6>
                    <?php if ($attendance): ?>
                    <p class="mb-1">
                        Login:
                        <strong><?= date('h:i A', strtotime($attendance['login_time'])) ?></strong>
                    </p>

                    <?php if (!empty($attendance['logout_time'])): ?>
                    <p class="mb-1">
                        Logout:
                        <strong><?= date('h:i A', strtotime($attendance['logout_time'])) ?></strong>
                    </p>
                    <?php endif; ?>

                    <span class="badge <?= $attendance['status'] === 'Late' ? 'bg-danger' : 'bg-success' ?>">
                        <?= $attendance['status'] ?>
                    </span>
                    <?php else: ?>
                    <span class="badge bg-secondary">Not Recorded</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2">
                        <i class="bi bi-activity"></i> Activity Status
                    </h6>
                    <span id="activityBadge" class="badge bg-secondary">Checking...</span>
                    <div id="activityText" class="small text-muted mt-1"></div>
                </div>
            </div>
        </div>

        <!-- ADMIN INFO -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2">
                        <i class="bi bi-person-badge"></i> Logged In As
                    </h6>
                    <p class="mb-0 fw-semibold"><?= htmlspecialchars($_SESSION['admin_name']) ?></p>
                    <small class="text-muted">Administrator</small>
                </div>
            </div>
        </div>

        <!-- SYSTEM -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2">
                        <i class="bi bi-shield-check"></i> System Status
                    </h6>
                    <span id="systemBadge" class="badge bg-secondary">Checking...</span>
                    <div id="systemText" class="small text-muted mt-1"></div>
                </div>
            </div>
        </div>

    </div>
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <h6 class="fw-semibold mb-3">
                <i class="bi bi-people"></i> Admin Live Status
            </h6>

            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Admin</th>
                        <th>Status</th>
                        <th>Last Activity</th>
                    </tr>
                </thead>
                <tbody id="adminLiveTable"></tbody>
            </table>
        </div>
    </div>



    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h6 class="fw-semibold mb-2">
                    <i class="bi bi-calendar2-week"></i> Shift Schedule
                </h6>

                <?php if ($shift): ?>
                <p class="mb-1 fw-semibold">
                    <?= htmlspecialchars($shift['shift_name']) ?>
                </p>
                <small class="text-muted">
                    <?= date('h:i A', strtotime($shift['start_time'])) ?>
                    —
                    <?= date('h:i A', strtotime($shift['end_time'])) ?>
                </small>
                <?php else: ?>
                <span class="badge bg-secondary">No Shift Assigned</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ATTENDANCE HISTORY -->
    <div class="card shadow-sm border-0 rounded-4 mb-3">
        <div class="card-body">
            <h6 class="fw-semibold mb-3">
                <i class="bi bi-calendar-check"></i> Attendance History (Last 7 Days)
            </h6>

            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Login Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                $hist = $conn->prepare("
                    SELECT login_date, login_time, status
                    FROM admin_attendance
                    WHERE admin_id = ?
                    ORDER BY login_date DESC
                    LIMIT 7
                ");
                $hist->bind_param("i", $admin_id);
                $hist->execute();
                $rows = $hist->get_result();

                while ($row = $rows->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= date('M d, Y', strtotime($row['login_date'])) ?></td>
                        <td><?= date('h:i A', strtotime($row['login_time'])) ?></td>
                        <td>
                            <span class="badge <?= $row['status'] === 'Late' ? 'bg-danger' : 'bg-success' ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MONTHLY SUMMARY -->
    <div class="mb-4">
        <span class="badge bg-primary">Total: <?= $summary['total_days'] ?></span>
        <span class="badge bg-success">On Time: <?= $summary['ontime_days'] ?></span>
        <span class="badge bg-danger">Late: <?= $summary['late_days'] ?></span>
    </div>



    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 text-center">
                <div class="card-body">
                    <i class="bi bi-box-seam fs-3 text-primary"></i>
                    <h4 class="fw-bold mt-2"><?= $stats['products'] ?></h4>
                    <small class="text-muted">Total Products</small>
                </div>
            </div>
        </div>


        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 text-center">
                <div class="card-body">
                    <i class="bi bi-hourglass-split fs-3 text-warning"></i>
                    <h4 class="fw-bold mt-2"><?= $stats['pending_orders'] ?></h4>
                    <small class="text-muted">Pending Orders</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 text-center">
                <div class="card-body">
                    <i class="bi bi-printer fs-3 text-success"></i>
                    <h4 class="fw-bold mt-2"><?= $stats['today_jobs'] ?></h4>
                    <small class="text-muted">Print Jobs Today</small>
                </div>
            </div>
        </div>

    </div>


</main>

<!-- CLOCK STYLE + SCRIPT -->
<style>
.clock-card {
    background: var(--brand-gradient);
    color: rgba(255, 255, 255, 1);
    font-family: 'Courier New', monospace;
}
</style>

<script>
function updateClock() {
    const now = new Date();
    document.getElementById("clockTime").innerText =
        now.toLocaleTimeString('en-PH');
    document.getElementById("clockDate").innerText =
        now.toLocaleDateString('en-PH', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
}
setInterval(updateClock, 1000);
updateClock();
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    function heartbeat() {
        fetch('ajax/update_activity.php')
            .catch(err => console.error('Heartbeat error:', err));
    }

    function refreshStatus() {
        fetch('ajax/get_activity_status.php')
            .then(res => res.json())
            .then(data => {

                const me = <?= (int)$admin_id ?>;
                const table = document.getElementById('adminLiveTable');

                table.innerHTML = '';

                data.forEach(admin => {

                    if (admin.id === me) {
                        const badge = document.getElementById('activityBadge');
                        const text = document.getElementById('activityText');
                        const sys = document.getElementById('systemBadge');
                        const sysT = document.getElementById('systemText');

                        if (admin.online) {
                            badge.className = 'badge bg-success';
                            badge.textContent = 'Active Now';
                            text.textContent = '';

                            sys.className = 'badge bg-success';
                            sys.textContent = 'Online';
                            sysT.textContent = '';
                        } else {
                            badge.className = 'badge bg-secondary';
                            badge.textContent = 'Inactive';
                            text.textContent = admin.offlineMinutes + ' mins offline';

                            sys.className = 'badge bg-secondary';
                            sys.textContent = 'Offline';
                            sysT.textContent = 'Offline for ' + admin.offlineMinutes + ' min';
                        }
                    }

                    table.innerHTML += `
                        <tr>
                            <td class="fw-semibold">${admin.name}</td>
                            <td>
                                <span class="badge ${admin.online ? 'bg-success' : 'bg-secondary'}">
                                    ${admin.online ? 'Online' : 'Offline'}
                                </span>
                            </td>
                            <td class="small text-muted">
                                ${admin.online ? 'Active now' : (admin.offlineMinutes ? admin.offlineMinutes + ' mins ago' : '—')}
                            </td>
                        </tr>
                    `;
                });
            })
            .catch(err => console.error('Status error:', err));
    }

    // run immediately
    heartbeat();
    refreshStatus();

    // repeat every 30 seconds
    setInterval(() => {
        heartbeat();
        refreshStatus();
    }, 30000);
});
</script>

<?php include 'includes/footer.php'; ?>