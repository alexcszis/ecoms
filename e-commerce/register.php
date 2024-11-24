<?php
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "ecommerce_db";

// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
require 'database.php';

if (isset($_POST['register'])) {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Ensure unique customer_id generation
    do {
        $customer_id = mt_rand(100000, 999999);
        $check_id = $conn->query("SELECT 1 FROM users WHERE customer_id = '$customer_id'");
    } while ($check_id->num_rows > 0);

    $check_query = "SELECT * FROM users WHERE username='$username' LIMIT 1";
    $result = $conn->query($check_query);

    if ($result->num_rows > 0) {
        echo "Username already exists. Please choose another.";
    } else {
        $sql = "INSERT INTO users (customer_id, first_name, last_name, email, username, password) VALUES ('$customer_id', '$first_name', '$last_name', '$email', '$username', '$password')";

        if ($conn->query($sql) === TRUE) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
</head>
<body>
    <h2>Register</h2>
    <form action="register.php" method="POST">
        <label>First Name:</label>
        <input type="text" name="first_name" required><br><br>

        <label>Last Name:</label>
        <input type="text" name="last_name" required><br><br>

        <label>Email Address:</label>
        <input type="email" name="email" required><br><br>

        <label>Username:</label>
        <input type="text" name="username" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit" name="register">Register</button><br><br>

        <a href="index.php">Back to Home</a> 
    </form>
</body>
</html>
