<?php
session_start();
include '../db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch all available badges for dropdown
$badges = $conn->query("SELECT * FROM badges");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat | Smart Campus</title>
    
    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* Chat Box Styling */
        .chat-container {
            margin-top: 80px;
            padding: 20px;
        }
        .chat-box {
            height: 450px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
        }
        .message {
            padding: 8px;
            border-radius: 10px;
            margin-bottom: 10px;
            max-width: 75%;
            word-wrap: break-word;
        }
        .sent {
            background:rgba(211, 30, 30, 0.7);
            color: white;
            align-self: flex-end;
            text-align: right;
        }
        .received {
            background:rgba(211, 30, 30, 0.7);
            color: white;
            align-self: flex-start;
            text-align: left;
        }
        .timestamp {
            display: block;
            font-size: 12px;
            color: rgb(240, 239, 239);
            margin-top: 3px;
        }
        .user-info {
            font-size: 12px;
            color:rgb(240, 239, 239);
            margin-top: 5px;
        }
        .file-preview {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .file-preview div {
            background: #f1f1f1;
            padding: 5px;
            border-radius: 5px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        .file-preview .remove-file {
            margin-left: 5px;
            color: red;
            cursor: pointer;
        }
        .chat-header {
            background: #800020;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .chat-footer {
            margin-top: 10px;
        }
        .chat-select {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content chat-container">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h5><i class="fas fa-users"></i> Select Badge Group</h5>
                <select id="badgeSelect" class="form-control chat-select">
                    <option value="">Select a badge</option>
                    <option value="all">All Students</option>
                    <?php while ($row = $badges->fetch_assoc()): ?>
                        <option value="<?php echo $row['badge_id']; ?>"><?php echo $row['badge_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="chat-header">
                        <h5><i class="fas fa-comments"></i> Chat Room</h5>
                    </div>
                    <div id="chatBox" class="chat-box"></div>
                    
                    <div id="filePreview" class="file-preview"></div>

                    <div class="chat-footer p-3">
                        <div class="input-group">
                            <input type="file" id="fileInput" class="form-control d-none" multiple>
                            <button class="btn btn-secondary" id="attachFile"><i class="fas fa-paperclip"></i></button>
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
    let selectedFiles = [];

    // Load Messages
    function loadMessages() {
        if (selectedBadgeId) {
            $.get("get_messages.php?receiver_badge_id=" + selectedBadgeId, function(data) {
                let messages = JSON.parse(data);
                let chatHtml = '';
                messages.forEach(msg => {
                    let alignment = msg.user_id == <?php echo json_encode($user_id); ?> ? 'sent' : 'received';
                    chatHtml += `
                        <div class="message ${alignment}">
                            <b>${msg.message}</b>
                            <span class="timestamp">${msg.sent_at}</span>
                            <div class="user-info">
                                <small>${msg.uni_ID} - ${msg.first_name} ${msg.last_name}</small>
                            </div>
                            ${msg.attachments.length > 0 ? '<div class="attachments">' + msg.attachments.map(file => `<a href="../uploads/${file}" target="_blank">${file}</a>`).join('<br>') + '</div>' : ''}
                        </div>
                    `;
                });
                $("#chatBox").html(chatHtml);
                $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);
            });
        }
    }

    // Badge selection changes messages
    $("#badgeSelect").change(function() {
        selectedBadgeId = $(this).val();
        $("#chatBox").html("<p>Loading messages...</p>");
        loadMessages();
    });

    // File Selection
    $("#attachFile").click(function() {
        $("#fileInput").click();
    });

    $("#fileInput").change(function() {
        selectedFiles = Array.from(this.files);
        $("#filePreview").html("");
        selectedFiles.forEach((file, index) => {
            $("#filePreview").append(`<div>${file.name} <span class="remove-file" data-index="${index}">&times;</span></div>`);
        });
    });

    // Remove File
    $(document).on("click", ".remove-file", function() {
        let index = $(this).data("index");
        selectedFiles.splice(index, 1);
        $(this).parent().remove();
    });

    // Send Message
    $("#sendMessage").click(function() {
        let message = $("#messageInput").val().trim();
        let formData = new FormData();

        if (!selectedBadgeId) {
            alert("Please select a badge first.");
            return;
        }

        formData.append("receiver_badge_id", selectedBadgeId);
        formData.append("message", message);

        if (selectedFiles.length > 0) {
            $.each(selectedFiles, function (index, file) {
                formData.append("files[]", file);
            });
        }

        $.ajax({
            url: "send_message.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    $("#messageInput").val('');
                    $("#fileInput").val('');
                    $("#filePreview").html("");
                    loadMessages();
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    });

    // Refresh messages every 2 seconds
    setInterval(loadMessages, 2000);
});
</script>

</body>
</html>