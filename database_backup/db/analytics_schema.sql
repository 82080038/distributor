-- =====================================================
-- DATABASE ANALYTICS - Business Intelligence & Data Warehouse
-- =====================================================
-- Created: 19 Januari 2026
-- Purpose: Data warehouse untuk analytics, reporting, dan business intelligence
-- Integration: ETL dari semua database transaksional

CREATE DATABASE IF NOT EXISTS analytics CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE analytics;

-- =====================================================
-- 1. DIM_DATE - Dimensi Waktu
-- =====================================================
CREATE TABLE dim_date (
    date_key INT PRIMARY KEY COMMENT 'YYYYMMDD format',
    date_value DATE NOT NULL UNIQUE,
    year SMALLINT NOT NULL,
    quarter TINYINT NOT NULL,
    month TINYINT NOT NULL,
    week TINYINT NOT NULL,
    day TINYINT NOT NULL,
    day_of_year SMALLINT NOT NULL,
    day_name VARCHAR(10) NOT NULL,
    day_of_week TINYINT NOT NULL,
    is_weekend BOOLEAN DEFAULT FALSE,
    month_name VARCHAR(10) NOT NULL,
    month_short VARCHAR(3) NOT NULL,
    quarter_name VARCHAR(7) NOT NULL,
    fiscal_year SMALLINT NOT NULL,
    fiscal_quarter TINYINT NOT NULL,
    fiscal_month TINYINT NOT NULL,
    is_holiday BOOLEAN DEFAULT FALSE,
    holiday_name VARCHAR(100) NULL,
    season ENUM('spring', 'summer', 'autumn', 'winter') NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_year (year),
    INDEX idx_quarter (quarter),
    INDEX idx_month (month),
    INDEX idx_fiscal_year (fiscal_year),
    INDEX idx_is_holiday (is_holiday),
    INDEX idx_is_weekend (is_weekend)
) ENGINE=InnoDB COMMENT='Dimensi waktu untuk data warehouse';

-- =====================================================
-- 2. DIM_CUSTOMER - Dimensi Customer
-- =====================================================
CREATE TABLE dim_customer (
    customer_key BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke orang.persons',
    customer_code VARCHAR(50) NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_type ENUM('individual', 'company', 'government', 'other') DEFAULT 'individual',
    customer_category ENUM('regular', 'vip', 'wholesale', 'retail', 'new') DEFAULT 'regular',
    gender ENUM('male', 'female', 'other') NULL,
    age_group ENUM('0-17', '18-25', '26-35', '36-45', '46-55', '56-65', '65+') NULL,
    registration_date DATE NULL,
    city VARCHAR(100) NULL,
    province VARCHAR(100) NULL,
    region VARCHAR(100) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    total_lifetime_value DECIMAL(15,2) DEFAULT 0,
    total_orders INT DEFAULT 0,
    avg_order_value DECIMAL(15,2) DEFAULT 0,
    last_order_date DATE NULL,
    days_since_last_order INT DEFAULT 999,
    loyalty_tier ENUM('bronze', 'silver', 'gold', 'platinum', 'diamond') DEFAULT 'bronze',
    credit_limit DECIMAL(15,2) DEFAULT 0,
    payment_terms VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_customer_id (customer_id),
    INDEX idx_customer_code (customer_code),
    INDEX idx_customer_type (customer_type),
    INDEX idx_customer_category (customer_category),
    INDEX idx_city (city),
    INDEX idx_province (province),
    INDEX idx_region (region),
    INDEX idx_registration_date (registration_date),
    INDEX idx_loyalty_tier (loyalty_tier),
    INDEX idx_total_lifetime_value (total_lifetime_value),
    INDEX idx_days_since_last_order (days_since_last_order)
) ENGINE=InnoDB COMMENT='Dimensi customer untuk analytics';

