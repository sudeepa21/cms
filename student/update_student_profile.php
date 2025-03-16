<?php
session_start();
include '../db.php';

// Ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');

// Handle profile picture removal
if (isset($_POST['remove_profile_picture'])) {
    // Fetch the current profile picture
    $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $stmt->bind_result($profile_picture);
    $stmt->fetch();
    $stmt->close();

    // Delete the profile picture file
    if (!empty($profile_picture) && file_exists("../uploads/$profile_picture")) {
        unlink("../uploads/$profile_picture");
    }

    // Update the database to remove the profile picture
    $stmt = $conn->prepare("UPDATE users SET profile_picture = NULL WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile picture removed successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to remove profile picture"]);
    }
    $stmt->close();
    exit();
}

// Validate inputs
if (empty($first_name) || empty($last_name) || empty($email) || empty($mobile)) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email format"]);
    exit();
}

// Validate mobile number format (10 digits starting with 0)
if (!preg_match("/^0\d{9}$/", $mobile)) {
    echo json_encode(["status" => "error", "message" => "Mobile number must be 10 digits and start with 0"]);
    exit();
}

// Check if the email is already in use by another user
$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit();
}

$stmt->bind_param("ss", $email, $user_id); // Use "ss" for two strings
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email is already in use by another user"]);
    $stmt->close();
    exit();
}
$stmt->close();

// Handle profile picture upload
$profile_picture = null;
if (!empty($_FILES['profile_picture']['name'])) {
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES['profile_picture']['name']);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is an image
    $check = getimagesize($_FILES['profile_picture']['tmp_name']);
    if ($check === false) {
        echo json_encode(["status" => "error", "message" => "File is not an image"]);
        exit();
    }

    // Check file size (max 5MB)
    if ($_FILES['profile_picture']['size'] > 5000000) {
        echo json_encode(["status" => "error", "message" => "File is too large (max 5MB)"]);
        exit();
    }

    // Allow only specific file formats
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        echo json_encode(["status" => "error", "message" => "Only JPG, JPEG, PNG, and GIF files are allowed"]);
        exit();
    }

    // Upload the file
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
        $profile_picture = basename($_FILES['profile_picture']['name']);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to upload profile picture"]);
        exit();
    }
}

// Update student profile in the database
if ($profile_picture) {
    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, mobile=?, profile_picture=? WHERE user_id=?");
    $stmt->bind_param("ssssss", $first_name, $last_name, $email, $mobile, $profile_picture, $user_id);
} else {
    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, mobile=? WHERE user_id=?");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $mobile, $user_id);
}

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update profile. Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>