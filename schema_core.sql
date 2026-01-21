CREATE DATABASE IF NOT EXISTS distributor
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE distributor;

CREATE TABLE IF NOT EXISTS roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO roles (name) VALUES ('owner'), ('manager'), ('staff')
ON DUPLICATE KEY UPDATE name = VALUES(name);

CREATE TABLE IF NOT EXISTS perusahaan (
    id_perusahaan INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_perusahaan VARCHAR(150) NOT NULL,
    alamat TEXT,
    kontak VARCHAR(50),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS branches (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    perusahaan_id INT UNSIGNED NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    province_id INT UNSIGNED NULL,
    regency_id INT UNSIGNED NULL,
    district_id INT UNSIGNED NULL,
    village_id INT UNSIGNED NULL,
    street_address TEXT,
    postal_code VARCHAR(10),
    owner_id INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_branches_perusahaan FOREIGN KEY (perusahaan_id) REFERENCES perusahaan(id_perusahaan)
);

CREATE TABLE IF NOT EXISTS orang (
    id_orang INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    perusahaan_id INT UNSIGNED NULL,
    nama_lengkap VARCHAR(150) NOT NULL,
    alamat TEXT,
    kontak VARCHAR(50),
    province_id INT UNSIGNED NULL,
    regency_id INT UNSIGNED NULL,
    district_id INT UNSIGNED NULL,
    village_id INT UNSIGNED NULL,
    postal_code VARCHAR(10),
    is_supplier TINYINT(1) NOT NULL DEFAULT 0,
    is_customer TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orang_perusahaan FOREIGN KEY (perusahaan_id) REFERENCES perusahaan(id_perusahaan)
);

CREATE TABLE IF NOT EXISTS user (
    id_user INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_orang INT UNSIGNED NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    branch_id INT UNSIGNED NULL,
    status_aktif TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_user_orang (id_orang),
    CONSTRAINT fk_user_orang FOREIGN KEY (id_orang) REFERENCES orang(id_orang),
    CONSTRAINT fk_user_role FOREIGN KEY (role_id) REFERENCES roles(id),
    CONSTRAINT fk_user_branch FOREIGN KEY (branch_id) REFERENCES branches(id)
);

CREATE TABLE IF NOT EXISTS product_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO product_categories (name) VALUES
('Beras'),
('Minyak Goreng'),
('Gula'),
('Telur'),
('Bawang')
ON DUPLICATE KEY UPDATE name = VALUES(name);

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    barcode VARCHAR(100) NULL,
    category_id INT UNSIGNED NULL,
    buy_price DECIMAL(15,2) NOT NULL DEFAULT 0,
    sell_price DECIMAL(15,2) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES product_categories(id)
);

INSERT INTO products (code, name, unit, barcode, category_id, buy_price, sell_price, is_active) VALUES
('BERAS_MEDIUM', 'Beras Medium', 'KG', NULL, (SELECT id FROM product_categories WHERE name = 'Beras' LIMIT 1), 0, 0, 1),
('MINYAK_GORENG_SAWIT', 'Minyak Goreng Sawit', 'LITER', NULL, (SELECT id FROM product_categories WHERE name = 'Minyak Goreng' LIMIT 1), 0, 0, 1),
('GULA_PASIR', 'Gula Pasir', 'KG', NULL, (SELECT id FROM product_categories WHERE name = 'Gula' LIMIT 1), 0, 0, 1),
('TELUR_AYAM_RAS', 'Telur Ayam Ras', 'PCS', NULL, (SELECT id FROM product_categories WHERE name = 'Telur' LIMIT 1), 0, 0, 1),
('BAWANG_MERAH', 'Bawang Merah', 'KG', NULL, (SELECT id FROM product_categories WHERE name = 'Bawang' LIMIT 1), 0, 0, 1)
ON DUPLICATE KEY UPDATE
name = VALUES(name),
unit = VALUES(unit),
barcode = VALUES(barcode),
category_id = VALUES(category_id),
buy_price = VALUES(buy_price),
sell_price = VALUES(sell_price),
is_active = VALUES(is_active);

CREATE TABLE IF NOT EXISTS purchases (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id INT UNSIGNED NOT NULL,
    supplier_id INT UNSIGNED NULL,
    supplier_name VARCHAR(150) NOT NULL,
    invoice_no VARCHAR(50) NULL,
    purchase_date DATE NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    notes TEXT,
    created_by INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_purchases_branch FOREIGN KEY (branch_id) REFERENCES branches(id),
    CONSTRAINT fk_purchases_supplier FOREIGN KEY (supplier_id) REFERENCES orang(id_orang),
    CONSTRAINT fk_purchases_user FOREIGN KEY (created_by) REFERENCES user(id_user)
);

