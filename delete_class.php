<?php
include 'DB.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = $_POST['class_name'];

    $sql = "DELETE FROM classes WHERE class_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $class_name);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Class deleted successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error deleting class!"]);
    }
}
?>
