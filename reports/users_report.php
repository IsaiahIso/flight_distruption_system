<?php
require '../auth/config.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch registered users
$users_query = "SELECT id, first_name, last_name, user_account, email FROM users";
$users_result = mysqli_query($conn, $users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users Report</title>
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
    <h1>Users Report</h1>
    <button class="print-btn" onclick="printReport()">Print Report</button>
    <table>
        <tr>
            <th>User ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Account Type</th>
            <th>Registered email</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($users_result)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['first_name']; ?></td>
                <td><?php echo $row['last_name']; ?></td>
                <td><?php echo $row['user_account']; ?></td>
                <td><?php echo $row['email']; ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
