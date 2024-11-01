<?php
function registerUser($username, $email, $password, $profile_picture) {
    global $db; // Assuming you have a PDO instance for the database connection

    // Check if username or email already exists
    $query = "SELECT COUNT(*) FROM users WHERE username = :username OR email = :email";
    $stmt = $db->prepare($query);
    $stmt->execute([':username' => $username, ':email' => $email]);

    if ($stmt->fetchColumn() > 0) {
        return false; // Username or email already exists
    }

    // Insert new user
    $query = "INSERT INTO users (username, email, password, profile_picture) VALUES (:username, :email, :password, :profile_picture)";
    $stmt = $db->prepare($query);
    return $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => $password,
        ':profile_picture' => $profile_picture
    ]);
}

function loginUser($usernameOrEmail, $password) {
    global $db;

    // Check if the username or email exists
    $login_query = $db->prepare('SELECT * FROM users WHERE username = ? OR email = ?');
    $login_query->execute([$usernameOrEmail, $usernameOrEmail]);

    if ($login_query->rowCount() === 1) {
        $user = $login_query->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if (password_verify($password, $user['password'])) {
            return $user['user_id']; // Return the user ID if successful
        }
    }

    return false; // Login failed
}
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../../src/pages/login.php'); // Redirect to login.php
        exit;
    }
}

function getProducts() {
    global $db; // Access the global $db variable

    // Query to fetch all products from the products table
    $query = $db->prepare("SELECT * FROM products");
    $query->execute();
    
    // Fetch all products as an associative array
    return $query->fetchAll(PDO::FETCH_ASSOC);
}
function addToCart($userId, $productId, $quantity) {
    global $db;

    // Check if product is already in the cart
    $query = $db->prepare("SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id");
    $query->execute([':user_id' => $userId, ':product_id' => $productId]);

    if ($query->rowCount() > 0) {
        // Product is already in cart, update quantity
        $db->prepare("UPDATE cart SET quantity = quantity + :quantity WHERE user_id = :user_id AND product_id = :product_id")
           ->execute([':quantity' => $quantity, ':user_id' => $userId, ':product_id' => $productId]);
    } else {
        // Insert new product into cart with specified quantity
        $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)")
           ->execute([':user_id' => $userId, ':product_id' => $productId, ':quantity' => $quantity]);
    }
}

// Fetch all items in the cart for a given user
function getCartItems($userId) {
    global $db;
    $stmt = $db->prepare("SELECT p.product_id, p.name, p.price, p.image_url, c.quantity, p.stock
                          FROM cart c
                          JOIN products p ON c.product_id = p.product_id
                          WHERE c.user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Remove a product from the cart
function removeFromCart($userId, $productId) {
    global $db;
    $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);

}

// Update cart quantity for a specific product
function updateCartQuantity($userId, $productId, $quantity) {
    global $db;
    $stmt = $db->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$quantity, $userId, $productId]);
}

// Calculate total price of all items in the cart
function calculateCartTotal($cartItems) {
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return number_format($total, 2);
}

function createOrder($userId, $totalAmount) {
    global $db;

    // Insert the order into the orders table
    $stmt = $db->prepare("INSERT INTO orders (user_id, total_amount, created_at) VALUES (:user_id, :total_amount, NOW())");
    $stmt->execute([
        ':user_id' => $userId,
        ':total_amount' => $totalAmount
    ]);

    // Store the ID of the newly created order in the session
    $_SESSION['order_id'] = $db->lastInsertId(); // Store order ID in session

    return $_SESSION['order_id']; // Return the order ID
}


function clearCart($userId) {
    global $db;

    // Delete all items from the cart for the user
    $stmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$userId]);
}

function createOrderItem($orderId, $productId, $quantity, $price) {
    global $db;

    // Check current stock quantity
    $stmt = $db->prepare("SELECT stock FROM products WHERE product_id = :product_id");
    $stmt->execute([':product_id' => $productId]);
    $product = $stmt->fetch();

    if (!$product) {
        throw new Exception("Product not found.");
    }

    $currentStock = $product['stock'];

    // Check if sufficient stock is available
    if ($currentStock < $quantity) {
        throw new Exception("Insufficient stock for product ID: $productId");
    }

    // Deduct the quantity from the stock
    $newStock = $currentStock - $quantity;
    $stmt = $db->prepare("UPDATE products SET stock = :new_stock WHERE product_id = :product_id");
    $stmt->execute([
        ':new_stock' => $newStock,
        ':product_id' => $productId
    ]);

    // Insert each item into the order_items table
    $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)");
    $stmt->execute([
        ':order_id' => $orderId,
        ':product_id' => $productId,
        ':quantity' => $quantity,
        ':price' => $price
    ]);
}
function createPayment($orderId, $paymentMethod, $cardholderName, $cardNumber, $expiryDate, $cvv) {
    global $db; // Assuming $db is your PDO database connection
    
    // Hash or encrypt sensitive card data before storing (optional, but recommended)
    $hashedCardNumber = password_hash($cardNumber, PASSWORD_DEFAULT);

    // Prepare the SQL statement
    $sql = "INSERT INTO payments (order_id, payment_method, cardholder_name, card_number, expiry_date, cvv) 
            VALUES (:order_id, :payment_method, :cardholder_name, :card_number, :expiry_date, :cvv)";
    
    // Prepare the statement with PDO
    $stmt = $db->prepare($sql);

    // Execute the statement with the bound parameters
    $stmt->execute([
        ':order_id' => $orderId,
        ':payment_method' => $paymentMethod,
        ':cardholder_name' => $cardholderName,
        ':card_number' => $hashedCardNumber,
        ':expiry_date' => $expiryDate,
        ':cvv' => $cvv
    ]);

    // Return the ID of the inserted payment record
    return $db->lastInsertId(); // Return the payment ID
}
function getOrderDetails($orderId) {
    global $db;

    // Query to get order details
    $stmt = $db->prepare("
        SELECT oi.product_id, p.name, oi.quantity, oi.price, o.total_amount
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        JOIN orders o ON oi.order_id = o.order_id
        WHERE oi.order_id = :order_id
    ");
    $stmt->execute([':order_id' => $orderId]);

    // Fetch all items for the order
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If there are items, return the items and total amount
    if ($items) {
        // Get the total amount from the first item (assuming all items belong to the same order)
        $totalAmount = $items[0]['total_amount'];
        
        // Return items along with the total amount
        return [
            'items' => $items,
            'total_amount' => $totalAmount,
        ];
    }

    return null; // Return null if no items are found
}
function getUserDetails($userId) {
    global $db; // Assuming $db is your database connection

    // Prepare a SQL statement to fetch user details
    $query = "SELECT username, email, profile_picture FROM users WHERE user_id = :userId";
    $stmt = $db->prepare($query);
    
    // Bind the user ID parameter and execute the statement
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the user details
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function getUserOrdersWithDetails($userId) {
    global $db;
    $stmt = $db->prepare("
        SELECT o.order_id, o.total_amount, o.created_at, oi.product_id, oi.quantity, p.name, p.price 
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.product_id
        WHERE o.user_id = :user_id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
