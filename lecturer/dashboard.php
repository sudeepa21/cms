<?php

session_start();
include '../db.php';

// Ensure user is logged in and has lecturer privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch assigned courses for the lecturer
$query = "
    SELECT c.course_id, c.course_name 
    FROM lecturer l
    JOIN courses c ON l.course_id = c.course_id
    WHERE l.user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$assigned_courses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard | Smart Campus</title>
    
    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

    <?php include 'lecturer-header.php'; ?>
    <?php include 'lec_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <h3>Assigned Courses</h3>
            <p class="text-muted">Current semester</p>

            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Module ID</th>
                        <th>Module Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($assigned_courses)): ?>
                        <?php foreach ($assigned_courses as $course): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($course['course_id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center text-muted">No assigned courses available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>