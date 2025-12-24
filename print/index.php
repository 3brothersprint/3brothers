<?php
session_start();
require "../database/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../");
    exit;
}

/* ===============================
   FORM DATA
   =============================== */
$request_no = rand(100000000000000, 999999999999999);

// 🔴 CHANGE THIS BASED ON YOUR AUTH SYSTEM
$full_name  = $_SESSION['name'] ?? 'Guest';

$print_type = $_POST['print_type'] ?? '';
$paper_size = $_POST['paper_size'] ?? '';
$copies     = $_POST['copies'] ?? 1;
$color      = $_POST['color'] ?? '';
$notes      = $_POST['notes'] ?? '';

/* ===============================
   SAVE REQUEST FIRST
   =============================== */
$user_id = $_SESSION['user_id'] ?? 0;

$stmt = $conn->prepare("
    INSERT INTO print_requests
    (request_no, user_id, full_name, print_type, paper_size, copies, color, notes, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Order Placed')
");

$stmt->bind_param(
    "sisssiss",
    $request_no,
    $user_id,
    $full_name,
    $print_type,
    $paper_size,
    $copies,
    $color,
    $notes
);

$stmt->execute();
$request_id = $stmt->insert_id;
$stmt->close();


/* ===============================
   FILE UPLOAD (MAX 5)
   =============================== */
if (!isset($_FILES['files'])) {
    $_SESSION['success'] = "Request submitted (no files uploaded).";
    header("Location: ../index.php");
    exit;
}

$allowedExt = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
$uploadDir  = "uploads/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$fileCount = count($_FILES['files']['name']);

if ($fileCount > 5) {
    die("Maximum of 5 files allowed.");
}

for ($i = 0; $i < $fileCount; $i++) {

    if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) {
        continue;
    }

    $originalName = basename($_FILES['files']['name'][$i]); // ORIGINAL NAME
    $tmpPath      = $_FILES['files']['tmp_name'][$i];
    $fileSize     = $_FILES['files']['size'][$i];

    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        continue;
    }

    /**
     * Prevent overwrite:
     * uploads/filename.pdf → uploads/1699999999_filename.pdf
     */
    $origName = basename($_FILES['files']['name'][$i]);

// sanitize filename (IMPORTANT)
$safeName = time() . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "_", $origName);

$dest = $uploadDir . $safeName;

move_uploaded_file($tmpPath, $dest);

    move_uploaded_file($tmpPath, $destPath);

   $stmt = $conn->prepare("
    INSERT INTO print_request_files
    (request_id, file_name, file_path, file_size)
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param(
    "issi",
    $request_id,
    $origName,     // ORIGINAL name (for display)
    $dest,         // ACTUAL stored path
    $fileSize
);
$stmt->execute();
$stmt->close();

}
/* ===============================
   INSERT INITIAL LOG (ORDER PLACED)
================================ */
$logStmt = $conn->prepare("
    INSERT INTO print_request_logs
    (request_id, status, remark)
    VALUES (?, 'Order Placed', 'Order placed by customer')
");

$logStmt->bind_param("i", $request_id);
$logStmt->execute();
$logStmt->close();


/* ===============================
   SUCCESS
   =============================== */
$_SESSION['success'] =
    "Printing request submitted successfully! Please wait for the price update.";

header("Location: ../index.php");
exit;