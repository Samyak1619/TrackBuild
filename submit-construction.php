<?php 
// Start session
session_start();

// Database connection
require_once 'db_connection.php';

// Get form input
$construction_id = $_POST['construction-id'];

// Validate input
if (empty($construction_id)) {
    echo "<script>alert('Construction ID cannot be empty.'); window.history.back();</script>";
    exit();
}

// Fetch construction details
$sql_fetch = "SELECT * FROM constructions WHERE construction_id = ?";
$stmt = $conn->prepare($sql_fetch);
$stmt->bind_param("s", $construction_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $construction = $result->fetch_assoc();

    // Insert into site_manager_projects table
    $sitemanager_id = $_SESSION['userid']; // Site Manager ID from session
    $manager_id = $construction['user_id']; // Assuming the manager ID is the `user_id` of the construction
    $project_name = $construction['construction_name'];
    $status = 'In Progress'; // Or set as needed
    $deadline = $construction['expected_completion_date'];

    $sql_insert = "INSERT INTO site_manager_projects (
        sitemanager_id, manager_id, construction_id, project_name, status, deadline
    ) VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt_insert = $conn->prepare($sql_insert);

    if ($stmt_insert === false) {
        echo "Error preparing the insert query: " . $conn->error;
        exit();
    }

    $stmt_insert->bind_param(
        "ssssss",
        $sitemanager_id,
        $manager_id,
        $construction['construction_id'],
        $project_name,
        $status,
        $deadline
    );

    if ($stmt_insert->execute()) {
        // Redirect to site-manager.php
        header("Location: Site_manager.php");
        exit();
    } else {
        echo "<script>alert('Error adding construction to Site Manager Projects.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('No construction found with the given ID.'); window.history.back();</script>";
}

// Close connections
$stmt->close();
$conn->close();
?>
