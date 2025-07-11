<?php
require '../auth/config.php';
$booking_id = $_GET['booking_id'] ?? null;

if (!$booking_id) {
    die("Invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Choose Payment Method</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <script>
        function redirectToPayment() {
            var method = document.getElementById("payment_method").value;
            var bookingId = document.getElementById("booking_id").value;

            if (method === "credit_card") {
                window.location.href = "credit_card.php?booking_id=" + bookingId;
            } else if (method === "paypal") {
                window.location.href = "pay_payment.php?booking_id=" + bookingId;
            } else if (method === "bank_transfer") {
                window.location.href = "bank_transfer.php?booking_id=" + bookingId;
            }
        }
    </script>
</head>
<body>
<div class="payment-container">
    <h2>Select Payment Method</h2>
    <p>Booking ID: <?php echo htmlspecialchars($booking_id); ?></p>
    
    <input type="hidden" id="booking_id" value="<?php echo $booking_id; ?>">
    
    <label for="payment_method">Payment Method:</label>
    <select id="payment_method">
        <option value="credit_card">Credit Card</option>
        <option value="paypal">PayPal</option>
        <option value="bank_transfer">Bank Transfer</option>
    </select>
    
    <button type="button" class="action-btn" onclick="redirectToPayment()">Proceed</button>
</div>
</body>
</html>
