<?php
// Include config.php for database connection
require '../auth/config.php';

// Start the session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if the flight ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_flights.php");
    exit();
}

$flight_id = $_GET['id'];

// Fetch the current flight details
$query = "SELECT * FROM flights WHERE flight_id = '$flight_id'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) === 0) {
    header("Location: manage_flights.php");
    exit();
}
$flight = mysqli_fetch_assoc($result);

// Handle form submission to update the flight
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $flight_number = mysqli_real_escape_string($conn, $_POST['flight_number']);
    $departure_time = mysqli_real_escape_string($conn, $_POST['departure_time']);
    $arrival_time = mysqli_real_escape_string($conn, $_POST['arrival_time']);
    $departure_location = mysqli_real_escape_string($conn, $_POST['departure_location']);
    $arrival_location = mysqli_real_escape_string($conn, $_POST['arrival_location']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $airline = mysqli_real_escape_string($conn, $_POST['airline']);

    // Server-side validation for times
    $current_time = date('Y-m-d H:i:s'); // Current time
    if ($departure_time < $current_time) {
        $error_message = "Error: Departure time cannot be in the past.";
    } elseif ($arrival_time <= $departure_time) {
        $error_message = "Error: Arrival time must be after the departure time.";
    } else {
        // Update the flight in the database
        $update_query = "UPDATE flights 
                         SET flight_number = '$flight_number', 
                             departure_time = '$departure_time', 
                             arrival_time = '$arrival_time', 
                             departure_location = '$departure_location', 
                             arrival_location = '$arrival_location', 
                             status = '$status', 
                             airline = '$airline' 
                         WHERE flight_id = '$flight_id'";

        if (mysqli_query($conn, $update_query)) {
            $success_message = "Flight updated successfully!";
        } else {
            $error_message = "Error updating flight: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Flight</title>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const departureTimeInput = document.getElementById('departure_time');
            const arrivalTimeInput = document.getElementById('arrival_time');

            // Set the minimum value for departure time to the current date and time
            const now = new Date();
            const formattedNow = now.toISOString().slice(0, 16); // Format as "YYYY-MM-DDTHH:mm"
            departureTimeInput.min = formattedNow;

            // Adjust arrival time's min whenever departure time changes
            departureTimeInput.addEventListener('input', () => {
                const departureTime = new Date(departureTimeInput.value);
                if (departureTimeInput.value) {
                    arrivalTimeInput.min = departureTime.toISOString().slice(0, 16);
                }
            });
        });
    </script>
</head>
<body>
    <h1>Edit Flight</h1>

    <!-- Display success or error message -->
    <?php if (!empty($error_message)) { ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php } ?>
    <?php if (!empty($success_message)) { ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php } ?>

    <!-- Edit Flight Form -->
    <form method="POST" action="">
        <label for="flight_number">Flight Number:</label>
        <input type="text" id="flight_number" name="flight_number" value="<?php echo htmlspecialchars($flight['flight_number']); ?>" required><br>

        <label for="departure_time">Departure Time:</label>
        <input type="datetime-local" id="departure_time" name="departure_time" 
               value="<?php echo date('Y-m-d\TH:i', strtotime($flight['departure_time'])); ?>" required><br>

        <label for="arrival_time">Arrival Time:</label>
        <input type="datetime-local" id="arrival_time" name="arrival_time" 
               value="<?php echo date('Y-m-d\TH:i', strtotime($flight['arrival_time'])); ?>" required><br>

        <label for="departure_location">Departure Location:</label>
        <input type="text" id="departure_location" name="departure_location" value="<?php echo htmlspecialchars($flight['departure_location']); ?>" required><br>

        <label for="arrival_location">Arrival Location:</label>
        <input type="text" id="arrival_location" name="arrival_location" value="<?php echo htmlspecialchars($flight['arrival_location']); ?>" required><br>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Scheduled" <?php echo $flight['status'] === 'Scheduled' ? 'selected' : ''; ?>>Scheduled</option>
            <option value="Delayed" <?php echo $flight['status'] === 'Delayed' ? 'selected' : ''; ?>>Delayed</option>
            <option value="Cancelled" <?php echo $flight['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
        </select><br>

        <label for="airline">Airline:</label>
        <input type="text" id="airline" name="airline" value="<?php echo htmlspecialchars($flight['airline']); ?>" required><br>

        <button type="submit">Update Flight</button>
    </form>

    <br>
    <a href="manage_flights.php">Back to Manage Flights</a>
</body>
</html>
