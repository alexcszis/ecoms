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
// Check if product_id is passed in the URL
if (!isset($_GET['product_id'])) {
    die("Product ID is required.");
}

$product_id = intval($_GET['product_id']);

// Fetch product details
$product_query = "SELECT product_name, product_price, description FROM products WHERE product_id = $product_id";
$product_result = $conn->query($product_query);

if ($product_result->num_rows > 0) {
    $product = $product_result->fetch_assoc();
} else {
    die("Product not found.");
}

// Define image folder path
$image_folder = "images/";

// Determine the product image based on the product_id
$image_path = $image_folder . "product_" . $product_id . ".jpg";
if (!file_exists($image_path)) {
    // Use a placeholder image if product image is not found
    $image_path = $image_folder . "blank.jpg";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['product_name']); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
    
    <!-- Product Image -->
    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Product Image" style="width:300px; height:300px;">

    <p><strong>Price:</strong> $<?php echo number_format($product['product_price'], 2); ?></p>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>

    <!-- Add to Cart Button -->
    <form method="POST" action="add_to_cart.php">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="1" min="1" required>
        <br><br>
        <button type="submit" name="action" value="add_to_cart">Add and View Cart</button>
        <button type="submit" name="action" value="add_and_continue">Add to Cart</button>
    </form>

    <a href="user_dashboard.php">Back to Products</a>
</body>
</html>
