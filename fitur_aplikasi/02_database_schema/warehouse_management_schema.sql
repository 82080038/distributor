-- ========================================
-- Warehouse Management Schema
-- ========================================

-- Tabel Warehouses
CREATE TABLE warehouses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    warehouse_code VARCHAR(20) UNIQUE NOT NULL,
    warehouse_name VARCHAR(150) NOT NULL,
    warehouse_type ENUM('MAIN', 'BRANCH', 'TRANSIT', 'RETURN', 'DISPOSAL') DEFAULT 'MAIN',
    address TEXT,
    city VARCHAR(100),
    province VARCHAR(100),
    postal_code VARCHAR(10),
    phone VARCHAR(50),
    manager_id INT,
    total_capacity DECIMAL(15,3) DEFAULT 0,
    current_utilization DECIMAL(15,3) DEFAULT 0,
    temperature_controlled BOOLEAN DEFAULT FALSE,
    humidity_controlled BOOLEAN DEFAULT FALSE,
    security_level ENUM('LOW', 'MEDIUM', 'HIGH') DEFAULT 'MEDIUM',
    operating_hours JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_warehouse_code (warehouse_code),
    INDEX idx_is_active (is_active),
    INDEX idx_manager_id (manager_id)
);

-- Tabel Warehouse Locations (Rak, Shelf, Bin)
CREATE TABLE warehouse_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    warehouse_id INT NOT NULL,
    location_code VARCHAR(30) NOT NULL,
    location_type ENUM('AREA', 'RACK', 'SHELF', 'BIN', 'PALLET', 'ZONE') DEFAULT 'SHELF',
    parent_location_id INT,
    rack_code VARCHAR(20),
    shelf_code VARCHAR(20),
    bin_code VARCHAR(20),
    level_number INT,
    position_description VARCHAR(200),
    capacity_volume DECIMAL(10,3),
    capacity_weight DECIMAL(15,3),
    current_volume DECIMAL(10,3) DEFAULT 0,
    current_weight DECIMAL(15,3) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_location_id) REFERENCES warehouse_locations(id),
    UNIQUE KEY unique_location_code (warehouse_id, location_code),
    INDEX idx_location_type (location_type),
    INDEX idx_parent_location (parent_location_id)
);

-- Tabel Product Stock per Warehouse
CREATE TABLE warehouse_stocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    warehouse_id INT NOT NULL,
    location_id INT,
    product_id INT NOT NULL,
    quantity_on_hand DECIMAL(15,3) NOT NULL DEFAULT 0,
    quantity_reserved DECIMAL(15,3) NOT NULL DEFAULT 0,
    quantity_available DECIMAL(15,3) GENERATED ALWAYS AS (quantity_on_hand - quantity_reserved) STORED,
    reorder_level DECIMAL(15,3) DEFAULT 0,
    max_level DECIMAL(15,3) DEFAULT 0,
    last_count_date DATE,
    last_received_date DATE,
    average_cost DECIMAL(15,2) DEFAULT 0,
    total_value DECIMAL(15,2) GENERATED ALWAYS AS (quantity_on_hand * average_cost) STORED,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    FOREIGN KEY (location_id) REFERENCES warehouse_locations(id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_warehouse_product (warehouse_id, product_id),
    INDEX idx_quantity_available (quantity_available),
    INDEX idx_last_count_date (last_count_date)
);

-- Tabel Stock Batches dengan Tracking
CREATE TABLE stock_batches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    batch_number VARCHAR(50) NOT NULL,
    product_id INT NOT NULL,
    warehouse_id INT NOT NULL,
    location_id INT,
    initial_quantity DECIMAL(15,3) NOT NULL,
    current_quantity DECIMAL(15,3) NOT NULL,
    reserved_quantity DECIMAL(15,3) DEFAULT 0,
    available_quantity DECIMAL(15,3) GENERATED ALWAYS AS (current_quantity - reserved_quantity) STORED,
    unit_cost DECIMAL(15,2) NOT NULL,
    manufacture_date DATE,
    expiry_date DATE,
    supplier_id INT,
    purchase_order_id VARCHAR(50),
    quality_grade ENUM('A', 'B', 'C', 'REJECT') DEFAULT 'A',
    storage_conditions JSON,
    status ENUM('ACTIVE', 'EXPIRED', 'QUARANTINE', 'DISPOSED') DEFAULT 'ACTIVE',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    FOREIGN KEY (location_id) REFERENCES warehouse_locations(id),
    FOREIGN KEY (supplier_id) REFERENCES orang(id_orang),
    UNIQUE KEY unique_batch_number (batch_number),
    INDEX idx_expiry_date (expiry_date),
    INDEX idx_status (status)
);

