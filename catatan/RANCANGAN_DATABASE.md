# RANCANGAN DATABASE KOMPREHENSIF
# SPPG Distribution Management System

## 📋 OVERVIEW

Dokumen ini berisi rancangan database lengkap untuk SPPG Distribution Management System dengan fokus pada:
- Struktur tabel yang optimal
- Relasi yang konsisten
- Performa query yang efisien
- Data integrity
- Scalability
- Indonesian compliance

---

## 🏗️ DATABASE ARCHITECTURE

### Database Engine Selection
```
Primary Database: MySQL 8.0+ / MariaDB 10.8+
- Transactional support
- Full-text search
- JSON data type
- Performance optimization
- Indonesian collation support

Secondary Database: PostgreSQL 15+
- Advanced analytics
- Complex queries
- Geospatial data
- Time-series data

Cache Layer: Redis 7.0+
- Session management
- Real-time caching
- Queue management
- Pub/Sub messaging

Search Engine: Elasticsearch 8.0+
- Full-text search
- Analytics
- Log aggregation
- Real-time monitoring
```

### Database Design Principles
```
1. Normalization: 3NF dengan denormalization strategis
2. Indexing Strategy: Composite indexes untuk query kompleks
3. Partitioning: Range partitioning untuk tabel besar
4. Data Types: Optimal untuk performa dan storage
5. Constraints: Foreign keys dan check constraints
6. Audit Trail: Created_at, updated_at, deleted_at
7. Soft Deletes: Untuk data retention
8. Versioning: Untuk critical business data
```

---

## 📊 CORE BUSINESS ENTITIES

### 1. Users & Authentication System

