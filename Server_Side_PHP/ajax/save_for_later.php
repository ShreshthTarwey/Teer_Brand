<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $action = $data['action'] ?? '';
        $productId = $data['product_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;
        $cartId = $data['cart_id'] ?? null;

        switch ($action) {
            case 'save':
                // First, check if item exists in saved_items
                $stmt = $pdo->prepare("SELECT * FROM saved_items WHERE product_id = ?");
                $stmt->execute([$productId]);
                
                if ($stmt->rowCount() === 0) {
                    // Add to saved items
                    $stmt = $pdo->prepare("INSERT INTO saved_items (product_id, quantity) VALUES (?, ?)");
                    $stmt->execute([$productId, $quantity]);
                    
                    // If item was in cart, remove it
                    if ($cartId) {
                        $stmt = $pdo->prepare("DELETE FROM cart WHERE cart_id = ?");
                        $stmt->execute([$cartId]);
                    }
                    
                    $response['success'] = true;
                    $response['message'] = 'Item saved for later';
                } else {
                    $response['message'] = 'Item already saved';
                }
                break;

            case 'move_to_cart':
                // First, get the saved item details
                $stmt = $pdo->prepare("SELECT * FROM saved_items WHERE product_id = ?");
                $stmt->execute([$productId]);
                $savedItem = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($savedItem) {
                    // Check if product is already in cart
                    $stmt = $pdo->prepare("SELECT * FROM cart WHERE product_id = ?");
                    $stmt->execute([$productId]);
                    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($cartItem) {
                        // Update quantity in cart
                        $newQuantity = $cartItem['quantity'] + $savedItem['quantity'];
                        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE product_id = ?");
                        $stmt->execute([$newQuantity, $productId]);
                    } else {
                        // Add to cart
                        $stmt = $pdo->prepare("INSERT INTO cart (product_id, quantity) VALUES (?, ?)");
                        $stmt->execute([$productId, $savedItem['quantity']]);
                    }
                    
                    // Remove from saved items
                    $stmt = $pdo->prepare("DELETE FROM saved_items WHERE product_id = ?");
                    $stmt->execute([$productId]);
                    
                    $response['success'] = true;
                    $response['message'] = 'Item moved to cart';
                }
                break;

            case 'remove':
                $stmt = $pdo->prepare("DELETE FROM saved_items WHERE product_id = ?");
                $stmt->execute([$productId]);
                
                $response['success'] = true;
                $response['message'] = 'Item removed from saved items';
                break;
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    echo json_encode($response);
    exit;
} 