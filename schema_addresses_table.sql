-- Migration untuk menambahkan tabel alamat terpusat
-- File: schema_addresses_table.sql

USE distributor;

-- Tabel alamat terpusat yang bisa digunakan oleh berbagai entitas
CREATE TABLE IF NOT EXISTS addresses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    street_address TEXT NULL,
    province_id INT UNSIGNED NULL,
    regency_id INT UNSIGNED NULL,
    district_id INT UNSIGNED NULL,
    village_id INT UNSIGNED NULL,
    postal_code VARCHAR(10) NULL,
    address_type ENUM('supplier', 'customer', 'branch', 'company', 'pickup', 'delivery', 'other') DEFAULT 'other',
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_address_type (address_type),
    INDEX idx_is_primary (is_primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel penghubung antara orang dan alamat (one-to-many)
CREATE TABLE IF NOT EXISTS orang_addresses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    orang_id INT UNSIGNED NOT NULL,
    address_id INT UNSIGNED NOT NULL,
    address_type ENUM('supplier', 'customer', 'personal', 'other') DEFAULT 'other',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_orang_address_type (orang_id, address_type),
    INDEX idx_orang_id (orang_id),
    INDEX idx_address_id (address_id),
    
    CONSTRAINT fk_orang_addresses_orang FOREIGN KEY (orang_id) REFERENCES orang(id_orang) ON DELETE CASCADE,
    CONSTRAINT fk_orang_addresses_address FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel penghubung antara branch dan alamat
CREATE TABLE IF NOT EXISTS branch_addresses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id INT UNSIGNED NOT NULL,
    address_id INT UNSIGNED NOT NULL,
    address_type ENUM('office', 'warehouse', 'pickup', 'delivery', 'other') DEFAULT 'office',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_branch_address_type (branch_id, address_type),
    INDEX idx_branch_id (branch_id),
    INDEX idx_address_id (address_id),
    
    CONSTRAINT fk_branch_addresses_branch FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    CONSTRAINT fk_branch_addresses_address FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- View untuk mendapatkan alamat lengkap orang dengan format yang bagus
CREATE OR REPLACE VIEW v_orang_addresses AS
SELECT 
    o.id_orang,
    o.nama_lengkap,
    a.id as address_id,
    a.street_address,
    a.province_id,
    p.name as province_name,
    a.regency_id,
    r.name as regency_name,
    a.district_id,
    d.name as district_name,
    a.village_id,
    v.name as village_name,
    a.postal_code,
    oa.address_type,
    oa.is_active,
    CONCAT(
        COALESCE(a.street_address, ''),
        CASE WHEN a.street_address IS NOT NULL AND a.street_address != '' THEN ', ' ELSE '' END,
        CASE WHEN v.name IS NOT NULL THEN CONCAT('Desa/Kel. ', v.name) ELSE '' END,
        CASE WHEN v.name IS NOT NULL THEN ', ' ELSE '' END,
        CASE WHEN d.name IS NOT NULL THEN CONCAT('Kec. ', d.name) ELSE '' END,
        CASE WHEN d.name IS NOT NULL THEN ', ' ELSE '' END,
        CASE WHEN r.name IS NOT NULL THEN r.name ELSE '' END,
        CASE WHEN r.name IS NOT NULL THEN ', ' ELSE '' END,
        CASE WHEN p.name IS NOT NULL THEN p.name ELSE '' END,
        CASE WHEN a.postal_code IS NOT NULL AND a.postal_code != '' THEN CONCAT(', ', a.postal_code) ELSE '' END
    ) as full_address
FROM orang o
JOIN orang_addresses oa ON o.id_orang = oa.orang_id
JOIN addresses a ON oa.address_id = a.id
LEFT JOIN alamat_db.provinces p ON a.province_id = p.id
LEFT JOIN alamat_db.regencies r ON a.regency_id = r.id  
LEFT JOIN alamat_db.districts d ON a.district_id = d.id
LEFT JOIN alamat_db.villages v ON a.village_id = v.id
WHERE oa.is_active = 1;
