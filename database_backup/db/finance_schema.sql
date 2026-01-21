-- =====================================================
-- DATABASE FINANCE - Advanced Financial Management
-- =====================================================
-- Created: 19 Januari 2026
-- Purpose: Manajemen keuangan advanced, accounting, dan financial reporting
-- Integration: Link ke aplikasi, orang, barang, surat_laporan

CREATE DATABASE IF NOT EXISTS finance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE finance;

-- =====================================================
-- 1. CHART_OF_ACCOUNTS - Perkiraan Akun
-- =====================================================
CREATE TABLE chart_of_accounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    account_code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Kode akun',
    account_name VARCHAR(200) NOT NULL COMMENT 'Nama akun',
    account_type ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
    account_category ENUM('current_asset', 'fixed_asset', 'current_liability', 'long_term_liability', 'shareholders_equity', 'operating_revenue', 'non_operating_revenue', 'operating_expense', 'non_operating_expense') NOT NULL,
    parent_account_id BIGINT UNSIGNED NULL COMMENT 'Akun induk',
    account_level TINYINT DEFAULT 1 COMMENT 'Level akun (1=highest)',
    normal_balance ENUM('debit', 'credit') NOT NULL,
    is_contra_account BOOLEAN DEFAULT FALSE COMMENT 'Akun kontra',
    is_active BOOLEAN DEFAULT TRUE,
    is_cash_equivalent BOOLEAN DEFAULT FALSE COMMENT 'Setara kas',
    is_depreciable BOOLEAN DEFAULT FALSE COMMENT 'Dapat disusutkan',
    depreciation_method ENUM('straight_line', 'declining_balance', 'sum_of_years') NULL,
    useful_life_years INT NULL COMMENT 'Masa manfaat (tahun)',
    description TEXT NULL COMMENT 'Deskripsi akun',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (parent_account_id) REFERENCES chart_of_accounts(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_account_code (account_code),
    INDEX idx_account_name (account_name),
    INDEX idx_account_type (account_type),
    INDEX idx_account_category (account_category),
    INDEX idx_parent_account_id (parent_account_id),
    INDEX idx_account_level (account_level),
    INDEX idx_is_active (is_active),
    INDEX idx_is_cash_equivalent (is_cash_equivalent)
) ENGINE=InnoDB COMMENT='Perkiraan akun lengkap';

