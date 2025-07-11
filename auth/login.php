<?php
require 'config.php'; // Include the database connection

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username_or_email = htmlspecialchars(trim($_POST['username_or_email']));
    $password = htmlspecialchars(trim($_POST['password']));

    if (empty($username_or_email)) $errors[] = "Username or email is required";
    if (empty($password)) $errors[] = "Password is required";

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, username, email, password, user_account FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username_or_email, $username_or_email);

        try {
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                if (password_verify($password, $user['password'])) {
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_account'] = $user['user_account'];

                    if ($user['user_account'] === 'passengers') {
                        $_SESSION['passenger_id'] = $user['id']; // Set passenger-specific session variable
                    }

                    switch ($user['user_account']) {
                        case 'admin':
                            header("Location: ../admin/admin.php");
                            exit();
                        case 'cabin-crew':
                            header("Location: cabin_crew.php");
                            exit();
                        case 'ground-staff':
                            header("Location: ../groundstaff/groundstaff.php");
                            exit();
                        case 'passengers':
                            header("Location: ../passengers/passengers.php");
                            exit();
                        case 'finance Team':
                            header("Location: ../finance_Team/finance.php");
                            exit();
                        default:
                            header("Location: index.php");
                            exit();
                    }
                } else {
                    $errors[] = "Invalid password";
                }
            } else {
                $errors[] = "No account found with that username or email";
            }
        } catch (mysqli_sql_exception $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div id="form-container">
        <div id="image-container">
            <img src="../images/reg.png" alt="Airline Image">
        </div>
        <div id="form-content">
            <img src="../images/logo.jpeg" alt="Logo">
            <h1>Login</h1>

            <?php if (!empty($errors)): ?>
                <div id="error-container">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <label for="username_or_email">Username or Email:</label>
                <input type="text" id="username_or_email" name="username_or_email" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Login</button>
            </form>

            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>

    <script>
        setTimeout(() => {
            const errorContainer = document.getElementById('error-container');
            if (errorContainer) {
                errorContainer.style.display = 'none';
            }
        }, 5000);
    </script>
</body>
</html>
