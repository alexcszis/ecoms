<?php
// Start session
session_start();

// // Database connection
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "ecommerce_db";

// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
require 'database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Logout functionality
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];

// Fetch all orders
$order_query = "SELECT * FROM orders WHERE customer_id='$user_id' ORDER BY order_date DESC";
$order_result = $conn->query($order_query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Orders</title>
</head>
<body>
    <h2>All Your Orders</h2>
    
    <!-- Logout button -->
    <form method="POST" style="display:inline;">
        <button type="submit" name="logout">Logout</button>
    </form>

    <a href="user_dashboard.php">Back to Dashboard</a>
    
    <table border="1" cellspacing="0" cellpadding="8">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Details</th>
                <th>Date</th>
                <th>Delivery Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $order_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $order['order_number']; ?></td>
                    <td><?php echo htmlspecialchars($order['order_details']); ?></td>
                    <td><?php echo $order['order_date']; ?></td>
                    <td><?php echo $order['delivery_date']; ?></td>
                    <td><?php echo $order['order_status']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
