-- EV Showroom Platform Database Schema
-- For MySQL / MariaDB (XAMPP Compatible)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `ev_showroom` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ev_showroom`;

-- Super Admin Table
CREATE TABLE `super_admin` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `avatar` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `last_login` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Admin Table (Showroom Owners)
CREATE TABLE `admin` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `showroom_name` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `address` TEXT,
    `city` VARCHAR(100),
    `state` VARCHAR(100),
    `pincode` VARCHAR(10),
    `gst_number` VARCHAR(50),
    `avatar` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `permissions` JSON DEFAULT NULL,
    `last_login` DATETIME DEFAULT NULL,
    `created_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `super_admin`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Users Table (Customers)
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT NOT NULL,
    `username` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `address` TEXT,
    `city` VARCHAR(100),
    `state` VARCHAR(100),
    `pincode` VARCHAR(10),
    `avatar` VARCHAR(255) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `is_blocked` TINYINT(1) DEFAULT 0,
    `email_verified` TINYINT(1) DEFAULT 0,
    `otp` VARCHAR(10) DEFAULT NULL,
    `otp_expiry` DATETIME DEFAULT NULL,
    `last_login` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`admin_id`) REFERENCES `admin`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_email_per_admin` (`admin_id`, `email`)
) ENGINE=InnoDB;

-- Categories Table
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT,
    `image` VARCHAR(255) DEFAULT NULL,
    `parent_id` INT DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `sort_order` INT DEFAULT 0,
    `created_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`created_by`) REFERENCES `super_admin`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Products Table (EV Bikes)
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT NOT NULL,
    `category_id` INT,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `brand` VARCHAR(100),
    `model` VARCHAR(100),
    `description` TEXT,
    `specifications` JSON,
    `price` DECIMAL(12,2) NOT NULL,
    `sale_price` DECIMAL(12,2) DEFAULT NULL,
    `battery_type` VARCHAR(100),
    `battery_capacity` VARCHAR(50),
    `range_km` INT,
    `top_speed` INT,
    `charging_time` VARCHAR(50),
    `motor_power` VARCHAR(50),
    `weight` DECIMAL(6,2),
    `warranty` VARCHAR(100),
    `colors` JSON,
    `images` JSON,
    `video_url` VARCHAR(255) DEFAULT NULL,
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `views` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`admin_id`) REFERENCES `admin`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    UNIQUE KEY `unique_slug_per_admin` (`admin_id`, `slug`)
) ENGINE=InnoDB;

-- Inventory Table
CREATE TABLE `inventory` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `admin_id` INT NOT NULL,
    `quantity` INT NOT NULL DEFAULT 0,
    `reserved` INT DEFAULT 0,
    `min_stock_alert` INT DEFAULT 5,
    `last_restocked` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`admin_id`) REFERENCES `admin`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_product_admin` (`product_id`, `admin_id`)
) ENGINE=InnoDB;

-- Preorders Table
CREATE TABLE `preorders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_number` VARCHAR(50) NOT NULL UNIQUE,
    `user_id` INT NOT NULL,
    `admin_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `quantity` INT DEFAULT 1,
    `color` VARCHAR(50),
    `notes` TEXT,
    `status` ENUM('pending', 'confirmed', 'processing', 'ready', 'completed', 'cancelled') DEFAULT 'pending',
    `estimated_delivery` DATE DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`admin_id`) REFERENCES `admin`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Orders Table
CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_number` VARCHAR(50) NOT NULL UNIQUE,
    `preorder_id` INT DEFAULT NULL,
    `user_id` INT NOT NULL,
    `admin_id` INT NOT NULL,
    `subtotal` DECIMAL(12,2) NOT NULL,
    `discount` DECIMAL(12,2) DEFAULT 0,
    `tax_amount` DECIMAL(12,2) DEFAULT 0,
    `shipping_charges` DECIMAL(12,2) DEFAULT 0,
    `total_amount` DECIMAL(12,2) NOT NULL,
    `payment_status` ENUM('pending', 'partial', 'paid', 'refunded') DEFAULT 'pending',
    `payment_method` VARCHAR(50),
    `order_status` ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    `shipping_address` TEXT,
    `billing_address` TEXT,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`preorder_id`) REFERENCES `preorders`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`admin_id`) REFERENCES `admin`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Order Items Table
CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    `unit_price` DECIMAL(12,2) NOT NULL,
    `total_price` DECIMAL(12,2) NOT NULL,
    `color` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Invoices Table
CREATE TABLE `invoices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
    `order_id` INT NOT NULL,
    `admin_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `subtotal` DECIMAL(12,2) NOT NULL,
    `cgst` DECIMAL(12,2) DEFAULT 0,
    `sgst` DECIMAL(12,2) DEFAULT 0,
    `igst` DECIMAL(12,2) DEFAULT 0,
    `discount` DECIMAL(12,2) DEFAULT 0,
    `service_charges` DECIMAL(12,2) DEFAULT 0,
    `total_amount` DECIMAL(12,2) NOT NULL,
    `invoice_date` DATE NOT NULL,
    `due_date` DATE,
    `status` ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    `pdf_path` VARCHAR(255) DEFAULT NULL,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`admin_id`) REFERENCES `admin`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Payments Table
CREATE TABLE `payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `invoice_id` INT,
    `user_id` INT NOT NULL,
    `admin_id` INT NOT NULL,
    `amount` DECIMAL(12,2) NOT NULL,
    `payment_method` VARCHAR(50),
    `transaction_id` VARCHAR(255),
    `status` ENUM('pending', 'success', 'failed', 'refunded') DEFAULT 'pending',
    `payment_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`admin_id`) REFERENCES `admin`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Offers Table
CREATE TABLE `offers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT DEFAULT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `offer_type` ENUM('discount', 'exchange', 'festive', 'launch', 'coupon') NOT NULL,
    `discount_type` ENUM('percentage', 'fixed') DEFAULT 'percentage',
    `discount_value` DECIMAL(12,2),
    `coupon_code` VARCHAR(50) UNIQUE,
    `min_order_amount` DECIMAL(12,2) DEFAULT 0,
    `max_discount` DECIMAL(12,2) DEFAULT NULL,
    `usage_limit` INT DEFAULT NULL,
    `used_count` INT DEFAULT 0,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `is_global` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `image` VARCHAR(255) DEFAULT NULL,
    `created_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`admin_id`) REFERENCES `admin`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Notifications Table
CREATE TABLE `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sender_type` ENUM('super_admin', 'admin', 'system') NOT NULL,
    `sender_id` INT,
    `recipient_type` ENUM('all', 'admins', 'users', 'specific') NOT NULL,
    `recipient_id` INT DEFAULT NULL,
    `admin_id` INT DEFAULT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('info', 'offer', 'order', 'system', 'alert') DEFAULT 'info',
    `channels` JSON,
    `is_read` TINYINT(1) DEFAULT 0,
    `read_at` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`admin_id`) REFERENCES `admin`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Wishlist Table
CREATE TABLE `wishlist` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_wishlist` (`user_id`, `product_id`)
) ENGINE=InnoDB;

-- Global Settings Table
CREATE TABLE `global_settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `setting_type` VARCHAR(50) DEFAULT 'text',
    `category` VARCHAR(100) DEFAULT 'general',
    `is_editable` TINYINT(1) DEFAULT 1,
    `updated_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Banners Table
CREATE TABLE `banners` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255),
    `subtitle` VARCHAR(255),
    `image` VARCHAR(255) NOT NULL,
    `link` VARCHAR(255),
    `position` ENUM('home_hero', 'home_middle', 'sidebar', 'popup') DEFAULT 'home_hero',
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `start_date` DATE DEFAULT NULL,
    `end_date` DATE DEFAULT NULL,
    `created_by` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Themes Table
