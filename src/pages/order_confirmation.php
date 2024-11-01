<?php
require_once '../../private/required.php'; // Include necessary files
requireLogin(); // Ensure the user is logged in

// Get the order ID from the session
$orderId = $_SESSION['order_id']; // Assuming you store the order ID in the session after placing the order

// Fetch order details from the database
$orderDetails = getOrderDetails($orderId); // Create this function to get order details

if (!$orderDetails) {
    // Handle case where order details cannot be found
    echo "Order not found.";
    exit;
}

// Extract items and total amount
$items = $orderDetails['items'];
$totalAmount = $orderDetails['total_amount'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="../../src/styles/style.css">
</head>
<body>
    <?php include '../../src/components/header.php'; ?>

    <main>
        <h1>Order Confirmation</h1>
        <p>Thank you for your order! Your order has been successfully placed.</p>

        <h2>Order Details</h2>
        <ul>
            <?php foreach ($items as $item): ?>
                <li><?php echo htmlspecialchars($item['name']); ?> - Quantity: <?php echo $item['quantity']; ?> - Price: $<?php echo number_format($item['price'], 2); ?></li>
            <?php endforeach; ?>
        </ul>

        <p><strong>Total Amount: $<?php echo number_format($totalAmount, 2); ?></strong></p>

        <a href="profile.php">View Your Orders</a>
    </main>

    <?php include '../../src/components/footer.php'; ?>
</body>
</html>