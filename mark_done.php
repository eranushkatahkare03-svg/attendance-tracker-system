<?php
include 'DB.php';
if (isset($_GET["id"])) {
    $id = intval($_GET["id"]); // Ensure ID is an integer

    $stmt = $conn->prepare("UPDATE tasks SET status='completed' WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: toDo.php");
        exit();
    } else {
        echo "Error updating task status.";
    }
}
?>
