-- =====================================================
-- DATABASE HRM - Human Resource Management
-- =====================================================
-- Created: 19 Januari 2026
-- Purpose: Manajemen SDM lengkap, payroll, dan performa karyawan
-- Integration: Link ke orang, aplikasi, waktu

CREATE DATABASE IF NOT EXISTS hrm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hrm;

-- =====================================================
-- 1. EMPLOYEES - Master Karyawan
-- =====================================================
CREATE TABLE employees (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Kode karyawan',
    person_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke orang.persons',
    employee_status ENUM('active', 'probation', 'contract', 'intern', 'on_leave', 'terminated', 'resigned') DEFAULT 'active',
    employment_type ENUM('permanent', 'contract', 'daily', 'part_time', 'intern', 'consultant') DEFAULT 'permanent',
    hire_date DATE NOT NULL COMMENT 'Tanggal mulai kerja',
    confirmation_date DATE NULL COMMENT 'Tanggal konfirmasi',
    contract_end_date DATE NULL COMMENT 'Tanggal akhir kontrak',
    department_id BIGINT UNSIGNED NULL COMMENT 'Department',
    position_id BIGINT UNSIGNED NOT NULL COMMENT 'Posisi/Jabatan',
    supervisor_id BIGINT UNSIGNED NULL COMMENT 'Atasan langsung',
    grade_level VARCHAR(10) NULL COMMENT 'Grade/Level',
    work_location VARCHAR(100) NULL COMMENT 'Lokasi kerja',
    work_schedule_id BIGINT UNSIGNED NULL COMMENT 'Link ke waktu.work_schedules',
    email VARCHAR(255) UNIQUE NULL COMMENT 'Email karyawan',
    phone VARCHAR(20) NULL COMMENT 'Telepon karyawan',
    emergency_contact_name VARCHAR(100) NULL COMMENT 'Kontak darurat',
    emergency_contact_phone VARCHAR(20) NULL COMMENT 'Telepon darurat',
    emergency_contact_relation VARCHAR(50) NULL COMMENT 'Hubungan darurat',
    blood_type ENUM('A', 'B', 'AB', 'O') NULL COMMENT 'Golongan darah',
    marital_status ENUM('single', 'married', 'divorced', 'widowed') NULL COMMENT 'Status pernikahan',
    religion ENUM('islam', 'christian', 'catholic', 'hindu', 'buddhist', 'confucian', 'other') NULL COMMENT 'Agama',
    nationality VARCHAR(50) DEFAULT 'Indonesia' COMMENT 'Kewarganegaraan',
    is_tax_resident BOOLEAN DEFAULT TRUE COMMENT 'Wajib pajak',
    tax_id VARCHAR(25) NULL COMMENT 'NPWP',
    bpjs_ketenagakerjaan VARCHAR(20) NULL COMMENT 'No BPJS Ketenagakerjaan',
    bpjs_kesehatan VARCHAR(20) NULL COMMENT 'No BPJS Kesehatan',
    bank_account_number VARCHAR(50) NULL COMMENT 'Nomor rekening',
    bank_name VARCHAR(100) NULL COMMENT 'Nama bank',
    bank_account_name VARCHAR(100) NULL COMMENT 'Nama rekening',
    notes TEXT NULL COMMENT 'Catatan karyawan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (person_id) REFERENCES orang.persons(id) ON DELETE RESTRICT,
    FOREIGN KEY (supervisor_id) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_employee_code (employee_code),
    INDEX idx_person_id (person_id),
    INDEX idx_employee_status (employee_status),
    INDEX idx_employment_type (employment_type),
    INDEX idx_hire_date (hire_date),
    INDEX idx_department_id (department_id),
    INDEX idx_position_id (position_id),
    INDEX idx_supervisor_id (supervisor_id),
    INDEX idx_grade_level (grade_level),
    INDEX idx_work_location (work_location),
    INDEX idx_email (email)
) ENGINE=InnoDB COMMENT='Master data karyawan';

