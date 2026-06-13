<?php
include 'DB.php';
header('Content-Type: application/json'); 
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle DELETE request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll_no = $_POST['roll_no'];

    $sql = "DELETE FROM students WHERE roll_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $roll_no);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Student deleted successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error deleting student!"]);
    }
    exit;
}

// Handle GET request (fetch students)
if ($_SERVER["REQUEST_METHOD"] == "GET") {
 
    $class_name = $_GET['class'] ?? '';
    $batch = $_GET['batch'] ?? '';

  /*   // Validate input
    if (empty($class_name)) {
        echo json_encode(["success" => false, "message" => "Class name is required"]);
        exit;
    } */


if(empty($batch))
{
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

if(!empty($batch) && !empty($class_name))
{
    $sql = "SELECT roll_no, student_name FROM students WHERE class_name = ? and batch=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss",$class_name,$batch);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    echo json_encode(["success" => true, "students" => $students]);
    exit;
}

    /* // Base SQL Query
    $sql = "SELECT roll_no, student_name FROM students WHERE class_name = ?";
    $params = [$class_name];
    $types = "s"; // "s" for string
 */
    // Add batch condition if available
  /*   if (!empty($batch)) {
        $sql .= " AND batch = ?";
        $params = [$batch];
        $types .= "s"; // Another "s" for batch
    }

    // Prepare and bind query
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "SQL preparation error"]);
        exit;
    }
 */


   
}

?>