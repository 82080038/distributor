# Database Normalization Analysis & Recommendations

## 🎯 **ANALISIS NORMALISASI DATABASE**

Saya telah menganalisis semua schema database dan menemukan beberapa area yang bisa dinormalisasi lebih lanjut untuk mencapai **3NF (Third Normal Form)** yang optimal.

---

## **📊 TABEL-TABEL YANG PERLU NORMALISASI:**

### **1. ORANG SCHEMA - Perbaikan Normalisasi**

#### **❌ CURRENT ISSUE: `persons` Table**
```sql
-- Masalah: Redundancy data alamat langsung di persons
CREATE TABLE persons (
    ...
    village_id INT UNSIGNED NULL,
    district_id INT UNSIGNED NULL, 
    regency_id INT UNSIGNED NULL,
    province_id INT UNSIGNED NULL,
    address VARCHAR(255) NOT NULL,
    ...
);
```

#### **✅ NORMALIZED SOLUTION:**
```sql
-- Pisahkan alamat ke table terpisah
CREATE TABLE person_addresses_normalized (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    person_id BIGINT UNSIGNED NOT NULL,
    address_type ENUM('home', 'office', 'billing', 'shipping') NOT NULL,
    village_id INT UNSIGNED NULL,
    district_id INT UNSIGNED NULL,
    regency_id INT UNSIGNED NULL, 
    province_id INT UNSIGNED NULL,
    address_line VARCHAR(255) NOT NULL,
    postal_code VARCHAR(10) NULL,
    latitude DECIMAL(10,8) NULL,
    longitude DECIMAL(11,8) NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE,
    FOREIGN KEY (village_id) REFERENCES alamat_db.villages(id) ON DELETE SET NULL,
    FOREIGN KEY (district_id) REFERENCES alamat_db.districts(id) ON DELETE SET NULL,
    FOREIGN KEY (regency_id) REFERENCES alamat_db.regencies(id) ON DELETE SET NULL,
    FOREIGN KEY (province_id) REFERENCES alamat_db.provinces(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_person_address_type (person_id, address_type),
    INDEX idx_person_id (person_id),
    INDEX idx_village_id (village_id),
    INDEX idx_is_primary (is_primary)
) ENGINE=InnoDB COMMENT='Normalized person addresses';
```

#### **❌ CURRENT ISSUE: `person_contacts` Table**
```sql
-- Masalah: Multiple contact types dalam satu table
CREATE TABLE person_contacts (
    ...
    contact_type ENUM('phone', 'email', 'whatsapp', 'telegram', 'social_media', 'other') NOT NULL,
    contact_value VARCHAR(255) NOT NULL,
    ...
);
```

#### **✅ NORMALIZED SOLUTION:**
```sql
-- Pisahkan per contact type
CREATE TABLE person_phones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    person_id BIGINT UNSIGNED NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    phone_type ENUM('mobile', 'home', 'office', 'fax', 'other') DEFAULT 'mobile',
    is_primary BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE,
    UNIQUE KEY unique_person_phone (person_id, phone_number),
    INDEX idx_person_id (person_id),
    INDEX idx_phone_number (phone_number),
    INDEX idx_is_primary (is_primary)
) ENGINE=InnoDB COMMENT='Person phone numbers';

CREATE TABLE person_emails (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    person_id BIGINT UNSIGNED NOT NULL,
    email_address VARCHAR(255) NOT NULL,
    email_type ENUM('personal', 'work', 'other') DEFAULT 'personal',
    is_primary BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE,
    UNIQUE KEY unique_person_email (person_id, email_address),
    INDEX idx_person_id (person_id),
    INDEX idx_email_address (email_address),
    INDEX idx_is_primary (is_primary)
) ENGINE=InnoDB COMMENT='Person email addresses';

CREATE TABLE person_social_media (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    person_id BIGINT UNSIGNED NOT NULL,
    platform ENUM('facebook', 'twitter', 'instagram', 'linkedin', 'whatsapp', 'telegram', 'other') NOT NULL,
    profile_url VARCHAR(500) NOT NULL,
    username VARCHAR(100) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE,
    UNIQUE KEY unique_person_platform (person_id, platform),
    INDEX idx_person_id (person_id),
    INDEX idx_platform (platform)
) ENGINE=InnoDB COMMENT='Person social media accounts';
```

