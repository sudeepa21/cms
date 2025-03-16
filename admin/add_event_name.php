<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_name'])) {
    $event_name = $_POST['event_name'];
    $stmt = $conn->prepare("INSERT INTO events (name) VALUES (?)");
    $stmt->bind_param("s", $event_name);
    $stmt->execute();
    echo "Event name added successfully.";
}
?>