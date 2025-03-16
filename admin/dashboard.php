<?php

session_start();
include '../db.php';

// Ensure user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch user statistics
$total_students = $conn->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetch_row()[0];
$total_lecturers = $conn->query("SELECT COUNT(*) FROM users WHERE role='lecturer'")->fetch_row()[0];
$total_admins = $conn->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetch_row()[0];
$total_courses = $conn->query("SELECT COUNT(*) FROM courses")->fetch_row()[0];

// Fetch pending approvals
$pending_approvals = $conn->query("SELECT uni_ID, first_name, last_name, created_at FROM users WHERE status='pending' ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Smart Campus</title>
    
    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css"> <!-- Main CSS File -->
</head>
<body>

    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            
            <!-- Dashboard Statistics -->
            <div class="row text-center dashboard-cards d-flex justify-content-center align-items-center">
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="card p-4 bg-light shadow">
                        <i class="fas fa-user-graduate fa-2x text-primary"></i>
                        <h4 class="mt-2">Students</h4>
                        <p class="fs-3"><?php echo $total_students; ?>+</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="card p-4 bg-light shadow">
                        <i class="fas fa-chalkboard-teacher fa-2x text-success"></i>
                        <h4 class="mt-2">Teachers</h4>
                        <p class="fs-3"><?php echo $total_lecturers; ?>+</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="card p-4 bg-light shadow">
                        <i class="fas fa-user-shield fa-2x text-danger"></i>
                        <h4 class="mt-2">Admins</h4>
                        <p class="fs-3"><?php echo $total_admins; ?>+</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="card p-4 bg-light shadow">
                        <i class="fas fa-book fa-2x text-warning"></i>
                        <h4 class="mt-2">Courses</h4>
                        <p class="fs-3"><?php echo $total_courses; ?>+</p>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div class="mt-4">
                <h3>Pending Approvals</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered approval-table">
                        <thead class="table-dark">
                            <tr>
                                <th>University ID</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($approval = $pending_approvals->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $approval['uni_ID']; ?></td>
                                <td><?php echo $approval['first_name'] . " " . $approval['last_name']; ?></td>
                                <td><?php echo date("d/m/Y", strtotime($approval['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-success btn-sm approveUser" data-id="<?php echo $approval['uni_ID']; ?>">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm rejectUser" data-id="<?php echo $approval['uni_ID']; ?>">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Calendar -->
            <div class="calendar mt-4 text-center">
                <h3 class="text-danger">February 2025</h3>
                <img src="../assets/calendar.png" class="img-fluid" alt="Calendar">
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Approve User
            $(document).on("click", ".approveUser", function() {
                let uniID = $(this).data("id");
                $.post("approve_user.php", { uni_ID: uniID }, function(response) {
                    alert(response.message);
                    location.reload();
                }, "json");
            });

            // Reject User (Delete from DB)
            $(document).on("click", ".rejectUser", function() {
                let uniID = $(this).data("id");
                if (confirm("Are you sure you want to delete this user?")) {
                    $.post("deny_request.php", { uni_ID: uniID }, function(response) {
                        alert(response.message);
                        location.reload();
                    }, "json");
                }
            });
        });
    </script>

</body>
</html>
