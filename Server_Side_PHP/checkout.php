<?php
require_once 'config/database.php';

// Fetch cart items with product details
$stmt = $pdo->query("
    SELECT c.*, p.name, p.price, p.stock
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
");
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Create new order
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount, status, order_date)
            VALUES (?, ?, 'pending', NOW())
        ");
        $stmt->execute([1, $total]); // Assuming user_id 1 for now
        $orderId = $pdo->lastInsertId();

        // Add order items and update stock
        foreach ($cartItems as $item) {
            // Add to order_items
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $orderId,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);

            // Update product stock
            $stmt = $pdo->prepare("
                UPDATE products 
                SET stock = stock - ? 
                WHERE product_id = ?
            ");
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }

        // Clear cart
        $stmt = $pdo->prepare("DELETE FROM cart");
        $stmt->execute();

        // Send email notification
        $to = "shreshthtarwey3010@gmail.com";
        $subject = "Order Confirmation - Teer Brand";
        
        // Build email content
        $message = "Thank you for your order!\n\n";
        $message .= "Order ID: #" . $orderId . "\n";
        $message .= "Order Date: " . date('F j, Y') . "\n\n";
        $message .= "Order Details:\n";
        
        foreach ($cartItems as $item) {
            $message .= "- " . $item['name'] . " x" . $item['quantity'] . 
                       " (₹" . number_format($item['price'] * $item['quantity'], 2) . ")\n";
        }
        
        $message .= "\nTotal Amount: ₹" . number_format($total, 2) . "\n\n";
        $message .= "You can track your order status at: http://localhost/CA2/Teer_Brand/Server_Side_PHP/order_details.php?id=" . $orderId . "\n\n";
        $message .= "Best regards,\nTeer Brand Team";

        $headers = "From: noreply@teerbrand.com\r\n";
        $headers .= "Reply-To: support@teerbrand.com\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        mail($to, $subject, $message, $headers);

        $pdo->commit();

        // Redirect to success page
        header("Location: order_success.php?id=" . $orderId);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "An error occurred while processing your order. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teer Brand - Checkout</title>
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
        <h2>Checkout</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (empty($cartItems)): ?>
            <div class="alert alert-info">
                Your cart is empty. <a href="products.php">Continue shopping</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($cartItems as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                            <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td><strong>₹<?php echo number_format($total, 2); ?></strong></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <form method="POST" class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="card_number" class="form-label">Card Number</label>
                                <input type="text" class="form-control" id="card_number" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expiry" class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" id="expiry" placeholder="MM/YY" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Place Order</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
</body>
</html> 