-- Tabel Stock Movements
CREATE TABLE stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movement_number VARCHAR(50) UNIQUE NOT NULL,
    movement_type ENUM('IN', 'OUT', 'TRANSFER', 'ADJUSTMENT', 'RETURN', 'DISPOSAL') NOT NULL,
    movement_date DATETIME NOT NULL,
    reference_type ENUM('PURCHASE', 'SALE', 'TRANSFER', 'ADJUSTMENT', 'RETURN', 'EXPIRY') NOT NULL,
    reference_id INT,
    reference_number VARCHAR(50),
    product_id INT NOT NULL,
    batch_id INT,
    from_warehouse_id INT,
    to_warehouse_id INT,
    from_location_id INT,
    to_location_id INT,
    quantity DECIMAL(15,3) NOT NULL,
    unit_cost DECIMAL(15,2) DEFAULT 0,
    total_value DECIMAL(15,2) GENERATED ALWAYS AS (quantity * unit_cost) STORED,
    reason_code VARCHAR(50),
    reason_description TEXT,
    approved_by INT,
    status ENUM('DRAFT', 'CONFIRMED', 'POSTED') DEFAULT 'DRAFT',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (batch_id) REFERENCES stock_batches(id),
    FOREIGN KEY (from_warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (to_warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (from_location_id) REFERENCES warehouse_locations(id),
    FOREIGN KEY (to_location_id) REFERENCES warehouse_locations(id),
    INDEX idx_movement_date (movement_date),
    INDEX idx_movement_type (movement_type),
    INDEX idx_reference_type (reference_type),
    INDEX idx_status (status)
);

-- Tabel Stock Movement Details
CREATE TABLE stock_movement_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movement_id INT NOT NULL,
    batch_id INT NOT NULL,
    quantity_before DECIMAL(15,3) NOT NULL,
    quantity_moved DECIMAL(15,3) NOT NULL,
    quantity_after DECIMAL(15,3) NOT NULL,
    cost_per_unit DECIMAL(15,2) NOT NULL,
    total_cost DECIMAL(15,2) GENERATED ALWAYS AS (quantity_moved * cost_per_unit) STORED,
    
    FOREIGN KEY (movement_id) REFERENCES stock_movements(id) ON DELETE CASCADE,
    FOREIGN KEY (batch_id) REFERENCES stock_batches(id),
    INDEX idx_movement_details (movement_id)
);

-- Tabel Stock Opname (Stock Count)
CREATE TABLE stock_counts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    count_number VARCHAR(50) UNIQUE NOT NULL,
    warehouse_id INT NOT NULL,
    location_id INT,
    product_id INT NOT NULL,
    system_quantity DECIMAL(15,3) NOT NULL,
    counted_quantity DECIMAL(15,3) NOT NULL,
    difference_quantity DECIMAL(15,3) GENERATED ALWAYS AS (counted_quantity - system_quantity) STORED,
    difference_value DECIMAL(15,2) GENERATED ALWAYS AS (difference_quantity * (
        SELECT COALESCE(AVG(unit_cost), 0) 
        FROM warehouse_stocks 
        WHERE product_id = stock_counts.product_id 
        AND warehouse_id = stock_counts.warehouse_id
    )) STORED,
    variance_percentage DECIMAL(5,2) GENERATED ALWAYS AS (
        CASE 
            WHEN system_quantity > 0 THEN (difference_quantity / system_quantity) * 100
            ELSE 0 
        END
    ) STORED,
    count_status ENUM('MATCH', 'SHORTAGE', 'OVERAGE', 'PENDING') DEFAULT 'PENDING',
    recount_required BOOLEAN DEFAULT FALSE,
    counted_by INT,
    supervised_by INT,
    count_date DATE NOT NULL,
    approved_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (location_id) REFERENCES warehouse_locations(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (counted_by) REFERENCES user(id_user),
    FOREIGN KEY (supervised_by) REFERENCES user(id_user),
    INDEX idx_count_number (count_number),
    INDEX idx_count_date (count_date),
    INDEX idx_count_status (count_status)
);

