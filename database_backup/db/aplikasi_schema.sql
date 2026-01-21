-- =====================================================
-- DATABASE APLIKASI - Main Application Database
-- =====================================================
-- Created: 19 Januari 2026
-- Purpose: Database utama aplikasi distributor
-- Integration: Central hub yang menghubungkan semua database

CREATE DATABASE IF NOT EXISTS aplikasi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aplikasi;

-- =====================================================
-- 1. SYSTEM_CONFIGURATIONS - Konfigurasi Sistem
-- =====================================================
CREATE TABLE system_configurations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) UNIQUE NOT NULL COMMENT 'Key konfigurasi',
    config_value TEXT NULL COMMENT 'Value konfigurasi',
    config_type ENUM('string', 'number', 'boolean', 'json', 'array') DEFAULT 'string',
    description TEXT NULL COMMENT 'Deskripsi konfigurasi',
    category VARCHAR(50) DEFAULT 'general' COMMENT 'Kategori konfigurasi',
    is_system BOOLEAN DEFAULT FALSE COMMENT 'Konfigurasi sistem',
    is_encrypted BOOLEAN DEFAULT FALSE COMMENT 'Value di-encrypt',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by BIGINT UNSIGNED NULL,
    
    INDEX idx_config_key (config_key),
    INDEX idx_category (category),
    INDEX idx_is_system (is_system),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Konfigurasi sistem aplikasi';

-- =====================================================
-- 2. SALES - Transaksi Penjualan
-- =====================================================
CREATE TABLE sales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode transaksi',
    customer_id BIGINT UNSIGNED NOT NULL COMMENT 'Customer (link ke orang.persons)',
    warehouse_id BIGINT UNSIGNED NOT NULL COMMENT 'Gudang (link ke barang.warehouses)',
    sale_date DATE NOT NULL COMMENT 'Tanggal transaksi',
    sale_type ENUM('cash', 'credit', 'consignment', 'preorder') DEFAULT 'cash',
    payment_terms VARCHAR(100) NULL COMMENT 'Syarat pembayaran',
    due_date DATE NULL COMMENT 'Jatuh tempo',
    subtotal DECIMAL(15,2) DEFAULT 0 COMMENT 'Subtotal',
    discount_percent DECIMAL(5,2) DEFAULT 0 COMMENT 'Diskon persentase',
    discount_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Diskon nominal',
    tax_percent DECIMAL(5,2) DEFAULT 0 COMMENT 'Pajak persentase',
    tax_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Jumlah pajak',
    total_amount DECIMAL(15,2) GENERATED ALWAYS AS (subtotal - discount_amount + tax_amount) STORED,
    paid_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Sudah dibayar',
    remaining_amount DECIMAL(15,2) GENERATED ALWAYS AS (total_amount - paid_amount) STORED,
    status ENUM('draft', 'confirmed', 'processing', 'shipped', 'delivered', 'completed', 'cancelled') DEFAULT 'draft',
    priority_level TINYINT DEFAULT 3 COMMENT 'Prioritas (1=highest, 5=lowest)',
    notes TEXT NULL COMMENT 'Catatan transaksi',
    internal_notes TEXT NULL COMMENT 'Catatan internal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (customer_id) REFERENCES orang.persons(id) ON DELETE RESTRICT,
    FOREIGN KEY (warehouse_id) REFERENCES barang.warehouses(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_sale_code (sale_code),
    INDEX idx_customer_id (customer_id),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_sale_date (sale_date),
    INDEX idx_due_date (due_date),
    INDEX idx_status (status),
    INDEX idx_total_amount (total_amount),
    INDEX idx_remaining_amount (remaining_amount),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB COMMENT='Transaksi penjualan';

-- =====================================================
-- 3. SALE_ITEMS - Item Penjualan
-- =====================================================
CREATE TABLE sale_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    variant_id BIGINT UNSIGNED NULL COMMENT 'Variant produk',
    unit_id BIGINT UNSIGNED NOT NULL,
    quantity DECIMAL(12,2) NOT NULL COMMENT 'Quantity',
    unit_price DECIMAL(15,2) NOT NULL COMMENT 'Harga per unit',
    discount_percent DECIMAL(5,2) DEFAULT 0 COMMENT 'Diskon persentase',
    discount_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Diskon nominal',
    tax_percent DECIMAL(5,2) DEFAULT 0 COMMENT 'Pajak persentase',
    tax_amount DECIMAL(15,2) GENERATED ALWAYS AS ((quantity * unit_price - discount_amount) * tax_percent/100) STORED,
    subtotal DECIMAL(15,2) GENERATED ALWAYS AS (quantity * unit_price - discount_amount) STORED,
    total DECIMAL(15,2) GENERATED ALWAYS AS (subtotal + tax_amount) STORED,
    cost_price DECIMAL(15,2) DEFAULT 0 COMMENT 'Harga beli',
    profit DECIMAL(15,2) GENERATED ALWAYS AS (total - (quantity * cost_price)) STORED,
    profit_percent DECIMAL(5,2) GENERATED ALWAYS AS (CASE WHEN (quantity * cost_price) > 0 THEN (total - (quantity * cost_price)) / (quantity * cost_price) * 100 ELSE 0 END) STORED,
    notes VARCHAR(255) NULL COMMENT 'Catatan item',
    
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES barang.products(id) ON DELETE RESTRICT,
    FOREIGN KEY (variant_id) REFERENCES barang.product_variants(id) ON DELETE SET NULL,
    FOREIGN KEY (unit_id) REFERENCES barang.units(id) ON DELETE RESTRICT,
    
    INDEX idx_sale_id (sale_id),
    INDEX idx_product_id (product_id),
    INDEX idx_variant_id (variant_id),
    INDEX idx_unit_id (unit_id),
    INDEX idx_quantity (quantity),
    INDEX idx_unit_price (unit_price),
    INDEX idx_total (total),
    INDEX idx_profit (profit)
) ENGINE=InnoDB COMMENT='Item-item penjualan';

