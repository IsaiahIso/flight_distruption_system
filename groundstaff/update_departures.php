<?php
require '../auth/config.php';
session_start();

// Ensure only ground staff can access
if (!isset($_SESSION['username']) || $_SESSION['user_account'] !== 'ground-staff') {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_departure'])) {
    $flight_id = $_POST['flight_id'];
    $actual_departure_time = $_POST['actual_departure_time'];

    // Fetch the scheduled departure time
    $query = "SELECT departure_time FROM flights WHERE flight_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $flight_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $flight = $result->fetch_assoc();
    $stmt->close(); // Close statement

    if ($flight) {
        $scheduled_departure = $flight['departure_time'];

        // Ensure actual departure is not before scheduled departure
        if ($actual_departure_time >= $scheduled_departure) {
            $updateQuery = "UPDATE flights SET actual_departure_time = ? WHERE flight_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("si", $actual_departure_time, $flight_id);
            
            if ($stmt->execute()) {
                $message = "<p class='success'>Actual departure time updated successfully.</p>";
            } else {
                $message = "<p class='error'>Error updating time: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            $message = "<p class='error'>Error: Actual departure time cannot be before the scheduled departure time.</p>";
        }
    } else {
        $message = "<p class='error'>Error: Flight not found.</p>";
    }
}

// Fetch delayed flights for updating
$flightsQuery = "SELECT flight_id, flight_number, departure_time FROM flights WHERE status = 'Delayed'";
$flightsResult = $conn->query($flightsQuery);
$conn->close(); // Close connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Actual Departure</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>

    <div class="update_departure_container">
        <h1>Update Actual Departure Time</h1>

        <?php if ($message) echo $message; ?>

        <form method="POST" class="update_departures_form">
            <label for="flight_id">Select Flight:</label>
            <select name="flight_id" id="flight_id" required>
                <?php while ($flight = $flightsResult->fetch_assoc()) { ?>
                    <option value="<?= htmlspecialchars($flight['flight_id']); ?>">
                        <?= htmlspecialchars($flight['flight_number']); ?> 
                        (Scheduled: <?= htmlspecialchars($flight['departure_time']); ?>)
                    </option>
                <?php } ?>
            </select>

            <label for="actual_departure_time">Actual Departure Time:</label>
            <input type="datetime-local" name="actual_departure_time" id="actual_departure_time" required>

            <button type="submit" name="update_departure" class="action-btn">Update Departure</button>
        </form>

        <a href="groundstaff.php" class="back-btn">Back to Dashboard</a>
    </div>

</body>
</html>
