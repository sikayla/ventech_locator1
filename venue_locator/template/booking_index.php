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
  <title>Manage Bookings</title> 
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"> 
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> 
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> 
  <style> 
  body { 
    overflow: auto;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background-color: rgb(233, 233, 233);
    margin: 0;
}

.sidebar { 
    background: #383f45;
    padding: 20px;
    color: white;
    width: 250px;
    box-sizing: border-box;
    position: fixed;
    height: 100vh;
    overflow-y: auto;
    left: 0; /* Sidebar is now fixed and visible */
    transition: none; /* Remove transition */
    z-index: 1001; /* Ensure it's above other elements */
}


.sidebar h3 {
    color: white;
    font-size: calc(1rem + .5vw);
    margin-bottom: 1rem;
    padding-left: 0;
}

.sidebar .nav-link {
    color: #fff;
    font-family: "Roboto", "Helvetica Neue", sans-serif;
    font-size: calc(1rem + .2vw);
    font-weight: bold;
    padding: 0.5rem 0;
    display: block;
}

.sidebar .nav-link i {
    margin-right: 0.5rem;
}

.sidebar .nav-flex-column {
    margin-top: 8rem;
}

.main-content {
    flex: 1; /* Allow main content to take up remaining space */
    padding-left: 250px; /* Adjust padding to account for fixed sidebar */
    transition: none; /* Remove transition */
}
  
  h2 { 
  text-align: center; 
  margin-bottom: 20px; 
  } 
  
  .card { 
  border-radius: 10px; 
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
  transition: transform 0.3s; 
  } 
  
  .card:hover { 
  transform: scale(1.02); 
  } 
  
  .badge { 
  font-size: 14px; 
  } 
  
  .dropdown-menu { 
  min-width: auto; 
  } 
  
  /* Responsive Grid */ 
  .row { 
  margin: 0 auto; 
  justify-content: center; 
  } 
  
  /* Adjust card width for different screen sizes */ 
  @media (max-width: 576px) { /* Mobile */ 
  .col-custom { 
  width: 100%; 
  } 
  } 
  
  @media (min-width: 577px) and (max-width: 991px) { /* Tablets */ 
  .col-custom { 
  width: 50%; 
  } 
  } 
  
  @media (min-width: 992px) { /* Laptops & Desktops */ 
  .col-custom { 
  width: 33.33%; 
  } 
  } 
  .img-fluid { 
  max-width: 20%; 
  height: auto; 
  }
 
  </style> 
</head> 
<body> 
    <div class="sidebar" id="sidebar">
        <h3>Ventech Venue</h3>
        <nav class="nav flex-column nav-flex-column">
            <a href="index.php" class="nav-link">
                <i class="material-icons">📍</i> Map
            </a>
            <a href="index_list.php" class="nav-link">
                <i class="material-icons">🏢</i> List Venue
            </a>
            <a href="#" class="nav-link">
                <i class="material-icons">💬</i> Enquiries
            </a>
            <a href="manage_bookings.php" class="nav-link">
                <i class="material-icons">📖</i> Bookings
            </a>
            <br>
            <a href="signup.php" class="nav-link">
                <i class="material-icons">🔑</i>Register
            </a>
            <a href="signin.php" class="nav-link">
                <i class="material-icons">👤</i> Sign-in
            </a>
        </nav>
    </div>

