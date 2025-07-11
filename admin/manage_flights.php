<?php
// Include config.php for database connection
require '../auth/config.php';

// Start the session
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle form submission for adding a new flight
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $flight_number = mysqli_real_escape_string($conn, $_POST['flight_number']);
    $departure_time = mysqli_real_escape_string($conn, $_POST['departure_time']);
    $arrival_time = mysqli_real_escape_string($conn, $_POST['arrival_time']);
    $departure_location = mysqli_real_escape_string($conn, $_POST['departure_location']);
    $arrival_location = mysqli_real_escape_string($conn, $_POST['arrival_location']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $airline = mysqli_real_escape_string($conn, $_POST['airline']);
    $flight_type = mysqli_real_escape_string($conn, $_POST['flight_type']);


    // Server-side validation for times
    $current_time = date('Y-m-d H:i:s'); // Current time
    if ($departure_time < $current_time) {
        $error_message = "Error: Departure time cannot be in the past.";
    } elseif ($arrival_time <= $departure_time) {
        $error_message = "Error: Arrival time must be after the departure time.";
    } else {
        // Insert the new flight into the database
        $sql = "INSERT INTO flights (flight_number, departure_time, arrival_time, departure_location, arrival_location, status, airline, flight_type) 
                VALUES ('$flight_number', '$departure_time', '$arrival_time', '$departure_location', '$arrival_location', '$status', '$airline', '$flight_type')";
        if (mysqli_query($conn, $sql)) {
            $success_message = "Flight added successfully!";
        } else {
            $error_message = "Error adding flight: " . mysqli_error($conn);
        }
    }
}

// Fetch flights from the database
$query = "SELECT * FROM flights";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Flights</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const departureTimeInput = document.getElementById('departure_time');
            const arrivalTimeInput = document.getElementById('arrival_time');

             
            const now = new Date();
            const formattedNow = now.toISOString().slice(0, 16); // Format as "YYYY-MM-DDTHH:mm"
            departureTimeInput.min = formattedNow;

            // Adjust arrival time's min whenever departure time changes
            departureTimeInput.addEventListener('input', () => {
                const departureTime = new Date(departureTimeInput.value);
                if (departureTimeInput.value) {
                    arrivalTimeInput.min = departureTime.toISOString().slice(0, 16);
                } else {
                    arrivalTimeInput.min = formattedNow;
                }
            });
        });

         
        function validateTimes() {
            const departureTime = new Date(document.getElementById('departure_time').value);
            const arrivalTime = new Date(document.getElementById('arrival_time').value);
            
            if (arrivalTime <= departureTime) {
                alert('Arrival time must be after departure time.');
                return false;  
            }
            return true;  
        }
    </script>
</head>
<body>
    
    <h1>Manage Flights</h1>
    <div id="container">

    <div id="manage-form-container">
 
    <?php if (!empty($error_message)) { ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php } ?>
    <?php if (!empty($success_message)) { ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php } ?>

   
    <h2>Add New Flight</h2>
    <form method="POST" action="" onsubmit="return validateTimes()" >
        <label for="flight_number">Flight Number:</label>
        <input type="text" id="flight_number" name="flight_number" required><br>

        <label for="departure_time">Departure Time:</label>
        <input type="datetime-local" id="departure_time" name="departure_time" required><br>

        <label for="arrival_time">Arrival Time:</label>
        <input type="datetime-local" id="arrival_time" name="arrival_time" required><br>

        <label for="departure_location">Departure Location:</label>
        <input type="text" id="departure_location" name="departure_location" required><br>

        <label for="arrival_location">Arrival Location:</label>
        <input type="text" id="arrival_location" name="arrival_location" required><br>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Scheduled">Scheduled</option>
            <option value="Delayed">Delayed</option>
            <option value="Cancelled">Cancelled</option>
        </select><br>

        <label for="airline">Airline:</label>
        <input type="text" id="airline" name="airline" required><br><label for="flight_type">Flight Type:</label>
       <select name="flight_type" id="flight_type" required>
                   <option value="Local">Local</option>
                   <option value="International">International</option>
            </select>



        <button type="submit">Add Flight</button>
    </form>
    </div>

    <div id="table-container">

    <!-- Flight List -->
    <h2>Flight List</h2>
    <table>
        <thead>
            <tr>
                <th>Flight ID</th>
                <th>Flight Number</th>
                <th>Departure Time</th>
                <th>Arrival Time</th>
                <th>Departure Location</th>
                <th>Arrival Location</th>
                <th>Status</th>
                <th>Airline</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['flight_id']; ?></td>
                    <td><?php echo $row['flight_number']; ?></td>
                    <td><?php echo $row['departure_time']; ?></td>
                    <td><?php echo $row['arrival_time']; ?></td>
                    <td><?php echo $row['departure_location']; ?></td>
                    <td><?php echo $row['arrival_location']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td><?php echo $row['airline']; ?></td>
                    <td>
                        <a href="edit_flight.php?id=<?php echo $row['flight_id']; ?>">Edit</a> |
                        <a href="delete_flight.php?id=<?php echo $row['flight_id']; ?>" onclick="return confirm('Are you sure you want to delete this flight?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
    </div>
</body>
</html>
