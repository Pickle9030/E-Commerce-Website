<?php
// Check if session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<head>
    <!-- Other head elements -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../src/styles/header.css">
    <script>
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
            // Update total price
            document.getElementById("cart-total").textContent = 'Total: $' + response.totalPrice;
            // Update item count
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
        }
    };
}
</script>
</head>
<header class="header">
    <div class="logo">
        <a href="../../src/pages/index.php">Online Store</a>
    </div>
    <nav class="cart-profile">
        <div class="cart">
            <a href="cart.php" class="cart-pic">
                <i class="fas fa-shopping-cart"></i> <!-- Font Awesome cart icon -->
                <div class="cart-badge" id="cart-count">
                    <?php
                        $userId = $_SESSION['user_id'] ?? null;
                        if ($userId) {
                            $cartItems = getCartItems($userId);
                            $totalItems = array_sum(array_column($cartItems, 'quantity'));
                            echo $totalItems;
                        } else {
                            echo 0;
                        }
                    ?>
                </div>
            </a>
        </div>
        <div class="profile">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                    // Fetch user data to get profile picture
                    $user_id = $_SESSION['user_id'];
                    $query = "SELECT profile_picture FROM users WHERE user_id = :user_id";
                    $stmt = $db->prepare($query);
                    $stmt->execute([':user_id' => $_SESSION['user_id']]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Use the relative path from the database to display the image
                    $profile_picture = $user['profile_picture'] ?? '../../src/pictures/users/default-profile.png';
                ?>
                <img src="<?php echo $profile_picture; ?>" alt="Profile Picture">
            <?php else: ?>
                <img class="profile-pic" src="../../src/pictures/users/default-profile.png" alt="Default Profile Picture">
            <?php endif; ?>

            <div class="dropdown">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="../../src/pages/profile.php">Profile</a>
                    <a href="../../src/pages/logout.php">Logout</a>
                <?php else: ?>
                    <a href="../../src/pages/login.php">Login</a>
                    <a href="../../src/pages/register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>