CREATE TABLE `themes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `primary_color` VARCHAR(20) DEFAULT '#00ff88',
    `secondary_color` VARCHAR(20) DEFAULT '#1a1a2e',
    `accent_color` VARCHAR(20) DEFAULT '#16213e',
    `text_color` VARCHAR(20) DEFAULT '#ffffff',
    `font_family` VARCHAR(100) DEFAULT 'Poppins',
    `custom_css` TEXT,
    `is_active` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Support Tickets Table
CREATE TABLE `support_tickets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_number` VARCHAR(50) NOT NULL UNIQUE,
    `user_id` INT,
    `admin_id` INT,
    `subject` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `priority` ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    `status` ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
    `assigned_to` INT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`admin_id`) REFERENCES `admin`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- System Logs Table
CREATE TABLE `system_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_type` ENUM('super_admin', 'admin', 'user', 'system') NOT NULL,
    `user_id` INT,
    `action` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Admin Activity Table
CREATE TABLE `admin_activity` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `admin_id` INT NOT NULL,
    `action` VARCHAR(255) NOT NULL,
    `module` VARCHAR(100),
    `record_id` INT,
    `old_data` JSON,
    `new_data` JSON,
    `ip_address` VARCHAR(45),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`admin_id`) REFERENCES `admin`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Permissions Table
CREATE TABLE `permissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT,
    `module` VARCHAR(100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert Default Data

-- Default Super Admin (password: admin123)
INSERT INTO `super_admin` (`username`, `email`, `password`, `full_name`, `phone`) VALUES
('superadmin', 'superadmin@evshowroom.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Administrator', '+91 9876543210');

-- Default Categories
INSERT INTO `categories` (`name`, `slug`, `description`, `created_by`) VALUES
('Electric Scooters', 'electric-scooters', 'Two-wheeler electric scooters', 1),
('Electric Bikes', 'electric-bikes', 'Electric motorcycles and bikes', 1),
('Electric Cycles', 'electric-cycles', 'Electric bicycles and e-cycles', 1);

-- Default Permissions
INSERT INTO `permissions` (`name`, `slug`, `module`) VALUES
('View Products', 'view_products', 'products'),
('Add Products', 'add_products', 'products'),
('Edit Products', 'edit_products', 'products'),
('Delete Products', 'delete_products', 'products'),
('View Orders', 'view_orders', 'orders'),
('Manage Orders', 'manage_orders', 'orders'),
('View Users', 'view_users', 'users'),
('Manage Users', 'manage_users', 'users'),
('Create Invoices', 'create_invoices', 'billing'),
('View Reports', 'view_reports', 'reports'),
('Manage Offers', 'manage_offers', 'offers'),
('Send Notifications', 'send_notifications', 'notifications');

-- Default Global Settings
INSERT INTO `global_settings` (`setting_key`, `setting_value`, `category`) VALUES
('site_name', 'Vportal - EV Showroom', 'general'),
('site_tagline', 'Your Electric Vehicle Destination', 'general'),
('site_email', 'contact@evshowroom.com', 'general'),
('site_phone', '+91 9876543210', 'general'),
('currency', 'INR', 'general'),
('currency_symbol', '₹', 'general'),
('gst_rate', '18', 'billing'),
('cgst_rate', '9', 'billing'),
('sgst_rate', '9', 'billing'),
('invoice_prefix', 'INV', 'billing'),
('order_prefix', 'ORD', 'billing'),
('maintenance_mode', '0', 'system');

-- Default Theme
INSERT INTO `themes` (`name`, `primary_color`, `secondary_color`, `accent_color`, `text_color`, `is_active`) VALUES
('Default EV Theme', '#00ff88', '#1a1a2e', '#16213e', '#ffffff', 1);

COMMIT;
