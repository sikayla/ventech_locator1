<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "venue_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Database connection failed."]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["booking_id"])) {
    $booking_id = intval($_POST["booking_id"]);

    $query = "SELECT b.*, v.name AS venue_name 
              FROM bookings b 
              JOIN venues v ON b.venue_id = v.id
              WHERE b.id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $imagePath = !empty($row['id_photo']) ? "uploads/" . $row['id_photo'] : "default.jpg";

        echo json_encode([
            "success" => true,
            "data" => $row,
            "image_url" => $imagePath
        ]);
    } else {
        echo json_encode(["success" => false, "error" => "No booking found."]);
    }

    $stmt->close();
}

$conn->close();
?>


