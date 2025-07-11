<?php
session_start();
include '../auth/config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_account'] !== 'admin') {
    header('Location: ../auth/login.php'); // Redirect unauthorized users
    exit;
}

// Handle form submission to update user details
if (isset($_POST['update_user'])) {
    // Ensure only admins can submit the update
    if ($_SESSION['user_account'] !== 'admin') {
        echo "Unauthorized action!";
        exit;
    }

    $id = $_POST['id'];
    $firstname = $_POST['first_name'];
    $lastname = $_POST['last_name'];
    $email = $_POST['email'];
    $user_account = $_POST['user_account'];
    $gender = $_POST['gender'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate required fields
    if (empty($firstname) || empty($lastname) || empty($email) || empty($user_account) || empty($gender) || empty($username)) {
        echo "All fields are required!";
        exit;
    }

    // Sanitize inputs
    $firstname = mysqli_real_escape_string($conn, $firstname);
    $lastname = mysqli_real_escape_string($conn, $lastname);
    $email = mysqli_real_escape_string($conn, $email);
    $user_account = mysqli_real_escape_string($conn, $user_account);
    $gender = mysqli_real_escape_string($conn, $gender);
    $username = mysqli_real_escape_string($conn, $username);

    // If password is being updated, hash it before saving
    if (!empty($password)) {
        $password = mysqli_real_escape_string($conn, $password);
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET first_name = '$firstname', last_name = '$lastname', email = '$email', user_account = '$user_account', gender = '$gender', username = '$username', password = '$password' WHERE id = $id";
    } else {
        $sql = "UPDATE users SET first_name = '$firstname', last_name = '$lastname', email = '$email', user_account = '$user_account', gender = '$gender', username = '$username' WHERE id = $id";
    }

    // Execute update query
    if (mysqli_query($conn, $sql)) {
        header('Location: manage_users.php'); // Redirect after successful update
        exit;
    } else {
        echo "Error updating user: " . mysqli_error($conn);
    }
}

// Fetch user details for editing
if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id = $userId";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        echo "User not found!";
        exit;
    }
} else {
    echo "No user ID specified.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="edit_container">
        <h1>Edit User</h1>
        <form method="POST" action="edit_user.php">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

            <label>First Name:</label>
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required><br>

            <label>Last Name:</label>
            <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required><br>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>

            <label>Role:</label>
            <select name="user_account" required>
                <option value="passengers" <?php echo ($user['user_account'] == 'passengers') ? 'selected' : ''; ?>>Passengers</option>
                <option value="cabin-crew" <?php echo ($user['user_account'] == 'cabin-crew') ? 'selected' : ''; ?>>Cabin Crew</option>
                <option value="ground-staff" <?php echo ($user['user_account'] == 'ground-staff') ? 'selected' : ''; ?>>Ground Staff</option>
                <option value="finance Team" <?php echo ($user['user_account'] == 'finance Team') ? 'selected' : ''; ?>>Finance Team</option>
            </select><br>

            <label>Gender:</label>
            <select name="gender" required>
                <option value="Male" <?php echo ($user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
            </select><br>

            <label>Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br>

            <label>Password:</label>
            <input type="password" name="password" placeholder="Leave blank if not changing"><br>

            <button type="submit" name="update_user">Update User</button>
        </form>
    </div>
</body>
</html>
