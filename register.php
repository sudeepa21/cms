<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = uniqid("U"); // Generate a unique user ID
    $uni_ID = $_POST['uni_ID']; // Get University ID from the form
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Check if username or email exists
    $checkUser = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $checkUser->bind_param("ss", $username, $email);
    $checkUser->execute();
    $result = $checkUser->get_result();

    if ($result->num_rows > 0) {
        $error = "Username or Email already exists!";
    } else {
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (user_id, uni_ID, first_name, last_name, email, mobile, username, password, role) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $user_id, $uni_ID, $first_name, $last_name, $email, $mobile, $username, $password, $role);
        
        if ($stmt->execute()) {
            $success = "Registration successful. <a href='index.php'>Login here</a>";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Smart Campus</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: url('assets/bg-campus.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .register-container {
            max-width: 500px;
            margin: 5% auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="register-container">
            <h2>Register for Smart Campus</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger text-center">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success text-center">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">University ID</label>
                    <input type="text" name="uni_ID" class="form-control" placeholder="Enter University ID" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" placeholder="Enter First Name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" placeholder="Enter Last Name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mobile Number</label>
                    <input type="text" name="mobile" class="form-control" placeholder="Enter Mobile Number" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Choose a Username" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="student">Student</option>
                        <option value="lecturer">Lecturer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-custom">Register</button>
                </div>

                <div class="text-center mt-3">
                    <a href="index.php">Already have an account? Login</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
