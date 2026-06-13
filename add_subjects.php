<?php
include 'DB.php'; // Ensure DB.php sets up $conn properly

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = trim($_POST['class_name']);
    $subjects = trim($_POST['subjects']);

    if (empty($class_name) || empty($subjects)) {
        echo json_encode(["success" => false, "message" => "Class name and subjects are required!"]);
        exit();
    }

    // Split subjects if they are comma-separated
    $subjectArray = explode(',', $subjects);
    $success = true;

    foreach ($subjectArray as $subject) {
        $subject = trim($subject);

        $sql = "INSERT INTO subjects (class_name, subject_name) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $class_name, $subject);

        if (!$stmt->execute()) {
            $success = false;
        }
    }

    if ($success) {
        echo json_encode(["success" => true, "message" => "Subjects added successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error adding subjects. Some may already exist."]);
    }
}
?>
