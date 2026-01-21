-- ========================================
-- Complete Accounting System Schema
-- ========================================

-- Tabel Chart of Accounts (Perkiraan)
CREATE TABLE chart_of_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_code VARCHAR(20) UNIQUE NOT NULL,
    account_name VARCHAR(150) NOT NULL,
    account_type ENUM('ASSET', 'LIABILITY', 'EQUITY', 'REVENUE', 'EXPENSE') NOT NULL,
    account_category VARCHAR(100),
    parent_id INT,
    level INT DEFAULT 1,
    is_active BOOLEAN DEFAULT TRUE,
    is_cash_account BOOLEAN DEFAULT FALSE,
    tax_account BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES chart_of_accounts(id),
    INDEX idx_account_code (account_code),
    INDEX idx_account_type (account_type),
    INDEX idx_parent_id (parent_id)
);

-- Tabel General Journal (Jurnal Umum)
CREATE TABLE general_journal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journal_number VARCHAR(50) UNIQUE NOT NULL,
    transaction_date DATE NOT NULL,
    description TEXT,
    reference_no VARCHAR(50),
    reference_type ENUM('INVOICE', 'PAYMENT', 'PURCHASE', 'ADJUSTMENT', 'CLOSING') DEFAULT 'INVOICE',
    total_debit DECIMAL(15,2) DEFAULT 0,
    total_credit DECIMAL(15,2) DEFAULT 0,
    status ENUM('DRAFT', 'POSTED', 'REVERSED') DEFAULT 'DRAFT',
    branch_id INT,
    created_by INT,
    approved_by INT,
    approved_at DATETIME,
    posted_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    INDEX idx_journal_number (journal_number),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_status (status),
    INDEX idx_reference_type (reference_type)
);

-- Tabel Journal Details (Detail Jurnal)
CREATE TABLE journal_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journal_id INT NOT NULL,
    account_id INT NOT NULL,
    description TEXT,
    debit_amount DECIMAL(15,2) DEFAULT 0,
    credit_amount DECIMAL(15,2) DEFAULT 0,
    cost_center_id INT,
    project_id INT,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    
    FOREIGN KEY (journal_id) REFERENCES general_journal(id) ON DELETE CASCADE,
    FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id),
    FOREIGN KEY (cost_center_id) REFERENCES cost_centers(id),
    INDEX idx_journal_details (journal_id),
    INDEX idx_account_id (account_id)
);

-- Tabel Accounts Receivable (Piutang Usaha)
CREATE TABLE accounts_receivable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    invoice_number VARCHAR(50) NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE NOT NULL,
    original_amount DECIMAL(15,2) NOT NULL,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    remaining_amount DECIMAL(15,2) GENERATED ALWAYS AS (original_amount - paid_amount) STORED,
    status ENUM('OPEN', 'PARTIAL', 'PAID', 'OVERDUE', 'BAD_DEBT') DEFAULT 'OPEN',
    payment_terms INT DEFAULT 30,
    salesperson_id INT,
    branch_id INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    INDEX idx_customer_ar (customer_id),
    INDEX idx_invoice_number (invoice_number),
    INDEX idx_due_date (due_date),
    INDEX idx_status (status)
);

-- Tabel Accounts Receivable Payments
CREATE TABLE ar_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ar_id INT NOT NULL,
    payment_date DATE NOT NULL,
    payment_amount DECIMAL(15,2) NOT NULL,
    payment_method ENUM('CASH', 'TRANSFER', 'CHECK', 'CARD', 'OTHER') DEFAULT 'CASH',
    bank_account_id INT,
    reference_no VARCHAR(50),
    description TEXT,
    received_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (ar_id) REFERENCES accounts_receivable(id) ON DELETE CASCADE,
    FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id),
    INDEX idx_ar_payments (ar_id),
    INDEX idx_payment_date (payment_date)
);

-- Tabel Accounts Payable (Hutang Usaha)
CREATE TABLE accounts_payable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    invoice_number VARCHAR(50) NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE NOT NULL,
    original_amount DECIMAL(15,2) NOT NULL,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    remaining_amount DECIMAL(15,2) GENERATED ALWAYS AS (original_amount - paid_amount) STORED,
    status ENUM('OPEN', 'PARTIAL', 'PAID', 'OVERDUE') DEFAULT 'OPEN',
    payment_terms INT DEFAULT 30,
    purchase_id INT,
    branch_id INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (supplier_id) REFERENCES orang(id_orang),
    FOREIGN KEY (purchase_id) REFERENCES purchases(id),
    INDEX idx_supplier_ap (supplier_id),
    INDEX idx_invoice_number (invoice_number),
    INDEX idx_due_date (due_date),
    INDEX idx_status (status)
);

-- Tabel Accounts Payable Payments
CREATE TABLE ap_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ap_id INT NOT NULL,
    payment_date DATE NOT NULL,
    payment_amount DECIMAL(15,2) NOT NULL,
    payment_method ENUM('CASH', 'TRANSFER', 'CHECK', 'CARD', 'OTHER') DEFAULT 'TRANSFER',
    bank_account_id INT,
    reference_no VARCHAR(50),
    description TEXT,
    approved_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (ap_id) REFERENCES accounts_payable(id) ON DELETE CASCADE,
    FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id),
    INDEX idx_ap_payments (ap_id),
    INDEX idx_payment_date (payment_date)
);

