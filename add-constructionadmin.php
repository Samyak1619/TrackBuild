<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Database connection
require_once 'db_connection.php';

try {
    // Fetch the logged-in user's ID from the session
    $user_id = $_SESSION['userid'];

    // Query to fetch user details
    $sql = "SELECT * FROM user_registration WHERE userid = ? LIMIT 1";
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters and execute
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Check if the user is an admin
            if ($user['role'] !== 'admin') {
                echo "<script>alert('Access Denied: You are not authorized to view this page.');</script>";
                header("Location: login.php");
                exit();
            }
        } else {
            echo "<script>alert('User not found. Please log in again.');</script>";
            session_destroy();
            header("Location: login.php");
            exit();
        }

        $stmt->close();
    } else {
        throw new Exception("Error: Could not prepare query.");
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and sanitize form inputs
        $constructionName = htmlspecialchars($_POST['construction-name']);
        $description = htmlspecialchars($_POST['description']);
        $constructionType = htmlspecialchars($_POST['construction-type']);
        $completionDate = $_POST['completion-date'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        // Check if completion date is valid
        if (empty($completionDate)) {
            echo "<script>alert('Please select a valid completion date.');</script>";
            exit();
        }

        // Get today's date in YYYY-MM-DD format
        $today = date('Y-m-d');

        // Compare the selected completion date with today's date
        if ($completionDate <= $today) {
            echo "<script>alert('The expected completion date must be greater than today.');</script>";
            exit();
        }

        // Generate unique construction ID
        $initials = strtoupper(substr($constructionType, 0, 1)); // First letter of construction type
        $timestamp = time(); // Use current timestamp
        $constructionID = $initials . $timestamp;

        // Check if latitude and longitude are valid numbers
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            echo "<script>alert('Invalid latitude or longitude values.');</script>";
            exit();
        }

        // Ensure completion date format is correct for MySQL
        $completionDateFormatted = date('Y-m-d', strtotime($completionDate));

        // Insert construction details into the database
        $stmt = $conn->prepare("INSERT INTO constructions (construction_id, construction_name, description, construction_type, expected_completion_date, latitude, longitude, user_id) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssds", $constructionID, $constructionName, $description, $constructionType, $completionDateFormatted, $latitude, $longitude, $user_id);

        // Execute the query
        if ($stmt->execute()) {
            echo "<script>alert('Construction details added successfully!');</script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Close the database connection
$conn->close();
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Construction</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  
    <!-- Include Google Maps JavaScript API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_API_KEY&callback=initMap" async defer></script>
    <script>
// Function to handle reverse geocoding and display the address using Nominatim (OpenStreetMap)
function getAddressFromCoordinates(latitude, longitude) {
    const url = `https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data && data.display_name) {
                const address = data.display_name; // Address returned by Nominatim
                document.getElementById('address').value = address; // Set the address in the form field
            } else {
                document.getElementById('address').value = "Address not found.";
            }
        })
        .catch(error => {
            console.error("Error fetching address:", error);
            document.getElementById('address').value = "Error fetching address.";
        });
}

// Initialize Google Map
let map;
let marker;

function initMap() {
    // Default location (fallback if geolocation is not available)
    const defaultLocation = { lat: 19.8803456, lng: 75.3401856 };

    // Create a new map centered on default location
    map = new google.maps.Map(document.getElementById("map"), {
        center: defaultLocation,
        zoom: 15,
    });

    // Create a marker at the current location
    marker = new google.maps.Marker({
        position: defaultLocation,
        map: map,
    });

    // Try to get the current geolocation of the user
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                const userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };

                // Set map center to user's location
                map.setCenter(userLocation);
                marker.setPosition(userLocation);

                // Update the form with the location
                document.getElementById('current-location').value = `${userLocation.lat}, ${userLocation.lng}`;

                // Update the hidden latitude and longitude inputs
                document.getElementById('latitude').value = userLocation.lat;
                document.getElementById('longitude').value = userLocation.lng;

                // Call the function to get address from coordinates
                getAddressFromCoordinates(userLocation.lat, userLocation.lng);
            },
            function (error) {
                console.error("Error getting location:", error);
                document.getElementById('address').value = "Unable to fetch location.";
            }
        );
    }
}
</script>

<style>
    /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body and Background */
        body {
            font-family: 'Roboto', sans-serif;
            background-image: url(images/construction_img.webp);
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #2c3e50;
        }

        /* Full-page layout */
        .container {
            display: flex;
            height: 100vh;
            background-color: rgba(255, 255, 255, 0.8);
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: #fff;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.2);
        }

        .sidebar h1 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            margin-bottom: 20px;
            padding: 10px;
            display: block;
            background: #e67e22;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #d35400;
        }

        /* Main content */
        .main-content {
            flex: 1;
            padding: 40px;
            overflow-y: scroll;
        }

        .main-content h2 {
            font-size: 26px;
            font-weight: 700;
            color: #34495e;
            margin-bottom: 30px;
        }

        /* Form styling */
        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group textarea {
            resize: vertical;
            height: 100px;
        }

        .submit-btn {
            display: inline-block;
            background-color: #e67e22;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #d35400;
        }

        /* Google Map styling */
        #map {
            height: 300px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Responsive design */
        @media(max-width: 768px) {
            .container {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <h1>Add Construction</h1>
            
        </div>
        <div>
            <a href="admin_dashboard.php">Back to Dashboard</a>
        </div>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h2>Add New Construction</h2>

        <!-- Form to Add Construction -->
<div class="form-container">
    <form action="add-constructionadmin.php" method="POST" onsubmit="generateConstructionID()">
        <!-- Display User ID -->
        <div class="form-group">
            <label for="user-id">User ID:</label>
            <input type="text" id="user_id" name="user-id" value="<?php echo $userid; ?>" readonly>
        </div>

        <!-- Construction Name -->
        <div class="form-group">
            <label for="construction-name">Construction Name:</label>
            <input type="text" id="construction-name" name="construction-name" required>
        </div>

        <!-- Construction Description -->
        <div class="form-group">
            <label for="description">Construction Description:</label>
            <textarea id="description" name="description" required></textarea>
        </div>

        <!-- Construction Type -->
        <div class="form-group">
            <label for="construction-type">Construction Type:</label>
            <select id="construction-type" name="construction-type" required>
                <option value="Residential">Residential</option>
                <option value="Commercial">Commercial</option>
                <option value="Industrial">Industrial</option>
            </select>
        </div>

         <!-- Completion Date -->
    <div class="form-group">
        <label for="completion-date">Expected Completion Date:</label>
        <input type="date" id="completion-date" name="completion-date" required>
    </div>

        <!-- Current Location -->
        <div class="form-group">
            <label for="current-location">Current Location (Latitude, Longitude):</label>
            <input type="text" id="current-location" name="current-location" readonly>
        </div>

        <!-- Google Map Display -->
        <div class="form-group">
            <div id="map"></div>
        </div>

        <!-- Hidden Fields for Latitude, Longitude, and Construction ID -->
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
        <input type="hidden" id="construction-id" name="construction-id">

        <!-- Submit Button -->
        <div class="form-group">
            <input type="submit" class="submit-btn" value="Add Construction">
        </div>
    </form>
</div>
    </div>
</div>

<script>

function generateConstructionID() {
        const constructionType = document.getElementById('construction-type').value;
        const initials = constructionType.charAt(0).toUpperCase(); // R, C, I
        const timestamp = Date.now(); // Use timestamp for uniqueness
        const uniqueID = `${initials}${timestamp}`;
        document.getElementById('construction-id').value = uniqueID;
    }

    // Initialize Google Map
    let map;
    let marker;

    function initMap() {
        // Default location (fallback if geolocation is not available)
        const defaultLocation = { lat: 19.8803456, lng: 75.3401856 };

        // Create a new map centered on default location
        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultLocation,
            zoom: 15,
        });

        // Create a marker at the current location
        marker = new google.maps.Marker({
            position: defaultLocation,
            map: map,
        });

        // Try to get the current geolocation of the user
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };

                    // Set map center to user's location
                    map.setCenter(userLocation);
                    marker.setPosition(userLocation);

                    // Update the form with the location
                    document.getElementById('current-location').value = `${userLocation.lat}, ${userLocation.lng}`;

                    // Update the hidden latitude and longitude inputs
                    document.getElementById('latitude').value = userLocation.lat;
                    document.getElementById('longitude').value = userLocation.lng;
                },
                function (error) {
                    console.error("Error getting location:", error);
                }
            );
        }
    }
</script>

</body>

</html>
