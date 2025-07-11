<?php
// Include config.php for database connection
require '../auth/config.php';

// Start the session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if the flight ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_flights.php");
    exit();
}

$flight_id = $_GET['id'];

// Delete the flight from the database
$query = "DELETE FROM flights WHERE flight_id = '$flight_id'";
if (mysqli_query($conn, $query)) {
    header("Location: manage_flights.php?success=Flight deleted successfully");
    exit();
} else {
    header("Location: manage_flights.php?error=Error deleting flight: " . mysqli_error($conn));
    exit();
}
?>