-- Tabel Bank Accounts
CREATE TABLE bank_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_name VARCHAR(150) NOT NULL,
    account_number VARCHAR(50) UNIQUE NOT NULL,
    bank_name VARCHAR(100) NOT NULL,
    bank_branch VARCHAR(100),
    account_type ENUM('CHECKING', 'SAVINGS', 'CREDIT', 'INVESTMENT') DEFAULT 'CHECKING',
    currency VARCHAR(3) DEFAULT 'IDR',
    opening_balance DECIMAL(15,2) DEFAULT 0,
    current_balance DECIMAL(15,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_account_number (account_number),
    INDEX idx_is_active (is_active)
);

-- Tabel Bank Reconciliation
CREATE TABLE bank_reconciliations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bank_account_id INT NOT NULL,
    reconciliation_date DATE NOT NULL,
    statement_balance DECIMAL(15,2) NOT NULL,
    book_balance DECIMAL(15,2) NOT NULL,
    difference_amount DECIMAL(15,2) DEFAULT 0,
    status ENUM('PENDING', 'RECONCILED', 'FAILED') DEFAULT 'PENDING',
    reconciled_by INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id),
    INDEX idx_reconciliation_date (reconciliation_date),
    INDEX idx_status (status)
);

-- Tabel Cost Centers
CREATE TABLE cost_centers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    center_code VARCHAR(20) UNIQUE NOT NULL,
    center_name VARCHAR(150) NOT NULL,
    center_type ENUM('DEPARTMENT', 'PROJECT', 'LOCATION', 'ACTIVITY') DEFAULT 'DEPARTMENT',
    manager_id INT,
    budget_amount DECIMAL(15,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_center_code (center_code),
    INDEX idx_center_type (center_type)
);

-- Tabel Tax Configuration
CREATE TABLE tax_configurations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tax_name VARCHAR(100) NOT NULL,
    tax_code VARCHAR(20) UNIQUE NOT NULL,
    tax_rate DECIMAL(5,2) NOT NULL,
    tax_type ENUM('PPN', 'PPH_21', 'PPH_22', 'PPH_23', 'PPH_4_2', 'OTHER') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    effective_date DATE NOT NULL,
    expiry_date DATE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_tax_code (tax_code),
    INDEX idx_tax_type (tax_type),
    INDEX idx_is_active (is_active)
);

-- Tabel Fixed Assets
CREATE TABLE fixed_assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_code VARCHAR(20) UNIQUE NOT NULL,
    asset_name VARCHAR(150) NOT NULL,
    asset_category ENUM('TANAH', 'BANGUNAN', 'KENDARAAN', 'MESIN', 'PERALATAN', 'IT', 'FURNITUR', 'OTHER') NOT NULL,
    purchase_date DATE NOT NULL,
    purchase_cost DECIMAL(15,2) NOT NULL,
    current_value DECIMAL(15,2) NOT NULL,
    depreciation_method ENUM('STRAIGHT_LINE', 'DECLINING_BALANCE', 'SUM_OF_YEARS_DIGITS') DEFAULT 'STRAIGHT_LINE',
    useful_life_years INT NOT NULL,
    annual_depreciation DECIMAL(15,2) GENERATED ALWAYS AS (purchase_cost / useful_life_years) STORED,
    accumulated_depreciation DECIMAL(15,2) DEFAULT 0,
    net_book_value DECIMAL(15,2) GENERATED ALWAYS AS (current_value - accumulated_depreciation) STORED,
    location VARCHAR(100),
    responsible_person_id INT,
    status ENUM('ACTIVE', 'DISPOSED', 'MAINTENANCE') DEFAULT 'ACTIVE',
    disposal_date DATE,
    disposal_value DECIMAL(15,2) DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_asset_code (asset_code),
    INDEX idx_asset_category (asset_category),
    INDEX idx_status (status)
);

-- View untuk Trial Balance
CREATE VIEW trial_balance_view AS
SELECT 
    coa.account_code,
    coa.account_name,
    coa.account_type,
    COALESCE(SUM(jd.debit_amount), 0) as total_debits,
    COALESCE(SUM(jd.credit_amount), 0) as total_credits,
    COALESCE(SUM(jd.debit_amount - jd.credit_amount), 0) as balance
FROM chart_of_accounts coa
LEFT JOIN journal_details jd ON coa.id = jd.account_id
LEFT JOIN general_journal gj ON jd.journal_id = gj.id
WHERE gj.status = 'POSTED'
GROUP BY coa.id;

-- View untuk Income Statement
CREATE VIEW income_statement_view AS
SELECT 
    coa.account_code,
    coa.account_name,
    coa.account_category,
    COALESCE(SUM(jd.credit_amount), 0) as revenue,
    COALESCE(SUM(jd.debit_amount), 0) as expenses,
    COALESCE(SUM(jd.credit_amount - jd.debit_amount), 0) as net_amount
FROM chart_of_accounts coa
LEFT JOIN journal_details jd ON coa.id = jd.account_id
LEFT JOIN general_journal gj ON jd.journal_id = gj.id
WHERE gj.status = 'POSTED' 
AND gj.transaction_date BETWEEN DATE_FORMAT(NOW(), '%Y-01-01') AND LAST_DAY(NOW())
GROUP BY coa.id
HAVING coa.account_type IN ('REVENUE', 'EXPENSE');
