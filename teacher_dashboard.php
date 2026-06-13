<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="teacher.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <h2>Teacher Dashboard</h2>

        <label for="class-select">Select Class:</label>
        <select id="class-select">
            <option value="">-- Select Class --</option>
            <?php
                include "db.php"; // Database connection
                $result = $conn->query("SELECT DISTINCT class FROM students");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['class'] . "'>" . $row['class'] . "</option>";
                }
            ?>
        </select>

        <label for="subject-select">Select Subject:</label>
        <select id="subject-select">
            <option value="">-- Select Subject --</option>
            <?php
                $result = $conn->query("SELECT DISTINCT subject FROM subjects");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['subject'] . "'>" . $row['subject'] . "</option>";
                }
            ?>
        </select>

        <label>Attendance Type:</label>
        <input type="radio" name="type" value="theory" checked> Theory
        <input type="radio" name="type" value="lab"> Lab

        <div id="batch-selection" style="display: none;">
            <label>Select Batch:</label>
            <select id="batch-select">
                <option value="Batch-1">Batch 1</option>
                <option value="Batch-2">Batch 2</option>
                <option value="Batch-3">Batch 3</option>
            </select>
        </div>

        <button id="load-students">Load Students</button>

        <div id="students-list"></div>

        <button id="mark-attendance-btn" style="display: none;">Mark Attendance</button>
        
        <button onclick="logout()">Logout</button>
    </div>

    <script src="t.js"></script>
</body>
</html>
