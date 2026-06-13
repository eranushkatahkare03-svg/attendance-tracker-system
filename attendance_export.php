<?php
ob_start(); 
require 'vendor/autoload.php'; // Load PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

include 'DB.php'; // Include database connection

// Check if the database connection is successful
if ($conn->connect_error) {
    die("❌ Database Connection Failed: " . $conn->connect_error);
}

// Receive input values safely
$class = trim($_POST['class'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$date = trim($_POST['date'] ?? '');

// Validate input
if (empty($class) || empty($subject) || empty($date)) {
    die("❌ Invalid input! Please select class, subject, and date.");
}

// Define file path
$filename = __DIR__ . "/Attendance_{$class}_{$subject}.xlsx";

// Load existing file or create a new one
if (file_exists($filename)) {
    $spreadsheet = IOFactory::load($filename);
    $sheet = $spreadsheet->getActiveSheet();
} else {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set headers if it's a new file
    $sheet->setCellValue('A1', 'Roll No')
          ->setCellValue('B1', 'Class')
          ->setCellValue('C1', 'Subject')
          ->setCellValue('D1', 'Date')
          ->setCellValue('E1', 'Status');

    // Apply styles to header row
    $headerStyle = [
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'CCCCCC']
        ],
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN]
        ]
    ];
    $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
}

// Fetch existing records from Excel to avoid duplicates
$existingRecords = [];
$highestRow = $sheet->getHighestRow();

for ($row = 2; $row <= $highestRow; $row++) {  // Start from row 2 (skip headers)
    $existingRollNo = $sheet->getCell("A$row")->getValue();
    $existingDate = $sheet->getCell("D$row")->getValue();
    $existingRecords["$existingRollNo|$existingDate"] = true;
}

// Fetch attendance records from the database
$sql = "SELECT roll_no, class_name, subject_name, date, status FROM attendance 
        WHERE class_name = ? AND subject_name = ? ORDER BY date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $class, $subject);
$stmt->execute();
$result = $stmt->get_result();

// Append new attendance data only if not already in Excel
while ($data = $result->fetch_assoc()) {
    $currentKey = "{$data['roll_no']}|{$data['date']}";

    // Skip if record already exists in the Excel file
    if (isset($existingRecords[$currentKey])) {
        continue;
    }

    $highestRow++;  // Move to the next empty row

    // Set values in the row
    $sheet->setCellValue("A$highestRow", $data['roll_no'])
          ->setCellValue("B$highestRow", $data['class_name'])
          ->setCellValue("C$highestRow", $data['subject_name'])
          ->setCellValue("D$highestRow", $data['date'])
          ->setCellValue("E$highestRow", $data['status']);

    // Apply styles
    $sheet->getStyle("A$highestRow:E$highestRow")->applyFromArray([
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN]
        ]
    ]);

    // Mark record as added
    $existingRecords[$currentKey] = true;
}

// Save the file
$writer = new Xlsx($spreadsheet);
$writer->save($filename);

// ✅ Fix: Close the connection properly
$stmt->close();
$conn->close();

// ✅ Fix: Flush output before downloading
ob_clean();
ob_flush();

// Send the file for download
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=" . basename($filename));
header("Content-Length: " . filesize($filename));
readfile($filename);
exit;
?>
