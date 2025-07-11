<?php
// Include config.php for database connection
require '../auth/config.php';
require '../vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_account'] !== 'passengers') {
    header("Location: ../auth/login.php");
    exit();
}

// Get booking ID from URL
if (!isset($_GET['booking_id'])) {
    die("Booking ID is required.");
}

$booking_id = intval($_GET['booking_id']);

// Fetch booking and flight details
$sql = "SELECT b.id AS booking_id, f.flight_number, f.departure_location, f.arrival_location 
        FROM bookings b 
        JOIN flights f ON b.flight_id = f.flight_id 
        WHERE b.id = '$booking_id'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}

$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    die("Invalid booking ID or no booking found.");
}

// Process Payment via M-Pesa
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $amount = floatval($_POST['amount']);

    if (empty($phone_number) || empty($amount)) {
        die("Phone number and amount are required.");
    }

    // Load credentials from .env
    $consumer_key = $_ENV['DARAJA_CONSUMER_KEY'];
    $consumer_secret = $_ENV['DARAJA_CONSUMER_SECRET'];
    $shortcode = $_ENV['DARAJA_SHORTCODE'];
    $passkey = $_ENV['DARAJA_PASSKEY'];
    $callback_url = $_ENV['DARAJA_CALLBACK_URL'];

    $timestamp = date("YmdHis");
    $password = base64_encode($shortcode . $passkey . $timestamp);
    $stk_push_url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
    $access_token_url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";

    // Get Access Token
    $credentials = base64_encode("$consumer_key:$consumer_secret");

    $ch = curl_init($access_token_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = json_decode(curl_exec($ch));
    curl_close($ch);

    if (!isset($response->access_token)) {
        die("Failed to get access token");
    }

    $access_token = $response->access_token;

    // Send STK Push
    $stk_data = [
        "BusinessShortCode" => $shortcode,
        "Password" => $password,
        "Timestamp" => $timestamp,
        "TransactionType" => "CustomerPayBillOnline",
        "Amount" => $amount,
        "PartyA" => $phone_number,
        "PartyB" => $shortcode,
        "PhoneNumber" => $phone_number,
        "CallBackURL" => $callback_url,
        "AccountReference" => "FLIGHT$booking_id",
        "TransactionDesc" => "Flight Payment"
    ];

    $ch = curl_init($stk_push_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $access_token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($stk_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = json_decode(curl_exec($ch));
    curl_close($ch);

    if (isset($response->ResponseCode) && $response->ResponseCode == "0") {
        $updateQuery = "UPDATE bookings SET payment_status = 'Completed', payment_amount = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "di", $amount, $booking_id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "<script>alert('Payment successful! Your booking is confirmed.');</script>";
        } else {
            echo "<script>alert('Payment recorded, but database update failed. Contact support.');</script>";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Payment failed: " . json_encode($response) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="payment-container">
        <div class="logo-container">
            <img src="../images/mpesa-logo.png" alt="Company Logo">
        </div>
        <h1>Flight Payment</h1>
        <div class="flight-info">
            <p><strong>Flight:</strong> <?php echo htmlspecialchars($booking['flight_number']); ?></p>
            <p><strong>Route:</strong> <?php echo htmlspecialchars($booking['departure_location']); ?> to <?php echo htmlspecialchars($booking['arrival_location']); ?></p>
        </div>

        <form method="POST" class="payment-form">
            <label>Enter Payment Amount (KSH):</label>
            <input type="number" name="amount" required placeholder="e.g. 5000">

            <label>Enter M-Pesa Phone Number:</label>
            <input type="text" name="phone_number" required placeholder="2547XXXXXXXX">

            <button type="submit">Pay with M-Pesa</button>
        </form>
    </div>
</body>
</html>
