<?php
session_start();
include '../db.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit();
}

// Fetch pending user requests
$query = "SELECT user_id, uni_id, first_name, last_name, created_at FROM users WHERE status = 'pending'";
$result = $conn->query($query);

if (!$result) {
    echo json_encode(["status" => "error", "message" => "Failed to fetch user requests"]);
    exit();
}

$output = "";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $output .= "<tr>
            <td>{$row['uni_id']}</td>
            <td>{$row['first_name']} {$row['last_name']}</td>
            <td>{$row['created_at']}</td>
            <td>
                <button class='btn btn-success btn-sm approveUser' data-id='{$row['uni_id']}'><i class='fas fa-check'></i></button>
                <button class='btn btn-danger btn-sm rejectUser' data-id='{$row['uni_id']}'><i class='fas fa-times'></i></button>
            </td>
        </tr>";
    }
} else {
    $output = "<tr><td colspan='4'>No pending user requests</td></tr>";
}

echo $output;

$conn->close();
?>