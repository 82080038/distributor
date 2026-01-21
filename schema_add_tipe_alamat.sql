-- Migration untuk menambahkan field tipe_alamat ke tabel orang
-- File: schema_add_tipe_alamat.sql

USE distributor;

-- Tambahkan field tipe_alamat ke tabel orang
ALTER TABLE orang 
ADD COLUMN tipe_alamat ENUM('rumah', 'kantor', 'gudang', 'toko', 'pabrik', 'lainnya') NULL DEFAULT NULL 
AFTER postal_code;

-- Update existing records to have default tipe_alamat if they have address but no tipe_alamat
UPDATE orang 
SET tipe_alamat = 'rumah' 
WHERE (alamat IS NOT NULL AND alamat != '') 
AND tipe_alamat IS NULL;

-- Add index for better performance
ALTER TABLE orang ADD INDEX idx_tipe_alamat (tipe_alamat);
