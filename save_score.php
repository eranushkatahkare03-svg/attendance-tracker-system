<?php
include 'DB.php';


$player_name = $_POST['player_name'];
$score = (int)$_POST['score'];

$sql = "INSERT INTO high_scores (player_name, score) VALUES ('$player_name', '$score')";
$conn->query($sql);

echo json_encode(["status" => "success", "player_name" => $player_name, "score" => $score]);

$conn->close();
?>
