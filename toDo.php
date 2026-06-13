<?php include 'DB.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        table { margin: auto; border-collapse: collapse; width: 50%; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #4CAF50; color: white; }
        button { margin: 5px; padding: 5px 10px; }
    </style>
</head>
<body>
    <h2>Simple To-Do List</h2>
    <form action="add_task.php" method="POST">
        <input type="text" name="task" placeholder="Enter task..." required>
        <button type="submit">Add Task</button>
    </form>
    <table>
        <tr><th>Task</th><th>Status</th><th>Actions</th></tr>
        <?php
        $result = $conn->query("SELECT * FROM tasks ORDER BY id DESC");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['task']}</td>
                    <td>{$row['status']}</td>
                    <td>
                        <a href=mark_done.php?id={$row['id']}'>✔</a> 
                        <a href='delete_task.php?id={$row['id']}' onclick='return confirm(\"Delete this task?\");'>❌</a>
                    </td>
                  </tr>";
        }
        ?>
    </table>
</body>
</html>
