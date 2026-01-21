-- ================================================================
-- DISTRIBUTOR APPLICATION - UPDATED DATABASE SCHEMA
-- Generated: 21 Januari 2026
-- Status: Production Ready
-- ================================================================

-- Main Database
CREATE DATABASE IF NOT EXISTS `distributor` 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `distributor`;

-- ================================================================
-- TABLE STRUCTURES
-- ================================================================

-- Roles Table
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Perusahaan (Company) Table
CREATE TABLE `perusahaan` (
  `id_perusahaan` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_perusahaan` varchar(150) NOT NULL,
  `alamat` text DEFAULT NULL,
  `kontak` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_perusahaan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orang (People) Table
CREATE TABLE `orang` (
  `id_orang` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `perusahaan_id` int(10) unsigned DEFAULT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `alamat` text DEFAULT NULL,
  `kontak` varchar(50) DEFAULT NULL,
  `province_id` int(10) unsigned DEFAULT NULL,
  `regency_id` int(10) unsigned DEFAULT NULL,
  `district_id` int(10) unsigned DEFAULT NULL,
  `village_id` int(10) unsigned DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `tipe_alamat` enum('rumah','kantor','gudang','toko','pabrik','lainnya') DEFAULT NULL,
  `is_supplier` tinyint(1) NOT NULL DEFAULT 0,
  `is_customer` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_orang`),
  KEY `perusahaan_id` (`perusahaan_id`),
  KEY `tipe_alamat` (`tipe_alamat`),
  CONSTRAINT `orang_ibfk_1` FOREIGN KEY (`perusahaan_id`) REFERENCES `perusahaan` (`id_perusahaan`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Accounts Table (FIXED - replaces 'user' table)
CREATE TABLE `user_accounts` (
  `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_orang` int(10) unsigned NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(10) unsigned NOT NULL DEFAULT 3,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `id_orang` (`id_orang`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_accounts_ibfk_1` FOREIGN KEY (`id_orang`) REFERENCES `orang` (`id_orang`) ON DELETE CASCADE,
  CONSTRAINT `user_accounts_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product Categories Table
CREATE TABLE `product_categories` (
  `id_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_category`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products Table
CREATE TABLE `products` (
  `id_product` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `purchase_price` decimal(15,2) DEFAULT 0.00,
  `selling_price` decimal(15,2) DEFAULT 0.00,
  `stock` decimal(15,2) DEFAULT 0.00,
  `min_stock` decimal(15,2) DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_product`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Purchases Table
CREATE TABLE `purchases` (
  `id_purchase` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `purchase_date` date NOT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `discount` decimal(15,2) DEFAULT 0.00,
  `tax` decimal(15,2) DEFAULT 0.00,
  `grand_total` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_purchase`),
  KEY `supplier_id` (`supplier_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `orang` (`id_orang`),
  CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Purchase Items Table
CREATE TABLE `purchase_items` (
  `id_item` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_item`),
  KEY `purchase_id` (`purchase_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id_purchase`) ON DELETE CASCADE,
  CONSTRAINT `purchase_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sales Table
CREATE TABLE `sales` (
  `id_sale` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) unsigned NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `sale_date` date NOT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `discount` decimal(15,2) DEFAULT 0.00,
  `tax` decimal(15,2) DEFAULT 0.00,
  `grand_total` decimal(15,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','partial') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_sale`),
  KEY `customer_id` (`customer_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `orang` (`id_orang`),
  CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sale Items Table
CREATE TABLE `sale_items` (
  `id_item` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_item`),
  KEY `sale_id` (`sale_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id_sale`) ON DELETE CASCADE,
  CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders Table
CREATE TABLE `orders` (
  `id_order` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) unsigned NOT NULL,
  `order_number` varchar(50) DEFAULT NULL,
  `order_date` date NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_order`),
  KEY `customer_id` (`customer_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `orang` (`id_orang`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user_accounts` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order Items Table
CREATE TABLE `order_items` (
  `id_item` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_item`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- ADDRESS DATABASE (alamat_db) - Reference
-- ================================================================

CREATE DATABASE IF NOT EXISTS `alamat_db` 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `alamat_db`;

-- Provinces Table
CREATE TABLE `provinces` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Regencies Table
CREATE TABLE `regencies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `province_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `province_id` (`province_id`),
  CONSTRAINT `regencies_ibfk_1` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Districts Table
CREATE TABLE `districts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `regency_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `regency_id` (`regency_id`),
  CONSTRAINT `districts_ibfk_1` FOREIGN KEY (`regency_id`) REFERENCES `regencies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Villages Table
CREATE TABLE `villages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `district_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `district_id` (`district_id`),
  CONSTRAINT `villages_ibfk_1` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- INITIAL DATA
-- ================================================================

USE `distributor`;

-- Insert Roles
INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'owner'),
(2, 'manager'),
(3, 'staff');

-- Insert Product Categories
INSERT INTO `product_categories` (`id_category`, `name`) VALUES
(1, 'Makanan'),
(2, 'Minuman'),
(3, 'Snack'),
(4, 'Rokok'),
(5, 'Lainnya');

-- Insert Sample Products
INSERT INTO `products` (`id_product`, `category_id`, `code`, `name`, `unit`, `purchase_price`, `selling_price`, `stock`) VALUES
(1, 1, 'BRG001', 'Indomie Goreng', 'pcs', 2000.00, 2500.00, 100),
(2, 2, 'BRG002', 'Coca Cola 330ml', 'pcs', 5000.00, 6000.00, 50),
(3, 3, 'BRG003', 'Chips Qtela', 'pcs', 8000.00, 10000.00, 30);

-- ================================================================
-- NOTES & IMPORTANT CHANGES
-- ================================================================

/*
IMPORTANT CHANGES MADE:

1. TABLE NAME FIXES:
   - 'user' → 'user_accounts' (due to foreign key conflicts)
   - 'branches' table removed (corrupted, not needed for basic functionality)

2. NEW COLUMNS:
   - orang.tipe_alamat: ENUM for address types
   - user_accounts.status_aktif: User status management
   - user_accounts.last_login_at: Login tracking

3. FOREIGN KEY RELATIONSHIPS:
   - user_accounts.id_orang → orang.id_orang
   - user_accounts.role_id → roles.id
   - orang.perusahaan_id → perusahaan.id_perusahaan

4. SECURITY IMPROVEMENTS:
   - All passwords use bcrypt hashing
   - CSRF tokens implemented
   - Input sanitization with prepared statements

5. CROSS-PLATFORM COMPATIBILITY:
   - Environment detection functions
   - Multiple config files for Windows/Linux
   - Universal database connection handling

6. LOGIN IMPROVEMENTS:
   - Output buffering for clean redirects
   - Cache control headers
   - JavaScript fallback for browser issues
   - Enhanced error handling

STATUS: PRODUCTION READY
*/
