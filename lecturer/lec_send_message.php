<?php
session_start();
include '../db.php';

// Ensure lecturer is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id']; // Logged-in lecturer's ID
$receiver_badge_id = isset($_POST['receiver_badge_id']) ? $_POST['receiver_badge_id'] : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

$uploadDir = "../uploads/"; // Directory to store uploaded files
$attachments = []; // Store uploaded file names

// Validate input
if (empty($receiver_badge_id) || (empty($message) && empty($_FILES['files']))) {
    echo json_encode(["status" => "error", "message" => "Badge ID or message missing"]);
    exit();
}

// Handle file uploads
if (!empty($_FILES['files'])) {
    foreach ($_FILES['files']['name'] as $key => $fileName) {
        $tmpName = $_FILES['files']['tmp_name'][$key];
        $uniqueFileName = time() . "_" . basename($fileName); // Generate a unique file name
        $targetFilePath = $uploadDir . $uniqueFileName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($tmpName, $targetFilePath)) {
            $attachments[] = $uniqueFileName; // Save uploaded file name
        } else {
            echo json_encode(["status" => "error", "message" => "File upload failed"]);
            exit();
        }
    }
}

// Convert attachment names to JSON for storage
$attachmentsJson = !empty($attachments) ? json_encode($attachments) : null;

// Insert message into the database
$stmt = $conn->prepare("INSERT INTO chat_messages (user_id, badge_id, message, attachments, sent_at) VALUES (?, ?, ?, ?, NOW())");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit();
}

$stmt->bind_param("siss", $user_id, $receiver_badge_id, $message, $attachmentsJson);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>