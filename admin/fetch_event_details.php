<?php
include '../db.php';

$event_id = $_GET['event_id'];
$event = $conn->query("SELECT date FROM events WHERE id='$event_id'")->fetch_assoc();
$attendance = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE event_id='$event_id'")->fetch_assoc();

$response = [
    'date' => $event['date'],
    'status' => ($attendance['count'] > 0) ? 'Marked' : 'Not Marked'
];

echo json_encode($response);
?>