-- =====================================================
-- 4. PURCHASES - Transaksi Pembelian
-- =====================================================
CREATE TABLE purchases (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    purchase_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode transaksi',
    supplier_id BIGINT UNSIGNED NOT NULL COMMENT 'Supplier (link ke barang.suppliers)',
    warehouse_id BIGINT UNSIGNED NOT NULL COMMENT 'Gudang tujuan',
    purchase_date DATE NOT NULL COMMENT 'Tanggal transaksi',
    expected_date DATE NULL COMMENT 'Tanggal diterima',
    received_date DATE NULL COMMENT 'Tanggal diterima aktual',
    purchase_type ENUM('regular', 'consignment', 'preorder', 'emergency') DEFAULT 'regular',
    payment_terms VARCHAR(100) NULL COMMENT 'Syarat pembayaran',
    due_date DATE NULL COMMENT 'Jatuh tempo',
    subtotal DECIMAL(15,2) DEFAULT 0 COMMENT 'Subtotal',
    discount_percent DECIMAL(5,2) DEFAULT 0 COMMENT 'Diskon persentase',
    discount_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Diskon nominal',
    tax_percent DECIMAL(5,2) DEFAULT 0 COMMENT 'Pajak persentase',
    tax_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Jumlah pajak',
    shipping_cost DECIMAL(15,2) DEFAULT 0 COMMENT 'Biaya pengiriman',
    other_cost DECIMAL(15,2) DEFAULT 0 COMMENT 'Biaya lain-lain',
    total_amount DECIMAL(15,2) GENERATED ALWAYS AS (subtotal - discount_amount + tax_amount + shipping_cost + other_cost) STORED,
    paid_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Sudah dibayar',
    remaining_amount DECIMAL(15,2) GENERATED ALWAYS AS (total_amount - paid_amount) STORED,
    status ENUM('draft', 'ordered', 'partial_received', 'received', 'completed', 'cancelled') DEFAULT 'draft',
    priority_level TINYINT DEFAULT 3 COMMENT 'Prioritas (1=highest, 5=lowest)',
    notes TEXT NULL COMMENT 'Catatan transaksi',
    internal_notes TEXT NULL COMMENT 'Catatan internal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (supplier_id) REFERENCES barang.suppliers(id) ON DELETE RESTRICT,
    FOREIGN KEY (warehouse_id) REFERENCES barang.warehouses(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_purchase_code (purchase_code),
    INDEX idx_supplier_id (supplier_id),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_purchase_date (purchase_date),
    INDEX idx_expected_date (expected_date),
    INDEX idx_received_date (received_date),
    INDEX idx_due_date (due_date),
    INDEX idx_status (status),
    INDEX idx_total_amount (total_amount),
    INDEX idx_remaining_amount (remaining_amount)
) ENGINE=InnoDB COMMENT='Transaksi pembelian';

-- =====================================================
-- 5. PURCHASE_ITEMS - Item Pembelian
-- =====================================================
CREATE TABLE purchase_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    purchase_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    variant_id BIGINT UNSIGNED NULL COMMENT 'Variant produk',
    unit_id BIGINT UNSIGNED NOT NULL,
    quantity_ordered DECIMAL(12,2) NOT NULL COMMENT 'Quantity diorder',
    quantity_received DECIMAL(12,2) DEFAULT 0 COMMENT 'Quantity diterima',
    quantity_remaining DECIMAL(12,2) GENERATED ALWAYS AS (quantity_ordered - quantity_received) STORED,
    unit_price DECIMAL(15,2) NOT NULL COMMENT 'Harga per unit',
    discount_percent DECIMAL(5,2) DEFAULT 0 COMMENT 'Diskon persentase',
    discount_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Diskon nominal',
    tax_percent DECIMAL(5,2) DEFAULT 0 COMMENT 'Pajak persentase',
    tax_amount DECIMAL(15,2) GENERATED ALWAYS AS ((quantity_ordered * unit_price - discount_amount) * tax_percent/100) STORED,
    subtotal DECIMAL(15,2) GENERATED ALWAYS AS (quantity_ordered * unit_price - discount_amount) STORED,
    total DECIMAL(15,2) GENERATED ALWAYS AS (subtotal + tax_amount) STORED,
    notes VARCHAR(255) NULL COMMENT 'Catatan item',
    
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES barang.products(id) ON DELETE RESTRICT,
    FOREIGN KEY (variant_id) REFERENCES barang.product_variants(id) ON DELETE SET NULL,
    FOREIGN KEY (unit_id) REFERENCES barang.units(id) ON DELETE RESTRICT,
    
    INDEX idx_purchase_id (purchase_id),
    INDEX idx_product_id (product_id),
    INDEX idx_variant_id (variant_id),
    INDEX idx_unit_id (unit_id),
    INDEX idx_quantity_ordered (quantity_ordered),
    INDEX idx_quantity_received (quantity_received),
    INDEX idx_quantity_remaining (quantity_remaining),
    INDEX idx_unit_price (unit_price),
    INDEX idx_total (total)
) ENGINE=InnoDB COMMENT='Item-item pembelian';

