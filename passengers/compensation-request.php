<?php
session_start();
require '../auth/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_account'] !== 'passengers') {
    header("Location: ../auth/login.php");
    exit();
}

// Initialize message variable
$message = "";

// Handle form submission
if (isset($_POST['submit'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $request_type = mysqli_real_escape_string($conn, $_POST['request_type']);
    $flight_id = mysqli_real_escape_string($conn, $_POST['flight_id']);

    $compensation_amount = 0;

    // If the request is for delay compensation, calculate amount
    if ($request_type == 'delay') {
        $query = "SELECT departure_time, actual_departure_time FROM flights WHERE flight_id = '$flight_id'";
        $result = mysqli_query($conn, $query);

        if ($row = mysqli_fetch_assoc($result)) {
            $departure_time = strtotime($row['departure_time']);
            $actual_departure_time = strtotime($row['actual_departure_time']);
            $delay_hours = ($actual_departure_time - $departure_time) / 3600;

            if ($delay_hours >= 2) {
                $compensation_amount = 5000;
            }
            if ($delay_hours >= 5) {
                $compensation_amount = 10000;
            }
        }
    }

    $insert_query = "INSERT INTO compensation_requests (first_name, last_name, email, phone_number, request_type, flight_id, compensation_amount) 
                     VALUES ('$first_name', '$last_name', '$email', '$phone_number', '$request_type', '$flight_id', '$compensation_amount')";

    if (mysqli_query($conn, $insert_query)) {
        $message = "<p class='success-message'>Compensation request submitted successfully!</p>";
    } else {
        $message = "<p class='error-message'>Error: " . mysqli_error($conn) . "</p>";
    }
}

// Fetch available flights
$flights_query = "SELECT flight_id, flight_number FROM flights";
$flights_result = mysqli_query($conn, $flights_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compensation Request</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="compensation-request-container">
        <div class="compensation-request">
            <h2>Compensation Request Form</h2>

            <?php echo $message; ?>

            <form action="compensation-request.php" method="POST">
                <div class="compensation-request form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" name="first_name" required>
                </div>
                
                <div class="compensation-request form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" name="last_name" required>
                </div>
                
                <div class="compensation-request form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="compensation-request form-group">
                    <label for="phone_number">Phone Number:</label>
                    <input type="text" name="phone_number" required>
                </div>
                
                <div class="compensation-request form-group">
                    <label for="request_type">Compensation Type:</label>
                    <select name="request_type" required>
                        <option value="delay">Compensation Due to Delay</option>
                        <option value="refund">Full Refund</option>
                        <option value="rebook">Rebook Flight</option>
                    </select>
                </div>
                
                <div class="compensation-request form-group">
                    <label for="flight_id">Select Flight:</label>
                    <select name="flight_id" required>
                        <option value="">-- Select a Flight --</option>
                        <?php
                        if ($flights_result && mysqli_num_rows($flights_result) > 0) {
                            while ($flight = mysqli_fetch_assoc($flights_result)) {
                                echo "<option value='" . $flight['flight_id'] . "'>" . $flight['flight_number'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>No available flights</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <input type="submit" name="submit" value="Submit Request">
            </form>
        </div>
    </div>
</body>
</html>
