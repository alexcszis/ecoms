<?php
session_start();

require 'database.php';
// Ensure $_SESSION['cart'] is an array
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Database connection
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "ecommerce_db";

// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// Check for POST data
if (isset($_POST['product_id']) && isset($_POST['quantity']) && isset($_POST['action'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $action = $_POST['action'];

    // Ensure quantity is at least 1
    if ($quantity < 1) {
        $quantity = 1;
    }

    // Fetch product details
    $product_query = "SELECT product_name, product_price FROM products WHERE product_id = $product_id";
    $product_result = $conn->query($product_query);

    if ($product_result->num_rows > 0) {
        $product = $product_result->fetch_assoc();

        // Add or update the product in the cart
        if (isset($_SESSION['cart'][$product_id])) {
            // Update the quantity
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            // Add new product to the cart
            $_SESSION['cart'][$product_id] = [
                'product_name' => $product['product_name'],
                'price' => $product['product_price'],
                'quantity' => $quantity,
            ];
        }
    }

    // Redirect based on the action
    if ($action === 'add_and_continue') {
        header("Location: user_dashboard.php");
    } else {
        header("Location: cart.php");
    }
    exit();
} else {
    die("Invalid input.");
}

$conn->close();
?>
