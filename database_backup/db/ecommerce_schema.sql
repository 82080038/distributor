-- =====================================================
-- DATABASE ECOMMERCE - E-commerce Integration
-- =====================================================
-- Created: 19 Januari 2026
-- Purpose: Integrasi multi-channel e-commerce platforms
-- Integration: Link ke aplikasi, barang, orang, surat_laporan

CREATE DATABASE IF NOT EXISTS ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce;

-- =====================================================
-- 1. MARKETPLACES - Master Marketplace
-- =====================================================
CREATE TABLE marketplaces (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    marketplace_code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Kode marketplace',
    marketplace_name VARCHAR(100) NOT NULL COMMENT 'Nama marketplace',
    marketplace_type ENUM('marketplace', 'social_commerce', 'website', 'mobile_app', 'pos', 'b2b_portal') NOT NULL,
    platform ENUM('tokopedia', 'shopee', 'lazada', 'bukalapak', 'blibli', 'jd_id', 'shopify', 'woocommerce', 'magento', 'custom_api', 'other') NOT NULL,
    website_url VARCHAR(255) NULL COMMENT 'URL marketplace',
    api_endpoint VARCHAR(500) NULL COMMENT 'API endpoint',
    api_version VARCHAR(20) NULL COMMENT 'API version',
    authentication_type ENUM('api_key', 'oauth2', 'basic_auth', 'custom') DEFAULT 'api_key',
    api_credentials JSON NULL COMMENT 'API credentials (encrypted)',
    webhook_url VARCHAR(500) NULL COMMENT 'Webhook URL',
    supported_features JSON NULL COMMENT 'Fitur yang didukung',
    commission_rate DECIMAL(5,2) DEFAULT 0 COMMENT 'Rate komisi marketplace',
    transaction_fee_rate DECIMAL(5,2) DEFAULT 0 COMMENT 'Rate biaya transaksi',
    payment_terms VARCHAR(100) NULL COMMENT 'Syarat pembayaran',
    settlement_period_days INT DEFAULT 0 COMMENT 'Periode settlement (hari)',
    currency VARCHAR(3) DEFAULT 'IDR' COMMENT 'Mata uang',
    timezone VARCHAR(50) DEFAULT 'Asia/Jakarta' COMMENT 'Timezone',
    is_active BOOLEAN DEFAULT TRUE,
    is_sandbox BOOLEAN DEFAULT FALSE COMMENT 'Sandbox mode',
    last_sync_at TIMESTAMP NULL COMMENT 'Terakhir sync',
    sync_status ENUM('success', 'failed', 'in_progress', 'never_synced') DEFAULT 'never_synced',
    sync_error_message TEXT NULL COMMENT 'Error message sync',
    notes TEXT NULL COMMENT 'Catatan marketplace',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_marketplace_code (marketplace_code),
    INDEX idx_marketplace_name (marketplace_name),
    INDEX idx_marketplace_type (marketplace_type),
    INDEX idx_platform (platform),
    INDEX idx_commission_rate (commission_rate),
    INDEX idx_transaction_fee_rate (transaction_fee_rate),
    INDEX idx_is_active (is_active),
    INDEX idx_is_sandbox (is_sandbox),
    INDEX idx_last_sync_at (last_sync_at),
    INDEX idx_sync_status (sync_status)
) ENGINE=InnoDB COMMENT='Master data marketplace';

