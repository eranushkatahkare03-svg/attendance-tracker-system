<?php
include 'DB.php';

$data = json_decode(file_get_contents("php://input"), true);
$class_name = $data['className'] ?? '';
$subject_name = $data['subjectName'] ?? '';
$date = $data['date'] ?? '';
$batch = $data['batch'] ?? 'default_batch'; // Get batch if applicable

file_put_contents("debug_log.txt", print_r($data, true));

if (empty($class_name) || empty($subject_name) || empty($date)) {
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit;
}

if (!$data || !isset($data['students'])) {
    echo json_encode(["success" => false, "message" => "Invalid student data"]);
    exit;
}

$present_students = is_array($data['students']) ? $data['students'] : [];

if (empty($present_students)) {
    echo json_encode(["success" => false, "message" => "No students selected"]);
    exit;
}

file_put_contents("debug_log.txt", file_get_contents("php://input"));

// Step 1: Fetch students based on subject type
if (strpos($subject_name, "-PR") !== false) { 
    // If it's a practical subject, fetch only students from the selected batch
    $students_query = $conn->prepare("SELECT roll_no FROM students WHERE class_name = ? AND batch = ?");
    $students_query->bind_param("ss", $class_name, $batch);
} else {
    // For theory subjects, fetch all students in the class
    $students_query = $conn->prepare("SELECT roll_no FROM students WHERE class_name = ?");
    $students_query->bind_param("s", $class_name);
}

$students_query->execute();
$result = $students_query->get_result();

$all_students = [];
while ($row = $result->fetch_assoc()) {
    $all_students[] = $row['roll_no'];
}
$students_query->close();

// Step 2: Mark Present students
$insert_stmt = $conn->prepare("INSERT INTO attendance (class_name, roll_no, subject_name, date, status) VALUES (?, ?, ?, ?, ?)");
$marked_students = [];
$absent_students = [];

foreach ($all_students as $roll_no) {
    $status = in_array($roll_no, $present_students) ? 'Present' : 'Absent';

    // Check if attendance is already marked
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM attendance WHERE class_name = ? AND roll_no = ? AND subject_name = ? AND date = ?");
    $check_stmt->bind_param("siss", $class_name, $roll_no, $subject_name, $date);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $row = $check_result->fetch_assoc();
    $count = $row['count'] ?? 0;
    $check_stmt->close();

    if ($count == 0) { // If attendance not marked, insert it
        $insert_stmt->bind_param("sisss", $class_name, $roll_no, $subject_name, $date, $status);
        if ($insert_stmt->execute()) {
            if ($status == 'Present') {
                $marked_students[] = $roll_no;
            } else {
                if (strpos($subject_name, "-PR") === false) { 
                    // Only mark absent if it's a theory subject, NOT practical
                    $absent_students[] = $roll_no;
                }
            }
        }
    }
}
$insert_stmt->close();
$conn->close();

// Response with details
$response = [
    "success" => !empty($marked_students) || !empty($absent_students),
    "message" => "Attendance updated successfully",
    "marked_present" => $marked_students,
    "marked_absent" => $absent_students
];
echo json_encode($response);
?>