---

### **2. BARANG SCHEMA - Perbaikan Normalisasi**

#### **❌ CURRENT ISSUE: `products` Table**
```sql
-- Masalah: Multiple attributes dalam satu table
CREATE TABLE products (
    ...
    weight DECIMAL(10,3) NULL,
    length DECIMAL(8,2) NULL,
    width DECIMAL(8,2) NULL, 
    height DECIMAL(8,2) NULL,
    volume DECIMAL(10,3) NULL,
    min_stock_level DECIMAL(12,2) DEFAULT 0,
    max_stock_level DECIMAL(12,2) DEFAULT 0,
    reorder_point DECIMAL(12,2) DEFAULT 0,
    lead_time_days SMALLINT DEFAULT 0,
    ...
);
```

#### **✅ NORMALIZED SOLUTION:**
```sql
-- Pisahkan physical attributes
CREATE TABLE product_physical_attributes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    weight DECIMAL(10,3) NULL COMMENT 'Berat (kg)',
    length DECIMAL(8,2) NULL COMMENT 'Panjang (cm)',
    width DECIMAL(8,2) NULL COMMENT 'Lebar (cm)',
    height DECIMAL(8,2) NULL COMMENT 'Tinggi (cm)',
    volume DECIMAL(10,3) NULL COMMENT 'Volume (m³)',
    dimension_unit ENUM('cm', 'inch', 'mm') DEFAULT 'cm',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_physical (product_id),
    INDEX idx_product_id (product_id),
    INDEX idx_weight (weight),
    INDEX idx_volume (volume)
) ENGINE=InnoDB COMMENT='Product physical attributes';

-- Pisahkan inventory settings
CREATE TABLE product_inventory_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED NULL,
    min_stock_level DECIMAL(12,2) DEFAULT 0,
    max_stock_level DECIMAL(12,2) DEFAULT 0,
    reorder_point DECIMAL(12,2) DEFAULT 0,
    safety_stock DECIMAL(12,2) DEFAULT 0,
    lead_time_days SMALLINT DEFAULT 0,
    reorder_quantity DECIMAL(12,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_warehouse (product_id, warehouse_id),
    INDEX idx_product_id (product_id),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_reorder_point (reorder_point)
) ENGINE=InnoDB COMMENT='Product inventory settings per warehouse';
```

---

### **3. APLIKASI SCHEMA - Perbaikan Normalisasi**

#### **❌ CURRENT ISSUE: `sales` Table**
```sql
-- Masalah: Payment info mixed dengan sales info
CREATE TABLE sales (
    ...
    subtotal DECIMAL(15,2) DEFAULT 0,
    discount_percent DECIMAL(5,2) DEFAULT 0,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    tax_percent DECIMAL(5,2) DEFAULT 0,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) GENERATED ALWAYS AS (subtotal - discount_amount + tax_amount) STORED,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    remaining_amount DECIMAL(15,2) GENERATED ALWAYS AS (total_amount - paid_amount) STORED,
    payment_terms VARCHAR(100) NULL,
    ...
);
```

