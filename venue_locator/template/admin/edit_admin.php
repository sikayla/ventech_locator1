<?php
include '../../../config.php'; // Adjust based on your directory structure

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid request. No ID provided.'); window.location.href='admin_record.php';</script>";
    exit();
}

$id = $_GET['id'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM user_admin WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Redirect if user not found
if (!$user) {
    echo "<script>alert('User not found.'); window.location.href='admin_record.php';</script>";
    exit();
}

// Update user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE user_admin SET firstname=?, lastname=?, username=?, email=? WHERE id=?");
    $stmt->bind_param("ssssi", $firstname, $lastname, $username, $email, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully!'); window.location.href='admin_record.php';</script>";
    } else {
        echo "<script>alert('Error updating user.'); window.location.href='edit_admin.php?id=$id';</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Edit User</h2>
    <form method="POST" action="edit_admin.php?id=<?php echo $id; ?>">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
        <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" class="form-control" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" class="form-control" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="admin_record.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
