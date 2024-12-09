<?php
// Include the database connection file
require_once 'db_connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $userid = $_POST['userid'];
    $password = $_POST['password'];

    // Prepare the SQL query to fetch user data
    $sql = "SELECT * FROM user_registration WHERE userid = ?";
    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters and execute the query
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the user exists
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Start the session and store user info
                session_start();
                $_SESSION['userid'] = $user['userid'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                // Role-based redirection
                switch ($user['role']) {
                    case 'user':
                        header("Location: profile.php");
                        break;
                    case 'admin':
                        header("Location: admin_dashboard.php");
                        break;
                    case 'siteManager':
                        header("Location: site_manager.php");
                        break;
                    default:
                        echo "<script>alert('Invalid role assigned. Please contact support.');</script>";
                        session_destroy();
                        exit();
                }
                exit();
            } else {
                echo "<script>alert('Invalid username or password.');</script>";
            }
        } else {
            echo "<script>alert('User not found.');</script>";
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "<script>alert('Error: Could not execute query.');</script>";
    }

    // Close the database connection
    $conn->close();
}

// Function to send welcome email with user ID
function sendWelcomeEmail($userEmail, $userId) {
    $subject = "Welcome to TrackBuild!";
    $message = "Hello,\n\nWelcome to TrackBuild!\n\nYour unique User ID is: $userId\n\nThank you for joining us!\n\nBest Regards,\nTrackBuild Team";

    // Additional headers
    $headers = "From: no-reply@trackbuild.com\r\n";
    $headers .= "Reply-To: support@trackbuild.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send the email
    if (mail($userEmail, $subject, $message, $headers)) {
        echo "A welcome email has been sent to your email address.";
    } else {
        echo "Failed to send the welcome email.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to Your Account</title>
    <style>
        /* Body and Background */
        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url('images/construction_img.webp'); /* Set your background image */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            position: relative; /* Added for loader */
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

        /* Login Form Container */
        .login-container {
            display: none; /* Initially hidden */
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 2px solid #ddd;
            margin: 20px;
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

       /* Labels for Inputs and Select */
label, label[for="role"] {
    font-weight: bold;
    font-family: Arial, sans-serif;
    font-size: 16px;
    color: #333;
    display: block;
    margin-bottom: 8px;
    margin-top: 15px; /* Keeps spacing uniform */
}

/* Form Fields: Input */
input {
    width: 100%;
    padding: 12px 40px 12px 12px; /* Extra padding for icon space */
    margin-top: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    transition: border 0.3s ease, box-shadow 0.3s ease;
}

/* Input Field Focus Effect */
input:focus {
    border-color: #e67e22;
    box-shadow: 0 0 6px rgba(230, 126, 34, 0.5);
    background-color: #fdfdfd;
}

/* Style for the Select Dropdown */
select#role {
    width: 100%;
    max-width: 400px;
    padding: 12px 40px 12px 12px; /* Matches input padding for consistency */
    margin-top: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    font-family: Arial, sans-serif;
    color: #555;
    background-color: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 5px;
    outline: none;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

/* Hover and Focus Effects for Select */
select#role:hover {
    border-color: #888;
}

select#role:focus {
    border-color: #e67e22;
    box-shadow: 0 0 6px rgba(230, 126, 34, 0.5);
    background-color: #fdfdfd;
}

/* Disabled and Placeholder Option */
select#role option[disabled] {
    color: #aaa;
}


        /* Form Fields */
        input {
            width: 100%;
            padding: 12px 40px 12px 12px; /* Extra padding for icon space */
            margin-top: 8px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border 0.3s ease, box-shadow 0.3s ease;
        }

        /* Input Field Focus Effect */
        input:focus {
            border-color: #e67e22;
            box-shadow: 0 0 6px rgba(230, 126, 34, 0.5);
            background-color: #fdfdfd;
        }

        /* Login Button */
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

        /* Link Styling */
        .link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #3498db;
        }

        .link a {
            text-decoration: none;
            font-weight: bold;
            color: #3498db;
            transition: color 0.3s ease;
        }

        .link a:hover {
            color: #2980b9;
        }

        /* Or Continue as Guest */
        .guest-option {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #7f8c8d;
        }

        .guest-option a {
            text-decoration: none;
            font-weight: bold;
            color: #7f8c8d;
        }

        .guest-option a:hover {
            color: #95a5a6;
        }

        /* Responsive Adjustments for Small Screens */
        @media (max-width: 768px) {
            .login-container {
                width: 95%;
                padding: 20px;
            }

            h2 {
                font-size: 22px;
            }

            input, button {
                font-size: 14px;
            }

            button {
                padding: 10px;
            }
        }
    </style>

</head>
<body>

<!-- Loader Section -->
<div id="loader">
    <!-- Construction Animation -->
    <svg id="construction-animation" viewBox="0 0 200 300">
        <!-- Foundation -->
        <rect x="20" y="250" width="160" height="30" fill="#34495e" class="layer foundation" />
        <!-- Walls -->
        <rect x="40" y="170" width="120" height="80" fill="#3498db" class="layer walls" />
        <!-- Windows -->
        <rect x="50" y="180" width="20" height="20" fill="#ecf0f1" class="layer windows" />
        <rect x="130" y="180" width="20" height="20" fill="#ecf0f1" class="layer windows" />
        <rect x="50" y="210" width="20" height="20" fill="#ecf0f1" class="layer windows" />
        <rect x="130" y="210" width="20" height="20" fill="#ecf0f1" class="layer windows" />
        <!-- Roof -->
        <polygon points="20,170 180,170 100,130" fill="#e74c3c" class="layer roof" />
    </svg>
</div>

<div class="login-container">
    <h2>Login to Your Account</h2>

    <form id="loginForm" action="login.php" method="POST">
        <label for="userid">Username</label>
        <input type="text" id="userid" name="userid" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label for="role">Select Role</label>
        <select id="role" name="role" required>
        <option value="" disabled selected>Select Role</option>
        <option value="user">User</option>
        <option value="admin">Admin</option>
        <option value="siteManager">Site Manager</option>
        </select> <br>
        
        <button type="submit">Login</button>

        <div class="link">
            Don't have an account? <a href="registration.php">Register here</a>
        </div>

        <div class="guest-option">
            Or <a href="guest.php">Continue as Guest</a>
        </div>
    </form>
</div>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
    // Simulate loader timeout before showing the form
    setTimeout(() => {
        document.getElementById('loader').style.display = 'none';
        document.querySelector('.login-container').style.display = 'block';
    }, 6000); // Show loader for 6 seconds
</script>

</body>
</html>