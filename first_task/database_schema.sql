-- Database creation for e-commerce project
-- Create database
CREATE DATABASE IF NOT EXISTS project_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE project_db;

-- Table for products
CREATE TABLE products (
    product_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    product_category VARCHAR(100) NOT NULL,
    product_description VARCHAR(250),
    product_image VARCHAR(250),
    product_image2 VARCHAR(250),
    product_image3 VARCHAR(250),
    product_image4 VARCHAR(250),
    product_price DECIMAL(6,2) NOT NULL,
    product_special_offer INTEGER(2) DEFAULT 0,
    product_color VARCHAR(100)
);

-- Table for users
CREATE TABLE users (
    user_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    user_email VARCHAR(100) NOT NULL UNIQUE,
    user_password VARCHAR(100) NOT NULL
);

-- Table for orders
CREATE TABLE orders (
    order_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_cost DECIMAL(6,2) NOT NULL,
    order_status VARCHAR(100) NOT NULL,
    user_id INT(11) NOT NULL,
    shipping_city VARCHAR(255) NOT NULL,
    shipping_uf VARCHAR(2) NOT NULL,
    shipping_address VARCHAR(255) NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Table for order items
CREATE TABLE order_items (
    item_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    qnt INT(11) NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Table for payments
CREATE TABLE payments (
    payment_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    transaction_id VARCHAR(255) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Table for admin users
CREATE TABLE admins (
    admin_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    admin_name VARCHAR(255) NOT NULL,
    admin_email VARCHAR(255) NOT NULL UNIQUE,
    admin_password VARCHAR(100) NOT NULL
);

-- Insert default admin user (password: 123456 - MD5 hash: e10adc3949ba59abbe56e057f20f883e)
INSERT INTO admins VALUES(NULL, "admin", "admin@shop.com.br", "e10adc3949ba59abbe56e057f20f883e");