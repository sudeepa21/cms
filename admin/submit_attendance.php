<?php
include '../db.php';

// Ensure required data is received
if (!isset($_POST['event_id'], $_POST['badge_id'], $_POST['students'], $_POST['status'])) {
    echo json_encode(["message" => "Invalid request"]);
    exit();
}

$event_id = $_POST['event_id'];
$badge_id = $_POST['badge_id'];
$selected_students = $_POST['students']; // Array of selected students
$status = $_POST['status']; // 'present' or 'absent'

// Convert selected students array to ensure it's safe for SQL
$selected_students_list = implode(',', array_map('intval', $selected_students));

// **Only update the selected students' attendance**
foreach ($selected_students as $student_id) {
    $conn->query("
        INSERT INTO attendance (event_id, badge_id, student_id, status)
        VALUES ('$event_id', '$badge_id', '$student_id', '$status')
        ON DUPLICATE KEY UPDATE status = '$status'
    ");
}

echo json_encode(["message" => "Attendance updated successfully"]);
?>
