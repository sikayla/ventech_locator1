<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "venue_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if booking ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$bookingId = intval($_GET['id']);

// Fetch booking details
$query = "SELECT * FROM bookings WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    die("Booking not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventName = htmlspecialchars(trim($_POST['event_name']));
    $eventDate = $_POST['event_date'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $attendees = intval($_POST['num_attendees']);

    // Validation checks
    if (strtotime($eventDate) < strtotime(date("Y-m-d"))) {
        echo "<script>alert('Event date must be in the future!');</script>";
    } elseif (strtotime($startTime) >= strtotime($endTime)) {
        echo "<script>alert('Start time must be before end time!');</script>";
    } elseif ($attendees <= 0) {
        echo "<script>alert('Number of attendees must be greater than 0!');</script>";
    } else {
        // Update booking in the database
        $updateQuery = "UPDATE bookings 
                        SET event_name = ?, event_date = ?, start_time = ?, end_time = ?, num_attendees = ?
                        WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssssii", $eventName, $eventDate, $startTime, $endTime, $attendees, $bookingId);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Booking updated successfully!');
                    window.location='manage_bookings.php';
                  </script>";
        } else {
            echo "<script>alert('Failed to update booking.');</script>";
        }
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Booking</h2>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Event Name:</label>
                <input type="text" name="event_name" class="form-control" value="<?= htmlspecialchars($booking['event_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Event Date:</label>
                <input type="date" name="event_date" class="form-control" value="<?= $booking['event_date'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Start Time:</label>
                <input type="time" name="start_time" class="form-control" value="<?= $booking['start_time'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">End Time:</label>
                <input type="time" name="end_time" class="form-control" value="<?= $booking['end_time'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Number of Attendees:</label>
                <input type="number" name="num_attendees" class="form-control" value="<?= $booking['num_attendees'] ?>" min="1" required>
            </div>
            <button type="submit" class="btn btn-success">Update Booking</button>
            <a href="manage_bookings.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