-- =====================================================
-- 2. MARKETPLACE_ACCOUNTS - Akun Marketplace
-- =====================================================
CREATE TABLE marketplace_accounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    account_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode akun',
    marketplace_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke marketplaces',
    account_name VARCHAR(100) NOT NULL COMMENT 'Nama akun',
    store_name VARCHAR(100) NOT NULL COMMENT 'Nama toko',
    store_url VARCHAR(500) NULL COMMENT 'URL toko',
    seller_id VARCHAR(100) NULL COMMENT 'ID seller di marketplace',
    shop_id VARCHAR(100) NULL COMMENT 'ID shop di marketplace',
    account_email VARCHAR(255) NULL COMMENT 'Email akun',
    account_phone VARCHAR(20) NULL COMMENT 'Telepon akun',
    account_status ENUM('active', 'inactive', 'suspended', 'banned', 'verification_pending') DEFAULT 'active',
    verification_status ENUM('verified', 'unverified', 'pending', 'rejected') DEFAULT 'unverified',
    business_type ENUM('individual', 'company', 'government', 'other') DEFAULT 'individual',
    business_license VARCHAR(100) NULL COMMENT 'Nomor SIUP/NIB',
    tax_id VARCHAR(25) NULL COMMENT 'NPWP',
    bank_account_number VARCHAR(50) NULL COMMENT 'Nomor rekening',
    bank_name VARCHAR(100) NULL COMMENT 'Nama bank',
    bank_account_name VARCHAR(100) NULL COMMENT 'Nama rekening',
    warehouse_id BIGINT UNSIGNED NULL COMMENT 'Gudang utama',
    default_shipping_courier VARCHAR(100) NULL COMMENT 'Kurir default',
    auto_accept_order BOOLEAN DEFAULT TRUE COMMENT 'Auto accept order',
    auto_print_label BOOLEAN DEFAULT FALSE COMMENT 'Auto print label',
    auto_input_resi BOOLEAN DEFAULT FALSE COMMENT 'Auto input resi',
    sync_enabled BOOLEAN DEFAULT TRUE COMMENT 'Sync enabled',
    sync_interval_minutes INT DEFAULT 15 COMMENT 'Sync interval (menit)',
    last_order_sync TIMESTAMP NULL COMMENT 'Terakhir sync order',
    last_product_sync TIMESTAMP NULL COMMENT 'Terakhir sync produk',
    last_inventory_sync TIMESTAMP NULL COMMENT 'Terakhir sync inventory',
    sync_status ENUM('success', 'failed', 'in_progress', 'disabled') DEFAULT 'success',
    notes TEXT NULL COMMENT 'Catatan akun',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (marketplace_id) REFERENCES marketplaces(id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES barang.warehouses(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_account_code (account_code),
    INDEX idx_marketplace_id (marketplace_id),
    INDEX idx_account_name (account_name),
    INDEX idx_store_name (store_name),
    INDEX idx_seller_id (seller_id),
    INDEX idx_account_status (account_status),
    INDEX idx_verification_status (verification_status),
    INDEX idx_business_type (business_type),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_sync_enabled (sync_enabled),
    INDEX idx_sync_status (sync_status),
    INDEX idx_last_order_sync (last_order_sync)
) ENGINE=InnoDB COMMENT='Akun marketplace';

-- =====================================================
-- 3. PRODUCT_LISTINGS - Listing Produk di Marketplace
-- =====================================================
CREATE TABLE product_listings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode listing',
    product_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke barang.products',
    variant_id BIGINT UNSIGNED NULL COMMENT 'Link ke barang.product_variants',
    marketplace_account_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke marketplace_accounts',
    marketplace_product_id VARCHAR(100) NULL COMMENT 'ID produk di marketplace',
    marketplace_sku VARCHAR(100) NULL COMMENT 'SKU di marketplace',
    listing_title VARCHAR(255) NOT NULL COMMENT 'Judul listing',
    listing_description TEXT NULL COMMENT 'Deskripsi listing',
    listing_price DECIMAL(15,2) NOT NULL COMMENT 'Harga listing',
    original_price DECIMAL(15,2) DEFAULT 0 COMMENT 'Harga asli',
    discount_price DECIMAL(15,2) DEFAULT 0 COMMENT 'Harga diskon',
    discount_percentage DECIMAL(5,2) DEFAULT 0 COMMENT 'Persentase diskon',
    stock_quantity INT DEFAULT 0 COMMENT 'Quantity stok',
    min_order_quantity INT DEFAULT 1 COMMENT 'Minimum order',
    max_order_quantity INT NULL COMMENT 'Maximum order',
    weight_gram INT DEFAULT 0 COMMENT 'Berat (gram)',
    dimensions JSON NULL COMMENT 'Dimensi (p,l,t)',
    condition_type ENUM('new', 'like_new', 'good', 'fair', 'used') DEFAULT 'new',
    category_marketplace VARCHAR(200) NULL COMMENT 'Kategori di marketplace',
    tags JSON NULL COMMENT 'Tags produk',
    listing_status ENUM('active', 'inactive', 'draft', 'archived', 'banned') DEFAULT 'draft',
    visibility ENUM('public', 'private', 'unlisted') DEFAULT 'public',
    is_featured BOOLEAN DEFAULT FALSE COMMENT 'Featured product',
    is_preorder BOOLEAN DEFAULT FALSE COMMENT 'Preorder',
    preorder_days INT DEFAULT 0 COMMENT 'Hari preorder',
    free_shipping BOOLEAN DEFAULT FALSE COMMENT 'Free shipping',
    instant_courier BOOLEAN DEFAULT FALSE COMMENT 'Instant courier',
    chat_enabled BOOLEAN DEFAULT TRUE COMMENT 'Chat enabled',
    nego_enabled BOOLEAN DEFAULT TRUE COMMENT 'Nego enabled',
    last_sync_at TIMESTAMP NULL COMMENT 'Terakhir sync',
    sync_status ENUM('success', 'failed', 'pending') DEFAULT 'pending',
    sync_error_message TEXT NULL COMMENT 'Error sync',
    view_count INT DEFAULT 0 COMMENT 'Jumlah view',
    like_count INT DEFAULT 0 COMMENT 'Jumlah like',
    sold_count INT DEFAULT 0 COMMENT 'Jumlah terjual',
    rating_average DECIMAL(3,2) DEFAULT 0 COMMENT 'Rating rata-rata',
    review_count INT DEFAULT 0 COMMENT 'Jumlah review',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (product_id) REFERENCES barang.products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES barang.product_variants(id) ON DELETE SET NULL,
    FOREIGN KEY (marketplace_account_id) REFERENCES marketplace_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_listing_code (listing_code),
    INDEX idx_product_id (product_id),
    INDEX idx_variant_id (variant_id),
    INDEX idx_marketplace_account_id (marketplace_account_id),
    INDEX idx_marketplace_product_id (marketplace_product_id),
    INDEX idx_marketplace_sku (marketplace_sku),
    INDEX idx_listing_price (listing_price),
    INDEX idx_stock_quantity (stock_quantity),
    INDEX idx_listing_status (listing_status),
    INDEX idx_visibility (visibility),
    INDEX idx_is_featured (is_featured),
    INDEX idx_view_count (view_count),
    INDEX idx_sold_count (sold_count),
    INDEX idx_rating_average (rating_average),
    INDEX idx_last_sync_at (last_sync_at),
    INDEX idx_sync_status (sync_status)
) ENGINE=InnoDB COMMENT='Listing produk di marketplace';

