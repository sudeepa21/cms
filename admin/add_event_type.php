<?php
session_start();
include '../db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

// Handle the addition of a new event type
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_type'])) {
    $event_type = $_POST['event_type'];
    $stmt = $conn->prepare("INSERT INTO events (type) VALUES (?)");
    $stmt->bind_param("s", $event_type);
    $stmt->execute();
    echo "Event type added successfully.";
}
?>