-- Tabel Transfer Orders ( Antar Gudang)
CREATE TABLE transfer_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transfer_number VARCHAR(50) UNIQUE NOT NULL,
    from_warehouse_id INT NOT NULL,
    to_warehouse_id INT NOT NULL,
    transfer_date DATE NOT NULL,
    requested_by INT,
    approved_by INT,
    status ENUM('DRAFT', 'APPROVED', 'IN_TRANSIT', 'RECEIVED', 'PARTIAL', 'CANCELLED') DEFAULT 'DRAFT',
    priority ENUM('LOW', 'MEDIUM', 'HIGH', 'URGENT') DEFAULT 'MEDIUM',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (from_warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (to_warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (requested_by) REFERENCES user(id_user),
    FOREIGN KEY (approved_by) REFERENCES user(id_user),
    INDEX idx_transfer_number (transfer_number),
    INDEX idx_transfer_date (transfer_date),
    INDEX idx_status (status)
);

-- Tabel Transfer Order Items
CREATE TABLE transfer_order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transfer_id INT NOT NULL,
    product_id INT NOT NULL,
    batch_id INT,
    requested_quantity DECIMAL(15,3) NOT NULL,
    sent_quantity DECIMAL(15,3) DEFAULT 0,
    received_quantity DECIMAL(15,3) DEFAULT 0,
    unit_cost DECIMAL(15,2) DEFAULT 0,
    total_value DECIMAL(15,2) GENERATED ALWAYS AS (requested_quantity * unit_cost) STORED,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (transfer_id) REFERENCES transfer_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (batch_id) REFERENCES stock_batches(id),
    INDEX idx_transfer_items (transfer_id),
    INDEX idx_product_id (product_id)
);

-- View untuk Warehouse Stock Summary
CREATE VIEW warehouse_stock_summary_view AS
SELECT 
    w.warehouse_code,
    w.warehouse_name,
    p.name as product_name,
    p.code as product_code,
    ws.quantity_on_hand,
    ws.quantity_reserved,
    ws.quantity_available,
    ws.average_cost,
    ws.total_value,
    ws.reorder_level,
    CASE 
        WHEN ws.quantity_available <= ws.reorder_level THEN 'CRITICAL'
        WHEN ws.quantity_available <= (ws.reorder_level * 1.5) THEN 'LOW'
        ELSE 'NORMAL'
    END as stock_status,
    ws.last_count_date,
    wl.location_code
FROM warehouses w
JOIN warehouse_stocks ws ON w.id = ws.warehouse_id
JOIN products p ON ws.product_id = p.id
LEFT JOIN warehouse_locations wl ON ws.location_id = wl.id
WHERE w.is_active = TRUE;

-- View untuk Expiry Tracking
CREATE VIEW stock_expiry_view AS
SELECT 
    sb.batch_number,
    p.name as product_name,
    p.code as product_code,
    w.warehouse_name,
    sb.expiry_date,
    sb.current_quantity,
    sb.available_quantity,
    DATEDIFF(sb.expiry_date, CURDATE()) as days_to_expiry,
    CASE 
        WHEN DATEDIFF(sb.expiry_date, CURDATE()) <= 0 THEN 'EXPIRED'
        WHEN DATEDIFF(sb.expiry_date, CURDATE()) <= 30 THEN 'EXPIRING_SOON'
        WHEN DATEDIFF(sb.expiry_date, CURDATE()) <= 90 THEN 'EXPIRING_90_DAYS'
        ELSE 'GOOD'
    END as expiry_status,
    sb.current_quantity * sb.unit_cost as total_value
FROM stock_batches sb
JOIN products p ON sb.product_id = p.id
JOIN warehouses w ON sb.warehouse_id = w.id
WHERE sb.status = 'ACTIVE' 
AND sb.expiry_date IS NOT NULL
ORDER BY sb.expiry_date ASC;