-- =====================================================
-- 2. JOURNAL_ENTRIES - Jurnal Umum
-- =====================================================
CREATE TABLE journal_entries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    journal_number VARCHAR(30) UNIQUE NOT NULL COMMENT 'Nomor jurnal',
    journal_date DATE NOT NULL COMMENT 'Tanggal jurnal',
    journal_type ENUM('manual', 'system', 'recurring', 'adjusting', 'closing', 'opening') DEFAULT 'manual',
    reference_type VARCHAR(50) NULL COMMENT 'Tipe referensi (sale, purchase, dll)',
    reference_id BIGINT UNSIGNED NULL COMMENT 'ID referensi',
    description TEXT NOT NULL COMMENT 'Deskripsi transaksi',
    total_debit DECIMAL(15,2) NOT NULL DEFAULT 0,
    total_credit DECIMAL(15,2) NOT NULL DEFAULT 0,
    status ENUM('draft', 'posted', 'approved', 'reversed') DEFAULT 'draft',
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by BIGINT UNSIGNED NULL COMMENT 'Disetujui oleh',
    approved_at TIMESTAMP NULL COMMENT 'Waktu approval',
    reversal_journal_id BIGINT UNSIGNED NULL COMMENT 'ID jurnal pembatalan',
    is_reversal BOOLEAN DEFAULT FALSE COMMENT 'Jurnal pembatalan',
    period_id BIGINT UNSIGNED NULL COMMENT 'Link ke waktu.time_periods',
    fiscal_year SMALLINT NOT NULL COMMENT 'Tahun fiskal',
    fiscal_period ENUM('Q1', 'Q2', 'Q3', 'Q4') NOT NULL COMMENT 'Periode fiskal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (approved_by) REFERENCES orang.users(id) ON DELETE SET NULL),
    FOREIGN KEY (reversal_journal_id) REFERENCES journal_entries(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_journal_number (journal_number),
    INDEX idx_journal_date (journal_date),
    INDEX idx_journal_type (journal_type),
    INDEX idx_reference_type (reference_type),
    INDEX idx_reference_id (reference_id),
    INDEX idx_status (status),
    INDEX idx_approval_status (approval_status),
    INDEX idx_fiscal_year (fiscal_year),
    INDEX idx_fiscal_period (fiscal_period),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB COMMENT='Jurnal umum transaksi';

-- =====================================================
-- 3. JOURNAL_LINES - Detail Jurnal
-- =====================================================
CREATE TABLE journal_lines (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    journal_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke journal_entries',
    line_number INT NOT NULL COMMENT 'Nomor baris',
    account_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke chart_of_accounts',
    description VARCHAR(255) NULL COMMENT 'Deskripsi baris',
    debit_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Jumlah debit',
    credit_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Jumlah kredit',
    currency VARCHAR(3) DEFAULT 'IDR' COMMENT 'Mata uang',
    exchange_rate DECIMAL(10,6) DEFAULT 1.000000 COMMENT 'Kurs',
    cost_center_id BIGINT UNSIGNED NULL COMMENT 'Cost center',
    project_id BIGINT UNSIGNED NULL COMMENT 'Project ID',
    department_id BIGINT UNSIGNED NULL COMMENT 'Department ID',
    due_date DATE NULL COMMENT 'Jatuh tempo',
    reference_document VARCHAR(100) NULL COMMENT 'Dokumen referensi',
    tax_rate DECIMAL(5,2) DEFAULT 0 COMMENT 'Persentase pajak',
    tax_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Jumlah pajak',
    net_amount DECIMAL(15,2) GENERATED ALWAYS AS (debit_amount + credit_amount - tax_amount) STORED,
    reconciled BOOLEAN DEFAULT FALSE COMMENT 'Sudah direkonsiliasi',
    reconciliation_date DATE NULL COMMENT 'Tanggal rekonsiliasi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (journal_id) REFERENCES journal_entries(id) ON DELETE CASCADE,
    FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id) ON DELETE RESTRICT,
    
    UNIQUE KEY unique_journal_line (journal_id, line_number),
    INDEX idx_journal_id (journal_id),
    INDEX idx_account_id (account_id),
    INDEX idx_debit_amount (debit_amount),
    INDEX idx_credit_amount (credit_amount),
    INDEX idx_cost_center_id (cost_center_id),
    INDEX idx_project_id (project_id),
    INDEX idx_department_id (department_id),
    INDEX idx_due_date (due_date),
    INDEX idx_reconciled (reconciled)
) ENGINE=InnoDB COMMENT='Detail baris jurnal';

-- =====================================================
-- 4. ACCOUNTS_RECEIVABLE - Piutang Usaha
-- =====================================================
CREATE TABLE accounts_receivable (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ar_number VARCHAR(30) UNIQUE NOT NULL COMMENT 'Nomor piutang',
    customer_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke orang.persons',
    invoice_id BIGINT UNSIGNED NULL COMMENT 'Link ke surat_laporan.documents',
    transaction_date DATE NOT NULL COMMENT 'Tanggal transaksi',
    due_date DATE NOT NULL COMMENT 'Jatuh tempo',
    original_amount DECIMAL(15,2) NOT NULL COMMENT 'Jumlah asli',
    paid_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Sudah dibayar',
    remaining_amount DECIMAL(15,2) GENERATED ALWAYS AS (original_amount - paid_amount) STORED,
    overdue_days INT GENERATED ALWAYS AS (DATEDIFF(CURDATE(), due_date)) STORED,
    ar_status ENUM('current', 'due_soon', 'overdue', 'bad_debt', 'written_off') DEFAULT 'current',
    aging_bucket ENUM('0_30', '31_60', '61_90', '91_120', '120_plus') NULL COMMENT 'Bucket aging',
    credit_limit DECIMAL(15,2) DEFAULT 0 COMMENT 'Limit kredit',
    terms_days INT DEFAULT 0 COMMENT 'Syarat pembayaran (hari)',
    sales_person_id BIGINT UNSIGNED NULL COMMENT 'Sales person',
    collection_notes TEXT NULL COMMENT 'Catatan penagihan',
    last_payment_date DATE NULL COMMENT 'Terakhir pembayaran',
    last_contact_date DATE NULL COMMENT 'Terakhir kontak',
    is_disputed BOOLEAN DEFAULT FALSE COMMENT 'Disputasi',
    dispute_reason TEXT NULL COMMENT 'Alasan disputasi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (customer_id) REFERENCES orang.persons(id) ON DELETE RESTRICT,
    FOREIGN KEY (invoice_id) REFERENCES surat_laporan.documents(id) ON DELETE SET NULL,
    FOREIGN KEY (sales_person_id) REFERENCES orang.users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_ar_number (ar_number),
    INDEX idx_customer_id (customer_id),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_due_date (due_date),
    INDEX idx_original_amount (original_amount),
    INDEX idx_remaining_amount (remaining_amount),
    INDEX idx_overdue_days (overdue_days),
    INDEX idx_ar_status (ar_status),
    INDEX idx_aging_bucket (aging_bucket)
) ENGINE=InnoDB COMMENT='Piutang usaha';

