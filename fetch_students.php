<?php
include 'DB.php';

if (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];

    $sql = "SELECT s.id, s.roll_number, s.student_name, c.class_name, d.dept_name 
            FROM Students s
            JOIN Classes c ON s.class_id = c.id
            JOIN Departments d ON c.dept_id = d.id
            WHERE s.id IN (SELECT student_id FROM Attendance WHERE subject_id = ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<table border='1'>
            <tr>
                <th>Roll Number</th>
                <th>Name</th>
                <th>Class</th>
                <th>Department</th>
                <th>Attendance</th>
            </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['roll_number']}</td>
                <td>{$row['student_name']}</td>
                <td>{$row['class_name']}</td>
                <td>{$row['dept_name']}</td>
                <td>
                    <select name='attendance[{$row['id']}]'>
                        <option value='Present'>Present</option>
                        <option value='Absent'>Absent</option>
                        <option value='Late'>Late</option>
                    </select>
                </td>
              </tr>";
    }
    echo "</table>";
}

?>
