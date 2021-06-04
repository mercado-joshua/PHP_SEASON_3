<?php

#####################################
## EXPORT DATA FROM DATABASE
#####################################

// composer require phpoffice/phpspreadsheet --ignore-platform-reqs

// https://stackoverflow.com/questions/54921106/php-spreadsheet-is-not-working-when-i-run-it

// https://phpspreadsheet.readthedocs.io/en/latest/

// call the autoload
require 'vendor/autoload.php';

// load phpspreadsheet class using namespace
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include("connect.php");

$row_count = 2;
$query = mysqli_query($connect, "SELECT * FROM `product`");

// make a new spreadsheet object
$spreadsheet = new Spreadsheet();

// get current active sheet (first sheet)
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'Product Name');
$sheet->setCellValue('B1', 'Price');

// https://stackoverflow.com/questions/62203260/php-spreadsheet-cant-find-the-function-to-auto-size-column-width
foreach(range('A', 'B') as $col) {
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

while($row = mysqli_fetch_assoc($query)) {
    $product = $row["product"];
    $price = $row["price"];

    foreach(range('A', 'B') as $col) {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // start from row 2
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A'.$row_count, $product);
    $sheet->setCellValue('B'.$row_count, $price);
    $row_count++;
}

// make an xlsx writer object using above spreadsheet
$writer = new Xlsx($spreadsheet);

// write the file in the current directory
$writer->save('hello world.xlsx');
