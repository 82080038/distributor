-- Migration untuk menambahkan id_alamat_orang ke tabel orang
-- File: schema_orang_address_migration.sql

USE distributor;

-- 1. Tambahkan kolom id_alamat_orang ke tabel orang
ALTER TABLE orang 
ADD COLUMN id_alamat_orang INT UNSIGNED NULL AFTER alamat,
ADD INDEX idx_id_alamat_orang (id_alamat_orang),
ADD CONSTRAINT fk_orang_address FOREIGN KEY (id_alamat_orang) REFERENCES addresses(id) ON DELETE SET NULL;

-- 2. Update data orang yang sudah ada untuk menggunakan alamat dari tabel addresses jika ada
-- Migrasi data dari orang_addresses ke id_alamat_orang (hanya untuk alamat utama)
UPDATE orang o 
SET o.id_alamat_orang = (
    SELECT oa.address_id 
    FROM orang_addresses oa 
    WHERE oa.orang_id = o.id_orang 
    AND oa.is_active = 1 
    ORDER BY oa.created_at ASC 
    LIMIT 1
)
WHERE EXISTS (
    SELECT 1 FROM orang_addresses oa2 
    WHERE oa2.orang_id = o.id_orang 
    AND oa2.is_active = 1
);

-- 3. Buat view untuk menampilkan data orang dengan alamat lengkap
CREATE OR REPLACE VIEW v_orang_with_address AS
SELECT 
    o.id_orang,
    o.nama_lengkap,
    o.alamat as alamat_legacy,
    o.kontak,
    o.id_alamat_orang,
    oa.address_type,
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
    o.is_supplier,
    o.is_customer,
    o.is_active,
    o.created_at,
    o.updated_at,
    CASE 
        WHEN o.id_alamat_orang IS NOT NULL THEN
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
            )
        ELSE o.alamat
    END as alamat_lengkap
FROM orang o
LEFT JOIN addresses a ON o.id_alamat_orang = a.id
LEFT JOIN orang_addresses oa ON o.id_alamat_orang = oa.address_id AND oa.orang_id = o.id_orang
LEFT JOIN alamat_db.provinces p ON a.province_id = p.id
LEFT JOIN alamat_db.regencies r ON a.regency_id = r.id  
LEFT JOIN alamat_db.districts d ON a.district_id = d.id
LEFT JOIN alamat_db.villages v ON a.village_id = v.id;

-- 4. Update trigger untuk otomatis set id_alamat_orang saat ada alamat baru
DELIMITER //

CREATE TRIGGER tr_orang_addresses_after_insert
AFTER INSERT ON orang_addresses
FOR EACH ROW
BEGIN
    DECLARE address_count INT;
    
    -- Hitung jumlah alamat aktif untuk orang ini
    SELECT COUNT(*) INTO address_count
    FROM orang_addresses 
    WHERE orang_id = NEW.orang_id AND is_active = 1;
    
    -- Update id_alamat_orang di tabel orang jika ini adalah alamat pertama atau primary
    IF NEW.is_primary = 1 OR address_count = 1 THEN
        UPDATE orang 
        SET id_alamat_orang = NEW.address_id 
        WHERE id_orang = NEW.orang_id;
    END IF;
END//

CREATE TRIGGER tr_orang_addresses_after_update
AFTER UPDATE ON orang_addresses
FOR EACH ROW
BEGIN
    -- Update id_alamat_orang jika alamat diubah menjadi primary
    IF NEW.is_primary = 1 AND OLD.is_primary != 1 THEN
        UPDATE orang 
        SET id_alamat_orang = NEW.address_id 
        WHERE id_orang = NEW.orang_id;
    END IF;
END//

DELIMITER ;
