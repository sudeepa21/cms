<?php
session_start();
include '../db.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch pending resource requests with joined data
$requests = $conn->query("
    SELECT 
        rr.id, 
        rr.requested_date, 
        rr.other_resources, 
        b.badge_name, 
        c.course_name, 
        cr.name AS class_room_name,
        u.first_name,
        u.last_name
    FROM resource_requests rr
    JOIN badges b ON rr.badge_id = b.badge_id
    JOIN courses c ON rr.course_id = c.course_id
    JOIN class_rooms cr ON rr.class_id = cr.class_id
    JOIN users u ON rr.user_id = u.user_id -- Join users table to get lecturer's name
    WHERE rr.status='pending'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Resource Requests | Smart Campus</title>

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css"> <!-- Main CSS File -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .main-content {
            margin-left: 270px;
            padding: 20px;
            padding-top: 100px;
        }

        .card {
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background: white;
            margin-bottom: 20px;
        }

        .request-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .request-box div:first-child {
            flex: 1;
        }

        .request-box div:last-child {
            display: flex;
            gap: 10px;
        }

        .btn-approve {
            background: #28a745;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-approve:hover {
            background: #218838;
        }

        .btn-deny {
            background: #dc3545;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-deny:hover {
            background: #c82333;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding-top: 120px;
            }

            .request-box {
                flex-direction: column;
                text-align: center;
            }

            .request-box div:last-child {
                margin-top: 10px;
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <h3 class="mt-4"><i class="fas fa-clipboard-check"></i> Approve Resource Requests</h3>

    <!-- Display success message if any -->
    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>

    <div class="request-container">
        <?php if ($requests->num_rows > 0): ?>
            <?php while ($request = $requests->fetch_assoc()): ?>
            <div class="request-box">
                <div>
                    <strong>Lecture Name:</strong> <?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?><br>
                    <strong>Badge:</strong> <?php echo htmlspecialchars($request['badge_name']); ?><br>
                    <strong>Module:</strong> <?php echo htmlspecialchars($request['course_name']); ?><br>
                    <strong>Date:</strong> <?php echo htmlspecialchars($request['requested_date']); ?><br>
                    <strong>Requested Class Room:</strong> <?php echo htmlspecialchars($request['class_room_name']); ?><br>
                    <strong>Requested Resources:</strong> <?php echo htmlspecialchars($request['other_resources']); ?>
                </div>
                <div>
                    <a href="approve_request.php?id=<?php echo $request['id']; ?>" class="btn btn-approve">
                        <i class="fas fa-check"></i> Approve
                    </a>
                    <a href="deny_request.php?id=<?php echo $request['id']; ?>" class="btn btn-deny">
                        <i class="fas fa-times"></i> Deny
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center text-muted">No pending resource requests.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>