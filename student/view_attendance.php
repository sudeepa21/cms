<?php
session_start();
include '../db.php';

// Check student access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

// Fetch attendance records
$user_id = $_SESSION['user_id'];
$attendance = $conn->query("SELECT a.*, c.course_name FROM attendance a 
                            JOIN courses c ON a.course_code = c.course_code 
                            WHERE a.student_id='$user_id'");

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Attendance</title>
</head>
<body>
    <h2>View Attendance</h2>
    <a href="dashboard.php">Back to Dashboard</a>

    <table border="1">
        <tr>
            <th>Course</th>
            <th>Date</th>
        </tr>
        <?php while ($record = $attendance->fetch_assoc()): ?>
        <tr>
            <td><?php echo $record['course_name']; ?></td>
            <td><?php echo $record['attendance_date']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