#### users
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password_hash VARCHAR(255) NOT NULL,
    nip VARCHAR(20) UNIQUE NULL, -- Nomor Induk Pegawai
    nik VARCHAR(16) UNIQUE NULL, -- Nomor Induk Kependudukan
    full_name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NULL,
    avatar_url VARCHAR(500) NULL,
    department_id BIGINT UNSIGNED NULL,
    position_id BIGINT UNSIGNED NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    branch_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_online BOOLEAN DEFAULT FALSE,
    last_login_at TIMESTAMP NULL,
    last_activity_at TIMESTAMP NULL,
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    two_factor_secret VARCHAR(255) NULL,
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    password_changed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_users_email (email),
    INDEX idx_users_username (username),
    INDEX idx_users_nip (nip),
    INDEX idx_users_department (department_id),
    INDEX idx_users_role (role_id),
    INDEX idx_users_branch (branch_id),
    INDEX idx_users_active (is_active),
    INDEX idx_users_last_activity (last_activity_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### user_sessions
```sql
CREATE TABLE user_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    session_id VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    device_fingerprint VARCHAR(255) NULL,
    location JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_sessions_user (user_id),
    INDEX idx_sessions_session (session_id),
    INDEX idx_sessions_expires (expires_at),
    INDEX idx_sessions_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### roles
```sql
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    display_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    level INT DEFAULT 0, -- 0=Super Admin, 100=Basic User
    permissions JSON NOT NULL, -- Array of permissions
    is_system BOOLEAN DEFAULT FALSE, -- System roles cannot be deleted
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_roles_name (name),
    INDEX idx_roles_level (level),
    INDEX idx_roles_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. Organization Structure

#### departments
```sql
CREATE TABLE departments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    parent_id BIGINT UNSIGNED NULL,
    level INT DEFAULT 0,
    manager_id BIGINT UNSIGNED NULL,
    budget_limit DECIMAL(20,2) DEFAULT 0,
    cost_center VARCHAR(50) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (parent_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_departments_code (code),
    INDEX idx_departments_parent (parent_id),
    INDEX idx_departments_level (level),
    INDEX idx_departments_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### positions
```sql
CREATE TABLE positions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    department_id BIGINT UNSIGNED NULL,
    level INT DEFAULT 0,
    grade VARCHAR(20) NULL,
    base_salary DECIMAL(20,2) DEFAULT 0,
    max_overtime_hours INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    INDEX idx_positions_code (code),
    INDEX idx_positions_department (department_id),
    INDEX idx_positions_level (level),
    INDEX idx_positions_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### branches
```sql
CREATE TABLE branches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('headquarters', 'branch', 'warehouse', 'outlet') NOT NULL,
    address_id BIGINT UNSIGNED NOT NULL,
    phone_number VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    manager_id BIGINT UNSIGNED NULL,
    operation_hours JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (address_id) REFERENCES addresses(id),
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_branches_code (code),
    INDEX idx_branches_type (type),
    INDEX idx_branches_address (address_id),
    INDEX idx_branches_manager (manager_id),
    INDEX idx_branches_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3. Customer Management (SPPG + Regular)

#### customers
```sql
CREATE TABLE customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_number VARCHAR(50) UNIQUE NOT NULL,
    type ENUM('sppg', 'regular', 'government', 'institution') NOT NULL,
    category ENUM('a', 'b', 'c', 'd', 'e') NOT NULL, -- Customer categorization
    name VARCHAR(255) NOT NULL,
    legal_name VARCHAR(255) NULL,
    tax_id VARCHAR(50) NULL, -- NPWP
    business_license VARCHAR(100) NULL, -- SIUP/NIB
    registration_number VARCHAR(100) NULL, -- NIB for SPPG
    sppg_certificate VARCHAR(100) NULL, -- SPPG Certificate Number
    sppg_valid_from DATE NULL,
    sppg_valid_until DATE NULL,
    contact_person VARCHAR(255) NULL,
    contact_phone VARCHAR(20) NULL,
    contact_email VARCHAR(255) NULL,
    billing_address_id BIGINT UNSIGNED NULL,
    shipping_address_id BIGINT UNSIGNED NULL,
    credit_limit DECIMAL(20,2) DEFAULT 0,
    payment_terms INT DEFAULT 0, -- Days
    discount_rate DECIMAL(5,2) DEFAULT 0,
    tax_exempt BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    is_blacklisted BOOLEAN DEFAULT FALSE,
    blacklist_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (billing_address_id) REFERENCES addresses(id),
    FOREIGN KEY (shipping_address_id) REFERENCES addresses(id),
    INDEX idx_customers_number (customer_number),
    INDEX idx_customers_type (type),
    INDEX idx_customers_category (category),
    INDEX idx_customers_name (name),
    INDEX idx_customers_tax_id (tax_id),
    INDEX idx_customers_sppg_cert (sppg_certificate),
    INDEX idx_customers_active (is_active),
    INDEX idx_customers_blacklisted (is_blacklisted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### customer_contacts (Normalized with persons reference)
```sql
CREATE TABLE customer_contacts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED NOT NULL,
    person_id BIGINT UNSIGNED NULL, -- Reference to centralized person data
    position VARCHAR(255) NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE SET NULL,
    INDEX idx_contacts_customer (customer_id),
    INDEX idx_contacts_person (person_id),
    INDEX idx_contacts_primary (is_primary),
    INDEX idx_contacts_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4. Product & Inventory Management

#### product_categories
```sql
CREATE TABLE product_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    parent_id BIGINT UNSIGNED NULL,
    level INT DEFAULT 0,
    image_url VARCHAR(500) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (parent_id) REFERENCES product_categories(id) ON DELETE SET NULL,
    INDEX idx_categories_code (code),
    INDEX idx_categories_parent (parent_id),
    INDEX idx_categories_level (level),
    INDEX idx_categories_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### products
```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(100) UNIQUE NOT NULL,
    barcode VARCHAR(100) UNIQUE NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    category_id BIGINT UNSIGNED NULL,
    brand VARCHAR(100) NULL,
    unit_of_measure VARCHAR(20) NOT NULL DEFAULT 'PCS',
    weight DECIMAL(10,3) NULL, -- kg
    length DECIMAL(10,2) NULL, -- cm
    width DECIMAL(10,2) NULL, -- cm
    height DECIMAL(10,2) NULL, -- cm
    volume DECIMAL(10,3) NULL, -- cubic meters
    shelf_life INT NULL, -- days
    storage_temperature_min DECIMAL(5,2) NULL, -- celsius
    storage_temperature_max DECIMAL(5,2) NULL, -- celsius
    is_perishable BOOLEAN DEFAULT FALSE,
    is_hazardous BOOLEAN DEFAULT FALSE,
    requires_refrigeration BOOLEAN DEFAULT FALSE,
    min_stock_level DECIMAL(20,3) DEFAULT 0,
    max_stock_level DECIMAL(20,3) DEFAULT 0,
    reorder_point DECIMAL(20,3) DEFAULT 0,
    economic_order_quantity DECIMAL(20,3) DEFAULT 0,
    standard_cost DECIMAL(20,2) DEFAULT 0,
    average_cost DECIMAL(20,2) DEFAULT 0,
    last_cost DECIMAL(20,2) DEFAULT 0,
    selling_price DECIMAL(20,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL,
    INDEX idx_products_sku (sku),
    INDEX idx_products_barcode (barcode),
    INDEX idx_products_name (name),
    INDEX idx_products_category (category_id),
    INDEX idx_products_brand (brand),
    INDEX idx_products_active (is_active),
    FULLTEXT idx_products_search (name, description, brand)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### materials
```sql
CREATE TABLE materials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    material_type ENUM('raw', 'semi_processed', 'packaging') NOT NULL DEFAULT 'raw',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_materials_product (product_id),
    INDEX idx_materials_code (code),
    INDEX idx_materials_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### product_prices
```sql
CREATE TABLE product_prices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    customer_category ENUM('a', 'b', 'c', 'd', 'e', 'retail') NOT NULL,
    price_level ENUM('standard', 'special', 'promotional') NOT NULL,
    unit_price DECIMAL(20,2) NOT NULL,
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    valid_from DATE NOT NULL,
    valid_until DATE NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_price (product_id, customer_category, price_level, valid_from),
    INDEX idx_prices_product (product_id),
    INDEX idx_prices_category (customer_category),
    INDEX idx_prices_level (price_level),
    INDEX idx_prices_valid (valid_from, valid_until),
    INDEX idx_prices_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### warehouses
```sql
CREATE TABLE warehouses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('main', 'branch', 'transit', 'cold_storage') NOT NULL,
    address_id BIGINT UNSIGNED NOT NULL,
    manager_id BIGINT UNSIGNED NULL,
    capacity DECIMAL(20,3) DEFAULT 0, -- cubic meters
    temperature_range JSON NULL, -- {min: X, max: Y}
    humidity_range JSON NULL, -- {min: X, max: Y}
    operating_hours JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (address_id) REFERENCES addresses(id),
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_warehouses_code (code),
    INDEX idx_warehouses_type (type),
    INDEX idx_warehouses_address (address_id),
    INDEX idx_warehouses_manager (manager_id),
    INDEX idx_warehouses_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### inventory_transactions
```sql
CREATE TABLE inventory_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_number VARCHAR(50) UNIQUE NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    transaction_type ENUM('in', 'out', 'adjustment', 'transfer') NOT NULL,
    reference_type ENUM('purchase', 'sale', 'return', 'adjustment', 'transfer', 'production') NULL,
    reference_id BIGINT UNSIGNED NULL,
    quantity DECIMAL(20,3) NOT NULL, -- Positive for IN, negative for OUT
    unit_cost DECIMAL(20,2) NULL,
    batch_number VARCHAR(100) NULL,
    expiry_date DATE NULL,
    lot_number VARCHAR(100) NULL,
    serial_number VARCHAR(100) NULL,
    reason VARCHAR(255) NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_inventory_transaction_number (transaction_number),
    INDEX idx_inventory_product (product_id),
    INDEX idx_inventory_warehouse (warehouse_id),
    INDEX idx_inventory_type (transaction_type),
    INDEX idx_inventory_reference (reference_type, reference_id),
    INDEX idx_inventory_date (created_at),
    INDEX idx_inventory_batch (batch_number),
    INDEX idx_inventory_expiry (expiry_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### current_inventory
```sql
CREATE TABLE current_inventory (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    quantity_on_hand DECIMAL(20,3) NOT NULL DEFAULT 0,
    quantity_reserved DECIMAL(20,3) NOT NULL DEFAULT 0,
    quantity_available DECIMAL(20,3) GENERATED ALWAYS AS (quantity_on_hand - quantity_reserved) STORED,
    average_cost DECIMAL(20,2) NOT NULL DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    UNIQUE KEY unique_inventory (product_id, warehouse_id),
    INDEX idx_inventory_product (product_id),
    INDEX idx_inventory_warehouse (warehouse_id),
    INDEX idx_inventory_available (quantity_available),
    INDEX idx_inventory_updated (last_updated)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5. Sales Order Management

#### sales_orders
```sql
CREATE TABLE sales_orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    branch_id BIGINT UNSIGNED NOT NULL,
    order_type ENUM('regular', 'sppg', 'government', 'internal') NOT NULL,
    status ENUM('draft', 'confirmed', 'processing', 'shipped', 'delivered', 'invoiced', 'paid', 'cancelled') NOT NULL,
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    order_date DATE NOT NULL,
    delivery_date DATE NULL,
    delivery_address_id BIGINT UNSIGNED NULL,
    billing_address_id BIGINT UNSIGNED NULL,
    subtotal DECIMAL(20,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(20,2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(20,2) NOT NULL DEFAULT 0,
    shipping_cost DECIMAL(20,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(20,2) NOT NULL DEFAULT 0,
    paid_amount DECIMAL(20,2) NOT NULL DEFAULT 0,
    balance_amount DECIMAL(20,2) GENERATED ALWAYS AS (total_amount - paid_amount) STORED,
    payment_terms INT DEFAULT 0, -- Days
    due_date DATE NULL,
    notes TEXT NULL,
    internal_notes TEXT NULL,
    salesperson_id BIGINT UNSIGNED NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    shipped_by BIGINT UNSIGNED NULL,
    shipped_at TIMESTAMP NULL,
    delivered_by BIGINT UNSIGNED NULL,
    delivered_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (delivery_address_id) REFERENCES addresses(id),
    FOREIGN KEY (billing_address_id) REFERENCES addresses(id),
    FOREIGN KEY (salesperson_id) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (shipped_by) REFERENCES users(id),
    FOREIGN KEY (delivered_by) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_sales_orders_number (order_number),
    INDEX idx_sales_orders_customer (customer_id),
    INDEX idx_sales_orders_branch (branch_id),
    INDEX idx_sales_orders_type (order_type),
    INDEX idx_sales_orders_status (status),
    INDEX idx_sales_orders_priority (priority),
    INDEX idx_sales_orders_date (order_date),
    INDEX idx_sales_orders_delivery (delivery_date),
    INDEX idx_sales_orders_due (due_date),
    INDEX idx_sales_orders_salesperson (salesperson_id),
    INDEX idx_sales_orders_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### sales_order_items
```sql
CREATE TABLE sales_order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sales_order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    quantity DECIMAL(20,3) NOT NULL,
    unit_price DECIMAL(20,2) NOT NULL,
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    discount_amount DECIMAL(20,2) GENERATED ALWAYS AS (quantity * unit_price * discount_percentage / 100) STORED,
    subtotal DECIMAL(20,2) GENERATED ALWAYS AS (quantity * unit_price - discount_amount) STORED,
    tax_rate DECIMAL(5,2) DEFAULT 11, -- PPN 11%
    tax_amount DECIMAL(20,2) GENERATED ALWAYS AS (subtotal * tax_rate / 100) STORED,
    total_amount DECIMAL(20,2) GENERATED ALWAYS AS (subtotal + tax_amount) STORED,
    batch_number VARCHAR(100) NULL,
    expiry_date DATE NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sales_order_id) REFERENCES sales_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    INDEX idx_order_items_order (sales_order_id),
    INDEX idx_order_items_product (product_id),
    INDEX idx_order_items_warehouse (warehouse_id),
    INDEX idx_order_items_batch (batch_number),
    INDEX idx_order_items_expiry (expiry_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### v_sppg_orders
```sql
CREATE VIEW v_sppg_orders AS
SELECT
    so.id,
    so.order_number,
    so.customer_id,
    c.name AS customer_name,
    so.order_date,
    so.delivery_date,
    so.status,
    so.priority,
    so.total_amount,
    so.paid_amount,
    so.balance_amount
FROM sales_orders so
JOIN customers c ON c.id = so.customer_id
WHERE so.order_type = 'sppg';
```

#### sppg_order_batch_allocations
```sql
CREATE TABLE sppg_order_batch_allocations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sales_order_id BIGINT UNSIGNED NOT NULL,
    batch_id BIGINT UNSIGNED NOT NULL,
    allocated_quantity DECIMAL(20,3) NOT NULL,
    allocated_value DECIMAL(20,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sales_order_id) REFERENCES sales_orders(id),
    FOREIGN KEY (batch_id) REFERENCES distributor_sourcing_batches(id),
    INDEX idx_allocations_order (sales_order_id),
    INDEX idx_allocations_batch (batch_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

```sql
-- Contoh query laporan Profit per Order SPPG (ringkas)
SELECT
    o.id AS sales_order_id,
    o.order_number,
    o.customer_name,
    o.order_date,
    o.delivery_date,
    o.status,
    o.total_amount AS revenue,
    COALESCE(SUM(a.allocated_value), 0) AS total_cost_allocated,
    (o.total_amount - COALESCE(SUM(a.allocated_value), 0)) AS gross_profit,
    CASE 
        WHEN o.total_amount > 0 
        THEN (o.total_amount - COALESCE(SUM(a.allocated_value), 0)) / o.total_amount * 100
        ELSE 0
    END AS gross_margin_percentage
FROM v_sppg_orders o
LEFT JOIN sppg_order_batch_allocations a 
    ON a.sales_order_id = o.id
GROUP BY
    o.id,
    o.order_number,
    o.customer_name,
    o.order_date,
    o.delivery_date,
    o.status,
    o.total_amount;
```

```sql
SELECT
    o.customer_id,
    o.customer_name,
    SUM(o.total_amount) AS total_revenue,
    COALESCE(SUM(a.allocated_value), 0) AS total_cost_allocated,
    SUM(o.total_amount) - COALESCE(SUM(a.allocated_value), 0) AS gross_profit,
    CASE 
        WHEN SUM(o.total_amount) > 0 
        THEN (SUM(o.total_amount) - COALESCE(SUM(a.allocated_value), 0)) / SUM(o.total_amount) * 100
        ELSE 0
    END AS gross_margin_percentage
FROM v_sppg_orders o
LEFT JOIN sppg_order_batch_allocations a 
    ON a.sales_order_id = o.id
GROUP BY
    o.customer_id,
    o.customer_name;
```

```sql
-- Contoh query Profit per Customer SPPG untuk periode tertentu
SELECT
    o.customer_id,
    o.customer_name,
    SUM(o.total_amount) AS total_revenue,
    COALESCE(SUM(a.allocated_value), 0) AS total_cost_allocated,
    SUM(o.total_amount) - COALESCE(SUM(a.allocated_value), 0) AS gross_profit,
    CASE 
        WHEN SUM(o.total_amount) > 0 
        THEN (SUM(o.total_amount) - COALESCE(SUM(a.allocated_value), 0)) / SUM(o.total_amount) * 100
        ELSE 0
    END AS gross_margin_percentage
FROM v_sppg_orders o
LEFT JOIN sppg_order_batch_allocations a 
    ON a.sales_order_id = o.id
WHERE o.order_date BETWEEN :start_date AND :end_date
GROUP BY
    o.customer_id,
    o.customer_name;
```

```sql
-- Contoh query Profit per Customer SPPG per bulan (MySQL)
SELECT
    DATE_FORMAT(o.order_date, '%Y-%m') AS period_month,
    o.customer_id,
    o.customer_name,
    SUM(o.total_amount) AS total_revenue,
    COALESCE(SUM(a.allocated_value), 0) AS total_cost_allocated,
    SUM(o.total_amount) - COALESCE(SUM(a.allocated_value), 0) AS gross_profit,
    CASE 
        WHEN SUM(o.total_amount) > 0 
        THEN (SUM(o.total_amount) - COALESCE(SUM(a.allocated_value), 0)) / SUM(o.total_amount) * 100
        ELSE 0
    END AS gross_margin_percentage
FROM v_sppg_orders o
LEFT JOIN sppg_order_batch_allocations a 
    ON a.sales_order_id = o.id
WHERE o.order_date BETWEEN :start_date AND :end_date
GROUP BY
    DATE_FORMAT(o.order_date, '%Y-%m'),
    o.customer_id,
    o.customer_name;
```

```sql
WITH order_profit AS (
    SELECT
        o.id AS sales_order_id,
        o.total_amount AS revenue,
        COALESCE(SUM(a.allocated_value), 0) AS total_cost_allocated,
        o.total_amount - COALESCE(SUM(a.allocated_value), 0) AS gross_profit
    FROM v_sppg_orders o
    LEFT JOIN sppg_order_batch_allocations a
        ON a.sales_order_id = o.id
    GROUP BY
        o.id,
        o.total_amount
),
batch_farmer_cost AS (
    SELECT
        dbl.batch_id,
        dbl.farmer_id,
        SUM(dbl.line_cost + dbl.logistics_cost) AS farmer_batch_cost,
        SUM(SUM(dbl.line_cost + dbl.logistics_cost)) OVER (PARTITION BY dbl.batch_id) AS batch_total_cost
    FROM distributor_batch_lines dbl
    GROUP BY
        dbl.batch_id,
        dbl.farmer_id
),
order_farmer_cost AS (
    SELECT
        a.sales_order_id,
        bfc.farmer_id,
        a.allocated_value * bfc.farmer_batch_cost / NULLIF(bfc.batch_total_cost, 0) AS cost_from_farmer
    FROM sppg_order_batch_allocations a
    JOIN batch_farmer_cost bfc
        ON bfc.batch_id = a.batch_id
)
SELECT
    f.id AS farmer_id,
    f.name AS farmer_name,
    SUM(ofc.cost_from_farmer) AS total_cost_from_farmer,
    SUM(ofc.cost_from_farmer * op.gross_profit / NULLIF(op.total_cost_allocated, 0)) AS allocated_profit
FROM order_farmer_cost ofc
JOIN order_profit op
    ON op.sales_order_id = ofc.sales_order_id
JOIN farmers f
    ON f.id = ofc.farmer_id
GROUP BY
    f.id,
    f.name;
```

#### v_sppg_order_item_profit

```sql
CREATE VIEW v_sppg_order_item_profit AS
WITH order_cost AS (
    SELECT
        sales_order_id,
        SUM(allocated_value) AS total_cost_allocated
    FROM sppg_order_batch_allocations
    GROUP BY
        sales_order_id
)
SELECT
    so.id AS sales_order_id,
    so.order_number,
    so.customer_id,
    so.order_date,
    so.delivery_date,
    soi.id AS sales_order_item_id,
    soi.product_id,
    p.sku,
    p.name AS product_name,
    p.sppg_material_code AS sppg_material_code,
    sm.material_name AS sppg_material_name,
    mpm.plu_number,
    pc.product_name AS plu_product_name,
    soi.quantity,
    soi.total_amount AS line_revenue,
    oc.total_cost_allocated,
    CASE
        WHEN so.total_amount > 0 AND oc.total_cost_allocated IS NOT NULL
        THEN (soi.total_amount / so.total_amount) * oc.total_cost_allocated
        ELSE 0
    END AS line_cost_allocated,
    soi.total_amount - CASE
        WHEN so.total_amount > 0 AND oc.total_cost_allocated IS NOT NULL
        THEN (soi.total_amount / so.total_amount) * oc.total_cost_allocated
        ELSE 0
    END AS line_gross_profit,
    CASE
        WHEN soi.total_amount > 0
        THEN (
            soi.total_amount - CASE
                WHEN so.total_amount > 0 AND oc.total_cost_allocated IS NOT NULL
                THEN (soi.total_amount / so.total_amount) * oc.total_cost_allocated
                ELSE 0
            END
        ) / soi.total_amount * 100
        ELSE 0
    END AS line_margin_percentage,
    sm.protein_per_100g,
    sm.carb_per_100g,
    sm.fat_per_100g,
    sm.fiber_per_100g,
    sm.calories_per_100g
FROM sales_order_items soi
JOIN sales_orders so
    ON so.id = soi.sales_order_id
    AND so.order_type = 'sppg'
LEFT JOIN order_cost oc
    ON oc.sales_order_id = so.id
LEFT JOIN products p
    ON p.id = soi.product_id
LEFT JOIN sppg_materials sm
    ON sm.material_code = p.sppg_material_code
LEFT JOIN product_plu_mapping mpm
    ON mpm.product_id = p.id
    AND mpm.is_primary_plu = TRUE
LEFT JOIN plu_codes pc
    ON pc.id = mpm.plu_code_id;
```

```sql
SELECT
    v.sppg_material_code,
    v.sppg_material_name,
    v.plu_number,
    v.plu_product_name,
    SUM(v.line_revenue) AS total_revenue,
    SUM(v.line_cost_allocated) AS total_cost_allocated,
    SUM(v.line_gross_profit) AS gross_profit,
    CASE
        WHEN SUM(v.line_revenue) > 0
        THEN SUM(v.line_gross_profit) / SUM(v.line_revenue) * 100
        ELSE 0
    END AS gross_margin_percentage
FROM v_sppg_order_item_profit v
WHERE v.order_date BETWEEN :start_date AND :end_date
GROUP BY
    v.sppg_material_code,
    v.sppg_material_name,
    v.plu_number,
    v.plu_product_name;
```

#### sppg_materials (Master bahan baku SPPG)

```sql
CREATE TABLE sppg_materials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    material_code VARCHAR(50) UNIQUE NOT NULL,
    material_name VARCHAR(255) NOT NULL,
    category ENUM('B', 'K', 'M', 'O', 'D', 'T') NOT NULL,
    subcategory VARCHAR(50) NOT NULL,
    brand VARCHAR(100) NULL,
    unit VARCHAR(20) NOT NULL,
    package_size VARCHAR(50) NOT NULL,
    shelf_life_months INT NOT NULL,
    supplier_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    protein_per_100g DECIMAL(5,2) NULL,
    carb_per_100g DECIMAL(5,2) NULL,
    fat_per_100g DECIMAL(5,2) NULL,
    fiber_per_100g DECIMAL(5,2) NULL,
    calories_per_100g INT NULL,
    vitamin_a_mcg INT NULL,
    vitamin_c_mcg INT NULL,
    iron_mcg DECIMAL(5,2) NULL,
    calcium_mcg DECIMAL(5,2) NULL,
    INDEX idx_sppg_material_code (material_code),
    INDEX idx_sppg_material_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### sppg_material_plu_mappings (Relasi bahan SPPG ke PLU)

```sql
CREATE TABLE sppg_material_plu_mappings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    material_code VARCHAR(50) NOT NULL,
    plu_number VARCHAR(10) NOT NULL,
    mapping_type ENUM('direct', 'approximation') NOT NULL DEFAULT 'direct',
    conversion_factor DECIMAL(10,4) NOT NULL DEFAULT 1.0000,
    is_primary BOOLEAN NOT NULL DEFAULT TRUE,
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_sppg_mapping_material FOREIGN KEY (material_code) REFERENCES sppg_materials(material_code),
    CONSTRAINT fk_sppg_mapping_plu FOREIGN KEY (plu_number) REFERENCES plu_codes(plu_number),
    INDEX idx_sppg_mapping_material (material_code),
    INDEX idx_sppg_mapping_plu (plu_number),
    INDEX idx_sppg_mapping_primary (is_primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### v_sppg_material_plu_nutrition (View master SPPG ↔ PLU ↔ nutrisi)

```sql
CREATE VIEW v_sppg_material_plu_nutrition AS
SELECT
    sm.material_code,
    sm.material_name,
    sm.category,
    sm.subcategory,
    sm.unit,
    sm.package_size,
    mpm.plu_number,
    pc.product_name AS plu_product_name,
    pc.category AS plu_category,
    pc.subcategory AS plu_subcategory,
    sm.protein_per_100g,
    sm.carb_per_100g,
    sm.fat_per_100g,
    sm.fiber_per_100g,
    sm.calories_per_100g
FROM sppg_materials sm
LEFT JOIN sppg_material_plu_mappings mpm
    ON mpm.material_code = sm.material_code
    AND mpm.is_primary = TRUE
LEFT JOIN plu_codes pc
    ON pc.plu_number = mpm.plu_number
WHERE sm.is_active = TRUE;
```

#### sppg_daily_material_demand dan view kebutuhan bahan per periode

```sql
CREATE TABLE sppg_daily_material_demand (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sppg_id VARCHAR(50) NOT NULL,
    demand_date DATE NOT NULL,
    material_code VARCHAR(50) NOT NULL,
    target_group ENUM('anak', 'balita', 'remaja', 'dewasa', 'lansia') NOT NULL,
    beneficiaries_count INT NOT NULL,
    total_quantity_grams DECIMAL(14,3) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_demand_sppg_date (sppg_id, demand_date),
    INDEX idx_demand_material (material_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

```sql
CREATE VIEW v_sppg_material_demand_weekly AS
SELECT
    d.sppg_id,
    YEARWEEK(d.demand_date, 3) AS demand_week,
    MIN(d.demand_date) AS week_start_date,
    MAX(d.demand_date) AS week_end_date,
    d.material_code,
    sm.material_name,
    d.target_group,
    SUM(d.total_quantity_grams) / 1000 AS total_quantity_kg,
    SUM(d.beneficiaries_count) AS total_beneficiaries
FROM sppg_daily_material_demand d
LEFT JOIN sppg_materials sm
    ON sm.material_code = d.material_code
GROUP BY
    d.sppg_id,
    YEARWEEK(d.demand_date, 3),
    d.material_code,
    sm.material_name,
    d.target_group;
```

```sql
CREATE VIEW v_sppg_material_demand_monthly AS
SELECT
    d.sppg_id,
    DATE_FORMAT(d.demand_date, '%Y-%m') AS demand_month,
    MIN(d.demand_date) AS month_start_date,
    MAX(d.demand_date) AS month_end_date,
    d.material_code,
    sm.material_name,
    d.target_group,
    SUM(d.total_quantity_grams) / 1000 AS total_quantity_kg,
    SUM(d.beneficiaries_count) AS total_beneficiaries
FROM sppg_daily_material_demand d
LEFT JOIN sppg_materials sm
    ON sm.material_code = d.material_code
GROUP BY
    d.sppg_id,
    DATE_FORMAT(d.demand_date, '%Y-%m'),
    d.material_code,
    sm.material_name,
    d.target_group;
```

#### sppg_menus
```sql
CREATE TABLE sppg_menus (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    menu_code VARCHAR(50) UNIQUE NOT NULL,
    menu_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    target_group ENUM('anak', 'balita', 'remaja', 'dewasa', 'lansia') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_menus_code (menu_code),
    INDEX idx_menus_target_group (target_group),
    INDEX idx_menus_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### sppg_menu_items
```sql
CREATE TABLE sppg_menu_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    menu_id BIGINT UNSIGNED NOT NULL,
    material_code VARCHAR(50) NOT NULL,
    quantity_grams_per_portion DECIMAL(10,3) NOT NULL,
    is_optional BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_id) REFERENCES sppg_menus(id),
    FOREIGN KEY (material_code) REFERENCES sppg_materials(material_code),
    INDEX idx_menu_items_menu (menu_id),
    INDEX idx_menu_items_material (material_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### sppg_menu_logs
```sql
CREATE TABLE sppg_menu_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sppg_id VARCHAR(50) NOT NULL,
    menu_date DATE NOT NULL,
    meal_type ENUM('breakfast', 'lunch', 'dinner', 'snack') NOT NULL,
    menu_id BIGINT UNSIGNED NOT NULL,
    target_group ENUM('anak', 'balita', 'remaja', 'dewasa', 'lansia') NOT NULL,
    portions INT NOT NULL,
    beneficiaries_count INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_id) REFERENCES sppg_menus(id),
    INDEX idx_menu_logs_sppg_date (sppg_id, menu_date),
    INDEX idx_menu_logs_menu (menu_id),
    INDEX idx_menu_logs_target_group (target_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### sppg_material_ai_products (Mapping material SPPG ke produk AI)

```sql
CREATE TABLE sppg_material_ai_products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    material_code VARCHAR(50) NOT NULL,
    ai_product_id VARCHAR(50) NOT NULL,
    is_primary BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ai_map_material (material_code),
    INDEX idx_ai_map_product (ai_product_id),
    INDEX idx_ai_map_primary (is_primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Contoh query: jadwal menu 7 hari ke depan menjadi kebutuhan bahan mingguan per SPPG
```sql
SELECT
    l.sppg_id,
    MIN(l.menu_date) AS week_start_date,
    MAX(l.menu_date) AS week_end_date,
    mi.material_code,
    sm.material_name,
    SUM(mi.quantity_grams_per_portion * l.portions) / 1000 AS total_quantity_kg
FROM sppg_menu_logs l
JOIN sppg_menu_items mi
    ON mi.menu_id = l.menu_id
LEFT JOIN sppg_materials sm
    ON sm.material_code = mi.material_code
WHERE l.sppg_id = :sppg_id
  AND l.menu_date BETWEEN :start_date AND DATE_ADD(:start_date, INTERVAL 6 DAY)
GROUP BY
    l.sppg_id,
    mi.material_code,
    sm.material_name;
```

### 6. Procurement & Supplier Management

#### suppliers (Normalized with persons reference)
```sql
CREATE TABLE suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    person_id BIGINT UNSIGNED NULL, -- For individual suppliers
    supplier_code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL, -- Company/organization name for non-individual
    legal_name VARCHAR(255) NULL,
    tax_id VARCHAR(50) NULL, -- NPWP
    business_license VARCHAR(100) NULL, -- SIUP/NIB
    contact_person VARCHAR(255) NULL,
    contact_phone VARCHAR(20) NULL,
    contact_email VARCHAR(255) NULL,
    address_id BIGINT UNSIGNED NULL,
    payment_terms INT DEFAULT 0, -- Days
    credit_limit DECIMAL(20,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    is_blacklisted BOOLEAN DEFAULT FALSE,
    blacklist_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE SET NULL,
    FOREIGN KEY (address_id) REFERENCES addresses(id),
    INDEX idx_suppliers_person (person_id),
    INDEX idx_suppliers_code (supplier_code),
    INDEX idx_suppliers_name (name),
    INDEX idx_suppliers_tax_id (tax_id),
    INDEX idx_suppliers_active (is_active),
    INDEX idx_suppliers_blacklisted (is_blacklisted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### farmers
```sql
CREATE TABLE farmers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('petani', 'peternak', 'umkm') NOT NULL,
    region_code VARCHAR(20) NOT NULL,
    district VARCHAR(100),
    subdistrict VARCHAR(100),
    village VARCHAR(100),
    latitude DECIMAL(10,6),
    longitude DECIMAL(10,6),
    contact_person VARCHAR(255),
    phone VARCHAR(50),
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### farmer_products
```sql
CREATE TABLE farmer_products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    farmer_id BIGINT UNSIGNED NOT NULL,
    plu_code VARCHAR(50) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quality_grade ENUM('A', 'B', 'C') NOT NULL,
    unit VARCHAR(20) NOT NULL,
    min_order_quantity DECIMAL(12,2),
    max_capacity_per_day DECIMAL(12,2),
    base_price DECIMAL(14,2) NOT NULL,
    lead_time_days INT,
    is_seasonal BOOLEAN DEFAULT FALSE,
    season_start DATE,
    season_end DATE,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES farmers(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### sourcing_contracts
```sql
CREATE TABLE sourcing_contracts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    farmer_id BIGINT UNSIGNED NOT NULL,
    sppg_distributor_id VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    contract_price DECIMAL(14,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'IDR',
    payment_terms VARCHAR(100),
    max_volume_per_period DECIMAL(14,2),
    quality_min_score DECIMAL(5,2),
    status ENUM('draft', 'active', 'suspended', 'ended') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES farmers(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### distributor_sourcing_batches
```sql
CREATE TABLE distributor_sourcing_batches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_code VARCHAR(100) NOT NULL,
    sppg_id VARCHAR(50) NOT NULL,
    delivery_date DATE NOT NULL,
    commodity_group VARCHAR(100),
    total_quantity DECIMAL(14,2) NOT NULL,
    total_cost DECIMAL(16,2) NOT NULL,
    expected_revenue DECIMAL(16,2) NOT NULL,
    gross_margin DECIMAL(7,2) GENERATED ALWAYS AS
        ((expected_revenue - total_cost) / expected_revenue * 100) STORED,
    profit_per_unit DECIMAL(16,4) GENERATED ALWAYS AS
        ((expected_revenue - total_cost) / NULLIF(total_quantity, 0)) STORED,
    status ENUM('planned', 'ordered', 'delivered', 'settled') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### distributor_batch_lines
```sql
CREATE TABLE distributor_batch_lines (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_id BIGINT UNSIGNED NOT NULL,
    farmer_id BIGINT UNSIGNED NOT NULL,
    farmer_product_id BIGINT UNSIGNED NOT NULL,
    quantity DECIMAL(14,2) NOT NULL,
    unit_price DECIMAL(14,2) NOT NULL,
    line_cost DECIMAL(16,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    logistics_cost DECIMAL(16,2) DEFAULT 0,
    quality_score DECIMAL(5,2),
    delivery_distance_km DECIMAL(8,2),
    FOREIGN KEY (batch_id) REFERENCES distributor_sourcing_batches(id),
    FOREIGN KEY (farmer_id) REFERENCES farmers(id),
    FOREIGN KEY (farmer_product_id) REFERENCES farmer_products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Pemetaan API ke Tabel (Ringkasan)

- POST `/orders/sppg`
  - Menulis ke `sales_orders` (order_type = 'sppg') dan `sales_order_items` untuk detail produk dan kuantitas.
- GET `/orders/sppg/{id}`
  - Membaca dari `v_sppg_orders` (berbasis `sales_orders`) dan `sales_order_items` untuk line item.
- GET `/api/sppg/material-demand`
  - Membaca `v_sppg_material_demand_weekly` atau `v_sppg_material_demand_monthly` tergantung parameter periode, lalu mengembalikan kebutuhan bahan per `material_code`.
- GET `/api/sppg/purchase-recommendations`
  - Membaca `v_sppg_material_demand_weekly` atau `v_sppg_material_demand_monthly`, data stok dan inbound PO dari modul inventory, dan memanggil modul AI StockPricePredictor untuk memberikan rekomendasi pembelian per bahan.
- POST `/sourcing/plans/from-order`
  - Membaca `sales_orders`, `sales_order_items`, `products`, `current_inventory`, `distributor_sourcing_batches`, `distributor_batch_lines`, dan `sourcing_contracts` untuk menyusun rencana sourcing.
- POST `/sourcing/batches/from-plan`
  - Menulis ke `distributor_sourcing_batches` untuk header batch dan `distributor_batch_lines` untuk baris pemasok dan kuantitas.
- GET `/sourcing/recommendations`
  - Membaca `sales_orders`, `sales_order_items`, `products`, `product_plu_mapping`, `plu_codes`, `distributor_sourcing_batches`, `distributor_batch_lines`, dan `sourcing_contracts` untuk menghitung rekomendasi pemasok per material.
- GET `/purchase-orders/{id}`
  - Membaca dari `purchase_orders` (dan tabel detail terkait ketika ditambahkan) untuk header dan ringkasan PO.
- POST `/warehouse/receipts`
  - Menulis transaksi masuk ke `inventory_transactions`, memperbarui saldo di `current_inventory`, dan menambah atau memperbarui batch di `product_batch_info`.
- POST `/warehouse/qc`
  - Menulis hasil QC ke `raw_material_quality_checks` dan memperbarui `product_batch_info.quality_status`, serta dapat mengaitkan QC ke `delivery_tracking.quality_check_id`.
- POST `/orders/sppg/{id}/allocate-batches`
  - Menulis alokasi biaya dan kuantitas ke `sppg_order_batch_allocations` dengan menghubungkan `sales_orders` dan `distributor_sourcing_batches`.
- POST `/warehouse/picking-lists`
  - Secara konseptual menghasilkan rencana picking yang kemudian diterjemahkan menjadi transaksi keluar di `inventory_transactions` dan pengurangan `product_batch_info.current_quantity` ketika picking diselesaikan.
- PATCH `/warehouse/picking-lists/{id}/complete`
  - Mengonfirmasi picking dan menulis transaksi `inventory_transactions` bertipe `out` dengan reference_type = 'sale', lalu memperbarui `current_inventory` dan `product_batch_info` sesuai kuantitas yang dipakai.


#### purchase_orders
```sql
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    supplier_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    status ENUM('draft', 'sent', 'confirmed', 'partial_received', 'received', 'cancelled') NOT NULL,
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    order_date DATE NOT NULL,
    expected_delivery_date DATE NULL,
    delivery_address_id BIGINT UNSIGNED NULL,
    subtotal DECIMAL(20,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(20,2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(20,2) NOT NULL DEFAULT 0,
    shipping_cost DECIMAL(20,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(20,2) NOT NULL DEFAULT 0,
    paid_amount DECIMAL(20,2) NOT NULL DEFAULT 0,
    balance_amount DECIMAL(20,2) GENERATED ALWAYS AS (total_amount - paid_amount) STORED,
    payment_terms INT DEFAULT 0, -- Days
    due_date DATE NULL,
    notes TEXT NULL,
    internal_notes TEXT NULL,
    requested_by BIGINT UNSIGNED NOT NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    received_by BIGINT UNSIGNED NULL,
    received_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (delivery_address_id) REFERENCES addresses(id),
    FOREIGN KEY (requested_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (received_by) REFERENCES users(id),
    INDEX idx_purchase_orders_number (order_number),
    INDEX idx_purchase_orders_supplier (supplier_id),
    INDEX idx_purchase_orders_warehouse (warehouse_id),
    INDEX idx_purchase_orders_status (status),
    INDEX idx_purchase_orders_priority (priority),
    INDEX idx_purchase_orders_date (order_date),
    INDEX idx_purchase_orders_delivery (expected_delivery_date),
    INDEX idx_purchase_orders_due (due_date),
    INDEX idx_purchase_orders_requested (requested_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### supplier_documents
```sql
CREATE TABLE supplier_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_name VARCHAR(255) NOT NULL,
    document_type ENUM('BGN', 'QC') NOT NULL,
    supplier_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(512) NOT NULL,
    upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    notes TEXT NULL,
    expiration_date DATE NULL,
    verified_by BIGINT UNSIGNED NULL,
    verification_date DATETIME NULL,
    
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (verified_by) REFERENCES users(id),
    INDEX idx_supplier_documents_supplier (supplier_id),
    INDEX idx_supplier_documents_type (document_type),
    INDEX idx_supplier_documents_status (status),
    INDEX idx_supplier_documents_expiration (expiration_date),
    INDEX idx_supplier_documents_upload (upload_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### raw_material_quality_checks
```sql
CREATE TABLE raw_material_quality_checks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_number VARCHAR(50) NOT NULL,
    material_id BIGINT UNSIGNED NOT NULL,
    check_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    inspector_id BIGINT UNSIGNED NOT NULL,
    test_results JSON NOT NULL,
    status ENUM('passed', 'failed', 'pending') NOT NULL,
    document_id BIGINT UNSIGNED NULL,
    remarks TEXT NULL,
    
    FOREIGN KEY (material_id) REFERENCES materials(id),
    FOREIGN KEY (inspector_id) REFERENCES users(id),
    FOREIGN KEY (document_id) REFERENCES supplier_documents(id),
    INDEX idx_qc_batch_number (batch_number),
    INDEX idx_qc_material (material_id),
    INDEX idx_qc_status (status),
    INDEX idx_qc_inspector (inspector_id),
    INDEX idx_qc_check_date (check_date),
    INDEX idx_qc_document (document_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### delivery_tracking
```sql
CREATE TABLE delivery_tracking (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    delivery_number VARCHAR(50) NOT NULL,
    supplier_id BIGINT UNSIGNED NOT NULL,
    expected_arrival DATETIME NULL,
    actual_arrival DATETIME NULL,
    status ENUM('in_transit', 'delivered', 'delayed', 'cancelled') NOT NULL,
    quality_check_id BIGINT UNSIGNED NULL,
    documents JSON NULL,
    
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (quality_check_id) REFERENCES raw_material_quality_checks(id),
    INDEX idx_delivery_number (delivery_number),
    INDEX idx_delivery_supplier (supplier_id),
    INDEX idx_delivery_status (status),
    INDEX idx_delivery_expected (expected_arrival),
    INDEX idx_delivery_actual (actual_arrival),
    INDEX idx_delivery_quality_check (quality_check_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 7. Financial Management

#### chart_of_accounts
```sql
CREATE TABLE chart_of_accounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    account_number VARCHAR(20) UNIQUE NOT NULL,
    account_name VARCHAR(255) NOT NULL,
    account_type ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
    account_category VARCHAR(100) NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    level INT DEFAULT 0,
    normal_balance ENUM('debit', 'credit') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_contra BOOLEAN DEFAULT FALSE,
    tax_code VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES chart_of_accounts(id) ON DELETE SET NULL,
    INDEX idx_coa_number (account_number),
    INDEX idx_coa_name (account_name),
    INDEX idx_coa_type (account_type),
    INDEX idx_coa_category (account_category),
    INDEX idx_coa_parent (parent_id),
    INDEX idx_coa_level (level),
    INDEX idx_coa_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### journals
```sql
CREATE TABLE journals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    journal_number VARCHAR(50) UNIQUE NOT NULL,
    journal_date DATE NOT NULL,
    period_id BIGINT UNSIGNED NOT NULL,
    description TEXT NULL,
    reference_type VARCHAR(50) NULL,
    reference_id BIGINT UNSIGNED NULL,
    status ENUM('draft', 'posted', 'reversed') NOT NULL,
    total_debit DECIMAL(20,2) NOT NULL DEFAULT 0,
    total_credit DECIMAL(20,2) NOT NULL DEFAULT 0,
    created_by BIGINT UNSIGNED NOT NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    posted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (period_id) REFERENCES accounting_periods(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    INDEX idx_journals_number (journal_number),
    INDEX idx_journals_date (journal_date),
    INDEX idx_journals_period (period_id),
    INDEX idx_journals_status (status),
    INDEX idx_journals_reference (reference_type, reference_id),
    INDEX idx_journals_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 8. Integration dengan Database Alamat

#### Catatan Integrasi
Tabel-tabel berikut sudah tersedia di database alamat terpisah:
- `addresses` - Data alamat lengkap
- `countries` - Data negara
- `provinces` - Data provinsi  
- `regencies` - Data kabupaten/kota
- `districts` - Data kecamatan
- `villages` - Data kelurahan/desa

#### Cara Integrasi
```sql
-- Contoh penggunaan foreign key ke database alamat
-- Menggunakan cross-database reference atau view

CREATE VIEW v_customer_addresses AS
SELECT 
    c.id as customer_id,
    c.name as customer_name,
    a.address_line1,
    a.address_line2,
    a.postal_code,
    v.name as village,
    d.name as district,
    r.name as regency,
    p.name as province,
    co.name as country
FROM sppg_db.customers c
LEFT JOIN address_db.addresses a ON c.billing_address_id = a.id
LEFT JOIN address_db.villages v ON a.village_id = v.id
LEFT JOIN address_db.districts d ON a.district_id = d.id
LEFT JOIN address_db.regencies r ON a.regency_id = r.id
LEFT JOIN address_db.provinces p ON a.province_id = p.id
LEFT JOIN address_db.countries co ON a.country_id = co.id;

-- View untuk menggabungkan user dengan person data
CREATE VIEW v_users_complete AS
SELECT 
    u.id,
    u.username,
    u.email_verified_at,
    u.password_hash,
    u.department_id,
    u.position_id,
    u.role_id,
    u.branch_id,
    u.is_active,
    u.is_online,
    u.last_login_at,
    u.last_activity_at,
    u.two_factor_enabled,
    u.failed_login_attempts,
    u.locked_until,
    u.password_changed_at,
    u.created_at,
    u.updated_at,
    p.nik,
    p.nip,
    p.full_name,
    p.first_name,
    p.last_name,
    p.title,
    p.gender,
    p.birth_date,
    p.birth_place,
    p.phone_number,
    p.mobile_number,
    p.email,
    p.avatar_url
FROM users u
LEFT JOIN persons p ON u.person_id = p.id;

-- View untuk customer dengan person data
CREATE VIEW v_customers_complete AS
SELECT 
    c.id,
    c.customer_number,
    c.type,
    c.customer_category_id,
    c.name as company_name,
    c.legal_name,
    c.tax_id,
    c.business_license,
    c.registration_number,
    c.sppg_certificate,
    c.sppg_valid_from,
    c.sppg_valid_until,
    c.contact_person,
    c.contact_phone,
    c.contact_email,
    c.billing_address_id,
    c.shipping_address_id,
    c.credit_limit,
    c.payment_terms,
    c.tax_exempt,
    c.is_active,
    c.is_blacklisted,
    c.blacklist_reason,
    c.created_at,
    c.updated_at,
    p.full_name as person_name,
    p.phone_number as person_phone,
    p.email as person_email,
    cc.name as category_name,
    cc.default_discount_rate
FROM customers c
LEFT JOIN persons p ON c.person_id = p.id
LEFT JOIN customer_categories cc ON c.customer_category_id = cc.id;

-- View untuk produk dengan SKU dan barcode lengkap
CREATE VIEW v_products_complete AS
SELECT 
    p.id,
    p.sku,
    p.name,
    p.description,
    p.category_id,
    pc.name as category_name,
    p.brand,
    p.unit_id,
    pu.name as unit_name,
    p.weight,
    p.length,
    p.width,
    p.height,
    p.volume,
    p.shelf_life,
    p.storage_temperature_min,
    p.storage_temperature_max,
    p.is_perishable,
    p.is_hazardous,
    p.requires_refrigeration,
    p.min_stock_level,
    p.max_stock_level,
    p.reorder_point,
    p.economic_order_quantity,
    p.standard_cost,
    p.average_cost,
    p.last_cost,
    p.selling_price,
    p.tax_code_id,
    tc.name as tax_code_name,
    tc.rate as tax_rate,
    p.is_active,
    p.created_at,
    p.updated_at,
    -- Get primary barcode
    (SELECT barcode_value FROM product_barcodes pb 
     WHERE pb.product_id = p.id AND pb.is_primary = TRUE AND pb.is_active = TRUE 
     LIMIT 1) as primary_barcode,
    -- Get all barcodes as JSON array
    (SELECT JSON_ARRAYAGG(barcode_value) FROM product_barcodes pb 
     WHERE pb.product_id = p.id AND pb.is_active = TRUE) as all_barcodes
FROM products p
LEFT JOIN product_categories pc ON p.category_id = pc.id
LEFT JOIN product_units pu ON p.unit_id = pu.id
LEFT JOIN tax_codes tc ON p.tax_code_id = tc.id;

-- View untuk product variants dengan barcode
CREATE VIEW v_product_variants_complete AS
SELECT 
    pv.id,
    pv.product_id,
    p.name as product_name,
    pv.sku,
    pv.variant_name,
    pv.variant_attributes,
    pv.weight,
    pv.length,
    pv.width,
    pv.height,
    pv.volume,
    pv.standard_cost,
    pv.selling_price,
    pv.is_active,
    pv.created_at,
    pv.updated_at,
    -- Get primary barcode for variant
    (SELECT barcode_value FROM product_barcodes pb 
     WHERE pb.product_variant_id = pv.id AND pb.is_primary = TRUE AND pb.is_active = TRUE 
     LIMIT 1) as primary_barcode,
    -- Get all barcodes for variant as JSON array
    (SELECT JSON_ARRAYAGG(barcode_value) FROM product_barcodes pb 
     WHERE pb.product_variant_id = pv.id AND pb.is_active = TRUE) as all_barcodes,
    -- Get PLU information
    (SELECT JSON_OBJECT(
        'plu_number', pc.plu_number,
        'product_name', pc.product_name,
        'category', pc.category
    ) FROM product_plu_mapping ppm 
    JOIN plu_codes pc ON ppm.plu_code_id = pc.id 
    WHERE ppm.product_variant_id = pv.id AND ppm.is_primary_plu = TRUE 
    LIMIT 1) as primary_plu
FROM product_variants pv
LEFT JOIN products p ON pv.product_id = p.id;

-- View untuk PLU lookup by province
CREATE VIEW v_plu_by_province AS
SELECT 
    pc.id as plu_code_id,
    pc.plu_number,
    pc.product_name as standard_name,
    pc.category,
    pc.subcategory,
    pc.variety,
    pc.size_grade,
    pc.standard_unit,
    pc.is_organic,
    pc.is_conventional,
    pc.is_gmo,
    p.name as province_name,
    ppm.local_name,
    ppm.is_primary_plu,
    ppm.effective_date,
    ppm.expiry_date
FROM plu_codes pc
LEFT JOIN product_plu_mapping ppm ON pc.id = ppm.plu_code_id
LEFT JOIN provinces p ON ppm.province_id = p.id
WHERE pc.is_active = TRUE
AND (ppm.expiry_date IS NULL OR ppm.expiry_date >= CURDATE());
```

---

## 🔧 DATABASE NORMALIZATION

### Normalization Issues Identified & Fixed

#### 1NF Violations Fixed:
- JSON fields split into separate tables for atomic values
- Repeating groups eliminated
- All attributes made atomic

#### 2NF Violations Fixed:
- Partial dependencies removed
- Composite keys properly handled
- All non-key attributes fully dependent on primary key

#### 3NF Violations Fixed:
- Transitive dependencies eliminated
- Separate tables created for lookup data
- Proper relationships established

---

## 📊 NORMALIZED TABLE STRUCTURES

### 1. Reference Tables (Master Data)

#### Note: Address Tables Integration
Tabel alamat (`addresses`, `countries`, `provinces`, `regencies`, `districts`, `villages`) 
sudah tersedia di database alamat terpisah. Hanya foreign key yang diperlukan untuk integrasi.

#### persons (Centralized Person Data)
```sql
CREATE TABLE persons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(16) UNIQUE NULL, -- Nomor Induk Kependudukan
    nip VARCHAR(20) UNIQUE NULL, -- Nomor Induk Pegawai
    full_name VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NULL,
    title VARCHAR(50) NULL, -- Bapak/Ibu/Sdr/etc
    gender ENUM('male', 'female', 'other') NULL,
    birth_date DATE NULL,
    birth_place VARCHAR(100) NULL,
    phone_number VARCHAR(20) NULL,
    mobile_number VARCHAR(20) NULL,
    email VARCHAR(255) UNIQUE NULL,
    avatar_url VARCHAR(500) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_persons_nik (nik),
    INDEX idx_persons_nip (nip),
    INDEX idx_persons_name (full_name),
    INDEX idx_persons_email (email),
    INDEX idx_persons_phone (phone_number),
    INDEX idx_persons_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### customer_categories
```sql
CREATE TABLE customer_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    min_purchase_amount DECIMAL(20,2) DEFAULT 0,
    max_credit_limit DECIMAL(20,2) DEFAULT 0,
    default_payment_terms INT DEFAULT 0,
    default_discount_rate DECIMAL(5,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_customer_categories_code (code),
    INDEX idx_customer_categories_name (name),
    INDEX idx_customer_categories_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### product_variants (For multiple SKUs per product)
```sql
CREATE TABLE product_variants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    sku VARCHAR(100) UNIQUE NOT NULL,
    variant_name VARCHAR(255) NOT NULL,
    variant_attributes JSON NULL, -- {"size": "XL", "color": "Red", "material": "Cotton"}
    weight DECIMAL(10,3) NULL,
    length DECIMAL(10,2) NULL,
    width DECIMAL(10,2) NULL,
    height DECIMAL(10,2) NULL,
    volume DECIMAL(10,3) NULL,
    standard_cost DECIMAL(20,2) DEFAULT 0,
    selling_price DECIMAL(20,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_variants_product (product_id),
    INDEX idx_variants_sku (sku),
    INDEX idx_variants_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### product_barcodes (Updated with PLU support)
```sql
CREATE TABLE product_barcodes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    product_variant_id BIGINT UNSIGNED NULL, -- Can be linked to specific variant
    plu_code_id BIGINT UNSIGNED NULL, -- Link to PLU code for standardization
    barcode_type ENUM('EAN13', 'EAN8', 'UPC', 'CODE128', 'CODE39', 'QR', 'INTERNAL', 'PLU') NOT NULL,
    barcode_value VARCHAR(100) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    description VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON DELETE SET NULL,
    FOREIGN KEY (plu_code_id) REFERENCES plu_codes(id) ON DELETE SET NULL,
    UNIQUE KEY unique_product_barcode (product_id, product_variant_id, barcode_value),
    INDEX idx_barcodes_product (product_id),
    INDEX idx_barcodes_variant (product_variant_id),
    INDEX idx_barcodes_plu (plu_code_id),
    INDEX idx_barcodes_type (barcode_type),
    INDEX idx_barcodes_value (barcode_value),
    INDEX idx_barcodes_primary (is_primary),
    INDEX idx_barcodes_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### product_units
```sql
CREATE TABLE product_units (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) UNIQUE NOT NULL,
    name VARCHAR(50) NOT NULL,
    description TEXT NULL,
    base_unit_id BIGINT UNSIGNED NULL, -- For unit conversion
    conversion_factor DECIMAL(10,6) DEFAULT 1,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (base_unit_id) REFERENCES product_units(id),
    INDEX idx_product_units_code (code),
    INDEX idx_product_units_name (name),
    INDEX idx_product_units_base (base_unit_id),
    INDEX idx_product_units_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### tax_codes
```sql
CREATE TABLE tax_codes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    rate DECIMAL(5,2) NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_tax_codes_code (code),
    INDEX idx_tax_codes_name (name),
    INDEX idx_tax_codes_rate (rate),
    INDEX idx_tax_codes_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### plu_codes (Standard PLU for cross-province standardization)
```sql
CREATE TABLE plu_codes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    plu_number VARCHAR(10) UNIQUE NOT NULL, -- 4-5 digit PLU code
    product_name VARCHAR(255) NOT NULL, -- Standardized product name
    scientific_name VARCHAR(255) NULL, -- Latin/scientific name
    category VARCHAR(100) NOT NULL, -- FRUITS, VEGETABLES, etc
    subcategory VARCHAR(100) NULL, -- APPLES, BANANAS, etc
    variety VARCHAR(100) NULL, -- RED DELICIOUS, CAVENDISH, etc
    size_grade VARCHAR(50) NULL, -- SMALL, MEDIUM, LARGE, EXTRA LARGE
    description TEXT NULL,
    commodity_code VARCHAR(50) NULL, -- HS code for customs
    is_organic BOOLEAN DEFAULT FALSE,
    is_conventional BOOLEAN DEFAULT TRUE,
    is_gmo BOOLEAN DEFAULT FALSE,
    standard_unit VARCHAR(20) DEFAULT 'KG', -- KG, PCS, BUNCH, etc
    country_origin VARCHAR(3) NULL, -- ISO country code
    seasonality JSON NULL, -- {"peak": ["Jun", "Jul"], "available": ["May", "Jun", "Jul", "Aug"]}
    storage_requirements JSON NULL, -- {"temp_min": 2, "temp_max": 8, "humidity": "85-95%"}
    shelf_life_days INT NULL,
    nutrition_info JSON NULL, -- {"calories": 52, "protein": 0.3, "carbs": 14}
    allergen_info JSON NULL, -- ["gluten", "nuts", etc]
    certification JSON NULL, -- ["halal", "organic", etc]
    is_active BOOLEAN DEFAULT TRUE,
    effective_date DATE NULL,
    expiry_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_plu_number (plu_number),
    INDEX idx_plu_name (product_name),
    INDEX idx_plu_category (category),
    INDEX idx_plu_subcategory (subcategory),
    INDEX idx_plu_variety (variety),
    INDEX idx_plu_active (is_active),
    INDEX idx_plu_origin (country_origin),
    FULLTEXT idx_plu_search (product_name, description, variety)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### product_plu_mapping (Link products to PLU codes)
```sql
CREATE TABLE product_plu_mapping (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    product_variant_id BIGINT UNSIGNED NULL,
    plu_code_id BIGINT UNSIGNED NOT NULL,
    province_id BIGINT UNSIGNED NULL, -- NULL if national standard
    region_code VARCHAR(10) NULL, -- Regional variation
    local_name VARCHAR(255) NULL, -- Local product name
    is_primary_plu BOOLEAN DEFAULT FALSE,
    effective_date DATE NOT NULL,
    expiry_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON DELETE SET NULL,
    FOREIGN KEY (plu_code_id) REFERENCES plu_codes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_plu (product_id, product_variant_id, province_id, effective_date),
    INDEX idx_mapping_product (product_id),
    INDEX idx_mapping_variant (product_variant_id),
    INDEX idx_mapping_plu (plu_code_id),
    INDEX idx_mapping_province (province_id),
    INDEX idx_mapping_primary (is_primary_plu),
    INDEX idx_mapping_effective (effective_date, expiry_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. Enhanced Core Tables (Normalized)

#### users (Normalized with persons reference)
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    person_id BIGINT UNSIGNED NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password_hash VARCHAR(255) NOT NULL,
    department_id BIGINT UNSIGNED NULL,
    position_id BIGINT UNSIGNED NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    branch_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_online BOOLEAN DEFAULT FALSE,
    last_login_at TIMESTAMP NULL,
    last_activity_at TIMESTAMP NULL,
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    two_factor_secret VARCHAR(255) NULL,
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    password_changed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (person_id) REFERENCES persons(id),
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE SET NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL,
    INDEX idx_users_person (person_id),
    INDEX idx_users_username (username),
    INDEX idx_users_department (department_id),
    INDEX idx_users_role (role_id),
    INDEX idx_users_branch (branch_id),
    INDEX idx_users_active (is_active),
    INDEX idx_users_last_activity (last_activity_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### customers (Normalized with persons reference)
```sql
CREATE TABLE customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    person_id BIGINT UNSIGNED NULL, -- For individual customers
    customer_number VARCHAR(50) UNIQUE NOT NULL,
    type ENUM('sppg', 'regular', 'government', 'institution') NOT NULL,
    customer_category_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL, -- Company/organization name for non-individual
    legal_name VARCHAR(255) NULL,
    tax_id VARCHAR(50) NULL,
    business_license VARCHAR(100) NULL,
    registration_number VARCHAR(100) NULL,
    sppg_certificate VARCHAR(100) NULL,
    sppg_valid_from DATE NULL,
    sppg_valid_until DATE NULL,
    contact_person VARCHAR(255) NULL,
    contact_phone VARCHAR(20) NULL,
    contact_email VARCHAR(255) NULL,
    billing_address_id BIGINT UNSIGNED NULL,
    shipping_address_id BIGINT UNSIGNED NULL,
    credit_limit DECIMAL(20,2) DEFAULT 0,
    payment_terms INT DEFAULT 0,
    tax_exempt BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    is_blacklisted BOOLEAN DEFAULT FALSE,
    blacklist_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE SET NULL,
    FOREIGN KEY (customer_category_id) REFERENCES customer_categories(id),
    FOREIGN KEY (billing_address_id) REFERENCES addresses(id),
    FOREIGN KEY (shipping_address_id) REFERENCES addresses(id),
    INDEX idx_customers_person (person_id),
    INDEX idx_customers_number (customer_number),
    INDEX idx_customers_type (type),
    INDEX idx_customers_category (customer_category_id),
    INDEX idx_customers_name (name),
    INDEX idx_customers_tax_id (tax_id),
    INDEX idx_customers_sppg_cert (sppg_certificate),
    INDEX idx_customers_active (is_active),
    INDEX idx_customers_blacklisted (is_blacklisted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### products (Normalized with improved SKU handling)
```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    category_id BIGINT UNSIGNED NULL,
    brand VARCHAR(100) NULL,
    unit_id BIGINT UNSIGNED NOT NULL,
    weight DECIMAL(10,3) NULL,
    length DECIMAL(10,2) NULL,
    width DECIMAL(10,2) NULL,
    height DECIMAL(10,2) NULL,
    volume DECIMAL(10,3) NULL,
    shelf_life INT NULL,
    storage_temperature_min DECIMAL(5,2) NULL,
    storage_temperature_max DECIMAL(5,2) NULL,
    is_perishable BOOLEAN DEFAULT FALSE,
    is_hazardous BOOLEAN DEFAULT FALSE,
    requires_refrigeration BOOLEAN DEFAULT FALSE,
    min_stock_level DECIMAL(20,3) DEFAULT 0,
    max_stock_level DECIMAL(20,3) DEFAULT 0,
    reorder_point DECIMAL(20,3) DEFAULT 0,
    economic_order_quantity DECIMAL(20,3) DEFAULT 0,
    standard_cost DECIMAL(20,2) DEFAULT 0,
    average_cost DECIMAL(20,2) DEFAULT 0,
    last_cost DECIMAL(20,2) DEFAULT 0,
    selling_price DECIMAL(20,2) DEFAULT 0,
    tax_code_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (unit_id) REFERENCES product_units(id),
    FOREIGN KEY (tax_code_id) REFERENCES tax_codes(id),
    INDEX idx_products_sku (sku),
    INDEX idx_products_name (name),
    INDEX idx_products_category (category_id),
    INDEX idx_products_brand (brand),
    INDEX idx_products_unit (unit_id),
    INDEX idx_products_tax_code (tax_code_id),
    INDEX idx_products_active (is_active),
    FULLTEXT idx_products_search (name, description, brand)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### product_prices (Normalized)
```sql
CREATE TABLE product_prices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    customer_category_id BIGINT UNSIGNED NOT NULL,
    price_level ENUM('standard', 'special', 'promotional') NOT NULL,
    unit_price DECIMAL(20,2) NOT NULL,
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    valid_from DATE NOT NULL,
    valid_until DATE NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_category_id) REFERENCES customer_categories(id),
    UNIQUE KEY unique_product_price (product_id, customer_category_id, price_level, valid_from),
    INDEX idx_prices_product (product_id),
    INDEX idx_prices_customer_category (customer_category_id),
    INDEX idx_prices_level (price_level),
    INDEX idx_prices_valid (valid_from, valid_until),
    INDEX idx_prices_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### sales_orders (Normalized)
```sql
CREATE TABLE sales_orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    branch_id BIGINT UNSIGNED NOT NULL,
    order_type ENUM('regular', 'sppg', 'government', 'internal') NOT NULL,
    status ENUM('draft', 'confirmed', 'processing', 'shipped', 'delivered', 'invoiced', 'paid', 'cancelled') NOT NULL,
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    order_date DATE NOT NULL,
    delivery_date DATE NULL,
    delivery_address_id BIGINT UNSIGNED NULL,
    billing_address_id BIGINT UNSIGNED NULL,
    subtotal DECIMAL(20,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(20,2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(20,2) NOT NULL DEFAULT 0,
    shipping_cost DECIMAL(20,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(20,2) NOT NULL DEFAULT 0,
    paid_amount DECIMAL(20,2) NOT NULL DEFAULT 0,
    balance_amount DECIMAL(20,2) GENERATED ALWAYS AS (total_amount - paid_amount) STORED,
    payment_terms INT DEFAULT 0,
    due_date DATE NULL,
    notes TEXT NULL,
    internal_notes TEXT NULL,
    salesperson_id BIGINT UNSIGNED NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    shipped_by BIGINT UNSIGNED NULL,
    shipped_at TIMESTAMP NULL,
    delivered_by BIGINT UNSIGNED NULL,
    delivered_at TIMESTAMP NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (delivery_address_id) REFERENCES addresses(id),
    FOREIGN KEY (billing_address_id) REFERENCES addresses(id),
    FOREIGN KEY (salesperson_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (shipped_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (delivered_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_sales_orders_number (order_number),
    INDEX idx_sales_orders_customer (customer_id),
    INDEX idx_sales_orders_branch (branch_id),
    INDEX idx_sales_orders_type (order_type),
    INDEX idx_sales_orders_status (status),
    INDEX idx_sales_orders_priority (priority),
    INDEX idx_sales_orders_date (order_date),
    INDEX idx_sales_orders_delivery (delivery_date),
    INDEX idx_sales_orders_due (due_date),
    INDEX idx_sales_orders_salesperson (salesperson_id),
    INDEX idx_sales_orders_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### sales_order_items (Normalized)
```sql
CREATE TABLE sales_order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sales_order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    quantity DECIMAL(20,3) NOT NULL,
    unit_price DECIMAL(20,2) NOT NULL,
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    discount_amount DECIMAL(20,2) GENERATED ALWAYS AS (quantity * unit_price * discount_percentage / 100) STORED,
    subtotal DECIMAL(20,2) GENERATED ALWAYS AS (quantity * unit_price - discount_amount) STORED,
    tax_rate DECIMAL(5,2) DEFAULT 11,
    tax_amount DECIMAL(20,2) GENERATED ALWAYS AS (subtotal * tax_rate / 100) STORED,
    total_amount DECIMAL(20,2) GENERATED ALWAYS AS (subtotal + tax_amount) STORED,
    batch_number VARCHAR(100) NULL,
    expiry_date DATE NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sales_order_id) REFERENCES sales_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    INDEX idx_order_items_order (sales_order_id),
    INDEX idx_order_items_product (product_id),
    INDEX idx_order_items_warehouse (warehouse_id),
    INDEX idx_order_items_batch (batch_number),
    INDEX idx_order_items_expiry (expiry_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3. Additional Normalized Tables

#### user_permissions (Normalized from roles.permissions JSON)
```sql
CREATE TABLE user_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id BIGINT UNSIGNED NOT NULL,
    permission_name VARCHAR(100) NOT NULL,
    permission_group VARCHAR(50) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_role_permission (role_id, permission_name),
    INDEX idx_permissions_role (role_id),
    INDEX idx_permissions_name (permission_name),
    INDEX idx_permissions_group (permission_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### warehouse_locations (Normalized from JSON fields)
```sql
CREATE TABLE warehouse_locations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    location_code VARCHAR(50) NOT NULL,
    location_name VARCHAR(255) NOT NULL,
    location_type ENUM('shelf', 'rack', 'bin', 'area', 'zone') NOT NULL,
    capacity DECIMAL(20,3) DEFAULT 0,
    current_usage DECIMAL(20,3) DEFAULT 0,
    temperature_min DECIMAL(5,2) NULL,
    temperature_max DECIMAL(5,2) NULL,
    humidity_min DECIMAL(5,2) NULL,
    humidity_max DECIMAL(5,2) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_warehouse_location (warehouse_id, location_code),
    INDEX idx_locations_warehouse (warehouse_id),
    INDEX idx_locations_type (location_type),
    INDEX idx_locations_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### branch_operating_hours (Normalized from JSON)
```sql
CREATE TABLE branch_operating_hours (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id BIGINT UNSIGNED NOT NULL,
    day_of_week ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
    open_time TIME NOT NULL,
    close_time TIME NOT NULL,
    is_closed BOOLEAN DEFAULT FALSE,
    break_start_time TIME NULL,
    break_end_time TIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    UNIQUE KEY unique_branch_day (branch_id, day_of_week),
    INDEX idx_operating_hours_branch (branch_id),
    INDEX idx_operating_hours_day (day_of_week)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### product_batch_info (Normalized from inventory_transactions)
```sql
CREATE TABLE product_batch_info (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    batch_number VARCHAR(100) NOT NULL,
    lot_number VARCHAR(100) NULL,
    serial_number VARCHAR(100) NULL,
    manufacture_date DATE NULL,
    expiry_date DATE NULL,
    supplier_id BIGINT UNSIGNED NULL,
    warehouse_id BIGINT UNSIGNED NOT NULL,
    location_id BIGINT UNSIGNED NULL,
    initial_quantity DECIMAL(20,3) NOT NULL,
    current_quantity DECIMAL(20,3) NOT NULL,
    unit_cost DECIMAL(20,2) NULL,
    quality_status ENUM('pending', 'approved', 'rejected', 'quarantine') DEFAULT 'pending',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (location_id) REFERENCES warehouse_locations(id) ON DELETE SET NULL,
    UNIQUE KEY unique_product_batch (product_id, batch_number, warehouse_id),
    INDEX idx_batch_product (product_id),
    INDEX idx_batch_number (batch_number),
    INDEX idx_batch_warehouse (warehouse_id),
    INDEX idx_batch_expiry (expiry_date),
    INDEX idx_batch_status (quality_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### customer_discount_rules (Normalized from customer data)
```sql
CREATE TABLE customer_discount_rules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED NULL,
    customer_category_id BIGINT UNSIGNED NULL,
    product_id BIGINT UNSIGNED NULL,
    product_category_id BIGINT UNSIGNED NULL,
    discount_type ENUM('percentage', 'fixed_amount') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    min_quantity DECIMAL(20,3) DEFAULT 0,
    min_purchase_amount DECIMAL(20,2) DEFAULT 0,
    valid_from DATE NOT NULL,
    valid_until DATE NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_category_id) REFERENCES customer_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (product_category_id) REFERENCES product_categories(id) ON DELETE CASCADE,
    INDEX idx_discount_customer (customer_id),
    INDEX idx_discount_customer_category (customer_category_id),
    INDEX idx_discount_product (product_id),
    INDEX idx_discount_product_category (product_category_id),
    INDEX idx_discount_valid (valid_from, valid_until),
    INDEX idx_discount_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🔧 DATABASE OPTIMIZATION

### Indexing Strategy
```sql
-- Composite Indexes for Common Queries
CREATE INDEX idx_sales_orders_customer_status ON sales_orders(customer_id, status);
CREATE INDEX idx_sales_orders_date_status ON sales_orders(order_date, status);
CREATE INDEX idx_inventory_product_warehouse ON current_inventory(product_id, warehouse_id);
CREATE INDEX idx_transactions_product_date ON inventory_transactions(product_id, created_at);
CREATE INDEX idx_orders_items_product_order ON sales_order_items(product_id, sales_order_id);

-- Additional Indexes for Normalized Tables
CREATE INDEX idx_customers_category_type ON customers(customer_category_id, type);
CREATE INDEX idx_products_category_unit ON products(category_id, unit_id);
CREATE INDEX idx_product_prices_category_date ON product_prices(customer_category_id, valid_from);
CREATE INDEX idx_batch_info_product_warehouse ON product_batch_info(product_id, warehouse_id);
CREATE INDEX idx_discount_rules_customer_product ON customer_discount_rules(customer_id, product_id);
CREATE INDEX idx_locations_warehouse_type ON warehouse_locations(warehouse_id, location_type);
CREATE INDEX idx_operating_hours_branch_time ON branch_operating_hours(branch_id, open_time);

-- Full-text Search Indexes
CREATE FULLTEXT idx_products_search ON products(name, description, brand);
CREATE FULLTEXT idx_customers_search ON customers(name, legal_name, contact_person);

-- Partitioning for Large Tables
ALTER TABLE inventory_transactions PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2023 VALUES LESS THAN (2024),
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);

ALTER TABLE audit_trail PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2023 VALUES LESS THAN (2024),
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

### Performance Monitoring
```sql
-- Query Performance Monitoring
CREATE TABLE query_performance_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    query_hash VARCHAR(64) NOT NULL,
    query_text TEXT NOT NULL,
    execution_time DECIMAL(10,3) NOT NULL,
    rows_examined INT NOT NULL,
    rows_returned INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_query_performance_hash (query_hash),
    INDEX idx_query_performance_time (execution_time),
    INDEX idx_query_performance_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 📊 MIGRATION SCRIPTS

### Migration from Original to Normalized Structure

```sql
-- Step 1: Create centralized persons table
CREATE TABLE persons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(16) UNIQUE NULL,
    nip VARCHAR(20) UNIQUE NULL,
    full_name VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NULL,
    title VARCHAR(50) NULL,
    gender ENUM('male', 'female', 'other') NULL,
    birth_date DATE NULL,
    birth_place VARCHAR(100) NULL,
    phone_number VARCHAR(20) NULL,
    mobile_number VARCHAR(20) NULL,
    email VARCHAR(255) UNIQUE NULL,
    avatar_url VARCHAR(500) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_persons_nik (nik),
    INDEX idx_persons_nip (nip),
    INDEX idx_persons_name (full_name),
    INDEX idx_persons_email (email),
    INDEX idx_persons_phone (phone_number),
    INDEX idx_persons_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: Migrate person data from existing tables
-- Migrate from users table
INSERT INTO persons (nik, nip, full_name, phone_number, email, avatar_url)
SELECT DISTINCT 
    nik, 
    nip, 
    full_name, 
    phone_number, 
    email, 
    avatar_url
FROM users 
WHERE nik IS NOT NULL OR nip IS NOT NULL OR full_name IS NOT NULL;

-- Step 3: Update users table to reference persons
ALTER TABLE users 
ADD COLUMN person_id BIGINT UNSIGNED AFTER id,
ADD FOREIGN KEY (person_id) REFERENCES persons(id);

UPDATE users u 
SET person_id = (
    SELECT p.id FROM persons p 
    WHERE (p.nik = u.nik AND u.nik IS NOT NULL) 
    OR (p.nip = u.nip AND u.nip IS NOT NULL)
    OR (p.email = u.email AND u.email IS NOT NULL)
    LIMIT 1
);

-- Remove redundant columns from users
ALTER TABLE users 
DROP COLUMN nik,
DROP COLUMN nip,
DROP COLUMN full_name,
DROP COLUMN phone_number,
DROP COLUMN avatar_url;

-- Step 4: Create new reference tables (excluding address tables)
-- Address tables already exist in separate database

-- Step 5: Skip countries, provinces, regencies, districts, villages migration
-- These tables already exist in address database

-- Step 6: Migrate customer categories
INSERT INTO customer_categories (code, name, description, default_discount_rate)
SELECT 
    DISTINCT category,
    CASE category 
        WHEN 'a' THEN 'Category A - Premium'
        WHEN 'b' THEN 'Category B - Gold'
        WHEN 'c' THEN 'Category C - Silver'
        WHEN 'd' THEN 'Category D - Bronze'
        WHEN 'e' THEN 'Category E - Basic'
    END,
    CONCAT('Customer category ', UPPER(category)),
    CASE category 
        WHEN 'a' THEN 15.0
        WHEN 'b' THEN 10.0
        WHEN 'c' THEN 7.5
        WHEN 'd' THEN 5.0
        WHEN 'e' THEN 2.5
    END
FROM customers;

-- Step 4: Update customers table
ALTER TABLE customers 
ADD COLUMN customer_category_id BIGINT UNSIGNED AFTER type,
ADD FOREIGN KEY (customer_category_id) REFERENCES customer_categories(id);

UPDATE customers c 
SET customer_category_id = (
    SELECT id FROM customer_categories cc WHERE cc.code = c.category
);

ALTER TABLE customers DROP COLUMN category;

-- Step 5: Migrate product units
INSERT INTO product_units (code, name, description)
SELECT 
    DISTINCT unit_of_measure,
    unit_of_measure,
    CONCAT('Unit of measure: ', unit_of_measure)
FROM products
WHERE unit_of_measure IS NOT NULL;

-- Step 6: Update products table
ALTER TABLE products 
ADD COLUMN unit_id BIGINT UNSIGNED AFTER brand,
ADD COLUMN tax_code_id BIGINT UNSIGNED NULL AFTER selling_price,
ADD FOREIGN KEY (unit_id) REFERENCES product_units(id),
ADD FOREIGN KEY (tax_code_id) REFERENCES tax_codes(id);

UPDATE products p 
SET unit_id = (
    SELECT id FROM product_units pu WHERE pu.code = p.unit_of_measure
);

ALTER TABLE products DROP COLUMN unit_of_measure;

-- Step 7: Migrate user permissions from JSON
INSERT INTO user_permissions (role_id, permission_name, permission_group, description)
SELECT 
    r.id,
    TRIM(BOTH '"' FROM JSON_UNQUOTE(JSON_EXTRACT(r.permissions, CONCAT('$[', seq.seq, ']')))),
    'general',
    CONCAT('Permission for role ', r.name)
FROM roles r
CROSS JOIN (
    SELECT 0 as seq UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION 
    SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION 
    SELECT 8 UNION SELECT 9
) seq
WHERE JSON_EXTRACT(r.permissions, CONCAT('$[', seq.seq, ']')) IS NOT NULL;

-- Step 8: Update roles table
ALTER TABLE roles DROP COLUMN permissions;
```

---

## 📈 SAMPLE DATA & SEEDING

### Master Data Seeding (Updated)
```sql
-- Default Roles
INSERT INTO roles (name, display_name, level) VALUES
('super_admin', 'Super Administrator', 0),
('admin', 'Administrator', 10),
('manager', 'Manager', 20),
('sales', 'Sales', 30),
('warehouse', 'Warehouse', 40),
('finance', 'Finance', 50),
('user', 'Basic User', 100);

-- Default Permissions
INSERT INTO user_permissions (role_id, permission_name, permission_group, description) VALUES
(1, '*', 'all', 'All permissions'),
(2, 'users.*', 'users', 'Full user management'),
(2, 'products.*', 'products', 'Full product management'),
(2, 'sales.*', 'sales', 'Full sales management'),
(2, 'inventory.*', 'inventory', 'Full inventory management'),
(3, 'sales.*', 'sales', 'Full sales management'),
(3, 'inventory.view', 'inventory', 'View inventory'),
(3, 'reports.view', 'reports', 'View reports');

-- Default Product Categories
INSERT INTO product_categories (code, name, level) VALUES
('BGR', 'Bahan Gizi', 1),
('BGR-01', 'Beras', 2),
('BGR-02', 'Tepung', 2),
('BGR-03', 'Gula', 2),
('BGR-04', 'Minyak Goreng', 2);

-- Default Product Units
INSERT INTO product_units (code, name, description) VALUES
('PCS', 'Pieces', 'Individual pieces'),
('KG', 'Kilogram', 'Weight in kilograms'),
('L', 'Liter', 'Volume in liters'),
('BOX', 'Box', 'Box packaging'),
('PACK', 'Pack', 'Pack packaging');

-- Default Tax Codes
INSERT INTO tax_codes (code, name, rate, description) VALUES
('PPN-11', 'PPN 11%', 11.00, 'Pajak Pertambahan Nilai 11%'),
('PPN-0', 'PPN 0%', 0.00, 'Pajak Pertambahan Nilai 0%'),
('NON-PPN', 'Non PPN', 0.00, 'Non PPN items');

-- Default Customer Categories
INSERT INTO customer_categories (code, name, description, default_discount_rate) VALUES
('A', 'Category A - Premium', 'Premium customers with highest benefits', 15.00),
('B', 'Category B - Gold', 'Gold customers with good benefits', 10.00),
('C', 'Category C - Silver', 'Silver customers with standard benefits', 7.50),
('D', 'Category D - Bronze', 'Bronze customers with basic benefits', 5.00),
('E', 'Category E - Basic', 'Basic customers with minimal benefits', 2.50);

-- Default PLU Codes (International Standards)
INSERT INTO plu_codes (plu_number, product_name, category, subcategory, variety, standard_unit, is_organic, is_conventional) VALUES
-- Fruits
('4011', 'Red Delicious Apples', 'FRUITS', 'APPLES', 'RED DELICIOUS', 'KG', FALSE, TRUE),
('4012', 'Golden Delicious Apples', 'FRUITS', 'APPLES', 'GOLDEN DELICIOUS', 'KG', FALSE, TRUE),
('4015', 'Gala Apples', 'FRUITS', 'APPLES', 'GALA', 'KG', FALSE, TRUE),
('4016', 'Granny Smith Apples', 'FRUITS', 'APPLES', 'GRANNY SMITH', 'KG', FALSE, TRUE),
('4017', 'Fuji Apples', 'FRUITS', 'APPLES', 'FUJI', 'KG', FALSE, TRUE),
('4030', 'Bananas', 'FRUITS', 'BANANAS', 'CAVENDISH', 'KG', FALSE, TRUE),
('4031', 'Organic Bananas', 'FRUITS', 'BANANAS', 'CAVENDISH', 'KG', TRUE, FALSE),
('4046', 'Oranges', 'FRUITS', 'CITRUS', 'NAVEL', 'KG', FALSE, TRUE),
('4047', 'Organic Oranges', 'FRUITS', 'CITRUS', 'NAVEL', 'KG', TRUE, FALSE),
('4050', 'Lemons', 'FRUITS', 'CITRUS', 'EUREKA', 'KG', FALSE, TRUE),
('4052', 'Limes', 'FRUITS', 'CITRUS', 'PERSIAN', 'KG', FALSE, TRUE),
('4060', 'Grapes', 'FRUITS', 'GRAPES', 'RED GLOBE', 'KG', FALSE, TRUE),
('4062', 'Green Grapes', 'FRUITS', 'GRAPES', 'THOMPSON', 'KG', FALSE, TRUE),
('4063', 'Organic Grapes', 'FRUITS', 'GRAPES', 'RED GLOBE', 'KG', TRUE, FALSE),
('4066', 'Strawberries', 'FRUITS', 'BERRIES', 'ALBION', 'KG', FALSE, TRUE),
('4067', 'Organic Strawberries', 'FRUITS', 'BERRIES', 'ALBION', 'KG', TRUE, FALSE),
('4080', 'Mangoes', 'FRUITS', 'TROPICAL', 'TOMMY ATKINS', 'KG', FALSE, TRUE),
('4081', 'Organic Mangoes', 'FRUITS', 'TROPICAL', 'TOMMY ATKINS', 'KG', TRUE, FALSE),

-- Vegetables
('4068', 'Tomatoes', 'VEGETABLES', 'TOMATOES', 'BEEFSTEAK', 'KG', FALSE, TRUE),
('4069', 'Cherry Tomatoes', 'VEGETABLES', 'TOMATOES', 'CHERRY', 'KG', FALSE, TRUE),
('4070', 'Organic Tomatoes', 'VEGETABLES', 'TOMATOES', 'BEEFSTEAK', 'KG', TRUE, FALSE),
('4071', 'Cucumbers', 'VEGETABLES', 'CUCUMBERS', 'ENGLISH', 'KG', FALSE, TRUE),
('4072', 'Organic Cucumbers', 'VEGETABLES', 'CUCUMBERS', 'ENGLISH', 'KG', TRUE, FALSE),
('4075', 'Carrots', 'VEGETABLES', 'ROOT', 'NANTES', 'KG', FALSE, TRUE),
('4076', 'Organic Carrots', 'VEGETABLES', 'ROOT', 'NANTES', 'KG', TRUE, FALSE),
('4082', 'Potatoes', 'VEGETABLES', 'ROOT', 'RUSSET', 'KG', FALSE, TRUE),
('4083', 'Sweet Potatoes', 'VEGETABLES', 'ROOT', 'BEAUREGARD', 'KG', FALSE, TRUE),
('4084', 'Organic Potatoes', 'VEGETABLES', 'ROOT', 'RUSSET', 'KG', TRUE, FALSE),
('4085', 'Onions', 'VEGETABLES', 'ALLIUM', 'YELLOW', 'KG', FALSE, TRUE),
('4086', 'Red Onions', 'VEGETABLES', 'ALLIUM', 'RED', 'KG', FALSE, TRUE),
('4087', 'Organic Onions', 'VEGETABLES', 'ALLIUM', 'YELLOW', 'KG', TRUE, FALSE),
('4090', 'Lettuce', 'VEGETABLES', 'LEAFY', 'ICEBERG', 'PCS', FALSE, TRUE),
('4091', 'Romaine Lettuce', 'VEGETABLES', 'LEAFY', 'ROMAINE', 'PCS', FALSE, TRUE),
('4092', 'Organic Lettuce', 'VEGETABLES', 'LEAFY', 'ICEBERG', 'PCS', TRUE, FALSE),
('4093', 'Spinach', 'VEGETABLES', 'LEAFY', 'SAVOY', 'KG', FALSE, TRUE),
('4094', 'Organic Spinach', 'VEGETABLES', 'LEAFY', 'SAVOY', 'KG', TRUE, FALSE),
('4095', 'Broccoli', 'VEGETABLES', 'CRUCIFEROUS', 'CALABRESE', 'KG', FALSE, TRUE),
('4096', 'Organic Broccoli', 'VEGETABLES', 'CRUCIFEROUS', 'CALABRESE', 'KG', TRUE, FALSE),
('4097', 'Cauliflower', 'VEGETABLES', 'CRUCIFEROUS', 'WHITE', 'KG', FALSE, TRUE),
('4098', 'Organic Cauliflower', 'VEGETABLES', 'CRUCIFEROUS', 'WHITE', 'KG', TRUE, FALSE),

-- Local Indonesian Products (Custom PLU range 9000-9999)
('9001', 'Cabai Merah Besar', 'VEGETABLES', 'CHILI', 'BESAR', 'KG', FALSE, TRUE),
('9002', 'Cabai Rawit', 'VEGETABLES', 'CHILI', 'RAWIT', 'KG', FALSE, TRUE),
('9003', 'Cabai Hijau', 'VEGETABLES', 'CHILI', 'HIJAU', 'KG', FALSE, TRUE),
('9010', 'Terong Ungu', 'VEGETABLES', 'EGGPLANT', 'UNGU', 'KG', FALSE, TRUE),
('9011', 'Terong Hijau', 'VEGETABLES', 'EGGPLANT', 'HIJAU', 'KG', FALSE, TRUE),
('9020', 'Kangkung', 'VEGETABLES', 'LEAFY', 'KANGKUNG', 'IKAT', FALSE, TRUE),
('9021', 'Bayam', 'VEGETABLES', 'LEAFY', 'BAYAM', 'IKAT', FALSE, TRUE),
('9030', 'Pisang Ambon', 'FRUITS', 'BANANAS', 'AMBON', 'KG', FALSE, TRUE),
('9031', 'Pisang Raja', 'FRUITS', 'BANANAS', 'RAJA', 'KG', FALSE, TRUE),
('9032', 'Pisang Kepok', 'FRUITS', 'BANANAS', 'KEPOK', 'KG', FALSE, TRUE),
('9040', 'Nanas', 'FRUITS', 'TROPICAL', 'QUEEN', 'PCS', FALSE, TRUE),
('9041', 'Semangka', 'FRUITS', 'MELONS', 'RED', 'PCS', FALSE, TRUE),
('9042', 'Melon', 'FRUITS', 'MELONS', 'GALIA', 'PCS', FALSE, TRUE),
('9050', 'Jeruk Sunkist', 'FRUITS', 'CITRUS', 'SUNKIST', 'KG', FALSE, TRUE),
('9051', 'Jeruk Lokal', 'FRUITS', 'CITRUS', 'KEPROK', 'KG', FALSE, TRUE);

-- Default Chart of Accounts
INSERT INTO chart_of_accounts (account_number, account_name, account_type, normal_balance) VALUES
('1000', 'Aktiva Lancar', 'asset', 'debit'),
('1100', 'Kas dan Bank', 'asset', 'debit'),
('1110', 'Kas', 'asset', 'debit'),
('1120', 'Bank', 'asset', 'debit'),
('2000', 'Utang Lancar', 'liability', 'credit'),
('2100', 'Utang Usaha', 'liability', 'credit'),
('3000', 'Ekuitas', 'equity', 'credit'),
('4000', 'Pendapatan', 'revenue', 'credit'),
('4100', 'Penjualan', 'revenue', 'credit'),
('5000', 'Harga Pokok Penjualan', 'expense', 'debit'),
('6000', 'Beban Operasional', 'expense', 'debit');

-- Note: Address data (countries, provinces, regencies, districts, villages) 
-- should be seeded in the address database separately
```

---

## 🔒 SECURITY & COMPLIANCE

### Data Encryption
```sql
-- Sensitive Data Encryption
ALTER TABLE customers 
ADD COLUMN encrypted_tax_id VARBINARY(255),
ADD COLUMN encrypted_business_license VARBINARY(255);

ALTER TABLE users 
ADD COLUMN encrypted_nik VARBINARY(255),
ADD COLUMN encrypted_phone_number VARBINARY(255);
```

### Audit Trail
```sql
CREATE TABLE audit_trail (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(100) NOT NULL,
    record_id BIGINT UNSIGNED NOT NULL,
    action ENUM('insert', 'update', 'delete') NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    changed_fields JSON NULL,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_audit_table_record (table_name, record_id),
    INDEX idx_audit_action (action),
    INDEX idx_audit_user (user_id),
    INDEX idx_audit_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 📈 BACKUP & MAINTENANCE

### Backup Strategy
```sql
-- Backup Tables
CREATE TABLE backup_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    backup_type ENUM('full', 'incremental', 'differential') NOT NULL,
    table_name VARCHAR(100) NULL,
    backup_file VARCHAR(500) NOT NULL,
    file_size BIGINT NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    status ENUM('running', 'completed', 'failed') NOT NULL,
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_backup_type (backup_type),
    INDEX idx_backup_status (status),
    INDEX idx_backup_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🎯 CONCLUSION

Rancangan database ini dirancang untuk:
- **Scalability:** Mendukung pertumbuhan bisnis jangka panjang
- **Performance:** Query yang optimal dengan indexing strategy
- **Data Integrity:** Constraints dan audit trail
- **Security:** Enkripsi data sensitif
- **Compliance:** Memenuhi regulasi Indonesia
- **Flexibility:** Mudah dikembangkan dan di-modifikasi

Total tabel utama: **50+ tabel**
Total estimasi ukuran: **10-50GB untuk 5 tahun operasi**
Performance target: **<100ms untuk 95% queries**

---

*Dokumen ini akan terus di-update sesuai dengan kebutuhan bisnis dan perkembangan teknologi.*
