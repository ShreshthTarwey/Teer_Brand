<?php
require_once 'config/database.php';

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teer Brand - Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Teer Brand</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="products.php">
                            <i class="fas fa-store"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart"></i> Cart
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="order_history.php">
                            <i class="fas fa-history"></i> Order History
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <h1 class="text-center mb-4">Our Premium Spices</h1>
        
        <div class="row g-4">
            <?php foreach($products as $product): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             style="height: 200px; object-fit: cover;">
                        
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title h5"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="card-text fw-bold text-primary mb-3">$<?php echo number_format($product['price'], 2); ?></p>
                            
                            <?php if($product['stock'] > 0): ?>
                                <div class="input-group mb-3">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(<?php echo $product['product_id']; ?>, 'decrease')">-</button>
                                    <input type="number" 
                                           class="form-control text-center" 
                                           id="quantity_<?php echo $product['product_id']; ?>" 
                                           value="1" 
                                           min="1" 
                                           max="<?php echo $product['stock']; ?>">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(<?php echo $product['product_id']; ?>, 'increase')">+</button>
                                </div>
                                <button class="btn btn-primary w-100" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                </button>
                                <small class="text-success mt-2">
                                    <i class="fas fa-check-circle"></i> In stock: <?php echo $product['stock']; ?> units
                                </small>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-times-circle me-2"></i>Out of Stock
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize all toasts when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl);
            });
        });

        function updateQuantity(productId, action) {
            const input = document.getElementById(`quantity_${productId}`);
            let value = parseInt(input.value);
            
            if (action === 'increase') {
                value = Math.min(value + 1, parseInt(input.max));
            } else {
                value = Math.max(value - 1, 1);
            }
            
            input.value = value;
        }

        function addToCart(productId) {
            const quantity = document.getElementById(`quantity_${productId}`).value;
            
            $.ajax({
                url: 'ajax/add_to_cart.php',
                method: 'POST',
                data: {
                    product_id: productId,
                    quantity: quantity
                },
                success: function(response) {
                    try {
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        
                        if (response.success) {
                            $('#successToastBody').text('Product added to cart successfully!');
                            $('#successToast').toast('show');
                        } else {
                            $('#errorToastBody').text(response.error || 'Error adding product to cart.');
                            $('#errorToast').toast('show');
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        $('#errorToastBody').text('Error adding product to cart. Please try again.');
                        $('#errorToast').toast('show');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    $('#errorToastBody').text('Error adding product to cart. Please try again.');
                    $('#errorToast').toast('show');
                }
            });
        }
    </script>

    <!-- Toast Notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <!-- Success Toast -->
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="successToastBody">
                    Product added to cart successfully!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        
        <!-- Error Toast -->
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="errorToastBody">
                    Error adding product to cart.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
</body>
</html> 