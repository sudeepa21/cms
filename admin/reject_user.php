<?php
session_start();
include '../db.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the request_id is provided
    if (isset($_POST['request_id'])) {
        $request_id = intval($_POST['request_id']); // Get the resource request ID

        // Update the status of the resource request to 'denied'
        $stmt = $conn->prepare("UPDATE resource_requests SET status = 'denied' WHERE id = ?");
        $stmt->bind_param("i", $request_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Resource request denied successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to deny resource request"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Resource request ID not provided"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close();
?>