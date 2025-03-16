<?php
session_start();
include '../db.php';

// Ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

// Fetch the student's enrolled badge IDs
$student_id = $_SESSION['user_id']; // Assuming user_id is the student's ID
$enrolled_badges = $conn->query("SELECT badge_id FROM enrollments WHERE student_id = '$student_id'");

// Create a list of badge IDs (including 0 for all students)
$badge_ids = [0]; // Include badge_id = 0 for all students
while ($row = $enrolled_badges->fetch_assoc()) {
    $badge_ids[] = $row['badge_id'];
}

// Convert the list of badge IDs to a comma-separated string for the SQL query
$badge_ids_str = implode(",", $badge_ids);

// Fetch notifications relevant to the student's badges
$notifications = $conn->query("SELECT * FROM notifications WHERE badge_id IN ($badge_ids_str) ORDER BY created_at DESC LIMIT 10");

if ($notifications->num_rows > 0) {
    while ($notification = $notifications->fetch_assoc()) {
        echo '
        <div class="notification-card p-3" data-notification-id="' . $notification['id'] . '">
            <div class="d-flex justify-content-between align-items-center">
                <span class="notification-badge ' . $notification['type'] . '">
                    ' . strtoupper($notification['type']) . '
                </span>
                <form method="POST" action="student_notification.php" class="d-inline">
                    <input type="hidden" name="notification_id" value="' . $notification['id'] . '">
                    <button type="submit" name="delete_notification" class="delete-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
            <p class="notification-message">' . $notification['message'] . '</p>
            <small class="notification-time">
                ' . date('M d, Y H:i', strtotime($notification['created_at'])) . '
            </small>
        </div>';
    }
} else {
    echo '
    <div class="empty-state">
        <i class="fas fa-bell-slash"></i>
        <p>No new notifications.</p>
    </div>';
}
?>