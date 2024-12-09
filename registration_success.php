<?php
session_start();

// Check if user ID is set
if (!isset($_SESSION['userid'])) {
    echo "<script>alert('No user ID found. Please register again!');</script>";
    header("Location: registration_form.php");
    exit;
}

// Retrieve the user ID from the session
$userid = $_SESSION['userid'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 50px;
        }
        .container {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 8px;
            display: inline-block;
        }
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registration Successful!</h1>
        <p>Your unique User ID is: <strong><?php echo $userid; ?></strong></p>
        <a href="login.php" class="button">Go to Login Page</a>
    </div>
</body>
</html>
