-- =====================================================
-- DATABASE BARANG - Product & Inventory Management
-- =====================================================
-- Created: 19 Januari 2026
-- Purpose: Manajemen produk, inventory, gudang, dan pricing
-- Integration: Link ke orang untuk supplier, alamat_db untuk lokasi gudang

CREATE DATABASE IF NOT EXISTS barang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE barang;

-- =====================================================
-- 1. CATEGORIES - Kategori Produk
-- =====================================================
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id BIGINT UNSIGNED NULL COMMENT 'Kategori induk',
    name VARCHAR(100) NOT NULL COMMENT 'Nama kategori',
    code VARCHAR(50) UNIQUE NOT NULL COMMENT 'Kode kategori',
    description TEXT NULL COMMENT 'Deskripsi kategori',
    image_url VARCHAR(500) NULL COMMENT 'URL gambar kategori',
    level TINYINT DEFAULT 1 COMMENT 'Level kedalaman (1=root)',
    sort_order INT DEFAULT 0 COMMENT 'Urutan sorting',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    
    INDEX idx_parent_id (parent_id),
    INDEX idx_code (code),
    INDEX idx_name (name),
    INDEX idx_level (level),
    INDEX idx_sort_order (sort_order),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Kategori produk hierarkis';

-- =====================================================
-- 2. BRANDS - Merek Produk
-- =====================================================
CREATE TABLE brands (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL COMMENT 'Nama merek',
    code VARCHAR(50) UNIQUE NULL COMMENT 'Kode merek',
    description TEXT NULL COMMENT 'Deskripsi merek',
    logo_url VARCHAR(500) NULL COMMENT 'URL logo merek',
    website VARCHAR(255) NULL COMMENT 'Website merek',
    is_popular BOOLEAN DEFAULT FALSE COMMENT 'Merek populer',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_name (name),
    INDEX idx_code (code),
    INDEX idx_is_popular (is_popular),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Merek produk';

-- =====================================================
-- 3. UNITS - Satuan Unit
-- =====================================================
CREATE TABLE units (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL COMMENT 'Nama satuan (pcs, kg, liter)',
    code VARCHAR(10) UNIQUE NOT NULL COMMENT 'Kode satuan',
    description VARCHAR(255) NULL COMMENT 'Deskripsi satuan',
    unit_type ENUM('quantity', 'weight', 'volume', 'length', 'area', 'time', 'other') DEFAULT 'quantity',
    base_unit_id BIGINT UNSIGNED NULL COMMENT 'Satuan dasar untuk konversi',
    conversion_factor DECIMAL(10,4) DEFAULT 1.0000 COMMENT 'Faktor konversi ke satuan dasar',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (base_unit_id) REFERENCES units(id) ON DELETE SET NULL,
    
    INDEX idx_name (name),
    INDEX idx_code (code),
    INDEX idx_unit_type (unit_type),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Satuan unit produk';

-- =====================================================
-- 4. WAREHOUSES - Gudang
-- =====================================================
CREATE TABLE warehouses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Kode gudang',
    name VARCHAR(100) NOT NULL COMMENT 'Nama gudang',
    description TEXT NULL COMMENT 'Deskripsi gudang',
    village_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.villages',
    district_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.districts',
    regency_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.regencies',
    province_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.provinces',
    address VARCHAR(255) NOT NULL COMMENT 'Alamat lengkap',
    postal_code VARCHAR(10) NULL COMMENT 'Kode pos',
    phone VARCHAR(20) NULL COMMENT 'Telepon gudang',
    email VARCHAR(255) NULL COMMENT 'Email gudang',
    manager_id BIGINT UNSIGNED NULL COMMENT 'ID manager gudang (link ke orang.users)',
    warehouse_type ENUM('main', 'branch', 'transit', 'return', 'virtual') DEFAULT 'branch',
    capacity DECIMAL(15,2) NULL COMMENT 'Kapasitas gudang (m³)',
    area DECIMAL(10,2) NULL COMMENT 'Luas gudang (m²)',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_code (code),
    INDEX idx_name (name),
    INDEX idx_village_id (village_id),
    INDEX idx_district_id (district_id),
    INDEX idx_regency_id (regency_id),
    INDEX idx_province_id (province_id),
    INDEX idx_manager_id (manager_id),
    INDEX idx_warehouse_type (warehouse_type),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Data gudang';

-- =====================================================
-- 5. PRODUCTS - Master Produk
-- =====================================================
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) UNIQUE NOT NULL COMMENT 'Stock Keeping Unit',
    barcode VARCHAR(50) UNIQUE NULL COMMENT 'Barcode/EAN',
    name VARCHAR(200) NOT NULL COMMENT 'Nama produk',
    description TEXT NULL COMMENT 'Deskripsi produk',
    category_id BIGINT UNSIGNED NOT NULL,
    brand_id BIGINT UNSIGNED NULL,
    unit_id BIGINT UNSIGNED NOT NULL COMMENT 'Satuan dasar',
    weight DECIMAL(10,3) NULL COMMENT 'Berat (kg)',
    length DECIMAL(8,2) NULL COMMENT 'Panjang (cm)',
    width DECIMAL(8,2) NULL COMMENT 'Lebar (cm)',
    height DECIMAL(8,2) NULL COMMENT 'Tinggi (cm)',
    volume DECIMAL(10,3) NULL COMMENT 'Volume (m³)',
    min_stock_level DECIMAL(12,2) DEFAULT 0 COMMENT 'Stok minimum',
    max_stock_level DECIMAL(12,2) DEFAULT 0 COMMENT 'Stok maksimum',
    reorder_point DECIMAL(12,2) DEFAULT 0 COMMENT 'Titik order ulang',
    lead_time_days SMALLINT DEFAULT 0 COMMENT 'Lead time (hari)',
    cost_price DECIMAL(15,2) DEFAULT 0 COMMENT 'Harga beli',
    selling_price DECIMAL(15,2) DEFAULT 0 COMMENT 'Harga jual',
    margin_percent DECIMAL(5,2) DEFAULT 0 COMMENT 'Persentase margin',
    is_taxable BOOLEAN DEFAULT TRUE COMMENT 'Kena pajak',
    tax_rate DECIMAL(5,2) DEFAULT 0 COMMENT 'Persentase pajak',
    is_active BOOLEAN DEFAULT TRUE,
    is_discontinued BOOLEAN DEFAULT FALSE COMMENT 'Dihentikan produksi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
    FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE RESTRICT,
    
    INDEX idx_sku (sku),
    INDEX idx_barcode (barcode),
    INDEX idx_name (name),
    INDEX idx_category_id (category_id),
    INDEX idx_brand_id (brand_id),
    INDEX idx_unit_id (unit_id),
    INDEX idx_cost_price (cost_price),
    INDEX idx_selling_price (selling_price),
    INDEX idx_is_active (is_active),
    INDEX idx_is_discontinued (is_discontinued)
) ENGINE=InnoDB COMMENT='Master data produk';

