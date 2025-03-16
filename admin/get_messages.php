<?php
session_start();
include '../db.php';

$receiver_badge_id = isset($_GET['receiver_badge_id']) ? $_GET['receiver_badge_id'] : '';

if (empty($receiver_badge_id)) {
    echo json_encode([]);
    exit();
}

// Fetch messages for the selected badge with user details
$query = $conn->prepare("
    SELECT cm.*, u.uni_ID, u.first_name, u.last_name 
    FROM chat_messages cm
    JOIN users u ON cm.user_id = u.user_id
    WHERE cm.badge_id = ?
    ORDER BY cm.sent_at ASC
");
$query->bind_param("i", $receiver_badge_id);
$query->execute();
$result = $query->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    // Decode attachments JSON
    $row['attachments'] = !empty($row['attachments']) ? json_decode($row['attachments'], true) : [];
    $messages[] = $row;
}

echo json_encode($messages);
$query->close();
$conn->close();
?>