-- =====================================================
-- 4. ORDER_SYNC - Sinkronisasi Order
-- =====================================================
CREATE TABLE order_sync (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sync_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode sync',
    marketplace_account_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke marketplace_accounts',
    marketplace_order_id VARCHAR(100) NOT NULL COMMENT 'ID order di marketplace',
    marketplace_invoice_id VARCHAR(100) NULL COMMENT 'ID invoice di marketplace',
    internal_order_id BIGINT UNSIGNED NULL COMMENT 'Link ke aplikasi.sales',
    customer_marketplace_id VARCHAR(100) NULL COMMENT 'ID customer di marketplace',
    customer_name VARCHAR(255) NOT NULL COMMENT 'Nama customer',
    customer_phone VARCHAR(20) NULL COMMENT 'Telepon customer',
    customer_email VARCHAR(255) NULL COMMENT 'Email customer',
    shipping_address JSON NOT NULL COMMENT 'Alamat pengiriman',
    billing_address JSON NULL COMMENT 'Alamat penagihan',
    order_date DATETIME NOT NULL COMMENT 'Tanggal order',
    order_status ENUM('pending', 'paid', 'processed', 'shipped', 'delivered', 'completed', 'cancelled', 'returned') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'paid', 'refunded', 'partially_refunded') DEFAULT 'unpaid',
    payment_method VARCHAR(100) NULL COMMENT 'Metode pembayaran',
    payment_channel VARCHAR(100) NULL COMMENT 'Channel pembayaran',
    payment_date DATETIME NULL COMMENT 'Tanggal pembayaran',
    subtotal_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Subtotal',
    shipping_cost DECIMAL(15,2) DEFAULT 0 COMMENT 'Biaya pengiriman',
    insurance_cost DECIMAL(15,2) DEFAULT 0 COMMENT 'Biaya asuransi',
    admin_fee DECIMAL(15,2) DEFAULT 0 COMMENT 'Biaya admin',
    marketplace_fee DECIMAL(15,2) DEFAULT 0 COMMENT 'Fee marketplace',
    commission_fee DECIMAL(15,2) DEFAULT 0 COMMENT 'Fee komisi',
    total_amount DECIMAL(15,2) GENERATED ALWAYS AS (subtotal_amount + shipping_cost + insurance_cost + admin_fee) STORED,
    net_amount DECIMAL(15,2) GENERATED ALWAYS AS (total_amount - marketplace_fee - commission_fee) STORED,
    voucher_code VARCHAR(50) NULL COMMENT 'Kode voucher',
    voucher_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Nilai voucher',
    coins_used DECIMAL(15,2) DEFAULT 0 COMMENT 'Coins digunakan',
    notes VARCHAR(500) NULL COMMENT 'Catatan order',
    tracking_number VARCHAR(100) NULL COMMENT 'Nomor resi',
    courier_name VARCHAR(100) NULL COMMENT 'Nama kurir',
    courier_service VARCHAR(100) NULL COMMENT 'Layanan kurir',
    estimated_delivery DATE NULL COMMENT 'Estimasi pengiriman',
    actual_delivery DATE NULL COMMENT 'Pengiriman aktual',
    sync_status ENUM('pending', 'synced', 'failed', 'skipped') DEFAULT 'pending',
    sync_error_message TEXT NULL COMMENT 'Error sync',
    last_sync_at TIMESTAMP NULL COMMENT 'Terakhir sync',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (marketplace_account_id) REFERENCES marketplace_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (internal_order_id) REFERENCES aplikasi.sales(id) ON DELETE SET NULL,
    
    INDEX idx_sync_code (sync_code),
    INDEX idx_marketplace_account_id (marketplace_account_id),
    INDEX idx_marketplace_order_id (marketplace_order_id),
    INDEX idx_internal_order_id (internal_order_id),
    INDEX idx_customer_name (customer_name),
    INDEX idx_order_date (order_date),
    INDEX idx_order_status (order_status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_total_amount (total_amount),
    INDEX idx_net_amount (net_amount),
    INDEX idx_tracking_number (tracking_number),
    INDEX idx_courier_name (courier_name),
    INDEX idx_sync_status (sync_status),
    INDEX idx_last_sync_at (last_sync_at)
) ENGINE=InnoDB COMMENT='Sinkronisasi order dari marketplace';