-- =====================================================
-- 6. PAYMENTS - Pembayaran
-- =====================================================
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode pembayaran',
    payment_type ENUM('sale', 'purchase', 'other') NOT NULL,
    reference_id BIGINT UNSIGNED NOT NULL COMMENT 'ID referensi (sale/purchase)',
    payment_method ENUM('cash', 'transfer', 'check', 'card', 'ewallet', 'other') NOT NULL,
    payment_date DATE NOT NULL COMMENT 'Tanggal pembayaran',
    amount DECIMAL(15,2) NOT NULL COMMENT 'Jumlah pembayaran',
    bank_name VARCHAR(100) NULL COMMENT 'Nama bank',
    account_number VARCHAR(50) NULL COMMENT 'Nomor rekening',
    account_name VARCHAR(100) NULL COMMENT 'Nama rekening',
    check_number VARCHAR(50) NULL COMMENT 'Nomor cek',
    card_number VARCHAR(20) NULL COMMENT 'Nomor kartu',
    transaction_ref VARCHAR(100) NULL COMMENT 'Referensi transaksi',
    notes VARCHAR(255) NULL COMMENT 'Catatan pembayaran',
    status ENUM('pending', 'confirmed', 'cancelled', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_payment_code (payment_code),
    INDEX idx_payment_type (payment_type),
    INDEX idx_reference_id (reference_id),
    INDEX idx_payment_method (payment_method),
    INDEX idx_payment_date (payment_date),
    INDEX idx_amount (amount),
    INDEX idx_status (status),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB COMMENT='Pembayaran penjualan dan pembelian';

-- =====================================================
-- 7. DELIVERIES - Pengiriman
-- =====================================================
CREATE TABLE deliveries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    delivery_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode pengiriman',
    sale_id BIGINT UNSIGNED NOT NULL COMMENT 'Referensi penjualan',
    delivery_type ENUM('regular', 'express', 'same_day', 'pickup') DEFAULT 'regular',
    delivery_date DATE NOT NULL COMMENT 'Tanggal pengiriman',
    estimated_delivery_date DATE NULL COMMENT 'Estimasi tiba',
    actual_delivery_date DATE NULL COMMENT 'Tiba aktual',
    warehouse_id BIGINT UNSIGNED NOT NULL COMMENT 'Gudang asal',
    driver_id BIGINT UNSIGNED NULL COMMENT 'Driver (link ke orang.persons)',
    vehicle_number VARCHAR(20) NULL COMMENT 'Nomor kendaraan',
    delivery_address VARCHAR(255) NOT NULL COMMENT 'Alamat pengiriman',
    recipient_name VARCHAR(100) NOT NULL COMMENT 'Nama penerima',
    recipient_phone VARCHAR(20) NULL COMMENT 'Telepon penerima',
    notes TEXT NULL COMMENT 'Catatan pengiriman',
    status ENUM('preparing', 'ready', 'in_transit', 'delivered', 'failed', 'cancelled') DEFAULT 'preparing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE RESTRICT,
    FOREIGN KEY (warehouse_id) REFERENCES barang.warehouses(id) ON DELETE RESTRICT,
    FOREIGN KEY (driver_id) REFERENCES orang.persons(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_delivery_code (delivery_code),
    INDEX idx_sale_id (sale_id),
    INDEX idx_delivery_date (delivery_date),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_driver_id (driver_id),
    INDEX idx_status (status)
) ENGINE=InnoDB COMMENT='Pengiriman pesanan';

-- =====================================================
-- 8. DELIVERY_ITEMS - Item Pengiriman
-- =====================================================
CREATE TABLE delivery_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    delivery_id BIGINT UNSIGNED NOT NULL,
    sale_item_id BIGINT UNSIGNED NOT NULL COMMENT 'Referensi item penjualan',
    product_id BIGINT UNSIGNED NOT NULL,
    variant_id BIGINT UNSIGNED NULL,
    unit_id BIGINT UNSIGNED NOT NULL,
    quantity_delivered DECIMAL(12,2) NOT NULL COMMENT 'Quantity dikirim',
    quantity_received DECIMAL(12,2) DEFAULT 0 COMMENT 'Quantity diterima',
    quantity_returned DECIMAL(12,2) DEFAULT 0 COMMENT 'Quantity dikembalikan',
    notes VARCHAR(255) NULL COMMENT 'Catatan item',
    
    FOREIGN KEY (delivery_id) REFERENCES deliveries(id) ON DELETE CASCADE,
    FOREIGN KEY (sale_item_id) REFERENCES sale_items(id) ON DELETE RESTRICT,
    FOREIGN KEY (product_id) REFERENCES barang.products(id) ON DELETE RESTRICT,
    FOREIGN KEY (variant_id) REFERENCES barang.product_variants(id) ON DELETE SET NULL,
    FOREIGN KEY (unit_id) REFERENCES barang.units(id) ON DELETE RESTRICT,
    
    INDEX idx_delivery_id (delivery_id),
    INDEX idx_sale_item_id (sale_item_id),
    INDEX idx_product_id (product_id),
    INDEX idx_variant_id (variant_id),
    INDEX idx_quantity_delivered (quantity_delivered),
    INDEX idx_quantity_received (quantity_received)
) ENGINE=InnoDB COMMENT='Item-item pengiriman';

