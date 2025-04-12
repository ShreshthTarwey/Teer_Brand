<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_GET['product_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Product ID is required']);
    exit;
}

$product_id = $_GET['product_id'];

try {
    // First check if product exists
    $stmt = $pdo->prepare("SELECT product_id FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        exit;
    }

    // Get reviews
    $stmt = $pdo->prepare("
        SELECT r.* 
        FROM reviews r 
        WHERE r.product_id = ? 
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$product_id]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'reviews' => $reviews]);
} catch (PDOException $e) {
    error_log("Database error in get_reviews.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
} 