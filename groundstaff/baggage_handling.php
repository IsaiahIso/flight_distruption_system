<?php
require '../auth/config.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['user_account'] !== 'ground-staff') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle baggage status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flight_id'], $_POST['baggage_status'])) {
    $flight_id = $_POST['flight_id'];
    $baggage_status = $_POST['baggage_status'];

    $update = "UPDATE flights SET baggage_status = '$baggage_status' WHERE flight_id = $flight_id";
    mysqli_query($conn, $update);
}

// Fetch flights for display
$query = "SELECT flight_id, flight_number, departure_time, baggage_status FROM flights ORDER BY departure_time ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Baggage Handling</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <h1>Baggage Handling</h1>
    <table>
        <tr>
            <th>Flight Number</th>
            <th>Departure Time</th>
            <th>Baggage Status</th>
            <th>Update</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['flight_number']; ?></td>
            <td><?php echo $row['departure_time']; ?></td>
            <td><?php echo $row['baggage_status'] ?? 'Not Set'; ?></td>
            <td>
                <form method="post">
                    <input type="hidden" name="flight_id" value="<?php echo $row['flight_id']; ?>">
                    <select name="baggage_status">
                        <option value="Loaded" <?php if ($row['baggage_status'] === 'Loaded') echo 'selected'; ?>>Loaded</option>
                        <option value="Not Loaded" <?php if ($row['baggage_status'] === 'Not Loaded') echo 'selected'; ?>>Not Loaded</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
