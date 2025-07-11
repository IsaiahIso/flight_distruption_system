<?php
// Include the database connection file
include '../auth/config.php';

// Check if the ID is provided in the query string
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Validate the ID (make sure it's a number)
    if (is_numeric($id)) {
        // Delete the user from the database
        $sql = "DELETE FROM users WHERE id = $id";

        if (mysqli_query($conn, $sql)) {
            // Redirect to the user management page after deletion
            header('Location: manage_users.php');
        } else {
            echo "Error deleting user: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid user ID.";
    }
} else {
    echo "No user ID specified.";
}
?>