#### **✅ NORMALIZED SOLUTION:**
```sql
-- Pisahkan payment information
CREATE TABLE sales_payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id BIGINT UNSIGNED NOT NULL,
    payment_sequence INT NOT NULL COMMENT 'Urutan pembayaran',
    payment_method ENUM('cash', 'transfer', 'check', 'card', 'ewallet', 'credit') NOT NULL,
    payment_date DATE NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    bank_name VARCHAR(100) NULL,
    account_number VARCHAR(50) NULL,
    check_number VARCHAR(50) NULL,
    card_number VARCHAR(20) NULL,
    transaction_ref VARCHAR(100) NULL,
    notes VARCHAR(255) NULL,
    status ENUM('pending', 'confirmed', 'failed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_sale_id (sale_id),
    INDEX idx_payment_date (payment_date),
    INDEX idx_payment_method (payment_method),
    INDEX idx_amount (amount),
    INDEX idx_status (status)
) ENGINE=InnoDB COMMENT='Sales payment details';

-- Pisahkan discount information
CREATE TABLE sales_discounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id BIGINT UNSIGNED NOT NULL,
    discount_type ENUM('percentage', 'fixed', 'buy_get', 'volume') NOT NULL,
    discount_value DECIMAL(15,2) NOT NULL,
    discount_reason VARCHAR(255) NULL,
    promo_code VARCHAR(50) NULL,
    applied_by BIGINT UNSIGNED NULL,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (applied_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_sale_id (sale_id),
    INDEX idx_discount_type (discount_type),
    INDEX idx_discount_value (discount_value),
    INDEX idx_promo_code (promo_code)
) ENGINE=InnoDB COMMENT='Sales discount details';

-- Pisahkan tax information
CREATE TABLE sales_taxes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id BIGINT UNSIGNED NOT NULL,
    tax_type ENUM('vat', 'sales_tax', 'service_tax', 'other') NOT NULL,
    tax_rate DECIMAL(5,2) NOT NULL,
    taxable_amount DECIMAL(15,2) NOT NULL,
    tax_amount DECIMAL(15,2) NOT NULL,
    tax_code VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    
    INDEX idx_sale_id (sale_id),
    INDEX idx_tax_type (tax_type),
    INDEX idx_tax_rate (tax_rate),
    INDEX idx_tax_amount (tax_amount)
) ENGINE=InnoDB COMMENT='Sales tax details';
```

---

### **4. HRM SCHEMA - Perbaikan Normalisasi**

#### **❌ CURRENT ISSUE: `employees` Table**
```sql
-- Masalah: Multiple roles dan departments dalam satu table
CREATE TABLE employees (
    ...
    department_id BIGINT UNSIGNED NULL,
    position_id BIGINT UNSIGNED NOT NULL,
    supervisor_id BIGINT UNSIGNED NULL,
    ...
);
```

#### **✅ NORMALIZED SOLUTION:**
```sql
-- Pisahkan employee assignments
CREATE TABLE employee_department_assignments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED NOT NULL,
    assignment_type ENUM('primary', 'secondary', 'temporary') DEFAULT 'primary',
    role_in_department VARCHAR(100) NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    
    INDEX idx_employee_id (employee_id),
    INDEX idx_department_id (department_id),
    INDEX idx_assignment_type (assignment_type),
    INDEX idx_start_date (start_date),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Employee department assignments';

CREATE TABLE employee_position_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL,
    position_id BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    salary_at_assignment DECIMAL(12,2) NULL,
    promotion_reason VARCHAR(255) NULL,
    is_current BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE RESTRICT,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    
    INDEX idx_employee_id (employee_id),
    INDEX idx_position_id (position_id),
    INDEX idx_department_id (department_id),
    INDEX idx_start_date (start_date),
    INDEX idx_is_current (is_current)
) ENGINE=InnoDB COMMENT='Employee position history';
```

---

### **5. FINANCE SCHEMA - Perbaikan Normalisasi**

#### **❌ CURRENT ISSUE: `chart_of_accounts` Table**
```sql
-- Masalah: Recursive structure tanpa proper normalization
CREATE TABLE chart_of_accounts (
    ...
    parent_account_id BIGINT UNSIGNED NULL,
    account_level TINYINT DEFAULT 1,
    normal_balance ENUM('debit', 'credit') NOT NULL,
    ...
);
```

