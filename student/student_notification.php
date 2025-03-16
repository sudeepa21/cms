<?php
session_start();
include '../db.php';

// Ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

// Fetch notifications for students
$notifications = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Notifications | Smart Campus</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* Custom styles for notifications */
        .notification-container {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .notification-container::-webkit-scrollbar {
            width: 8px;
        }

        .notification-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .notification-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .notification-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .notification-badge {
            font-size: 0.9em;
            padding: 5px 10px;
            border-radius: 12px;
        }

        .notification-badge.event {
            background-color: rgba(232, 16, 27, 0.77);
            color: white;
        }

        .notification-badge.alert {
            background-color: #dc3545;
            color: white;
        }

        .notification-message {
            font-size: 1em;
            color: #333;
            margin: 10px 0;
        }

        .notification-time {
            font-size: 0.85em;
            color: #777;
        }

        .remove-btn {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            transition: color 0.2s;
        }

        .remove-btn:hover {
            color: #a71d2a;
        }

        .empty-state {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 2em;
            margin-bottom: 10px;
            color: #6c757d;
        }
    </style>
</head>
<body>

<?php include 'student_header.php'; ?>
<?php include 'student_sidebar.php'; ?>

    <div class="main-content">
        
        <h3 class="mt-4">Notifications <i class="fas fa-bell text-danger"></i></h3>

        <div class="notification-container">
            <?php if ($notifications->num_rows > 0): ?>
                <?php while ($notification = $notifications->fetch_assoc()): ?>
                <div class="notification-card p-3" data-notification-id="<?php echo $notification['id']; ?>">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="notification-badge <?php echo $notification['type']; ?>">
                            <?php echo strtoupper($notification['type']); ?>
                        </span>
                        <button class="remove-btn" onclick="removeNotification(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <p class="notification-message"><?php echo $notification['message']; ?></p>
                    <small class="notification-time">
                        <?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?>
                    </small>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <p>No new notifications.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        let currentNotificationIds = [];

        // Get the current user ID from PHP session
        let userId = "<?php echo $_SESSION['user_id']; ?>";

        // Load dismissed notifications for the current user from local storage
        let dismissedNotifications = JSON.parse(localStorage.getItem(`dismissedNotifications_${userId}`)) || [];

        // Hide notifications that have been dismissed by the current user
        $('.notification-card').each(function() {
            let notificationId = $(this).data('notification-id');
            if (dismissedNotifications.includes(notificationId)) {
                $(this).hide();
            }
        });

        // Function to fetch notifications
        function fetchNotifications() {
            $.ajax({
                url: "fetch_notifications.php",
                method: "GET",
                success: function(data) {
                    const newNotificationIds = $(data).find('.notification-card').map(function() {
                        return $(this).data('notification-id');
                    }).get();

                    // Only update the UI if the notification IDs have changed
                    if (JSON.stringify(newNotificationIds) !== JSON.stringify(currentNotificationIds)) {
                        $(".notification-container").html(data);
                        currentNotificationIds = newNotificationIds;

                        // Re-hide dismissed notifications for the current user after refresh
                        $('.notification-card').each(function() {
                            let notificationId = $(this).data('notification-id');
                            if (dismissedNotifications.includes(notificationId)) {
                                $(this).hide();
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching notifications:", error);
                }
            });
        }

        // Refresh notifications every 5 seconds
        setInterval(fetchNotifications, 5000);
    });

    // Function to remove notification from the UI and store in local storage for the current user
    function removeNotification(button) {
        let notificationId = $(button).closest('.notification-card').data('notification-id');
        let userId = "<?php echo $_SESSION['user_id']; ?>";
        let dismissedNotifications = JSON.parse(localStorage.getItem(`dismissedNotifications_${userId}`)) || [];

        // Add the notification ID to the dismissed list for the current user
        if (!dismissedNotifications.includes(notificationId)) {
            dismissedNotifications.push(notificationId);
            localStorage.setItem(`dismissedNotifications_${userId}`, JSON.stringify(dismissedNotifications));
        }

        // Hide the notification
        $(button).closest('.notification-card').fadeOut(300, function() {
            $(this).remove();
        });
    }
    </script>

</body>
</html>