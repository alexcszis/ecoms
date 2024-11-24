<?php
session_start();

require 'database.php';
// // Database connection
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "ecommerce_db";

// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// Login logic
if (isset($_POST['login'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['customer_id'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect based on username
            if ($username === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            $error_message = "Incorrect password. Please try again.";
        }
    } else {
        $error_message = "Username does not exist.";
    }
}

// Forgot password logic
if (isset($_POST['forgot_password'])) {
    $forgot_username = $conn->real_escape_string($_POST['forgot_username']);
    
    $forgot_sql = "SELECT username FROM users WHERE username='$forgot_username'";
    $forgot_result = $conn->query($forgot_sql);

    if ($forgot_result->num_rows > 0) {
        // Redirect to reset_password.php with the username
        header("Location: reset_password.php?username=" . urlencode($forgot_username));
        exit();
    } else {
        $error_message = "Username not found. Please check and try again.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form action="login.php" method="POST">
        <label>Username:</label>
        <input type="text" name="username" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit" name="login">Login</button>
    </form>

    <?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>

    <h3>Forgot Password?</h3>
    <form action="login.php" method="POST">
        <label>Username:</label>
        <input type="text" name="forgot_username" required><br><br>
        <button type="submit" name="forgot_password">Submit</button>
    </form>
    <br>
    <a href="index.php">Back to Home</a> <!-- Back to Index link -->
</body>
</html>
