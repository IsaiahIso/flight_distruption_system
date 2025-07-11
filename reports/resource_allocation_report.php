<?php
require '../auth/config.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch resource allocation records
$query = "SELECT r.id, f.flight_number, r.resource_type, r.resource_id, r.staff_username, r.timestamp 
          FROM resource_allocations r 
          JOIN flights f ON r.flight_id = f.flight_id
          ORDER BY r.timestamp DESC";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resource Allocation Report</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .print-btn { padding: 10px 20px; background: #007BFF; color: white; border: none; cursor: pointer; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="resource_report_container">
        <h1>Resource Allocation Report</h1>
        <button class="print-btn" onclick="window.print()">Print Report</button>

        <table>
            <tr>
                <th>Flight Number</th>
                <th>Resource Type</th>
                <th>Resource ID</th>
                <th>Assigned By</th>
                <th>Timestamp</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['flight_number']); ?></td>
                <td><?php echo htmlspecialchars($row['resource_type']); ?></td>
                <td><?php echo htmlspecialchars($row['resource_id']); ?></td>
                <td><?php echo htmlspecialchars($row['staff_username']); ?></td>
                <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>
