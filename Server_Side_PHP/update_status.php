<?php
require_once 'config/database.php';

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    header('Content-Type: application/json');
    
    try {
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET status = ? 
            WHERE order_id = ?
        ");
        $stmt->execute([$_POST['status'], $_POST['order_id']]);
        
        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Fetch all orders for display
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
    <title>Teer Brand - Update Order Status</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Teer Brand</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="products.php">Products</a>
                <a class="nav-link" href="cart.php">Cart</a>
                <a class="nav-link" href="order_history.php">Order History</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Update Order Status</h2>
        
        <?php if (empty($orders)): ?>
            <div class="alert alert-info">
                No orders found.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total Items</th>
                            <th>Total Amount</th>
                            <th>Current Status</th>
                            <th>Update Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td><?php echo date('F j, Y', strtotime($order['order_date'])); ?></td>
                            <td><?php echo $order['total_items']; ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
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
                            </td>
                            <td>
                                <select class="form-select status-select" 
                                        data-order-id="<?php echo $order['order_id']; ?>">
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn btn-primary btn-sm update-status" 
                                        data-order-id="<?php echo $order['order_id']; ?>">
                                    Update
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.update-status').click(function() {
            const orderId = $(this).data('order-id');
            const status = $(this).closest('tr').find('.status-select').val();
            
            $.ajax({
                url: 'update_status.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    order_id: orderId,
                    status: status
                },
                success: function(response) {
                    try {
                        if (response.success) {
                            alert('Status updated successfully!');
                            location.reload();
                        } else {
                            alert('Error updating status: ' + (response.error || 'Unknown error'));
                        }
                    } catch (e) {
                        alert('Error processing response');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error updating status: ' + error);
                }
            });
        });
    });
    </script>
</body>
</html> 