-- =====================================================
-- 5. ORDER_SYNC_ITEMS - Item Order Sync
-- =====================================================
CREATE TABLE order_sync_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_sync_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke order_sync',
    marketplace_product_id VARCHAR(100) NULL COMMENT 'ID produk di marketplace',
    marketplace_sku VARCHAR(100) NULL COMMENT 'SKU di marketplace',
    product_id BIGINT UNSIGNED NULL COMMENT 'Link ke barang.products',
    variant_id BIGINT UNSIGNED NULL COMMENT 'Link ke barang.product_variants',
    product_name VARCHAR(255) NOT NULL COMMENT 'Nama produk',
    product_category VARCHAR(100) NULL COMMENT 'Kategori produk',
    quantity INT NOT NULL COMMENT 'Quantity',
    unit_price DECIMAL(15,2) NOT NULL COMMENT 'Harga satuan',
    discount_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Diskon',
    subtotal_amount DECIMAL(15,2) GENERATED ALWAYS AS (quantity * unit_price - discount_amount) STORED,
    weight_gram INT DEFAULT 0 COMMENT 'Berat (gram)',
    notes VARCHAR(255) NULL COMMENT 'Catatan item',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_sync_id) REFERENCES order_sync(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES barang.products(id) ON DELETE SET NULL,
    FOREIGN KEY (variant_id) REFERENCES barang.product_variants(id) ON DELETE SET NULL,
    
    INDEX idx_order_sync_id (order_sync_id),
    INDEX idx_marketplace_product_id (marketplace_product_id),
    INDEX idx_marketplace_sku (marketplace_sku),
    INDEX idx_product_id (product_id),
    INDEX idx_variant_id (variant_id),
    INDEX idx_product_name (product_name),
    INDEX idx_quantity (quantity),
    INDEX idx_unit_price (unit_price),
    INDEX idx_subtotal_amount (subtotal_amount)
) ENGINE=InnoDB COMMENT='Item-item order sync';