CREATE TABLE IF NOT EXISTS purchase_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    qty DECIMAL(15,3) NOT NULL,
    price DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    CONSTRAINT fk_purchase_items_purchase FOREIGN KEY (purchase_id) REFERENCES purchases(id),
    CONSTRAINT fk_purchase_items_product FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS sales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NULL,
    customer_name VARCHAR(150) NOT NULL,
    invoice_no VARCHAR(50) NULL,
    sale_date DATE NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    notes TEXT,
    created_by INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sales_branch FOREIGN KEY (branch_id) REFERENCES branches(id),
    CONSTRAINT fk_sales_customer FOREIGN KEY (customer_id) REFERENCES orang(id_orang),
    CONSTRAINT fk_sales_user FOREIGN KEY (created_by) REFERENCES user(id_user)
);

CREATE TABLE IF NOT EXISTS sale_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    qty DECIMAL(15,3) NOT NULL,
    price DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    CONSTRAINT fk_sale_items_sale FOREIGN KEY (sale_id) REFERENCES sales(id),
    CONSTRAINT fk_sale_items_product FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id INT UNSIGNED NOT NULL,
    customer_name VARCHAR(150) NOT NULL,
    order_date DATE NOT NULL,
    required_date DATE NULL,
    raw_text LONGTEXT NULL,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    status ENUM('draft', 'diproses', 'selesai', 'parsial') NOT NULL DEFAULT 'draft',
    created_by INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_branch FOREIGN KEY (branch_id) REFERENCES branches(id),
    CONSTRAINT fk_orders_user FOREIGN KEY (created_by) REFERENCES user(id_user)
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    seq_no INT UNSIGNED NOT NULL DEFAULT 0,
    product_id INT UNSIGNED NULL,
    description VARCHAR(255) NOT NULL,
    qty DECIMAL(15,3) NOT NULL DEFAULT 0,
    unit VARCHAR(50) NOT NULL,
    notes VARCHAR(255) NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id),
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS branch_product_prices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    buy_price DECIMAL(15,2) NOT NULL DEFAULT 0,
    sell_price DECIMAL(15,2) NOT NULL DEFAULT 0,
    effective_date DATE NOT NULL DEFAULT CURRENT_DATE,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_bpp_branch FOREIGN KEY (branch_id) REFERENCES branches(id),
    CONSTRAINT fk_bpp_product FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE KEY uk_bpp_branch_product_date (branch_id, product_id, effective_date)
);

CREATE TABLE IF NOT EXISTS product_plu_mapping (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    plu_code_id BIGINT UNSIGNED NOT NULL,
    province_id INT UNSIGNED NULL,
    local_name VARCHAR(150) NULL,
    is_primary_plu TINYINT(1) NOT NULL DEFAULT 1,
    effective_date DATE NOT NULL DEFAULT CURRENT_DATE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_plu_product FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_product_plu_plu (plu_code_id),
    INDEX idx_product_plu_province (province_id),
    INDEX idx_product_plu_product (product_id)
);

CREATE TABLE IF NOT EXISTS product_barcodes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    plu_code_id BIGINT UNSIGNED NULL,
    barcode_type VARCHAR(20) NOT NULL,
    barcode_value VARCHAR(50) NOT NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 1,
    description VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_barcodes_product FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE KEY uk_product_barcodes_value (barcode_value),
    INDEX idx_product_barcodes_product (product_id),
    INDEX idx_product_barcodes_plu (plu_code_id),
    INDEX idx_product_barcodes_type (barcode_type)
);

INSERT INTO product_plu_mapping (product_id, plu_code_id, province_id, local_name, is_primary_plu, effective_date)
SELECT p.id, pc.id, NULL, p.name, 1, CURRENT_DATE
FROM products p
JOIN plu_codes pc ON p.code = 'BAWANG_MERAH' AND pc.plu_number = '4086'
WHERE NOT EXISTS (
    SELECT 1 FROM product_plu_mapping m
    WHERE m.product_id = p.id AND m.plu_code_id = pc.id
);

INSERT INTO product_barcodes (product_id, plu_code_id, barcode_type, barcode_value, is_primary, description, is_active)
SELECT p.id, pc.id, 'PLU', pc.plu_number, 1, CONCAT('PLU ', pc.plu_number, ' untuk ', p.name), 1
FROM products p
JOIN plu_codes pc ON p.code = 'BAWANG_MERAH' AND pc.plu_number = '4086'
WHERE NOT EXISTS (
    SELECT 1 FROM product_barcodes b
    WHERE b.barcode_value = pc.plu_number
);