-- =====================================================
-- 6. PRODUCT_IMAGES - Gambar Produk
-- =====================================================
CREATE TABLE product_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    image_url VARCHAR(500) NOT NULL COMMENT 'URL gambar',
    image_path VARCHAR(500) NULL COMMENT 'Path file di server',
    alt_text VARCHAR(255) NULL COMMENT 'Alt text untuk gambar',
    sort_order INT DEFAULT 0 COMMENT 'Urutan tampil',
    is_primary BOOLEAN DEFAULT FALSE COMMENT 'Gambar utama',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    
    INDEX idx_product_id (product_id),
    INDEX idx_sort_order (sort_order),
    INDEX idx_is_primary (is_primary),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Gambar-gambar produk';

-- =====================================================
-- 7. PRODUCT_ATTRIBUTES - Atribut Produk
-- =====================================================
CREATE TABLE product_attributes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    attribute_name VARCHAR(100) NOT NULL COMMENT 'Nama atribut (warna, ukuran, dll)',
    attribute_value VARCHAR(255) NOT NULL COMMENT 'Nilai atribut',
    attribute_type ENUM('text', 'number', 'boolean', 'date', 'select', 'multiselect') DEFAULT 'text',
    is_variant BOOLEAN DEFAULT FALSE COMMENT 'Atribut untuk variant',
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    
    INDEX idx_product_id (product_id),
    INDEX idx_attribute_name (attribute_name),
    INDEX idx_is_variant (is_variant),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Atribut-atribut tambahan produk';

