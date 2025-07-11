<?php
require '../auth/config.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['user_account'] !== 'ground-staff') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle clearance action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flight_id'])) {
    $flightId = $_POST['flight_id'];
    $update = "UPDATE flights SET security_status = 'Cleared' WHERE flight_id = $flightId";
    mysqli_query($conn, $update);
}

// Fetch flights needing security clearance
$query = "SELECT * FROM flights WHERE security_status = 'Pending'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Security Checks</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>

<div class="groundstaff-container">
    <h2>Security Checks</h2>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Flight ID</th>
                    <th>Flight Number</th>
                    <th>Destination</th>
                    <th>Scheduled Departure</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($flight = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $flight['flight_id'] ?></td>
                        <td><?= $flight['flight_number'] ?></td>
                        <td><?= $flight['arrival_location'] ?></td>
                        <td><?= $flight['departure_time'] ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="flight_id" value="<?= $flight['flight_id'] ?>">
                                <button type="submit" class="action-btn">Mark Cleared</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>All flights have cleared security.</p>
    <?php endif; ?>
</div>

</body>
</html>
