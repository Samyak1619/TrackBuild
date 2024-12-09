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
        if ($user['role'] === 'admin') {
            // Fetch admin-specific details if needed
            $admin_name = $user['name'];
            $admin_email = $user['email'];
            $admin_role = $user['role'];
            $admin_id = $user['userid'];
        } else {
            // Redirect non-admin users to the login page or a forbidden access page
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
    echo "Error: Could not prepare query.";
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
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
            background-image: url(construction_img.webp); /* Use uploaded background image */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #2c3e50;
        }

        /* Full-page layout */
        .container {
            display: flex;
            height: 100vh;
            background-color: rgba(255, 255, 255, 0.8); /* White overlay to enhance readability */
        }

        /* Left Sidebar */
        .sidebar {
            width: 250px;
            background-color: #2c3e50; /* Dark color for sidebar */
            color: #fff;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.2); /* Shadow for sidebar depth */
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
            background-color: #d35400; /* Hover effect */
        }

        /* Welcome Message */
        .welcome {
            font-size: 18px;
            margin-bottom: 30px;
            color: #e67e22;
        }

        /* Right Column (Main Content) */
        .main-content {
            flex: 1;
            padding: 40px;
            overflow-y: scroll;
        }

        .welcome-message {
            font-size: 26px;
            font-weight: 700;
            color: #34495e;
            margin-bottom: 30px;
        }

        /* Project Section */
        .projects-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .current-project {
            width: 70%;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .current-project:hover {
            transform: scale(1.05); /* Hover effect for current project */
        }

        .current-project h2 {
            font-size: 20px;
            color: #2c3e50;
            border-bottom: 2px solid #e67e22;
            margin-bottom: 15px;
        }

        .project-list {
            width: 25%;
            background-color: #f7f7f7;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .project-list h2 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .project-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .project-item:hover {
            transform: scale(1.05); /* Hover effect for project list */
        }

        .project-item img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        .project-item p {
            color: #34495e;
            font-weight: 500;
        }

        /* Reports Section */
        .reports-section {
            margin-top: 30px;
        }

        .reports-section h2 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #34495e;
        }

        .report-item {
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .report-item:hover {
            transform: translateY(-5px);
        }

        .report-item h3 {
            font-size: 18px;
            color: #e67e22;
            margin-bottom: 10px;
        }

        /* Responsive Design */
        @media(max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .projects-section {
                flex-direction: column;
            }

            .current-project,
            .project-list {
                width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <div class="sidebar">
        <div class="welcome">
            <h1>Dashboard</h1>
            <p>Welcome, <strong><?php echo $admin_name;?></strong> 👷</p>
        </div>
        <div>
            <a href="add-constructionadmin.php">Add Construction</a> 
            <a href="daily-reports.php">Daily Reports 📊</a>
            <a href="view-projects.php">My Projects 🏗️</a>
            <a href="logout.php">Logout 🚪</a>
        </div>
    </div>

    <div class="main-content">
        <div class="welcome-message">Hello, <strong><?php echo $admin_name;?></strong>! Here's your project overview for today:</div>

        <div class="projects-section">
            <!-- Current Project Info -->
            <a href="admindetailspage.html" style="text-decoration: none; color: inherit;">
                <div class="current-project">
                    <h2>Current Project 🏗️</h2>
                    <p><strong>Project Name:</strong> Downtown Skyscraper Development</p>
                    <p><strong>Status:</strong> 45% Completed</p>
                    <p><strong>Deadline:</strong> 12th December 2024</p>
                </div>
            </a>

            <!-- My Projects List -->
            <div class="project-list">
                <h2>My Projects</h2>
                <a href="admindetailspage.html" style="text-decoration: none; color: inherit;">
                    <div class="project-item">
                        <img src="images/construction.png" alt="Project 1 Icon">
                        <p>Downtown Skyscraper Development</p>
                    </div>
                </a>
                <a href="admindetailspage.html" style="text-decoration: none; color: inherit;">
                    <div class="project-item">
                        <img src="images/construction.png" alt="Project 2 Icon">
                        <p>City Mall Construction</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Reports Section -->
        <div class="reports-section">
            <h2>Reports of Selected Projects</h2>
            <div class="report-item">
                <h3>Downtown Skyscraper Development - Weekly Report</h3>
                <p>Progress: 45%</p>
                <p>Key updates: Concrete structure completed, interior work started.</p>
            </div>
            <div class="report-item">
                <h3>City Mall Construction - Monthly Report</h3>
                <p>Progress: 20%</p>
                <p>Key updates: Foundation work ongoing.</p>
            </div>
        </div>
    </div>
</div>

</body>

</html>
