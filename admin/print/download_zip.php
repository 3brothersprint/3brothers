<?php
require "../database/db.php";

$request_id = (int)($_GET['request_id'] ?? 0);
if ($request_id <= 0) {
    die("Invalid request");
}

$stmt = $conn->prepare("
    SELECT file_name, file_path
    FROM print_request_files
    WHERE request_id = ?
");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No files found");
}

$zipName = "Print Order - {$request_id}.zip";
$tmpZip  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipName;

$zip = new ZipArchive();
if ($zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("ZIP creation failed");
}

while ($f = $result->fetch_assoc()) {

    // 🔥 CORRECT absolute path (../../print/)
    $fullPath = realpath(__DIR__ . "/../../print/" . $f['file_path']);

    if ($fullPath && file_exists($fullPath)) {
        $zip->addFile($fullPath, $f['file_name']); // keep original filename
    }
}

$zip->close();

if (!file_exists($tmpZip)) {
    die("ZIP file not created");
}

header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=\"$zipName\"");
header("Content-Length: " . filesize($tmpZip));

readfile($tmpZip);
unlink($tmpZip);
exit;