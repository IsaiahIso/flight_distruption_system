<?php
session_start();
require '../auth/config.php';
require '../vendor/autoload.php'; // Include PHPMailer



if (!isset($_SESSION['user_id']) || $_SESSION['user_account'] !== 'finance Team') {
    header("Location: ../auth/login.php"); // Redirect to login if not authorized
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Email function
function sendCompensationEmail($email, $first_name, $status, $compensation_amount) {
    $mail = new PHPMailer(true);
    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Change this based on your email provider
        $mail->SMTPAuth = true;
        $mail->Username = 'lesorogol1@gmail.com'; // Replace with your email
        $mail->Password = 'vuvt mwxq pgfy zmrx'; // Use App Password if using Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Email details
        $mail->setFrom('lesorogol1@gmail.com', 'FlightDistSys');
        $mail->addAddress($email, $first_name);
        $mail->isHTML(true);
        $mail->Subject = "Compensation Request Update";

        // Email message
        if ($status == 'approved') {
            $message = "Dear $first_name,<br><br>
                        Your compensation request has been <b>approved</b>. 
                        You will receive KSh $compensation_amount in your account.<br><br>
                        Thank you for choosing our system.";
        } else {
            $message = "Dear $first_name,<br><br>
                        Your compensation request has been <b>rejected</b>.<br><br>
                        If you have any concerns, please contact customer support.";
        }

        $mail->Body = $message;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Process Approve/Reject Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $action = isset($_POST['approve']) ? 'approved' : (isset($_POST['reject']) ? 'rejected' : 'pending');

    // Fetch passenger details
    $fetch_query = "SELECT first_name, compensation_amount FROM compensation_requests WHERE email = ? AND status = 'pending' LIMIT 1";
    $stmt = mysqli_prepare($conn, $fetch_query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $first_name, $compensation_amount);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($first_name) {
        // Update the status in the database
        $update_query = "UPDATE compensation_requests SET status = ? WHERE email = ? AND status = 'pending'";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "ss", $action, $email);
        if (mysqli_stmt_execute($stmt)) {
            // Send email notification
            if (sendCompensationEmail($email, $first_name, $action, $compensation_amount)) {
                $_SESSION['message'] = "Compensation request has been $action successfully. Email sent.";
            } else {
                $_SESSION['message'] = "Compensation request has been $action successfully. Email failed.";
            }
        } else {
            $_SESSION['message'] = "Error updating status: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = "No pending requests found for this email.";
    }

    // Redirect to refresh the page
    header("Location: compensation-processing.php");
    exit();
}

// Fetch pending requests
$query = "SELECT cr.*, f.flight_number 
          FROM compensation_requests cr
          LEFT JOIN flights f ON cr.flight_id = f.flight_id
          WHERE cr.status = 'pending'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compensation Processing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: red;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .btn {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            color: white;
        }
        .approve {
            background-color: green;
        }
        .reject {
            background-color: red;
        }
        .message {
            text-align: center;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Compensation Processing</h2>

<!-- Show success/error message -->
<?php if (isset($_SESSION['message'])): ?>
    <p class="message"><?= $_SESSION['message'] ?></p>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<?php if (mysqli_num_rows($result) > 0): ?>
    <table>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Request Type</th>
            <th>Flight Number</th>
            <th>Compensation Amount</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['first_name'] ?></td>
            <td><?= $row['last_name'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['phone_number'] ?></td>
            <td><?= ucfirst($row['request_type']) ?></td>
            <td><?= $row['flight_number'] ? $row['flight_number'] : 'N/A' ?></td>
            <td>KSh <?= $row['compensation_amount'] ? $row['compensation_amount'] : 'Not Calculated' ?></td>
            <td><?= ucfirst($row['status']) ?></td>
            <td>
                <form action="compensation-processing.php" method="POST" style="display: inline;">
                    <input type="hidden" name="email" value="<?= $row['email'] ?>" />
                    <input type="submit" name="approve" value="Approve" class="btn approve">
                    <input type="submit" name="reject" value="Reject" class="btn reject">
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No pending compensation requests found.</p>
<?php endif; ?>

</body>
</html>
