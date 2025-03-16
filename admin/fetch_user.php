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

    // Debugging: Log the uni_ID
    error_log("Searching for user with uni_ID: " . $uni_ID);

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT uni_ID, first_name, last_name, role, mobile, email FROM users WHERE uni_ID = ?");
    if (!$stmt) {
        $error = $conn->error;
        error_log("Database error: " . $error); // Log the error
        echo json_encode(["status" => "error", "message" => "Database error: " . $error]);
        exit();
    }

    $stmt->bind_param("s", $uni_ID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user was found
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Debugging: Log the fetched user data
        error_log("Fetched user data: " . print_r($user, true));

        // Ensure all required fields are present
        if (!isset($user['uni_ID']) || !isset($user['first_name']) || !isset($user['last_name']) || !isset($user['role']) || !isset($user['mobile']) || !isset($user['email'])) {
            echo json_encode(["status" => "error", "message" => "Incomplete user data in the database"]);
            exit();
        }

        echo json_encode([
            "status" => "success",
            "data" => [
                "uni_ID" => $user['uni_ID'],
                "first_name" => $user['first_name'],
                "last_name" => $user['last_name'],
                "role" => $user['role'],
                "mobile" => $user['mobile'],
                "email" => $user['email']
            ]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

// Close the database connection
$conn->close();
?>