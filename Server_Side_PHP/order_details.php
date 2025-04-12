<?php
require_once 'config/database.php';

$orderId = $_GET['id'] ?? null;

if (!$orderId) {
    header('Location: order_history.php');
    exit;
}

// Fetch order details
$stmt = $pdo->prepare("
    SELECT o.*, oi.quantity, oi.price, p.name, p.image_url, p.description
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.order_id = ?
");
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($orderItems)) {
    header('Location: order_history.php');
    exit;
}

$order = $orderItems[0]; // Get order details from first item
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teer Brand - Order Details</title>
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
        .card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            animation: slideIn 0.5s ease-out;
        }
        .card-header {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 1.5rem;
        }
        .card-header h4 {
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
        .table {
            margin-bottom: 0;
        }
        .table th {
            border-top: none;
            color: #2c3e50;
            font-weight: 600;
        }
        .table td {
            vertical-align: middle;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 10px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 10px;
            transition: all 0.3s ease;
        }
        .product-image:hover {
            transform: scale(1.1);
        }
        .product-name {
            font-weight: 600;
            color: #2c3e50;
            transition: all 0.3s ease;
        }
        .product-name:hover {
            color: #28a745;
        }
        .order-info {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: slideInRight 0.5s ease-out;
        }
        .order-info .card-header {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
        }
        .info-item {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        .info-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .info-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .info-value {
            color: #2c3e50;
            font-weight: 600;
            font-size: 1.1rem;
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
        .container {
            margin-top: 100px;
            margin-bottom: 50px;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            transform: translateX(-5px);
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);
            color: white;
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

    <div class="container">
        <a href="order_history.php" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Back to Orders
        </a>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Order #<?php echo $orderId; ?></h4>
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
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($orderItems as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo str_replace('images/', '../images/', htmlspecialchars($item['image_url'])); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                                     class="product-image">
                                                <span class="product-name ms-3"><?php echo htmlspecialchars($item['name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-muted"><?php echo htmlspecialchars($item['description']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>₹<?php echo number_format($item['price'], 0); ?></td>
                                        <td>₹<?php echo number_format($item['price'] * $item['quantity'], 0); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                        <td><strong class="text-success">₹<?php echo number_format($order['total_amount'], 0); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card order-info">
                    <div class="card-header">
                        <h5 class="mb-0">Order Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label">Order Date</div>
                            <div class="info-value">
                                <i class="far fa-calendar-alt me-2"></i>
                                <?php echo date('F j, Y', strtotime($order['order_date'])); ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Order Status</div>
                            <div class="info-value">
                                <i class="fas fa-circle me-2 text-<?php 
                                    echo match($order['status']) {
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        default => 'secondary'
                                    };
                                ?>"></i>
                                <?php echo ucfirst($order['status']); ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Total Items</div>
                            <div class="info-value">
                                <i class="fas fa-box me-2"></i>
                                <?php echo count($orderItems); ?> items
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Total Amount</div>
                            <div class="info-value text-success">
                                <i class="fas fa-receipt me-2"></i>
                                ₹<?php echo number_format($order['total_amount'], 0); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 