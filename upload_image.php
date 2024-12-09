<?php
// Include the database connection file
include 'db_connection.php';

// Handle the uploaded image and construction ID
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK && isset($_POST['construction-id'])) {
    // Define the upload directory
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Create directory if it doesn't exist
    }

    // Get the file details
    $imageName = basename($_FILES['image']['name']);
    $targetFilePath = $uploadDir . time() . "_" . $imageName;
    $constructionId = htmlspecialchars($_POST['construction-id']); // Get construction ID and sanitize input

    // Move the uploaded file to the designated directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
        // Save the file path and construction ID to the database
        $stmt = $conn->prepare("INSERT INTO project_images (image_path, construction_id) VALUES (?, ?)");
        $stmt->bind_param("ss", $targetFilePath, $constructionId);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Image and construction ID uploaded and saved successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Image upload succeeded, but saving to database failed."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Failed to upload image."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No image file uploaded or an error occurred, or construction ID missing."]);
}

// Close the database connection
$conn->close();
?>
