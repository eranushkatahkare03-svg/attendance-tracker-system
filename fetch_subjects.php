<?php
include 'DB.php'; // Ensure DB.php connects to the database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = trim($_POST['class_name']);

    if (empty($class_name)) {
        echo json_encode(["success" => false, "message" => "Class name is required!"]);
        exit();
    }

    $sql = "SELECT subject_name FROM subjects WHERE class_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $class_name);
    $stmt->execute();
    $result = $stmt->get_result();

    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row['subject_name'];
    }

    echo json_encode(["success" => true, "subjects" => $subjects]);
}
?>
