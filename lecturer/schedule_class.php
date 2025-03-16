<?php
session_start();
include '../db.php';

// Check lecturer access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: ../index.php");
    exit();
}

// Handle class scheduling form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_code = $_POST['course_code'];
    $class_date = $_POST['class_date'];
    $class_time = $_POST['class_time'];
    $room = $_POST['room'];

    // Insert class into database
    $stmt = $conn->prepare("INSERT INTO scheduled_classes (course_code, class_date, class_time, room) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $course_code, $class_date, $class_time, $room);
    
    if ($stmt->execute()) {
        echo "Class scheduled successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch lecturer's courses
$user_id = $_SESSION['user_id'];
$courses = $conn->query("SELECT * FROM courses WHERE lecturer_id='$user_id'");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Schedule a Class</title>
</head>
<body>
    <h2>Schedule a Class</h2>
    <a href="dashboard.php">Back to Dashboard</a>

    <form method="post">
        <label>Course:</label>
        <select name="course_code" required>
            <?php while ($course = $courses->fetch_assoc()): ?>
                <option value="<?php echo $course['course_code']; ?>"><?php echo $course['course_name']; ?></option>
            <?php endwhile; ?>
        </select>

        <label>Date:</label>
        <input type="date" name="class_date" required>

        <label>Time:</label>
        <input type="time" name="class_time" required>

        <label>Room:</label>
        <input type="text" name="room" required>

        <button type="submit">Schedule Class</button>
    </form>
</body>
</html>
