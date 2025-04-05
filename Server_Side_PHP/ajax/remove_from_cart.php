<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$cart_id = $_POST['cart_id'] ?? null;

if (!$cart_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Cart ID is required']);
    exit;
}

try {
    // Check if cart item exists
    $stmt = $pdo->prepare("SELECT cart_id FROM cart WHERE cart_id = ?");
    $stmt->execute([$cart_id]);
    $cartItem = $stmt->fetch();

    if (!$cartItem) {
        http_response_code(404);
        echo json_encode(['error' => 'Cart item not found']);
        exit;
    }

    // Remove item from cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE cart_id = ?");
    $stmt->execute([$cart_id]);

    echo json_encode(['success' => true, 'message' => 'Item removed from cart successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?> 