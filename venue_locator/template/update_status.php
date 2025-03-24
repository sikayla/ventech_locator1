<?php
session_start();
header("Content-Type: application/json");

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "venue_db";

// Database connection
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

// Ensure the user is an admin before proceeding
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit();
}

// Decode JSON input safely
$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid JSON input"]);
    exit();
}

// Validate required fields
if (empty($data['id']) || empty($data['status'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit();
}

// Sanitize and validate inputs
$bookingId = filter_var($data['id'], FILTER_VALIDATE_INT);
$newStatus = trim($conn->real_escape_string($data['status']));

// Ensure a valid integer ID is provided
if (!$bookingId || $bookingId <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid booking ID"]);
    exit();
}

// Allow only valid status values
$validStatuses = ['Approved', 'Rejected', 'Pending'];
if (!in_array($newStatus, $validStatuses, true)) { // strict mode
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid status value"]);
    exit();
}

// Check if booking ID exists (optimized)
$checkQuery = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE id = ?");
$checkQuery->bind_param("i", $bookingId);
$checkQuery->execute();
$checkQuery->bind_result($count);
$checkQuery->fetch();
$checkQuery->close();

if ($count === 0) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Booking ID not found"]);
    exit();
}

// Update the status securely
$updateQuery = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
$updateQuery->bind_param("si", $newStatus, $bookingId);

if ($updateQuery->execute()) {
    echo json_encode(["success" => true, "message" => "Booking status updated successfully"]);
} else {
    http_response_code(500);
    error_log("SQL Error: " . $updateQuery->error); // Log error for debugging
    echo json_encode(["success" => false, "message" => "Error updating status"]);
}

$updateQuery->close();
$conn->close();
?>



