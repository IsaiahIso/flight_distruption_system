<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="report_container">
        <h1>Reports Dashboard</h1>
        <ul class="report-links">
            <li><a href="bookings_report.php">Bookings Report</a></li>
            <li><a href="payments_report.php">Payments Report</a></li>
            <li><a href="compensation_report.php">Compensations Report</a></li>
            <li><a href="disruptions_report.php">Flight Disruptions Report</a></li>
            <li><a href="users_report.php">Users Report</a></li>
            <li><a href="resource_allocation_report.php">Resource Allocation Report</a></li>
            <li><a href="visual_reports.php">Visual Reports</a></li>
        </ul>
    </div>
</body>
</html>
