<?php
require_once 'config/database.php';

// Fetch all orders with their items count
$stmt = $pdo->query("
    SELECT o.*, 
           COUNT(oi.order_item_id) as total_items,
           GROUP_CONCAT(p.name) as product_names
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.product_id
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teer Brand - Order History</title>
    <link rel="stylesheet" href="css/style.css">
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
        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .navbar-brand:hover {
            color: rgba(255,255,255,0.9);
        }
        .navbar-nav {
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0;
        }
        .nav-item {
            list-style: none;
            margin: 0 0.5rem;
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.15);
        }
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: rgba(0,0,0,0.95);
                padding: 1rem;
                border-radius: 10px;
                margin-top: 0.5rem;
            }
            .nav-item {
                margin: 0.5rem 0;
            }
            .nav-link {
                display: block;
                padding: 0.75rem 1rem;
            }
        }
        .order-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: slideIn 0.5s ease-out;
            background: white;
        }
        .order-card:hover {
            transform: translateY(-5px) scale(1.01);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .order-card .card-header {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 1.5rem;
        }
        .order-card .card-header h5 {
            font-weight: 600;
            margin: 0;
        }
        .badge {
            padding: 0.6rem 1.2rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .badge:hover {
            transform: scale(1.05);
        }
        .card-body {
            padding: 2rem;
        }
        .order-detail {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        .order-detail:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .order-detail strong {
            color: #2c3e50;
            font-weight: 600;
        }
        .order-detail span {
            color: #6c757d;
        }
        .btn-primary {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.2);
        }
        .btn-primary:hover::before {
            left: 100%;
        }
        .alert-info {
            background: linear-gradient(45deg, #e3f2fd, #bbdefb);
            border: none;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }
        .alert-info i {
            font-size: 3rem;
            color: #1976d2;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
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
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .container {
            margin-top: 100px;
        }
        h2 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="products.php">
                <i class="fas fa-pepper-hot me-2"></i>Teer Brand
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                        <a class="nav-link active" href="order_history.php">
                            <i class="fas fa-history me-1"></i> Order History
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 100px;">
        <h2>Order History</h2>
        
        <?php if (empty($orders)): ?>
            <div class="alert alert-info">
                <i class="fas fa-shopping-bag mb-3"></i>
                <h4>No Orders Yet</h4>
                <p class="mb-3">You haven't placed any orders yet.</p>
                <a href="products.php" class="btn btn-primary">
                    <i class="fas fa-store me-2"></i>Start Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach($orders as $order): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card order-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Order #<?php echo $order['order_id']; ?></h5>
                                <span class="badge bg-<?php 
                                    echo match($order['status']) {
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="order-detail">
                                    <strong><i class="far fa-calendar-alt me-2"></i>Order Date:</strong>
                                    <span><?php echo date('F j, Y', strtotime($order['order_date'])); ?></span>
                                </div>
                                <div class="order-detail">
                                    <strong><i class="fas fa-box me-2"></i>Total Items:</strong>
                                    <span><?php echo $order['total_items']; ?></span>
                                </div>
                                <div class="order-detail">
                                    <strong><i class="fas fa-receipt me-2"></i>Total Amount:</strong>
                                    <span>₹<?php echo number_format($order['total_amount'], 0); ?></span>
                                </div>
                                <div class="order-detail">
                                    <strong><i class="fas fa-tags me-2"></i>Products:</strong>
                                    <span><?php echo htmlspecialchars($order['product_names']); ?></span>
                                </div>
                                
                                <a href="order_details.php?id=<?php echo $order['order_id']; ?>" 
                                   class="btn btn-primary w-100">
                                    <i class="fas fa-eye me-2"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 