<?php
session_start();
include '../db.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $uni_ID = trim($_POST['uni_ID']);

    if (empty($uni_ID)) {
        echo json_encode(["status" => "error", "message" => "University ID is required"]);
        exit();
    }

    // Update the user's status to 'approved'
    $stmt = $conn->prepare("UPDATE users SET status = 'approved' WHERE uni_ID = ?");
    $stmt->bind_param("s", $uni_ID);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User approved successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to approve user"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>