-- =====================================================
-- 9. AUDIT_LOGS - Log Audit Sistem
-- =====================================================
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(100) NOT NULL COMMENT 'Nama tabel',
    record_id BIGINT UNSIGNED NOT NULL COMMENT 'ID record',
    action ENUM('insert', 'update', 'delete') NOT NULL COMMENT 'Aksi yang dilakukan',
    old_values JSON NULL COMMENT 'Nilai lama',
    new_values JSON NULL COMMENT 'Nilai baru',
    changed_fields JSON NULL COMMENT 'Field yang berubah',
    user_id BIGINT UNSIGNED NULL COMMENT 'User yang melakukan aksi',
    ip_address VARCHAR(45) NULL COMMENT 'IP address',
    user_agent TEXT NULL COMMENT 'User agent',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_table_name (table_name),
    INDEX idx_record_id (record_id),
    INDEX idx_action (action),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB COMMENT='Log audit untuk tracking perubahan data';

-- =====================================================
-- 10. NOTIFICATIONS - Notifikasi Sistem
-- =====================================================
CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    notification_type ENUM('info', 'success', 'warning', 'error', 'system') DEFAULT 'info',
    title VARCHAR(255) NOT NULL COMMENT 'Judul notifikasi',
    message TEXT NOT NULL COMMENT 'Pesan notifikasi',
    recipient_type ENUM('user', 'role', 'all', 'public') NOT NULL,
    recipient_id BIGINT UNSIGNED NULL COMMENT 'ID recipient (user/role)',
    reference_type VARCHAR(50) NULL COMMENT 'Tipe referensi',
    reference_id BIGINT UNSIGNED NULL COMMENT 'ID referensi',
    action_url VARCHAR(500) NULL COMMENT 'URL untuk action',
    is_read BOOLEAN DEFAULT FALSE COMMENT 'Sudah dibaca',
    read_at TIMESTAMP NULL COMMENT 'Waktu dibaca',
    expires_at TIMESTAMP NULL COMMENT 'Kadaluarsa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_notification_type (notification_type),
    INDEX idx_recipient_type (recipient_type),
    INDEX idx_recipient_id (recipient_id),
    INDEX idx_reference_type (reference_type),
    INDEX idx_reference_id (reference_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB COMMENT='Notifikasi sistem';

-- =====================================================
-- 11. USER_PREFERENCES - Preferensi User
-- =====================================================
CREATE TABLE user_preferences (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    preference_key VARCHAR(100) NOT NULL COMMENT 'Key preferensi',
    preference_value TEXT NULL COMMENT 'Value preferensi',
    preference_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    category VARCHAR(50) DEFAULT 'general' COMMENT 'Kategori preferensi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES orang.users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_user_preference (user_id, preference_key),
    INDEX idx_user_id (user_id),
    INDEX idx_preference_key (preference_key),
    INDEX idx_category (category)
) ENGINE=InnoDB COMMENT='Preferensi personalisasi user';

-- =====================================================
-- 12. INTEGRATIONS - Integrasi Sistem
-- =====================================================
CREATE TABLE integrations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    integration_name VARCHAR(100) NOT NULL COMMENT 'Nama integrasi',
    integration_type ENUM('api', 'webhook', 'database', 'file', 'other') NOT NULL,
    description TEXT NULL COMMENT 'Deskripsi integrasi',
    config JSON NOT NULL COMMENT 'Konfigurasi integrasi',
    status ENUM('active', 'inactive', 'error', 'testing') DEFAULT 'inactive',
    last_sync TIMESTAMP NULL COMMENT 'Terakhir sync',
    sync_frequency INT DEFAULT 0 COMMENT 'Frekuensi sync (menit)',
    error_message TEXT NULL COMMENT 'Error message terakhir',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_integration_name (integration_name),
    INDEX idx_integration_type (integration_type),
    INDEX idx_status (status),
    INDEX idx_last_sync (last_sync),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Integrasi dengan sistem eksternal';

-- =====================================================
-- INSERT DEFAULT SYSTEM CONFIGURATIONS
-- =====================================================

INSERT INTO system_configurations (config_key, config_value, config_type, description, category, is_system) VALUES
('company_name', 'PT Distributor Indonesia', 'string', 'Nama perusahaan', 'company', TRUE),
('company_address', 'Jakarta, Indonesia', 'string', 'Alamat perusahaan', 'company', TRUE),
('company_phone', '+62-21-1234567', 'string', 'Telepon perusahaan', 'company', TRUE),
('company_email', 'info@distributor.com', 'string', 'Email perusahaan', 'company', TRUE),
('tax_rate', '11', 'number', 'Persentase pajak default', 'finance', FALSE),
('currency', 'IDR', 'string', 'Mata uang default', 'finance', TRUE),
('date_format', 'Y-m-d', 'string', 'Format tanggal', 'general', FALSE),
('time_format', 'H:i:s', 'string', 'Format waktu', 'general', FALSE),
('decimal_places', '2', 'number', 'Jumlah desimal', 'general', FALSE),
('thousands_separator', '.', 'string', 'Pemisah ribuan', 'general', FALSE),
('decimal_separator', ',', 'string', 'Pemisah desimal', 'general', FALSE),
('default_warehouse', '1', 'number', 'Gudang default', 'inventory', FALSE),
('low_stock_alert', 'true', 'boolean', 'Alert stok minimum', 'inventory', FALSE),
('backup_enabled', 'true', 'boolean', 'Backup otomatis', 'system', TRUE),
('backup_frequency', 'daily', 'string', 'Frekuensi backup', 'system', TRUE),
('max_login_attempts', '5', 'number', 'Maksimum percobaan login', 'security', TRUE),
('session_timeout', '30', 'number', 'Timeout session (menit)', 'security', TRUE);

-- =====================================================
-- TRIGGERS untuk audit logging
-- =====================================================

DELIMITER $$

-- Trigger untuk audit log pada sales
CREATE TRIGGER sales_after_insert
AFTER INSERT ON sales
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, new_values, user_id)
    VALUES ('sales', NEW.id, 'insert', JSON_OBJECT(
        'sale_code', NEW.sale_code,
        'customer_id', NEW.customer_id,
        'total_amount', NEW.total_amount,
        'status', NEW.status
    ), NEW.created_by);
