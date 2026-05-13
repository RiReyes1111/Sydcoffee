



CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS coffee_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category ENUM('cold_coffee', 'non_coffee', 'hot_drinks') NOT NULL,
    price_small DECIMAL(10,2) DEFAULT NULL,
    price_large DECIMAL(10,2) DEFAULT NULL,
    price_single DECIMAL(10,2) DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO users (name, email, username, password, role) VALUES
('Admin', 'admin@sydcoffee.com', 'admin', MD5('admin123'), 'admin');


INSERT INTO coffee_items (name, category, price_small, price_large, image) VALUES
('Cafe Latte',        'cold_coffee', 95.00, 105.00, 'images/cafe-latte.jpg'),
('Cafe Americano',    'cold_coffee', 85.00, 95.00,  'images/cafe-americano.jpg'),
('Cafe Hazelnut',     'cold_coffee', 95.00, 105.00, 'images/cafe-hazelnut.jpg'),
('Cafe Vanilla',      'cold_coffee', 95.00, 105.00, 'images/cafe-vanilla.jpg'),
('Cafe Mocha',        'cold_coffee', 95.00, 105.00, 'images/cafe-mocha.jpg'),
('Salted Caramel',    'cold_coffee', 95.00, 105.00, 'images/salted-caramel.jpg'),
('Spanish Latte',     'cold_coffee', 100.00, 110.00,'images/spanish-latte.jpg'),
('Caramel Macchiato', 'cold_coffee', 100.00, 110.00,'images/caramel-macchiato.jpg');

INSERT INTO coffee_items (name, category, price_small, price_large, image) VALUES
('Dark Chocolate', 'non_coffee', 95.00, 105.00, 'images/dark-chocolate.jpg'),
('Matcha Latte',   'non_coffee', 95.00, 105.00, 'images/matcha-latte.jpg'),
('Blue Lemonade',  'non_coffee', 45.00, 55.00,  'images/blue-lemonade.jpg'),
('Strawberry',     'non_coffee', 45.00, 55.00,  'images/strawberry-soda.jpg'),
('Green Apple',    'non_coffee', 45.00, 55.00,  'images/green-apple.jpg'),
('Lychee',         'non_coffee', 45.00, 55.00,  'images/lychee.jpg');


INSERT INTO coffee_items (name, category, price_single, image) VALUES
('Brewed Cafe',    'hot_drinks', 40.00, 'images/brewed-cafe.jpg'),
('Hot Cafe Latte', 'hot_drinks', 45.00, 'images/hot-latte.jpg'),
('Hot Choco',      'hot_drinks', 45.00, 'images/hot-choco.jpg');

ALTER TABLE coffee_items ADD COLUMN deleted_at DATETIME DEFAULT NULL;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) DEFAULT 'Guest',
    user_id INT DEFAULT NULL,
    items_json TEXT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    delivery_fee DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    fulfillment ENUM('pickup','delivery') DEFAULT 'pickup',
    payment_method VARCHAR(20) DEFAULT 'cash',
    delivery_address TEXT DEFAULT NULL,
    contact_number VARCHAR(30) DEFAULT NULL,
    special_notes TEXT DEFAULT NULL,
    status ENUM('pending','preparing','ready','completed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE orders ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;