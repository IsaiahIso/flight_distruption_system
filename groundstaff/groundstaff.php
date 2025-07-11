<?php
require '../auth/config.php';
session_start();

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['username']) || $_SESSION['user_account'] !== 'ground-staff') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch delayed flights for display
$flightsQuery = "SELECT * FROM flights WHERE status = 'Delayed'";
$flightsResult = mysqli_query($conn, $flightsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ground Staff Page</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>

    <div class="groundstaff-container">
        <h1>Ground Staff Page</h1>

        <!-- Update Actual Departures Section -->
        <div class="groundstaff-section">
            <h2>Update Actual Departures</h2>
            <p>Ensure that flight departure times are accurately recorded.</p>
            <a href="update_departures.php" class="action-btn">Update Departures</a>
        </div>

         <!-- Baggage Handling Section -->
        <div class="groundstaff-section">
         <h2>Baggage Handling</h2>
         <p>Oversee the proper loading, unloading, and tracking of baggage.</p>
        <a href="baggage_handling.php" class="action-btn">Manage Baggage</a>
         </div>

        <!-- Security Checks Section -->
        <div class="groundstaff-section">
             <h2>Security Checks</h2>
             <p>Ensure that all security protocols are followed before takeoff.</p>
         <a href="security_checks.php" class="action-btn">Perform Security Checks</a>
          </div>


        <!-- Gate Assignments Section -->
         
    </div>

</body>
</html>