END$$

CREATE TRIGGER sales_after_update
AFTER UPDATE ON sales
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, changed_fields, user_id)
    VALUES ('sales', NEW.id, 'update', 
        JSON_OBJECT(
            'status', OLD.status,
            'total_amount', OLD.total_amount,
            'updated_at', OLD.updated_at
        ),
        JSON_OBJECT(
            'status', NEW.status,
            'total_amount', NEW.total_amount,
            'updated_at', NEW.updated_at
        ),
        JSON_ARRAY(
            CASE WHEN OLD.status != NEW.status THEN 'status' END,
            CASE WHEN OLD.total_amount != NEW.total_amount THEN 'total_amount' END
        ),
        NEW.updated_by
    );
END$$

DELIMITER ;

-- =====================================================
-- VIEWS untuk kemudahan query
-- =====================================================

-- View untuk sales lengkap dengan customer dan items
CREATE VIEW v_sales_complete AS
SELECT 
    s.*,
    p.full_name as customer_name,
    p.phone as customer_phone,
    w.name as warehouse_name,
    u.username as created_by_username,
    COUNT(si.id) as item_count,
    SUM(si.total) as items_total,
    SUM(si.profit) as total_profit,
    CASE 
        WHEN s.remaining_amount > 0 AND s.due_date < CURDATE() THEN 'overdue'
        WHEN s.remaining_amount > 0 THEN 'partial_paid'
        WHEN s.remaining_amount = 0 THEN 'fully_paid'
        ELSE 'unpaid'
    END as payment_status
FROM sales s
JOIN orang.persons p ON s.customer_id = p.id
JOIN barang.warehouses w ON s.warehouse_id = w.id
LEFT JOIN orang.users u ON s.created_by = u.id
LEFT JOIN sale_items si ON s.id = si.sale_id
GROUP BY s.id;

-- View untuk inventory movements
CREATE VIEW v_inventory_movements_summary AS
SELECT 
    DATE(sm.movement_date) as movement_date,
    sm.movement_type,
    sm.reference_type,
    p.name as product_name,
    p.sku,
    w.name as warehouse_name,
    SUM(sm.quantity) as total_quantity,
    SUM(sm.total_cost) as total_value,
    COUNT(*) as movement_count
FROM stock_movements sm
JOIN barang.products p ON sm.product_id = p.id
JOIN barang.warehouses w ON sm.warehouse_id = w.id
GROUP BY DATE(sm.movement_date), sm.movement_type, sm.reference_type, p.id, w.id
ORDER BY sm.movement_date DESC;
