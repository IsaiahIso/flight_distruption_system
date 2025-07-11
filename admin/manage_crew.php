<?php
require '../auth/config.php';
session_start();
if (!isset($_SESSION['username']) || $_SESSION['user_account'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";

// Handle crew addition
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_username = $_POST['staff_username'];
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];

    $sql = "INSERT INTO crew (staff_username, full_name, role) VALUES ('$staff_username', '$full_name', '$role')";
    
    if (mysqli_query($conn, $sql)) {
        $message = "<div class='success'>✅ Crew member added successfully!</div>";
    } else {
        $message = "<div class='error'>❌ Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Crew</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .success {
  color: green;
  font-weight: bold;
  margin-bottom: 10px;
   }
    .error {
  color: red;
  font-weight: bold;
  margin-bottom: 10px;
     }
    </style>
</head>
<body>

    <div class="crew_container">
        <div class="left">
            <img src="../images/c.png" alt="Crew Management">
        </div>
        <div class="right">
            <h2>Add Crew Member</h2>
            <?php echo $message; ?>
            <form method="post" class="crew_form">
                <label>Username:</label>
                <input type="text" name="staff_username" required>
                
                <label>Full Name:</label>
                <input type="text" name="full_name" required>
                
                <label>Role:</label>
                <select name="role">
                    <option value="Pilot">Pilot</option>
                    <option value="Cabin Crew">Cabin Crew</option>
                    <option value="Ground Staff">Ground Staff</option>
                </select>
                
                <button type="submit">Add Crew</button>
            </form>
        </div>
    </div>

</body>
</html>
