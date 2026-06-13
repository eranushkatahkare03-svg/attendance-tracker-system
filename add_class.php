<?php
include 'DB.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = $_POST['class_name'];

    if (empty($class_name)) {
        echo json_encode(["success" => false, "message" => "Class name is required!"]);
        exit();
    }

    // Insert class into database
    $sql = "INSERT INTO classes (class_name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $class_name);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Class added successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Class already exists!"]);
    }
}
?>
