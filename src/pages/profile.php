<?php
require_once '../../private/required.php'; // Include necessary files

// Ensure user is logged in
requireLogin();

$userId = $_SESSION['user_id']; // Get the logged-in user's ID

// Fetch user details (username, email, profile picture)
$userDetails = getUserDetails($userId);

// Fetch past orders and their details for the user
$orders = getUserOrdersWithDetails($userId); // Updated function to get orders with item details

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link rel="stylesheet" href="../../src/styles/style.css">
</head>
<body>
    <?php include '../../src/components/header.php'; ?>

    <main>
        <h1>Your Profile</h1>
        
        <!-- User Information -->
        <div class="user-info">
            <?php if (!empty($userDetails)): ?>
                
                <h2>Your Information</h2>
                <img src="<?php echo htmlspecialchars($userDetails['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
                <p>Email: <?php echo htmlspecialchars($userDetails['email']); ?></p>
                <p>Username: <?php echo htmlspecialchars($userDetails['username']); ?></p>
            <?php else: ?>
                <p>User information not available.</p>
            <?php endif; ?>
        </div>

        <!-- Past Orders -->
        <h2>Your Past Orders</h2>
        <?php if (!empty($orders)): ?>
            <?php 
                // Group orders by order ID
                $groupedOrders = [];
                foreach ($orders as $order) {
                    $groupedOrders[$order['order_id']][] = $order;
                }
            ?>
            <?php foreach ($groupedOrders as $orderId => $orderDetails): ?>
                <div class="order">
                    <h3>Order ID: <?php echo $orderId; ?></h3>
                    <p>Total Amount: $<?php echo number_format($orderDetails[0]['total_amount'], 2); ?></p>
                    <p>Date: <?php echo date('Y-m-d', strtotime($orderDetails[0]['created_at'])); ?></p>
                    <h4>Order Items:</h4>
                    <ul>
                        <?php foreach ($orderDetails as $item): ?>
                            <li>
                                <?php echo htmlspecialchars($item['name']); ?> - 
                                Quantity: <?php echo $item['quantity']; ?> - 
                                Price: $<?php echo number_format($item['price'], 2); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have no past orders.</p>
        <?php endif; ?>
    </main>

    <?php include '../../src/components/footer.php'; ?>
</body>
</html>
