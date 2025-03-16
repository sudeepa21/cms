<?php
session_start();
include '../db.php';

// Handle adding event type via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_event_type'])) {
    $new_event_type = trim($_POST['new_event_type']);
    if (!empty($new_event_type)) {
        $stmt = $conn->prepare("INSERT INTO events (type) VALUES (?)");
        $stmt->bind_param("s", $new_event_type);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
    }
    exit();
}

// Handle adding event name via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_event_name'])) {
    $new_event_name = trim($_POST['new_event_name']);
    if (!empty($new_event_name)) {
        $stmt = $conn->prepare("INSERT INTO events (name) VALUES (?)");
        $stmt->bind_param("s", $new_event_name);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
    }
    exit();
}
?>
