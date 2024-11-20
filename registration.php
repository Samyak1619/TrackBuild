<?php
// Include the database connection file
require_once 'db_connection.php';

// Function to generate a unique user ID (6 digits)
function generateUniqueUserID($conn) {
    $uniqueID = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Check if the generated ID already exists
    $stmt = $conn->prepare("SELECT userid FROM user_registration WHERE userid = ?");
    $stmt->bind_param("s", $uniqueID);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // ID exists, generate another one
        return generateUniqueUserID($conn);  // Recursive call to ensure uniqueness
    } else {
        // Return the unique ID if it's not taken
        return $uniqueID;
    }
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $constructionType = $_POST['constructionType'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt password
    $latitude = $_POST['latitude']; // Get latitude from form
    $longitude = $_POST['longitude']; // Get longitude from form

    // Validation for required fields and format
    if (empty($name) || empty($email) || empty($phone) || empty($constructionType) || empty($password)) {
        echo "<script>alert('All fields are required!');</script>";
        exit;
    }

    // Validate email uniqueness
    $stmt = $conn->prepare("SELECT email FROM user_registration WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email already exists!');</script>";
        exit;
    }

    // Validate name (only alphabets)
    if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
        echo "<script>alert('Name should contain only alphabets!');</script>";
        exit;
    }

    // Validate password (at least 8 characters, one uppercase, one special character)
    if (!preg_match("/^(?=.*[A-Z])(?=.*\W).{8,}$/", $_POST['password'])) {
        echo "<script>alert('Password must be at least 8 characters long with one uppercase letter and one special character!');</script>";
        exit;
    }

    // Generate a unique user ID (6 digits)
    $userid = generateUniqueUserID($conn);

    // Insert data into the database
    $sql = "INSERT INTO user_registration (userid, name, email, phone, construction_type, password, latitude, longitude)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssss", $userid, $name, $email, $phone, $constructionType, $password, $latitude, $longitude);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!');</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        // Close statement
        $stmt->close();
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }

    // Close connection
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Construction Site Registration</title>
    <style> /* Body and Background */
 body {
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-image: url('images/construction_img.webp'); /* Background image */
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    font-family: 'Roboto', sans-serif;
    margin: 0;
    padding: 0;
    position: relative; /* Added for positioning of loader */
}

/* Universal Box-Sizing Fix */
* {
    box-sizing: border-box;
}

/* Loader Section */
#loader {
    position: absolute;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    z-index: 10;
    background: linear-gradient(90deg, #4a90e2, #e74c3c, #f39c12, #2ecc71, #8e44ad);
    background-size: 500% 500%;
    animation: colorShift 10s ease infinite;
}

@keyframes colorShift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

/* Building construction animation */
#construction-animation {
    width: 200px;
    height: 300px;
}

.layer {
    opacity: 0;
    transform-origin: bottom;
    animation: buildLayer 2s forwards;
}

/* Keyframes for building layers rising */
@keyframes buildLayer {
    0% {
        transform: scaleY(0) translateY(50px);
        opacity: 0;
    }
    50% {
        opacity: 0.5;
    }
    100% {
        transform: scaleY(1) translateY(0);
        opacity: 1;
    }
}

.foundation {
    animation-delay: 0s;
}

.walls {
    animation-delay: 1.5s;
}

.windows {
    animation-delay: 3s;
}

.roof {
    animation-delay: 4.5s;
}

/* Registration Form Container */
.registration-container {
    display: none; /* Initially hidden */
    background-color: rgba(255, 255, 255, 0.85); /* Semi-transparent background */
    padding: 30px;
    border-radius: 15px;
    width: 100%;
    max-width: 450px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 2px solid #ddd;
    margin: 20px;
    overflow: hidden; /* Ensure content doesn't overflow */
}

/* Heading */
h2 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 26px;
    color: #2c3e50;
    text-transform: uppercase;
    font-weight: bold;
    letter-spacing: 1.5px;
}

/* Labels for Inputs */
label {
    font-weight: bold;
    margin-top: 15px;
    display: block;
    font-size: 15px;
    color: #333;
}

/* Form Fields with Icons */
input, select {
    width: 100%;
    padding: 12px 40px 12px 12px; /* Extra padding for icon space */
    margin-top: 8px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    transition: border 0.3s ease, box-shadow 0.3s ease;
    box-sizing: border-box; /* Ensures padding doesn't make input overflow */
}

/* Input Field Focus Effect */
input:focus, select:focus {
    border-color: #e67e22;
    box-shadow: 0 0 6px rgba(230, 126, 34, 0.5);
    background-color: #fdfdfd;
}

