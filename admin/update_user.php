<?php
session_start();
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are provided
    if (!isset($_POST['uni_ID']) || !isset($_POST['first_name']) || !isset($_POST['last_name']) || !isset($_POST['role']) || !isset($_POST['mobile']) || !isset($_POST['email'])) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit();
    }

    $uni_ID = $_POST['uni_ID'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $role = $_POST['role'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];

    // Prepare and execute the query
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, role = ?, mobile = ?, email = ? WHERE uni_ID = ?");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("ssssss", $first_name, $last_name, $role, $mobile, $email, $uni_ID);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update user"]);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

// Close the database connection
$conn->close();
?>