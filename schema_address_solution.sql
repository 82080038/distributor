-- Solusi untuk alamat jalan yang tidak ada data streets
-- File: schema_address_solution.sql

USE distributor;

-- 1. Tambahkan kolom untuk nomor rumah/bangunan di tabel addresses
ALTER TABLE addresses 
ADD COLUMN nomor_rumah VARCHAR(20) NULL AFTER street_address,
ADD COLUMN nomor_bangunan VARCHAR(10) NULL AFTER nomor_rumah,
ADD COLUMN blok VARCHAR(20) NULL AFTER nomor_bangunan,
ADD COLUMN lantai VARCHAR(10) NULL AFTER blok,
ADD COLUMN nomor_unit VARCHAR(20) NULL AFTER lantai,
ADD COLUMN patokan_lokasi VARCHAR(100) NULL AFTER nomor_unit;

-- 2. Tambahkan kolom untuk tipe input alamat
ALTER TABLE addresses 
ADD COLUMN input_type ENUM('street_dropdown', 'manual_full', 'manual_partial') DEFAULT 'manual_full' AFTER patokan_lokasi,
ADD COLUMN is_validated TINYINT(1) NOT NULL DEFAULT 0 AFTER input_type;

-- 3. Update view untuk format alamat lengkap Indonesia
CREATE OR REPLACE VIEW v_addresses_full AS
SELECT 
    a.id,
    a.street_address,
    a.street_id,
    a.nomor_rumah,
    a.nomor_bangunan,
    a.blok,
    a.lantai,
    a.nomor_unit,
    a.patokan_lokasi,
    a.input_type,
    a.is_validated,
    s.name as street_name,
    s.type as street_type,
    s.rt as street_rt,
    s.rw as street_rw,
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
        WHEN a.input_type = 'street_dropdown' AND a.street_id IS NOT NULL THEN
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
        WHEN a.input_type = 'manual_full' THEN
            CONCAT(
                COALESCE(a.street_address, ''),
                CASE 
                    WHEN a.nomor_rumah IS NOT NULL AND a.nomor_rumah != '' THEN CONCAT(' No. ', a.nomor_rumah) ELSE '' END,
                CASE 
                    WHEN a.nomor_bangunan IS NOT NULL AND a.nomor_bangunan != '' THEN CONCAT(' ', a.nomor_bangunan) ELSE '' END,
                CASE 
                    WHEN a.blok IS NOT NULL AND a.blok != '' THEN CONCAT(' Blok ', a.blok) ELSE '' END,
                CASE 
                    WHEN a.lantai IS NOT NULL AND a.lantai != '' THEN CONCAT(' Lantai ', a.lantai) ELSE '' END,
                CASE 
                    WHEN a.nomor_unit IS NOT NULL AND a.nomor_unit != '' THEN CONCAT(' Unit ', a.nomor_unit) ELSE '' END,
                CASE WHEN a.patokan_lokasi IS NOT NULL AND a.patokan_lokasi != '' THEN CONCAT(' (', a.patokan_lokasi, ')') ELSE '' END,
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
        WHEN a.input_type = 'manual_partial' THEN
            CONCAT(
                COALESCE(a.street_address, ''),
                CASE 
                    WHEN a.nomor_rumah IS NOT NULL AND a.nomor_rumah != '' THEN CONCAT(' No. ', a.nomor_rumah) ELSE '' END,
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
        ELSE COALESCE(a.street_address, '')
    END as full_address_formatted,
    CASE 
        WHEN a.input_type = 'street_dropdown' AND a.street_id IS NOT NULL THEN 1
        WHEN a.input_type = 'manual_full' THEN 2
        WHEN a.input_type = 'manual_partial' THEN 3
        ELSE 0
    END as address_completeness_level
FROM addresses a
LEFT JOIN alamat_db.streets s ON a.street_id = s.id
LEFT JOIN alamat_db.villages v ON a.village_id = v.id
LEFT JOIN alamat_db.districts d ON a.district_id = d.id
LEFT JOIN alamat_db.regencies r ON a.regency_id = r.id
LEFT JOIN alamat_db.provinces p ON a.province_id = p.id;

-- 4. View untuk orang dengan alamat lengkap
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
    a.nomor_rumah,
    a.nomor_bangunan,
    a.blok,
    a.lantai,
    a.nomor_unit,
    a.patokan_lokasi,
    a.input_type,
    a.is_validated,
    s.name as street_name,
    s.type as street_type,
    s.rt as street_rt,
    s.rw as street_rw,
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
        WHEN a.input_type = 'street_dropdown' AND a.street_id IS NOT NULL THEN
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
        WHEN a.input_type = 'manual_full' THEN
            CONCAT(
                COALESCE(a.street_address, ''),
                CASE 
                    WHEN a.nomor_rumah IS NOT NULL AND a.nomor_rumah != '' THEN CONCAT(' No. ', a.nomor_rumah) ELSE '' END,
                CASE 
                    WHEN a.nomor_bangunan IS NOT NULL AND a.nomor_bangunan != '' THEN CONCAT(' ', a.nomor_bangunan) ELSE '' END,
                CASE 
                    WHEN a.blok IS NOT NULL AND a.blok != '' THEN CONCAT(' Blok ', a.blok) ELSE '' END,
                CASE 
                    WHEN a.lantai IS NOT NULL AND a.lantai != '' THEN CONCAT(' Lantai ', a.lantai) ELSE '' END,
                CASE 
                    WHEN a.nomor_unit IS NOT NULL AND a.nomor_unit != '' THEN CONCAT(' Unit ', a.nomor_unit) ELSE '' END,
                CASE WHEN a.patokan_lokasi IS NOT NULL AND a.patokan_lokasi != '' THEN CONCAT(' (', a.patokan_lokasi, ')') ELSE '' END,
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
        WHEN a.input_type = 'manual_partial' THEN
            CONCAT(
                COALESCE(a.street_address, ''),
                CASE 
                    WHEN a.nomor_rumah IS NOT NULL AND a.nomor_rumah != '' THEN CONCAT(' No. ', a.nomor_rumah) ELSE '' END,
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
        ELSE COALESCE(a.street_address, '')
    END as alamat_lengkap,
    CASE 
        WHEN a.input_type = 'street_dropdown' AND a.street_id IS NOT NULL THEN 1
        WHEN a.input_type = 'manual_full' THEN 2
        WHEN a.input_type = 'manual_partial' THEN 3
        ELSE 0
    END as address_completeness_level
FROM orang o
LEFT JOIN addresses a ON o.id_alamat_orang = a.id
LEFT JOIN orang_addresses oa ON o.id_alamat_orang = oa.address_id AND oa.orang_id = o.id_orang
LEFT JOIN alamat_db.streets s ON a.street_id = s.id
LEFT JOIN alamat_db.villages v ON a.village_id = v.id
LEFT JOIN alamat_db.districts d ON a.district_id = d.id
LEFT JOIN alamat_db.regencies r ON a.regency_id = r.id
LEFT JOIN alamat_db.provinces p ON a.province_id = p.id;
