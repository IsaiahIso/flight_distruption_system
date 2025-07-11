<?php
// Include config.php for database connection
require '../auth/config.php';

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_account'] !== 'passengers') {
    header("Location: ../auth/login.php");
    exit();
}

// Initialize error message variable
$error_message = "";

// Handle form submission for booking a passenger
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $passenger_first_name = trim($_POST['passenger_first_name']);
    $passenger_last_name = trim($_POST['passenger_last_name']);
    $passenger_email = trim($_POST['passenger_email']);
    $passenger_phone_number = trim($_POST['passenger_phone_number']);
    $flight_id = trim($_POST['flight_id']);

    // Validate first name and last name (only letters allowed)
    if (!preg_match("/^[a-zA-Z\s]+$/", $passenger_first_name) || !preg_match("/^[a-zA-Z\s]+$/", $passenger_last_name)) {
        $error_message = "First name and last name should contain only letters.";
    }

    // Validate email
    if (!filter_var($passenger_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    }

    // Validate phone number (must be Kenyan format 2547XXXXXXXX)
    if (!preg_match('/^2547\d{8}$/', $passenger_phone_number)) {
        $error_message = "Invalid phone number. It should start with 2547 and have 12 digits.";
    }
    $sql_check_phone = "SELECT id FROM passengers WHERE phone_number = '$passenger_phone_number'";
    $result_check_phone = mysqli_query($conn, $sql_check_phone);
    if (mysqli_num_rows($result_check_phone) > 0) {
        $error_message = "This phone number is already registered.";
    }

    // Stop execution if there's an error
    if (!empty($error_message)) {
        echo "<script>alert('$error_message');</script>";
    } else {
        // Sanitize input before inserting into the database
        $passenger_first_name = mysqli_real_escape_string($conn, $passenger_first_name);
        $passenger_last_name = mysqli_real_escape_string($conn, $passenger_last_name);
        $passenger_email = mysqli_real_escape_string($conn, $passenger_email);
        $passenger_phone_number = mysqli_real_escape_string($conn, $passenger_phone_number);
        $flight_id = mysqli_real_escape_string($conn, $flight_id);

        // Fetch the flight type
        $sql_check_flight_type = "SELECT flight_type FROM flights WHERE flight_id = '$flight_id'";
        $result_check = mysqli_query($conn, $sql_check_flight_type);
        $flight_data = mysqli_fetch_assoc($result_check);
        $flight_type = $flight_data['flight_type'];

        // Insert the new passenger into the passengers table
        $sql_insert_passenger = "INSERT INTO passengers (first_name, last_name, email, phone_number, flight_id) 
                            VALUES ('$passenger_first_name', '$passenger_last_name', '$passenger_email', '$passenger_phone_number', '$flight_id')";
        
        if (mysqli_query($conn, $sql_insert_passenger)) {
            $passenger_id = mysqli_insert_id($conn);

            // Insert the booking
            $sql_insert_booking = "INSERT INTO bookings (flight_id, passenger_id, booking_date, payment_status, payment_amount) 
                                VALUES ('$flight_id', '$passenger_id', NOW(), 'Pending', 0)";
            
            if (mysqli_query($conn, $sql_insert_booking)) {
                $booking_id = mysqli_insert_id($conn);

                // Update flight_id in passengers table
                $sql_update_passenger = "UPDATE passengers SET flight_id = '$flight_id' WHERE id = '$passenger_id'";
                mysqli_query($conn, $sql_update_passenger);

                // Redirect based on flight type
                if ($flight_type === 'Local') {
                    header("Location: ../passengers/payment.php?booking_id=$booking_id");
                } else {
                    header("Location: ../passengers/other_payments.php?booking_id=$booking_id");
                }
                exit();
            } else {
                $error_message = "Error creating booking: " . mysqli_error($conn);
            }
        } else {
            $error_message = "Error adding passenger: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Flight</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div id="booking-form-container"> 
        <div id="booking-img">
            <img src="../images/B2.jpg" alt="Flight Booking">
        </div>
        <div id="booking-form">
            <h1>Book Flight</h1>

            <?php if (!empty($error_message)) { ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php } ?>

            <form method="POST" action="">
                <label for="passenger_first_name">First Name:</label>
                <input type="text" id="passenger_first_name" name="passenger_first_name" required><br>

                <label for="passenger_last_name">Last Name:</label>
                <input type="text" id="passenger_last_name" name="passenger_last_name" required><br>

                <label for="passenger_email">Email:</label>
                <input type="email" id="passenger_email" name="passenger_email" required><br>

                <label for="passenger_phone_number">Phone Number:</label>
                <input type="text" id="passenger_phone_number" name="passenger_phone_number" required><br>

                <label for="flight_id">Select Flight:</label>
                <select id="flight_id" name="flight_id" required>
                    <?php
                    $query = "SELECT * FROM flights";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['flight_id'] . "'>" . $row['flight_number'] . " - " . $row['departure_location'] . " to " . $row['arrival_location'] . "</option>";
                    }
                    ?>
                </select><br>
                <button type="submit">Book Now</button>
            </form>
        </div>
    </div>
</body>
</html> 