-- =====================================================
-- 6. INVENTORY_SYNC - Sinkronisasi Inventory
-- =====================================================
CREATE TABLE inventory_sync (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sync_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode sync',
    marketplace_account_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke marketplace_accounts',
    product_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke barang.products',
    variant_id BIGINT UNSIGNED NULL COMMENT 'Link ke barang.product_variants',
    warehouse_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke barang.warehouses',
    internal_quantity DECIMAL(12,2) NOT NULL COMMENT 'Quantity internal',
    marketplace_quantity DECIMAL(12,2) NOT NULL COMMENT 'Quantity di marketplace',
    sync_quantity DECIMAL(12,2) NOT NULL COMMENT 'Quantity yang disync',
    sync_type ENUM('full_sync', 'incremental', 'manual', 'auto') DEFAULT 'auto',
    sync_reason VARCHAR(255) NULL COMMENT 'Alasan sync',
    sync_status ENUM('pending', 'success', 'failed', 'skipped') DEFAULT 'pending',
    sync_error_message TEXT NULL COMMENT 'Error sync',
    marketplace_response JSON NULL COMMENT 'Response dari marketplace',
    last_sync_at TIMESTAMP NULL COMMENT 'Terakhir sync',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (marketplace_account_id) REFERENCES marketplace_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES barang.products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES barang.product_variants(id) ON DELETE SET NULL,
    FOREIGN KEY (warehouse_id) REFERENCES barang.warehouses(id) ON DELETE CASCADE,
    
    INDEX idx_sync_code (sync_code),
    INDEX idx_marketplace_account_id (marketplace_account_id),
    INDEX idx_product_id (product_id),
    INDEX idx_variant_id (variant_id),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_internal_quantity (internal_quantity),
    INDEX idx_marketplace_quantity (marketplace_quantity),
    INDEX idx_sync_quantity (sync_quantity),
    INDEX idx_sync_type (sync_type),
    INDEX idx_sync_status (sync_status),
    INDEX idx_last_sync_at (last_sync_at)
) ENGINE=InnoDB COMMENT='Sinkronisasi inventory ke marketplace';

-- =====================================================
-- 7. PRICING_RULES - Aturan Harga
-- =====================================================
CREATE TABLE pricing_rules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rule_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode aturan',
    rule_name VARCHAR(100) NOT NULL COMMENT 'Nama aturan',
    rule_type ENUM('markup', 'discount', 'dynamic', 'competitor_based', 'cost_plus', 'bulk') NOT NULL,
    marketplace_account_id BIGINT UNSIGNED NULL COMMENT 'Link ke marketplace_accounts',
    product_category_id BIGINT UNSIGNED NULL COMMENT 'Link ke barang.categories',
    product_id BIGINT UNSIGNED NULL COMMENT 'Link ke barang.products',
    rule_priority INT DEFAULT 1 COMMENT 'Prioritas aturan',
    is_active BOOLEAN DEFAULT TRUE,
    effective_date DATE NOT NULL COMMENT 'Tanggal efektif',
    expiry_date DATE NULL COMMENT 'Tanggal kadaluarsa',
    conditions JSON NULL COMMENT 'Kondisi aturan',
    actions JSON NULL COMMENT 'Aksi aturan',
    markup_percentage DECIMAL(5,2) NULL COMMENT 'Persentase markup',
    discount_percentage DECIMAL(5,2) NULL COMMENT 'Persentase diskon',
    fixed_amount DECIMAL(15,2) NULL COMMENT 'Jumlah tetap',
    min_quantity INT NULL COMMENT 'Quantity minimum',
    max_quantity INT NULL COMMENT 'Quantity maksimum',
    competitor_marketplace VARCHAR(100) NULL COMMENT 'Marketplace kompetitor',
    price_comparison_operator ENUM('lower_than', 'higher_than', 'equal_to') NULL COMMENT 'Operator perbandingan',
    auto_apply BOOLEAN DEFAULT TRUE COMMENT 'Auto apply',
    schedule_config JSON NULL COMMENT 'Konfigurasi jadwal',
    last_applied_at TIMESTAMP NULL COMMENT 'Terakhir diaplikasikan',
    applied_count INT DEFAULT 0 COMMENT 'Jumlah diaplikasikan',
    notes TEXT NULL COMMENT 'Catatan aturan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (marketplace_account_id) REFERENCES marketplace_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_category_id) REFERENCES barang.categories(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES barang.products(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_rule_code (rule_code),
    INDEX idx_rule_name (rule_name),
    INDEX idx_rule_type (rule_type),
    INDEX idx_marketplace_account_id (marketplace_account_id),
    INDEX idx_product_category_id (product_category_id),
    INDEX idx_product_id (product_id),
    INDEX idx_rule_priority (rule_priority),
    INDEX idx_is_active (is_active),
    INDEX idx_effective_date (effective_date),
    INDEX idx_expiry_date (expiry_date),
    INDEX idx_markup_percentage (markup_percentage),
    INDEX idx_discount_percentage (discount_percentage),
    INDEX idx_last_applied_at (last_applied_at)
) ENGINE=InnoDB COMMENT='Aturan pricing otomatis';

