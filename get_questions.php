<?php
include 'DB.php';


$difficulty = $_GET['difficulty'];

$sql = "SELECT id, question, option1, option2, option3, option4, correct_option FROM questions WHERE difficulty = '$difficulty' ORDER BY RAND() LIMIT 5";
$result = $conn->query($sql);

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

echo json_encode($questions);
$conn->close();
?>
