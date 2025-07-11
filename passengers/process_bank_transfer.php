<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? null;

    if (!$booking_id) {
        die("Invalid booking ID.");
    }

    if (!isset($_FILES['receipt']) || $_FILES['receipt']['error'] !== UPLOAD_ERR_OK) {
        die("Error uploading file.");
    }

    $targetDir = "../uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($_FILES["receipt"]["name"]);
    $targetFilePath = $targetDir . uniqid() . "_" . $fileName;

    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ["pdf", "jpg", "jpeg", "png"];

    if (!in_array($fileType, $allowedTypes)) {
        die("Only PDF, JPG, JPEG, and PNG files are allowed.");
    }

    if (move_uploaded_file($_FILES["receipt"]["tmp_name"], $targetFilePath)) {
        echo "Receipt uploaded successfully for booking ID: " . htmlspecialchars($booking_id);
        // You can store $targetFilePath in your database for future verification
    } else {
        echo "Failed to upload file.";
    }
} else {
    echo "Invalid request method.";
}
?>
