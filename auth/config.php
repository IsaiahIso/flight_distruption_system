<?php
$servername = "localhost";
$username = "root";
$password = "ISAIAH254";
$dbname = "flight_distruption";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
/*
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === FALSE) {
    die("Error creating database: " . $conn->error);
}

$conn->select_db($dbname);

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    user_account ENUM('admin', 'cabin-crew', 'ground-staff', 'passengers', 'finance Team') NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
)";

if ($conn->query($sql) === FALSE) {
    die("Error creating table: " . $conn->error);
}
   

   $sql = "CREATE TABLE IF NOT EXISTS flights (
    flight_id INT AUTO_INCREMENT PRIMARY KEY,
    flight_number VARCHAR(20) NOT NULL,
    departure_time DATETIME NOT NULL,
    arrival_time DATETIME NOT NULL,
    departure_location VARCHAR(100) NOT NULL,
    arrival_location VARCHAR(100) NOT NULL,
    status ENUM('Scheduled', 'Delayed', 'Cancelled') NOT NULL DEFAULT 'Scheduled',
    airline VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Execute the query to create the table
if ($conn->query($sql) === TRUE) {
    echo "Flights table is ready.<br>";
} else {
    echo "Error creating flights table: " . $conn->error . "<br>";
}
    */
    
?>