#### **✅ NORMALIZED SOLUTION:**
```sql
-- Pisahkan account hierarchy
CREATE TABLE account_hierarchy (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_account_id BIGINT UNSIGNED NOT NULL,
    child_account_id BIGINT UNSIGNED NOT NULL,
    hierarchy_level TINYINT NOT NULL,
    path VARCHAR(500) NOT NULL COMMENT 'Path dalam hierarchy',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_account_id) REFERENCES chart_of_accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (child_account_id) REFERENCES chart_of_accounts(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_parent_child (parent_account_id, child_account_id),
    INDEX idx_parent_account_id (parent_account_id),
    INDEX idx_child_account_id (child_account_id),
    INDEX idx_hierarchy_level (hierarchy_level),
    INDEX idx_path (path)
) ENGINE=InnoDB COMMENT='Account hierarchy structure';

-- Pisahkan account types
CREATE TABLE account_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type_code VARCHAR(20) UNIQUE NOT NULL,
    type_name VARCHAR(100) NOT NULL,
    parent_type_id BIGINT UNSIGNED NULL,
    description TEXT NULL,
    normal_balance ENUM('debit', 'credit') NOT NULL,
    is_contra_account BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_type_id) REFERENCES account_types(id) ON DELETE SET NULL,
    
    INDEX idx_type_code (type_code),
    INDEX idx_type_name (type_name),
    INDEX idx_parent_type_id (parent_type_id),
    INDEX idx_normal_balance (normal_balance),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Account types classification';

-- Update chart_of_accounts dengan reference ke account_types
ALTER TABLE chart_of_accounts 
ADD COLUMN account_type_id BIGINT UNSIGNED NULL COMMENT 'Link ke account_types',
ADD FOREIGN KEY (account_type_id) REFERENCES account_types(id) ON DELETE SET NULL;
```

---

## **📊 SUMMARY NORMALISASI:**

### **✅ AREAS YANG SUDAH BAIK:**
1. **Alamat Database** - Sudah terpisah dengan foreign keys yang tepat
2. **Time Periods** - Sudah terstruktur dengan baik
3. **Document Templates** - Sudah terpisah dari documents
4. **GPS Tracking** - Sudah terpisah dengan proper indexing

### **⚠️ AREAS YANG PERLU PERBAIKAN:**
1. **Person Contacts** - Pisahkan per contact type
2. **Product Attributes** - Pisahkan physical attributes dan inventory settings
3. **Sales Payments** - Pisahkan payment, discount, dan tax
4. **Employee Assignments** - Pisahkan department dan position history
5. **Account Hierarchy** - Pisahkan hierarchy dari account data

### **🎯 BENEFITS NORMALISASI:**
1. **Reduced Data Redundancy** - Tidak ada duplikasi data
2. **Improved Data Integrity** - Foreign keys yang lebih tepat
3. **Better Performance** - Index yang lebih optimal
4. **Easier Maintenance** - Table yang lebih spesifik
5. **Scalability** - Lebih mudah untuk scale
6. **Flexibility** - Lebih mudah untuk menambah fitur

---

## **🚀 IMPLEMENTATION RECOMMENDATIONS:**

### **Phase 1: Critical Normalization (Week 1-2)**
1. **Person contacts normalization**
2. **Sales payment separation**
3. **Product attributes separation**

### **Phase 2: Advanced Normalization (Week 3-4)**
1. **Employee assignments normalization**
2. **Account hierarchy normalization**
3. **Address normalization**

### **Phase 3: Optimization (Week 5-6)**
1. **Index optimization**
2. **Query optimization**
3. **Performance tuning**

---

## **🎯 KESIMPULAN:**

**Ya, ada beberapa tabel yang bisa dinormalisasi lebih lanjut** untuk mencapai **3NF yang optimal**. Normalisasi ini akan:

✅ **Mengurangi redundancy** data
✅ **Meningkatkan integrity** data  
✅ **Memperbaiki performance** query
✅ **Memudahkan maintenance** database
✅ **Meningkatkan scalability** sistem

**Namun, perlu dipertimbangkan trade-off antara normalisasi dan complexity query untuk implementasi yang praktis.**
