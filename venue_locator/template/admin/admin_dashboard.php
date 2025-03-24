<?php
session_start();

$configPath = realpath(__DIR__ . "/../config.php");
if ($configPath && file_exists($configPath)) {
    include $configPath;
} else {
    die("Error: Configuration file not found.");
}

if (!isset($conn)) {
    die("Error: Database connection failed.");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../signin.php");
    exit();
}

// Check correct table name (users vs. user_admin)
$userQuery = $conn->query("SELECT COUNT(*) AS total_users FROM user_admin"); 
if (!$userQuery) {
    die("Error in SQL query: " . $conn->error);
}
$userData = $userQuery->fetch_assoc();
$totalUsers = $userData['total_users'] ?? 0;

$bookingQuery = $conn->query("SELECT COUNT(*) AS total_bookings FROM bookings");
$bookingData = $bookingQuery->fetch_assoc();
$totalBookings = $bookingData['total_bookings'] ?? 0;

$venueQuery = $conn->query("SELECT COUNT(*) AS total_venues FROM venues");
$venueData = $venueQuery->fetch_assoc();
$totalVenues = $venueData['total_venues'] ?? 0;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #0d6efd;
            color: white;
            padding: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            margin-top: 10px;
        }
        .sidebar a:hover {
            background-color: #0056b3;
            border-radius: 5px;
        }
        .dashboard-content {
            flex-grow: 1;
            padding: 20px;
        }
        .logout-btn {
            position: absolute;
            top: 10px;
            right: 20px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="/venue_locator/template/admin/admin_dashboard.php">🏠 Dashboard</a>
    <a href="/venue_locator/template/admin/manage_bookings2.php">📅 Manage Bookings</a>
    <a href="/venue_locator/template/admin/admin_record.php">👤 Users</a>
    <a href="manage_venues.php">📍 Manage Venues</a>
</div>

<!-- Main Content -->
<div class="dashboard-content">
    <button class="btn btn-danger logout-btn" onclick="window.location.href='logout.php'">Logout</button>
    <h2>Dashboard</h2>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text fs-2"><?php echo $totalUsers; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Bookings</h5>
                    <p class="card-text fs-2"><?php echo $totalBookings; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Venues</h5>
                    <p class="card-text fs-2"><?php echo $totalVenues; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
