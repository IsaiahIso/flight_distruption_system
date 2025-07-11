<?php
require '../auth/config.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch delayed flights
$flightsQuery = "SELECT flight_id, flight_number FROM flights WHERE status = 'Delayed'";
$flightsResult = mysqli_query($conn, $flightsQuery);

// Fetch available crew members
$crewQuery = "SELECT id, full_name, role FROM crew WHERE status = 'Available'";
$crewResult = mysqli_query($conn, $crewQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Crew to Flights</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>

    <h2>Assign Crew to Delayed Flights</h2>
    <form action="process_assignment.php" method="POST">
        <label for="flight_id">Select Flight:</label>
        <select name="flight_id" required>
            <option value="">-- Select Flight --</option>
            <?php while ($flight = mysqli_fetch_assoc($flightsResult)) { ?>
                <option value="<?= $flight['flight_id']; ?>"><?= $flight['flight_number']; ?></option>
            <?php } ?>
        </select>

        <label for="crew_id">Select Crew Member:</label>
        <select name="crew_id" required>
            <option value="">-- Select Crew --</option>
            <?php while ($crew = mysqli_fetch_assoc($crewResult)) { ?>
                <option value="<?= $crew['id']; ?>">
                    <?= $crew['full_name'] . " (" . $crew['role'] . ")"; ?>
                </option>
            <?php } ?>
        </select>

        <button type="submit">Assign Crew</button>
    </form>

</body>
</html>
