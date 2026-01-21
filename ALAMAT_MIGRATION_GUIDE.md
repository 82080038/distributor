# Instruksi Menambahkan Field Tipe Alamat

## Langkah 1: Jalankan Migration SQL

Jalankan perintah berikut di MySQL CLI atau phpMyAdmin:

```sql
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
```

## Langkah 2: Verifikasi

Setelah migration berhasil, field `tipe_alamat` akan ditambahkan ke tabel `orang` dengan struktur:

- Field: `tipe_alamat`
- Type: `ENUM('rumah', 'kantor', 'gudang', 'toko', 'pabrik', 'lainnya')`
- Null: YES
- Default: NULL

## Langkah 3: Testing

Setelah migration, sistem alamat baru akan berfungsi dengan:

1. **Combo wilayah di paling atas**: Propinsi → Kabupaten → Kecamatan → Desa
2. **Tipe Alamat di tengah**: Setelah combo wilayah, sebelum alamat jalan
3. **Alamat Jalan di bawah**: Field textarea untuk alamat lengkap
4. **Kode Pos otomatis**: Terisi saat desa dipilih
5. **Autocomplete desa**: Pencarian real-time untuk desa

## Perubahan yang Sudah Dilakukan

✅ Template alamat seragam (`address_helper.php`)
✅ Posisi field: Combo wilayah → Tipe Alamat → Alamat Jalan
✅ Autocomplete desa dengan dropdown
✅ Validasi lengkap untuk semua field
✅ Update semua file PHP (register, profile, customers, suppliers)
✅ Kode pos otomatis dari database

## File yang Diperbarui

- `address_helper.php` - Template alamat seragam
- `register.php` - Pendaftaran user
- `profile.php` & `profile_view.php` - Profil user
- `customers.php` & `customers_view.php` - Data pembeli
- `suppliers.php` & `suppliers_view.php` - Data pemasok
- `schema_add_tipe_alamat.sql` - Migration script

## Cara Menjalankan Migration

### Via MySQL CLI:
```bash
mysql -u root -p distributor < schema_add_tipe_alamat.sql
```

### Via phpMyAdmin:
1. Buka phpMyAdmin
2. Pilih database `distributor`
3. Klik tab "SQL"
4. Copy-paste SQL di atas
5. Klik "Go"

Setelah migration berhasil, sistem alamat baru siap digunakan!
