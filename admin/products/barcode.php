<?php
use Picqer\Barcode\BarcodeGeneratorPNG;

$generator = new BarcodeGeneratorPNG();

file_put_contents(
    "barcodes/$variant_code.png",
    $generator->getBarcode($variant_code, $generator::TYPE_CODE_128)
);

?>