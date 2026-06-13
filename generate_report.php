<?php
require 'DB.php';  // Include database connection
require 'vendor/autoload.php';  // Ensure PhpSpreadsheet is installed via Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Check if all required fields are set
if (!isset($_POST['start_date'], $_POST['end_date'], $_POST['subject'])) {
    die(json_encode(["success" => false, "message" => "❌ All fields are required!"]));
}

$startDate = $_POST['start_date'];
$endDate = $_POST['end_date'];
$subject = $_POST['subject'];

// Prepare SQL query
$sql = "SELECT roll_no, COUNT(*) AS total_lectures, 
               SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS attended_lectures
        FROM attendance
        WHERE date BETWEEN ? AND ? AND subject_name = ?
        GROUP BY roll_no";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["success" => false, "message" => "❌ SQL Error: " . $conn->error]));
}

$stmt->bind_param("sss", $startDate, $endDate, $subject);
$stmt->execute();
$result = $stmt->get_result();

// Check if results exist
if ($result->num_rows === 0) {
    die(json_encode(["success" => false, "message" => "❌ No attendance records found for the selected date range and subject."]));
}

// Create an array to store data for Excel
$data = [];
while ($row = $result->fetch_assoc()) {
    $row['attendance_ratio'] = $row['attended_lectures'] . "/" . $row['total_lectures'];
    $data[] = $row;
}

// Send JSON response for debugging
echo json_encode(["success" => true, "data" => $data]);

// Create a new Excel file
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Attendance Report");

// Set headers
$sheet->setCellValue("A1", "Roll No");
$sheet->setCellValue("B1", "Attendance (Attended / Total)");

$rowNumber = 2;
foreach ($data as $entry) {
    $sheet->setCellValue("A$rowNumber", $entry["roll_no"]);
    $sheet->setCellValue("B$rowNumber", $entry["attendance_ratio"]);
    $rowNumber++;
}

// Send Excel file as a download
$filename = "Attendance_Report_" . date("Y-m-d") . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=$filename");
$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
?>
