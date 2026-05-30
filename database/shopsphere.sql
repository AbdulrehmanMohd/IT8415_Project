-- Create and select database
CREATE DATABASE IF NOT EXISTS shopsphere_db;
USE shopsphere_db;

-- USERS TABLE
CREATE TABLE IF NOT EXISTS dbproj_users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','seller','customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- CATEGORIES TABLE
CREATE TABLE IF NOT EXISTS dbproj_categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL
);

-- PRODUCTS TABLE
CREATE TABLE IF NOT EXISTS dbproj_products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    category_id INT,
    image VARCHAR(255),
    seller_id INT,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES dbproj_categories(category_id),
    FOREIGN KEY (seller_id) REFERENCES dbproj_users(user_id)
);

-- ORDERS TABLE
CREATE TABLE IF NOT EXISTS dbproj_orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES dbproj_users(user_id)
);

-- ORDER ITEMS TABLE
CREATE TABLE IF NOT EXISTS dbproj_order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES dbproj_orders(order_id),
    FOREIGN KEY (product_id) REFERENCES dbproj_products(product_id)
);

-- COMMENTS TABLE
CREATE TABLE IF NOT EXISTS dbproj_comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES dbproj_products(product_id),
    FOREIGN KEY (user_id) REFERENCES dbproj_users(user_id)
);

-- RATINGS TABLE
CREATE TABLE IF NOT EXISTS dbproj_ratings (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    UNIQUE KEY unique_rating (product_id, user_id),
    FOREIGN KEY (product_id) REFERENCES dbproj_products(product_id),
    FOREIGN KEY (user_id) REFERENCES dbproj_users(user_id)
);

-- FULL TEXT SEARCH INDEX
ALTER TABLE dbproj_products ADD FULLTEXT INDEX ft_product_search (name, description);

-- ============================
-- Get top selling products within a date range
-- ============================
DELIMITER $$

CREATE PROCEDURE GetTopProducts(IN start_date DATE, IN end_date DATE)
BEGIN
    SELECT p.name, SUM(oi.quantity) AS total_sold, SUM(oi.quantity * oi.price) AS revenue
    FROM dbproj_order_items oi
    JOIN dbproj_products p ON oi.product_id = p.product_id
    JOIN dbproj_orders o ON oi.order_id = o.order_id
    WHERE DATE(o.created_at) BETWEEN start_date AND end_date
    GROUP BY oi.product_id
    ORDER BY total_sold DESC
    LIMIT 10;
END$$

DELIMITER ;

-- ============================
-- Reduce stock automatically when an order item is placed
-- ============================
DELIMITER $$

CREATE TRIGGER after_order_item_insert
AFTER INSERT ON dbproj_order_items
FOR EACH ROW
BEGIN
    UPDATE dbproj_products
    SET stock = stock - NEW.quantity
    WHERE product_id = NEW.product_id;
END$$

DELIMITER ;

-- ============================
-- Sample data
-- ============================
INSERT INTO dbproj_categories (category_name) VALUES
('Phones'), ('Laptops'), ('Accessories'), ('Tablets'), ('Audio');

-- Test accounts (password: Pass@1234)
INSERT INTO dbproj_users (username, email, password, role) VALUES
('AbdulRehman', 'abdulrehman@shopsphere.com', '$2y$10$TKh8H1.PfbuNIxLGZ3kOOuSlFHKf9z2dM9BsaLe7OkFjV6dxK3jJK', 'admin'),
('Abdulaziz', 'abdulaziz@shopsphere.com', '$2y$10$TKh8H1.PfbuNIxLGZ3kOOuSlFHKf9z2dM9BsaLe7OkFjV6dxK3jJK', 'seller'),
('Abdulla', 'abdulla@shopsphere.com', '$2y$10$TKh8H1.PfbuNIxLGZ3kOOuSlFHKf9z2dM9BsaLe7OkFjV6dxK3jJK', 'seller'),
('Ahmed', 'ahmed@shopsphere.com', '$2y$10$TKh8H1.PfbuNIxLGZ3kOOuSlFHKf9z2dM9BsaLe7OkFjV6dxK3jJK', 'customer'),
('MohamedAli', 'mohamedali@shopsphere.com', '$2y$10$TKh8H1.PfbuNIxLGZ3kOOuSlFHKf9z2dM9BsaLe7OkFjV6dxK3jJK', 'customer');

