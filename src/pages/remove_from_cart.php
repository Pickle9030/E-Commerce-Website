<?php
require_once '../../private/required.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);
    $userId = $_SESSION['user_id'];

    if ($userId) {
        // Remove the product from the cart
        removeFromCart($userId, $productId);

        // Fetch updated cart items
        $cartItems = getCartItems($userId);

        // Calculate total price and item count
        $totalPrice = calculateCartTotal($cartItems);
        $itemCount = 0;
        foreach ($cartItems as $item) {
            $itemCount += $item['quantity'];
        }

        // Return the updated total price and item count
        echo json_encode([
    'totalPrice' => calculateCartTotal(getCartItems($userId)), // Assuming you have this function
    'itemCount' => count(getCartItems($userId)) // Count of items in the cart
]);
    }
}
