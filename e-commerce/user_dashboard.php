<?php
session_start();

// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "ecommerce_db";

// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

require 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch latest 3 orders
$order_query = "SELECT order_number, order_details, order_date, 
                delivery_date AS display_delivery_date, 
                order_status 
                FROM orders 
                WHERE customer_id='$user_id' 
                ORDER BY order_date DESC 
                LIMIT 3";

$order_result = $conn->query($order_query);

// Pagination and product fetching
$products_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $products_per_page;

$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$order_by = "";

if ($sort === 'alphabetical') {
    $order_by = "ORDER BY product_name ASC";
} elseif ($sort === 'price') {
    $order_by = "ORDER BY product_price ASC";
}

$product_query = "SELECT product_id, product_name AS name, product_price AS price 
                  FROM products 
                  $order_by 
                  LIMIT $offset, $products_per_page";
$product_result = $conn->query($product_query);

$total_products_query = "SELECT COUNT(*) AS total FROM products";
$total_products_result = $conn->query($total_products_query);
$total_products = $total_products_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $products_per_page);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Dashboard</title>
</head>
<body>
    <h2>Welcome to Your Account</h2>
    
    <form method="POST" style="display:inline;">
        <button type="submit" name="logout">Logout</button>
    </form>
    
    <h3>Your Latest Orders</h3>
    <table border="1" cellspacing="0" cellpadding="8">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Details</th>
                <th>Date Ordered</th>
                <th>Estimated Delivery Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $order_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $order['order_number']; ?></td>
                    <td><?php echo htmlspecialchars($order['order_details']); ?></td>
                    <td><?php echo $order['order_date']; ?></td>
                    <td><?php echo $order['display_delivery_date']; ?></td>
                    <td><?php echo $order['order_status']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="user_orders.php">View All Orders</a>

    <h3>Products</h3>

    <div>
        <a href="user_dashboard.php?sort=alphabetical">Sort Alphabetically</a> | 
        <a href="user_dashboard.php?sort=price">Sort by Price</a>
    </div>

    <ul>
        <?php while ($product = $product_result->fetch_assoc()) { ?>
            <li>
                <a href="product_details.php?product_id=<?php echo $product['product_id']; ?>">
                    <?php echo $product['name']; ?>
                </a> 
                - $<?php echo $product['price']; ?>
            </li>
        <?php } ?>
    </ul>

    <div>
        <?php if ($page > 1) { ?>
            <a href="user_dashboard.php?page=<?php echo $page - 1; ?>&sort=<?php echo $sort; ?>">Previous</a>
        <?php } ?>

        <?php if ($page < $total_pages) { ?>
            <a href="user_dashboard.php?page=<?php echo $page + 1; ?>&sort=<?php echo $sort; ?>">Next</a>
        <?php } ?>
    </div>

    <h3><a href="cart.php">View Cart</a></h3>
</body>
</html>
