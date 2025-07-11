<?php
// Include config.php for database connection
require '../auth/config.php';
session_start();

// Check if the user is logged in and has finance role
if (!isset($_SESSION['user_id']) || $_SESSION['user_account'] !== 'finance Team') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch all bookings with payment details
$sql = "SELECT b.id AS booking_id, p.first_name, p.last_name, f.flight_number, 
               b.booking_date, b.payment_status, b.payment_amount 
        FROM bookings b 
        JOIN passengers p ON b.passenger_id = p.id 
        JOIN flights f ON b.flight_id = f.flight_id
        ORDER BY b.booking_date DESC";

$result = mysqli_query($conn, $sql);

// Process payment updates
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_payment'])) {
    $booking_id = intval($_POST['booking_id']);
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);
    $payment_amount = floatval($_POST['payment_amount']);

    $update_query = "UPDATE bookings SET payment_status = ?, payment_amount = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "sdi", $payment_status, $payment_amount, $booking_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Payment status updated successfully!'); window.location.href='finance.php';</script>";
    } else {
        echo "<script>alert('Failed to update payment. Please try again.');</script>";
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Management</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">
        <h1>Finance Management</h1>
        
        <h2>Payments Management</h2>
        <a href="../finance_Team/compensation-processing.php" class="button">Go to Compensation Processing</a>

        <table border="1">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Passenger</th>
                    <th>Flight</th>
                    <th>Booking Date</th>
                    <th>Payment Status</th>
                    <th>Payment Amount</th>
                    <th>Update Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['flight_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                    <td><?php echo number_format($row['payment_amount'], 2); ?> KSH</td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                            <select name="payment_status">
                                <option value="Pending" <?php echo $row['payment_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Completed" <?php echo $row['payment_status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                            <input type="number" name="payment_amount" value="<?php echo number_format($row['payment_amount'], 2); ?>" required>
                            <button type="submit" name="update_payment" class="action-btn">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
