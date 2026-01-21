-- =====================================================
-- DATABASE WAKTU - Time & Transaction Management
-- =====================================================
-- Created: 19 Januari 2026
-- Purpose: Manajemen waktu, scheduling, dan tracking temporal
-- Integration: Link ke semua database untuk temporal data

CREATE DATABASE IF NOT EXISTS waktu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE waktu;

-- =====================================================
-- 1. TIME_PERIODS - Periode Waktu
-- =====================================================
CREATE TABLE time_periods (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    period_type ENUM('day', 'week', 'month', 'quarter', 'year', 'custom') NOT NULL,
    period_name VARCHAR(100) NOT NULL COMMENT 'Nama periode (contoh: Januari 2026)',
    period_code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Kode periode (contoh: 202601)',
    start_date DATE NOT NULL COMMENT 'Tanggal mulai',
    end_date DATE NOT NULL COMMENT 'Tanggal selesai',
    parent_period_id BIGINT UNSIGNED NULL COMMENT 'Link ke periode induk',
    is_current BOOLEAN DEFAULT FALSE COMMENT 'Periode saat ini',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_period_id) REFERENCES time_periods(id) ON DELETE SET NULL,
    
    INDEX idx_period_type (period_type),
    INDEX idx_period_code (period_code),
    INDEX idx_start_date (start_date),
    INDEX idx_end_date (end_date),
    INDEX idx_is_current (is_current),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Periode-periode waktu untuk reporting';

