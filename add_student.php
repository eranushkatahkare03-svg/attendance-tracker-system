<?php
include 'DB.php';
header('Content-Type: application/json'); 
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log received data
file_put_contents("debug.log", print_r($_POST, true));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST)) {
        die(json_encode(["success" => false, "message" => "❌ No data received! Check Content-Type."]));
    }

    $class_name = trim($_POST['class_name'] ?? '');
    $student_name = trim($_POST['student_name'] ?? '');
    $roll_no = trim($_POST['roll_no'] ?? '');
    $parent_email = trim($_POST['parent_email'] ?? '');
    $batch = trim($_POST['batch'] ?? '');

    // ✅ Check if any field is empty
    if (empty($class_name) || empty($student_name) || empty($roll_no) || empty($parent_email) || empty($batch)) {
        die(json_encode(["success" => false, "message" => "❌ All fields are required!"]));
    }

    // Check if roll number already exists
    $check_sql = "SELECT * FROM students WHERE class_name = ? AND roll_no = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $class_name, $roll_no);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        die(json_encode(["success" => false, "message" => "⚠️ Student with this Roll No already exists!"]));
    }

    // Insert student with parent's contact
    $sql = "INSERT INTO students (class_name, roll_no, student_name, parent_email, batch) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $class_name, $roll_no, $student_name, $parent_email, $batch);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "✅ Student added successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "❌ Error adding student!"]);
    }

    $stmt->close();
    $conn->close();
}
?>
