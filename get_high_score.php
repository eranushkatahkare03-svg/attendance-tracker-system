<?php
include 'DB.php';
$sql = "SELECT player_name, score FROM high_scores ORDER BY score DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["player_name" => $row['player_name'], "score" => $row['score']]);
} else {
    echo json_encode(["player_name" => "None", "score" => 0]);
}

$conn->close();
?>
