<?php
include "DB.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

$response = ["subjects" => []];

// Get 'class' parameter safely
$class_name = $_REQUEST['class'] ?? null;
$class_name = trim($class_name); // Trim whitespace

if (!empty($class_name)) {
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT subject_name FROM subjects WHERE class_name = ?");
    $stmt->bind_param("s", $class_name);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response["subjects"][] = $row['subject_name'];
    }
} else {
    $response["error"] = "Class parameter is missing or empty!";
}

// ✅ Debugging: Log the received parameter safely
file_put_contents("debug_log.txt", "Received class: " . ($class_name ?: "NULL") . "\n", FILE_APPEND);

// Return JSON response
echo json_encode($response);
?>