-- Products listed by Abdulaziz (id=2) and Abdulla (id=3)
INSERT INTO dbproj_products (name, description, price, stock, category_id, image, seller_id) VALUES
('iPad Pro M4 11-inch', 'The most powerful iPad ever. M4 chip, Ultra Retina XDR display, super thin design.', 1099.00, 12, 4, 'https://placehold.co/300x200?text=iPad+Pro+M4', 2),
('Memo CX-05', 'Compact Android phone with clean UI and solid battery life, great value pick.', 219.00, 35, 1, 'https://placehold.co/300x200?text=Memo+CX-05', 2),
('KZ ZST X IEMs', 'Hybrid in-ear monitors with a balanced armature + dynamic driver setup. Audiophile on a budget.', 18.99, 80, 5, 'https://placehold.co/300x200?text=KZ+ZST+X', 2),
('iPhone 17 Pro Max', 'Apples latest flagship. Bigger display, improved cameras, A19 Pro chip.', 1299.00, 20, 1, 'https://placehold.co/300x200?text=iPhone+17+PM', 2),
('RedMagic Astra', 'Gaming phone with 165Hz display, shoulder triggers, and massive cooling system.', 949.00, 10, 1, 'https://placehold.co/300x200?text=RedMagic+Astra', 3),
('RedMagic 11 Pro', 'Previous gen gaming beast. Snapdragon 8 Gen 3, 6500mAh battery, RGB everything.', 799.00, 14, 1, 'https://placehold.co/300x200?text=RedMagic+11+Pro', 3),
('Samsung Galaxy S25 Ultra', 'S Pen included, 200MP camera, Snapdragon 8 Elite.', 1199.00, 18, 1, 'https://placehold.co/300x200?text=S25+Ultra', 2),
('Logitech G Pro X 2', 'Lightweight wireless gaming headset used by pros.', 249.00, 25, 5, 'https://placehold.co/300x200?text=GPro+X2', 2),
('MacBook Pro M4', '14-inch with M4 Pro chip. Insane performance for dev work and video editing.', 1999.00, 8, 2, 'https://placehold.co/300x200?text=MBP+M4', 2),
('Anker MagGo 15W', 'Fast wireless charger stand, works with MagSafe iPhones.', 45.99, 60, 3, 'https://placehold.co/300x200?text=Anker+MagGo', 2),
('Dell XPS 15 OLED', 'Premium laptop with OLED touch display and RTX 4060.', 1799.00, 7, 2, 'https://placehold.co/300x200?text=Dell+XPS+15', 2),
('Google Pixel 9 Pro', 'Best Android camera phone. Pure Google AI experience.', 999.00, 16, 1, 'https://placehold.co/300x200?text=Pixel+9+Pro', 3),
('Baseus 65W GaN Charger', 'Charges laptop + phone + earbuds at the same time. Travel essential.', 34.99, 120, 3, 'https://placehold.co/300x200?text=Baseus+GaN', 2),
('Sony WF-1000XM5', 'Best noise cancelling earbuds. Small, light, ridiculous ANC.', 299.00, 22, 5, 'https://placehold.co/300x200?text=WF1000XM5', 2),
('Xiaomi Pad 7 Pro', 'Fast 144Hz tablet, great for students. Runs HyperOS.', 499.00, 30, 4, 'https://placehold.co/300x200?text=Xiaomi+Pad7', 3);