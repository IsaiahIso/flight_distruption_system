<?php
// Include database connection
require '../auth/config.php';

// Start the session
session_start();

// Ensure the user is logged in as a passenger
if (!isset($_SESSION['user_id']) || $_SESSION['user_account'] !== 'passengers') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch delayed/canceled flights
$query = "SELECT * FROM flights WHERE status IN ('Delayed', 'Cancelled') ORDER BY departure_time ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Status</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="status-container">
        <h1>Delayed/Cancelled Flights</h1>

        <table>
            <thead>
                <tr>
                    <th>Flight Number</th>
                    <th>Departure Time</th>
                    <th>Arrival Time</th>
                    <th>Departure Location</th>
                    <th>Arrival Location</th>
                    <th>Status</th>
                    <th>Airline</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['flight_number']); ?></td>
                        <td><?php echo date("Y-m-d H:i", strtotime($row['departure_time'])); ?></td>
                        <td><?php echo date("Y-m-d H:i", strtotime($row['arrival_time'])); ?></td>
                        <td><?php echo htmlspecialchars($row['departure_location']); ?></td>
                        <td><?php echo htmlspecialchars($row['arrival_location']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['airline']); ?></td>
                        <td>
                            <?php if ($row['status'] == 'Delayed' || $row['status'] == 'Cancelled') { ?>
                                <a href="compensation-request.php?flight_id=<?php echo $row['flight_id']; ?>" class="action-btn">Proceed to Compensation</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="passengers.php">Back to Dashboard</a>

    </div>
</body>
</html>
