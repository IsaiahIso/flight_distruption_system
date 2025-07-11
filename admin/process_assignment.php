<?php
require '../auth/config.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $flight_id = $_POST['flight_id'];
    $crew_id = $_POST['crew_id'];

    // Get the crew member's username and name
    $crewQuery = "SELECT staff_username, full_name FROM crew WHERE id = '$crew_id'";
    $crewResult = mysqli_query($conn, $crewQuery);
    $crewRow = mysqli_fetch_assoc($crewResult);
    $staff_username = $crewRow['staff_username'];
    $crew_name = $crewRow['full_name'];

    // Get the flight number
    $flightQuery = "SELECT flight_number FROM flights WHERE flight_id = '$flight_id'";
    $flightResult = mysqli_query($conn, $flightQuery);
    $flightRow = mysqli_fetch_assoc($flightResult);
    $flight_number = $flightRow['flight_number'];

    if ($staff_username && $flight_number) {
        // Assign the crew member and update their status
        $assignQuery = "INSERT INTO resource_allocations (flight_id, resource_type, resource_id, staff_username)
                        VALUES ('$flight_id', 'Crew', '$crew_id', '$staff_username')";
        $updateCrewQuery = "UPDATE crew SET status = 'Assigned', assigned_flight_id = '$flight_id' WHERE id = '$crew_id'";

        if (mysqli_query($conn, $assignQuery) && mysqli_query($conn, $updateCrewQuery)) {
            echo "✅ Crew member <strong>$crew_name</strong> assigned successfully to Flight <strong>$flight_number</strong>!";
        } else {
            echo "❌ Error: " . mysqli_error($conn);
        }
    } else {
        echo "❌ Invalid crew or flight selection.";
    }
}
?>
