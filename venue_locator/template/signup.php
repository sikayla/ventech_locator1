<?php  
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $profile_image = "default.png";
    if (!empty($_FILES['profile_image']['name'])) {
        $image_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_file = $upload_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $profile_image = $target_file;
            } else {
                echo "<script>alert('Error: Unable to upload file.'); window.location.href='signup.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Error: Only JPG, JPEG, and PNG files are allowed.'); window.location.href='signup.php';</script>";
            exit();
        }
    }

    // ✅ Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM user_admin WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Error: Username or Email already exists. Please try another.'); window.location.href='signup.php';</script>";
        $stmt->close();
        exit();
    }
    $stmt->close();

    // ✅ Insert new user into user_admin table
    $stmt = $conn->prepare("INSERT INTO user_admin (firstname, lastname, username, email, password, profile_image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstname, $lastname, $username, $email, $password, $profile_image);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Please log in.'); window.location.href='signin.php';</script>";
    } else {
        echo "<script>alert('Error: Something went wrong. Please try again.'); window.location.href='signup.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: linear-gradient(to right, #66a6ff, #c1c8e4);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 450px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background: linear-gradient(45deg, #7F00FF, #E100FF);
            border: none;
            padding: 10px;
            font-size: 16px;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #5F00CC, #B100CC);
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
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Registration</h2>
    <form action="signup.php" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">First Name</label>
                <input type="text" class="form-control" name="firstname" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Last Name</label>
                <input type="text" class="form-control" name="lastname" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-3 password-container">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
            <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
        </div>
        <div class="mb-3">
            <label class="form-label">Profile Image (Optional)</label>
            <input type="file" class="form-control" name="profile_image" accept="image/png, image/jpeg, image/jpg">
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
        <p class="mt-3 text-center">Already have an account? <a href="signin.php">Sign-in</a></p>
    </form>
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
