<?php
session_start();

if (isset($_POST['update'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Update quantity if it's greater than zero
    if ($quantity > 0) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
    }
}

if (isset($_POST['remove'])) {
    $product_id = $_POST['product_id'];

    // Remove the item from the cart
    unset($_SESSION['cart'][$product_id]);
}

// Redirect back to the cart page
header("Location: cart.php");
exit();
?>
