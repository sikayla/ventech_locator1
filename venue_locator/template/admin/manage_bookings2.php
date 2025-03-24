<?php  
session_start();
$conn = new mysqli("localhost", "root", "", "venue_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch bookings with venue details
$query = "SELECT b.*, v.name AS venue_name  
          FROM bookings b
          JOIN venues v ON b.venue_id = v.id
          ORDER BY b.id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Bookings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        
body {
    margin: 0;
    font-family: var(--bs-body-font-family);
    font-size: var(--bs-body-font-size);
    font-weight: var(--bs-body-font-weight);
    line-height: var(--bs-body-line-height);
    color: var(--bs-body-color);
    text-align: var(--bs-body-text-align);
    background-color: #151515;
    -webkit-text-size-adjust: 100%;
    -webkit-tap-highlight-color: transparent;
}
        .booking-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            position: relative;
            background: #fff;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .status-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-pending { background: #f4d03f; } /* Yellow */
        .status-approved { background: #2a9d8f; } /* Green */
        .status-rejected { background: #e63946; } /* Red */
        .booking-buttons button {
            width: 100%;
            margin-top: 10px;
        }
        .btn-approve { background: #7b61ff; color: white; }
        .btn-reject { background: #e63946; color: white; }
        .row {
    --bs-gutter-x: 1.5rem;
    --bs-gutter-y: 0;
    display: flex;
    flex-wrap: wrap;
    margin-top: calc(-1* var(--bs-gutter-y));
    margin-right: calc(-.5* var(--bs-gutter-x));
    margin-left: calc(-.5* var(--bs-gutter-x));
    margin-top: 10%;
}
.h5, h5 {
    font-size: 1.25rem;
    margin-left: 25%;
}
@media (min-width: 1200px) {
    .h2, h2 {
        font-size: 2rem;
        color: white;
    }
}
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Admin - Manage Bookings</h2>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="col-md-4">
                    <div class="booking-card">
                        <span class="status-badge 
                            <?= $row['status'] == 'Approved' ? 'status-approved' : ($row['status'] == 'Rejected' ? 'status-rejected' : 'status-pending') ?>">
                            <?= $row['status'] ?>
                        </span>

                        <h5><?= htmlspecialchars($row['full_name']) ?></h5>
                        <p class="text-muted"><?= $row['email'] ?></p>
                        <p><strong>👥 Attendees:</strong> <?= $row['num_attendees'] ?></p>
                        <p><strong>📅 Date:</strong> <?= date("l, F j, Y", strtotime($row['event_date'])) ?></p>
                        <p><strong>⏰ Time:</strong> <?= date("H:i", strtotime($row['start_time'])) ?> - <?= date("H:i", strtotime($row['end_time'])) ?></p>
                        <p><strong>📍 Venue:</strong> <?= htmlspecialchars($row['venue_name']) ?></p>

                        <div class="booking-buttons">
                            <button class="btn btn-approve approve-btn" data-id="<?= $row['id'] ?>">APPROVE</button>
                            <button class="btn btn-reject reject-btn" data-id="<?= $row['id'] ?>">REJECT</button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(".approve-btn, .reject-btn").click(function() {
                let bookingId = $(this).data("id");
                let action = $(this).hasClass("approve-btn") ? "Approved" : "Rejected";

                $.ajax({
                    url: "update_status.php",
                    type: "POST",
                    data: { booking_id: bookingId, action: action },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    }
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