-- =====================================================
-- 5. ACCOUNTS_PAYABLE - Hutang Usaha
-- =====================================================
CREATE TABLE accounts_payable (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ap_number VARCHAR(30) UNIQUE NOT NULL COMMENT 'Nomor hutang',
    supplier_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke barang.suppliers',
    bill_id BIGINT UNSIGNED NULL COMMENT 'Link ke surat_laporan.documents',
    transaction_date DATE NOT NULL COMMENT 'Tanggal transaksi',
    due_date DATE NOT NULL COMMENT 'Jatuh tempo',
    original_amount DECIMAL(15,2) NOT NULL COMMENT 'Jumlah asli',
    paid_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Sudah dibayar',
    remaining_amount DECIMAL(15,2) GENERATED ALWAYS AS (original_amount - paid_amount) STORED,
    overdue_days INT GENERATED ALWAYS AS (DATEDIFF(CURDATE(), due_date)) STORED,
    ap_status ENUM('current', 'due_soon', 'overdue', 'paid', 'written_off') DEFAULT 'current',
    aging_bucket ENUM('0_30', '31_60', '61_90', '91_120', '120_plus') NULL COMMENT 'Bucket aging',
    payment_terms VARCHAR(100) NULL COMMENT 'Syarat pembayaran',
    discount_terms VARCHAR(100) NULL COMMENT 'Syarat diskon',
    discount_available_until DATE NULL COMMENT 'Diskon tersedia sampai',
    discount_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Jumlah diskon',
    purchase_order_id BIGINT UNSIGNED NULL COMMENT 'Link ke aplikasi.purchases',
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by BIGINT UNSIGNED NULL COMMENT 'Disetujui oleh',
    approved_at TIMESTAMP NULL COMMENT 'Waktu approval',
    payment_notes TEXT NULL COMMENT 'Catatan pembayaran',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (supplier_id) REFERENCES barang.suppliers(id) ON DELETE RESTRICT,
    FOREIGN KEY (bill_id) REFERENCES surat_laporan.documents(id) ON DELETE SET NULL,
    FOREIGN KEY (purchase_order_id) REFERENCES aplikasi.purchases(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_ap_number (ap_number),
    INDEX idx_supplier_id (supplier_id),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_due_date (due_date),
    INDEX idx_original_amount (original_amount),
    INDEX idx_remaining_amount (remaining_amount),
    INDEX idx_overdue_days (overdue_days),
    INDEX idx_ap_status (ap_status),
    INDEX idx_aging_bucket (aging_bucket),
    INDEX idx_approval_status (approval_status)
) ENGINE=InnoDB COMMENT='Hutang usaha';

-- =====================================================
-- 6. BANK_ACCOUNTS - Rekening Bank
-- =====================================================
CREATE TABLE bank_accounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    account_number VARCHAR(50) UNIQUE NOT NULL COMMENT 'Nomor rekening',
    account_name VARCHAR(100) NOT NULL COMMENT 'Nama rekening',
    bank_name VARCHAR(100) NOT NULL COMMENT 'Nama bank',
    bank_code VARCHAR(10) NULL COMMENT 'Kode bank',
    branch_name VARCHAR(100) NULL COMMENT 'Nama cabang',
    account_type ENUM('checking', 'savings', 'current', 'credit_card', 'investment') DEFAULT 'checking',
    currency VARCHAR(3) DEFAULT 'IDR' COMMENT 'Mata uang',
    opening_balance DECIMAL(15,2) DEFAULT 0 COMMENT 'Saldo awal',
    current_balance DECIMAL(15,2) DEFAULT 0 COMMENT 'Saldo saat ini',
    available_balance DECIMAL(15,2) DEFAULT 0 COMMENT 'Saldo tersedia',
    overdraft_limit DECIMAL(15,2) DEFAULT 0 COMMENT 'Limit overdraft',
    interest_rate DECIMAL(5,2) DEFAULT 0 COMMENT 'Suku bunga',
    is_active BOOLEAN DEFAULT TRUE,
    is_primary BOOLEAN DEFAULT FALSE COMMENT 'Rekening utama',
    last_reconciliation_date DATE NULL COMMENT 'Terakhir rekonsiliasi',
    reconciliation_status ENUM('reconciled', 'unreconciled', 'in_progress') DEFAULT 'unreconciled',
    account_holder VARCHAR(100) NULL COMMENT 'Nama pemegang rekening',
    notes TEXT NULL COMMENT 'Catatan rekening',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_account_number (account_number),
    INDEX idx_account_name (account_name),
    INDEX idx_bank_name (bank_name),
    INDEX idx_bank_code (bank_code),
    INDEX idx_account_type (account_type),
    INDEX idx_currency (currency),
    INDEX idx_current_balance (current_balance),
    INDEX idx_is_active (is_active),
    INDEX idx_is_primary (is_primary)
) ENGINE=InnoDB COMMENT='Master rekening bank';

