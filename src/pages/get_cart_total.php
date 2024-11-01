<?php
require_once '../../private/required.php'; // Include your necessary files

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $cartItems = getCartItems($userId); // Assuming this function fetches all cart items

    // Calculate total number of items in the cart
    $totalItems = 0;
    foreach ($cartItems as $item) {
        $totalItems += $item['quantity'];
    }

    // Output the total number of items (as a string to be returned by the AJAX request)
    echo $totalItems;
} else {
    // If the user is not logged in, return 0
    echo 0;
}
?>
