<?php
session_start();
include '../database/db.php';
require 'vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

/* ================= PRODUCT ================= */

$name        = $_POST['name'];
$sku         = $_POST['sku'];
$category    = $_POST['category'];
$status      = $_POST['status'];
$description = $_POST['description'];
$small       = $_POST['small_description'];

$product_no = rand(100000000000000, 999999999999999);

/* INSERT PRODUCT */
$conn->query("
    INSERT INTO products 
(product_no, sku, name, category, status, description, small_description)
VALUES 
('$product_no', '$sku', '$name', '$category', '$status', '$description', '$small')
");

$product_id = $conn->insert_id;

/* ================= BARCODE GENERATOR ================= */

$generator = new BarcodeGeneratorPNG();
$barcodeDir = "barcodes/";

if (!is_dir($barcodeDir)) {
    mkdir($barcodeDir, 0777, true);
}

/* ================= PRODUCT BARCODE ================= */

$product_barcode = "3BP-" . str_pad($product_id, 6, "0", STR_PAD_LEFT);

// Save barcode image
file_put_contents(
    $barcodeDir . $product_barcode . ".png",
    $generator->getBarcode($product_barcode, $generator::TYPE_CODE_128)
);

// Save barcode value in DB
$conn->query("
    UPDATE products 
    SET barcode = '$product_barcode'
    WHERE id = '$product_id'
");

/* ================= VARIANTS ================= */

if (!empty($_POST['variant_type'])) {
    foreach ($_POST['variant_type'] as $i => $type) {

        $value   = $_POST['variant_value'][$i];
        $v_price = $_POST['variant_price'][$i];
        $v_stock = $_POST['variant_stock'][$i];

        $variant_barcode =
            rand(100000000000000, 999999999999999);

        // Save variant barcode image
        file_put_contents(
            $barcodeDir . $variant_barcode . ".png",
            $generator->getBarcode($variant_barcode, $generator::TYPE_CODE_128)
        );

        // Save variant
        $conn->query("
            INSERT INTO product_variants 
            (product_id, type, value, price, stock, barcode)
            VALUES 
            ('$product_id', '$type', '$value', '$v_price', '$v_stock', '$variant_barcode')
        ");
    }
}

/* ================= SPECIFICATIONS ================= */

if (!empty($_POST['spec_name'])) {
    foreach ($_POST['spec_name'] as $i => $spec_name) {
        $spec_value = $_POST['spec_value'][$i];

        $conn->query("
            INSERT INTO product_specs 
            (product_id, spec_name, spec_value)
            VALUES 
            ('$product_id', '$spec_name', '$spec_value')
        ");
    }
}

/* ================= IMAGES ================= */

$uploadDir = "uploads/";

if (!empty($_FILES['images']['tmp_name'][0])) {
    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        $file = uniqid() . "_" . basename($_FILES['images']['name'][$i]);
        move_uploaded_file($tmp, $uploadDir . $file);

        $conn->query("
            INSERT INTO product_images 
            (product_id, image, sort_order)
            VALUES ('$product_id', '$file', '$i')
        ");
    }
}

$_SESSION['message'] = "Product saved successfully";
$_SESSION['msg_type'] = "success";

header("Location: ../products.php");
exit;