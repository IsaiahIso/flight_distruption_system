<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available flights</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <h1>Available Flights</h1>
</body>
</html>
<?php
// Include database configuration
require '../auth/config.php';

if (isset($_GET['from']) && isset($_GET['to'])) {
    $from = $_GET['from'];
    $to = $_GET['to'];

    // Define the query to retrieve flights based on the given departure and arrival locations
    $query = "SELECT * FROM flights 
              WHERE LOWER(departure_location) LIKE LOWER('%$from%')
              AND LOWER(arrival_location) LIKE LOWER('%$to%')
              AND (status = 'Scheduled' OR status = 'Delayed' OR status = 'Cancelled')";
    
    // Execute the query
    $result = $conn->query($query);
    
    // Check if any flights are found
    if ($result->num_rows > 0) {
        // Display results in a table
        echo "<table>
                <tr>
                    <th>Flight Number</th>
                    <th>Departure Time</th>
                    <th>Arrival Time</th>
                    <th>Airline</th>
                    <th>Status</th>
                </tr>";

        // Loop through the results and display them in table rows
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['flight_number'] . "</td>
                    <td>" . $row['departure_time'] . "</td>
                    <td>" . $row['arrival_time'] . "</td>
                    <td>" . $row['airline'] . "</td>
                    <td>" . $row['status'] . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='no-results'>No flights found for the selected route.</p>";
    }
} else {
    echo "<p class='no-results'>Please provide both departure and arrival locations.</p>";
}
?>
  <?php
// Include database configuration
require '../auth/config.php';

if (isset($_GET['from']) && isset($_GET['to'])) {
    $from = $_GET['from'];
    $to = $_GET['to'];

    // Define the query to retrieve flights based on the given departure and arrival locations
    $query = "SELECT * FROM flights 
              WHERE LOWER(departure_location) LIKE LOWER('%$from%')
              AND LOWER(arrival_location) LIKE LOWER('%$to%')
              AND (status = 'Scheduled' OR status = 'Delayed' OR status = 'Cancelled')";
    
    // Execute the query
    $result = $conn->query($query);
    
    // Check if any flights are found
    if ($result->num_rows > 0) {
        // Display results in a table
        echo "<h3>These are the flights:</h3>";
        echo "<table>
                <tr>
                    <th>Flight Number</th>
                    <th>Departure Time</th>
                    <th>Arrival Time</th>
                    <th>Airline</th>
                    <th>Status</th>
                </tr>";

        // Loop through the results and display them in table rows
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['flight_number'] . "</td>
                    <td>" . $row['departure_time'] . "</td>
                    <td>" . $row['arrival_time'] . "</td>
                    <td>" . $row['airline'] . "</td>
                    <td>" . $row['status'] . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='no-results'>No flights found for the selected route.</p>";
    }
} else {
    echo "<p class='no-results'>Please provide both departure and arrival locations.</p>";
}
?>

<!-- Back to Home Button -->
<div class="back-home">
    <a href="index.html#search-flights" class="back-home-button">Back to Home</a>
</div>

