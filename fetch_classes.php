<?php
include 'DB.php'; // Database connection

header("Content-Type: application/json"); // Ensure JSON response

$query = "SELECT class_name FROM classes";
$result = $conn->query($query);

$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = $row['class_name']; // Fetch only class_name
}

$conn->close();

echo json_encode($classes);
?>