-- =====================================================
-- 7. CASH_FLOW - Arus Kas
-- =====================================================
CREATE TABLE cash_flow (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_date DATE NOT NULL COMMENT 'Tanggal transaksi',
    flow_type ENUM('operating', 'investing', 'financing') NOT NULL COMMENT 'Tipe arus kas',
    cash_category ENUM('cash_receipts', 'cash_payments', 'short_term_investments', 'long_term_investments', 'borrowings', 'repayments', 'equity', 'dividends') NOT NULL,
    reference_type VARCHAR(50) NULL COMMENT 'Tipe referensi',
    reference_id BIGINT UNSIGNED NULL COMMENT 'ID referensi',
    description VARCHAR(255) NOT NULL COMMENT 'Deskripsi transaksi',
    cash_in DECIMAL(15,2) DEFAULT 0 COMMENT 'Masuk kas',
    cash_out DECIMAL(15,2) DEFAULT 0 COMMENT 'Keluar kas',
    net_cash DECIMAL(15,2) GENERATED ALWAYS AS (cash_in - cash_out) STORED,
    bank_account_id BIGINT UNSIGNED NULL COMMENT 'Link ke bank_accounts',
    currency VARCHAR(3) DEFAULT 'IDR' COMMENT 'Mata uang',
    exchange_rate DECIMAL(10,6) DEFAULT 1.000000 COMMENT 'Kurs',
    is_budget BOOLEAN DEFAULT FALSE COMMENT 'Transaksi budget',
    period_id BIGINT UNSIGNED NULL COMMENT 'Link ke waktu.time_periods',
    fiscal_year SMALLINT NOT NULL COMMENT 'Tahun fiskal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_flow_type (flow_type),
    INDEX idx_cash_category (cash_category),
    INDEX idx_reference_type (reference_type),
    INDEX idx_reference_id (reference_id),
    INDEX idx_cash_in (cash_in),
    INDEX idx_cash_out (cash_out),
    INDEX idx_net_cash (net_cash),
    INDEX idx_bank_account_id (bank_account_id),
    INDEX idx_fiscal_year (fiscal_year),
    INDEX idx_is_budget (is_budget)
) ENGINE=InnoDB COMMENT='Arus kas';

