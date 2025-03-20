<?php
session_start();
include '../db.php';

// Ensure user is logged in and has lecturer privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get logged-in user_id from session
$username = $_SESSION['username'];

// Debugging: Check if session user_id is set correctly
if (!$user_id) {
    die("User ID is missing from the session.");
}

// Fetch badge names from 'badges' table
$badges_result = $conn->query("SELECT badge_id, badge_name FROM badges");
$badges = $badges_result->fetch_all(MYSQLI_ASSOC);

// Fetch course names from 'courses' table
$courses_result = $conn->query("SELECT course_id, course_name FROM courses");
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);

// Fetch class-rooms from 'class_rooms' table
$classrooms_result = $conn->query("SELECT class_id, name FROM class_rooms");
$classrooms = $classrooms_result->fetch_all(MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $badge_id = $_POST['badge'];
    $course_id = $_POST['module'];
    $requested_date = $_POST['date'];
    $class_id = $_POST['classroom'];
    $other_resources = $_POST['other_resources'];

    // Debugging: Print user_id and form data
    error_log("User ID: " . $user_id);
    error_log("Form Data: badge_id=$badge_id, course_id=$course_id, requested_date=$requested_date, class_id=$class_id, other_resources=$other_resources");

    // Insert booking request into resource_requests table
    $stmt = $conn->prepare("INSERT INTO resource_requests (badge_id, course_id, requested_date, class_id, other_resources, status, user_id) VALUES (?, ?, ?, ?, ?, 'pending', ?)");
    if (!$stmt) {
        die("Error preparing query: " . $conn->error);
    }
    $stmt->bind_param("ssssss", $badge_id, $course_id, $requested_date, $class_id, $other_resources, $user_id); // Pass user_id as a string
    if (!$stmt->execute()) {
        die("Error executing query: " . $stmt->error);
    }
    $stmt->close();

    echo "<script>alert('Resource booking request submitted successfully!'); window.location.href='lec_resource_booking.php';</script>";
}

// Fetch booking requests for the logged-in lecturer
$booking_requests = [];
$booking_query = $conn->prepare("SELECT requested_date, class_id, other_resources, status FROM resource_requests WHERE user_id = ?");
$booking_query->bind_param("s", $user_id); // Use "s" for VARCHAR type
$booking_query->execute();
$booking_result = $booking_query->get_result();

if ($booking_result->num_rows > 0) {
    $booking_requests = $booking_result->fetch_all(MYSQLI_ASSOC);
}
$booking_query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Resource Booking | Smart Campus</title>
    
    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

    <?php include 'lecturer-header.php'; ?>
    <?php include 'lec_sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <h3>Resource Booking</h3>
            <p class="text-muted">(Lecture Halls, Labs)</p>

            <!-- Resource Booking Form -->
            <form method="POST" action="lec_resource_booking.php">
                <div class="mb-3">
                    <label>Select Badge</label>
                    <select name="badge" class="form-control" required>
                        <option value="" disabled selected>Select Badge</option>
                        <?php foreach ($badges as $badge): ?>
                            <option value="<?php echo $badge['badge_id']; ?>"><?php echo $badge['badge_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Select Your Module</label>
                    <select name="module" class="form-control" required>
                        <option value="" disabled selected>Select Module</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo $course['course_id']; ?>"><?php echo $course['course_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Select Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Select Time</label>
                    <input type="time" name="time" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Class Rooms</label>
                    <select name="classroom" class="form-control" required>
                        <option value="" disabled selected>Select Classroom</option>
                        <?php foreach ($classrooms as $classroom): ?>
                            <option value="<?php echo $classroom['class_id']; ?>"><?php echo $classroom['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Reserve equipment or other facilities</label>
                    <input type="text" name="other_resources" class="form-control" placeholder="Enter additional resources (if any)">
                </div>

                <div class="text-start">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>

            <div class="mt-5">
                <h3>Booking Status</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Requested Date</th>
                            <th>Class Room</th>
                            <th>Other Resources</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($booking_requests)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No booking requests found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($booking_requests as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['requested_date']); ?></td>
                                    <td><?php echo htmlspecialchars($request['class_id']); ?></td>
                                    <td><?php echo htmlspecialchars($request['other_resources']); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php echo $request['status'] === 'approved' ? 'bg-success' : 
                                                  ($request['status'] === 'denied' ? 'bg-danger' : 'bg-warning'); ?>">
                                            <?php echo htmlspecialchars($request['status']); ?>
                                        </span>
                                    </td>
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
