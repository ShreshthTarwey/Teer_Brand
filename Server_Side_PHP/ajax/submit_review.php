<?php
require_once '../config/database.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the received POST data
error_log("Received POST data: " . print_r($_POST, true));

// Handle both parameter naming conventions
$product_id = $_POST['product_id'] ?? $_POST['productId'] ?? null;
$rating = $_POST['rating'] ?? null;
$comment = $_POST['comment'] ?? null;
$reviewer_name = $_POST['reviewer_name'] ?? $_POST['reviewerName'] ?? null;

if (!$product_id || !$rating || !$comment || !$reviewer_name) {
    error_log("Missing required fields in POST data");
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

$rating = (int)$rating;
$comment = trim($comment);
$reviewer_name = trim($reviewer_name);

error_log("Processing review for product_id: $product_id, rating: $rating, name: $reviewer_name");

if ($rating < 1 || $rating > 5) {
    error_log("Invalid rating value: $rating");
    echo json_encode(['error' => 'Rating must be between 1 and 5']);
    exit;
}

if (empty($comment)) {
    error_log("Empty comment received");
    echo json_encode(['error' => 'Comment cannot be empty']);
    exit;
}

if (empty($reviewer_name)) {
    error_log("Empty reviewer name received");
    echo json_encode(['error' => 'Please enter your name']);
    exit;
}

try {
    // Check if products table exists
    $tableExists = $pdo->query("SHOW TABLES LIKE 'products'")->rowCount() > 0;
    if (!$tableExists) {
        error_log("Products table does not exist");
        echo json_encode(['error' => 'Products table does not exist']);
        exit;
    }

    // First check if product exists
    $stmt = $pdo->prepare("SELECT product_id FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    if (!$stmt->fetch()) {
        error_log("Product not found with ID: $product_id");
        echo json_encode(['error' => 'Product not found']);
        exit;
    }

    // Check if reviews table exists
    $tableExists = $pdo->query("SHOW TABLES LIKE 'reviews'")->rowCount() > 0;
    if (!$tableExists) {
        // Create reviews table
        $pdo->exec("
            CREATE TABLE reviews (
                review_id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                user_id INT NOT NULL,
                reviewer_name VARCHAR(255) NOT NULL,
                rating INT NOT NULL,
                comment TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
            )
        ");
        error_log("Created reviews table");
    } else {
        // Check if user_id column exists
        $columns = $pdo->query("SHOW COLUMNS FROM reviews")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('user_id', $columns)) {
            // Add user_id column
            $pdo->exec("ALTER TABLE reviews ADD COLUMN user_id INT NOT NULL AFTER product_id");
            $pdo->exec("ALTER TABLE reviews ADD CONSTRAINT fk_review_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE");
            error_log("Added user_id column to reviews table");
        }
    }

    // Get the current user's ID (you'll need to implement this based on your authentication system)
    $user_id = 1; // This should be replaced with the actual logged-in user's ID

    // Insert new review
    $stmt = $pdo->prepare("
        INSERT INTO reviews (product_id, user_id, reviewer_name, rating, comment) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    error_log("Attempting to insert review into database");
    $result = $stmt->execute([$product_id, $user_id, $reviewer_name, $rating, $comment]);
    
    if ($result) {
        error_log("Review inserted successfully");
        echo json_encode(['success' => true]);
    } else {
        error_log("Failed to insert review");
        echo json_encode(['error' => 'Failed to submit review']);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    error_log("SQL State: " . $e->getCode());
    error_log("Error Info: " . print_r($stmt->errorInfo(), true));
    echo json_encode(['error' => 'Database error occurred: ' . $e->getMessage()]);
} 