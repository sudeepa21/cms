<?php
session_start();
include '../db.php';

// Check lecturer access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: ../index.php");
    exit();
}

// Handle attendance submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_code = $_POST['course_code'];
    $student_ids = $_POST['student_ids']; // Comma-separated student IDs

    foreach (explode(",", $student_ids) as $student_id) {
        $student_id = trim($student_id);
        if (!empty($student_id)) {
            $conn->query("INSERT INTO attendance (course_code, student_id, attendance_date) VALUES ('$course_code', '$student_id', CURDATE())");
        }
    }
    echo "Attendance recorded.";
}

// Fetch lecturer's courses
$user_id = $_SESSION['user_id'];
$courses = $conn->query("SELECT * FROM courses WHERE lecturer_id='$user_id'");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Mark Attendance</title>
</head>
<body>
    <h2>Mark Attendance</h2>
    <a href="dashboard.php">Back to Dashboard</a>

    <form method="post">
        <label>Course:</label>
        <select name="course_code" required>
            <?php while ($course = $courses->fetch_assoc()): ?>
                <option value="<?php echo $course['course_code']; ?>"><?php echo $course['course_name']; ?></option>
            <?php endwhile; ?>
        </select>

        <label>Student IDs (comma-separated):</label>
        <input type="text" name="student_ids" required>

        <button type="submit">Submit Attendance</button>
    </form>
</body>
</html>
