<?php
require_once 'config/database.php';

// Fetch cart items with product details
$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.price, p.image_url, p.stock 
    FROM cart c 
    JOIN products p ON c.product_id = p.product_id
");
$stmt->execute();
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Teer Brand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .navbar {
            background-color: rgba(0,0,0,0.8) !important;
            backdrop-filter: blur(10px);
        }
        .cart-container {
            animation: fadeIn 0.5s ease-out;
        }
        .cart-item {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: slideIn 0.5s ease-out;
            background: white;
        }
        .cart-item:hover {
            transform: translateY(-5px) scale(1.01);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .cart-image {
            width: 120px;
            height: 120px;
            object-fit: contain;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 15px;
            border-radius: 15px;
            transition: all 0.4s ease;
        }
        .cart-item:hover .cart-image {
            transform: scale(1.1) rotate(2deg);
        }
        .quantity-badge {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 30px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);
            transition: all 0.3s ease;
        }
        .quantity-badge:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(40, 167, 69, 0.3);
        }
        .remove-btn {
            transition: all 0.3s ease;
            border-radius: 30px;
            padding: 8px 15px;
            background: #f8f9fa;
            color: #dc3545;
            border: 2px solid #dc3545;
        }
        .remove-btn:hover {
            background: #dc3545;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.2);
        }
        .cart-summary {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            position: sticky;
            top: 100px;
            animation: slideInRight 0.5s ease-out;
            border: 1px solid rgba(0,0,0,0.1);
        }
        .checkout-btn {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            padding: 1rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .checkout-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        .checkout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.2);
        }
        .checkout-btn:hover::before {
            left: 100%;
        }
        .empty-cart {
            text-align: center;
            padding: 4rem;
            animation: fadeIn 0.5s ease-out;
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .empty-cart i {
            font-size: 6rem;
            color: #dee2e6;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
        }
        .empty-cart h3 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .empty-cart p {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .empty-cart .btn {
            padding: 0.8rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .empty-cart .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from {
                transform: translateX(-20px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideInRight {
            from {
                transform: translateX(20px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        .quantity-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 2px solid #28a745;
            background: white;
            color: #28a745;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .quantity-btn:hover {
            background: #28a745;
            color: white;
            transform: scale(1.1);
        }
        .quantity-input {
            width: 60px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .quantity-input:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .cart-summary h4 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }
        .cart-summary .d-flex {
            margin-bottom: 1rem;
            color: #6c757d;
        }
        .cart-summary .fw-bold {
            color: #28a745;
            font-size: 1.1rem;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-pepper-hot me-2"></i>Teer Brand
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">
                            <i class="fas fa-store me-1"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cart.php">
                            <i class="fas fa-shopping-cart me-1"></i> Cart
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="saved_items.php">
                            <i class="fas fa-bookmark me-1"></i> Saved Items
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="order_history.php">
                            <i class="fas fa-history me-1"></i> Order History
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 100px;">
        <div class="cart-container">
            <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your cart is empty</h3>
                    <p class="text-muted">Add some products to your cart and they will appear here</p>
                    <a href="products.php" class="btn btn-primary mt-3">
                        <i class="fas fa-store me-2"></i>Continue Shopping
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-lg-8">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item bg-white p-3" data-item-id="<?php echo $item['cart_id']; ?>">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <img src="<?php echo str_replace('images/', '../images/', htmlspecialchars($item['image_url'])); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             class="cart-image">
                                    </div>
                                    <div class="col">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h5>
                                        <p class="text-success mb-1">₹<?php echo number_format($item['price'], 0); ?></p>
                                        <div class="quantity-control">
                                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 'decrease')">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" 
                                                   class="quantity-input" 
                                                   value="<?php echo $item['quantity']; ?>"
                                                   min="1"
                                                   max="<?php echo $item['stock']; ?>"
                                                   onchange="updateQuantity(<?php echo $item['cart_id']; ?>, 'input', this.value)">
                                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 'increase')">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn remove-btn" onclick="removeItem(<?php echo $item['cart_id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button class="btn btn-outline-success mt-2" onclick="saveForLater(<?php echo $item['cart_id']; ?>, <?php echo $item['product_id']; ?>, <?php echo $item['quantity']; ?>)">
                                            <i class="fas fa-bookmark me-2"></i>Save for Later
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-lg-4">
                        <div class="cart-summary">
                            <h4 class="mb-4">Cart Summary</h4>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotal</span>
                                <span class="fw-bold">₹<?php echo number_format($total, 0); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Shipping</span>
                                <span class="text-success">Free</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold text-success">₹<?php echo number_format($total, 0); ?></span>
                            </div>
                            <a href="checkout.php" class="btn checkout-btn text-white w-100">
                                <i class="fas fa-lock me-2"></i>Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateQuantity(cartId, action, value = null) {
            let newQuantity;
            const input = document.querySelector(`[data-item-id="${cartId}"] .quantity-input`);
            const currentQuantity = parseInt(input.value);
            const maxQuantity = parseInt(input.max);

            if (action === 'increase') {
                newQuantity = Math.min(currentQuantity + 1, maxQuantity);
            } else if (action === 'decrease') {
                newQuantity = Math.max(currentQuantity - 1, 1);
            } else {
                newQuantity = Math.min(Math.max(parseInt(value) || 1, 1), maxQuantity);
            }

            $.ajax({
                url: 'ajax/update_cart.php',
                method: 'POST',
                data: {
                    cart_id: cartId,
                    quantity: newQuantity
                },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.success) {
                            location.reload();
                        } else {
                            alert(result.error || 'Error updating quantity');
                        }
                    } catch (e) {
                        alert('Error updating quantity');
                    }
                },
                error: function() {
                    alert('Error updating quantity');
                }
            });
        }

        function removeItem(cartId) {
            if (confirm('Are you sure you want to remove this item?')) {
                const item = document.querySelector(`[data-item-id="${cartId}"]`);
                item.style.transform = 'translateX(-100%)';
                item.style.opacity = '0';

                setTimeout(() => {
                    $.ajax({
                        url: 'ajax/remove_from_cart.php',
                        method: 'POST',
                        data: { cart_id: cartId },
                        success: function(response) {
                            try {
                                const result = JSON.parse(response);
                                if (result.success) {
                                    location.reload();
                                } else {
                                    alert(result.error || 'Error removing item');
                                }
                            } catch (e) {
                                alert('Error removing item');
                            }
                        },
                        error: function() {
                            alert('Error removing item');
                        }
                    });
                }, 300);
            }
        }

        function saveForLater(cartId, productId, quantity) {
            fetch('ajax/save_for_later.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'save',
                    product_id: productId,
                    quantity: quantity,
                    cart_id: cartId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove item from cart display
                    document.querySelector(`[data-item-id="${cartId}"]`).remove();
                    updateCartTotal();
                    
                    document.getElementById('successToastBody').textContent = data.message;
                    bootstrap.Toast.getInstance(document.getElementById('successToast')).show();
                    
                    // Refresh if cart is empty
                    if (document.querySelectorAll('.cart-item').length === 0) {
                        location.reload();
                    }
                } else {
                    document.getElementById('errorToastBody').textContent = data.message;
                    bootstrap.Toast.getInstance(document.getElementById('errorToast')).show();
                }
            })
            .catch(error => {
                document.getElementById('errorToastBody').textContent = 'Error saving item for later';
                bootstrap.Toast.getInstance(document.getElementById('errorToast')).show();
            });
        }
    </script>
</body>
</html> 