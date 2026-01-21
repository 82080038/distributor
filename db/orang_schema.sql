-- =====================================================
-- DATABASE ORANG - People Management System
-- =====================================================
-- Created: 19 Januari 2026
-- Purpose: Manajemen data orang, users, roles, dan relasi
-- Integration: Link ke alamat_db untuk data alamat

CREATE DATABASE IF NOT EXISTS orang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE orang;

-- =====================================================
-- 1. PERSONS - Data Pribadi Lengkap
-- =====================================================
CREATE TABLE persons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(16) UNIQUE NULL COMMENT 'Nomor Induk Kependudukan',
    paspor VARCHAR(20) UNIQUE NULL COMMENT 'Nomor Paspor',
    first_name VARCHAR(100) NOT NULL COMMENT 'Nama depan',
    last_name VARCHAR(100) NULL COMMENT 'Nama belakang',
    full_name VARCHAR(255) GENERATED ALWAYS AS (CONCAT(IFNULL(first_name, ''), ' ', IFNULL(last_name, ''))) STORED,
    gender ENUM('male', 'female', 'other') NULL,
    birth_date DATE NULL COMMENT 'Tanggal lahir',
    birth_place VARCHAR(100) NULL COMMENT 'Tempat lahir',
    phone VARCHAR(20) NULL COMMENT 'Nomor telepon utama',
    email VARCHAR(255) UNIQUE NULL COMMENT 'Email utama',
    photo_url VARCHAR(500) NULL COMMENT 'URL foto profil',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Status aktif',
    notes TEXT NULL COMMENT 'Catatan tambahan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    
    INDEX idx_nik (nik),
    INDEX idx_full_name (full_name),
    INDEX idx_phone (phone),
    INDEX idx_email (email),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Master data pribadi lengkap';

-- =====================================================
-- 2. USERS - Akun Sistem
-- =====================================================
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    person_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke persons',
    username VARCHAR(50) UNIQUE NOT NULL COMMENT 'Username login',
    password_hash VARCHAR(255) NOT NULL COMMENT 'Hash password',
    email VARCHAR(255) UNIQUE NOT NULL COMMENT 'Email login',
    last_login TIMESTAMP NULL COMMENT 'Terakhir login',
    login_attempts TINYINT DEFAULT 0 COMMENT 'Jumlah percobaan login gagal',
    locked_until TIMESTAMP NULL COMMENT 'Dikunci sampai',
    password_changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Password terakhir diubah',
    must_change_password BOOLEAN DEFAULT FALSE COMMENT 'Wajib ganti password',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Status aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_last_login (last_login),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Akun pengguna sistem';

-- =====================================================
-- 3. ROLES - Peran Pengguna
-- =====================================================
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL COMMENT 'Nama role',
    display_name VARCHAR(100) NOT NULL COMMENT 'Nama tampilan',
    description TEXT NULL COMMENT 'Deskripsi role',
    level TINYINT DEFAULT 0 COMMENT 'Level hierarki (0=lowest, 9=highest)',
    is_system BOOLEAN DEFAULT FALSE COMMENT 'Role sistem (tidak bisa dihapus)',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Status aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_name (name),
    INDEX idx_level (level),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Peran/role pengguna';

-- =====================================================
-- 4. USER_ROLES - Hubungan User dengan Role
-- =====================================================
CREATE TABLE user_roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    assigned_by BIGINT UNSIGNED NULL COMMENT 'Siapa yang assign',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL COMMENT 'Kadaluarsa role',
    is_active BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_user_role (user_id, role_id),
    INDEX idx_user_id (user_id),
    INDEX idx_role_id (role_id),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB COMMENT='Hubungan user dengan role';

-- =====================================================
-- 5. PERSON_CONTACTS - Kontak Multiple
-- =====================================================
CREATE TABLE person_contacts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    person_id BIGINT UNSIGNED NOT NULL,
    contact_type ENUM('phone', 'email', 'whatsapp', 'telegram', 'social_media', 'other') NOT NULL,
    contact_value VARCHAR(255) NOT NULL COMMENT 'Nilai kontak',
    is_primary BOOLEAN DEFAULT FALSE COMMENT 'Kontak utama',
    is_active BOOLEAN DEFAULT TRUE,
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE,
    
    INDEX idx_person_id (person_id),
    INDEX idx_contact_type (contact_type),
    INDEX idx_contact_value (contact_value),
    INDEX idx_is_primary (is_primary)
) ENGINE=InnoDB COMMENT='Kontak multiple untuk setiap person';