-- =====================================================
-- 8. BUDGET_PLANNING - Perencanaan Budget
-- =====================================================
CREATE TABLE budget_planning (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    budget_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode budget',
    budget_name VARCHAR(100) NOT NULL COMMENT 'Nama budget',
    budget_type ENUM('operating', 'capital', 'cash_flow', 'departmental', 'project') NOT NULL,
    account_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke chart_of_accounts',
    department_id BIGINT UNSIGNED NULL COMMENT 'Department ID',
    project_id BIGINT UNSIGNED NULL COMMENT 'Project ID',
    cost_center_id BIGINT UNSIGNED NULL COMMENT 'Cost center',
    fiscal_year SMALLINT NOT NULL COMMENT 'Tahun fiskal',
    fiscal_period ENUM('Q1', 'Q2', 'Q3', 'Q4', 'annual') NOT NULL COMMENT 'Periode fiskal',
    budget_amount DECIMAL(15,2) NOT NULL COMMENT 'Jumlah budget',
    actual_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Jumlah aktual',
    variance_amount DECIMAL(15,2) GENERATED ALWAYS AS (actual_amount - budget_amount) STORED,
    variance_percent DECIMAL(5,2) GENERATED ALWAYS AS (CASE WHEN budget_amount != 0 THEN ((actual_amount - budget_amount) / budget_amount * 100) ELSE 0 END) STORED,
    budget_status ENUM('draft', 'approved', 'active', 'closed', 'cancelled') DEFAULT 'draft',
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by BIGINT UNSIGNED NULL COMMENT 'Disetujui oleh',
    approved_at TIMESTAMP NULL COMMENT 'Waktu approval',
    notes TEXT NULL COMMENT 'Catatan budget',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_budget_code (budget_code),
    INDEX idx_budget_name (budget_name),
    INDEX idx_budget_type (budget_type),
    INDEX idx_account_id (account_id),
    INDEX idx_department_id (department_id),
    INDEX idx_project_id (project_id),
    INDEX idx_fiscal_year (fiscal_year),
    INDEX idx_fiscal_period (fiscal_period),
    INDEX idx_budget_amount (budget_amount),
    INDEX idx_actual_amount (actual_amount),
    INDEX idx_variance_percent (variance_percent),
    INDEX idx_budget_status (budget_status),
    INDEX idx_approval_status (approval_status)
) ENGINE=InnoDB COMMENT='Perencanaan budget';

