/*<?php
session_start();
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User request rejected"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to reject user"]);
    }
}
?>

