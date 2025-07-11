<?php
require '../auth/config.php'; // Adjust the path to your config file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password
    $user_account = $_POST['user_account'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];

    // Check if the username already exists
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Error: Username already exists!";
    } else {
        // Insert the new user into the database
        $query = "INSERT INTO users (first_name, last_name, username, user_account, gender, password, email) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssss", $first_name, $last_name, $username, $user_account, $gender, $password, $email);

        if ($stmt->execute()) {
            echo "New user added successfully!";
            header("Location: manage_users.php"); // Redirect to manage_users
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: manage_users.php");
    exit();
}
?>