-- =====================================================
-- 8. PRODUCT_VARIANTS - Variant Produk
-- =====================================================
CREATE TABLE product_variants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    sku VARCHAR(50) UNIQUE NOT NULL COMMENT 'SKU variant',
    barcode VARCHAR(50) UNIQUE NULL COMMENT 'Barcode variant',
    variant_name VARCHAR(200) NOT NULL COMMENT 'Nama variant',
    variant_combination JSON NULL COMMENT 'Kombinasi atribut variant',
    weight DECIMAL(10,3) NULL COMMENT 'Berat variant (kg)',
    cost_price DECIMAL(15,2) DEFAULT 0 COMMENT 'Harga beli variant',
    selling_price DECIMAL(15,2) DEFAULT 0 COMMENT 'Harga jual variant',
    min_stock_level DECIMAL(12,2) DEFAULT 0,
    max_stock_level DECIMAL(12,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    
    INDEX idx_sku (sku),
    INDEX idx_barcode (barcode),
    INDEX idx_product_id (product_id),
    INDEX idx_cost_price (cost_price),
    INDEX idx_selling_price (selling_price),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Variant produk (ukuran, warna, dll)';

-- =====================================================
-- 9. SUPPLIERS - Supplier Produk
-- =====================================================
CREATE TABLE suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Kode supplier',
    name VARCHAR(200) NOT NULL COMMENT 'Nama supplier',
    contact_person VARCHAR(100) NULL COMMENT 'Nama kontak',
    phone VARCHAR(20) NULL COMMENT 'Telepon',
    email VARCHAR(255) NULL COMMENT 'Email',
    village_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.villages',
    district_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.districts',
    regency_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.regencies',
    province_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.provinces',
    address VARCHAR(255) NULL COMMENT 'Alamat lengkap',
    postal_code VARCHAR(10) NULL COMMENT 'Kode pos',
    npwp VARCHAR(25) NULL COMMENT 'NPWP supplier',
    payment_terms VARCHAR(100) NULL COMMENT 'Syarat pembayaran',
    credit_limit DECIMAL(15,2) DEFAULT 0 COMMENT 'Limit kredit',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_code (code),
    INDEX idx_name (name),
    INDEX idx_village_id (village_id),
    INDEX idx_district_id (district_id),
    INDEX idx_regency_id (regency_id),
    INDEX idx_province_id (province_id),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Data supplier';

-- =====================================================
-- 10. PRODUCT_SUPPLIERS - Hubungan Produk dengan Supplier
-- =====================================================
CREATE TABLE product_suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    supplier_id BIGINT UNSIGNED NOT NULL,
    supplier_sku VARCHAR(50) NULL COMMENT 'SKU di supplier',
    supplier_product_name VARCHAR(200) NULL COMMENT 'Nama produk di supplier',
    cost_price DECIMAL(15,2) DEFAULT 0 COMMENT 'Harga beli dari supplier',
    min_order_qty DECIMAL(12,2) DEFAULT 1 COMMENT 'Minimum order quantity',
    lead_time_days SMALLINT DEFAULT 0 COMMENT 'Lead time dari supplier',
    is_primary BOOLEAN DEFAULT FALSE COMMENT 'Supplier utama',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_product_supplier (product_id, supplier_id),
    INDEX idx_product_id (product_id),
    INDEX idx_supplier_id (supplier_id),
    INDEX idx_cost_price (cost_price),
    INDEX idx_is_primary (is_primary),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Hubungan produk dengan supplier';

-- =====================================================
-- 11. INVENTORY - Stok Produk per Gudang
-- =====================================================
CREATE TABLE inventory (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    variant_id BIGINT UNSIGNED NULL COMMENT 'ID variant jika ada',
    quantity_on_hand DECIMAL(12,2) DEFAULT 0 COMMENT 'Stok tersedia',
    quantity_reserved DECIMAL(12,2) DEFAULT 0 COMMENT 'Stok di-reserve',
    quantity_available DECIMAL(12,2) GENERATED ALWAYS AS (quantity_on_hand - quantity_reserved) STORED,
    reorder_point DECIMAL(12,2) DEFAULT 0 COMMENT 'Titik order ulang',
    max_stock DECIMAL(12,2) DEFAULT 0 COMMENT 'Stok maksimum',
    average_cost DECIMAL(15,2) DEFAULT 0 COMMENT 'Harga beli rata-rata',
    last_count_date DATE NULL COMMENT 'Tanggal stock opname terakhir',
    last_count_quantity DECIMAL(12,2) NULL COMMENT 'Hasil stock opname terakhir',
    variance_quantity DECIMAL(12,2) DEFAULT 0 COMMENT 'Selisih stock opname',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_product_warehouse (product_id, warehouse_id, variant_id),
    INDEX idx_product_id (product_id),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_variant_id (variant_id),
    INDEX idx_quantity_on_hand (quantity_on_hand),
    INDEX idx_quantity_available (quantity_available),
    INDEX idx_reorder_point (reorder_point),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Stok produk per gudang';

-- =====================================================
-- 12. PRICE_LISTS - Daftar Harga
-- =====================================================
CREATE TABLE price_lists (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Nama price list',
    description TEXT NULL COMMENT 'Deskripsi price list',
    price_list_type ENUM('standard', 'customer_specific', 'promotion', 'special') DEFAULT 'standard',
    customer_id BIGINT UNSIGNED NULL COMMENT 'Customer khusus (link ke orang.persons)',
    valid_from DATE NOT NULL COMMENT 'Berlaku dari',
    valid_until DATE NULL COMMENT 'Berlaku sampai',
    currency VARCHAR(3) DEFAULT 'IDR' COMMENT 'Mata uang',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_name (name),
    INDEX idx_price_list_type (price_list_type),
    INDEX idx_customer_id (customer_id),
    INDEX idx_valid_from (valid_from),
    INDEX idx_valid_until (valid_until),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Daftar harga';

-- =====================================================
-- 13. PRICE_LIST_ITEMS - Item Daftar Harga
-- =====================================================
CREATE TABLE price_list_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    price_list_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    variant_id BIGINT UNSIGNED NULL COMMENT 'ID variant jika ada',
    unit_id BIGINT UNSIGNED NOT NULL,
    price DECIMAL(15,2) NOT NULL COMMENT 'Harga',
    discount_percent DECIMAL(5,2) DEFAULT 0 COMMENT 'Diskon persentase',
    discount_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Diskon nominal',
    net_price DECIMAL(15,2) GENERATED ALWAYS AS (price - (price * discount_percent/100) - discount_amount) STORED,
    min_qty DECIMAL(12,2) DEFAULT 1 COMMENT 'Minimum quantity',
    max_qty DECIMAL(12,2) NULL COMMENT 'Maksimum quantity',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (price_list_id) REFERENCES price_lists(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE RESTRICT,
    
    UNIQUE KEY unique_price_list_product (price_list_id, product_id, variant_id, unit_id),
    INDEX idx_price_list_id (price_list_id),
    INDEX idx_product_id (product_id),
    INDEX idx_variant_id (variant_id),
    INDEX idx_unit_id (unit_id),
    INDEX idx_price (price),
    INDEX idx_net_price (net_price),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Item-item daftar harga';

-- =====================================================
-- 14. STOCK_MOVEMENTS - Pergerakan Stok
-- =====================================================
CREATE TABLE stock_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    variant_id BIGINT UNSIGNED NULL,
    movement_type ENUM('in', 'out', 'transfer', 'adjustment', 'return') NOT NULL,
    reference_type ENUM('purchase', 'sale', 'transfer', 'adjustment', 'production', 'return') NOT NULL,
    reference_id BIGINT UNSIGNED NULL COMMENT 'ID referensi transaksi',
    quantity DECIMAL(12,2) NOT NULL COMMENT 'Quantity (positif untuk masuk, negatif untuk keluar)',
    unit_cost DECIMAL(15,2) DEFAULT 0 COMMENT 'Cost per unit',
    total_cost DECIMAL(15,2) GENERATED ALWAYS AS (ABS(quantity) * unit_cost) STORED,
    balance_before DECIMAL(12,2) NOT NULL COMMENT 'Saldo sebelum movement',
    balance_after DECIMAL(12,2) NOT NULL COMMENT 'Saldo setelah movement',
    movement_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes VARCHAR(255) NULL COMMENT 'Catatan movement',
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE RESTRICT,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_product_id (product_id),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_variant_id (variant_id),
    INDEX idx_movement_type (movement_type),
    INDEX idx_reference_type (reference_type),
    INDEX idx_reference_id (reference_id),
    INDEX idx_movement_date (movement_date),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB COMMENT='Histori pergerakan stok';

-- =====================================================
-- INSERT DEFAULT DATA
-- =====================================================

-- Default Units
INSERT INTO units (name, code, description, unit_type) VALUES
('Pieces', 'pcs', 'Pieces/Unit', 'quantity'),
('Kilogram', 'kg', 'Kilogram', 'weight'),
('Gram', 'gr', 'Gram', 'weight'),
('Liter', 'ltr', 'Liter', 'volume'),
('Milliliter', 'ml', 'Milliliter', 'volume'),
('Meter', 'm', 'Meter', 'length'),
('Centimeter', 'cm', 'Centimeter', 'length'),
('Box', 'box', 'Box', 'quantity'),
('Carton', 'ctn', 'Carton', 'quantity'),
('Dozen', 'dz', 'Dozen (12 pieces)', 'quantity');

-- Default Categories
INSERT INTO categories (name, code, description, level) VALUES
('General', 'GEN', 'General Products', 1),
('Food & Beverage', 'F&B', 'Food and Beverage Products', 1),
('Electronics', 'ELC', 'Electronic Products', 1),
('Clothing', 'CLT', 'Clothing and Apparel', 1),
('Home & Garden', 'HNG', 'Home and Garden Products', 1),
('Health & Beauty', 'HLB', 'Health and Beauty Products', 1),
('Sports & Outdoors', 'SPO', 'Sports and Outdoor Equipment', 1),
('Toys & Games', 'TYG', 'Toys and Games', 1);

-- =====================================================
-- VIEWS untuk kemudahan query
-- =====================================================

-- View untuk produk lengkap dengan kategori dan brand
CREATE VIEW v_products_complete AS
SELECT 
    p.*,
    c.name as category_name,
    c.code as category_code,
    b.name as brand_name,
    u.name as unit_name,
    i.total_quantity as total_stock,
    i.total_available as total_available,
    COUNT(pi.id) as image_count
FROM products p
JOIN categories c ON p.category_id = c.id
LEFT JOIN brands b ON p.brand_id = b.id
JOIN units u ON p.unit_id = u.id
LEFT JOIN (
    SELECT product_id, SUM(quantity_on_hand) as total_quantity, SUM(quantity_available) as total_available
    FROM inventory 
    WHERE is_active = TRUE
    GROUP BY product_id
) i ON p.id = i.product_id
LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_active = TRUE
WHERE p.is_active = TRUE
GROUP BY p.id;

-- View untuk inventory lengkap
CREATE VIEW v_inventory_complete AS
SELECT 
    inv.*,
    p.sku,
    p.name as product_name,
    p.barcode,
    w.name as warehouse_name,
    w.code as warehouse_code,
    u.name as unit_name,
    CASE 
        WHEN inv.quantity_available <= inv.reorder_point THEN 'Low Stock'
        WHEN inv.quantity_available <= inv.min_stock_level THEN 'Critical'
        ELSE 'Normal'
    END as stock_status
FROM inventory inv
JOIN products p ON inv.product_id = p.id
JOIN warehouses w ON inv.warehouse_id = w.id
JOIN units u ON p.unit_id = u.id
WHERE inv.is_active = TRUE;
