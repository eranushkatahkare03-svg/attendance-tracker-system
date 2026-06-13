<?php
include 'DB.php';
$id = $_GET["id"];
$conn->query("DELETE FROM tasks WHERE id=$id");
header("Location: toDo.php");
exit();
?>
