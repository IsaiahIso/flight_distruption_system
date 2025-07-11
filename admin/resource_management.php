<?php
require '../auth/config.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch available flights that are delayed
$flightsQuery = "SELECT * FROM flights WHERE status = 'Delayed'";
$flightsResult = mysqli_query($conn, $flightsQuery);

// Fetch available crew members
$crewQuery = "SELECT * FROM crew WHERE status = 'Available'";
$crewResult = mysqli_query($conn, $crewQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resource Management</title>
    <link rel="stylesheet" href="../styles/styles.css">

    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
    </style>
</head>
<body>
    <div class="resource_container">
        <h1>Resource Allocation</h1>
        <p>Assign available crew members to delayed flights.</p>

        <form action="process_assignment.php" method="POST" class="resource_form">
            <label for="flight_id">✈ Select Delayed Flight:</label>
            <select name="flight_id" required>
                <?php while ($flight = mysqli_fetch_assoc($flightsResult)) { ?>
                    <option value="<?php echo $flight['flight_id']; ?>">
                        <?php echo $flight['flight_number']; ?>
                    </option>
                <?php } ?>
            </select>

            <label for="crew_id">👨‍✈️ Assign Crew Member:</label>
            <select name="crew_id" required>
                <?php while ($crew = mysqli_fetch_assoc($crewResult)) { ?>
                    <option value="<?php echo $crew['id']; ?>">
                        <?php echo $crew['full_name'] . " (" . $crew['role'] . ")"; ?>
                    </option>
                <?php } ?>
            </select>

            <button type="submit">Assign Resource</button>
        </form>
    </div>
</body>
</html>
