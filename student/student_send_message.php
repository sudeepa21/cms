<?php
session_start();
include '../db.php';

// Ensure student is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$badge_id = isset($_POST['badge_id']) ? $_POST['badge_id'] : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate input
if (empty($badge_id) || empty($message)) {
    echo json_encode(["status" => "error", "message" => "Badge ID or message missing"]);
    exit();
}

// Insert message into database
$stmt = $conn->prepare("INSERT INTO chat_messages (user_id, badge_id, message, sent_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("sis", $user_id, $badge_id, $message);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