-- =====================================================
-- 9. EXPENSE_MANAGEMENT - Manajemen Biaya
-- =====================================================
CREATE TABLE expense_management (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expense_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode expense',
    expense_date DATE NOT NULL COMMENT 'Tanggal expense',
    expense_category ENUM('office', 'travel', 'marketing', 'maintenance', 'utilities', 'rent', 'salary', 'training', 'other') NOT NULL,
    expense_type ENUM('reimbursable', 'non_reimbursable', 'advance', 'claim') DEFAULT 'reimbursable',
    employee_id BIGINT UNSIGNED NULL COMMENT 'Employee (link ke orang.persons)',
    department_id BIGINT UNSIGNED NULL COMMENT 'Department',
    project_id BIGINT UNSIGNED NULL COMMENT 'Project',
    cost_center_id BIGINT UNSIGNED NULL COMMENT 'Cost center',
    description VARCHAR(255) NOT NULL COMMENT 'Deskripsi expense',
    amount DECIMAL(15,2) NOT NULL COMMENT 'Jumlah expense',
    currency VARCHAR(3) DEFAULT 'IDR' COMMENT 'Mata uang',
    exchange_rate DECIMAL(10,6) DEFAULT 1.000000 COMMENT 'Kurs',
    tax_amount DECIMAL(15,2) DEFAULT 0 COMMENT 'Jumlah pajak',
    total_amount DECIMAL(15,2) GENERATED ALWAYS AS (amount + tax_amount) STORED,
    receipt_number VARCHAR(50) NULL COMMENT 'Nomor receipt',
    receipt_date DATE NULL COMMENT 'Tanggal receipt',
    receipt_image_url VARCHAR(500) NULL COMMENT 'URL gambar receipt',
    payment_method ENUM('cash', 'card', 'transfer', 'company_account', 'personal') DEFAULT 'cash',
    status ENUM('draft', 'submitted', 'approved', 'rejected', 'paid', 'reimbursed') DEFAULT 'draft',
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by BIGINT UNSIGNED NULL COMMENT 'Disetujui oleh',
    approved_at TIMESTAMP NULL COMMENT 'Waktu approval',
    paid_date DATE NULL COMMENT 'Tanggal dibayar',
    reimbursed_date DATE NULL COMMENT 'Tanggal direimburse',
    notes TEXT NULL COMMENT 'Catatan expense',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (employee_id) REFERENCES orang.persons(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_expense_code (expense_code),
    INDEX idx_expense_date (expense_date),
    INDEX idx_expense_category (expense_category),
    INDEX idx_expense_type (expense_type),
    INDEX idx_employee_id (employee_id),
    INDEX idx_department_id (department_id),
    INDEX idx_project_id (project_id),
    INDEX idx_amount (amount),
    INDEX idx_total_amount (total_amount),
    INDEX idx_status (status),
    INDEX idx_approval_status (approval_status)
) ENGINE=InnoDB COMMENT='Manajemen biaya operasional';

-- =====================================================
-- 10. TAX_MANAGEMENT - Manajemen Pajak
-- =====================================================
CREATE TABLE tax_management (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tax_code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Kode pajak',
    tax_name VARCHAR(100) NOT NULL COMMENT 'Nama pajak',
    tax_type ENUM('income_tax', 'vat', 'withholding_tax', 'luxury_tax', 'other') NOT NULL,
    tax_category ENUM('government', 'regional', 'local', 'other') DEFAULT 'government',
    tax_rate DECIMAL(5,2) NOT NULL COMMENT 'Persentase pajak',
    tax_base ENUM('gross', 'net', 'specific') DEFAULT 'gross',
    calculation_method ENUM('percentage', 'fixed', 'tiered', 'progressive') DEFAULT 'percentage',
    effective_date DATE NOT NULL COMMENT 'Tanggal efektif',
    expiry_date DATE NULL COMMENT 'Tanggal kadaluarsa',
    tax_account_id BIGINT UNSIGNED NULL COMMENT 'Akun pajak (link ke chart_of_accounts)',
    payable_account_id BIGINT UNSIGNED NULL COMMENT 'Akun hutang pajak',
    is_active BOOLEAN DEFAULT TRUE,
    description TEXT NULL COMMENT 'Deskripsi pajak',
    reporting_frequency ENUM('monthly', 'quarterly', 'annually') DEFAULT 'monthly',
    due_day INT DEFAULT 15 COMMENT 'Tanggal jatuh tempo pelaporan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (tax_account_id) REFERENCES chart_of_accounts(id) ON DELETE SET NULL,
    FOREIGN KEY (payable_account_id) REFERENCES chart_of_accounts(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_tax_code (tax_code),
    INDEX idx_tax_name (tax_name),
    INDEX idx_tax_type (tax_type),
    INDEX idx_tax_category (tax_category),
    INDEX idx_tax_rate (tax_rate),
    INDEX idx_effective_date (effective_date),
    INDEX idx_expiry_date (expiry_date),
    INDEX idx_is_active (is_active),
    INDEX idx_reporting_frequency (reporting_frequency)
) ENGINE=InnoDB COMMENT='Master data pajak';

-- =====================================================
-- 11. FINANCIAL_REPORTS - Laporan Keuangan
-- =====================================================
CREATE TABLE financial_reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    report_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode laporan',
    report_name VARCHAR(100) NOT NULL COMMENT 'Nama laporan',
    report_type ENUM('balance_sheet', 'income_statement', 'cash_flow', 'trial_balance', 'general_ledger', 'aged_receivables', 'aged_payables', 'budget_vs_actual') NOT NULL,
    report_period ENUM('daily', 'weekly', 'monthly', 'quarterly', 'yearly', 'custom') NOT NULL,
    fiscal_year SMALLINT NOT NULL COMMENT 'Tahun fiskal',
    fiscal_period ENUM('Q1', 'Q2', 'Q3', 'Q4', 'annual') NULL COMMENT 'Periode fiskal',
    start_date DATE NOT NULL COMMENT 'Tanggal mulai',
    end_date DATE NOT NULL COMMENT 'Tanggal selesai',
    report_status ENUM('draft', 'generating', 'completed', 'failed') DEFAULT 'draft',
    report_format ENUM('html', 'pdf', 'excel', 'json') DEFAULT 'html',
    file_path VARCHAR(500) NULL COMMENT 'Path file laporan',
    file_url VARCHAR(500) NULL COMMENT 'URL file laporan',
    file_size BIGINT NULL COMMENT 'Ukuran file',
    generated_at TIMESTAMP NULL COMMENT 'Waktu generate',
    generated_by BIGINT UNSIGNED NULL COMMENT 'Siapa yang generate',
    notes TEXT NULL COMMENT 'Catatan laporan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_report_code (report_code),
    INDEX idx_report_name (report_name),
    INDEX idx_report_type (report_type),
    INDEX idx_report_period (report_period),
    INDEX idx_fiscal_year (fiscal_year),
    INDEX idx_fiscal_period (fiscal_period),
    INDEX idx_start_date (start_date),
    INDEX idx_end_date (end_date),
    INDEX idx_report_status (report_status),
    INDEX idx_generated_at (generated_at),
    INDEX idx_generated_by (generated_by)
) ENGINE=InnoDB COMMENT='Laporan keuangan';

-- =====================================================
-- INSERT DEFAULT CHART OF ACCOUNTS
-- =====================================================

-- Assets
INSERT INTO chart_of_accounts (account_code, account_name, account_type, account_category, normal_balance, is_cash_equivalent) VALUES
('1100', 'KAS DAN SETARA KAS', 'asset', 'current_asset', 'debit', TRUE),
('1101', 'KAS', 'asset', 'current_asset', 'debit', TRUE),
('1102', 'BANK', 'asset', 'current_asset', 'debit', TRUE),
('1103', 'PIUTANG USAHA', 'asset', 'current_asset', 'debit', FALSE),
('1104', 'PIUTANG LAIN-LAIN', 'asset', 'current_asset', 'debit', FALSE),
('1200', 'PERSEDIAAN', 'asset', 'current_asset', 'debit', FALSE),
('1201', 'BARANG DAGANG', 'asset', 'current_asset', 'debit', FALSE),
('1202', 'BAHAN BAKU', 'asset', 'current_asset', 'debit', FALSE),
('1300', 'AKTIVA TETAP', 'asset', 'fixed_asset', 'debit', FALSE),
('1301', 'TANAH', 'asset', 'fixed_asset', 'debit', FALSE),
('1302', 'GEDUNG', 'asset', 'fixed_asset', 'debit', FALSE),
('1303', 'KENDARAAN', 'asset', 'fixed_asset', 'debit', FALSE),
('1304', 'PERALATAN', 'asset', 'fixed_asset', 'debit', FALSE),
('1305', 'AKUMULASI PENYUSUTAN', 'asset', 'fixed_asset', 'credit', FALSE);

-- Liabilities
INSERT INTO chart_of_accounts (account_code, account_name, account_type, account_category, normal_balance) VALUES
('2100', 'HUTANG USAHA', 'liability', 'current_liability', 'credit'),
('2101', 'HUTANG DAGANG', 'liability', 'current_liability', 'credit'),
('2102', 'HUTANG PAJAK', 'liability', 'current_liability', 'credit'),
('2103', 'HUTANG GAJI', 'liability', 'current_liability', 'credit'),
('2200', 'HUTANG JANGKA PANJANG', 'liability', 'long_term_liability', 'credit'),
('2201', 'HUTANG BANK', 'liability', 'long_term_liability', 'credit');

-- Equity
INSERT INTO chart_of_accounts (account_code, account_name, account_type, account_category, normal_balance) VALUES
('3100', 'MODAL', 'equity', 'shareholders_equity', 'credit'),
('3101', 'MODAL DISETOR', 'equity', 'shareholders_equity', 'credit'),
('3200', 'LABA DITAHAN', 'equity', 'shareholders_equity', 'credit');

-- Revenue
INSERT INTO chart_of_accounts (account_code, account_name, account_type, account_category, normal_balance) VALUES
('4100', 'PENDAPATAN USAHA', 'revenue', 'operating_revenue', 'credit'),
('4101', 'PENJUALAN BARANG', 'revenue', 'operating_revenue', 'credit'),
('4102', 'PENJUALAN JASA', 'revenue', 'operating_revenue', 'credit'),
('4200', 'PENDAPATAN LAIN-LAIN', 'revenue', 'non_operating_revenue', 'credit');

-- Expenses
INSERT INTO chart_of_accounts (account_code, account_name, account_type, account_category, normal_balance) VALUES
('5100', 'HARGA POKOK PENJUALAN', 'expense', 'operating_expense', 'debit'),
('5200', 'BIAYA OPERASIONAL', 'expense', 'operating_expense', 'debit'),
('5201', 'GAJI DAN UPAH', 'expense', 'operating_expense', 'debit'),
('5202', 'SEWA', 'expense', 'operating_expense', 'debit'),
('5203', 'LISTRIK DAN AIR', 'expense', 'operating_expense', 'debit'),
('5204', 'TELEPON DAN INTERNET', 'expense', 'operating_expense', 'debit'),
('5205', 'BIAYA MARKETING', 'expense', 'operating_expense', 'debit'),
('5206', 'BIAYA TRANSPORT', 'expense', 'operating_expense', 'debit'),
('5300', 'BIAYA LAIN-LAIN', 'expense', 'non_operating_expense', 'debit'),
('5301', 'BIAYA BANK', 'expense', 'non_operating_expense', 'debit');

-- Default Tax Management
INSERT INTO tax_management (tax_code, tax_name, tax_type, tax_rate, effective_date, is_active) VALUES
('PPN', 'Pajak Pertambahan Nilai', 'vat', 11.00, '2022-04-01', TRUE),
('PPH21', 'PPh Pasal 21', 'income_tax', 5.00, '2022-01-01', TRUE),
('PPH22', 'PPh Pasal 22', 'withholding_tax', 1.50, '2022-01-01', TRUE),
('PPH23', 'PPh Pasal 23', 'withholding_tax', 2.00, '2022-01-01', TRUE);

-- =====================================================
-- VIEWS untuk financial reporting
-- =====================================================

-- View untuk Trial Balance
CREATE VIEW v_trial_balance AS
SELECT 
    ca.account_code,
    ca.account_name,
    ca.account_type,
    ca.account_category,
    ca.normal_balance,
    COALESCE(SUM(jl.debit_amount), 0) as total_debit,
    COALESCE(SUM(jl.credit_amount), 0) as total_credit,
    CASE 
        WHEN ca.normal_balance = 'debit' THEN 
            COALESCE(SUM(jl.debit_amount), 0) - COALESCE(SUM(jl.credit_amount), 0)
        ELSE 
            COALESCE(SUM(jl.credit_amount), 0) - COALESCE(SUM(jl.debit_amount), 0)
    END as balance
FROM chart_of_accounts ca
LEFT JOIN journal_lines jl ON ca.id = jl.account_id
LEFT JOIN journal_entries je ON jl.journal_id = je.id
WHERE ca.is_active = TRUE 
  AND je.status = 'posted'
GROUP BY ca.id
HAVING balance != 0
ORDER BY ca.account_code;

-- View untuk Aging Receivables
CREATE VIEW v_aging_receivables AS
SELECT 
    ar.customer_id,
    p.full_name as customer_name,
    COUNT(*) as total_invoices,
    SUM(ar.original_amount) as total_original,
    SUM(ar.paid_amount) as total_paid,
    SUM(ar.remaining_amount) as total_remaining,
    SUM(CASE WHEN ar.overdue_days BETWEEN 0 AND 30 THEN ar.remaining_amount ELSE 0 END) as bucket_0_30,
    SUM(CASE WHEN ar.overdue_days BETWEEN 31 AND 60 THEN ar.remaining_amount ELSE 0 END) as bucket_31_60,
    SUM(CASE WHEN ar.overdue_days BETWEEN 61 AND 90 THEN ar.remaining_amount ELSE 0 END) as bucket_61_90,
    SUM(CASE WHEN ar.overdue_days BETWEEN 91 AND 120 THEN ar.remaining_amount ELSE 0 END) as bucket_91_120,
    SUM(CASE WHEN ar.overdue_days > 120 THEN ar.remaining_amount ELSE 0 END) as bucket_120_plus
FROM accounts_receivable ar
JOIN orang.persons p ON ar.customer_id = p.id
WHERE ar.remaining_amount > 0
GROUP BY ar.customer_id
ORDER BY total_remaining DESC;
