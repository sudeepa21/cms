<?php
session_start();
include '../db.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['uni_ID'])) {
    $uniID = $_POST['uni_ID'];

    // Delete the user from the `users` table
    $stmt = $conn->prepare("DELETE FROM users WHERE uni_ID = ?");
    $stmt->bind_param("s", $uniID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "User deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete user"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "University ID not provided"]);
}

$conn->close();
?>