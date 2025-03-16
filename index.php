<?php
session_start();
include 'db.php';

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user details
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Check if the user's status is "approved"
        if ($user['status'] == 'approved') {
            if (password_verify($password, $user['password'])) {
                // Start a session
                session_regenerate_id(true); // Regenerate session ID for security
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Store session in the database
                $session_id = session_id();
                $stmt = $conn->prepare("INSERT INTO sessions (session_id, user_id, role) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $session_id, $user['user_id'], $user['role']);
                $stmt->execute();

                // Redirect users based on role
                if ($user['role'] == 'admin') {
                    header("Location: admin/dashboard.php");
                } elseif ($user['role'] == 'lecturer') {
                    header("Location: lecturer/dashboard.php");
                } else {
                    header("Location: student/dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid Password!";
            }
        } else {
            $error = "Your account has not been approved yet.";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Smart Campus</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: url('assets/bg-campus.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .login-container {
            max-width: 400px;
            margin: 8% auto;
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .login-container h2 {
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
        <div class="login-container">
            <h2>Smart Campus Login</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger text-center">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter Username" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-custom">Login</button>
                </div>

                <div class="text-center mt-3">
                    <a href="register.php">Don't have an account? Register</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>