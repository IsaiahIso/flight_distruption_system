 <?php
require '../auth/config.php';
require '../vendor/autoload.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Start session
session_start();

if (!isset($_SESSION['username']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$flight_id = $_GET['flight_id'] ?? null;

if (!$flight_id) {
    die("Flight ID is required.");
}

// Get flight details
$query = "SELECT * FROM flights WHERE flight_id = '$flight_id'";
$result = mysqli_query($conn, $query);
$flight = mysqli_fetch_assoc($result);

if (!$flight) {
    die("Flight not found.");
}

// Fetch all passengers booked on the affected flight
$passengerQuery = "SELECT first_name, email FROM passengers WHERE flight_id = '$flight_id'";
$passengerResult = mysqli_query($conn, $passengerQuery);

$passengerEmails = [];
if ($passengerResult) {
    while ($row = mysqli_fetch_assoc($passengerResult)) {
        $passengerEmails[] = [
            'email' => $row['email'],
            'first_name' => $row['first_name'] ?? 'Valued Passenger'
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notify'])) {
    if (empty($passengerEmails)) {
        $emailStatus = ["No passengers found for this flight."];
    } else {
        $emailStatus = [];

        foreach ($passengerEmails as $passenger) {
            $email = $passenger['email'];
            $first_name = htmlspecialchars($passenger['first_name'], ENT_QUOTES, 'UTF-8');

            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['SMTP_EMAIL'];
                $mail->Password = $_ENV['SMTP_PASSWORD'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom($_ENV['SMTP_EMAIL'], 'Flight Disruption Notifications');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Flight Disruption Notification';

                $mail->Body = "
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; color: #333; }
                            .header { font-size: 18px; font-weight: bold; }
                            .content { margin-top: 20px; }
                        </style>
                    </head>
                    <body>
                        <div class='header'>
                            Dear {$first_name},
                        </div>
                        <div class='content'>
                            We regret to inform you that your flight <strong>{$flight['flight_number']}</strong> has been 
                            <strong>{$flight['status']}</strong>. We sincerely apologize for the inconvenience caused.
                            <br><br>";

                if ($flight['status'] === 'Delayed') {
                    $mail->Body .= "We will send you an email regarding your compensation for the delay.";
                } elseif ($flight['status'] === 'Cancelled') {
                    $mail->Body .= "We will send you an email regarding your refund for the cancellation.";
                }

                $mail->Body .= "
                            <br><br>For further assistance, please contact our customer support.
                        </div>
                        <div class='footer' style='margin-top: 20px;'>
                            Regards, <br>FlightDistSys
                        </div>
                    </body>
                    </html>
                ";

                $mail->send();
                $emailStatus[] = "Email sent to $email";
            } catch (Exception $e) {
                $emailStatus[] = "Error sending email to $email: " . $mail->ErrorInfo;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notify Passengers</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="notify_container">
        <div class="notify_img_container">
            <img src="../images/N1.png" alt="notify image">
        </div>
        <div class="notify_form_container">
            <h1>Notify Passengers for Flight Disruption</h1>
            <form method="POST">
                <input type="hidden" name="flight_id" value="<?php echo htmlspecialchars($flight['flight_id']); ?>">

                <p>Notifying all passengers booked on flight <strong><?php echo htmlspecialchars($flight['flight_number']); ?></strong> which is <strong><?php echo htmlspecialchars($flight['status']); ?></strong>.</p>

                <button type="submit" name="notify">Notify All Passengers</button>
            </form>
        </div>
    </div>
    <div class="email-container">
    <?php 
        if (!empty($emailStatus)) {
            foreach ($emailStatus as $msg) {
                if (strpos($msg, 'Error') !== false) {
                    echo "<p class='message error'>" . htmlspecialchars($msg) . "</p>";
                } else {
                    echo "<p class='message success'>" . htmlspecialchars($msg) . "</p>";
                }
            }
        }
    ?>
    </div>
</body>
</html>
