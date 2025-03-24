<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "venue_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["booking_id"])) {
    $booking_id = intval($_POST["booking_id"]);

    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Booking deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error deleting booking."]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close();
?>


