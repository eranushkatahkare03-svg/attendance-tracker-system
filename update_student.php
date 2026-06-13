<?php
include 'DB.php';
header('Content-Type: application/json'); 
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the received data
file_put_contents("debug.log", print_r($_POST, true)); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll_no = $_POST['roll_no'];
    $new_name = $_POST['name'];

    $sql = "UPDATE students SET student_name = ? WHERE roll_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_name, $roll_no);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Student updated successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error updating student!"]);
    }
}
?>
