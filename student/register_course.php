<?php
session_start();
include '../db.php';

// Check student access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

// Handle course registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_SESSION['user_id'];
    $course_code = $_POST['course_code'];

    // Check if already enrolled
    $check = $conn->query("SELECT * FROM enrollments WHERE student_id='$student_id' AND course_code='$course_code'");
    
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO enrollments (student_id, course_code) VALUES ('$student_id', '$course_code')");
        echo "Successfully registered!";
    } else {
        echo "You are already enrolled in this course!";
    }
}

// Fetch all available courses
$courses = $conn->query("SELECT * FROM courses");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Register for a Course</title>
</head>
<body>
    <h2>Register for a Course</h2>
    <a href="dashboard.php">Back to Dashboard</a>

    <form method="post">
        <label>Select Course:</label>
        <select name="course_code" required>
            <?php while ($course = $courses->fetch_assoc()): ?>
                <option value="<?php echo $course['course_code']; ?>"><?php echo $course['course_name']; ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Register</button>
    </form>
</body>
</html>
