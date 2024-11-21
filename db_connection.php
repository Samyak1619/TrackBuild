<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = ""; // Set to empty string if no password
$dbname = "construction_users";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
