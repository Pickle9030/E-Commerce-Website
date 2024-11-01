<?php
require_once '../../private/required.php'; // Updated path for required.php
requireLogin(); // Make sure user is logged in

$products = getProducts(); // Fetch products from the database
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="../../src/styles/style.css">
    <script>
    // JavaScript function to handle adding a product to the cart
    function addToCart(productId) {
    const quantityInput = document.getElementById(`quantity_${productId}`);
    const quantity = quantityInput.value;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "add_to_cart.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Send AJAX request with product ID and quantity
    xhr.send(`product_id=${productId}&quantity=${quantity}`);

    // Handle the response from the server
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            // Update cart total items in the header
            document.getElementById("cart-count").textContent = response.itemCount;

            // Optionally, you can also update cart total price if displayed
            // document.getElementById("cart-total").textContent = response.totalPrice;
        }
    };
}
function filterProducts() {
    var input = document.getElementById('searchInput').value.toLowerCase();
    var filter = document.getElementById('categoryFilter').value.toLowerCase();
    var productList = document.getElementById('productList');  // Target productList by id
    var products = productList.getElementsByClassName('product');  // Get all products

    for (var i = 0; i < products.length; i++) {
        var productName = products[i].getAttribute('data-name').toLowerCase();
        var productCategory = products[i].getAttribute('data-category').toLowerCase();

        // Check both the search input and the selected category
        if ((filter === 'all' || productCategory === filter) && productName.includes(input)) {
            products[i].style.display = '';  // Show matching product
        } else {
            products[i].style.display = 'none';  // Hide non-matching product
        }
    }
}
</script>
</head>
<body>
    <?php include '../../src/components/header.php'; ?>
    
    <main>
    <h1>Products</h1>
    <div class="search-section">
        <!-- Search Bar -->
        <input type="text" id="searchInput" placeholder="Search products..." oninput="filterProducts()">
        
    </div>
    <div class="filter-section">
        <!-- Filter Dropdown -->
        <select id="categoryFilter" onchange="filterProducts()">
            <option value="all">All Categories</option>
            <option value="supplements">Supplements</option>
            <option value="gear">Gear</option>
            <option value="clothes">Clothes</option>
        </select>
    </div>
    
    <div class="product-list" id="productList"> <!-- Add id here for JavaScript to target -->
        <?php foreach ($products as $product): ?>
        <div class="product" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-category="<?php echo htmlspecialchars($product['category']); ?>"> <!-- Add data attributes -->
            <img src="../../src/pictures/products/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
            <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>

            <?php if ($product['stock'] > 0): ?>
            <div>
                <p class="quantity">In Stock: <?php echo $product['stock']; ?></p>
                <input type="number" id="quantity_<?php echo $product['product_id']; ?>" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                <button type="button" onclick="addToCart(<?php echo $product['product_id']; ?>)">Add to Cart</button>
            </div>
            <?php else: ?>
            <p class="out-of-stock">Out of Stock</p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</main>
    <?php include '../../src/components/footer.php'; ?>
</body>

</html>
