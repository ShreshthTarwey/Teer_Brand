<?php
$host = 'localhost';
$dbname = 'teer_brand';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create saved_items table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS saved_items (
        saved_item_id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(product_id)
    )";

    $pdo->exec($sql);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?> 