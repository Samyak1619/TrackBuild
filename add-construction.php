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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the construction ID from the form
    $construction_id = $_POST['construction-id'];
    $site_manager_id = $_SESSION['userid']; // Current logged-in user's ID

    // Query to fetch data from the constructions table
    $sql = "SELECT * FROM constructions WHERE construction_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $construction_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch construction details
            $construction = $result->fetch_assoc();

            // Insert data into the site_manager_projects table
            $insert_sql = "INSERT INTO site_manager_projects (sitemanager_id, manager_id, construction_id, project_name, status, deadline) VALUES (?, ?, ?, ?, ?, ?)";
            if ($insert_stmt = $conn->prepare($insert_sql)) {
                $insert_stmt->bind_param(
                    "ssssss",
                    $site_manager_id,
                    $construction['user_id'], // Assuming user_id represents the manager in the constructions table
                    $construction['construction_id'],
                    $construction['project_name'],
                    $construction['status'],
                    $construction['deadline']
                );

                if ($insert_stmt->execute()) {
                    echo "<script>alert('Project added successfully!');</script>";
                    header("Location: site-manager.php");
                    exit();
                } else {
                    echo "<script>alert('Error while adding project.');</script>";
                }
            }
        } else {
            echo "<script>alert('Construction ID not found.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Construction</title>
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
            <h1>Dashboard</h1>
            <p>Welcome, <strong>user</strong> 👷</p>
        </div>
        <div>
            <a href="userDashboared.html">Back to Dashboard</a>
            <a href="capture-image.php">Capture Image 📸</a>
            <a href="view-projects.php">My Projects 🏗️</a>
            <a href="logout.php">Logout 🚪</a>
        </div>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h2>Add New Construction</h2>

        <!-- Form to Add Construction -->
        <div class="form-container">
            <form action="submit-construction.php" method="POST">
                <div class="form-group">
                    <label for="construction-id">Construction ID:</label>
                    <input type="text" id="construction-id" name="construction-id" required>
                </div>


                <div class="form-group">
                    <input type="submit" class="submit-btn" value="Add Construction">
                </div>
            </form>
        </div>
    </div>
</div>

</body>

</html>
