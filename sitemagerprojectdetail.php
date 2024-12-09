<?php
// Include the database connection
include 'db_connection.php'; // Ensure the correct path to your db connection file

// Start the session to get user ID
session_start();

// Fetch the logged-in user's ID from the session
$user_id = $_SESSION['userid'];

// Query to fetch project details for the current site manager
$sql = "SELECT * FROM site_manager_projects WHERE sitemanager_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id); // Assuming sitemanager_id is a string
$stmt->execute();
$result = $stmt->get_result();
$projectDetails = $result->fetch_all(MYSQLI_ASSOC); // Fetch all records
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style> 
        /* Add your custom styles here */
        

{
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Body and Background */
        body {
            background-image: url('images/construction_img.webp');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Roboto', sans-serif;
            color: #34495e;
        }

        /* Full-page layout */
        .project-details-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.85);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: relative; /* Add relative positioning for absolute elements */
        }

        /* Section Titles */
        h1, h2 {
            font-weight: 700;
            color: #2c3e50;
            border-bottom: 2px solid #e67e22;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        /* Buttons */
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            margin-right: 10px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            background-color: #e67e22;
            color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        button:hover {
            background-color: #d95f0e;
        }

        /* Go to Dashboard Button */
        .dashboard-button {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000; /* Ensures the button is on top of other elements */
        }

        /* Chatbox */
        .chatbox {
            margin-bottom: 40px;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .chatbox-header {
            background-color: #4a6572;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: 700;
        }

        .chatbox-messages {
            height: 250px;
            overflow-y: auto;
            padding: 15px;
        }

        .chatbox-messages p {
            background-color: #e7e9eb;
            border-radius: 20px;
            padding: 10px 15px;
            margin-bottom: 10px;
            display: inline-block;
        }

        .chatbox-input {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ddd;
        }

        .chatbox-input input {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 20px;
            margin-right: 10px;
        }

        .chatbox-input button {
            background-color: #e67e22;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
        }

        /* Images Section */
        .images-section {
            margin-bottom: 40px;
        }

        .images-section h2 {
            margin-bottom: 20px;
        }

        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }

        .images-grid img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
        }

        .images-grid img:hover {
            transform: scale(1.05);
        }

        /* Progress Chart */
        .progress-chart {
            margin-bottom: 40px;
        }

        /* Full-Width Design */
        .full-width {
            width: 100%;
            margin-bottom: 30px;
        }

        .chart-section {
            margin-bottom: 40px;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Flexbox layout for chat and info */
        .flex-container {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            gap: 30px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .flex-container {
                flex-direction: column;
            }
        }

        .images-grid img {
            width: 150px;
            height: 150px;
            margin: 5px;
            border-radius: 5px;
            object-fit: cover;
        }

        .upload-section {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="project-details-container">
        <div class="dashboard-button">
            <button onclick="goToDashboard()">Go to Dashboard</button>
        </div>

        <h1>Project: TracBuild</h1>

        <!-- Loop through projects and display their details -->
        <?php if ($projectDetails): ?>
            <?php foreach ($projectDetails as $project): ?>
                <div class="project-info">
                    <h2>Project Information</h2>
                    <p><strong>Project Name:</strong> <?php echo htmlspecialchars($project['project_name']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($project['construction_id']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($project['status']); ?></p>
                    <p><strong>Deadline:</strong> <?php echo $project['deadline'] ? htmlspecialchars($project['deadline']) : 'Not set'; ?></p>
                    <p><strong>Created At:</strong> <?php echo htmlspecialchars($project['created_at']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No projects found for this site manager.</p>
        <?php endif; ?>

        <div class="flex-container">
            <div class="chatbox">
                <div class="chatbox-header">Chat with Admin</div>
                <div class="chatbox-messages" id="chatMessages"></div>
                <div class="chatbox-input">
                    <input type="text" id="chatInput" placeholder="Type your message..." />
                    <button onclick="sendMessage()">Send</button>
                </div>
            </div>

            <div class="chart-section">
                <h2>Progress Report</h2>
                <canvas id="progressChart" class="full-width"></canvas>
            </div>
        </div>

        <div class="images-section">
            <h2>Project Images</h2>
            <button onclick="viewTodayPhotos()">View Today's Photos</button>
            <button onclick="captureImage()">Capture Image</button>

           <!-- Updated Upload Form -->
<div class="upload-section">
    <form id="uploadForm" method="post" enctype="multipart/form-data" action="upload_image.php">
        <label for="uploadImage">Upload Image:</label>
        <input type="file" name="image" id="uploadImage" accept="image/*" required />
        
        <div class="form-group">
            <label for="construction-id">Construction ID:</label>
            <input type="text" id="construction-id" name="construction-id" required />
        </div>
        
        <button type="submit">Upload Image</button>
    </form>
</div>

            <div class="images-grid" id="imagesGrid">
                <!-- Images will be displayed here -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const imagesGrid = document.getElementById('imagesGrid');
        const chatMessages = document.getElementById('chatMessages');

        window.onload = function () {
            loadChatMessages();
            loadImages();
        };

        function goToDashboard() {
            window.location.href = "userDashboard.html"; // Change this URL to your actual dashboard page
        }

        function viewTodayPhotos() {
            alert("Displaying today's photos.");
        }

        function captureImage() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then((stream) => {
                    const video = document.createElement('video');
                    video.srcObject = stream;
                    video.play();

                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');

                    video.addEventListener('loadedmetadata', () => {
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        document.body.appendChild(video);

                        setTimeout(() => {
                            context.drawImage(video, 0, 0);
                            const imgData = canvas.toDataURL('image/png');

                            addImageToGrid(imgData);

                            stream.getTracks().forEach(track => track.stop());
                            video.remove();
                            canvas.remove();
                        }, 1000);
                    });
                })
                .catch(err => console.error("Error accessing the camera: ", err));
        }

        function sendMessage() {
            const messageInput = document.getElementById('chatInput');
            const messageText = messageInput.value;
            if (messageText) {
                const messageElement = document.createElement('p');
                messageElement.textContent = "User: " + messageText;
                chatMessages.appendChild(messageElement);
                messageInput.value = '';
            }
        }

        var ctx = document.getElementById('progressChart').getContext('2d');
        var progressChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Completed', 'Pending'],
                datasets: [{
                    label: 'Project Progress',
                    data: [60, 40], // Sample data
                    backgroundColor: ['#4caf50', '#ff9800'],
                }]
            },
            options: {
                responsive: true
            }
        });

        function loadChatMessages() {
            const staticMessages = [
                { user: 'Admin', text: 'Please upload today\'s progress photos.' },
                { user: 'User', text: 'Sure, I will upload them shortly.' },
            ];

            staticMessages.forEach(msg => {
                const messageElement = document.createElement('p');
                messageElement.textContent = `${msg.user}: ${msg.text}`;
                chatMessages.appendChild(messageElement);
            });
        }

        // Handle Form Submission
        const uploadForm = document.getElementById('uploadForm');
        uploadForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent page reload
            const formData = new FormData(uploadForm);

            fetch('upload_image.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        loadImages(); // Reload images grid
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error("Error:", error));
        });

        function loadImages() {
            fetch('fetch_images.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        imagesGrid.innerHTML = '';
                        data.images.forEach(imgPath => addImageToGrid(imgPath));
                    } else {
                        console.error(data.message);
                    }
                });
        }

        function addImageToGrid(imgPath) {
            const imgElement = document.createElement('img');
            imgElement.src = imgPath;
            imgElement.alt = "Uploaded Image";
            imagesGrid.appendChild(imgElement);
        }
    </script>
</body>
</html>
