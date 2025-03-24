<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "venue_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed."]));
}

// Check if booking_id is sent via AJAX
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['booking_id'])) {
    $bookingId = intval($_POST['booking_id']);

    // Check if the booking is already canceled
    $checkQuery = "SELECT status FROM bookings WHERE id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    if (!$booking) {
        echo json_encode(["success" => false, "message" => "Booking not found."]);
        exit();
    }

    if ($booking['status'] === 'Canceled') {
        echo json_encode(["success" => false, "message" => "Booking is already canceled."]);
        exit();
    }

    // Update the booking status to "Canceled"
    $updateQuery = "UPDATE bookings SET status = 'Canceled' WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $bookingId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to cancel booking."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>

