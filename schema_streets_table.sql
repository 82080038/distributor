-- Migration untuk menambahkan tabel streets di database alamat_db
-- File: schema_streets_table.sql

USE alamat_db;

-- 1. Tabel untuk alamat jalan yang terhubung dengan desa
CREATE TABLE IF NOT EXISTS streets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    village_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('jalan', 'gang', 'lorong', 'komplek', 'perumahan', 'jalan_raya', 'jalan_utama', 'jalan_tol', 'other') DEFAULT 'jalan',
    rt VARCHAR(10) NULL,
    rw VARCHAR(10) NULL,
    postal_code VARCHAR(10) NULL,
    latitude DECIMAL(10,8) NULL,
    longitude DECIMAL(11,8) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_village_id (village_id),
    INDEX idx_name (name),
    INDEX idx_postal_code (postal_code),
    INDEX idx_is_active (is_active),
    INDEX idx_village_name (village_id, name),
    
    CONSTRAINT fk_streets_villages FOREIGN KEY (village_id) REFERENCES villages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. View untuk mendapatkan alamat lengkap
CREATE OR REPLACE VIEW v_full_addresses AS
SELECT 
    s.id as street_id,
    s.name as street_name,
    s.type as street_type,
    s.rt,
    s.rw,
    s.postal_code as street_postal_code,
    s.latitude,
    s.longitude,
    v.id as village_id,
    v.name as village_name,
    v.postal_code as village_postal_code,
    d.id as district_id,
    d.name as district_name,
    r.id as regency_id,
    r.name as regency_name,
    p.id as province_id,
    p.name as province_name,
    CASE 
        WHEN s.rt IS NOT NULL AND s.rw IS NOT NULL THEN
            CONCAT(
                CASE s.type 
                    WHEN 'jalan' THEN 'Jl. '
                    WHEN 'gang' THEN 'Gg. '
                    WHEN 'lorong' THEN 'Lr. '
                    WHEN 'komplek' THEN 'Komplek '
                    WHEN 'perumahan' THEN 'Perum. '
                    WHEN 'jalan_raya' THEN 'Jl. Raya '
                    WHEN 'jalan_utama' THEN 'Jl. Utama '
                    WHEN 'jalan_tol' THEN 'Jl. Tol '
                    ELSE s.name
                END,
                ' RT ', s.rt, ' RW ', s.rw,
                ', ', v.name,
                ', ', d.name,
                ', ', r.name,
                ', ', p.name,
                CASE 
                    WHEN s.postal_code IS NOT NULL AND s.postal_code != '' THEN CONCAT(', ', s.postal_code)
                    WHEN v.postal_code IS NOT NULL AND v.postal_code != '' THEN CONCAT(', ', v.postal_code)
                    ELSE ''
                END
            )
        ELSE
            CONCAT(
                CASE s.type 
                    WHEN 'jalan' THEN 'Jl. '
                    WHEN 'gang' THEN 'Gg. '
                    WHEN 'lorong' THEN 'Lr. '
                    WHEN 'komplek' THEN 'Komplek '
                    WHEN 'perumahan' THEN 'Perum. '
                    WHEN 'jalan_raya' THEN 'Jl. Raya '
                    WHEN 'jalan_utama' THEN 'Jl. Utama '
                    WHEN 'jalan_tol' THEN 'Jl. Tol '
                    ELSE s.name
                END,
                ', ', v.name,
                ', ', d.name,
                ', ', r.name,
                ', ', p.name,
                CASE 
                    WHEN s.postal_code IS NOT NULL AND s.postal_code != '' THEN CONCAT(', ', s.postal_code)
                    WHEN v.postal_code IS NOT NULL AND v.postal_code != '' THEN CONCAT(', ', v.postal_code)
                    ELSE ''
                END
            )
    END as full_address
FROM streets s
JOIN villages v ON s.village_id = v.id
JOIN districts d ON v.district_id = d.id
JOIN regencies r ON d.regency_id = r.id
JOIN provinces p ON r.province_id = p.id
WHERE s.is_active = 1;

-- 3. Insert beberapa contoh data (opsional, bisa dihapus)
INSERT IGNORE INTO streets (village_id, name, type, rt, rw, postal_code) VALUES
-- Contoh untuk Jakarta (village_id perlu disesuaikan dengan data sebenarnya)
(1, 'Sudirman', 'jalan', '01', '02', '10110'),
(1, 'Thamrin', 'jalan_raya', NULL, NULL, '10250'),
(1, 'Gatot Subroto', 'jalan_utama', NULL, NULL, '10270');

-- 4. Trigger untuk update postal code otomatis dari village jika kosong
DELIMITER //

CREATE TRIGGER tr_streets_before_insert
BEFORE INSERT ON streets
FOR EACH ROW
BEGIN
    IF NEW.postal_code IS NULL OR NEW.postal_code = '' THEN
        SET NEW.postal_code = (
            SELECT postal_code 
            FROM villages 
            WHERE id = NEW.village_id 
            LIMIT 1
        );
    END IF;
END//

CREATE TRIGGER tr_streets_before_update
BEFORE UPDATE ON streets
FOR EACH ROW
BEGIN
    IF (NEW.postal_code IS NULL OR NEW.postal_code = '') AND 
       (OLD.postal_code IS NULL OR OLD.postal_code = '') THEN
        SET NEW.postal_code = (
            SELECT postal_code 
            FROM villages 
            WHERE id = NEW.village_id 
            LIMIT 1
        );
    END IF;
END//

DELIMITER ;
