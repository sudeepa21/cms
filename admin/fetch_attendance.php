<?php
include '../db.php';

$badge_id = $_GET['badge_id'];
$event_id = $_GET['event_id']; // Add event_id to the request

// Fetch attendance data
$query = "
    SELECT u.uni_ID, u.first_name, u.last_name, a.status 
    FROM attendance a
    JOIN students s ON a.student_id = s.student_id
    JOIN users u ON s.user_id = u.user_id
    WHERE a.badge_id = ? AND a.event_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $badge_id, $event_id); // Bind both badge_id and event_id
$stmt->execute();
$result = $stmt->get_result();

$attendance = [];
while ($row = $result->fetch_assoc()) {
    $attendance[] = [
        'name' => $row['first_name'] . ' ' . $row['last_name'],
        'uni_ID' => $row['uni_ID'],
        'status' => $row['status']
    ];
}

echo json_encode($attendance);
?>