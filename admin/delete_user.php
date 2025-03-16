<?php
session_start();
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if uni_ID is provided
    if (!isset($_POST['uni_ID']) || empty($_POST['uni_ID'])) {
        echo json_encode(["status" => "error", "message" => "University ID is required"]);
        exit();
    }

    $uni_ID = $_POST['uni_ID'];

    // Prepare and execute the query
    $stmt = $conn->prepare("DELETE FROM users WHERE uni_ID = ?");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("s", $uni_ID);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete user"]);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

// Close the database connection
$conn->close();
?>