<?php
session_start();
include '../db.php';

// Enable or disable debugging
$debug = false;

// Ensure user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Use user_id directly from the session
$assigned_courses = []; // Initialize an empty array for courses

// Debugging: Print user_id
if ($debug) echo "<p>Debug: Logged-in User ID = $user_id</p>";

// Step 1: Check if user_id exists in the users table
$user_query = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
$user_query->bind_param("s", $user_id); // Bind as string (VARCHAR)
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();
$user_query->close();

if (!$user) {
    die("<p class='text-danger'>Error: User not found in users table.</p>");
}

// Debugging: Print user_id from users table
if ($debug) echo "<p>Debug: User found in users table.</p>";

// Step 2: Check if user_id exists in the students table
$student_query = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
$student_query->bind_param("s", $user_id); // Bind as string (VARCHAR)
$student_query->execute();
$student_result = $student_query->get_result();
$student = $student_result->fetch_assoc();
$student_query->close();

if (!$student) {
    // Debugging: User not found in students table
    if ($debug) echo "<p>Debug: User not found in students table.</p>";
} else {
    $student_id = $student['student_id'];

    // Debugging: Print student_id
    if ($debug) echo "<p>Debug: Student ID = $student_id</p>";

    // Step 3: Fetch assigned courses and badge details for the student
    $query = "
        SELECT c.course_name, b.badge_name 
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        LEFT JOIN badges b ON e.badge_id = b.badge_id
        WHERE e.student_id = ? 
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id); // Bind student_id as integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Debugging: Print the number of courses found
    $num_courses = $result->num_rows;
    if ($debug) echo "<p>Debug: Number of courses found = $num_courses</p>";

    while ($row = $result->fetch_assoc()) {
        $assigned_courses[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | Smart Campus</title>
    
    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">

    <style>
        /* Adjust padding and margins to prevent overlap */
        .main-content { 
            padding: 10px 20px; /* Reduced padding */
            margin-top: 60px; /* Add margin to prevent overlap with header */
        }
        .table-container { 
            margin-top: 20px; 
        }
        .table thead { 
            background-color: #800020; /* Dark red header */
            color: white; 
        }
        .table-striped tbody tr:nth-of-type(odd) { 
            background-color: rgba(128, 0, 32, 0.05); /* Light red stripes */
        }
        .table-bordered { 
            border: 1px solid #dee2e6; 
        }
        .no-data { 
            color: red; 
            font-weight: bold; 
            text-align: center; 
        }
    </style>
</head>
<body>

    <?php include 'student_header.php'; ?>
    <?php include 'student_sidebar.php'; ?>

    <div class="main-content">
        <div class="container mt-4">
            <h3>Registerd Courses</h3>
            <p class="text-muted">Current semester</p>

            <div class="table-container">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Course Name</th>
                            <th>Badge Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($assigned_courses)): ?>
                            <tr>
                                <td colspan="2" class="no-data">No assigned courses found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($assigned_courses as $course): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                    <td><?php echo htmlspecialchars($course['badge_name'] ?? 'No Badge'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>