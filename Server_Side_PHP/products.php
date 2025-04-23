<?php
session_start();
require_once 'config/database.php';

// Fetch all products from database
$stmt = $pdo->query("SELECT * FROM products ORDER BY name");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teer Brand - Premium Spices</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('../images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(40, 167, 69, 0.2), rgba(32, 201, 151, 0.2));
            animation: gradientShift 8s ease infinite;
        }
        .product-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            border-radius: 20px;
            overflow: hidden;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        .product-image-container {
            position: relative;
            width: 100%;
            padding-top: 100%;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            overflow: hidden;
        }
        .product-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 15px;
            transition: all 0.5s ease;
        }
        .product-card:hover .product-image {
            transform: scale(1.1);
        }
        .product-price {
            color: #28a745;
            font-size: 1.25rem;
            font-weight: bold;
            position: relative;
            display: inline-block;
        }
        .product-price::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #28a745;
            transition: width 0.3s ease;
        }
        .product-card:hover .product-price::after {
            width: 100%;
        }
        .btn-custom {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.2);
        }
        .btn-custom:hover::before {
            left: 100%;
        }
        .stock-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 8px 15px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            z-index: 1;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .stock-badge:hover {
            transform: scale(1.05);
        }
        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 15px 0;
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
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .card-body {
            padding: 25px;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        .card-text {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: white;
        }
        .navbar {
            background-color: rgba(0,0,0,0.8) !important;
            backdrop-filter: blur(10px);
        }
        .rating {
            color: #ffc107;
            font-size: 1.5rem;
            cursor: pointer;
        }
        .rating i {
            transition: transform 0.2s;
        }
        .rating i:hover {
            transform: scale(1.2);
        }
        .rating i.active {
            color: #ffc107;
        }
        .review-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .review-item:last-child {
            border-bottom: none;
        }
        .review-rating {
            color: #ffc107;
        }
        .review-date {
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="../index.html">
                <i class="fas fa-pepper-hot me-2"></i>Teer Brand
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="products.php">
                            <i class="fas fa-store me-1"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
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
                    <li class="nav-item">
                        <a class="nav-link" href="contact_form.php">
                            <i class="fas fa-envelope me-1"></i> Get in Touch
                        </a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Premium Spices</h1>
            <p class="lead">Discover the finest quality spices from around the world</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-4">
            <?php foreach($products as $index => $product): ?>
                <div class="col-md-4">
                    <div class="product-card card h-100">
                        <div class="position-relative">
                            <div class="product-image-container">
                                <img src="<?php echo str_replace('images/', '../images/', htmlspecialchars($product['image_url'])); ?>" 
                                     class="product-image" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </div>
                            <?php if($product['stock'] > 0): ?>
                                <span class="stock-badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>In Stock
                                </span>
                            <?php else: ?>
                                <span class="stock-badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i>Out of Stock
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title h5 mb-3"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="card-text text-muted mb-3"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="product-price mb-3">₹<?php echo number_format($product['price'], 0); ?></p>
                            
                            <?php if($product['stock'] > 0): ?>
                                <div class="input-group mb-3">
                                    <button class="btn btn-outline-secondary quantity-control" 
                                            type="button" 
                                            onclick="updateQuantity(<?php echo $product['product_id']; ?>, 'decrease')">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" 
                                           class="form-control text-center" 
                                           id="quantity_<?php echo $product['product_id']; ?>" 
                                           value="1" 
                                           min="1" 
                                           max="<?php echo $product['stock']; ?>">
                                    <button class="btn btn-outline-secondary quantity-control" 
                                            type="button" 
                                            onclick="updateQuantity(<?php echo $product['product_id']; ?>, 'increase')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <button class="btn btn-custom text-white w-100" 
                                        onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                </button>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-times-circle me-2"></i>Out of Stock
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-outline-primary w-100 mt-2" 
                                    onclick="showReviews(<?php echo $product['product_id']; ?>)">
                                <i class="fas fa-star me-2"></i>Reviews
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Product Reviews</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="reviewsList" class="mb-4">
                        <!-- Reviews will be loaded here -->
                    </div>
                    <form id="reviewForm">
                        <input type="hidden" id="reviewProductId">
                        <div class="mb-3">
                            <label for="reviewerName" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="reviewerName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Your Rating</label>
                            <div class="rating">
                                <i class="fas fa-star" data-rating="1"></i>
                                <i class="fas fa-star" data-rating="2"></i>
                                <i class="fas fa-star" data-rating="3"></i>
                                <i class="fas fa-star" data-rating="4"></i>
                                <i class="fas fa-star" data-rating="5"></i>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="reviewComment" class="form-label">Your Review</label>
                            <textarea class="form-control" id="reviewComment" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>
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
            const products = <?php echo json_encode($products); ?>;
            const product = products.find(p => p.product_id == productId);
            
            if (!product) {
                console.error('Product not found:', productId);
                $('#errorToastBody').text('Error: Product not found');
                $('#errorToast').toast('show');
                return;
            }
            
            $.ajax({
                url: 'ajax/add_to_cart.php',
                method: 'POST',
                data: {
                    product_id: product.product_id,
                    quantity: quantity,
                    name: product.name,
                    price: product.price
                },
                success: function(response) {
                    try {
                        let result;
                        if (typeof response === 'string') {
                            result = JSON.parse(response);
                        } else {
                            result = response;
                        }
                        
                        if (result.success) {
                            $('#successToastBody').text('Product added to cart successfully!');
                            $('#successToast').toast('show');
                        } else {
                            console.error('Server error:', result.error);
                            $('#errorToastBody').text(result.error || 'Error adding product to cart.');
                            $('#errorToast').toast('show');
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        console.error('Raw response:', response);
                        $('#errorToastBody').text('Error adding product to cart. Please try again.');
                        $('#errorToast').toast('show');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    $('#errorToastBody').text('Error adding product to cart. Please try again.');
                    $('#errorToast').toast('show');
                }
            });
        }

        function showReviews(productId) {
            $('#reviewProductId').val(productId);
            $('#reviewModal').modal('show');
            loadReviews(productId);
        }

        function loadReviews(productId) {
            $.ajax({
                url: 'ajax/get_reviews.php',
                method: 'GET',
                data: { product_id: productId },
                success: function(response) {
                    try {
                        let result;
                        if (typeof response === 'string') {
                            result = JSON.parse(response);
                        } else {
                            result = response;
                        }
                        
                        let html = '';
                        if (!result.success) {
                            html = `<p class="text-danger">${result.error || 'Error loading reviews'}</p>`;
                        } else if (!result.reviews || result.reviews.length === 0) {
                            html = '<p class="text-muted">No reviews yet. Be the first to review!</p>';
                        } else {
                            result.reviews.forEach(review => {
                                html += `
                                    <div class="review-item">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong>${review.reviewer_name}</strong>
                                            <div class="review-rating">
                                                ${'<i class="fas fa-star"></i>'.repeat(review.rating)}
                                                ${'<i class="far fa-star"></i>'.repeat(5 - review.rating)}
                                            </div>
                                        </div>
                                        <p class="mb-1">${review.comment}</p>
                                        <small class="review-date">${new Date(review.created_at).toLocaleDateString()}</small>
                                    </div>
                                `;
                            });
                        }
                        $('#reviewsList').html(html);
                    } catch (e) {
                        console.error('Error parsing reviews:', e);
                        console.error('Raw response:', response);
                        $('#reviewsList').html('<p class="text-danger">Error loading reviews. Please try again.</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    $('#reviewsList').html('<p class="text-danger">Error loading reviews. Please try again.</p>');
                }
            });
        }

        // Rating stars interaction
        $('.rating i').click(function() {
            const rating = $(this).data('rating');
            $('.rating i').removeClass('active');
            $('.rating i').each(function(index) {
                if (index < rating) {
                    $(this).addClass('active');
                }
            });
        });

        // Submit review
        $('#reviewForm').submit(function(e) {
            e.preventDefault();
            const productId = $('#reviewProductId').val();
            const rating = $('.rating i.active').length;
            const comment = $('#reviewComment').val();
            const reviewerName = $('#reviewerName').val();

            console.log('Submitting review:', {
                productId,
                rating,
                comment,
                reviewerName
            });

            if (rating === 0) {
                $('#errorToastBody').text('Please select a rating');
                $('#errorToast').toast('show');
                return;
            }

            if (!reviewerName.trim()) {
                $('#errorToastBody').text('Please enter your name');
                $('#errorToast').toast('show');
                return;
            }

            if (!comment.trim()) {
                $('#errorToastBody').text('Please enter your review');
                $('#errorToast').toast('show');
                return;
            }

            $.ajax({
                url: 'ajax/submit_review.php',
                method: 'POST',
                data: {
                    product_id: productId,
                    rating: rating,
                    comment: comment,
                    reviewer_name: reviewerName
                },
                success: function(response) {
                    console.log('Server response:', response);
                    try {
                        let result;
                        if (typeof response === 'string') {
                            result = JSON.parse(response);
                        } else {
                            result = response;
                        }
                        
                        if (result.success) {
                            loadReviews(productId);
                            $('#reviewComment').val('');
                            $('#reviewerName').val('');
                            $('.rating i').removeClass('active');
                            $('#successToastBody').text('Review submitted successfully!');
                            $('#successToast').toast('show');
                        } else {
                            $('#errorToastBody').text(result.error || 'Error submitting review.');
                            $('#errorToast').toast('show');
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        console.error('Raw response:', response);
                        $('#errorToastBody').text('Error submitting review. Please try again.');
                        $('#errorToast').toast('show');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    $('#errorToastBody').text('Error submitting review. Please try again.');
                    $('#errorToast').toast('show');
                }
            });
        });
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