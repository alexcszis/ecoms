<?php
session_start();

// Check if the cart is initialized
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<h2>Your Shopping Cart</h2>";
    echo "<p>Your cart is empty.</p>";
    echo '<a href="user_dashboard.php">Continue Shopping</a>';
    exit();
}

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
</head>
<body>
    <h2>Your Shopping Cart</h2>

    <table border="1">
        <tr>
            <th>Product Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Action</th>
        </tr>

        <?php foreach ($_SESSION['cart'] as $product_id => $product) { 
            // Ensure all necessary keys are available
            $subtotal = $product['price'] * $product['quantity'];
            $total_price += $subtotal;
        ?>
            <tr>
                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                <td><?php echo number_format($product['price'], 2); ?></td>
                <td><?php echo $product['quantity']; ?></td>
                <td><?php echo number_format($subtotal, 2); ?></td>
                <td>
                    <form method="POST" action="update_cart.php" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <input type="number" name="quantity" value="<?php echo $product['quantity']; ?>" min="1">
                        <button type="submit">Update</button>
                    </form>
                    <form method="POST" action="remove_from_cart.php" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <button type="submit">Remove</button>
                    </form>
                </td>
            </tr>
        <?php } ?>

        <tr>
            <td colspan="3">Total Price</td>
            <td colspan="2"><?php echo number_format($total_price, 2); ?></td>
        </tr>
    </table>

    <!-- Navigation options -->
    <a href="checkout.php">Proceed to Checkout</a>
    <a href="user_dashboard.php" style="margin-left: 10px;">Continue Shopping</a>
</body>
</html>
