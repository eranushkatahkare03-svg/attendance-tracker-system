<?php
// Database connection
include 'DB.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get student details from URL
$class_name = $_GET['class'];
$roll_no = $_GET['roll'];

// Fetch attendance data
$sql = "SELECT subject_name, COUNT(*) AS total_classes, 
        SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS attended 
        FROM attendance 
        WHERE class_name = '$class_name' AND roll_no = '$roll_no' 
        GROUP BY subject_name";

$result = $conn->query($sql);

$totalAttended = 0;
$totalClasses = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <link rel="stylesheet" href="attendance.css">
</head>
<body>
    <div class="container">
        <h2>Attendance Report</h2>
        <h3>Class: <?php echo htmlspecialchars($class_name); ?> | Roll No: <?php echo htmlspecialchars($roll_no); ?></h3>
        
        <table>
            <tr>
                <th>Subject</th>
                <th>Attendance</th>
            </tr>
            
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $subject = $row['subject_name'];
                    $attended = $row['attended'];
                    $total = $row['total_classes'];
                    
                    $totalAttended += $attended;
                    $totalClasses += $total;
                    
                    echo "<tr><td>$subject</td><td>$attended / $total</td></tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No attendance data found.</td></tr>";
            }
            ?>

        </table>

        <?php
        // Calculate overall attendance percentage
        $percentage = ($totalClasses > 0) ? ($totalAttended / $totalClasses) * 100 : 0;
        ?>
        <h3>Overall Attendance: <?php echo round($percentage, 2); ?>%</h3>
    </div>
</body>
</html>

<?php $conn->close(); ?>
