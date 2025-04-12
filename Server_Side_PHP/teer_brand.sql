-- Create products table
CREATE TABLE IF NOT EXISTS products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create cart table
CREATE TABLE IF NOT EXISTS cart (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'processing', 'shipped', 'delivered') DEFAULT 'pending'
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- First, disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing data
TRUNCATE TABLE cart;
TRUNCATE TABLE order_items;
TRUNCATE TABLE orders;
TRUNCATE TABLE products;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Insert new products
INSERT INTO products (name, description, price, stock, image_url) VALUES
('Kala Namak', 'Black salt with a distinctive sulfurous aroma, perfect for chaats and salads.', 299, 50, 'images/Products/Kala_namak.png'),
('Sabji Mix', 'A perfect blend of spices for vegetable dishes.', 399, 45, 'images/Products/Sabji_Mix.png'),
('Mirch Powder', 'Finely ground red chili powder for that perfect heat.', 249, 60, 'images/Products/Mirch_Powder.png'),
('Golki Powder', 'Traditional spice blend for authentic Indian flavors.', 349, 40, 'images/Products/Golki_Powder.png'),
('Dhania Powder', 'Ground coriander seeds for aromatic dishes.', 199, 70, 'images/Products/Dhania_Powder.png'),
('Chatt Masala', 'Tangy spice mix perfect for chaats and snacks.', 299, 55, 'images/Products/Chatt_Masala.png'),
('Dhania', 'Whole coriander seeds for fresh grinding.', 249, 65, 'images/Products/Dhania.png'),
('Sendha Namak', 'Rock salt, a pure form of salt used in fasting.', 249, 50, 'images/Products/Sendha_namak.png'),
('Sarso', 'Mustard seeds for tempering and pickling.', 299, 45, 'images/Products/Sarso.png'),
('Jeera', 'Cumin seeds for aromatic Indian cooking.', 249, 60, 'images/Products/Jeera.png'),
('Black Pepper', 'Premium quality black pepper for everyday use.', 349, 55, 'images/Products/BlackPepper.png'),
('Turmeric Powder', 'Pure turmeric powder for health and flavor.', 249, 70, 'images/Products/Turmeric_Powder.png'); 