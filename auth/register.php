<?php 
require 'config.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $username = htmlspecialchars(trim($_POST['username']));
    $user_account = htmlspecialchars(trim($_POST['user_account']));
    $gender = htmlspecialchars(trim($_POST['gender']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));

    if (empty($first_name) || !preg_match("/^[a-zA-Z\s]+$/", $first_name)) {
        $errors[] = "First name should contain only letters and spaces.";
    }
    if (empty($last_name) || !preg_match("/^[a-zA-Z\s]+$/", $last_name)) {
        $errors[] = "Last name should contain only letters and spaces.";
    }
    if (empty($username) || !preg_match("/^\w+$/", $username)) {
        $errors[] = "Username should contain only letters, numbers, and underscores.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password) || !preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/", $password)) {
        $errors[] = "Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, and one number.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, user_account, gender, password, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
        try {
            $stmt->bind_param("sssssss", $first_name, $last_name, $username, $user_account, $gender, $hashed_password, $email);
            $stmt->execute();
            header("Location: login.php");
            exit();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {  
                $errors[] = "Username or email already exists.";
            } else {
                $errors[] = "Error: " . $e->getMessage();
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
    <title>Register</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div id="form-container">
        <div id="image-container">
            <img src="../images/reg.png" alt="Airline Image">
        </div>
        <div id="form-content">
            <img src="../images/logo.jpeg" alt="Logo">
            <h1>Register</h1> 

            <?php if (!empty($errors)): ?>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li style="color: red;"><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form action="" method="POST">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>

                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="user_account">Account Type:</label>
                <select id="user_account" name="user_account" required>
                    <option value="user_account">User Account</option>
                    <option value="admin">Admin</option>
                    <option value="cabin-crew">Cabin Crew</option>
                    <option value="ground-staff">Ground Staff</option>
                    <option value="passengers">Passenger</option>
                    <option value="finance Team">Finance Team</option>
                </select>

                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <button type="submit">Register</button>
            </form>

            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