<div class="main-content" id="mainContent"> 
  <div class="container mt-4"> 
  <h2>Manage Bookings</h2> 
  
  <div class="row g-3"> 
  <?php while ($row = $result->fetch_assoc()) { ?> 
  <div class="col-custom p-2"> 
  <div class="card p-3"> 
  <span class="badge bg-warning text-dark"><?= htmlspecialchars($row['status']) ?></span> 
  <h5 class="mt-2"><?= htmlspecialchars($row['full_name']) ?></h5> 
  <p class="text-muted"><?= htmlspecialchars($row['email']) ?></p> 
  <p><strong>👥 Attendees:</strong> <?= $row['num_attendees'] ?></p> 
  <p><strong>📍 Venue:</strong> <?= htmlspecialchars($row['venue_name']) ?></p> 
  <p><strong>📅 Date:</strong> <?= $row['event_date'] ?></p> 
  <p><strong>⏰ Time:</strong> <?= date("h:i A", strtotime($row['start_time'])) . " - " . date("h:i A", strtotime($row['end_time'])) ?></p> 
  
  <div class="dropdown"> 
  <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"> 
  Actions 
  </button> 
  <ul class="dropdown-menu"> 
  <li> 
  <a class="dropdown-item view-details" href="#" data-bs-toggle="modal" data-bs-target="#viewModal" 
  
  data-id="<?= $row['id'] ?>"  
  data-event="<?= htmlspecialchars($row['event_name']) ?>"  
  data-date="<?= $row['event_date'] ?>" 
  data-start="<?= date('h:i A', strtotime($row['start_time'])) ?>" 
  data-end="<?= date('h:i A', strtotime($row['end_time'])) ?>" 
  data-venue="<?= htmlspecialchars($row['venue_name']) ?>" 
  data-name="<?= htmlspecialchars($row['full_name']) ?>" 
  data-contact="<?= $row['contact_number'] ?>" 
  data-email="<?= $row['email'] ?>" 
  data-attendees="<?= $row['num_attendees'] ?>" 
  data-cost="<?= number_format($row['total_cost'], 2) ?>" 
  data-payment="<?= htmlspecialchars($row['payment_method']) ?>" 
  data-status="<?= htmlspecialchars($row['status']) ?>" 
  data-photo="uploads/<?= basename($row['id_photo']) ?>"> 
  <i class="fas fa-eye text-primary"></i> View 
  </a> 
  </li> 
  <li><a class="dropdown-item" href="edit_booking.php?id=<?= $row['id'] ?>"><i class="fas fa-edit text-success"></i> Edit</a></li> 
  <li><a class="dropdown-item text-danger delete-booking" href="#" data-id="<?= $row['id'] ?>"> 
  <i class="fas fa-trash text-danger"></i> Delete 
  </a></li> 
  </ul> 
  </div> 
  
  </div> 
  </div> 
  <?php } ?> 
  </div> 
  </div> 
  
  <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true"> 
  <div class="modal-dialog"> 
  <div class="modal-content"> 
  <div class="modal-header"> 
  <h5 class="modal-title" id="viewModalLabel">Booking Details</h5> 
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> 
  </div> 
  <div class="modal-body"> 
  <p><strong>ID:</strong> <span id="view-id"></span></p> 
  <p><strong>Event:</strong> <span id="view-event"></span></p> 
  <p><strong>Date:</strong> <span id="view-date"></span></p> 
  <p><strong>Time:</strong> <span id="view-time"></span></p> 
  <p><strong>Venue:</strong> <span id="view-venue"></span></p> 
  <p><strong>Full Name:</strong> <span id="view-name"></span></p> 
  <p><strong>Contact:</strong> <span id="view-contact"></span></p> 
  <p><strong>Email:</strong> <span id="view-email"></span></p> 
  <p><strong>Attendees:</strong> <span id="view-attendees"></span></p> 
  <p><strong>Total Cost:</strong> ₱<span id="view-cost"></span></p> 
  <p><strong>Payment:</strong> <span id="view-payment"></span></p> 
  <p><strong>Status:</strong> <span id="view-status"></span></p> 
  <p><strong>ID Photo:</strong></p> 
  <img id="view-photo" src="" alt="ID Photo" class="img-fluid"> 
  </div> 
  <div class="modal-footer"> 
  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> 
  </div> 
  </div> 
  </div> 
  </div> 
  </div>
  
  <script> 
  $(document).ready(function () { 
  $(".view-details").click(function () { 
  let modal = $("#viewModal"); 
  
  // Get data from clicked item 
  let id = $(this).data("id"); 
  let event = $(this).data("event"); 
  let date = $(this).data("date"); 
  let start = $(this).data("start"); 
  let end = $(this).data("end"); 
  let venue = $(this).data("venue"); 
  let name = $(this).data("name"); 
  let contact = $(this).data("contact"); 
  let email = $(this).data("email"); 
  let attendees = $(this).data("attendees"); 
  let cost = $(this).data("cost"); 
  let payment = $(this).data("payment"); 
  let status = $(this).data("status"); 
  let photo = $(this).data("photo"); 
  
  // Populate modal with data 
  modal.find("#view-id").text(id); 
  modal.find("#view-event").text(event); 
  modal.find("#view-date").text(date); 
  modal.find("#view-time").text(start + " - " + end); 
  modal.find("#view-venue").text(venue); 
  modal.find("#view-name").text(name); 
  modal.find("#view-contact").text(contact); 
  modal.find("#view-email").text(email); 
  modal.find("#view-attendees").text(attendees); 
  modal.find("#view-cost").text(cost); 
  modal.find("#view-payment").text(payment); 
  modal.find("#view-status").text(status); 
  
  // Handle image display 
  if (photo) { 
  modal.find("#view-photo").attr("src", photo).show(); 
  } else { 
  modal.find("#view-photo").attr("src", "default-placeholder.jpg").show(); 
  } 
  
  modal.modal("show"); // Open modal 
  }); 
  }); 
  
  $(document).ready(function () { 
  $(".delete-booking").click(function () { 
  var bookingId = $(this).data("id"); 
  
  if (confirm("Are you sure you want to delete this booking?")) { 
  $.ajax({ 
  url: "delete_booking.php", 
  type: "POST", 
  data: { booking_id: bookingId }, 
  dataType: "json", 
  success: function (response) { 
  if (response.success) { 
  alert("Booking deleted successfully."); 
  location.reload(); // Refresh the page 
  } else { 
  alert("Error: " + response.message); 
  } 
  }, 
  error: function () { 
  alert("Failed to delete booking."); 
  } 
  }); 
  } 
  }); 
  }); 

  </script> 
  
  
  
  
  </body> 
  </html>



