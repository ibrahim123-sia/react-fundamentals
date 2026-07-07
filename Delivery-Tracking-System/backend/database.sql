CREATE DATABASE IF NOT EXISTS delivery_db;
USE delivery_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'delivery_boy', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    status ENUM('Pending', 'Assigned', 'Accepted', 'On the way', 'Delivered') DEFAULT 'Pending',
    assigned_to INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert Default Demo Users (with password plain text for easy debugging in local test, 
-- though production should use password_hash)
INSERT INTO users (name, email, phone, password, role) VALUES
('Admin User', 'admin@test.com', '123456789', '123', 'admin')
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO users (name, email, phone, password, role) VALUES
('John Delivery', 'delivery@test.com', '555555555', '123', 'delivery_boy')
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO users (name, email, phone, password, role) VALUES
('Sarah Customer', 'customer@test.com', '999999999', '123', 'user')
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO users (name, email, phone, password, role) VALUES
('Ali Rider', 'ali@test.com', '444444444', '123', 'delivery_boy')
ON DUPLICATE KEY UPDATE id=id;
