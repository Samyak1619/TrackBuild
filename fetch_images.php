<?php
include 'db_connection.php';

$response = ["success" => false, "images" => []];

$result = $conn->query("SELECT image_path FROM project_images");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $response["images"][] = $row["image_path"];
    }
    $response["success"] = true;
} else {
    $response["message"] = "No images found.";
}

echo json_encode($response);

$conn->close();
?>
