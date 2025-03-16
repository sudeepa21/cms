<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Campus</title>
    <!-- Import Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Import Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar h2.text-center {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1.5rem;
            margin: 0; /* Remove default margin */
            padding: 20px 0; /* Add slight padding for spacing */
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 5px; /* Add consistent spacing between links */
        }

        .sidebar a {
            text-decoration: none;
            color: inherit;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: rgba(228, 33, 66, 0.53); /* Add hover effect */
        }

        .sidebar a.active {
            background-color: rgb(228, 33, 65); /* Highlight active link */
            color: white;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2 class="text-center"><i class="fas fa-graduation-cap"></i> Smart Campus</h2>

        <!-- Student Pages Links -->
        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Home
        </a>
        <a href="student_calendar.php" class="<?= basename($_SERVER['PHP_SELF']) == 'student_calendar.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-alt"></i> Calendar
        </a>
        <a href="student_chat.php" class="<?= basename($_SERVER['PHP_SELF']) == 'student_chat.php' ? 'active' : ''; ?>">
            <i class="fas fa-comments"></i> Chat
        </a>
        <a href="student_notification.php" class="<?= basename($_SERVER['PHP_SELF']) == 'student_notification.php' ? 'active' : ''; ?>">
            <i class="fas fa-bell"></i> Notifications
        </a>
        <a href="student_profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'student_profile.php' ? 'active' : ''; ?>">
            <i class="fas fa-user"></i> My Profile
        </a>
        <a href="../logout.php">
            <i class="fas fa-sign-out-alt"></i> Log Out
        </a>
    </div>
</body>
</html>