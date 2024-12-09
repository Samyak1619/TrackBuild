<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Project Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Basic Reset */
        * {
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
            position: relative;
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

        /* "Go to Dashboard" Button Styling */
        .go-to-dashboard-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #e67e22;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .go-to-dashboard-btn:hover {
            background-color: #d95f0e;
        }

        /* Chatbox */
        .chatbox {
            margin-bottom: 40px;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
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
    </style>
</head>
<body>

    <div class="project-details-container">
        <!-- "Go to Dashboard" Button at Top Right -->
        <button class="go-to-dashboard-btn" onclick="goToDashboard()">Go to Dashboard</button>

        <h1>Project: TracBuild (Admin)</h1>

        <!-- Project Info Section -->
        <div class="project-info">
            <h2>Project Information</h2>
            <p><strong>Location:</strong> Site A, Block 12</p>
            <p><strong>Start Date:</strong> Jan 15, 2024</p>
            <p><strong>Status:</strong> In Progress</p>
        </div>

        <!-- Flex Container for Chart and Chatbox -->
        <div class="flex-container">
            <!-- Chatbox -->
            <div class="chatbox">
                <div class="chatbox-header">Chat with User</div>
                <div class="chatbox-messages" id="chatMessages">
                    <p>Admin: Please upload today's progress photos.</p>
                    <p>User: Sure, I will upload them shortly.</p>
                </div>
                <div class="chatbox-input">
                    <input type="text" id="chatInput" placeholder="Type your message..." />
                    <button onclick="sendMessage()">Send</button>
                </div>
            </div>

            <!-- Progress Chart Section -->
            <div class="chart-section">
                <h2>Progress Report</h2>
                <canvas id="progressChart" class="full-width"></canvas>
            </div>
        </div>

        <!-- Images Section -->
        <div class="images-section">
            <h2>Project Images</h2>
            <button onclick="viewTodayPhotos()">View Today's Photos</button>
            <button onclick="selectDate()">Select Date</button>
            <div class="images-grid" id="imagesGrid"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const imagesGrid = document.getElementById('imagesGrid');
        const chatMessages = document.getElementById('chatMessages');

        // Load stored images and chat messages from local storage
        window.onload = function() {
            loadChatMessages();
            loadImages();
        };

        function viewTodayPhotos() {
            alert("Displaying today's photos.");
        }

        function selectDate() {
            const date = prompt("Please enter a date (YYYY-MM-DD):");
            if (date) {
                alert("Displaying images for date: " + date);
                // Implement fetching images from the database based on the selected date here
            }
        }

        function sendMessage() {
            const message = document.getElementById('chatInput').value;
            if (message) {
                const msgElement = document.createElement('p');
                msgElement.textContent = "Admin: " + message;
                chatMessages.appendChild(msgElement);
                document.getElementById('chatInput').value = '';

                // Save the message to local storage
                saveMessageToLocal("Admin: " + message);
            }
        }

        function saveMessageToLocal(message) {
            const messages = JSON.parse(localStorage.getItem('chatMessages')) || [];
            messages.push(message);
            localStorage.setItem('chatMessages', JSON.stringify(messages));
        }

        function loadChatMessages() {
            const messages = JSON.parse(localStorage.getItem('chatMessages')) || [];
            messages.forEach(msg => {
                const msgElement = document.createElement('p');
                msgElement.textContent = msg;
                chatMessages.appendChild(msgElement);
            });
        }

        function loadImages() {
            // Example images loading logic
            const exampleImages = ['image1.jpg', 'image2.jpg', 'image3.jpg'];
            exampleImages.forEach(imgSrc => {
                const imgElement = document.createElement('img');
                imgElement.src = imgSrc;
                imagesGrid.appendChild(imgElement);
            });
        }

        // Progress Chart
        const ctx = document.getElementById('progressChart').getContext('2d');
        const progressChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Pending'],
                datasets: [{
                    label: 'Progress',
                    data: [65, 25, 10], // Example data
                    backgroundColor: ['#2ecc71', '#f39c12', '#e74c3c']
                }]
            },
            options: {
                responsive: true
            }
        });

        // "Go to Dashboard" Button Action
        function goToDashboard() {
            window.location.href = 'admin_dashboard.html'; // Replace with your dashboard link
        }
    </script>
</body>
</html>
