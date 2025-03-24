<?php
session_start(); // Start session to track login status

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $isLoggedIn = false;
} else {
    $isLoggedIn = true;
}

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "venue_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get venue details
$venue_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM venues WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $venue_id);
$stmt->execute();
$result = $stmt->get_result();
$venue = $result->fetch_assoc();

if (!$venue) {
    echo "<script>alert('Venue not found!'); window.location.href='list_venues.php';</script>";
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!$isLoggedIn) {
        echo "<script>alert('You must be registered and logged in to book a venue!'); window.location.href='signup.php';</script>";
        exit;
    }
    
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $full_name = $_POST['full_name'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $num_attendees = $_POST['num_attendees'];
    $total_cost = $venue['price'];
    $payment_method = $_POST['payment_method'];
    $shared_booking = isset($_POST['shared_booking']) ? 1 : 0;

    // File upload handling
    $id_photo = null;
    if (!empty($_FILES['id_photo']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $id_photo = $target_dir . basename($_FILES["id_photo"]["name"]);
        $imageFileType = strtolower(pathinfo($id_photo, PATHINFO_EXTENSION));

        if (in_array($imageFileType, ["jpg", "jpeg", "png"])) {
            if (!move_uploaded_file($_FILES["id_photo"]["tmp_name"], $id_photo)) {
                echo "Error uploading ID Photo.";
                exit;
            }
        } else {
            echo "Only JPG, JPEG, and PNG files are allowed.";
            exit;
        }
    }

    // Insert Booking
    $insertQuery = "INSERT INTO bookings (venue_id, event_name, event_date, start_time, end_time, full_name, contact_number, email, num_attendees, total_cost, payment_method, shared_booking, id_photo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insertQuery);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("issssssssdsss", $venue_id, $event_name, $event_date, $start_time, $end_time, 
        $full_name, $contact_number, $email, $num_attendees, 
        $total_cost, $payment_method, $shared_booking, $id_photo);
    
    if ($stmt->execute()) {
        echo "<script>alert('Booking Successful!'); window.location.href='venue_details.php?id=$venue_id';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($venue['name']); ?> - Venue Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .container { max-width: 1200px; }
        .form-container { background: #f8f9fa; padding: 20px; border-radius: 10px; }
        .venue-details img { width: 100%; height: auto; border-radius: 10px; }
        .section-header { background: #FFD700; padding: 10px; font-weight: bold; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container mt-4">
    <a href="list_venues.php" class="btn btn-secondary">← Back to Venues</a>
    <div class="row mt-3">
        <!-- Booking Form -->
        <div class="col-md-6">
            <div class="form-container">
                <h4 class="mb-3">Event Booking Form</h4>
                <form method="POST" enctype="multipart/form-data">
                    <label>Event Name:</label>
                    <input type="text" name="event_name" class="form-control" required>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Date of Event:</label>
                            <input type="date" name="event_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Venue:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($venue['name']); ?>" disabled>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Start Time:</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>End Time:</label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                    </div>

                    <div class="section-header mt-3">Attendee Information</div>

                    <label>Full Name:</label>
                    <input type="text" name="full_name" class="form-control" required>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Contact Number:</label>
                            <input type="text" name="contact_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Email Address:</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>

                    <label class="mt-2">Number of Attendees:</label>
                    <input type="number" name="num_attendees" class="form-control" required>

                    <label class="mt-2">Upload School ID for Verification:</label>
                    <input type="file" name="id_photo" class="form-control" accept="image/*" required>

                    <label class="mt-2">Additional Requests:</label>
                    <textarea name="requests" class="form-control"></textarea>

                    <div class="section-header mt-3">Payment Details</div>

                    <label>Total Cost:</label>
                    <input type="text" class="form-control" value="₱<?= number_format($venue['price'], 2); ?>" readonly>

                    <label class="mt-2">Payment Method:</label><br>
                    <input type="radio" name="payment_method" value="Cash" required> Cash
                    <input type="radio" name="payment_method" value="Credit/Debit"> Credit/Debit
                    <input type="radio" name="payment_method" value="Online"> Online

                    <div class="mt-3">
                        <input type="checkbox" name="shared_booking" value="1"> I agree to share this booking with other events
                    </div>

                    <button type="button" class="btn btn-primary mt-3" id="bookNowBtn">Book Now</button>

                </form>
            </div>
        </div>

        <!-- Venue Details -->
        <div class="col-md-6">
            <div class="venue-details">
                <h2><?= htmlspecialchars($venue['name']); ?></h2>
                <img src="<?= htmlspecialchars($venue['image']); ?>" alt="<?= htmlspecialchars($venue['name']); ?>">
                <p class="mt-3"><?= nl2br(htmlspecialchars($venue['description'])); ?></p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("bookNowBtn").addEventListener("click", function() {
    var isLoggedIn = <?= json_encode($isLoggedIn); ?>;
    
    if (!isLoggedIn) {
        alert("You must be registered and logged in to book a venue!");
        window.location.href = "signup.php"; // Redirect to signup/login page
    } else {
        document.querySelector("form").submit(); // Submit form if logged in
    }
});
</script>


</body>
</html>
