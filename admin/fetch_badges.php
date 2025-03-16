<?php
include '../db.php';

$event_id = $_GET['event_id'];

$query = "
    SELECT b.badge_id, b.badge_name 
    FROM badges b
    INNER JOIN event_badges eb ON b.badge_id = eb.badge_id
    WHERE eb.event_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<option value="">Select a badge</option>';
    while ($badge = $result->fetch_assoc()) {
        echo '<option value="'.$badge['badge_id'].'">'.$badge['badge_name'].'</option>';
    }
} else {
    echo '<option value="">No badges found</option>';
}

$stmt->close();
?>
