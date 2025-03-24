<?php 
include 'config.php';

// Start session and check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php"); // Redirect to login page if not logged in
    exit();
}

$query = "SELECT * FROM properties";
$result = $conn->query($query);
$properties = [];

while ($row = $result->fetch_assoc()) {
    $properties[] = $row;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VENTECH</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script> <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">


    <style>
        body { 
            overflow: auto; /* Change overflow to auto to enable scrolling */
            display: flex;
            flex-direction: column; /* Start with column layout for small screens */
            min-height: 100vh; /* Ensure full viewport height */
            background-color: rgb(233, 233, 233);
            margin: 0; /* Reset default body margin */
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
        
        .search-bar {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 0.5rem;
            border-radius: 5px;
            display: flex;
            align-items: center;
            width: 90%; /* Use a percentage of the parent width */
            max-width: 300px; /* Limit the maximum width */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            margin: 1rem auto 0; /* Center the search bar and add margin */
            position: relative;
        }

        @media (min-width: 768px) {
            .search-bar {
                position: fixed; /* Fix search bar position on larger screens */
                top: 1rem;
                left: 260px; /* Adjust left position to account for sidebar width */
                transform: translateX(0); /* Reset horizontal centering */
                margin-left: 0;
            }
        }
        
        .search-bar input {
            border: none;
            outline: none;
            padding: 0.5rem;
            font-size: 1rem;
            width: 100%;
            border-radius: 20px;
        }
        
        .search-bar button {
            background-color: #ff6f61;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .search-bar button:hover {
            background-color: #e65c4f;
        }
        
        #map {
            height: 300px; /* Start with a reasonable default height for small screens */
            width: 100%;  /* Make map take full width */
            margin-top: 4rem; /* Add margin to separate from search bar on small screens */
            position: relative;
            z-index: 1;
            flex-grow: 1; /* Allow map to grow and take up available space */
        }

        @media (min-width: 768px) {
            #map {
                height: calc(100vh - 2rem); /* Adjust map height for larger screens, considering potential top margin */
                width: calc(100vw - 250px); /* Map width adjusted for sidebar */
                margin-left: 250px; /* Push map to the right of the fixed sidebar */
                margin-top: 0; /* Remove top margin on larger screens if not needed */
            }
        }
        
        .property-panel {
            background: #fff;
            padding: 1rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-height: 80vh; /* Use viewport height units */
            overflow-y: auto;
            margin-top: 1rem; /* Add margin for spacing */
            width: 90%;
            margin-left: auto;
            margin-right: auto;
            position: relative; /* Ensure relative positioning for z-index */
            z-index: 2; /* Give the property panel a higher z-index */
        }
        
        @media (min-width: 768px) {
            .property-panel {
                width: 300px;
                position: absolute;
                top: 1rem;
                right: 1rem;
                margin-top: 0;
            }
        }

        .property-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .card {
            margin-bottom: 1rem;
        }
        
        .marker-number {
            background-color: #007bff;
            color: white;
            font-size: 0.8rem;
            font-weight: bold;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .text-center{
            margin-top: 20%;
        }
    </style>
</head>
<body>
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

    <div class="main-content" style="display: flex; flex-direction: column; flex: 1;">
        <div class="search-bar">
            <input type="text" id="searchInput" onkeyup="filterMarkers()" placeholder="Search venues...">
            <button onclick="clearSearch()">Clear</button>
        </div>
        <div id="map" style="flex-grow: 1;"></div>
        <div class="property-panel">
            <h4>Venue Listings</h4>
            <?php foreach ($properties as $property): ?>
                <div class="card mb-3">
                    <img src="<?php echo $property['image_url']; ?>" class="property-image" alt="Property">
                    <div class="card-body">
                        <span class="badge bg-<?php echo strtolower($property['property_type']) == 'commercial' ? 'purple' : 'green'; ?>">
                            <?php echo ucfirst($property['property_type']); ?> VENTECH
                        </span>
                        <h5 class="card-title"><?php echo $property['title']; ?></h5>
                        <p class="card-text"><?php echo substr($property['description'], 0, 80); ?>...</p>
                        <p><strong>₱<?php echo number_format($property['price'], 2); ?></strong></p>
                        <button class="btn btn-primary">View Details</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        var map = L.map('map').setView([14.3914, 120.982], 15);

        // Load OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Venue locations with id added
        var locations = [
            { id: 1, name: "Molino 3 Bacoor 3 Basketball Court", lat: 14.3914, lng: 120.982 },
            { id: 2, name: "Bacoor City Hall", lat: 14.3895, lng: 120.984 },
            { id: 3, name: "SM City Bacoor", lat: 14.3950, lng: 120.980 },
            { id: 4, name: "St. Dominic College", lat: 14.3872, lng: 120.987 },
            { id: 5, name: "LOWLAND COVERED COURT", lat: 14.3750, lng: 120.985 },
            { id: 6, name: "Soldiers Hills IV Phase 2 Covered Court", lat: 14.3875, lng: 120.980 },
            { id: 7, name: "SAN LORENZO RUIZ HOMES COVERED COURT", lat: 14.3800, lng: 120.972 },
            { id: 8, name: "Molino 1 (Progressive 18) Covered Court", lat: 14.3925, lng: 120.975 }
        ];

        // Store markers for filtering
        var markers = [];

        // Add markers to the map
        locations.forEach(function(location) {
            var marker = L.marker([location.lat, location.lng]).addTo(map)
                .bindPopup(`
                    <b>${location.name}</b><br>
                    <button onclick="viewMore(${location.id})" class="btn btn-primary btn-sm mt-2">
                        View More
                    </button>
                `);
            markers.push({ name: location.name, marker: marker });
        });

        // Function to redirect to venue details page
        function viewMore(id) {
            window.location.href = "venue_details.php?id=" + id;
        }

        // Function to filter markers based on search
        function filterMarkers() {
            var input = document.getElementById("searchInput").value.toLowerCase();
            markers.forEach(function(entry) {
                if (entry.name.toLowerCase().includes(input)) {
                    if (!map.hasLayer(entry.marker)) { 
                        entry.marker.addTo(map); // Show if it matches
                    }
                } else {
                    entry.marker.remove(); // Hide if not a match
                }
            });
        }

        // Function to clear search and show all markers
        function clearSearch() {
            document.getElementById("searchInput").value = "";
            markers.forEach(function(entry) {
                if (!map.hasLayer(entry.marker)) {
                    entry.marker.addTo(map);
                }
            });
        }
    </script>
</body>
</html>
