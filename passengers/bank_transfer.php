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
    <title>Bank Transfer Payment</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="bank-container">
        <h2>Bank Transfer Details</h2>
        <p>Please transfer the total amount to the following bank account and upload the payment receipt.</p>

        <h3>Bank Details:</h3>
        <p><strong>Bank Name:</strong> Kenya Commercial Bank (KCB)</p>
        <p><strong>Account Name:</strong> Kenya Airways Payments</p>
        <p><strong>Account Number:</strong> 123456789</p>
        <p><strong>SWIFT Code:</strong> KCBLKENX</p>

        <form action="process_bank_transfer.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking_id); ?>">

            <label for="receipt">Upload Payment Receipt (PDF/Image):</label>
            <input type="file" name="receipt" accept=".pdf, .jpg, .jpeg, .png" required>

            <button type="submit">Submit Payment Proof</button>
        </form>

        <p>Once the transfer is verified, we will confirm your payment.</p>
    </div>
</body>
</html>