-- =====================================================
-- 3. DIM_PRODUCT - Dimensi Produk
-- =====================================================
CREATE TABLE dim_product (
    product_key BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke barang.products',
    product_code VARCHAR(50) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    category_path VARCHAR(500) NULL COMMENT 'Full category hierarchy',
    category_l1 VARCHAR(100) NULL COMMENT 'Level 1 category',
    category_l2 VARCHAR(100) NULL COMMENT 'Level 2 category',
    category_l3 VARCHAR(100) NULL COMMENT 'Level 3 category',
    brand_name VARCHAR(100) NULL,
    product_line VARCHAR(100) NULL,
    product_group VARCHAR(100) NULL,
    unit_of_measure VARCHAR(20) NOT NULL,
    weight_class ENUM('light', 'medium', 'heavy') NULL,
    size_class ENUM('small', 'medium', 'large', 'xlarge') NULL,
    price_tier ENUM('economy', 'standard', 'premium', 'luxury') DEFAULT 'standard',
    margin_tier ENUM('low', 'medium', 'high', 'very_high') DEFAULT 'medium',
    is_seasonal BOOLEAN DEFAULT FALSE,
    season_type ENUM('spring', 'summer', 'autumn', 'winter', 'all_year') DEFAULT 'all_year',
    is_perishable BOOLEAN DEFAULT FALSE,
    shelf_life_days INT NULL,
    storage_requirement ENUM('dry', 'cold', 'frozen', 'special') DEFAULT 'dry',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_product_id (product_id),
    INDEX idx_product_code (product_code),
    INDEX idx_category_l1 (category_l1),
    INDEX idx_category_l2 (category_l2),
    INDEX idx_category_l3 (category_l3),
    INDEX idx_brand_name (brand_name),
    INDEX idx_product_line (product_line),
    INDEX idx_price_tier (price_tier),
    INDEX idx_margin_tier (margin_tier),
    INDEX idx_is_seasonal (is_seasonal),
    INDEX idx_is_perishable (is_perishable)
) ENGINE=InnoDB COMMENT='Dimensi produk untuk analytics';

-- =====================================================
-- 4. DIM_WAREHOUSE - Dimensi Gudang
-- =====================================================
CREATE TABLE dim_warehouse (
    warehouse_key BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    warehouse_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke barang.warehouses',
    warehouse_code VARCHAR(20) NOT NULL,
    warehouse_name VARCHAR(100) NOT NULL,
    warehouse_type ENUM('main', 'branch', 'transit', 'return', 'virtual') DEFAULT 'branch',
    city VARCHAR(100) NULL,
    province VARCHAR(100) NULL,
    region VARCHAR(100) NULL,
    warehouse_size ENUM('small', 'medium', 'large', 'xlarge') DEFAULT 'medium',
    capacity_tier ENUM('low', 'medium', 'high', 'very_high') DEFAULT 'medium',
    automation_level ENUM('manual', 'semi_auto', 'fully_auto') DEFAULT 'manual',
    temperature_control ENUM('ambient', 'cold', 'frozen', 'variable') DEFAULT 'ambient',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_warehouse_id (warehouse_id),
    INDEX idx_warehouse_code (warehouse_code),
    INDEX idx_warehouse_type (warehouse_type),
    INDEX idx_city (city),
    INDEX idx_province (province),
    INDEX idx_region (region),
    INDEX idx_warehouse_size (warehouse_size),
    INDEX idx_automation_level (automation_level)
) ENGINE=InnoDB COMMENT='Dimensi gudang untuk analytics';

