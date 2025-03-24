<?php
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
    die("Query preparation failed: " . $conn->error);
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

    // Upload ID Photo
    $id_photo = "";
    if (!empty($_FILES['id_photo']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_name = time() . "_" . basename($_FILES["id_photo"]["name"]);
        $id_photo = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($id_photo, PATHINFO_EXTENSION));

        // Check file type
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
    $insertQuery = "INSERT INTO bookings  
        (venue_id, event_name, event_date, start_time, end_time, full_name, contact_number, email, num_attendees, total_cost, payment_method, shared_booking, id_photo)  
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insertQuery);

    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("issssssssdsss", $venue_id, $event_name, $event_date, $start_time, $end_time,  
        $full_name, $contact_number, $email, $num_attendees,  
        $total_cost, $payment_method, $shared_booking, $id_photo);
    
        if ($stmt->execute()) {
            echo "<script>
                alert('Booking Successful!');
                window.open('manage_bookings2.php', '_blank');
                window.location.href = 'manage_bookings.php';
            </script>";
            exit();
        }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($venue['name']); ?> - Venue Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #1a1a2e;
            color: #f0f0f0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        @media (min-width: 768px) {
    .venue-details {
        margin-bottom: 20px;
        flex: 0 0 100%;
        max-width: 75%;
        margin-left: -78%;
        margin-top: -185%;
    }
}
@media (min-width: 768px) {
    .col-md-8 {
        flex: 0 0 66.66%;
        max-width: 66.66%;
        margin-left: 70%;
        margin-top: -30%;
    }
}

        .container {
            max-width: 1100px;
            margin: auto;
            padding-left: 10px;
            padding-right: 10px;
        }

        .form-container {
            padding: 30px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
            backdrop-filter: blur(7px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.4);
        }

        .venue-details img {
         width: 70%;
         height: auto;
         border-radius: 8px;
         box-shadow: 0 3px 6px rgba(0, 0, 0, 0.4);
         margin-top: 5%;
}
        
        .section-header {
            background: #ff6b81;
            padding: 12px;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 15px;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.6);
        }

        .booking-form {
            flex: 1;
        }

        .venue-image {
            flex: 1;
            text-align: center;
        }

        .venue-image img {
            max-width: 100%;
            border-radius: 8px;
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.4);
            
        }


        .map-container {
            margin-top: 20px;
        }
        
