-- MiniShop MVC Demo
-- MySQL 8.x
-- Dùng cho demo MVC + Bài 7 (Authentication/Authorization/CSRF)
-- + Bài 8 (JSON API/Fetch API)
-- Tài khoản demo:
--   admin@example.com / Admin@123
--   customer@example.com / Customer@123

DROP DATABASE IF EXISTS minishop_mvc;

CREATE DATABASE minishop_mvc
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE minishop_mvc;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    code VARCHAR(30) NOT NULL,
    name VARCHAR(180) NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    CONSTRAINT uq_products_code UNIQUE (code),
    CONSTRAINT chk_products_price CHECK (price >= 0),
    CONSTRAINT chk_products_stock CHECK (stock >= 0),
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    status ENUM('active', 'locked') NOT NULL DEFAULT 'active',
    CONSTRAINT uq_users_email UNIQUE (email)
) ENGINE=InnoDB;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(14,2) NOT NULL DEFAULT 0,
    status ENUM('pending', 'paid', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_orders_total CHECK (total_amount >= 0),
    CONSTRAINT fk_orders_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    CONSTRAINT chk_order_items_quantity CHECK (quantity > 0),
    CONSTRAINT chk_order_items_price CHECK (unit_price >= 0),
    CONSTRAINT uq_order_product UNIQUE (order_id, product_id),
    CONSTRAINT fk_order_items_order
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO categories(name, status) VALUES
('Văn phòng phẩm', 'active'),
('Sách', 'active'),
('Phụ kiện máy tính', 'active'),
('Danh mục tạm ẩn', 'inactive');

INSERT INTO products(category_id, code, name, price, stock, status) VALUES
(1, 'PEN-01',  'Bút bi xanh',              5000,   120, 'active'),
(1, 'NB-01',   'Sổ tay A5',               35000,    50, 'active'),
(2, 'BK-SQL',  'Nhập môn SQL',           120000,    20, 'active'),
(3, 'USB-32',  'USB 32GB',               180000,    15, 'active'),
(3, 'MOUSE-01','Chuột không dây',        250000,    12, 'active'),
(3, 'KEY-01',  'Bàn phím USB',           320000,     8, 'active'),
(2, 'BK-PHP',  'Lập trình PHP căn bản',  150000,    10, 'active'),
(1, 'PEN-02',  'Bút gel đen',              9000,     0, 'active');

-- Hash tạo bởi password_hash() của PHP.
INSERT INTO users(email, password_hash, full_name, role, status) VALUES
('admin@example.com',
 '$2y$12$JOH5EDD7hP.5MddArAEaOegGIPa21GLj6udmHEst6bHFztUKSDo0K',
 'Quản trị MiniShop', 'admin', 'active'),
('customer@example.com',
 '$2y$12$o2CjKbTUIiXzw1kadFPci.6wN3ZNav5iRijj1UCPPmPtaq/ACei6e',
 'Khách hàng Demo', 'customer', 'active'),
('locked@example.com',
 '$2y$12$o2CjKbTUIiXzw1kadFPci.6wN3ZNav5iRijj1UCPPmPtaq/ACei6e',
 'Tài khoản Bị khóa', 'customer', 'locked');

INSERT INTO orders(user_id, total_amount, status, created_at) VALUES
(2, 370000, 'paid',      '2026-08-25 09:00:00'),
(2, 300000, 'pending',   '2026-08-28 14:30:00'),
(2, 180000, 'cancelled', '2026-08-29 10:15:00');

INSERT INTO order_items(order_id, product_id, quantity, unit_price) VALUES
(1, 1, 2,   5000),
(1, 4, 2, 180000),
(2, 7, 2, 150000),
(3, 4, 1, 180000);
