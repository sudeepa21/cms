<?php
session_start();
include '../db.php';

// Ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

// Fetch notifications for the logged-in student
$student_id = $_SESSION['user_id'];
$notifications = $conn->query("SELECT * FROM notifications WHERE user_id = '$student_id' ORDER BY created_at DESC LIMIT 10");

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