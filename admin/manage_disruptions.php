<?php
require '../auth/config.php';
require '../vendor/autoload.php'; 

// Twilio and PHPMailer namespaces
use Twilio\Rest\Client;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Start the session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch flights with disruption details
$query = "SELECT flight_id, flight_number, departure_time, arrival_time, departure_location, arrival_location, status, airline 
          FROM flights 
          WHERE status IN ('Delayed', 'Cancelled')"; // Filter only disrupted flights
$result = mysqli_query($conn, $query);

// Handle notifications (SMS and Email)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notify'])) {
    // Twilio setup
    $sid = 'YOUR_TWILIO_ACCOUNT_SID';
    $token = 'YOUR_TWILIO_AUTH_TOKEN';
    $twilio = new Client($sid, $token);

    // PHPMailer setup
    $flightId = $_POST['flight_id'];
    $passengerEmail = $_POST['passenger_email'];
    $passengerPhone = $_POST['passenger_phone'];
    $message = $_POST['message'];

    // Send SMS
    try {
        $twilio->messages->create(
            $passengerPhone,
            [
                'from' => 'YOUR_TWILIO_PHONE_NUMBER',
                'body' => $message
            ]
        );
        $smsStatus = "SMS sent successfully!";
    } catch (Exception $e) {
        $smsStatus = "Error sending SMS: " . $e->getMessage();
    }

    // Send Email
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'YOUR_EMAIL@gmail.com'; // Your email
        $mail->Password = 'YOUR_EMAIL_PASSWORD'; // Your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('YOUR_EMAIL@gmail.com', 'Flight Disruption Notifications');
        $mail->addAddress($passengerEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Flight Disruption Notification';
        $mail->Body = $message;

        $mail->send();
        $emailStatus = "Email sent successfully!";
    } catch (Exception $e) {
        $emailStatus = "Error sending email: " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Flight Disruptions</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="distruptions-container">
    <h1>Manage Flight Disruptions</h1>

    <!-- Display success/failure messages -->
    <?php if (!empty($smsStatus)) { echo "<p>$smsStatus</p>"; } ?>
    <?php if (!empty($emailStatus)) { echo "<p>$emailStatus</p>"; } ?>

    <table border="1">
        <thead>
            <tr>
                <th>Flight Number</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Departure Location</th>
                <th>Arrival Location</th>
                <th>Status</th>
                <th>Airline</th>
                <th>Notify</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['flight_number']; ?></td>
                    <td><?php echo $row['departure_time']; ?></td>
                    <td><?php echo $row['arrival_time']; ?></td>
                    <td><?php echo $row['departure_location']; ?></td>
                    <td><?php echo $row['arrival_location']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td><?php echo $row['airline']; ?></td>
                    <td>
                        <!-- "Notify" link that redirects to notify_passenger.php with the flight_id -->
                        <a href="notification.php ?flight_id=<?php echo $row['flight_id']; ?>">Notify</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
</body>
</html>
