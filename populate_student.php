<?php
include 'DB.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
 
    $class_name = $_GET['class'] ?? '';

    if(empty($class_name))
    {
        die(json_encode(["success" => false, "message" => "No data received! Check Content-Type."]));
    }

    $sql = "SELECT roll_no, student_name FROM students WHERE class_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s",$class_name);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    echo json_encode(["success" => true, "students" => $students]);
    exit;
}
?>
