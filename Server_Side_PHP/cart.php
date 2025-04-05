<?php
require_once 'config/database.php';

// Fetch cart items with product details
$stmt = $pdo->query("
    SELECT c.*, p.name, p.price, p.image_url 
    FROM cart c 
    JOIN products p ON c.product_id = p.product_id
");
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
    <title>Teer Brand - Shopping Cart</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Teer Brand</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="products.php">Products</a>
                <a class="nav-link active" href="cart.php">
                    <i class="fas fa-shopping-cart"></i> Cart
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Shopping Cart</h2>
        <?php if (empty($cartItems)): ?>
            <div class="alert alert-info">
                Your cart is empty. <a href="products.php">Continue shopping</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cartItems as $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                    <span class="ms-2"><?php echo htmlspecialchars($item['name']); ?></span>
                                </div>
                            </td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <div class="input-group" style="width: 120px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateCartQuantity(<?php echo $item['cart_id']; ?>, 'decrease')">-</button>
                                    <input type="number" class="form-control text-center" value="<?php echo $item['quantity']; ?>" min="1" max="99" onchange="updateCartQuantity(<?php echo $item['cart_id']; ?>, 'set', this.value)">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateCartQuantity(<?php echo $item['cart_id']; ?>, 'increase')">+</button>
                                </div>
                            </td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm" onclick="removeFromCart(<?php echo $item['cart_id']; ?>)">Remove</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary" onclick="proceedToCheckout()">Proceed to Checkout</button>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
    <script>
        function updateCartQuantity(cartId, action, value = null) {
            $.ajax({
                url: 'ajax/update_cart.php',
                method: 'POST',
                data: {
                    cart_id: cartId,
                    action: action,
                    value: value
                },
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert('Error updating cart quantity.');
                }
            });
        }

        function removeFromCart(cartId) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                $.ajax({
                    url: 'ajax/remove_from_cart.php',
                    method: 'POST',
                    data: {
                        cart_id: cartId
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function() {
                        alert('Error removing item from cart.');
                    }
                });
            }
        }

        function proceedToCheckout() {
            window.location.href = 'checkout.php';
        }
    </script>
</body>
</html> 