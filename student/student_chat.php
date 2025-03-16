<?php
session_start();
include '../db.php';

// Ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the student's ID from the students table
$stmt = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student record not found.");
}

$student_id = $student['student_id'];

// Fetch assigned badges for the student
$stmt = $conn->prepare("
    SELECT DISTINCT b.badge_id, b.badge_name 
    FROM enrollments e
    JOIN badges b ON e.badge_id = b.badge_id
    WHERE e.student_id = ?
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$badges = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Chat | Smart Campus</title>
    
    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .chat-container { margin-top: 80px; padding: 20px; }
        .chat-box { height: 450px; overflow-y: auto; border: 1px solid #ddd; border-radius: 10px; padding: 15px; background: #f8f9fa; display: flex; flex-direction: column; }
        .message { padding: 8px; border-radius: 10px; margin-bottom: 10px; max-width: 75%; word-wrap: break-word; }
        .sent { background: #007bff; color: white; align-self: flex-end; text-align: right; }
        .received { background: #e9ecef; color: black; align-self: flex-start; text-align: left; }
        .timestamp { display: block; font-size: 12px; color: #666; margin-top: 3px; }
        .user-info { font-size: 12px; color: #555; margin-top: 5px; }
        .chat-header { background: #800020; color: white; padding: 15px; text-align: center; border-radius: 10px 10px 0 0; }
        .chat-footer { margin-top: 10px; }
        .chat-select { margin-bottom: 10px; }
    </style>
</head>
<body>

<?php include 'student_header.php'; ?>
<?php include 'student_sidebar.php'; ?>

<div class="main-content chat-container">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h5><i class="fas fa-users"></i> Select Badge Group</h5>
                <select id="badgeSelect" class="form-control chat-select">
                    <option value="">Select a badge</option>
                    <?php foreach ($badges as $badge): ?>
                        <option value="<?php echo $badge['badge_id']; ?>"><?php echo htmlspecialchars($badge['badge_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="chat-header">
                        <h5><i class="fas fa-comments"></i> Student Chat Room</h5>
                    </div>
                    <div id="chatBox" class="chat-box"></div>

                    <div class="chat-footer p-3">
                        <div class="input-group">
                            <input type="text" id="messageInput" class="form-control" placeholder="Type a message">
                            <button class="btn btn-primary" id="sendMessage"><i class="fas fa-paper-plane"></i> Send</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>

<script>
$(document).ready(function() {
    let selectedBadgeId = null;

    function loadMessages() {
        if (selectedBadgeId) {
            $.get("student_get_messages.php?badge_id=" + selectedBadgeId, function(data) {
                let messages = JSON.parse(data);
                let chatHtml = '';
                messages.forEach(msg => {
                    let alignment = msg.user_id == <?php echo json_encode($user_id); ?> ? 'sent' : 'received';
                    chatHtml += `
                        <div class="message ${alignment}">
                            <b>${msg.message}</b>
                            <span class="timestamp">${msg.sent_at}</span>
                            <div class="user-info"><small>${msg.uni_ID} - ${msg.first_name} ${msg.last_name}</small></div>
                        </div>
                    `;
                });
                $("#chatBox").html(chatHtml);
                $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);
            });
        }
    }

    $("#badgeSelect").change(function() { selectedBadgeId = $(this).val(); loadMessages(); });

    $("#sendMessage").click(function() {
        let message = $("#messageInput").val().trim();
        if (!selectedBadgeId || message === "") return;

        $.post("student_send_message.php", { badge_id: selectedBadgeId, message: message }, function(response) {
            if (response.status === "success") {
                $("#messageInput").val('');
                loadMessages();
            }
        }, "json");
    });

    setInterval(loadMessages, 2000);
});
</script>

</body>
</html>