-- =====================================================
-- 6. PERSON_DOCUMENTS - Dokumen Pribadi
-- =====================================================
CREATE TABLE person_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    person_id BIGINT UNSIGNED NOT NULL,
    document_type ENUM('ktp', 'npwp', 'sim', 'passport', 'bpjs', 'bank_account', 'certificate', 'other') NOT NULL,
    document_number VARCHAR(50) NULL COMMENT 'Nomor dokumen',
    document_name VARCHAR(255) NOT NULL COMMENT 'Nama dokumen',
    file_url VARCHAR(500) NULL COMMENT 'URL file dokumen',
    file_path VARCHAR(500) NULL COMMENT 'Path file di server',
    file_size BIGINT NULL COMMENT 'Ukuran file dalam bytes',
    mime_type VARCHAR(100) NULL COMMENT 'MIME type file',
    issued_date DATE NULL COMMENT 'Tanggal terbit',
    expired_date DATE NULL COMMENT 'Tanggal kadaluarsa',
    issuing_authority VARCHAR(255) NULL COMMENT 'Penerbit',
    is_verified BOOLEAN DEFAULT FALSE COMMENT 'Status verifikasi',
    verification_notes TEXT NULL COMMENT 'Catatan verifikasi',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_person_id (person_id),
    INDEX idx_document_type (document_type),
    INDEX idx_document_number (document_number),
    INDEX idx_expired_date (expired_date),
    INDEX idx_is_verified (is_verified)
) ENGINE=InnoDB COMMENT='Dokumen-dokumen pribadi';

-- =====================================================
-- 7. PERSON_ADDRESSES - Alamat Person
-- =====================================================
CREATE TABLE person_addresses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    person_id BIGINT UNSIGNED NOT NULL,
    address_type ENUM('home', 'office', 'billing', 'shipping', 'warehouse', 'other') NOT NULL,
    village_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.villages',
    district_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.districts',
    regency_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.regencies',
    province_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.provinces',
    postal_code VARCHAR(10) NULL COMMENT 'Kode pos',
    address_line VARCHAR(255) NOT NULL COMMENT 'Alamat lengkap',
    address_line2 VARCHAR(255) NULL COMMENT 'Alamat tambahan',
    latitude DECIMAL(10,8) NULL COMMENT 'Koordinat latitude',
    longitude DECIMAL(11,8) NULL COMMENT 'Koordinat longitude',
    is_primary BOOLEAN DEFAULT FALSE COMMENT 'Alamat utama',
    is_active BOOLEAN DEFAULT TRUE,
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (person_id) REFERENCES persons(id) ON DELETE CASCADE,
    
    INDEX idx_person_id (person_id),
    INDEX idx_address_type (address_type),
    INDEX idx_village_id (village_id),
    INDEX idx_district_id (district_id),
    INDEX idx_regency_id (regency_id),
    INDEX idx_province_id (province_id),
    INDEX idx_postal_code (postal_code),
    INDEX idx_is_primary (is_primary)
) ENGINE=InnoDB COMMENT='Alamat-alamat person';

-- =====================================================
-- 8. PERSON_RELATIONS - Hubungan Antar Person
-- =====================================================
CREATE TABLE person_relations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    person1_id BIGINT UNSIGNED NOT NULL COMMENT 'Person pertama',
    person2_id BIGINT UNSIGNED NOT NULL COMMENT 'Person kedua',
    relation_type ENUM('family', 'spouse', 'parent', 'child', 'sibling', 'friend', 'colleague', 'business_partner', 'other') NOT NULL,
    relation_description VARCHAR(255) NULL COMMENT 'Deskripsi hubungan',
    is_emergency_contact BOOLEAN DEFAULT FALSE COMMENT 'Kontak darurat',
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (person1_id) REFERENCES persons(id) ON DELETE CASCADE,
    FOREIGN KEY (person2_id) REFERENCES persons(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_relation (person1_id, person2_id, relation_type),
    INDEX idx_person1_id (person1_id),
    INDEX idx_person2_id (person2_id),
    INDEX idx_relation_type (relation_type),
    INDEX idx_is_emergency_contact (is_emergency_contact)
) ENGINE=InnoDB COMMENT='Hubungan antar persons';

