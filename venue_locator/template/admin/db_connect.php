<?php
$host = "localhost"; // Change if using a remote database
$user = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$database = "venue_db"; // Use the correct database name

// Connect to MySQL
$conn = new mysqli($host, $user, $password, $database);

// Check if connection was successful
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>