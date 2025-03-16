<?php
// No need to call session_start() here, as it is already called in session_check.php

if (!isset($_SESSION['role'])) {
    header("Location: /smart%20campus/index.php"); // Absolute path to index.php
    exit();
}

$role = $_SESSION['role'];
$current_page = $_SERVER['REQUEST_URI']; // Get the full URL path

// Redirect users based on their role, but only if they are not already on the correct page
if ($role === 'student' && strpos($current_page, 'student/dashboard.php') === false) {
    header("Location: /smart%20campus/student/dashboard.php"); // Absolute path
    exit();
} elseif ($role === 'lecturer' && strpos($current_page, 'lecturer/dashboard.php') === false) {
    header("Location: /smart%20campus/lecturer/dashboard.php"); // Absolute path
    exit();
} elseif ($role === 'admin' && strpos($current_page, 'admin/dashboard.php') === false) {
    header("Location: /smart%20campus/admin/dashboard.php"); // Absolute path
    exit();
}
?>
