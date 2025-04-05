<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$product_id = $_POST['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? 1;

if (!$product_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Product ID is required']);
    exit;
}

try {
    // Check if product exists and has enough stock
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        exit;
    }

    if ($product['stock'] < $quantity) {
        http_response_code(400);
        echo json_encode(['error' => 'Not enough stock available']);
        exit;
    }

    // Check if product already exists in cart
    $stmt = $pdo->prepare("SELECT cart_id, quantity FROM cart WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $existingItem = $stmt->fetch();

    if ($existingItem) {
        // Update quantity if product exists in cart
        $newQuantity = $existingItem['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
        $stmt->execute([$newQuantity, $existingItem['cart_id']]);
    } else {
        // Add new item to cart
        $stmt = $pdo->prepare("INSERT INTO cart (product_id, quantity) VALUES (?, ?)");
        $stmt->execute([$product_id, $quantity]);
    }

    echo json_encode(['success' => true, 'message' => 'Product added to cart successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?> 