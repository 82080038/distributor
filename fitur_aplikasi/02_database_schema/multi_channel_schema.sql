-- ========================================
-- Multi-Channel Sales Management Schema
-- ========================================

-- Tabel Sales Channels
CREATE TABLE sales_channels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    channel_name VARCHAR(100) NOT NULL,
    channel_type ENUM('OFFLINE', 'ONLINE_MARKETPLACE', 'WEBSITE', 'SOCIAL_COMMERCE', 'B2B_PORTAL') NOT NULL,
    platform_name VARCHAR(100),
    api_endpoint VARCHAR(255),
    api_key_encrypted TEXT,
    api_secret_encrypted TEXT,
    commission_rate DECIMAL(5,2) DEFAULT 0,
    transaction_fee_rate DECIMAL(5,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    sync_status ENUM('ACTIVE', 'INACTIVE', 'ERROR') DEFAULT 'ACTIVE',
    last_sync_at TIMESTAMP NULL,
    configuration JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_channel_type (channel_type),
    INDEX idx_is_active (is_active),
    INDEX idx_sync_status (sync_status)
);

-- Tabel Channel Product Mappings
CREATE TABLE channel_product_mappings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    channel_id INT NOT NULL,
    local_product_id INT NOT NULL,
    channel_product_id VARCHAR(100),
    channel_sku VARCHAR(100),
    price_override DECIMAL(15,2),
    stock_override DECIMAL(15,3),
    status ENUM('ACTIVE', 'INACTIVE', 'PENDING') DEFAULT 'ACTIVE',
    last_sync_at TIMESTAMP,
    sync_error TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (channel_id) REFERENCES sales_channels(id) ON DELETE CASCADE,
    FOREIGN KEY (local_product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_channel_product (channel_id, local_product_id),
    INDEX idx_channel_sku (channel_sku)
);

-- Tabel Channel Orders (Pesanan dari Berbagai Channel)
CREATE TABLE channel_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    channel_id INT NOT NULL,
    channel_order_id VARCHAR(100) NOT NULL,
    customer_id INT,
    customer_name VARCHAR(150),
    customer_phone VARCHAR(50),
    customer_email VARCHAR(100),
    customer_address TEXT,
    order_date DATETIME NOT NULL,
    order_status ENUM('PENDING', 'CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'CANCELLED', 'RETURNED') DEFAULT 'PENDING',
    payment_status ENUM('PENDING', 'PAID', 'FAILED', 'REFUNDED') DEFAULT 'PENDING',
    subtotal_amount DECIMAL(15,2) NOT NULL,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    shipping_amount DECIMAL(15,2) DEFAULT 0,
    commission_amount DECIMAL(15,2) DEFAULT 0,
    transaction_fee_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) NOT NULL,
    notes TEXT,
    raw_order_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (channel_id) REFERENCES sales_channels(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    INDEX idx_channel_order (channel_id, channel_order_id),
    INDEX idx_order_date (order_date),
    INDEX idx_order_status (order_status),
    INDEX idx_payment_status (payment_status)
);

-- Tabel Channel Order Items
CREATE TABLE channel_order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(150),
    product_sku VARCHAR(100),
    quantity DECIMAL(15,3) NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    subtotal DECIMAL(15,2) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES channel_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_order_items (order_id),
    INDEX idx_product_sku (product_sku)
);

-- Tabel Inventory Sync Logs
CREATE TABLE inventory_sync_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    channel_id INT NOT NULL,
    product_id INT NOT NULL,
    sync_type ENUM('STOCK_UPDATE', 'PRICE_UPDATE', 'PRODUCT_CREATE', 'PRODUCT_UPDATE', 'PRODUCT_DELETE') NOT NULL,
    direction ENUM('TO_CHANNEL', 'FROM_CHANNEL') NOT NULL,
    quantity_before DECIMAL(15,3),
    quantity_after DECIMAL(15,3),
    price_before DECIMAL(15,2),
    price_after DECIMAL(15,2),
    sync_status ENUM('SUCCESS', 'FAILED', 'PENDING') DEFAULT 'PENDING',
    error_message TEXT,
    sync_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (channel_id) REFERENCES sales_channels(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_sync_timestamp (sync_timestamp),
    INDEX idx_sync_status (sync_status)
);

