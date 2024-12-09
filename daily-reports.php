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
            $admin_name = $user['name'];
            $admin_email = $user['email'];
        } else {
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
    echo "Error: Could not prepare user details query. Error: " . $conn->error;
}

// Fetch the construction details if the form is submitted
$construction_info = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['construction-id'])) {
    $construction_id = $_POST['construction-id'];
    
    // Query to fetch construction details based on the logged-in user and construction_id
    $construction_query = "
        SELECT * FROM site_manager_projects 
        WHERE construction_id = ? 
        AND manager_id = ?";
    if ($stmt = $conn->prepare($construction_query)) {
        $stmt->bind_param("ss", $construction_id, $user_id); // Pass the user_id and construction_id to the query
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the construction details
            $construction_info = $result->fetch_assoc();
        } else {
            echo "<script>alert('Construction ID not found or you do not have permission to view it.');</script>";
        }
        $stmt->close();
    } else {
        echo "Error: Could not prepare construction query. Error: " . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Reports</title>
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

        .form-container {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input[type="text"],
        .form-group input[type="submit"] {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-group input[type="submit"] {
            background-color:  #e67e22;
            color: #fff;
            cursor: pointer;
            border: none;
        }

        .form-group input[type="submit"]:hover {
            background-color:    #d35400;
        }

        .construction-details {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }yh

        .construction-details h3 {
            margin-top: 0;
        }

        .construction-details button {
            background-color: #e67e22;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .construction-details button:hover {
            background-color:  #d35400;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            text-align: center;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .modal-content h3 {
            margin-top: 0;
        }

        .modal-content .close {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }
    </</style>
</head>

<body>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <h1>Daily Reports</h1>
            <p>Welcome, <strong><?php echo htmlspecialchars($admin_name); ?></strong> 👷</p>
        </div>
        <div>
            <a href="admin_dashboard.php">Back to Dashboard</a>
        </div>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h2>Search the Construction</h2>

        <!-- Form to Search Construction -->
        <div class="form-container">
            <form action="" method="POST">
                <div class="form-group">
                    <label for="construction-id">Construction ID:</label>
                    <input type="text" id="construction-id" name="construction-id" required>
                </div>

                <div class="form-group">
                    <input type="submit" value="Search Construction ID">
                </div>
            </form>
        </div>

        <!-- Display the construction details if found -->
        <?php if (!empty($construction_info)): ?>
        <div class="construction-details">
            <h3>Construction Details:</h3>
            <p><strong>Project Name:</strong> <?php echo htmlspecialchars($construction_info['project_name']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($construction_info['status']); ?></p>
            <p><strong>Deadline:</strong> <?php echo htmlspecialchars($construction_info['deadline']); ?></p>
            <p><strong>Manager ID:</strong> <?php echo htmlspecialchars($construction_info['manager_id']); ?></p>
            <p><strong>Created At:</strong> <?php echo htmlspecialchars($construction_info['created_at']); ?></p>

          <!-- Button to open the report page -->
<a href="give_report.php?construction_id=<?php echo htmlspecialchars($construction_info['construction_id']); ?>" id="giveReportBtn">
    <button>Give Report</button>
</a>

        </div>
        <?php endif; ?>
    </div>
</div>

<!-- The Modal -->
<div id="constructionModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h3>Report for Construction ID: <?php echo htmlspecialchars($construction_info['construction_id'] ?? ''); ?></h3>
        <textarea placeholder="Enter your report here..." rows="4" style="width: 100%;"></textarea><br>
        <button>Submit Report</button>
    </div>
</div>

<script>
    // Modal Logic
    const modal = document.getElementById("constructionModal");
    const openModalBtn = document.getElementById("openModalBtn");
    const closeModalBtn = document.getElementById("closeModal");

    openModalBtn.onclick = function () {
        modal.style.display = "flex";
    }

    closeModalBtn.onclick = function () {
        modal.style.display = "none";
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
