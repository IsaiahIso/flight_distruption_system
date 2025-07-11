<?php
require '../auth/config.php';
session_start();

// Ensure the user is logged in as a passenger
if (!isset($_SESSION['user_id']) || $_SESSION['user_account'] !== 'passengers') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>passengers Page</title>
    <link rel="stylesheet" href="../styles/styles.css"> <!-- Link to external CSS -->
</head>
<body>
    <div class="passengers_container">
        
        <!-- Left Side: Image -->
        <div class="passengers-image-container">
            <img src="../images/passengers.png" alt="Passenger Dashboard Image">
        </div>

        <!-- Right Side: Dashboard Content -->
        <div class="dashboard-content">
            <h1>Welcome to Your Passenger Page</h1>

            <!-- Bookings Section -->
            <div class="dashboard-section">
                <h3>Your Bookings</h3>
                <a href="bookings.php" class="action-btn">Book Flight</a>
            </div>

            <!-- Flight Status Section -->
            <div class="dashboard-section">
                <h4>Check Flight Status</h4>
                <a href="status.php" class="action-btn">View Delayed/Cancelled Flights</a>
            </div>
            <div class="dashboard-section">
            <a href="../auth/logout.php"><h4>Logout</h4></a>
            </div>
        </div>
    </div>
</body>
</html>
