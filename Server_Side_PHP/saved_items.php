<?php
require_once 'config/database.php';

// Fetch saved items with product details
$stmt = $pdo->query("
    SELECT s.*, p.name, p.price, p.image_url, p.description, p.stock
    FROM saved_items s
    JOIN products p ON s.product_id = p.product_id
    ORDER BY s.date_added DESC
");
$savedItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Items - Teer Brand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .navbar {
            background-color: rgba(0,0,0,0.8) !important;
            backdrop-filter: blur(10px);
            padding: 0.5rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        .navbar .container {
            max-width: 1140px;
            padding-left: 1rem;
            padding-right: 1rem;
            margin: 0 auto;
        }
        .saved-item {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            background: white;
            animation: slideIn 0.5s ease-out;
        }
        .saved-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .saved-item-image {
            width: 120px;
            height: 120px;
            object-fit: contain;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 15px;
            border-radius: 15px;
            transition: all 0.3s ease;
        }
        .saved-item:hover .saved-item-image {
            transform: scale(1.1) rotate(2deg);
        }
        .btn-move-to-cart {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            color: white;
        }
        .btn-move-to-cart:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.2);
            color: white;
        }
        .btn-remove {
            background: transparent;
            border: 2px solid #dc3545;
            color: #dc3545;
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-remove:hover {
            background: #dc3545;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(220, 53, 69, 0.2);
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease-out;
        }
        .empty-state i {
            font-size: 4rem;
            color: #20c997;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
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
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .price {
            font-size: 1.25rem;
            font-weight: 600;
            color: #28a745;
        }
        .stock-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="products.php">
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
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart me-1"></i> Cart
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="saved_items.php">
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
        <h2 class="mb-4">
            <i class="fas fa-bookmark me-2"></i>Saved Items
        </h2>

        <?php if (empty($savedItems)): ?>
            <div class="empty-state">
                <i class="fas fa-bookmark"></i>
                <h3>No Saved Items</h3>
                <p class="text-muted mb-4">Items you save for later will appear here</p>
                <a href="products.php" class="btn btn-move-to-cart">
                    <i class="fas fa-store me-2"></i>Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="saved-items-container">
                <?php foreach($savedItems as $item): ?>
                    <div class="saved-item p-4" data-product-id="<?php echo $item['product_id']; ?>">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <img src="<?php echo str_replace('images/', '../images/', htmlspecialchars($item['image_url'])); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     class="saved-item-image">
                            </div>
                            <div class="col">
                                <h4 class="mb-2"><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p class="text-muted mb-2"><?php echo htmlspecialchars($item['description']); ?></p>
                                <div class="price mb-2">₹<?php echo number_format($item['price'], 0); ?></div>
                                <span class="stock-badge bg-<?php echo $item['stock'] > 0 ? 'success' : 'danger'; ?>">
                                    <?php echo $item['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                </span>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex flex-column gap-2">
                                    <button class="btn btn-move-to-cart" onclick="moveToCart(<?php echo $item['product_id']; ?>)">
                                        <i class="fas fa-shopping-cart me-2"></i>Move to Cart
                                    </button>
                                    <button class="btn btn-remove" onclick="removeItem(<?php echo $item['product_id']; ?>)">
                                        <i class="fas fa-trash me-2"></i>Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Toast notifications -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="successToastMessage"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <span id="errorToastMessage"></span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize toasts
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl);
            });
        });

        function moveToCart(productId) {
            fetch('ajax/save_for_later.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'move_to_cart',
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`[data-product-id="${productId}"]`).remove();
                    document.getElementById('successToastMessage').textContent = data.message;
                    bootstrap.Toast.getInstance(document.getElementById('successToast')).show();
                    
                    // Refresh the page if no items left
                    if (document.querySelectorAll('.saved-item').length === 0) {
                        location.reload();
                    }
                } else {
                    document.getElementById('errorToastMessage').textContent = data.message;
                    bootstrap.Toast.getInstance(document.getElementById('errorToast')).show();
                }
            })
            .catch(error => {
                document.getElementById('errorToastMessage').textContent = 'Error moving item to cart';
                bootstrap.Toast.getInstance(document.getElementById('errorToast')).show();
            });
        }

        function removeItem(productId) {
            fetch('ajax/save_for_later.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'remove',
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`[data-product-id="${productId}"]`).remove();
                    document.getElementById('successToastMessage').textContent = data.message;
                    bootstrap.Toast.getInstance(document.getElementById('successToast')).show();
                    
                    // Refresh the page if no items left
                    if (document.querySelectorAll('.saved-item').length === 0) {
                        location.reload();
                    }
                } else {
                    document.getElementById('errorToastMessage').textContent = data.message;
                    bootstrap.Toast.getInstance(document.getElementById('errorToast')).show();
                }
            })
            .catch(error => {
                document.getElementById('errorToastMessage').textContent = 'Error removing item';
                bootstrap.Toast.getInstance(document.getElementById('errorToast')).show();
            });
        }
    </script>
</body>
</html> 