-- =====================================================
-- 2. DEPARTMENTS - Departemen
-- =====================================================
CREATE TABLE departments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    department_code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Kode departemen',
    department_name VARCHAR(100) NOT NULL COMMENT 'Nama departemen',
    parent_department_id BIGINT UNSIGNED NULL COMMENT 'Departemen induk',
    department_level TINYINT DEFAULT 1 COMMENT 'Level departemen',
    manager_id BIGINT UNSIGNED NULL COMMENT 'Manager departemen',
    description TEXT NULL COMMENT 'Deskripsi departemen',
    cost_center VARCHAR(20) NULL COMMENT 'Cost center',
    budget_limit DECIMAL(15,2) DEFAULT 0 COMMENT 'Limit budget',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (parent_department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (manager_id) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_department_code (department_code),
    INDEX idx_department_name (department_name),
    INDEX idx_parent_department_id (parent_department_id),
    INDEX idx_department_level (department_level),
    INDEX idx_manager_id (manager_id),
    INDEX idx_cost_center (cost_center),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Master departemen';

-- =====================================================
-- 3. POSITIONS - Posisi/Jabatan
-- =====================================================
CREATE TABLE positions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    position_code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Kode posisi',
    position_name VARCHAR(100) NOT NULL COMMENT 'Nama posisi',
    position_level TINYINT NOT NULL COMMENT 'Level posisi (1=highest)',
    position_category ENUM('executive', 'manager', 'supervisor', 'staff', 'operator', 'intern') NOT NULL,
    department_id BIGINT UNSIGNED NULL COMMENT 'Department utama',
    job_description TEXT NULL COMMENT 'Deskripsi pekerjaan',
    job_requirements TEXT NULL COMMENT 'Persyaratan pekerjaan',
    responsibilities TEXT NULL COMMENT 'Tanggung jawab',
    skills_required JSON NULL COMMENT 'Skill yang dibutuhkan',
    education_level ENUM('smp', 'sma', 'd3', 's1', 's2', 's3', 'other') NULL COMMENT 'Pendidikan minimal',
    experience_years_min INT DEFAULT 0 COMMENT 'Pengalaman minimal (tahun)',
    experience_years_max INT NULL COMMENT 'Pengalaman maksimal (tahun)',
    salary_min DECIMAL(10,2) DEFAULT 0 COMMENT 'Gaji minimal',
    salary_max DECIMAL(10,2) DEFAULT 0 COMMENT 'Gaji maksimal',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_position_code (position_code),
    INDEX idx_position_name (position_name),
    INDEX idx_position_level (position_level),
    INDEX idx_position_category (position_category),
    INDEX idx_department_id (department_id),
    INDEX idx_education_level (education_level),
    INDEX idx_salary_min (salary_min),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Master posisi/jabatan';

-- =====================================================
-- 4. SALARY_STRUCTURE - Struktur Gaji
-- =====================================================
CREATE TABLE salary_structure (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    structure_code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Kode struktur',
    structure_name VARCHAR(100) NOT NULL COMMENT 'Nama struktur',
    position_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke positions',
    grade_level VARCHAR(10) NOT NULL COMMENT 'Grade level',
    base_salary DECIMAL(12,2) NOT NULL COMMENT 'Gaji pokok',
    transport_allowance DECIMAL(10,2) DEFAULT 0 COMMENT 'Tunjangan transport',
    meal_allowance DECIMAL(10,2) DEFAULT 0 COMMENT 'Tunjangan makan',
    housing_allowance DECIMAL(10,2) DEFAULT 0 COMMENT 'Tunjangan rumah',
    health_allowance DECIMAL(10,2) DEFAULT 0 COMMENT 'Tunjangan kesehatan',
    communication_allowance DECIMAL(10,2) DEFAULT 0 COMMENT 'Tunjangan komunikasi',
    position_allowance DECIMAL(10,2) DEFAULT 0 COMMENT 'Tunjangan jabatan',
    performance_bonus DECIMAL(10,2) DEFAULT 0 COMMENT 'Bonus performa',
    other_allowances DECIMAL(10,2) DEFAULT 0 COMMENT 'Tunjangan lain',
    total_gross DECIMAL(12,2) GENERATED ALWAYS AS (base_salary + transport_allowance + meal_allowance + housing_allowance + health_allowance + communication_allowance + position_allowance + performance_bonus + other_allowances) STORED,
    bpjs_company DECIMAL(10,2) DEFAULT 0 COMMENT 'BPJS perusahaan',
    bpjs_employee DECIMAL(10,2) DEFAULT 0 COMMENT 'BPJS karyawan',
    jkk_company DECIMAL(10,2) DEFAULT 0 COMMENT 'JKK perusahaan',
    jkm_company DECIMAL(10,2) DEFAULT 0 COMMENT 'JKM perusahaan',
    jht_employee DECIMAL(10,2) DEFAULT 0 COMMENT 'JHT karyawan',
    jht_company DECIMAL(10,2) DEFAULT 0 COMMENT 'JHT perusahaan',
    jp_employee DECIMAL(10,2) DEFAULT 0 COMMENT 'JP karyawan',
    jp_company DECIMAL(10,2) DEFAULT 0 COMMENT 'JP perusahaan',
    pph21_employee DECIMAL(10,2) DEFAULT 0 COMMENT 'PPh21 karyawan',
    total_deduction DECIMAL(10,2) GENERATED ALWAYS AS (bpjs_employee + jht_employee + jp_employee + pph21_employee) STORED,
    net_salary DECIMAL(12,2) GENERATED ALWAYS AS (total_gross - total_deduction) STORED,
    effective_date DATE NOT NULL COMMENT 'Tanggal efektif',
    expiry_date DATE NULL COMMENT 'Tanggal kadaluarsa',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_structure_code (structure_code),
    INDEX idx_structure_name (structure_name),
    INDEX idx_position_id (position_id),
    INDEX idx_grade_level (grade_level),
    INDEX idx_base_salary (base_salary),
    INDEX idx_total_gross (total_gross),
    INDEX idx_net_salary (net_salary),
    INDEX idx_effective_date (effective_date),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Struktur gaji karyawan';

-- =====================================================
-- 5. ATTENDANCE - Absensi
-- =====================================================
CREATE TABLE attendance (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke employees',
    attendance_date DATE NOT NULL COMMENT 'Tanggal absensi',
    clock_in TIME NULL COMMENT 'Jam masuk',
    clock_out TIME NULL COMMENT 'Jam keluar',
    break_start TIME NULL COMMENT 'Mulai istirahat',
    break_end TIME NULL NULL COMMENT 'Selesai istirahat',
    total_work_minutes INT DEFAULT 0 COMMENT 'Total menit kerja',
    total_break_minutes INT DEFAULT 0 COMMENT 'Total menit istirahat',
    overtime_minutes INT DEFAULT 0 COMMENT 'Menit lembur',
    late_minutes INT DEFAULT 0 COMMENT 'Menit terlambat',
    early_leave_minutes INT DEFAULT 0 COMMENT 'Menit pulang awal',
    attendance_status ENUM('present', 'absent', 'late', 'early_leave', 'holiday', 'leave', 'sick', 'permission') DEFAULT 'present',
    work_location VARCHAR(100) NULL COMMENT 'Lokasi kerja',
    clock_in_device VARCHAR(50) NULL COMMENT 'Device clock in',
    clock_out_device VARCHAR(50) NULL COMMENT 'Device clock out',
    clock_in_latitude DECIMAL(10,8) NULL COMMENT 'Latitude clock in',
    clock_in_longitude DECIMAL(11,8) NULL COMMENT 'Longitude clock in',
    clock_out_latitude DECIMAL(10,8) NULL COMMENT 'Latitude clock out',
    clock_out_longitude DECIMAL(11,8) NULL COMMENT 'Longitude clock out',
    notes VARCHAR(255) NULL COMMENT 'Catatan absensi',
    approved_by BIGINT UNSIGNED NULL COMMENT 'Disetujui oleh',
    approved_at TIMESTAMP NULL COMMENT 'Waktu approval',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_employee_date (employee_id, attendance_date),
    INDEX idx_employee_id (employee_id),
    INDEX idx_attendance_date (attendance_date),
    INDEX idx_clock_in (clock_in),
    INDEX idx_clock_out (clock_out),
    INDEX idx_total_work_minutes (total_work_minutes),
    INDEX idx_overtime_minutes (overtime_minutes),
    INDEX idx_late_minutes (late_minutes),
    INDEX idx_attendance_status (attendance_status),
    INDEX idx_approved_by (approved_by)
) ENGINE=InnoDB COMMENT='Data absensi karyawan';

-- =====================================================
-- 6. LEAVE_MANAGEMENT - Cuti
-- =====================================================
CREATE TABLE leave_management (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    leave_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode cuti',
    employee_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke employees',
    leave_type ENUM('annual', 'sick', 'maternity', 'paternity', 'unpaid', 'emergency', 'study', 'sabbatical', 'other') NOT NULL,
    start_date DATE NOT NULL COMMENT 'Tanggal mulai',
    end_date DATE NOT NULL COMMENT 'Tanggal selesai',
    total_days DECIMAL(4,1) NOT NULL COMMENT 'Total hari cuti',
    reason TEXT NOT NULL COMMENT 'Alasan cuti',
    attachment_url VARCHAR(500) NULL COMMENT 'URL lampiran',
    emergency_contact VARCHAR(100) NULL COMMENT 'Kontak darurat selama cuti',
    work_handover_to BIGINT UNSIGNED NULL COMMENT 'Serahkan kerja ke',
    status ENUM('draft', 'submitted', 'approved', 'rejected', 'cancelled', 'completed') DEFAULT 'draft',
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by BIGINT UNSIGNED NULL COMMENT 'Disetujui oleh',
    approved_at TIMESTAMP NULL COMMENT 'Waktu approval',
    rejection_reason TEXT NULL COMMENT 'Alasan penolakan',
    actual_return_date DATE NULL COMMENT 'Tanggal kembali aktual',
    notes TEXT NULL COMMENT 'Catatan cuti',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (work_handover_to) REFERENCES employees(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_leave_code (leave_code),
    INDEX idx_employee_id (employee_id),
    INDEX idx_leave_type (leave_type),
    INDEX idx_start_date (start_date),
    INDEX idx_end_date (end_date),
    INDEX idx_total_days (total_days),
    INDEX idx_status (status),
    INDEX idx_approval_status (approval_status),
    INDEX idx_approved_by (approved_by)
) ENGINE=InnoDB COMMENT='Manajemen cuti karyawan';

-- =====================================================
-- 7. PERFORMANCE_REVIEWS - Review Performa
-- =====================================================
CREATE TABLE performance_reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    review_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode review',
    employee_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke employees',
    reviewer_id BIGINT UNSIGNED NOT NULL COMMENT 'Reviewer',
    review_period ENUM('monthly', 'quarterly', 'semi_annual', 'annual', 'probation') NOT NULL,
    review_date DATE NOT NULL COMMENT 'Tanggal review',
    period_start_date DATE NOT NULL COMMENT 'Awal periode review',
    period_end_date DATE NOT NULL COMMENT 'Akhir periode review',
    overall_rating DECIMAL(3,2) NOT NULL COMMENT 'Rating overall (1-5)',
    performance_category ENUM('outstanding', 'exceeds_expectations', 'meets_expectations', 'needs_improvement', 'unsatisfactory') NOT NULL,
    achievements TEXT NULL COMMENT 'Pencapaian',
    strengths TEXT NULL COMMENT 'Kelebihan',
    areas_for_improvement TEXT NULL COMMENT 'Area perbaikan',
    goals_next_period TEXT NULL COMMENT 'Target periode berikutnya',
    training_recommendations TEXT NULL COMMENT 'Rekomendasi training',
    promotion_recommendation ENUM('promote', 'consider_promotion', 'maintain', 'performance_improvement_plan') NULL COMMENT 'Rekomendasi promosi',
    salary_recommendation ENUM('increase', 'maintain', 'decrease') NULL COMMENT 'Rekomendasi gaji',
    salary_recommendation_amount DECIMAL(10,2) NULL COMMENT 'Jumlah rekomendasi gaji',
    bonus_recommendation DECIMAL(10,2) NULL COMMENT 'Rekomendasi bonus',
    employee_comments TEXT NULL COMMENT 'Komentar karyawan',
    reviewer_comments TEXT NULL COMMENT 'Komentar reviewer',
    status ENUM('draft', 'submitted', 'reviewed', 'approved', 'rejected') DEFAULT 'draft',
    final_approval_by BIGINT UNSIGNED NULL COMMENT 'Approval akhir',
    final_approval_at TIMESTAMP NULL COMMENT 'Waktu approval akhir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES employees(id) ON DELETE RESTRICT),
    FOREIGN KEY (final_approval_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_review_code (review_code),
    INDEX idx_employee_id (employee_id),
    INDEX idx_reviewer_id (reviewer_id),
    INDEX idx_review_period (review_period),
    INDEX idx_review_date (review_date),
    INDEX idx_overall_rating (overall_rating),
    INDEX idx_performance_category (performance_category),
    INDEX idx_promotion_recommendation (promotion_recommendation),
    INDEX idx_status (status),
    INDEX idx_final_approval_by (final_approval_by)
) ENGINE=InnoDB COMMENT='Review performa karyawan';

-- =====================================================
-- 8. TRAINING_RECORDS - Record Training
-- =====================================================
CREATE TABLE training_records (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    training_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode training',
    employee_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke employees',
    training_name VARCHAR(200) NOT NULL COMMENT 'Nama training',
    training_type ENUM('internal', 'external', 'online', 'certification', 'workshop', 'seminar', 'conference') NOT NULL,
    training_category ENUM('technical', 'soft_skill', 'leadership', 'compliance', 'safety', 'quality', 'other') NOT NULL,
    provider VARCHAR(100) NULL COMMENT 'Penyelenggara training',
    instructor VARCHAR(100) NULL COMMENT 'Instruktur',
    start_date DATE NOT NULL COMMENT 'Tanggal mulai',
    end_date DATE NOT NULL COMMENT 'Tanggal selesai',
    duration_hours DECIMAL(5,2) NOT NULL COMMENT 'Durasi (jam)',
    location VARCHAR(255) NULL COMMENT 'Lokasi training',
    training_cost DECIMAL(10,2) DEFAULT 0 COMMENT 'Biaya training',
    certificate_issued BOOLEAN DEFAULT FALSE COMMENT 'Sertifikat diterbitkan',
    certificate_number VARCHAR(50) NULL COMMENT 'Nomor sertifikat',
    certificate_expiry DATE NULL COMMENT 'Kadaluarsa sertifikat',
    competency_level ENUM('basic', 'intermediate', 'advanced', 'expert') NULL COMMENT 'Level kompetensi',
    training_status ENUM('planned', 'in_progress', 'completed', 'cancelled', 'failed') DEFAULT 'planned',
    completion_status ENUM('passed', 'failed', 'pending') NULL COMMENT 'Status kelulusan',
    score DECIMAL(5,2) NULL COMMENT 'Nilai/Score',
    feedback_rating DECIMAL(3,2) NULL COMMENT 'Rating feedback (1-5)',
    feedback_comments TEXT NULL COMMENT 'Komentar feedback',
    skills_acquired JSON NULL COMMENT 'Skill yang diperoleh',
    notes TEXT NULL COMMENT 'Catatan training',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_training_code (training_code),
    INDEX idx_employee_id (employee_id),
    INDEX idx_training_name (training_name),
    INDEX idx_training_type (training_type),
    INDEX idx_training_category (training_category),
    INDEX idx_start_date (start_date),
    INDEX idx_end_date (end_date),
    INDEX idx_training_cost (training_cost),
    INDEX idx_certificate_issued (certificate_issued),
    INDEX idx_training_status (training_status),
    INDEX idx_completion_status (completion_status)
) ENGINE=InnoDB COMMENT='Record training karyawan';

-- =====================================================
-- 9. PAYROLL - Penggajian
-- =====================================================
CREATE TABLE payroll (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payroll_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode payroll',
    employee_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke employees',
    payroll_period VARCHAR(20) NOT NULL COMMENT 'Periode payroll (YYYY-MM)',
    payroll_date DATE NOT NULL COMMENT 'Tanggal payroll',
    payment_date DATE NOT NULL COMMENT 'Tanggal pembayaran',
    work_days INT DEFAULT 0 COMMENT 'Hari kerja',
    present_days INT DEFAULT 0 COMMENT 'Hari hadir',
    leave_days DECIMAL(4,1) DEFAULT 0 COMMENT 'Hari cuti',
    sick_days DECIMAL(4,1) DEFAULT 0 COMMENT 'Hari sakit',
    absent_days INT DEFAULT 0 COMMENT 'Hari tidak hadir',
    overtime_hours DECIMAL(5,2) DEFAULT 0 COMMENT 'Jam lembur',
    base_salary DECIMAL(12,2) NOT NULL COMMENT 'Gaji pokok',
    transport_allowance DECIMAL(10,2) DEFAULT 0 COMMENT 'Tunjangan transport',
    meal_allowance DECIMAL(10,2) DEFAULT 0 COMMENT 'Tunjangan makan',
    housing_allowance DECIMAL(10,2) DEFAULT 0 COMMENT 'Tunjangan rumah',
    health_allowance DECIMAL(10,2) DEFAULT 0 COMMENT 'Tunjangan kesehatan',
    position_allowance DECIMAL(10,2) DEFAULT 0 COMMENT 'Tunjangan jabatan',
    overtime_pay DECIMAL(10,2) DEFAULT 0 COMMENT 'Upah lembur',
    other_allowances DECIMAL(10,2) DEFAULT 0 COMMENT 'Tunjangan lain',
    bonus DECIMAL(10,2) DEFAULT 0 COMMENT 'Bonus',
    commission DECIMAL(10,2) DEFAULT 0 COMMENT 'Komisi',
    thr DECIMAL(10,2) DEFAULT 0 COMMENT 'THR',
    total_gross DECIMAL(12,2) GENERATED ALWAYS AS (base_salary + transport_allowance + meal_allowance + housing_allowance + health_allowance + position_allowance + overtime_pay + other_allowances + bonus + commission + thr) STORED,
    bpjs_employee DECIMAL(10,2) DEFAULT 0 COMMENT 'BPJS karyawan',
    jht_employee DECIMAL(10,2) DEFAULT 0 COMMENT 'JHT karyawan',
    jp_employee DECIMAL(10,2) DEFAULT 0 COMMENT 'JP karyawan',
    pph21 DECIMAL(10,2) DEFAULT 0 COMMENT 'PPh21',
    other_deductions DECIMAL(10,2) DEFAULT 0 COMMENT 'Potongan lain',
    total_deduction DECIMAL(10,2) GENERATED ALWAYS AS (bpjs_employee + jht_employee + jp_employee + pph21 + other_deductions) STORED,
    net_salary DECIMAL(12,2) GENERATED ALWAYS AS (total_gross - total_deduction) STORED,
    bank_account VARCHAR(50) NULL COMMENT 'Rekening transfer',
    payment_method ENUM('transfer', 'cash', 'check') DEFAULT 'transfer',
    payment_status ENUM('pending', 'processed', 'paid', 'failed', 'cancelled') DEFAULT 'pending',
    processed_by BIGINT UNSIGNED NULL COMMENT 'Diproses oleh',
    processed_at TIMESTAMP NULL COMMENT 'Waktu proses',
    notes TEXT NULL COMMENT 'Catatan payroll',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (processed_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_payroll_code (payroll_code),
    INDEX idx_employee_id (employee_id),
    INDEX idx_payroll_period (payroll_period),
    INDEX idx_payroll_date (payroll_date),
    INDEX idx_payment_date (payment_date),
    INDEX idx_base_salary (base_salary),
    INDEX idx_total_gross (total_gross),
    INDEX idx_net_salary (net_salary),
    INDEX idx_payment_status (payment_status),
    INDEX idx_processed_by (processed_by)
) ENGINE=InnoDB COMMENT='Data payroll karyawan';

-- =====================================================
-- 10. EMPLOYEE_BENEFITS - Benefit Karyawan
-- =====================================================
CREATE TABLE employee_benefits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    benefit_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode benefit',
    employee_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke employees',
    benefit_type ENUM('health_insurance', 'life_insurance', 'pension', 'vehicle', 'housing', 'education', 'meal', 'transport', 'other') NOT NULL,
    benefit_name VARCHAR(100) NOT NULL COMMENT 'Nama benefit',
    provider VARCHAR(100) NULL COMMENT 'Provider benefit',
    policy_number VARCHAR(50) NULL COMMENT 'Nomor polis',
    coverage_amount DECIMAL(12,2) DEFAULT 0 COMMENT 'Jumlah pertanggungan',
    employee_contribution DECIMAL(10,2) DEFAULT 0 COMMENT 'Kontribusi karyawan',
    company_contribution DECIMAL(10,2) DEFAULT 0 COMMENT 'Kontribusi perusahaan',
    total_contribution DECIMAL(10,2) GENERATED ALWAYS AS (employee_contribution + company_contribution) STORED,
    effective_date DATE NOT NULL COMMENT 'Tanggal efektif',
    expiry_date DATE NULL COMMENT 'Tanggal kadaluarsa',
    is_active BOOLEAN DEFAULT TRUE,
    beneficiary VARCHAR(100) NULL COMMENT 'Beneficiary',
    relationship VARCHAR(50) NULL COMMENT 'Hubungan beneficiary',
    notes TEXT NULL COMMENT 'Catatan benefit',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_benefit_code (benefit_code),
    INDEX idx_employee_id (employee_id),
    INDEX idx_benefit_type (benefit_type),
    INDEX idx_benefit_name (benefit_name),
    INDEX idx_provider (provider),
    INDEX idx_coverage_amount (coverage_amount),
    INDEX idx_effective_date (effective_date),
    INDEX idx_expiry_date (expiry_date),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Benefit karyawan';

-- =====================================================
-- INSERT DEFAULT DATA
-- =====================================================

-- Default Departments
INSERT INTO departments (department_code, department_name, department_level, description, cost_center) VALUES
('HR', 'Human Resources', 1, 'Human Resources Department', 'CC001'),
('FIN', 'Finance', 1, 'Finance Department', 'CC002'),
('OPS', 'Operations', 1, 'Operations Department', 'CC003'),
('MKT', 'Marketing', 1, 'Marketing Department', 'CC004'),
('IT', 'Information Technology', 1, 'IT Department', 'CC005'),
('LOG', 'Logistics', 1, 'Logistics Department', 'CC006');

-- Default Positions
INSERT INTO positions (position_code, position_name, position_level, position_category, department_id, job_description, salary_min, salary_max) VALUES
('CEO', 'Chief Executive Officer', 1, 'executive', NULL, 'CEO of the company', 50000000, 100000000),
('CFO', 'Chief Financial Officer', 2, 'executive', 2, 'CFO of the company', 30000000, 60000000),
('HRM', 'HR Manager', 3, 'manager', 1, 'HR Manager', 15000000, 25000000),
('ACC', 'Accountant', 5, 'staff', 2, 'Senior Accountant', 8000000, 15000000),
('OPR', 'Operations Staff', 6, 'staff', 3, 'Operations Staff', 5000000, 8000000),
('SLS', 'Sales Staff', 6, 'staff', 4, 'Sales Staff', 4000000, 10000000),
('IT', 'IT Staff', 5, 'staff', 5, 'IT Support Staff', 7000000, 12000000),
('DRV', 'Driver', 7, 'operator', 6, 'Delivery Driver', 3000000, 5000000);

-- =====================================================
-- VIEWS untuk HRM analytics
-- =====================================================

-- View untuk employee summary
CREATE VIEW v_employee_summary AS
SELECT 
    e.*,
    p.full_name,
    p.gender,
    p.birth_date,
    TIMESTAMPDIFF(YEAR, p.birth_date, CURDATE()) as age,
    d.department_name,
    pos.position_name,
    pos.position_level,
    pos.position_category,
    ss.base_salary,
    ss.net_salary,
    sup.employee_code as supervisor_code,
    sup_p.full_name as supervisor_name,
    TIMESTAMPDIFF(YEAR, e.hire_date, CURDATE()) as years_of_service,
    CASE 
        WHEN e.employee_status = 'active' THEN 'Active'
        WHEN e.employee_status = 'probation' THEN 'Probation'
        WHEN e.employee_status = 'contract' THEN 'Contract'
        WHEN e.employee_status = 'on_leave' THEN 'On Leave'
        WHEN e.employee_status = 'terminated' THEN 'Terminated'
        WHEN e.employee_status = 'resigned' THEN 'Resigned'
    END as status_description
FROM employees e
JOIN orang.persons p ON e.person_id = p.id
LEFT JOIN departments d ON e.department_id = d.id
LEFT JOIN positions pos ON e.position_id = pos.id
LEFT JOIN salary_structure ss ON e.position_id = ss.position_id AND ss.is_active = TRUE
LEFT JOIN employees sup ON e.supervisor_id = sup.id
LEFT JOIN orang.persons sup_p ON sup.person_id = sup_p.id;

-- View untuk attendance summary
CREATE VIEW v_attendance_summary AS
SELECT 
    e.employee_code,
    p.full_name,
    d.department_name,
    pos.position_name,
    COUNT(a.id) as total_days,
    SUM(CASE WHEN a.attendance_status = 'present' THEN 1 ELSE 0 END) as present_days,
    SUM(CASE WHEN a.attendance_status = 'absent' THEN 1 ELSE 0 END) as absent_days,
    SUM(CASE WHEN a.attendance_status = 'late' THEN 1 ELSE 0 END) as late_days,
    SUM(CASE WHEN a.attendance_status = 'sick' THEN 1 ELSE 0 END) as sick_days,
    SUM(CASE WHEN a.attendance_status = 'leave' THEN 1 ELSE 0 END) as leave_days,
    SUM(a.total_work_minutes) as total_work_minutes,
    AVG(a.total_work_minutes) as avg_daily_minutes,
    SUM(a.overtime_minutes) as total_overtime_minutes,
    AVG(a.overtime_minutes) as avg_overtime_minutes,
    MIN(a.attendance_date) as period_start,
    MAX(a.attendance_date) as period_end
FROM employees e
JOIN orang.persons p ON e.person_id = p.id
LEFT JOIN departments d ON e.department_id = d.id
LEFT JOIN positions pos ON e.position_id = pos.id
LEFT JOIN attendance a ON e.id = a.employee_id
WHERE a.attendance_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY e.id
ORDER BY total_work_minutes DESC;