-- =====================================================
-- 8. PROMOTION_MANAGEMENT - Manajemen Promo
-- =====================================================
CREATE TABLE promotion_management (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    promo_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode promo',
    promo_name VARCHAR(100) NOT NULL COMMENT 'Nama promo',
    promo_type ENUM('discount', 'cashback', 'free_shipping', 'buy_get', 'bundle', 'voucher', 'flash_sale') NOT NULL,
    marketplace_account_id BIGINT UNSIGNED NULL COMMENT 'Link ke marketplace_accounts',
    product_category_id BIGINT UNSIGNED NULL COMMENT 'Link ke barang.categories',
    product_id BIGINT UNSIGNED NULL COMMENT 'Link ke barang.products',
    promo_status ENUM('draft', 'active', 'paused', 'expired', 'cancelled') DEFAULT 'draft',
    start_date DATETIME NOT NULL COMMENT 'Tanggal mulai',
    end_date DATETIME NOT NULL COMMENT 'Tanggal selesai',
    target_audience JSON NULL COMMENT 'Target audience',
    discount_type ENUM('percentage', 'fixed_amount', 'buy_x_get_y') DEFAULT 'percentage',
    discount_value DECIMAL(15,2) NOT NULL COMMENT 'Nilai diskon',
    max_discount_amount DECIMAL(15,2) NULL COMMENT 'Maksimum diskon',
    min_purchase_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Minimum pembelian',
    max_usage_per_customer INT NULL COMMENT 'Maksimum pakai per customer',
    total_usage_limit INT NULL COMMENT 'Batas total penggunaan',
    current_usage_count INT DEFAULT 0 COMMENT 'Jumlah penggunaan saat ini',
    applicable_products JSON NULL COMMENT 'Produk yang berlaku',
    excluded_products JSON NULL NULL COMMENT 'Produk dikecualikan',
    auto_apply BOOLEAN DEFAULT FALSE COMMENT 'Auto apply',
    stackable BOOLEAN DEFAULT FALSE COMMENT 'Bisa digabung',
    promo_description TEXT NULL COMMENT 'Deskripsi promo',
    terms_conditions TEXT NULL COMMENT 'Syarat dan ketentuan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (marketplace_account_id) REFERENCES marketplace_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_category_id) REFERENCES barang.categories(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES barang.products(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_promo_code (promo_code),
    INDEX idx_promo_name (promo_name),
    INDEX idx_promo_type (promo_type),
    INDEX idx_marketplace_account_id (marketplace_account_id),
    INDEX idx_product_category_id (product_category_id),
    INDEX idx_product_id (product_id),
    INDEX idx_promo_status (promo_status),
    INDEX idx_start_date (start_date),
    INDEX idx_end_date (end_date),
    INDEX idx_discount_value (discount_value),
    INDEX idx_current_usage_count (current_usage_count)
) ENGINE=InnoDB COMMENT='Manajemen promo marketplace';

-- =====================================================
-- 9. CUSTOMER_REVIEWS - Review Customer
-- =====================================================
CREATE TABLE customer_reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    review_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode review',
    marketplace_account_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke marketplace_accounts',
    marketplace_review_id VARCHAR(100) NOT NULL COMMENT 'ID review di marketplace',
    product_id BIGINT UNSIGNED NULL COMMENT 'Link ke barang.products',
    variant_id BIGINT UNSIGNED NULL COMMENT 'Link ke barang.product_variants',
    order_sync_id BIGINT UNSIGNED NULL COMMENT 'Link ke order_sync',
    customer_name VARCHAR(255) NOT NULL COMMENT 'Nama customer',
    customer_marketplace_id VARCHAR(100) NULL COMMENT 'ID customer di marketplace',
    rating TINYINT NOT NULL COMMENT 'Rating (1-5)',
    review_title VARCHAR(255) NULL COMMENT 'Judul review',
    review_content TEXT NULL COMMENT 'Isi review',
    review_images JSON NULL COMMENT 'Gambar review',
    review_date DATETIME NOT NULL COMMENT 'Tanggal review',
    helpful_count INT DEFAULT 0 COMMENT 'Jumlah helpful',
    is_verified_purchase BOOLEAN DEFAULT FALSE COMMENT 'Purchase terverifikasi',
    review_status ENUM('published', 'hidden', 'flagged', 'removed') DEFAULT 'published',
    sentiment_analysis ENUM('positive', 'neutral', 'negative') NULL COMMENT 'Analisis sentimen',
    auto_reply_sent BOOLEAN DEFAULT FALSE COMMENT 'Auto reply terkirim',
    reply_content TEXT NULL COMMENT 'Balasan review',
    reply_date DATETIME NULL COMMENT 'Tanggal balasan',
    sync_status ENUM('pending', 'synced', 'failed') DEFAULT 'pending',
    last_sync_at TIMESTAMP NULL COMMENT 'Terakhir sync',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (marketplace_account_id) REFERENCES marketplace_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES barang.products(id) ON DELETE SET NULL,
    FOREIGN KEY (variant_id) REFERENCES barang.product_variants(id) ON DELETE SET NULL,
    FOREIGN KEY (order_sync_id) REFERENCES order_sync(id) ON DELETE SET NULL,
    
    INDEX idx_review_code (review_code),
    INDEX idx_marketplace_account_id (marketplace_account_id),
    INDEX idx_marketplace_review_id (marketplace_review_id),
    INDEX idx_product_id (product_id),
    INDEX idx_variant_id (variant_id),
    INDEX idx_order_sync_id (order_sync_id),
    INDEX idx_customer_name (customer_name),
    INDEX idx_rating (rating),
    INDEX idx_review_date (review_date),
    INDEX idx_helpful_count (helpful_count),
    INDEX idx_is_verified_purchase (is_verified_purchase),
    INDEX idx_review_status (review_status),
    INDEX idx_sentiment_analysis (sentiment_analysis),
    INDEX idx_sync_status (sync_status)
) ENGINE=InnoDB COMMENT='Review customer dari marketplace';

