<?php
include 'DB.php';
require 'vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$class_name = $_POST['class_name'] ?? '';
$month = $_POST['month'] ?? '';
$year = $_POST['year'] ?? '';


// Convert month and year into SQL date format
$month_start = "$year-$month-01";  
$month_end = date("Y-m-t", strtotime($month_start)); // Last day of month

// Fetch subjects for the class
$subjects_stmt = $conn->prepare("SELECT subject_name FROM subjects WHERE class_name = ?");
$subjects_stmt->bind_param("s", $class_name);
$subjects_stmt->execute();
$subjects_result = $subjects_stmt->get_result();
$subjects = [];
while ($row = $subjects_result->fetch_assoc()) {
    $subjects[] = $row['subject_name'];
}
$subjects_stmt->close();

if (empty($subjects)) {
    echo json_encode(["success" => false, "message" => "No subjects found for this class"]);
    exit();
}

// Fetch student attendance records
$attendance_stmt = $conn->prepare("SELECT roll_no, subject_name, COUNT(*) AS total_lectures,
    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS attended
    FROM attendance WHERE class_name = ? AND date BETWEEN ? AND ?
    GROUP BY roll_no, subject_name");
$attendance_stmt->bind_param("sss", $class_name, $month_start, $month_end);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();

$students = [];
while ($row = $attendance_result->fetch_assoc()) {
    $roll = $row['roll_no'];
    $subject = $row['subject_name'];
    $attended = $row['attended'];
    $total = $row['total_lectures'];
    
    if (!isset($students[$roll])) {
        $students[$roll] = ["total_attended" => 0, "total_lectures" => 0, "subjects" => []];
    }
    
    $students[$roll]["subjects"][$subject] = "$attended/$total";
    $students[$roll]["total_attended"] += $attended;
    $students[$roll]["total_lectures"] += $total;
}
$attendance_stmt->close();

// Identify defaulters (below 60%)
$defaulters = [];
foreach ($students as $roll => $data) {
    $overall_percentage = ($data["total_lectures"] > 0) ? ($data["total_attended"] / $data["total_lectures"]) * 100 : 0;
    if ($overall_percentage < 60) {
        $defaulters[$roll] = $data + ["overall_percentage" => round($overall_percentage, 2)];
    }
}

// If no defaulters, return a JSON response instead of empty output
if (empty($defaulters)) {
    echo json_encode(["success" => false, "message" => "No defaulters found"]);
    exit();
}

// Generate Excel file
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set headers
$headers = array_merge(["ID", "Roll No"], $subjects, ["Overall %"]);
$sheet->fromArray([$headers], NULL, 'A1');
$rowNumber = 2;

foreach ($defaulters as $roll => $data) {
    $row = [$rowNumber - 1, $roll];
    foreach ($subjects as $subject) {
        $row[] = $data["subjects"][$subject] ?? "0/0";
    }
    $row[] = $data["overall_percentage"];
    $sheet->fromArray([$row], NULL, "A$rowNumber");
    $rowNumber++;
}

// Save the file
$filename = "{$class_name}_Defaulter.xlsx";
$filepath = __DIR__ . "/$filename";
$writer = new Xlsx($spreadsheet);
$writer->save($filepath);

// Send JSON response with file URL
echo json_encode(["success" => true, "file_url" => $filename]);
exit();
?>
