<?php
require '../auth/config.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch flight disruptions
$disruptions_query = "SELECT flight_id, flight_number, status, departure_time, actual_departure_time 
                      FROM flights WHERE status IN ('Delayed', 'Cancelled')";
$disruptions_result = mysqli_query($conn, $disruptions_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flight Disruptions Report</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .print-btn { padding: 10px 20px; background: #007BFF; color: white; border: none; cursor: pointer; margin-bottom: 20px; }
    </style>
    <script> function printReport() { window.print(); } </script>
</head>
<body>
    <h1>Flight Disruptions Report</h1>
    <button class="print-btn" onclick="printReport()">Print Report</button>
    <table>
        <tr>
            <th>Flight ID</th>
            <th>Flight Number</th>
            <th>Status</th>
            <th>Scheduled Departure</th>
            <th>Actual Departure</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($disruptions_result)) { ?>
            <tr>
                <td><?php echo $row['flight_id']; ?></td>
                <td><?php echo $row['flight_number']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><?php echo $row['departure_time']; ?></td>
                <td><?php echo $row['actual_departure_time'] ?? 'N/A'; ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
