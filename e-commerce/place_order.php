<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if the cart has items
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Your cart is empty!";
    exit();
}
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

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Insert order into database with "Pending Order" status
$order_status = "Pending Order";
$order_details = json_encode($_SESSION['cart']); // Save product IDs as JSON
$order_date = date("Y-m-d H:i:s");

$sql = "INSERT INTO orders (customer_id, order_details, order_date, order_status) VALUES ('$user_id', '$order_details', '$order_date', '$order_status')";

if ($conn->query($sql) === TRUE) {
    // Clear the cart session after placing the order
    unset($_SESSION['cart']);
    echo "Order placed successfully!";
    echo "<a href='user_dashboard.php'>Return to Dashboard</a>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
