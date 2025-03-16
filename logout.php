<?php
session_start();
include 'db.php';

$session_id = session_id();

// Delete session from database
$stmt = $conn->prepare("DELETE FROM sessions WHERE session_id = ?");
$stmt->bind_param("s", $session_id);
$stmt->execute();

// Destroy the session
session_destroy();
header("Location: index.php");
exit();
?>