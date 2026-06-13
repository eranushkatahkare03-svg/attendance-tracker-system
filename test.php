<?php
// Manually require necessary files
require 'PhpSpreadsheet\src\PhpSpreadsheet\Spreadsheet.php';
require 'PhpSpreadsheet\src\PhpSpreadsheet\Writer\Xlsx.php';

// Use correct namespaces
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create a new Spreadsheet instance
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello, PhpSpreadsheet!');

// Save the Excel file
$writer = new Xlsx($spreadsheet);
$writer->save('example.xlsx');

echo "Excel file created successfully!";
?>