-- =====================================================
-- 10. SALES_CHANNELS - Channel Penjualan
-- =====================================================
CREATE TABLE sales_channels (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    channel_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode channel',
    channel_name VARCHAR(100) NOT NULL COMMENT 'Nama channel',
    channel_type ENUM('marketplace', 'website', 'social_media', 'mobile_app', 'pos', 'b2b_portal', 'call_center', 'offline_store') NOT NULL,
    parent_channel_id BIGINT UNSIGNED NULL COMMENT 'Channel induk',
    channel_level TINYINT DEFAULT 1 COMMENT 'Level channel',
    description TEXT NULL COMMENT 'Deskripsi channel',
    target_audience JSON NULL COMMENT 'Target audience',
    geographic_coverage JSON NULL COMMENT 'Coverage geografis',
    product_categories JSON NULL COMMENT 'Kategori produk',
    commission_model ENUM('percentage', 'fixed', 'tiered', 'revenue_share') DEFAULT 'percentage',
    commission_rate DECIMAL(5,2) DEFAULT 0 COMMENT 'Rate komisi',
    transaction_fee_rate DECIMAL(5,2) DEFAULT 0 COMMENT 'Rate biaya transaksi',
    settlement_period_days INT DEFAULT 0 COMMENT 'Periode settlement',
    currency VARCHAR(3) DEFAULT 'IDR' COMMENT 'Mata uang',
    timezone VARCHAR(50) DEFAULT 'Asia/Jakarta' COMMENT 'Timezone',
    integration_status ENUM('integrated', 'partial', 'manual', 'planned') DEFAULT 'manual',
    last_order_date DATE NULL COMMENT 'Terakhir order',
    total_orders INT DEFAULT 0 COMMENT 'Total orders',
    total_revenue DECIMAL(15,2) DEFAULT 0 COMMENT 'Total revenue',
    average_order_value DECIMAL(15,2) DEFAULT 0 COMMENT 'AOV',
    conversion_rate DECIMAL(5,2) DEFAULT 0 COMMENT 'Conversion rate',
    is_active BOOLEAN DEFAULT TRUE,
    priority_level TINYINT DEFAULT 3 COMMENT 'Prioritas (1=highest)',
    notes TEXT NULL COMMENT 'Catatan channel',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (parent_channel_id) REFERENCES sales_channels(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_channel_code (channel_code),
    INDEX idx_channel_name (channel_name),
    INDEX idx_channel_type (channel_type),
    INDEX idx_parent_channel_id (parent_channel_id),
    INDEX idx_channel_level (channel_level),
    INDEX idx_commission_rate (commission_rate),
    INDEX idx_transaction_fee_rate (transaction_fee_rate),
    INDEX idx_integration_status (integration_status),
    INDEX idx_total_orders (total_orders),
    INDEX idx_total_revenue (total_revenue),
    INDEX idx_average_order_value (average_order_value),
    INDEX idx_conversion_rate (conversion_rate),
    INDEX idx_is_active (is_active),
    INDEX idx_priority_level (priority_level)
) ENGINE=InnoDB COMMENT='Channel penjualan';

-- =====================================================
-- INSERT DEFAULT DATA
-- =====================================================

-- Default Marketplaces
INSERT INTO marketplaces (marketplace_code, marketplace_name, marketplace_type, platform, commission_rate, transaction_fee_rate) VALUES
('TKPD', 'Tokopedia', 'marketplace', 'tokopedia', 2.00, 1.00),
('SHP', 'Shopee', 'marketplace', 'shopee', 6.00, 2.00),
('LZD', 'Lazada', 'marketplace', 'lazada', 5.00, 2.00),
('BL', 'Bukalapak', 'marketplace', 'bukalapak', 3.00, 1.00),
('BLIB', 'Blibli', 'marketplace', 'blibli', 4.00, 1.50),
('SF', 'Shopify', 'website', 'shopify', 0.00, 2.90),
('WC', 'WooCommerce', 'website', 'woocommerce', 0.00, 2.90);

-- Default Sales Channels
INSERT INTO sales_channels (channel_code, channel_name, channel_type, commission_rate, transaction_fee_rate, integration_status) VALUES
('ONLINE_MP', 'Online Marketplaces', 'marketplace', 4.00, 1.50, 'integrated'),
('WEBSITE', 'Official Website', 'website', 0.00, 2.90, 'integrated'),
('SOCIAL', 'Social Commerce', 'social_media', 5.00, 0.00, 'partial'),
('POS', 'Point of Sale', 'pos', 0.00, 0.00, 'integrated'),
('B2B', 'B2B Portal', 'b2b_portal', 3.00, 1.00, 'manual'),
('CALL', 'Call Center', 'call_center', 2.00, 0.00, 'manual'),
('OFFLINE', 'Offline Store', 'offline_store', 0.00, 0.00, 'manual');

-- =====================================================
-- VIEWS untuk ecommerce analytics
-- =====================================================

-- View untuk marketplace performance
CREATE VIEW v_marketplace_performance AS
SELECT 
    ma.account_code,
    ma.store_name,
    m.marketplace_name,
    m.platform,
    COUNT(DISTINCT os.id) as total_orders,
    SUM(os.total_amount) as gross_revenue,
    SUM(os.marketplace_fee) as total_marketplace_fees,
    SUM(os.commission_fee) as total_commission_fees,
    SUM(os.net_amount) as net_revenue,
    AVG(os.total_amount) as avg_order_value,
    COUNT(DISTINCT os.customer_name) as unique_customers,
    COUNT(DISTINCT DATE(os.order_date)) as active_days,
    MAX(os.order_date) as last_order_date,
    ma.last_order_sync,
    ma.sync_status
FROM marketplace_accounts ma
JOIN marketplaces m ON ma.marketplace_id = m.id
LEFT JOIN order_sync os ON ma.id = os.marketplace_account_id
WHERE ma.account_status = 'active'
GROUP BY ma.id
ORDER BY net_revenue DESC;

-- View untuk product listing performance
CREATE VIEW v_product_listing_performance AS
SELECT 
    pl.listing_code,
    pl.listing_title,
    p.name as product_name,
    p.sku,
    ma.store_name,
    m.marketplace_name,
    pl.listing_price,
    pl.stock_quantity,
    pl.view_count,
    pl.like_count,
    pl.sold_count,
    pl.rating_average,
    pl.review_count,
    cr.positive_review_count,
    cr.negative_review_count,
    cr.neutral_review_count,
    CASE 
        WHEN pl.sold_count > 0 THEN (pl.sold_count * 1.0 / NULLIF(pl.view_count, 0)) * 100 
        ELSE 0 
    END as conversion_rate,
    pl.listing_status,
    pl.last_sync_at
FROM product_listings pl
JOIN barang.products p ON pl.product_id = p.id
JOIN marketplace_accounts ma ON pl.marketplace_account_id = ma.id
JOIN marketplaces m ON ma.marketplace_id = m.id
LEFT JOIN (
    SELECT 
        product_id,
        marketplace_account_id,
        SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) as positive_review_count,
        SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as neutral_review_count,
        SUM(CASE WHEN rating <= 2 THEN 1 ELSE 0 END) as negative_review_count
    FROM customer_reviews 
    GROUP BY product_id, marketplace_account_id
) cr ON pl.product_id = cr.product_id AND pl.marketplace_account_id = cr.marketplace_account_id
WHERE pl.listing_status IN ('active', 'inactive')
ORDER BY pl.sold_count DESC;
