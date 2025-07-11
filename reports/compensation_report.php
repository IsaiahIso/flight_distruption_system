<?php
require '../auth/config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$compensation_query = "SELECT id, first_name, last_name, email, flight_id, compensation_amount, status, request_date FROM compensation_requests";
$compensation_result = mysqli_query($conn, $compensation_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Compensations Report</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .print-btn { padding: 10px 20px; background: #007BFF; color: white; border: none; cursor: pointer; }
    </style>
    <script> function printReport() { window.print(); } </script>
</head>
<body>
    <h1>Compensations Report</h1>
    <button class="print-btn" onclick="printReport()">Print Report</button>
    <table>
        <tr>
            <th>ID</th>
            <th>Passenger Name</th>
            <th>Email</th>
            <th>Flight ID</th>
            <th>Amount (KES)</th>
            <th>Status</th>
            <th>Request Date</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($compensation_result)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['flight_id']; ?></td>
                <td><?php echo number_format($row['compensation_amount'], 2); ?></td>
                <td><?php echo ucfirst($row['status']); ?></td>
                <td><?php echo $row['request_date']; ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
