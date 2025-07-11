<?php
 
require '../auth/config.php';
session_start();
if (!isset($_SESSION['username']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div id="admin-container-img">
    <div class="header">
        <h1>Admin Page</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
</div>

    <div class="admin-container">
        <h2>Admin Functionalities</h2>
        <ul class="list">
            <li>
                <a href="manage_users.php">Manage Users</a>
                <p>Add, edit, or remove users from the system.</p>
            </li>
            <li>
                <a href="manage_flights.php">Manage Flights</a>
                <p>Add, edit, or delete flight schedules and details.</p>
            </li>
            <li>
                <a href="manage_disruptions.php">Manage Disruptions</a>
                <p>Handle flight disruptions and monitor responses.</p>
            </li>
            <li>
                <a href="resource_management.php">Resource Management</a>
                <p>View and reassign crew, ground staff, and gates to handle disruptions.</p>
            </li>
            <li>
                 <a href="manage_crew.php">Manage Crew</a>
                <p>Add or manage crew members.</p>
            </li>
            <li>
                 <a href="distruption_data.php">Distruption data</a>
                <p>Add or manage crew members.</p>
            </li>
            <li>
                <a href="../reports/reports.php">Reports</a>
                <p>Handle Reports of all happenings.</p>
            </li>
            <li>
                <a href="../auth/logout.php">Logout</a>
                <p>End your current session.</p>
            </li>
        </ul>
    </div>
    </div>
</body>
</html>