/* Register Button */
button {
    width: 100%;
    padding: 12px;
    background-color: #e67e22;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

/* Hover Effect for Button */
button:hover {
    background-color: #d35400;
    transform: scale(1.05);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

/* Map Container */
#map {
    width: 100%;
    height: 410px;
    margin-bottom: 20px;
    border: 2px solid #ddd;
    border-radius: 5px;
}

/* Placeholder Text Style */
input::placeholder {
    font-style: italic;
    color: #aaa;
}

/* Smooth Transition for Inputs on Hover */
input:hover {
    border-color: #b2bec3;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Image Style - For Responsive Image Handling */
img {
    width: 100%;
    max-width: 1024px;
    height: auto;
    display: block;
    margin: 0 auto;
}

/* Responsive Adjustments for Small Screens */
@media (max-width: 768px) {
    .registration-container {
        width: 95%;
        padding: 20px;
    }

    h2 {
        font-size: 22px;
    }

    input, select, button {
        font-size: 14px;
    }

    button {
        padding: 10px;
    }
}</style>
</head>
<body>

<!-- Loader Section -->
<div id="loader">
    <!-- Construction Animation -->
    <svg id="construction-animation" viewBox="0 0 200 300">
        <rect x="20" y="250" width="160" height="30" fill="#34495e" class="layer foundation" />
        <rect x="40" y="170" width="120" height="80" fill="#3498db" class="layer walls" />
        <rect x="50" y="180" width="20" height="20" fill="#ecf0f1" class="layer windows" />
        <rect x="130" y="180" width="20" height="20" fill="#ecf0f1" class="layer windows" />
        <rect x="50" y="210" width="20" height="20" fill="#ecf0f1" class="layer windows" />
        <rect x="130" y="210" width="20" height="20" fill="#ecf0f1" class="layer windows" />
        <polygon points="20,170 180,170 100,130" fill="#e74c3c" class="layer roof" />
    </svg>
</div>

<div class="registration-container">
    <h2>Register Your Account</h2>

    <form id="registrationForm" action="registration.php" method="POST">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required placeholder="Enter your name">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required placeholder="Enter your email">

        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" required pattern="[0-9]{10}" placeholder="Enter 10-digit mobile number">

        <label for="constructionType">Construction Type</label>
        <select id="constructionType" name="constructionType" required>
            <option value="" disabled selected>Select Construction Type</option>
            <option value="residential">Residential</option>
            <option value="commercial">Commercial</option>
            <option value="industrial">Industrial</option>
        </select>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required placeholder="Enter your password">

        <label for="latitude">Latitude</label>
        <input type="text" id="latitude" name="latitude" readonly placeholder="Latitude">

        <label for="longitude">Longitude</label>
        <input type="text" id="longitude" name="longitude" readonly placeholder="Longitude">

        <div id="map"></div>

        <button type="submit">Register</button>
    </form>
</div>

<script>
    // Simulate loader timeout before showing the form
    setTimeout(() => {
        document.getElementById('loader').style.display = 'none';
        document.querySelector('.registration-container').style.display = 'block';
    }, 6000); // Show loader for 6 seconds

    // Fetch User's Current Location Using Geolocation API
    document.addEventListener('DOMContentLoaded', function () {
        const latitudeInput = document.getElementById("latitude");
        const longitudeInput = document.getElementById("longitude");
        const mapElement = document.getElementById("map");

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Display Latitude and Longitude in respective Input Fields
                    latitudeInput.value = lat;
                    longitudeInput.value = lng;

                    // Initialize the map with user's location
                    const map = new google.maps.Map(mapElement, {
                        center: { lat: lat, lng: lng },
                        zoom: 15,
                    });

                    // Add marker for the user's location
                    const marker = new google.maps.Marker({
                        position: { lat: lat, lng: lng },
                        map: map,
                        title: "Your Location",
                    });
                },
                (error) => {
                    console.error("Error getting location: ", error.message);
                    latitudeInput.value = "Unable to fetch location!";
                    longitudeInput.value = "Unable to fetch location!";
                }
            );
        } else {
            latitudeInput.value = "Geolocation is not supported by this browser.";
            longitudeInput.value = "Geolocation is not supported by this browser.";
        }
    });

    // Google Maps Initialization for Autocomplete
    function initMap() {
        const map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: -34.397, lng: 150.644 },
            zoom: 8,
        });

        const input = document.getElementById("location");
        const autocomplete = new google.maps.places.Autocomplete(input);

        autocomplete.addListener("place_changed", function () {
            const place = autocomplete.getPlace();
            if (place.geometry) {
                map.setCenter(place.geometry.location);
                map.setZoom(15); // Zoom into the place
            }
        });
    }

    // Add event listener to submit form after location is populated
    const form = document.getElementById('registrationForm');
    form.addEventListener('submit', function(event) {
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');

        if (latitudeInput.value === "" || longitudeInput.value === "") {
            alert("Location is required!");
            event.preventDefault();  // Prevent form submission if location is empty
        }
    });
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places&callback=initMap" async defer></script>

</body>
</html>