-- =====================================================
-- 5. DIM_SUPPLIER - Dimensi Supplier
-- =====================================================
CREATE TABLE dim_supplier (
    supplier_key BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    supplier_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke barang.suppliers',
    supplier_code VARCHAR(20) NOT NULL,
    supplier_name VARCHAR(255) NOT NULL,
    supplier_type ENUM('manufacturer', 'distributor', 'wholesaler', 'importer', 'local') DEFAULT 'distributor',
    supplier_category ENUM('strategic', 'preferred', 'approved', 'trial') DEFAULT 'approved',
    city VARCHAR(100) NULL,
    province VARCHAR(100) NULL,
    region VARCHAR(100) NULL,
    country VARCHAR(100) NULL,
    business_size ENUM('small', 'medium', 'large', 'enterprise') DEFAULT 'medium',
    quality_rating ENUM('a', 'b', 'c', 'd') DEFAULT 'b',
    delivery_performance ENUM('excellent', 'good', 'average', 'poor') DEFAULT 'average',
    payment_terms VARCHAR(100) NULL,
    lead_time_tier ENUM('short', 'medium', 'long') DEFAULT 'medium',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_supplier_id (supplier_id),
    INDEX idx_supplier_code (supplier_code),
    INDEX idx_supplier_type (supplier_type),
    INDEX idx_supplier_category (supplier_category),
    INDEX idx_city (city),
    INDEX idx_province (province),
    INDEX idx_region (region),
    INDEX idx_business_size (business_size),
    INDEX idx_quality_rating (quality_rating),
    INDEX idx_delivery_performance (delivery_performance)
) ENGINE=InnoDB COMMENT='Dimensi supplier untuk analytics';

