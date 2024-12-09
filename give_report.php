<?php
session_start();
require_once 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Fetch the construction ID from the query string
if (!isset($_GET['construction_id']) || empty($_GET['construction_id'])) {
    echo "<script>alert('Invalid construction ID.'); window.location.href = 'daily_reports.php';</script>";
    exit();
}
$construction_id = $_GET['construction_id'];

// Fetch images related to the construction ID
$sql_images = "SELECT image_path FROM project_images WHERE construction_id = ?";
$stmt = $conn->prepare($sql_images);
$stmt->bind_param("s", $construction_id);
$stmt->execute();
$result = $stmt->get_result();
$images = [];
while ($row = $result->fetch_assoc()) {
    $images[] = $row['image_path'];
}
$stmt->close();

// Handle report submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stage = $_POST['stage'];
    $report = $_POST['report'];

    $insert_sql = "INSERT INTO project_reports (construction_id, stage, report, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("sss", $construction_id, $stage, $report);

    if ($stmt->execute()) {
        echo "<script>alert('Report submitted successfully!'); window.location.href = 'give_report.php?construction_id=$construction_id';</script>";
    } else {
        echo "<script>alert('Failed to submit the report. Please try again.');</script>";
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f4f4f9;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
        }
        .images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .images img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: 500;
            margin-bottom: 5px;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Report for Construction ID: <?php echo htmlspecialchars($construction_id); ?></h1>

        <!-- Display images -->
        <div class="images">
            <?php if (!empty($images)): ?>
                <?php foreach ($images as $image): ?>
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="Construction Image">
                <?php endforeach; ?>
            <?php else: ?>
                <p>No images available for this construction ID.</p>
            <?php endif; ?>
        </div>

        <!-- Report Submission Form -->
        <form action="" method="POST">
            <div class="form-group">
                <label for="stage">Stage:</label>
                <select name="stage" id="stage" required>
                    <option value="Stage 1">Stage 1</option>
                    <option value="Stage 2">Stage 2</option>
                    <option value="Stage 3">Stage 3</option>
                    <option value="Stage 4">Stage 4</option>
                    <option value="Stage 5">Stage 5</option>
                </select>
            </div>
            <div class="form-group">
                <label for="report">Report Details:</label>
                <textarea name="report" id="report" rows="4" required></textarea>
            </div>
            <button type="submit">Submit Report</button>
        </form>

        <!-- Display Submitted Reports -->
        <h2>Previous Reports</h2>
        <table>
            <thead>
                <tr>
                    <th>Stage</th>
                    <th>Report</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch submitted reports
                // $sql_reports = "SELECT stage, report, created_at FROM project_reports WHERE construction_id = ?";
                // $stmt = $conn->prepare($sql_reports);
                // $stmt->bind_param("s", $construction_id);
                // $stmt->execute();
                // $result = $stmt->get_result();

                // if ($result->num_rows > 0) {
                //     while ($row = $result->fetch_assoc()) {
                //         echo "<tr>
                //                 <td>" . htmlspecialchars($row['stage']) . "</td>
                //                 <td>" . htmlspecialchars($row['report']) . "</td>
                //                 <td>" . htmlspecialchars($row['created_at']) . "</td>
                //               </tr>";
                //     }
                // } else {
                //     echo "<tr><td colspan='3'>No reports available.</td></tr>";
                // }
                // $stmt->close();
                // ?>
            </tbody>
        </table>
    </div>
</body>
</html>
