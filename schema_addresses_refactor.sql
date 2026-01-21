-- Migration untuk memodifikasi tabel addresses agar merujuk ke streets
-- File: schema_addresses_refactor.sql

USE distributor;

-- 1. Tambahkan kolom street_id ke tabel addresses
ALTER TABLE addresses 
ADD COLUMN street_id INT UNSIGNED NULL AFTER id,
ADD INDEX idx_street_id (street_id),
ADD CONSTRAINT fk_addresses_street FOREIGN KEY (street_id) REFERENCES alamat_db.streets(id) ON DELETE SET NULL;

-- 2. Update data addresses yang sudah ada untuk tetap bekerja
-- Jika ada street_address text, coba match dengan nama street di tabel streets
UPDATE addresses a 
SET a.street_id = (
    SELECT s.id 
    FROM alamat_db.streets s 
    WHERE s.village_id = a.village_id 
    AND (
        s.name LIKE CONCAT('%', a.street_address, '%') OR
        a.street_address LIKE CONCAT('%', s.name, '%')
    )
    LIMIT 1
)
WHERE a.street_address IS NOT NULL 
AND a.street_address != ''
AND a.village_id IS NOT NULL
AND a.street_id IS NULL;

-- 3. Buat view baru untuk alamat lengkap dengan streets
CREATE OR REPLACE VIEW v_addresses_full AS
SELECT 
    a.id,
    a.street_address,
    a.street_id,
    s.name as street_name,
    s.type as street_type,
    s.rt,
    s.rw,
    a.province_id,
    p.name as province_name,
    a.regency_id,
    r.name as regency_name,
    a.district_id,
    d.name as district_name,
    a.village_id,
    v.name as village_name,
    COALESCE(a.postal_code, v.postal_code, s.postal_code) as postal_code,
    a.address_type,
    a.is_primary,
    a.created_at,
    a.updated_at,
    CASE 
        WHEN a.street_id IS NOT NULL THEN
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
                    ELSE a.street_address
                END,
                s.name,
                CASE WHEN s.rt IS NOT NULL THEN CONCAT(' RT ', s.rt) ELSE '' END,
                CASE WHEN s.rw IS NOT NULL THEN CONCAT(' RW ', s.rw) ELSE '' END,
                ', ', v.name,
                ', ', d.name,
                ', ', r.name,
                ', ', p.name,
                CASE 
                    WHEN COALESCE(a.postal_code, v.postal_code, s.postal_code) IS NOT NULL 
                    AND COALESCE(a.postal_code, v.postal_code, s.postal_code) != '' 
                    THEN CONCAT(', ', COALESCE(a.postal_code, v.postal_code, s.postal_code))
                    ELSE ''
                END
            )
        WHEN a.street_address IS NOT NULL AND a.street_address != '' THEN
            CONCAT(
                a.street_address,
                ', ', v.name,
                ', ', d.name,
                ', ', r.name,
                ', ', p.name,
                CASE 
                    WHEN COALESCE(a.postal_code, v.postal_code) IS NOT NULL 
                    AND COALESCE(a.postal_code, v.postal_code) != '' 
                    THEN CONCAT(', ', COALESCE(a.postal_code, v.postal_code))
                    ELSE ''
                END
            )
        ELSE
            CONCAT(
                v.name,
                ', ', d.name,
                ', ', r.name,
                ', ', p.name,
                CASE 
                    WHEN COALESCE(a.postal_code, v.postal_code) IS NOT NULL 
                    AND COALESCE(a.postal_code, v.postal_code) != '' 
                    THEN CONCAT(', ', COALESCE(a.postal_code, v.postal_code))
                    ELSE ''
                END
            )
    END as full_address_formatted
FROM addresses a
LEFT JOIN alamat_db.streets s ON a.street_id = s.id
LEFT JOIN alamat_db.villages v ON a.village_id = v.id
LEFT JOIN alamat_db.districts d ON a.district_id = d.id
LEFT JOIN alamat_db.regencies r ON a.regency_id = r.id
LEFT JOIN alamat_db.provinces p ON a.province_id = p.id;

-- 4. Update view orang dengan alamat untuk menggunakan streets
CREATE OR REPLACE VIEW v_orang_with_address AS
SELECT 
    o.id_orang,
    o.nama_lengkap,
    o.alamat as alamat_legacy,
    o.kontak,
    o.id_alamat_orang,
    oa.address_type,
    a.street_address,
    a.street_id,
    s.name as street_name,
    s.type as street_type,
    s.rt,
    s.rw,
    a.province_id,
    p.name as province_name,
    a.regency_id,
    r.name as regency_name,
    a.district_id,
    d.name as district_name,
    a.village_id,
    v.name as village_name,
    COALESCE(a.postal_code, v.postal_code, s.postal_code) as postal_code,
    o.is_supplier,
    o.is_customer,
    o.is_active,
    o.created_at,
    o.updated_at,
    CASE 
        WHEN a.street_id IS NOT NULL THEN
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
                    ELSE a.street_address
                END,
                s.name,
                CASE WHEN s.rt IS NOT NULL THEN CONCAT(' RT ', s.rt) ELSE '' END,
                CASE WHEN s.rw IS NOT NULL THEN CONCAT(' RW ', s.rw) ELSE '' END,
                ', ', v.name,
                ', ', d.name,
                ', ', r.name,
                ', ', p.name,
                CASE 
                    WHEN COALESCE(a.postal_code, v.postal_code, s.postal_code) IS NOT NULL 
                    AND COALESCE(a.postal_code, v.postal_code, s.postal_code) != '' 
                    THEN CONCAT(', ', COALESCE(a.postal_code, v.postal_code, s.postal_code))
                    ELSE ''
                END
            )
        WHEN a.street_address IS NOT NULL AND a.street_address != '' THEN
            CONCAT(
                a.street_address,
                ', ', v.name,
                ', ', d.name,
                ', ', r.name,
                ', ', p.name,
                CASE 
                    WHEN COALESCE(a.postal_code, v.postal_code) IS NOT NULL 
                    AND COALESCE(a.postal_code, v.postal_code) != '' 
                    THEN CONCAT(', ', COALESCE(a.postal_code, v.postal_code))
                    ELSE ''
                END
            )
        ELSE
            CONCAT(
                v.name,
                ', ', d.name,
                ', ', r.name,
                ', ', p.name,
                CASE 
                    WHEN COALESCE(a.postal_code, v.postal_code) IS NOT NULL 
                    AND COALESCE(a.postal_code, v.postal_code) != '' 
                    THEN CONCAT(', ', COALESCE(a.postal_code, v.postal_code))
                    ELSE ''
                END
            )
    END as alamat_lengkap
FROM orang o
LEFT JOIN addresses a ON o.id_alamat_orang = a.id
LEFT JOIN orang_addresses oa ON o.id_alamat_orang = oa.address_id AND oa.orang_id = o.id_orang
LEFT JOIN alamat_db.streets s ON a.street_id = s.id
LEFT JOIN alamat_db.villages v ON a.village_id = v.id
LEFT JOIN alamat_db.districts d ON a.district_id = d.id
LEFT JOIN alamat_db.regencies r ON a.regency_id = r.id
LEFT JOIN alamat_db.provinces p ON a.province_id = p.id;
