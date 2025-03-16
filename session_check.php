<?php
// Ensure session_start() is only called once in the entire script.
// Do not call session_start() here again if it is already called in a higher-level file.

include 'db.php'; // Your DB connection setup.

$session_id = session_id();

// Check if session exists in the database
$stmt = $conn->prepare("SELECT * FROM sessions WHERE session_id = ?");
$stmt->bind_param("s", $session_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Session is invalid, log the user out
    session_destroy();
    header("Location: /smart%20campus/index.php");
    exit();
}

// Update last activity time
$stmt = $conn->prepare("UPDATE sessions SET last_activity = NOW() WHERE session_id = ?");
$stmt->bind_param("s", $session_id);
$stmt->execute();
?>
