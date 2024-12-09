<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$user_name = $_SESSION['name']; // Retrieve the user's name from the session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
    <p>This is your profile page.</p>

    <!-- Add more content as needed for the user profile -->

    <a href="logout.php">Logout</a>
</body>
</html>
