-- ========================================
-- CRM (Customer Relationship Management) Schema
-- ========================================

-- Tabel Master Customer
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(50),
    email VARCHAR(100),
    address TEXT,
    city VARCHAR(100),
    province VARCHAR(100),
    postal_code VARCHAR(10),
    customer_type ENUM('RETAIL', 'WHOLESALE', 'DISTRIBUTOR', 'AGENCY') DEFAULT 'RETAIL',
    tax_id VARCHAR(50),
    credit_limit DECIMAL(15,2) DEFAULT 0,
    current_debt DECIMAL(15,2) DEFAULT 0,
    available_credit DECIMAL(15,2) GENERATED ALWAYS AS (credit_limit - current_debt) STORED,
    loyalty_points INT DEFAULT 0,
    total_spent DECIMAL(15,2) DEFAULT 0,
    last_purchase_date DATE,
    registration_date DATE,
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_customer_code (customer_code),
    INDEX idx_customer_type (customer_type),
    INDEX idx_is_active (is_active),
    INDEX idx_registration_date (registration_date)
);

-- Tabel Customer Segments
CREATE TABLE customer_segments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    special_offers TEXT,
    minimum_purchase DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_segment_name (name)
);

-- Tabel Hubungan Customer dengan Segments
CREATE TABLE customer_segment_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    segment_id INT NOT NULL,
    assigned_date DATE,
    assigned_by INT,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (segment_id) REFERENCES customer_segments(id) ON DELETE CASCADE,
    UNIQUE KEY unique_customer_segment (customer_id, segment_id)
);

-- Tabel Riwayat Pembelian Customer
CREATE TABLE customer_purchase_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    invoice_number VARCHAR(50),
    purchase_date DATE NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    final_amount DECIMAL(15,2) NOT NULL,
    payment_method VARCHAR(50),
    payment_status ENUM('PAID', 'PARTIAL', 'UNPAID') DEFAULT 'UNPAID',
    salesperson_id INT,
    branch_id INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    INDEX idx_customer_purchase (customer_id),
    INDEX idx_purchase_date (purchase_date),
    INDEX idx_payment_status (payment_status)
);

-- Tabel Detail Items Pembelian Customer
CREATE TABLE customer_purchase_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_history_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity DECIMAL(15,3) NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    subtotal DECIMAL(15,2) NOT NULL,
    
    FOREIGN KEY (purchase_history_id) REFERENCES customer_purchase_history(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_purchase_items (purchase_history_id),
    INDEX idx_product_id (product_id)
);

-- Tabel Loyalty Points
CREATE TABLE loyalty_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    transaction_type ENUM('EARN', 'REDEEM') NOT NULL,
    points INT NOT NULL,
    reference_type ENUM('PURCHASE', 'REFERRAL', 'BONUS', 'REDEMPTION') NOT NULL,
    reference_id INT,
    description TEXT,
    transaction_date DATETIME NOT NULL,
    expiry_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    INDEX idx_customer_loyalty (customer_id),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_transaction_type (transaction_type)
);

-- Tabel Customer Communications
CREATE TABLE customer_communications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    communication_type ENUM('EMAIL', 'SMS', 'WHATSAPP', 'PHONE', 'LETTER') NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('SENT', 'DELIVERED', 'FAILED', 'BOUNCE') DEFAULT 'SENT',
    sent_date DATETIME,
    scheduled_date DATETIME,
    sent_by INT,
    template_id INT,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    INDEX idx_customer_comm (customer_id),
    INDEX idx_comm_type (communication_type),
    INDEX idx_sent_date (sent_date)
);

-- Tabel Customer Feedback/Ratings
CREATE TABLE customer_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    purchase_history_id INT,
    rating DECIMAL(2,1) CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    feedback_type ENUM('PRODUCT', 'SERVICE', 'DELIVERY', 'GENERAL') DEFAULT 'GENERAL',
    status ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'PENDING',
    response_text TEXT,
    responded_by INT,
    response_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (purchase_history_id) REFERENCES customer_purchase_history(id),
    INDEX idx_customer_feedback (customer_id),
    INDEX idx_rating (rating),
    INDEX idx_feedback_type (feedback_type)
);

-- Tabel Credit Management
CREATE TABLE customer_credit_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    credit_limit DECIMAL(15,2) NOT NULL,
    approved_by INT,
    approval_date DATE,
    expiry_date DATE,
    terms_days INT DEFAULT 30,
    status ENUM('ACTIVE', 'EXPIRED', 'SUSPENDED') DEFAULT 'ACTIVE',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    INDEX idx_customer_credit (customer_id),
    INDEX idx_status (status)
);

-- Tabel Customer Groups/Tags
CREATE TABLE customer_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tag_name VARCHAR(50) NOT NULL,
    tag_color VARCHAR(7) DEFAULT '#007bff',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_tag_name (tag_name)
);

-- Tabel Hubungan Customer dengan Tags
CREATE TABLE customer_tag_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    tag_id INT NOT NULL,
    assigned_date DATE,
    assigned_by INT,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES customer_tags(id) ON DELETE CASCADE,
    UNIQUE KEY unique_customer_tag (customer_id, tag_id)
);

-- View untuk Customer Summary
CREATE VIEW customer_summary_view AS
SELECT 
    c.id,
    c.customer_code,
    c.name,
    c.phone,
    c.email,
    c.customer_type,
    c.credit_limit,
    c.current_debt,
    c.available_credit,
    c.loyalty_points,
    c.total_spent,
    c.last_purchase_date,
    c.is_active,
    COUNT(DISTINCT ph.id) as total_purchases,
    COALESCE(AVG(cf.rating), 0) as average_rating,
    COUNT(DISTINCT cf.id) as total_feedback,
    GROUP_CONCAT(DISTINCT ct.tag_name SEPARATOR ', ') as tags
FROM customers c
LEFT JOIN customer_purchase_history ph ON c.id = ph.customer_id
LEFT JOIN customer_feedback cf ON c.id = cf.customer_id
LEFT JOIN customer_tag_assignments cta ON c.id = cta.customer_id
LEFT JOIN customer_tags ct ON cta.tag_id = ct.id
GROUP BY c.id;