-- =====================================================
-- 6. FACT_SALES - Fact Table Penjualan
-- =====================================================
CREATE TABLE fact_sales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    date_key INT NOT NULL COMMENT 'Link ke dim_date',
    customer_key BIGINT UNSIGNED NOT NULL COMMENT 'Link ke dim_customer',
    product_key BIGINT UNSIGNED NOT NULL COMMENT 'Link ke dim_product',
    warehouse_key BIGINT UNSIGNED NOT NULL COMMENT 'Link ke dim_warehouse',
    sales_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke aplikasi.sales',
    sales_code VARCHAR(30) NOT NULL,
    quantity DECIMAL(12,2) NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    gross_revenue DECIMAL(15,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    net_revenue DECIMAL(15,2) GENERATED ALWAYS AS (gross_revenue - discount_amount) STORED,
    cost_of_goods_sold DECIMAL(15,2) DEFAULT 0,
    gross_profit DECIMAL(15,2) GENERATED ALWAYS AS (net_revenue - cost_of_goods_sold) STORED,
    gross_profit_percent DECIMAL(5,2) GENERATED ALWAYS AS (CASE WHEN net_revenue > 0 THEN (gross_profit / net_revenue * 100) ELSE 0 END) STORED,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) GENERATED ALWAYS AS (net_revenue + tax_amount) STORED,
    sales_channel ENUM('offline', 'online', 'phone', 'email', 'mobile', 'api') DEFAULT 'offline',
    payment_method ENUM('cash', 'transfer', 'card', 'ewallet', 'credit') DEFAULT 'cash',
    sales_type ENUM('regular', 'promotion', 'clearance', 'bulk', 'contract') DEFAULT 'regular',
    is_return BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (date_key) REFERENCES dim_date(date_key),
    FOREIGN KEY (customer_key) REFERENCES dim_customer(customer_key),
    FOREIGN KEY (product_key) REFERENCES dim_product(product_key),
    FOREIGN KEY (warehouse_key) REFERENCES dim_warehouse(warehouse_key),
    
    INDEX idx_date_key (date_key),
    INDEX idx_customer_key (customer_key),
    INDEX idx_product_key (product_key),
    INDEX idx_warehouse_key (warehouse_key),
    INDEX idx_sales_id (sales_id),
    INDEX idx_quantity (quantity),
    INDEX idx_gross_revenue (gross_revenue),
    INDEX idx_net_revenue (net_revenue),
    INDEX idx_gross_profit (gross_profit),
    INDEX idx_sales_channel (sales_channel),
    INDEX idx_payment_method (payment_method),
    INDEX idx_sales_type (sales_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB COMMENT='Fact table penjualan untuk analytics';

-- =====================================================
-- 7. FACT_PURCHASES - Fact Table Pembelian
-- =====================================================
CREATE TABLE fact_purchases (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    date_key INT NOT NULL COMMENT 'Link ke dim_date',
    supplier_key BIGINT UNSIGNED NOT NULL COMMENT 'Link ke dim_supplier',
    product_key BIGINT UNSIGNED NOT NULL COMMENT 'Link ke dim_product',
    warehouse_key BIGINT UNSIGNED NOT NULL COMMENT 'Link ke dim_warehouse',
    purchase_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke aplikasi.purchases',
    purchase_code VARCHAR(30) NOT NULL,
    quantity_ordered DECIMAL(12,2) NOT NULL,
    quantity_received DECIMAL(12,2) DEFAULT 0,
    unit_price DECIMAL(15,2) NOT NULL,
    gross_cost DECIMAL(15,2) GENERATED ALWAYS AS (quantity_ordered * unit_price) STORED,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    net_cost DECIMAL(15,2) GENERATED ALWAYS AS (gross_cost - discount_amount) STORED,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    shipping_cost DECIMAL(15,2) DEFAULT 0,
    other_cost DECIMAL(15,2) DEFAULT 0,
    total_cost DECIMAL(15,2) GENERATED ALWAYS AS (net_cost + tax_amount + shipping_cost + other_cost) STORED,
    purchase_type ENUM('regular', 'consignment', 'preorder', 'emergency') DEFAULT 'regular',
    payment_terms VARCHAR(100) NULL,
    lead_time_days INT DEFAULT 0,
    quality_rating ENUM('excellent', 'good', 'average', 'poor') DEFAULT 'average',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (date_key) REFERENCES dim_date(date_key),
    FOREIGN KEY (supplier_key) REFERENCES dim_supplier(supplier_key),
    FOREIGN KEY (product_key) REFERENCES dim_product(product_key),
    FOREIGN KEY (warehouse_key) REFERENCES dim_warehouse(warehouse_key),
    
    INDEX idx_date_key (date_key),
    INDEX idx_supplier_key (supplier_key),
    INDEX idx_product_key (product_key),
    INDEX idx_warehouse_key (warehouse_key),
    INDEX idx_purchase_id (purchase_id),
    INDEX idx_quantity_ordered (quantity_ordered),
    INDEX idx_quantity_received (quantity_received),
    INDEX idx_gross_cost (gross_cost),
    INDEX idx_net_cost (net_cost),
    INDEX idx_total_cost (total_cost),
    INDEX idx_purchase_type (purchase_type),
    INDEX idx_lead_time_days (lead_time_days),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB COMMENT='Fact table pembelian untuk analytics';

-- =====================================================
-- 8. FACT_INVENTORY - Fact Table Inventory
-- =====================================================
CREATE TABLE fact_inventory (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    date_key INT NOT NULL COMMENT 'Link ke dim_date',
    product_key BIGINT UNSIGNED NOT NULL COMMENT 'Link ke dim_product',
    warehouse_key BIGINT UNSIGNED NOT NULL COMMENT 'Link ke dim_warehouse',
    opening_stock DECIMAL(12,2) DEFAULT 0,
    quantity_in DECIMAL(12,2) DEFAULT 0,
    quantity_out DECIMAL(12,2) DEFAULT 0,
    closing_stock DECIMAL(12,2) GENERATED ALWAYS AS (opening_stock + quantity_in - quantity_out) STORED,
    stock_value DECIMAL(15,2) DEFAULT 0,
    average_cost DECIMAL(15,2) DEFAULT 0,
    turnover_ratio DECIMAL(8,2) DEFAULT 0,
    days_of_supply INT DEFAULT 0,
    stock_status ENUM('optimal', 'low', 'critical', 'overstock', 'out_of_stock') DEFAULT 'optimal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (date_key) REFERENCES dim_date(date_key),
    FOREIGN KEY (product_key) REFERENCES dim_product(product_key),
    FOREIGN KEY (warehouse_key) REFERENCES dim_warehouse(warehouse_key),
    
    INDEX idx_date_key (date_key),
    INDEX idx_product_key (product_key),
    INDEX idx_warehouse_key (warehouse_key),
    INDEX idx_opening_stock (opening_stock),
    INDEX idx_closing_stock (closing_stock),
    INDEX idx_stock_value (stock_value),
    INDEX idx_turnover_ratio (turnover_ratio),
    INDEX idx_days_of_supply (days_of_supply),
    INDEX idx_stock_status (stock_status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB COMMENT='Fact table inventory untuk analytics';

-- =====================================================
-- 9. ANALYTICS_REPORTS - Report Templates
-- =====================================================
CREATE TABLE analytics_reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    report_code VARCHAR(30) UNIQUE NOT NULL,
    report_name VARCHAR(100) NOT NULL,
    report_category ENUM('sales', 'inventory', 'financial', 'customer', 'supplier', 'operational') NOT NULL,
    report_type ENUM('summary', 'trend', 'comparison', 'drill_down', 'dashboard') DEFAULT 'summary',
    description TEXT NULL,
    query_definition LONGTEXT NOT NULL COMMENT 'SQL query untuk report',
    parameters JSON NULL COMMENT 'Parameter yang bisa diinput',
    visualization_config JSON NULL COMMENT 'Konfigurasi visualisasi',
    schedule_config JSON NULL COMMENT 'Konfigurasi schedule',
    output_format ENUM('table', 'chart', 'pivot', 'kpi') DEFAULT 'table',
    is_active BOOLEAN DEFAULT TRUE,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_report_code (report_code),
    INDEX idx_report_category (report_category),
    INDEX idx_report_type (report_type),
    INDEX idx_is_active (is_active),
    INDEX idx_is_public (is_public),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB COMMENT='Template report analytics';

-- =====================================================
-- 10. DASHBOARD_WIDGETS - Widget Dashboard
-- =====================================================
CREATE TABLE dashboard_widgets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    widget_code VARCHAR(30) UNIQUE NOT NULL,
    widget_name VARCHAR(100) NOT NULL,
    widget_type ENUM('kpi', 'chart', 'table', 'gauge', 'metric', 'trend') DEFAULT 'kpi',
    widget_category ENUM('sales', 'inventory', 'financial', 'customer', 'operational') NOT NULL,
    data_source LONGTEXT NOT NULL COMMENT 'SQL query atau API endpoint',
    visualization_config JSON NULL COMMENT 'Konfigurasi visualisasi',
    refresh_interval INT DEFAULT 300 COMMENT 'Refresh interval (detik)',
    position_config JSON NULL COMMENT 'Konfigurasi posisi di dashboard',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_widget_code (widget_code),
    INDEX idx_widget_type (widget_type),
    INDEX idx_widget_category (widget_category),
    INDEX idx_is_active (is_active),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB COMMENT='Widget untuk dashboard analytics';

-- =====================================================
-- 11. KPI_METRICS - KPI Tracking
-- =====================================================
CREATE TABLE kpi_metrics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kpi_code VARCHAR(30) UNIQUE NOT NULL,
    kpi_name VARCHAR(100) NOT NULL,
    kpi_category ENUM('sales', 'inventory', 'financial', 'customer', 'operational', 'quality') NOT NULL,
    kpi_type ENUM('absolute', 'percentage', 'ratio', 'rate', 'index') DEFAULT 'absolute',
    description TEXT NULL,
    target_value DECIMAL(15,2) NULL COMMENT 'Target value',
    tolerance_range JSON NULL COMMENT 'Range toleransi',
    calculation_formula LONGTEXT NULL COMMENT 'Formula perhitungan',
    data_source LONGTEXT NOT NULL COMMENT 'Source data query',
    reporting_frequency ENUM('real_time', 'hourly', 'daily', 'weekly', 'monthly') DEFAULT 'daily',
    benchmark_value DECIMAL(15,2) NULL COMMENT 'Nilai benchmark',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_kpi_code (kpi_code),
    INDEX idx_kpi_category (kpi_category),
    INDEX idx_kpi_type (kpi_type),
    INDEX idx_reporting_frequency (reporting_frequency),
    INDEX idx_is_active (is_active),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB COMMENT='Master KPI untuk tracking performa';

-- =====================================================
-- 12. KPI_VALUES - Historical KPI Values
-- =====================================================
CREATE TABLE kpi_values (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kpi_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke kpi_metrics',
    date_key INT NOT NULL COMMENT 'Link ke dim_date',
    actual_value DECIMAL(15,2) NOT NULL,
    target_value DECIMAL(15,2) NULL,
    variance DECIMAL(15,2) GENERATED ALWAYS AS (actual_value - IFNULL(target_value, 0)) STORED,
    variance_percent DECIMAL(5,2) GENERATED ALWAYS AS (CASE WHEN IFNULL(target_value, 0) != 0 THEN ((actual_value - target_value) / target_value * 100) ELSE 0 END) STORED,
    performance_level ENUM('excellent', 'good', 'average', 'poor', 'critical') DEFAULT 'average',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (kpi_id) REFERENCES kpi_metrics(id) ON DELETE CASCADE,
    FOREIGN KEY (date_key) REFERENCES dim_date(date_key),
    
    UNIQUE KEY unique_kpi_date (kpi_id, date_key),
    INDEX idx_kpi_id (kpi_id),
    INDEX idx_date_key (date_key),
    INDEX idx_actual_value (actual_value),
    INDEX idx_performance_level (performance_level),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB COMMENT='Historical values untuk KPI';

-- =====================================================
-- INSERT DEFAULT ANALYTICS REPORTS
-- =====================================================

INSERT INTO analytics_reports (report_code, report_name, report_category, report_type, description, query_definition, is_active) VALUES
('SALES_DAILY_SUMMARY', 'Sales Harian', 'sales', 'summary', 'Ringkasan penjualan harian per kategori', 
 'SELECT d.date_value, dc.category_l1, SUM(fs.quantity) as total_qty, SUM(fs.net_revenue) as revenue FROM fact_sales fs JOIN dim_date d ON fs.date_key = d.date_key JOIN dim_product dp ON fs.product_key = dp.product_key WHERE d.date_value = CURDATE() GROUP BY d.date_value, dc.category_l1', TRUE),

('SALES_MONTHLY_TREND', 'Trend Penjualan Bulanan', 'sales', 'trend', 'Trend penjualan 12 bulan terakhir',
 'SELECT YEAR(d.date_value) as year, MONTH(d.date_value) as month, SUM(fs.net_revenue) as revenue, SUM(fs.quantity) as qty, COUNT(DISTINCT fs.customer_key) as customers FROM fact_sales fs JOIN dim_date d ON fs.date_key = d.date_key WHERE d.date_value >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY YEAR(d.date_value), MONTH(d.date_value) ORDER BY year, month', TRUE),

('INVENTORY_STATUS', 'Status Inventory', 'inventory', 'summary', 'Status inventory per gudang dan kategori',
 'SELECT dw.warehouse_name, dp.category_l1, SUM(fi.closing_stock) as total_stock, SUM(fi.stock_value) as total_value FROM fact_inventory fi JOIN dim_warehouse dw ON fi.warehouse_key = dw.warehouse_key JOIN dim_product dp ON fi.product_key = dp.product_key WHERE fi.date_key = (SELECT MAX(date_key) FROM dim_date) GROUP BY dw.warehouse_name, dp.category_l1', TRUE),

('CUSTOMER_ANALYSIS', 'Analisis Customer', 'customer', 'drill_down', 'Analisis customer berdasarkan nilai dan frekuensi',
 'SELECT dc.customer_name, dc.customer_type, COUNT(fs.sales_id) as order_count, SUM(fs.net_revenue) as total_spent, AVG(fs.net_revenue) as avg_order, MAX(d.date_value) as last_order FROM fact_sales fs JOIN dim_customer dc ON fs.customer_key = dc.customer_key JOIN dim_date d ON fs.date_key = d.date_key GROUP BY dc.customer_key ORDER BY total_spent DESC', TRUE);

-- Default Dashboard Widgets
INSERT INTO dashboard_widgets (widget_code, widget_name, widget_type, widget_category, data_source, refresh_interval) VALUES
('TOTAL_SALES_TODAY', 'Total Penjualan Hari Ini', 'kpi', 'sales', 'SELECT COALESCE(SUM(net_revenue), 0) as value FROM fact_sales WHERE date_key = (SELECT date_key FROM dim_date WHERE date_value = CURDATE())', 300),
('TOTAL_ORDERS_TODAY', 'Total Order Hari Ini', 'metric', 'sales', 'SELECT COUNT(DISTINCT sales_id) as value FROM fact_sales WHERE date_key = (SELECT date_key FROM dim_date WHERE date_value = CURDATE())', 300),
('LOW_STOCK_ITEMS', 'Item Stok Rendah', 'metric', 'inventory', 'SELECT COUNT(*) as value FROM fact_inventory WHERE stock_status IN (''low'', ''critical'') AND date_key = (SELECT MAX(date_key) FROM dim_date)', 600),
('TOP_CUSTOMERS', 'Top 5 Customers', 'table', 'customer', 'SELECT dc.customer_name, SUM(fs.net_revenue) as revenue FROM fact_sales fs JOIN dim_customer dc ON fs.customer_key = dc.customer_key WHERE fs.date_key >= (SELECT date_key FROM dim_date WHERE date_value >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) GROUP BY dc.customer_key ORDER BY revenue DESC LIMIT 5', 3600);

-- Default KPI Metrics
INSERT INTO kpi_metrics (kpi_code, kpi_name, kpi_category, kpi_type, description, target_value, reporting_frequency) VALUES
('DAILY_REVENUE', 'Revenue Harian', 'sales', 'absolute', 'Total penjualan per hari', 10000000, 'daily'),
('GROSS_MARGIN', 'Gross Margin %', 'financial', 'percentage', 'Persentase gross margin', 25.00, 'daily'),
('INVENTORY_TURNOVER', 'Inventory Turnover', 'inventory', 'ratio', 'Rasio perputaran inventory', 4.00, 'monthly'),
('CUSTOMER_RETENTION', 'Customer Retention Rate', 'customer', 'percentage', 'Tingkat retensi customer', 80.00, 'monthly'),
('ON_TIME_DELIVERY', 'On-Time Delivery %', 'operational', 'percentage', 'Persentase pengiriman tepat waktu', 95.00, 'weekly');

-- =====================================================
-- VIEWS untuk analytics
-- =====================================================

-- View untuk sales performance summary
CREATE VIEW v_sales_performance_summary AS
SELECT 
    d.date_value,
    d.year,
    d.month,
    d.quarter,
    COUNT(DISTINCT fs.sales_id) as total_orders,
    COUNT(DISTINCT fs.customer_key) as unique_customers,
    SUM(fs.quantity) as total_quantity,
    SUM(fs.gross_revenue) as gross_revenue,
    SUM(fs.net_revenue) as net_revenue,
    SUM(fs.gross_profit) as gross_profit,
    AVG(fs.gross_profit_percent) as avg_margin_percent,
    SUM(CASE WHEN fs.is_return = TRUE THEN 1 ELSE 0 END) as return_count,
    SUM(CASE WHEN fs.is_return = TRUE THEN fs.net_revenue ELSE 0 END) as return_value
FROM fact_sales fs
JOIN dim_date d ON fs.date_key = d.date_key
GROUP BY d.date_key;

-- View untuk inventory health
CREATE VIEW v_inventory_health AS
SELECT 
    dp.product_name,
    dp.category_l1,
    dw.warehouse_name,
    fi.closing_stock,
    fi.stock_value,
    fi.turnover_ratio,
    fi.days_of_supply,
    fi.stock_status,
    CASE 
        WHEN fi.stock_status = 'optimal' THEN 1
        WHEN fi.stock_status = 'low' THEN 2
        WHEN fi.stock_status = 'critical' THEN 3
        WHEN fi.stock_status = 'overstock' THEN 2
        WHEN fi.stock_status = 'out_of_stock' THEN 4
    END as priority_level
FROM fact_inventory fi
JOIN dim_product dp ON fi.product_key = dp.product_key
JOIN dim_warehouse dw ON fi.warehouse_key = dw.warehouse_key
WHERE fi.date_key = (SELECT MAX(date_key) FROM dim_date)
ORDER BY priority_level DESC, fi.days_of_supply ASC;
