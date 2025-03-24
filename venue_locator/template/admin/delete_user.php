<?php
session_start();
include '../../config.php';

// Ensure the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../signin.php");
    exit();
}

// Check if the user ID is provided
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete user query
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully!'); window.location.href='user_records.php';</script>";
    } else {
        echo "<script>alert('Error deleting user.'); window.location.href='user_records.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request.'); window.location.href='user_records.php';</script>";
}

$conn->close();
?>
