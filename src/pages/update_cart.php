<?php
require_once '../../private/required.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $userId = $_SESSION['user_id'];

    if ($userId) {
        // Update cart quantity
        updateCartQuantity($userId, $productId, $quantity);

        // Fetch updated cart items
        $cartItems = getCartItems($userId);
        
        // Calculate the new total price
        $totalPrice = calculateCartTotal($cartItems);
        
        // Calculate the total number of items (sum of quantities)
        $itemCount = 0;
        foreach ($cartItems as $item) {
            $itemCount += $item['quantity'];
        }

        // Return JSON response with total price and total item count
        echo json_encode(['totalPrice' => $totalPrice, 'itemCount' => $itemCount]);
    }
}