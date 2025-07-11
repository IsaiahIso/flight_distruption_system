<?php
require '../auth/config.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch flight status counts (Delayed, On Time, Canceled)
$query = "SELECT status, COUNT(*) as count FROM flights GROUP BY status";
$result = mysqli_query($conn, $query);

$statuses = [];
$counts = [];

while ($row = mysqli_fetch_assoc($result)) {
    $statuses[] = $row['status'];
    $counts[] = $row['count'];
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visual Reports</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .print-btn { padding: 10px 20px; background: #088178; color: black; border: 1px solid black; font-size:14px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="chart-container">
        <h2>Flight Disruption Overview</h2>
        <button onclick="printReport()" class="print-btn">Print Report</button>
        <canvas id="flightChart"></canvas>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById("flightChart").getContext("2d");
            var flightChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: <?php echo json_encode($statuses); ?>,
                    datasets: [{
                        label: "Number of Flights",
                        data: <?php echo json_encode($counts); ?>,
                        backgroundColor: ["blue", "red", "green"],
                        borderColor: ["black"],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });

        function printReport() {
            window.print();
        }
    </script>

</body>
</html>
