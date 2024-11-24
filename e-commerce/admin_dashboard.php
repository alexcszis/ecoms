<?php
session_start();

// Only allow access if logged in as admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit();
}
require 'database.php';
// Include the database connection
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "ecommerce_db";
// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// Logout functionality
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Handling order confirmation, rejection, or deletion
if (isset($_POST['action'])) {
    $order_id = $_POST['order_id'];

    if ($_POST['action'] === 'confirm') {
        $update_sql = "UPDATE orders 
                       SET order_status = 'Confirmed' 
                       WHERE order_id = '$order_id'";
        if (!$conn->query($update_sql)) {
            echo "Error updating order status: " . $conn->error;
        }
    } elseif ($_POST['action'] === 'reject') {
        $update_sql = "UPDATE orders 
                       SET order_status = 'Cancelled' 
                       WHERE order_id = '$order_id'";
        $conn->query($update_sql);
    } elseif ($_POST['action'] === 'delete') {
        $delete_items_sql = "DELETE FROM order_items WHERE order_id = '$order_id'";
        $conn->query($delete_items_sql);

        $delete_order_sql = "DELETE FROM orders WHERE order_id = '$order_id'";
        $conn->query($delete_order_sql);
    }
}

// Fetch all orders for admin view
$sql = "SELECT * FROM orders";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Admin Dashboard</h2>
    
    <form method="POST" style="display:inline;">
        <button type="submit" name="logout">Logout</button>
    </form>

    <h3>Manage Orders</h3>
    <table border="1">
        <tr>
            <th>Order ID</th>
            <th>Order Number</th>
            <th>Customer ID</th>
            <th>Order Details</th>
            <th>Order Date</th>
            <th>Estimated Delivery Date</th>
            <th>Total</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        
        <?php while ($order = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $order['order_id']; ?></td>
                <td><?php echo $order['order_number']; ?></td>
                <td><?php echo $order['customer_id']; ?></td>
                <td><?php echo $order['order_details']; ?></td>
                <td><?php echo $order['order_date']; ?></td>
                <td><?php echo $order['delivery_date'] ?? 'Not set'; ?></td>
                <td><?php echo $order['total']; ?></td>
                <td><?php echo $order['order_status']; ?></td>
                <td>
                    <?php if ($order['order_status'] === 'Pending Order') { ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <button type="submit" name="action" value="confirm">Confirm</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <button type="submit" name="action" value="reject">Reject</button>
                        </form>
                    <?php } elseif ($order['order_status'] === 'Cancelled') { ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this order?');">Delete</button>
                        </form>
                    <?php } else { ?>
                        <em><?php echo $order['order_status']; ?></em>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