@media (min-width: 1200px) {
    .map-container {
        margin-top: 10%;
    }
}

        .map-container iframe {
            width: 100%;
            height: 400px;
            border: 0;
            border-radius: 8px;
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.4);
        }

        @media (max-width: 768px) {
            .container {
                padding-left: 10px;
                padding-right: 10px;
            }

            .row {
                flex-direction: column;
            }

            .col-md-6 {
                width: 100%;
            }

            .form-container,
            .venue-details {
                margin-bottom: 20px;
            }

             
             .map-container iframe{
                height: 300px;
             }
        }

        @media (min-width: 768px) {
            .row {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
                align-items: stretch;
            }

            .col-md-4 {
                flex: 0 0 33.33%;
                max-width: 33.33%;
            }

            @media (min-width: 768px) {
    .col-md-8 {
        margin-top: -15%;
        flex: 0 0 66.66%;
        max-width: 66.66%;
    }
}
            .form-container {
                margin-bottom: 20px;
                flex: 0 0 70%;
                max-width: 70%;
            }

            .venue-details {
                margin-bottom: 20px;
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        @media (min-width: 1200px) {
            .container {
                padding-left: 0;
                padding-right: 0;
            }

            .h2,
            h2 {
                font-size: 2rem;
                margin-left: 0;
                color: #fff;
                text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.6);
            }

            p {
                margin-left: 0;
                color: #eee;
                font-size: 0.9rem;
            }

            .map-container {}
        }

        .form-control:focus {
            border-color: #ff6b81;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 129, 0.2);
        }

        .btn-primary {
            background-color: #ff6b81;
            border-color: #ff6b81;
            color: #fff;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            font-size: 0.9rem;
        }

        .btn-primary:hover {
            background-color: #ff4757;
            border-color: #ff4757;
            transform: translateY(-1px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
        }

        .btn-secondary {
            background-color: #4a148c;
            border-color: #4a148c;
            color: #fff;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            font-size: 0.9rem;
        }

        .btn-secondary:hover {
            background-color: #311159;
            border-color: #311159;
            transform: translateY(-1px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
        }

        label {
            color: #fff;
            font-weight: bold;
            margin-bottom: 6px;
            display: block;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        input[type="email"],
        input[type="number"],
        textarea,
        select {
            background-color: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            border-radius: 5px;
            padding: 8px;
            margin-bottom: 12px;
            width: 100%;
            transition: all 0.2s ease;
            font-size: 0.9rem;
            backdrop-filter: blur(3px);
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="time"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #ff6b81;
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 129, 0.2);
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        .form-check-input:checked {
            background-color: #ff6b81;
            border-color: #ff6b81;
        }

        .form-check-label {
            color: #fff;
            font-weight: normal;
            margin-left: 3px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            font-size: 0.9rem;
        }
        
        .sidebar { 
            background: #383f45;
            padding: 20px;
            color: white;
            width: 100%; /* Full width for small screens */
            box-sizing: border-box; /* Include padding in width */
        }

        @media (min-width: 768px) {
            .sidebar {
                width: 250px; /* Fixed width for larger screens */
                height: 100vh; /* Full height on larger screens */
                position: fixed; /* Fixed positioning for larger screens */
                overflow-y: auto; /* Enable vertical scrolling if needed on larger screens */
                margin-left: -16%;
                margin-top: -5%;
            }
            body{
                flex-direction: row;
            }
        }
        
        .sidebar h3 {
            color: white;
            font-size: calc(1rem + .5vw); /* Responsive font size */
            margin-bottom: 1rem;
            padding-left: 0;
        }

        .sidebar .nav-link {
            color: #fff;
            font-family: "Roboto", "Helvetica Neue", sans-serif;
            font-size: calc(1rem + .2vw);  /* Responsive font size */
            font-weight: bold;
            padding: 0.5rem 0;
            display: block; /* Ensure each link takes full width */
        }

        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }

        .sidebar .nav-flex-column {
            margin-top: 8rem;
        }
        
    </style>
</head>
<body>

<div class="container mt-4">
    <a href="list_venues.php" class="btn btn-secondary">← Back to Venues</a>
    <div class="sidebar">
        <h3>Ventech Venue</h3>
        <nav class="nav flex-column nav-flex-column">
            <a href="dashboard.php" class="nav-link">
                <i class="material-icons">📍</i> Map
            </a>
            <a href="list_venues.php" class="nav-link">
                <i class="material-icons">🏢</i> List Venue
            </a>
            <a href="#" class="nav-link">
                <i class="material-icons">💬</i> Enquiries
            </a>
            <a href="manage_bookings.php" class="nav-link">
                <i class="material-icons">📖</i> Bookings
            </a>
            <br>
            <a href="signout.php" class="nav-link">
                <i class="material-icons">🔑</i> Sign-out
            </a>
            <a href="profile.php" class="nav-link">
                <i class="material-icons">👤</i> Profile
            </a>
        </nav>
    </div>
        <div class="col-md-8">
            <div class="form-container">
                <h4 class="mb-3">Molino 3 Bacoor 3 Basketball Court Event Booking Form</h4>
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
                            <input type="text" name="venue" class="form-control" value="<?= htmlspecialchars($venue['name']); ?>" style="width: 100%;" required>
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

                    <label>Full Name:</label>
                    <input type="text" name="full_name" class="form-control" required>

                    <label>Contact Number:</label>
                    <input type="text" name="contact_number" class="form-control" required>
                    <label>Email Address:</label>
                    <input type="email" name="email" class="form-control" required>

                    <label>Number of Attendees:</label>
                    <input type="number" name="num_attendees" class="form-control" required>

                    <label>Upload ID for Verification:</label>
                    <input type="file" name="id_photo" class="form-control" accept="image/*" required>

                    <label>Additional Requests:</label>
                    <textarea name="requests" class="form-control"></textarea>

                    <label>Total Cost:</label>
                    <input type="text" class="form-control" value="₱<?= number_format($venue['price'], 2); ?>" readonly>

                    <label>Payment Method:</label>
                    <input type="radio" name="payment_method" value="Cash" required> Cash
                    <input type="radio" name="payment_method" value="Credit/Debit"> Credit/Debit
                    <input type="radio" name="payment_method" value="Online"> Online

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="shared_booking" id="shared_booking" value="1">
                        <label class="form-check-label" for="shared_booking">
                            Allow shared booking
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Book Now</button>
                </form>
            </div>
            <div class="venue-details">
                <h2>Molino 3 Bacoor 3 Basketball Court</h2>
                <img src="<?= htmlspecialchars($venue['image']); ?>" alt="<?= htmlspecialchars($venue['name']); ?>">
                <p class="mt-3"><?= nl2br(htmlspecialchars($venue['description'])); ?></p>
                 <p> Molino 1 (Progressive 18) Covered Court</p>
                  <p>Samal, 4102 Cavite</p>
            </div>

            <div class="venue-details">
                <h2>Molino 3 Bacoor 3 Basketball Court</h2>
                <img src="<?= htmlspecialchars($venue['image']); ?>" alt="<?= htmlspecialchars($venue['name']); ?>">
                <p class="mt-3"><?= nl2br(htmlspecialchars($venue['description'])); ?></p>
                 <p> Molino 1 (Progressive 18) Covered Court</p>
                  <p>Samal, 4102 Cavite</p>
            </div>
        </div>
    </div>
</div>



<?php if ($venue_id == 1 || $venue_id == 2) { ?>
    <div class="container map-container">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.715672218494!2d120.97303237649983!3d14.55351458591883!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d300a31d7641%3A0x459c7332f81c294a!2sMolino%201%20(Progressive%2018)%20Covered%20Court!5e0!3m2!1sen!2sph!4v1742210443642!5m2!1sen!2sph"
            width="100%"
            height="400"
            style="border:0; border-radius:8px; box-shadow:0px 3px 6px rgba(0, 0, 0, 0.4);"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
<?php } ?>
</body>
</html>
