<?php
session_start();
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "ecommerce_db";
require 'database.php';
// Check if the cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Your cart is empty!";
    exit();
}

// Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// Calculate the total price for display purposes
$total_price = 0;
$order_details = "";
foreach ($_SESSION['cart'] as $product_id => $product) {
    $subtotal = $product['price'] * $product['quantity'];
    $total_price += $subtotal;
    $order_details .= "{$product['product_name']} ({$product['quantity']}x), ";
}

// Handle form submission to place the order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_order'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "You must log in to place an order.";
        exit();
    }

    $customer_id = $_SESSION['user_id']; // Use logged-in user ID
    $order_date = date("Y-m-d H:i:s");
    $status = "Pending Order";

    // Generate a unique order number
    $order_number = "ORD" . str_pad(mt_rand(0, 999999), 6, "0", STR_PAD_LEFT);

    // Calculate the estimated delivery date (+3 days from order placement)
    $delivery_date = date("Y-m-d", strtotime("+3 days"));

    // Insert into orders table with delivery_date set
    $sql = "INSERT INTO orders (order_number, customer_id, order_details, order_date, order_status, delivery_date, total) 
            VALUES ('$order_number', '$customer_id', '$order_details', '$order_date', '$status', '$delivery_date', '$total_price')";

    if ($conn->query($sql) === TRUE) {
        $order_id = $conn->insert_id;

        // Insert each cart item into order_items
        foreach ($_SESSION['cart'] as $product_id => $product) {
            $quantity = $product['quantity'];
            $price = $product['price'];
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            $stmt->execute();
            $stmt->close();
        }

        // Clear the cart after placing the order
        unset($_SESSION['cart']);

        // Display success message with the estimated delivery date
        echo "Order placed successfully! Your order number is <strong>$order_number</strong>.<br>";
        echo "The estimated delivery date is <strong>$delivery_date</strong>.<br>";
        echo "<a href='user_dashboard.php'>Continue Shopping</a>";
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
</head>
<body>
    <h2>Checkout</h2>
    <table border="1">
        <tr>
            <th>Product Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>
        
        <?php
        foreach ($_SESSION['cart'] as $product_id => $product) {
            $subtotal = $product['price'] * $product['quantity'];
            echo "<tr>
                    <td>{$product['product_name']}</td>
                    <td>\${$product['price']}</td>
                    <td>{$product['quantity']}</td>
                    <td>$" . number_format($subtotal, 2) . "</td>
                  </tr>";
        }
        ?>
        
        <tr>
            <td colspan="3" style="text-align:right;"><strong>Total Price</strong></td>
            <td>$<?php echo number_format($total_price, 2); ?></td>
        </tr>
    </table>

    <form method="POST" action="checkout.php" style="display: inline;">
        <button type="submit" name="confirm_order">Confirm Order</button>
    </form>
    <form method="GET" action="user_dashboard.php" style="display: inline;">
        <button type="submit">Cancel</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>
