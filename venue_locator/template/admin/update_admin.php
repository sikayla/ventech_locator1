<?php
include 'db_connect.php'; // Ensure this file connects to your database

$username = 'admin';
$new_password = password_hash('1234', PASSWORD_DEFAULT); // Securely hash the new password

// Update the admin password
$sql = "UPDATE admins SET password = ? WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $new_password, $username);

if ($stmt->execute()) {
    echo "✅ Admin password updated successfully!";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
