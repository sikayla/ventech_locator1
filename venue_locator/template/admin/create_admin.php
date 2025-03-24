<?php
include 'db_connect.php'; // Ensure this file correctly connects to your database

$username = 'admin';
$password = password_hash('1234', PASSWORD_DEFAULT); // Securely hash the password

// Prepare the SQL query to insert the admin user
$sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);

if ($stmt->execute()) {
    echo "✅ Admin user created successfully with a hashed password!";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
