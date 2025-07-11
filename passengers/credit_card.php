<?php
$booking_id = $_GET['booking_id'] ?? null;
if (!$booking_id) {
    die("Invalid request.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>

    <div class="credit-card--container">
        <div class="credit-image-container">
            <img src="../images/creditcard.png" alt="Credit Card">
        </div>
        <div class="crdit-form-container">
            <h2>Secure Payment</h2>
            <form>
                <input type="text" placeholder="Cardholder Name" required>
                <input type="text" placeholder="Card Number" required>
                <input type="text" placeholder="Expiration Date (MM/YY)" required>
                <input type="text" placeholder="CVV" required>
                <button type="submit">Pay Now</button>
            </form>
        </div>
    </div>

</body>
</html>