-- Tabel Channel Configurations
CREATE TABLE channel_configurations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    channel_id INT NOT NULL,
    config_key VARCHAR(100) NOT NULL,
    config_value TEXT,
    config_type ENUM('STRING', 'NUMBER', 'BOOLEAN', 'JSON') DEFAULT 'STRING',
    is_encrypted BOOLEAN DEFAULT FALSE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (channel_id) REFERENCES sales_channels(id) ON DELETE CASCADE,
    UNIQUE KEY unique_channel_config (channel_id, config_key)
);

-- Tabel Cross-Listing Templates
CREATE TABLE cross_listing_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_name VARCHAR(100) NOT NULL,
    channel_type ENUM('ONLINE_MARKETPLACE', 'SOCIAL_COMMERCE', 'WEBSITE') NOT NULL,
    title_template TEXT,
    description_template TEXT,
    tags_template TEXT,
    category_mapping JSON,
    attribute_mapping JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_template_name (template_name),
    INDEX idx_channel_type (channel_type)
);

-- Tabel Order Fulfillment
CREATE TABLE order_fulfillment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    fulfillment_type ENUM('PICKUP', 'DELIVERY', 'SHIPPING', 'DIGITAL') DEFAULT 'DELIVERY',
    tracking_number VARCHAR(100),
    carrier_name VARCHAR(100),
    shipping_address TEXT,
    estimated_delivery_date DATE,
    actual_delivery_date DATE,
    delivery_cost DECIMAL(15,2) DEFAULT 0,
    status ENUM('PREPARING', 'READY', 'SHIPPED', 'IN_TRANSIT', 'DELIVERED', 'FAILED') DEFAULT 'PREPARING',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES channel_orders(id) ON DELETE CASCADE,
    INDEX idx_tracking_number (tracking_number),
    INDEX idx_status (status),
    INDEX idx_delivery_date (actual_delivery_date)
);

-- Tabel Channel Performance Analytics
CREATE TABLE channel_performance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    channel_id INT NOT NULL,
    report_date DATE NOT NULL,
    total_orders INT DEFAULT 0,
    total_revenue DECIMAL(15,2) DEFAULT 0,
    total_items_sold DECIMAL(15,3) DEFAULT 0,
    average_order_value DECIMAL(15,2) GENERATED ALWAYS AS (total_revenue / NULLIF(total_orders, 0)) STORED,
    commission_total DECIMAL(15,2) DEFAULT 0,
    transaction_fee_total DECIMAL(15,2) DEFAULT 0,
    net_revenue DECIMAL(15,2) GENERATED ALWAYS AS (total_revenue - commission_total - transaction_fee_total) STORED,
    conversion_rate DECIMAL(5,2) DEFAULT 0,
    unique_customers INT DEFAULT 0,
    returning_customers INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (channel_id) REFERENCES sales_channels(id),
    UNIQUE KEY unique_channel_date (channel_id, report_date),
    INDEX idx_report_date (report_date)
);

-- View untuk Channel Summary
CREATE VIEW channel_summary_view AS
SELECT 
    sc.channel_name,
    sc.channel_type,
    sc.is_active,
    sc.sync_status,
    COUNT(DISTINCT co.id) as total_orders,
    COALESCE(SUM(co.total_amount), 0) as total_revenue,
    COALESCE(AVG(co.total_amount), 0) as average_order_value,
    COALESCE(SUM(co.commission_amount), 0) as total_commissions,
    COUNT(DISTINCT co.customer_id) as unique_customers,
    sc.last_sync_at
FROM sales_channels sc
LEFT JOIN channel_orders co ON sc.id = co.channel_id 
    AND DATE(co.order_date) = CURDATE()
GROUP BY sc.id;
