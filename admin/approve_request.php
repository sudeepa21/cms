<?php
session_start();
include '../db.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Update the status to 'approved'
    $conn->query("UPDATE resource_requests SET status='approved' WHERE id='$id'");

    // Redirect back to the resource requests page with a success message
    header("Location: resource_requests.php?message=Request+approved+successfully");
    exit();
} else {
    // Redirect back if no ID is provided
    header("Location: resource_requests.php");
    exit();
}
?>