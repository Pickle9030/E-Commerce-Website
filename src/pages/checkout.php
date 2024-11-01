<?php
require_once '../../private/required.php'; // Include necessary files

// Ensure user is logged in
requireLogin();

// Fetch cart items for the logged-in user
$userId = $_SESSION['user_id'];
$cartItems = getCartItems($userId);

// If no items in the cart, redirect to cart page
if (empty($cartItems)) {
    header('Location: cart.php');
    exit();
}

// Calculate total amount from the cart items
$totalAmount = calculateCartTotal($cartItems);

// Handle form submission for payment and order creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // This section will only be executed when the form is submitted (POST request)
    $paymentMethod = $_POST['payment_method'] ?? null;
    $cardholderName = $_POST['cardholder_name'] ?? null;
    $cardNumber = $_POST['card_number'] ?? null;
    $expiryDate = $_POST['expiry_date'] ?? null;
    $cvv = $_POST['cvv'] ?? null;

    if ($paymentMethod === 'credit_card') {
        // Simple validation for card details
        if (empty($cardholderName) || empty($cardNumber) || empty($expiryDate) || empty($cvv)) {
            echo "Please fill in all the card details.";
            exit();  // Stop further execution if validation fails
        }

        // Proceed to create order after successful payment
        $orderId = createOrder($userId, $totalAmount);

        // Insert payment details into the payment table
        $paymentId = createPayment($orderId, $paymentMethod, $cardholderName, $cardNumber, $expiryDate, $cvv);

        // Insert order items
        foreach ($cartItems as $item) {
            createOrderItem($orderId, $item['product_id'], $item['quantity'], $item['price']);
        }

        // Clear cart
        clearCart($userId);

        // Redirect to success page
        header('Location: order_confirmation.php');
        exit();
    } else {
        // Handle other payment methods (PayPal or Bank Transfer)
        // Proceed to create order without card details
        $orderId = createOrder($userId, $totalAmount);
        createPayment($orderId, $paymentMethod, null, null, null, null);

        foreach ($cartItems as $item) {
            createOrderItem($orderId, $item['product_id'], $item['quantity'], $item['price']);
        }

        clearCart($userId);
        header('Location: order_confirmation.php');
        exit();
    }
} else {
    // This section will be executed for the GET request (initial page load)
    // No POST data yet, just show the form
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="../../src/styles/style.css">
    <link rel="stylesheet" href="../../src/styles/header.css">
    <link rel="stylesheet" href="../../src/styles/footer.css">
</head>
<body>
    <?php include '../../src/components/header.php'; ?>

    <main class="checkout-container">
        <h1>Checkout</h1>
        <h2>Your Cart Items:</h2>

        <div class="checkout-list">
            <?php foreach ($cartItems as $item): ?>
                <div class="checkout-item">
                    <img src="../../src/pictures/products/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <h2><?php echo htmlspecialchars($item['name']); ?></h2>
                    <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
                    <p>Quantity: <?php echo $item['quantity']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="checkout-total">
            <strong>Total Amount:</strong> $<?php echo number_format($totalAmount, 2); ?>
        </div>

        <form action="checkout.php" method="POST" class="checkout-form">
            <h2>Payment Information</h2>

            <!-- Credit Card Fields -->
            <div class="credit-card-fields">
            <p><label for="cardholder_name">Cardholder Name:</label>
                <input type="text" id="cardholder_name" name="cardholder_name"></p>
                
                <p><label for="card_number">Card Number:</label>
                <input type="text" id="card_number" name="card_number"></p>

                <p><label for="expiry_date">Expiry Date:</label>
                <input type="text" id="expiry_date" name="expiry_date"></p>

                <p><label for="cvv">CVV:</label>
                <input type="text" id="cvv" name="cvv"></p>
            </div>

            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="credit_card">Credit Card</option>
                <option value="paypal">PayPal</option>
                <option value="bank_transfer">Bank Transfer</option>
            </select>

            <br>
            <button type="submit" class="btn-confirm-order">Confirm Order</button>
        </form>
    </main>

    <?php include '../../src/components/footer.php'; ?>
</body>

</html>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const paymentMethodSelect = document.getElementById('payment_method');
        const cardFields = document.querySelector('.credit-card-fields');
        
        // Function to toggle card fields visibility based on payment method
        function toggleCardFields() {
            if (paymentMethodSelect.value === 'credit_card') {
                cardFields.style.display = 'block';
            } else {
                cardFields.style.display = 'none';
            }
        }

        // Initialize visibility based on default selection
        toggleCardFields();

        // Add event listener to toggle visibility when payment method changes
        paymentMethodSelect.addEventListener('change', toggleCardFields);
    });
</script>