-- =====================================================
-- 2. BUSINESS_HOURS - Jam Operasional
-- =====================================================
CREATE TABLE business_hours (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    day_of_week TINYINT NOT NULL COMMENT 'Hari (1=Senin, 7=Minggu)',
    opening_time TIME NOT NULL COMMENT 'Jam buka',
    closing_time TIME NOT NULL COMMENT 'Jam tutup',
    break_start_time TIME NULL COMMENT 'Waktu istirahat mulai',
    break_end_time TIME NULL COMMENT 'Waktu istirahat selesai',
    is_working_day BOOLEAN DEFAULT TRUE COMMENT 'Hari kerja',
    description VARCHAR(255) NULL COMMENT 'Deskripsi',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_day (day_of_week),
    INDEX idx_day_of_week (day_of_week),
    INDEX idx_is_working_day (is_working_day),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Jam operasional bisnis';

-- =====================================================
-- 3. HOLIDAYS - Hari Libur
-- =====================================================
CREATE TABLE holidays (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Nama hari libur',
    date DATE NOT NULL COMMENT 'Tanggal libur',
    holiday_type ENUM('national', 'religious', 'company', 'custom') NOT NULL,
    is_recurring BOOLEAN DEFAULT FALSE COMMENT 'Berulang setiap tahun',
    description TEXT NULL COMMENT 'Deskripsi libur',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_date (date),
    INDEX idx_holiday_type (holiday_type),
    INDEX idx_is_recurring (is_recurring),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Daftar hari libur';

-- =====================================================
-- 4. WORK_SCHEDULES - Jadwal Kerja
-- =====================================================
CREATE TABLE work_schedules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL COMMENT 'ID karyawan (link ke orang.persons)',
    schedule_type ENUM('regular', 'shift', 'flexible', 'remote') DEFAULT 'regular',
    shift_name VARCHAR(50) NULL COMMENT 'Nama shift',
    start_date DATE NOT NULL COMMENT 'Tanggal mulai jadwal',
    end_date DATE NULL COMMENT 'Tanggal selesai jadwal',
    monday_schedule JSON NULL COMMENT 'Jadwal Senin',
    tuesday_schedule JSON NULL COMMENT 'Jadwal Selasa',
    wednesday_schedule JSON NULL COMMENT 'Jadwal Rabu',
    thursday_schedule JSON NULL COMMENT 'Jadwal Kamis',
    friday_schedule JSON NULL COMMENT 'Jadwal Jumat',
    saturday_schedule JSON NULL COMMENT 'Jadwal Sabtu',
    sunday_schedule JSON NULL COMMENT 'Jadwal Minggu',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (employee_id) REFERENCES orang.persons(id) ON DELETE CASCADE,
    
    INDEX idx_employee_id (employee_id),
    INDEX idx_schedule_type (schedule_type),
    INDEX idx_start_date (start_date),
    INDEX idx_end_date (end_date),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Jadwal kerja karyawan';

-- =====================================================
-- 5. DELIVERY_SCHEDULES - Jadwal Pengiriman
-- =====================================================
CREATE TABLE delivery_schedules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    schedule_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode jadwal',
    route_name VARCHAR(100) NOT NULL COMMENT 'Nama rute',
    description TEXT NULL COMMENT 'Deskripsi rute',
    delivery_type ENUM('regular', 'express', 'same_day', 'scheduled') DEFAULT 'regular',
    frequency_type ENUM('daily', 'weekly', 'monthly', 'custom') DEFAULT 'daily',
    frequency_value TINYINT NULL COMMENT 'Nilai frekuensi (contoh: 2 untuk 2 minggu sekali)',
    delivery_days JSON NULL COMMENT 'Hari pengiriman [1,2,3,4,5]',
    estimated_duration TIME NULL COMMENT 'Estimasi durasi pengiriman',
    max_orders_per_day INT DEFAULT 0 COMMENT 'Maksimum order per hari',
    warehouse_id BIGINT UNSIGNED NULL COMMENT 'Gudang asal (link ke barang.warehouses)',
    driver_id BIGINT UNSIGNED NULL COMMENT 'Driver (link ke orang.persons)',
    vehicle_id VARCHAR(50) NULL COMMENT 'ID kendaraan',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_schedule_code (schedule_code),
    INDEX idx_route_name (route_name),
    INDEX idx_delivery_type (delivery_type),
    INDEX idx_frequency_type (frequency_type),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_driver_id (driver_id),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Jadwal pengiriman rutin';

-- =====================================================
-- 6. DELIVERY_SCHEDULE_ITEMS - Item Jadwal Pengiriman
-- =====================================================
CREATE TABLE delivery_schedule_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    schedule_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL COMMENT 'Customer (link ke orang.persons)',
    village_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.villages',
    district_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.districts',
    regency_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.regencies',
    province_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.provinces',
    address VARCHAR(255) NOT NULL COMMENT 'Alamat pengiriman',
    postal_code VARCHAR(10) NULL COMMENT 'Kode pos',
    contact_person VARCHAR(100) NULL COMMENT 'Nama kontak',
    contact_phone VARCHAR(20) NULL COMMENT 'Telepon kontak',
    latitude DECIMAL(10,8) NULL COMMENT 'Koordinat latitude',
    longitude DECIMAL(11,8) NULL COMMENT 'Koordinat longitude',
    sequence_order INT DEFAULT 0 COMMENT 'Urutan pengiriman',
    estimated_arrival TIME NULL COMMENT 'Estimasi waktu tiba',
    special_instructions TEXT NULL COMMENT 'Instruksi khusus',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (schedule_id) REFERENCES delivery_schedules(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES orang.persons(id) ON DELETE CASCADE,
    
    INDEX idx_schedule_id (schedule_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_village_id (village_id),
    INDEX idx_district_id (district_id),
    INDEX idx_regency_id (regency_id),
    INDEX idx_province_id (province_id),
    INDEX idx_sequence_order (sequence_order),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Item-item dalam jadwal pengiriman';

-- =====================================================
-- 7. PRODUCTION_SCHEDULES - Jadwal Produksi
-- =====================================================
CREATE TABLE production_schedules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    schedule_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode jadwal produksi',
    product_id BIGINT UNSIGNED NOT NULL COMMENT 'Produk (link ke barang.products)',
    variant_id BIGINT UNSIGNED NULL COMMENT 'Variant produk',
    warehouse_id BIGINT UNSIGNED NOT NULL COMMENT 'Gudang produksi',
    production_type ENUM('manufacturing', 'assembly', 'packaging', 'processing') DEFAULT 'manufacturing',
    planned_quantity DECIMAL(12,2) NOT NULL COMMENT 'Quantity rencana',
    unit_id BIGINT UNSIGNED NOT NULL COMMENT 'Satuan produksi',
    start_datetime DATETIME NOT NULL COMMENT 'Waktu mulai produksi',
    end_datetime DATETIME NOT NULL COMMENT 'Waktu selesai produksi',
    estimated_duration INT DEFAULT 0 COMMENT 'Estimasi durasi (menit)',
    priority_level TINYINT DEFAULT 3 COMMENT 'Prioritas (1=highest, 5=lowest)',
    status ENUM('planned', 'in_progress', 'completed', 'cancelled', 'delayed') DEFAULT 'planned',
    actual_quantity DECIMAL(12,2) NULL COMMENT 'Quantity aktual',
    actual_start_datetime DATETIME NULL COMMENT 'Waktu mulai aktual',
    actual_end_datetime DATETIME NULL COMMENT 'Waktu selesai aktual',
    notes TEXT NULL COMMENT 'Catatan produksi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (product_id) REFERENCES barang.products(id) ON DELETE RESTRICT,
    FOREIGN KEY (variant_id) REFERENCES barang.product_variants(id) ON DELETE SET NULL,
    FOREIGN KEY (warehouse_id) REFERENCES barang.warehouses(id) ON DELETE RESTRICT,
    FOREIGN KEY (unit_id) REFERENCES barang.units(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_schedule_code (schedule_code),
    INDEX idx_product_id (product_id),
    INDEX idx_variant_id (variant_id),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_start_datetime (start_datetime),
    INDEX idx_end_datetime (end_datetime),
    INDEX idx_status (status),
    INDEX idx_priority_level (priority_level)
) ENGINE=InnoDB COMMENT='Jadwal produksi';

-- =====================================================
-- 8. TRANSACTION_DATES - Tracking Tanggal Transaksi
-- =====================================================
CREATE TABLE transaction_dates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_type ENUM('sale', 'purchase', 'transfer', 'adjustment', 'payment', 'return') NOT NULL,
    transaction_id BIGINT UNSIGNED NOT NULL COMMENT 'ID transaksi',
    transaction_date DATE NOT NULL COMMENT 'Tanggal transaksi',
    transaction_time TIME NOT NULL COMMENT 'Waktu transaksi',
    period_id BIGINT UNSIGNED NULL COMMENT 'Link ke time_periods',
    fiscal_year SMALLINT NOT NULL COMMENT 'Tahun fiskal',
    fiscal_quarter TINYINT NOT NULL COMMENT 'Kuartal fiskal',
    fiscal_month TINYINT NOT NULL COMMENT 'Bulan fiskal',
    fiscal_week TINYINT NULL COMMENT 'Minggu fiskal',
    is_business_day BOOLEAN DEFAULT TRUE COMMENT 'Hari kerja',
    is_holiday BOOLEAN DEFAULT FALSE COMMENT 'Hari libur',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_transaction_type (transaction_type),
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_period_id (period_id),
    INDEX idx_fiscal_year (fiscal_year),
    INDEX idx_fiscal_quarter (fiscal_quarter),
    INDEX idx_fiscal_month (fiscal_month),
    INDEX idx_is_business_day (is_business_day),
    INDEX idx_is_holiday (is_holiday)
) ENGINE=InnoDB COMMENT='Tracking temporal untuk semua transaksi';

-- =====================================================
-- 9. TIME_TRACKING - Tracking Waktu Kerja
-- =====================================================
CREATE TABLE time_tracking (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL COMMENT 'Karyawan (link ke orang.persons)',
    work_date DATE NOT NULL COMMENT 'Tanggal kerja',
    clock_in TIME NULL COMMENT 'Jam masuk',
    clock_out TIME NULL COMMENT 'Jam keluar',
    break_start TIME NULL COMMENT 'Mulai istirahat',
    break_end TIME NULL COMMENT 'Selesai istirahat',
    total_work_minutes INT DEFAULT 0 COMMENT 'Total menit kerja',
    total_break_minutes INT DEFAULT 0 COMMENT 'Total menit istirahat',
    overtime_minutes INT DEFAULT 0 COMMENT 'Menit lembur',
    work_type ENUM('regular', 'overtime', 'holiday', 'weekend') DEFAULT 'regular',
    status ENUM('present', 'absent', 'late', 'early_leave', 'holiday', 'leave') DEFAULT 'present',
    notes VARCHAR(255) NULL COMMENT 'Catatan',
    approved_by BIGINT UNSIGNED NULL COMMENT 'Disetujui oleh',
    approved_at TIMESTAMP NULL COMMENT 'Waktu approval',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (employee_id) REFERENCES orang.persons(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_employee_date (employee_id, work_date),
    INDEX idx_employee_id (employee_id),
    INDEX idx_work_date (work_date),
    INDEX idx_work_type (work_type),
    INDEX idx_status (status),
    INDEX idx_total_work_minutes (total_work_minutes),
    INDEX idx_overtime_minutes (overtime_minutes)
) ENGINE=InnoDB COMMENT='Tracking waktu kerja karyawan';

-- =====================================================
-- 10. APPOINTMENTS - Janji Temu
-- =====================================================
CREATE TABLE appointments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appointment_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode appointment',
    title VARCHAR(200) NOT NULL COMMENT 'Judul appointment',
    description TEXT NULL COMMENT 'Deskripsi appointment',
    appointment_type ENUM('meeting', 'visit', 'call', 'demo', 'training', 'other') DEFAULT 'meeting',
    customer_id BIGINT UNSIGNED NULL COMMENT 'Customer (link ke orang.persons)',
    employee_id BIGINT UNSIGNED NULL COMMENT 'Employee (link ke orang.persons)',
    start_datetime DATETIME NOT NULL COMMENT 'Waktu mulai',
    end_datetime DATETIME NOT NULL COMMENT 'Waktu selesai',
    duration_minutes INT GENERATED ALWAYS AS (TIMESTAMPDIFF(MINUTE, start_datetime, end_datetime)) STORED,
    location VARCHAR(255) NULL COMMENT 'Lokasi',
    is_virtual BOOLEAN DEFAULT FALSE COMMENT 'Meeting virtual',
    meeting_url VARCHAR(500) NULL COMMENT 'URL meeting virtual',
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
    priority_level TINYINT DEFAULT 3 COMMENT 'Prioritas (1=highest, 5=lowest)',
    reminder_minutes INT DEFAULT 15 COMMENT 'Reminder (menit sebelum)',
    notes TEXT NULL COMMENT 'Catatan appointment',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (customer_id) REFERENCES orang.persons(id) ON DELETE SET NULL,
    FOREIGN KEY (employee_id) REFERENCES orang.persons(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_appointment_code (appointment_code),
    INDEX idx_appointment_type (appointment_type),
    INDEX idx_customer_id (customer_id),
    INDEX idx_employee_id (employee_id),
    INDEX idx_start_datetime (start_datetime),
    INDEX idx_end_datetime (end_datetime),
    INDEX idx_status (status),
    INDEX idx_priority_level (priority_level)
) ENGINE=InnoDB COMMENT='Janji temu dan meeting';

-- =====================================================
-- 11. DEADLINES - Deadline dan Reminder
-- =====================================================
CREATE TABLE deadlines (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL COMMENT 'Judul deadline',
    description TEXT NULL COMMENT 'Deskripsi deadline',
    deadline_type ENUM('payment', 'delivery', 'report', 'task', 'contract', 'other') NOT NULL,
    reference_type VARCHAR(50) NOT NULL COMMENT 'Tipe referensi (sale, purchase, dll)',
    reference_id BIGINT UNSIGNED NOT NULL COMMENT 'ID referensi',
    deadline_datetime DATETIME NOT NULL COMMENT 'Waktu deadline',
    reminder_intervals JSON NULL COMMENT 'Interval reminder [24, 2, 0.5] jam sebelum',
    assigned_to BIGINT UNSIGNED NULL COMMENT 'Ditugaskan ke (link ke orang.users)',
    status ENUM('pending', 'reminded', 'completed', 'overdue', 'cancelled') DEFAULT 'pending',
    priority_level TINYINT DEFAULT 3 COMMENT 'Prioritas (1=highest, 5=lowest)',
    completed_at TIMESTAMP NULL COMMENT 'Waktu completion',
    completed_by BIGINT UNSIGNED NULL COMMENT 'Siapa yang complete',
    notes TEXT NULL COMMENT 'Catatan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (assigned_to) REFERENCES orang.users(id) ON DELETE SET NULL,
    FOREIGN KEY (completed_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_deadline_type (deadline_type),
    INDEX idx_reference_type (reference_type),
    INDEX idx_reference_id (reference_id),
    INDEX idx_deadline_datetime (deadline_datetime),
    INDEX idx_assigned_to (assigned_to),
    INDEX idx_status (status),
    INDEX idx_priority_level (priority_level)
) ENGINE=InnoDB COMMENT='Deadline dan reminder system';

-- =====================================================
-- INSERT DEFAULT DATA
-- =====================================================

-- Default Business Hours
INSERT INTO business_hours (day_of_week, opening_time, closing_time, break_start_time, break_end_time, is_working_day, description) VALUES
(1, '08:00:00', '17:00:00', '12:00:00', '13:00:00', TRUE, 'Senin'),
(2, '08:00:00', '17:00:00', '12:00:00', '13:00:00', TRUE, 'Selasa'),
(3, '08:00:00', '17:00:00', '12:00:00', '13:00:00', TRUE, 'Rabu'),
(4, '08:00:00', '17:00:00', '12:00:00', '13:00:00', TRUE, 'Kamis'),
(5, '08:00:00', '17:00:00', '12:00:00', '13:00:00', TRUE, 'Jumat'),
(6, '08:00:00', '15:00:00', NULL, NULL, TRUE, 'Sabtu'),
(7, NULL, NULL, NULL, NULL, FALSE, 'Minggu');

-- Default Time Periods untuk tahun 2026
INSERT INTO time_periods (period_type, period_name, period_code, start_date, end_date, is_current) VALUES
('year', '2026', '2026', '2026-01-01', '2026-12-31', TRUE),
('quarter', 'Q1 2026', '2026Q1', '2026-01-01', '2026-03-31', TRUE),
('quarter', 'Q2 2026', '2026Q2', '2026-04-01', '2026-06-30', FALSE),
('quarter', 'Q3 2026', '2026Q3', '2026-07-01', '2026-09-30', FALSE),
('quarter', 'Q4 2026', '2026Q4', '2026-10-01', '2026-12-31', FALSE),
('month', 'Januari 2026', '202601', '2026-01-01', '2026-01-31', TRUE),
('month', 'Februari 2026', '202602', '2026-02-01', '2026-02-28', FALSE),
('month', 'Maret 2026', '202603', '2026-03-01', '2026-03-31', FALSE);

-- =====================================================
-- VIEWS untuk kemudahan query
-- =====================================================

-- View untuk jadwal pengiriman hari ini
CREATE VIEW v_delivery_schedules_today AS
SELECT 
    ds.*,
    dsi.customer_id,
    p.full_name as customer_name,
    dsi.address,
    dsi.contact_person,
    dsi.contact_phone,
    dsi.sequence_order,
    dsi.estimated_arrival
FROM delivery_schedules ds
JOIN delivery_schedule_items dsi ON ds.id = dsi.schedule_id
LEFT JOIN orang.persons p ON dsi.customer_id = p.id
WHERE ds.is_active = TRUE 
  AND dsi.is_active = TRUE
  AND DAYOFWEEK(CURDATE()) - 1 IN (JSON_UNQUOTE(JSON_EXTRACT(ds.delivery_days, CONCAT('$[', DAYOFWEEK(CURDATE()) - 2, ']'))))
ORDER BY dsi.sequence_order;

-- View untuk tracking waktu kerja bulanan
CREATE VIEW v_time_tracking_monthly AS
SELECT 
    employee_id,
    p.full_name,
    YEAR(work_date) as year,
    MONTH(work_date) as month,
    COUNT(*) as total_days,
    SUM(total_work_minutes) as total_work_minutes,
    SUM(total_break_minutes) as total_break_minutes,
    SUM(overtime_minutes) as total_overtime_minutes,
    AVG(total_work_minutes) as avg_daily_minutes,
    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days,
    SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days,
    SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_days
FROM time_tracking tt
JOIN orang.persons p ON tt.employee_id = p.id
GROUP BY employee_id, YEAR(work_date), MONTH(work_date);
