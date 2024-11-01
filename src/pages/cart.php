<?php
require_once '../../private/required.php'; // Include necessary files

// Ensure user is logged in
requireLogin();

// Fetch cart items for the logged-in user
$userId = $_SESSION['user_id'];
$cartItems = getCartItems($userId); // This function should be in functions.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link rel="stylesheet" href="../../src/styles/style.css">
    <link rel="stylesheet" href="../../src/styles/header.css">
    <link rel="stylesheet" href="../../src/styles/footer.css">
    <script>
    // Function to update cart item quantity
    function updateCart(productId, quantity) {
        if (quantity < 1) {
            removeFromCart(productId); // If quantity is less than 1, remove the item
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "update_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        // Send product ID and new quantity
        xhr.send(`product_id=${productId}&quantity=${quantity}`);

        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                // Update total price in the cart
                document.getElementById("cart-total").textContent = 'Total: $' + response.totalPrice;
                // Update total item count (sum of quantities) in the header
                document.getElementById("cart-count").textContent = response.itemCount;
            }
        };
    }

    // Function to remove an item from the cart
    function removeFromCart(productId) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "remove_from_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        // Send product ID to remove
        xhr.send(`product_id=${productId}`);

        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                // Remove the item from the DOM
                document.getElementById(`cart-item-${productId}`).remove();
                // Update total price
                document.getElementById("cart-total").textContent = 'Total: $' + response.totalPrice;
                // Update item count
                document.getElementById("cart-count").textContent = response.itemCount;

                // Check if there are any items left in the cart
                if (response.itemCount === 0) {
                    document.getElementById("checkout-button").disabled = true; // Disable the checkout button
                }
            }
        };
    }
    </script>
</head>
<body>
    <?php include '../../src/components/header.php'; ?>

    <main>
        <h1>Your Cart</h1>
        
        <div class="cart-list">
        <?php if (!empty($cartItems)): ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item" id="cart-item-<?php echo $item['product_id']; ?>">
                    <img src="../../src/pictures/products/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <h2><?php echo htmlspecialchars($item['name']); ?></h2>
                    <p>Price: $<?php echo number_format($item['price'], 2); ?></p>

                    <!-- Quantity input field, triggers JS function when changed -->
                    <label for="quantity">Quantity:</label>
                    <input 
                        type="number" 
                        name="quantity" 
                        value="<?php echo $item['quantity']; ?>" 
                        min="1" 
                        max="<?php echo $item['stock']; ?>"
                        onchange="updateCart(<?php echo $item['product_id']; ?>, this.value)">
                    
                    <!-- Remove from cart -->
                    <button onclick="removeFromCart(<?php echo $item['product_id']; ?>)">Remove</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
        </div>

        <!-- Display total price -->
        <div class="cart-total" id="cart-total">
            <strong>Total:</strong> $<?php echo calculateCartTotal($cartItems); ?>
        </div>

        <!-- Checkout button -->
        <?php if (!empty($cartItems)): ?>
            <form action="../../src/pages/checkout.php" method="GET">
                <button type="submit" id="checkout-button">
                    Proceed to Checkout
                </button>
            </form>
        <?php endif; ?>
    </main>

    <?php include '../../src/components/footer.php'; ?>
</body>
</html>
