<?php
include 'DB.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['attendance'])) {
    foreach ($_POST['attendance'] as $student_id => $status) {
        $subject_id = $_POST['subject_id'];
        $date = date('Y-m-d');

        $sql = "INSERT INTO Attendance (student_id, subject_id, date, status) 
                VALUES (?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE status=?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisss", $student_id, $subject_id, $date, $status, $status);
        $stmt->execute();
    }
    echo "Attendance recorded successfully!";
}
?>

