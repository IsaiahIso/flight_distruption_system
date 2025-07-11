<?php
require '../auth/config.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch payments
$payments_query = "SELECT id, passenger_id, flight_id, payment_amount, payment_status, booking_date FROM bookings WHERE payment_status = 'Paid'";
$payments_result = mysqli_query($conn, $payments_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payments Report</title>
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
    <h1>Payments Report</h1>
    <button class="print-btn" onclick="printReport()">Print Report</button>
    <table>
        <tr>
            <th>ID</th>
            <th>Passenger ID</th>
            <th>Flight ID</th>
            <th>Amount (KES)</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($payments_result)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['passenger_id']; ?></td>
                <td><?php echo $row['flight_id']; ?></td>
                <td><?php echo number_format($row['payment_amount'], 2); ?></td>
                <td><?php echo $row['payment_status']; ?></td>
                <td><?php echo $row['booking_date']; ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
