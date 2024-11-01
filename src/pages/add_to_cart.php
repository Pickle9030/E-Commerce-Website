<?php
require_once '../../private/required.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $userId = $_SESSION['user_id'];

    if ($userId) {
        // Add product to cart or update quantity if it already exists
        addToCart($userId, $productId, $quantity);

        // Fetch updated cart items
        $cartItems = getCartItems($userId);

        // Calculate total price and item count
        $totalPrice = calculateCartTotal($cartItems);
        $itemCount = 0;
        foreach ($cartItems as $item) {
            $itemCount += $item['quantity'];
        }

        // Return the updated total price and item count
        echo json_encode(['totalPrice' => $totalPrice, 'itemCount' => $itemCount]);
    }
}
