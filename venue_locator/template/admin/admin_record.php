<?php
// Dynamically find the correct path to config.php
$possible_paths = [
    __DIR__ . '/../../config.php',   // When inside /venue_locator/template/admin/
    __DIR__ . '/../config.php',      // When inside /venue_locator/template/
    __DIR__ . '/../../../config.php' // If the structure is different
];

$configPath = null;
foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        $configPath = $path;
        break;
    }
}

if (!$configPath) {
    die("❌ Error: Configuration file not found. Check the path in admin_record.php.");
}

include $configPath;

// Ensure database connection is valid
if (!isset($conn) || $conn->connect_error) {
    die("❌ Database connection failed: " . ($conn->connect_error ?? "Connection object not set."));
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM user_admin WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully!'); window.location.href='admin_record.php';</script>";
    } else {
        echo "<script>alert('Error deleting user!');</script>";
    }
}

// Fetch user records
$result = $conn->query("SELECT * FROM user_admin");
if (!$result) {
    die("❌ Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Records</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Admin Records</h2>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Profile Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']); ?></td>
                    <td><?= htmlspecialchars($row['firstname']); ?></td>
                    <td><?= htmlspecialchars($row['lastname']); ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td>
                        <img src="<?= !empty($row['profile_image']) ? htmlspecialchars($row['profile_image']) : 'uploads/default.png'; ?>" 
                             alt="Profile Image" width="50" height="50">
                    </td>
                    <td>
                        <a href="edit_admin.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="admin_record.php?delete=<?= $row['id']; ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
