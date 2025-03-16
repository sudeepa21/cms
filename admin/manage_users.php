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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users | Smart Campus</title>
    
    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* Page Layout */
        .main-content {
            margin-left: 270px;
            padding: 20px;
            padding-top: 100px;
        }

        .search-bar {
            max-width: 600px;
            margin: 20px auto;
        }

        .table-container {
            margin-top: 30px;
            max-width: 100%;
        }

        /* Card Styling */
        .card {
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background: white;
        }

        /* Buttons */
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
        }

        /* Table */
        .table {
            background: white;
            border-radius: 8px;
        }

        .table thead {
            background: #343a40;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding-top: 120px;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <h2 class="text-center mb-4">Manage Users</h2>

        <!-- Search Bar -->
        <div class="input-group search-bar">
            <input type="text" id="searchUser" class="form-control" placeholder="Enter University ID">
            <button class="btn btn-primary" id="searchBtn"><i class="fas fa-search"></i></button>
        </div>

        <!-- Update Existing Users -->
        <div class="card">
            <h5 class="text-center mb-4">Update Existing Users</h5>
            
            <div class="row">
                <div class="col-md-6">
                    <label>University ID</label>
                    <input type="text" id="uni_ID" class="form-control" readonly>
                </div>
                <div class="col-md-6">
                    <label>Mobile Number</label>
                    <input type="text" id="mobile" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label>First Name</label>
                    <input type="text" id="first_name" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>Email</label>
                    <input type="email" id="email" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label>Last Name</label>
                    <input type="text" id="last_name" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>User Role</label>
                    <select id="role" class="form-select">
                        <option value="student">Student</option>
                        <option value="lecturer">Lecturer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>

            <div class="btn-container">
                <button class="btn btn-success" id="saveUser"><i class="fas fa-save"></i> Save</button>
                <button class="btn btn-danger" id="deleteUser"><i class="fas fa-trash"></i> Delete</button>
            </div>
        </div>

        <!-- New User Requests -->
        <div class="table-container">
            <h5 class="text-center mt-4">New User Requests</h5>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>University ID</th>
                        <th>User Name</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="userRequests">
                    <!-- Data will be dynamically inserted here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Search user by University ID
    $("#searchBtn").click(function() {
        let uniID = $("#searchUser").val().trim();
        if (uniID === "") {
            alert("Please enter a University ID");
            return;
        }

        $.post("fetch_user.php", { uni_ID: uniID }, function(response) {
            if (response.status === "success") {
                $("#uni_ID").val(response.data.uni_ID);
                $("#first_name").val(response.data.first_name);
                $("#last_name").val(response.data.last_name);
                $("#role").val(response.data.role);
                $("#mobile").val(response.data.mobile);
                $("#email").val(response.data.email);
            } else {
                alert(response.message);
            }
        }, "json");
    });

    // Save User Changes
    $("#saveUser").click(function() {
        let uniID = $("#uni_ID").val();
        let firstName = $("#first_name").val();
        let lastName = $("#last_name").val();
        let role = $("#role").val();
        let mobile = $("#mobile").val();
        let email = $("#email").val();

        if (!uniID) {
            alert("Please search for a user first");
            return;
        }

        $.post("update_user.php", { uni_ID: uniID, first_name: firstName, last_name: lastName, role: role, mobile: mobile, email: email }, function(response) {
            alert(response.message);
            location.reload();
        }, "json");
    });

    // Delete User
    $("#deleteUser").click(function() {
        let uniID = $("#uni_ID").val();
        if (confirm("Are you sure you want to delete this user?")) {
            $.post("delete_user.php", { uni_ID: uniID }, function(response) {
                alert(response.message);
                location.reload();
            }, "json");
        }
    });

    // Approve User
    $(document).on("click", ".approveUser", function() {
        let uniID = $(this).data("id");
        $.post("approve_user.php", { uni_ID: uniID }, function(response) {
            alert(response.message);
            location.reload();
        }, "json");
    });

    // Deny User
    $(document).on("click", ".rejectUser", function() {
        let uniID = $(this).data("id");
        if (confirm("Are you sure you want to deny and delete this user?")) {
            $.post("deny_request.php", { uni_ID: uniID }, function(response) {
                alert(response.message);
                location.reload();
            }, "json");
        }
    });

    // Load pending user requests
    function loadUserRequests() {
        $.get("get_user_requests.php", function(data) {
            $("#userRequests").html(data);
        });
    }

    loadUserRequests();
});
</script>

</body>
</html>
