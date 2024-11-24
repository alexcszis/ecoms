<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from the database
$sql = "SELECT product_id, product_name, description, product_price FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Action</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['product_id']}</td>
                <td>{$row['product_name']}</td>
                <td>{$row['description']}</td>
                <td>{$row['product_price']}</td>
                <td>
                    <form action='add_to_cart.php' method='POST'>
                        <input type='hidden' name='product_id' value='{$row['product_id']}'>
                        <button type='submit' name='add_to_cart'>Add to Cart</button>
                    </form>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No products available.";
}

$conn->close();
?>
