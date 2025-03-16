<?php
session_start();
include '../db.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

include 'header.php';

// Fetch the logged-in admin's user_id from the session
$user_id = $_SESSION['user_id'];

// Fetch admin details from the 'users' table
$stmt = $conn->prepare("SELECT uni_ID, first_name, last_name, email, mobile, username, profile_picture FROM users WHERE user_id = ?");
if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}

$stmt->bind_param("s", $user_id); // Use "s" for string
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc(); // Fetch the admin's details
} else {
    die("Error: Admin profile not found for user_id = $user_id");
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile | Smart Campus</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom CSS to push content down -->
    <style>
        .main-content {
            margin-top: 80px; /* Adjust this value as needed */
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        #statusMessage {
            display: inline-block;
            padding: 5px 10px;
            margin-left: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content p-4">
        <h3 class="mb-4">My Profile</h3>

        <form id="profileForm" class="bg-white p-4 rounded shadow-sm" enctype="multipart/form-data">
            <!-- Profile Picture -->
            <div class="row mb-3">
                <div class="col-12 text-center">
                    <?php if (!empty($admin['profile_picture'])): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($admin['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
                    <?php else: ?>
                        <img src="../assets/default_profile.png" alt="Default Profile Picture" class="profile-picture">
                    <?php endif; ?>
                </div>
            </div>

            <!-- Profile Picture Upload -->
            <div class="row mb-3">
                <div class="col-12">
                    <label for="profile_picture" class="form-label fw-bold">Upload Profile Picture</label>
                    <input type="file" id="profile_picture" name="profile_picture" class="form-control" accept="image/*">
                </div>
            </div>

            <!-- Remove Profile Picture -->
            <?php if (!empty($admin['profile_picture'])): ?>
                <div class="row mb-3">
                    <div class="col-12">
                        <button type="button" id="removeProfilePicture" class="btn btn-danger">Remove Profile Picture</button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- University ID -->
            <div class="row mb-3">
                <div class="col-12">
                    <label for="uni_ID" class="form-label fw-bold">University ID</label>
                    <input type="text" id="uni_ID" class="form-control" value="<?php echo htmlspecialchars($admin['uni_ID']); ?>" readonly>
                </div>
            </div>

            <!-- Username -->
            <div class="row mb-3">
                <div class="col-12">
                    <label for="username" class="form-label fw-bold">Username</label>
                    <input type="text" id="username" class="form-control" value="<?php echo htmlspecialchars($admin['username']); ?>" readonly>
                </div>
            </div>

            <!-- First Name -->
            <div class="row mb-3">
                <div class="col-12">
                    <label for="first_name" class="form-label fw-bold">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($admin['first_name']); ?>" required>
                </div>
            </div>

            <!-- Last Name -->
            <div class="row mb-3">
                <div class="col-12">
                    <label for="last_name" class="form-label fw-bold">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($admin['last_name']); ?>" required>
                </div>
            </div>

            <!-- Email -->
            <div class="row mb-3">
                <div class="col-12">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                </div>
            </div>

            <!-- Mobile -->
            <div class="row mb-3">
                <div class="col-12">
                    <label for="mobile" class="form-label fw-bold">Contact Number</label>
                    <input type="text" id="mobile" name="mobile" class="form-control" value="<?php echo htmlspecialchars($admin['mobile']); ?>" required>
                    <small class="text-muted">Must be a 10-digit number starting with 0 (e.g., 0123456789).</small>
                </div>
            </div>

            <!-- Save Button -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Save</button>
                <span id="statusMessage" class="ms-3"></span>
            </div>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        // Handle form submission
        $("#profileForm").submit(function(e) {
            e.preventDefault(); // Prevent the default form submission

            let formData = new FormData(this);

            // Validate mobile number format
            let mobile = formData.get("mobile").trim(); // Trim spaces
            if (!/^0\d{9}$/.test(mobile)) {
                $("#statusMessage").text("Mobile number must be 10 digits and start with 0.").css("color", "red");
                return;
            }

            // Submit the form via AJAX
            $.ajax({
                url: "update_admin_profile.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === "success") {
                        $("#statusMessage").text(response.message).css("color", "green");
                        // Optionally, reload the page after a short delay to reflect changes
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // Reload after 2 seconds
                    } else {
                        $("#statusMessage").text(response.message).css("color", "red");
                    }
                },
                error: function(xhr, status, error) {
                    $("#statusMessage").text("An error occurred. Please try again.").css("color", "red");
                },
                dataType: "json"
            });
        });

        // Handle profile picture removal
        $("#removeProfilePicture").click(function() {
            if (confirm("Are you sure you want to remove your profile picture?")) {
                $.post("update_admin_profile.php", { remove_profile_picture: true }, function(response) {
                    if (response.status === "success") {
                        $("#statusMessage").text(response.message).css("color", "green");
                        // Reload the page to reflect changes
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $("#statusMessage").text(response.message).css("color", "red");
                    }
                }, "json");
            }
        });
    });
    </script>

</body>
</html>