<?php
$booking_id = $_GET['booking_id'] ?? null;
if (!$booking_id) {
    die("Invalid request.");
}

// PayPal integration - Redirect to PayPal checkout
$paypal_url = "https://www.paypal.com/checkout?booking_id=" . $booking_id;
header("Location: " . $paypal_url);
exit;
?>