-- =====================================================
-- 9. USER_SESSIONS - Sesi Login User
-- =====================================================
CREATE TABLE user_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    session_id VARCHAR(255) UNIQUE NOT NULL COMMENT 'Session ID',
    ip_address VARCHAR(45) NOT NULL COMMENT 'IP address',
    user_agent TEXT NULL COMMENT 'User agent browser',
    device_type ENUM('desktop', 'mobile', 'tablet', 'other') DEFAULT 'desktop',
    login_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    logout_at TIMESTAMP NULL COMMENT 'Waktu logout',
    is_active BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_user_id (user_id),
    INDEX idx_session_id (session_id),
    INDEX idx_ip_address (ip_address),
    INDEX idx_last_activity (last_activity),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Tracking sesi user';

-- =====================================================
-- 10. USER_PERMISSIONS - Permission Spesifik
-- =====================================================
CREATE TABLE user_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    permission_name VARCHAR(100) NOT NULL COMMENT 'Nama permission',
    resource VARCHAR(100) NOT NULL COMMENT 'Resource yang diakses',
    action ENUM('create', 'read', 'update', 'delete', 'approve', 'export', 'import') NOT NULL,
    granted_by BIGINT UNSIGNED NULL COMMENT 'Siapa yang memberikan',
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL COMMENT 'Kadaluarsa permission',
    is_active BOOLEAN DEFAULT TRUE,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (granted_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_user_permission (user_id, permission_name, resource, action),
    INDEX idx_user_id (user_id),
    INDEX idx_permission_name (permission_name),
    INDEX idx_resource (resource),
    INDEX idx_action (action),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB COMMENT='Permission spesifik per user';

-- =====================================================
-- INSERT DEFAULT ROLES
-- =====================================================
INSERT INTO roles (name, display_name, description, level, is_system) VALUES
('super_admin', 'Super Administrator', 'Akses penuh ke seluruh sistem', 9, TRUE),
('admin', 'Administrator', 'Administrator sistem', 8, TRUE),
('manager', 'Manager', 'Manager operasional', 7, TRUE),
('supervisor', 'Supervisor', 'Supervisor tim', 6, TRUE),
('staff', 'Staff', 'Staf administrasi', 5, TRUE),
('sales', 'Sales', 'Tim penjualan', 4, TRUE),
('warehouse', 'Warehouse Staff', 'Staf gudang', 3, TRUE),
('customer', 'Customer', 'Pelanggan', 2, TRUE),
('supplier', 'Supplier', 'Pemasok', 2, TRUE),
('guest', 'Guest', 'Tamu dengan akses terbatas', 1, TRUE);

-- =====================================================
-- VIEWS untuk kemudahan query
-- =====================================================

-- View untuk user lengkap dengan person
CREATE VIEW v_users_complete AS
SELECT 
    u.id as user_id,
    u.username,
    u.email,
    u.last_login,
    u.is_active as user_active,
    p.id as person_id,
    p.nik,
    p.full_name,
    p.phone,
    p.gender,
    p.birth_date,
    GROUP_CONCAT(r.display_name) as roles
FROM users u
JOIN persons p ON u.person_id = p.id
LEFT JOIN user_roles ur ON u.id = ur.user_id
LEFT JOIN roles r ON ur.role_id = r.id AND ur.is_active = TRUE
WHERE u.is_active = TRUE
GROUP BY u.id;

-- View untuk person dengan alamat utama
CREATE VIEW v_persons_main_address AS
SELECT 
    p.*,
    pa.address_type,
    pa.address_line,
    pa.postal_code,
    v.name as village_name,
    d.name as district_name,
    reg.name as regency_name,
    prov.name as province_name
FROM persons p
LEFT JOIN person_addresses pa ON p.id = pa.person_id AND pa.is_primary = TRUE AND pa.is_active = TRUE
LEFT JOIN alamat_db.villages v ON pa.village_id = v.id
LEFT JOIN alamat_db.districts d ON pa.district_id = d.id
LEFT JOIN alamat_db.regencies reg ON pa.regency_id = reg.id
LEFT JOIN alamat_db.provinces prov ON pa.province_id = prov.id
WHERE p.is_active = TRUE;
