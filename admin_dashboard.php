<?php
// Author: Josiah de Leon
// Class: CSC 350
// Instructor: Dr. Galathara Kahanda
// Filename: admin_dashboard.php

session_start();

include 'db_connect.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy(); // Destroy the session to log out the admin
    header("Location: admin_login.php");
    exit();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$sql = "SELECT g.group_number, g.project_title, g.member_names, AVG(e.total) as average_grade,
        CASE 
            WHEN AVG(e.total) >= 90 THEN 'A'
            WHEN AVG(e.total) >= 80 THEN 'B'
            WHEN AVG(e.total) >= 70 THEN 'C'
            WHEN AVG(e.total) >= 60 THEN 'D'
            ELSE 'F'
        END as letter_grade
        FROM evaluations e
        JOIN groups g ON e.group_id = g.group_id
        GROUP BY g.group_id";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Group Averages</title>
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .logout-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Group Averages</h1>
    <table>
        <tr>
            <th>Group Number</th>
            <th>Project Title</th>
            <th>Members</th>
            <th>Average Grade</th>
            <th>Letter Grade</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['group_number']; ?></td>
            <td><?php echo $row['project_title']; ?></td>
            <td><?php echo $row['member_names']; ?></td>
            <td><?php echo number_format($row['average_grade'], 2); ?></td>
            <td><?php echo $row['letter_grade']; ?></td>
        </tr>
        <?php } ?>
    </table>

    <div class="logout-btn">
        <a href="?logout=true"><button>Logout</button></a>
    </div>

    <?php mysqli_close($conn); ?>
</body>
</html>
