<?php
// Ensure session starts only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if user is not logged in or is not a lecturer
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_name = htmlspecialchars($_SESSION['username']); // Sanitize username
$lecturer_id = htmlspecialchars($_SESSION['user_id']); // Sanitize lecturer ID

// Fetch profile picture safely
include '../db.php';
$stmt = $conn->prepare("SELECT COALESCE(profile_picture, '') AS profile_picture FROM users WHERE user_id = ?");
$stmt->bind_param("s", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $profile_picture = htmlspecialchars($row['profile_picture']); // Sanitize profile picture path
} else {
    $profile_picture = ''; // Default to empty if no profile picture is found
}

// Set profile image path
$profile_image_path = !empty($profile_picture) ? "../uploads/" . $profile_picture : "../assets/profile.png";
?>

<!-- Lecturer Header Section -->
<div class="lecturer-header">
    <div class="left">
        <h2 class="welcome-text">Welcome <b><?php echo $user_name; ?></b></h2>
        <span class="lecturer-id">Lecturer ID: <?php echo $lecturer_id; ?></span>
    </div>
    <div class="right">
        <div class="profile-section">
            <img src="<?php echo $profile_image_path; ?>" alt="Lecturer Profile" class="profile-icon">
            <span class="profile-name">Profile</span>
        </div>
    </div>
</div>

<style>
/* Updated Header Design */
.lecturer-header {
    background-color: #800020; /* Dark red background */
    color: white;
    padding: 15px 30px;
    width: calc(100% - 250px); /* Adjust width to fit sidebar */
    position: fixed;
    top: 0;
    left: 250px; /* Align with sidebar */
    z-index: 1000;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 4px solid #F8B400; /* Yellow border */
    font-family: 'Arial', sans-serif;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

/* Left Section - Welcome Text */
.left {
    display: flex;
    flex-direction: column;
}

.welcome-text {
    font-size: 24px;
    margin: 0;
    font-weight: bold;
}

.lecturer-id {
    font-size: 14px;
    font-weight: normal;
}

/* Right Section - Profile */
.right {
    display: flex;
    align-items: center;
}

.profile-section {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-right: 20px;
    cursor: pointer; /* Indicate interactivity */
}

.profile-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    border: 2px solid white;
    object-fit: cover;
}

.profile-name {
    font-size: 16px;
    font-weight: bold;
}

/* Responsive Adjustments */
@media (max-width: 992px) {
    .lecturer-header {
        width: 100%;
        left: 0;
        padding: 15px 20px;
    }

    .welcome-text {
        font-size: 20px;
    }

    .profile-icon {
        width: 40px;
        height: 40px;
    }

    .profile-name {
        font-size: 14px;
    }
}

@media (max-width: 768px) {
    .lecturer-header {
        flex-direction: column;
        align-items: flex-start;
        padding: 10px 15px;
    }

    .left {
        margin-bottom: 10px;
    }

    .welcome-text {
        font-size: 18px;
    }

    .lecturer-id {
        font-size: 12px;
    }

    .profile-section {
        padding-right: 0;
    }
}
</style>