<?php
require '../database/db.php';

$id = intval($_GET['id']);
$type = $_GET['type'];

if ($type === 'announcement') {
    $conn->query("DELETE FROM announcements WHERE id = $id");
}

header("Location: ../settings.php");