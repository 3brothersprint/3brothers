<?php
session_start();
require "database/db.php";
include "includes/header.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
?>

<main class="col-md-9 col-lg-10 px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">🔔 Notifications</h3>

        <button class="btn btn-sm btn-outline-secondary" id="markAllRead">
            Mark all as read
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="list-group list-group-flush" id="notificationList">
            <div class="text-center text-muted py-4">Loading notifications…</div>
        </div>
    </div>

</main>

<?php include "includes/footer.php"; ?>