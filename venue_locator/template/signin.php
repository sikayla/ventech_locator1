<?php 
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim(htmlspecialchars($_POST['username']));
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Query the database to check if the username exists
        $sql = "SELECT * FROM user_admin WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            // Secure session handling
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to the dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            display: flex;
            width: 850px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .login-image {
            width: 50%;
            background: url('/venue_locator/images/court.png') center/cover no-repeat;
        }
        .login-form {
            width: 50%;
            padding: 40px;
        }
        .login-form h2 {
            font-weight: bold;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 5px;
        }
        .btn-primary {
            width: 100%;
            border-radius: 5px;
        }
        .forgot-password {
            display: block;
            text-align: right;
            margin-top: 10px;
            font-size: 14px;
        }
        .password-container {
            position: relative;
        }
        .password-container input {
            width: 100%;
            padding-right: 40px;
        }
        .password-container .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            color: white;
            border-radius: 5px;
        }
        .alert-danger {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="login-box">
            <div class="login-image"></div>
            <div class="login-form">
                <h2>Login</h2>

                <?php if (isset($error)) : ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="signin.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3 password-container">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
                    </div>
                    <a href="#" class="forgot-password">Forgot password?</a>
                    <button type="submit" class="btn btn-primary mt-3">Log In</button>
                </form>

                <p class="mt-3">Don't have an account? <a href="signup.php">Sign Up</a></p>
            </div>
        </div>
    </div>

<script>
function togglePassword() {
    var passwordField = document.getElementById("password");
    var icon = document.querySelector(".toggle-password");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>
</body>
</html>
