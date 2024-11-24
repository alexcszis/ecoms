<?php
// Start the session
session_start();

// Database connection
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "ecommerce_db";

// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
require 'database.php';

// Retrieve username from URL parameter
$username = $_GET['username'] ?? null;

// Handle password reset submission
if (isset($_POST['reset_password']) && $username) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in the database
        $sql = "UPDATE users SET password='$hashed_password' WHERE username='$username'";
        
        if ($conn->query($sql) === TRUE) {
            echo "Password reset successful! You can now <a href='login.php'>login</a> with your new password.";
        } else {
            echo "Error updating password: " . $conn->error;
        }
    } else {
        echo "<p style='color:red;'>Passwords do not match. Please try again.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password for <?php echo htmlspecialchars($username); ?></h2>
    <form action="reset_password.php?username=<?php echo htmlspecialchars($username); ?>" method="POST">
        <label>New Password:</label>
        <input type="password" name="new_password" required><br><br>

        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit" name="reset_password">Reset Password</button>
    </form>
</body>
</html>
