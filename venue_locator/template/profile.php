<?php
session_start();
include 'config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT firstname, lastname, username, email, password, profile_image FROM user_admin WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $profile_image = $user['profile_image'];
    if (!empty($_FILES['profile_image']['name'])) {
        $image_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_file = $upload_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $profile_image = $target_file;
            } else {
                echo "<script>alert('Error: Unable to upload file.'); window.location.href='profile.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Error: Only JPG, JPEG, and PNG files are allowed.'); window.location.href='profile.php';</script>";
            exit();
        }
    }

    if (!empty($old_password) && !empty($new_password)) {
        if (password_verify($old_password, $user['password'])) {
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE user_admin SET firstname=?, lastname=?, username=?, email=?, password=?, profile_image=? WHERE id=?");
            $stmt->bind_param("ssssssi", $firstname, $lastname, $username, $email, $new_password_hashed, $profile_image, $user_id);
        } else {
            echo "<script>alert('Error: Old password is incorrect.'); window.location.href='profile.php';</script>";
            exit();
        }
    } else {
        $stmt = $conn->prepare("UPDATE user_admin SET firstname=?, lastname=?, username=?, email=?, profile_image=? WHERE id=?");
        $stmt->bind_param("sssssi", $firstname, $lastname, $username, $email, $profile_image, $user_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('Error: Something went wrong.'); window.location.href='profile.php';</script>";
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
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #c47cc7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            width: 100%;
            display: flex;
        }
        .profile-section {
            width: 35%;
            text-align: center;
            padding: 20px;
            border-right: 2px solid #ddd;
        }
        .profile-section img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .edit-section {
            width: 65%;
            padding: 20px;
        }
        .btn-save {
            background: #c47cc7;
            color: white;
            border: none;
            width: 100%;
            padding: 10px;
            border-radius: 5px;
        }
        .btn-save:hover {
            background: #a34ba5;
        }
        .back-home {
            display: block;
            margin-bottom: 15px;
            color: #333;
            text-decoration: none;
        }
        .back-home i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="profile-section">
        <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image">
        <h4><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></h4>
        <p><?= htmlspecialchars($user['email']) ?></p>
    </div>
    
    <div class="edit-section">
        <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Back to home</a>
        <h4>Edit Profile</h4>
        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Old Password (Required for changing password)</label>
                <input type="password" class="form-control" name="old_password">
            </div>
            <div class="mb-3">
                <label class="form-label">New Password (Leave blank to keep the same)</label>
                <input type="password" class="form-control" name="new_password">
            </div>
            <div class="mb-3">
                <label class="form-label">Update Profile Image</label>
                <input type="file" class="form-control" name="profile_image" accept="image/png, image/jpeg, image/jpg">
            </div>
            <button type="submit" class="btn btn-save">Save Profile</button>
        </form>
    </div>
</div>
</body>
</html>


