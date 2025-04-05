<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$cart_id = $_POST['cart_id'] ?? null;
$action = $_POST['action'] ?? null;
$value = $_POST['value'] ?? null;

if (!$cart_id || !$action) {
    http_response_code(400);
    echo json_encode(['error' => 'Cart ID and action are required']);
    exit;
}

try {
    // Get current cart item
    $stmt = $pdo->prepare("SELECT c.*, p.stock FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.cart_id = ?");
    $stmt->execute([$cart_id]);
    $cartItem = $stmt->fetch();

    if (!$cartItem) {
        http_response_code(404);
        echo json_encode(['error' => 'Cart item not found']);
        exit;
    }

    $currentQuantity = $cartItem['quantity'];
    $newQuantity = $currentQuantity;

    switch ($action) {
        case 'increase':
            $newQuantity = min($currentQuantity + 1, $cartItem['stock']);
            break;
        case 'decrease':
            $newQuantity = max($currentQuantity - 1, 1);
            break;
        case 'set':
            if ($value !== null) {
                $newQuantity = max(1, min((int)$value, $cartItem['stock']));
            }
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            exit;
    }

    // Update cart quantity
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
    $stmt->execute([$newQuantity, $cart_id]);

    echo json_encode(['success' => true, 'message' => 'Cart updated successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?> 