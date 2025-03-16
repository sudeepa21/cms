<?php
include '../db.php';

$badge_id = $_GET['badge_id'];

// Fetch students from `students` table and join `users` to get details
$query = "
    SELECT s.student_id, u.uni_ID, u.first_name, u.last_name 
    FROM students s
    JOIN users u ON s.user_id = u.user_id
    JOIN enrollments e ON s.student_id = e.student_id
    WHERE e.badge_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $badge_id);
$stmt->execute();
$result = $stmt->get_result();

while ($student = $result->fetch_assoc()) {
    echo '<option value="'.$student['student_id'].'">'.$student['uni_ID'].' - '.$student['first_name'].' '.$student['last_name'].'</option>';
}

$stmt->close();
?>
