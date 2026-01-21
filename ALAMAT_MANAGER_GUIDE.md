# 📋 Panduan Penggunaan Alamat Manager

## 🎯 **Tujuan**
File `alamat_manager.php` adalah file khusus untuk mengelola semua fungsi alamat yang bisa di-include ke berbagai file PHP. Ini membuat kode lebih modular dan mudah dikelola.

## 📁 **File yang Tersedia**

### File Utama:
- **`alamat_manager.php`** - File master dengan semua fungsi alamat
- **`register.php`** - Sudah diperbarui menggunakan alamat_manager
- **`profile_new.php`** - Profil user baru dengan alamat_manager
- **`profile_view_new.php` - View profil baru
- **`customers_new.php`** - Data pembeli baru dengan alamat_manager
- **customers_view_new.php` - View pembeli baru

### File Lama (untuk referensi):
- **`address_helper.php` - File helper lama (bisa dihapus)
- **`register.php` (versi lama)
- **`profile.php` (versi lama)
- **`customers.php` (versi lama)

## 🔧 **Cara Penggunaan**

### 1. Include File
```php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'alamat_manager.php';
```

### 2. Setup AJAX Endpoints
```php
// Setup AJAX endpoints untuk alamat
setup_alamat_ajax_endpoints();
```

### 3. Render Form Alamat
```php
$address_values = [
    'province_id' => $data['province_id'] ?? 0,
    'regency_id' => $data['regency_id'] ?? 0,
    'district_id' => $data['district_id'] ?? 0,
    'village_id' => $data['village_id'] ?? 0,
    'street_address' => $data['alamat'] ?? '',
    'postal_code' => $data['postal_code'] ?? '',
    'tipe_alamat' => $data['tipe_alamat'] ?? ''
];
render_alamat_form('', $address_values, true, true, true);
```

### 4. Validasi Data Alamat
```php
$address_validation = validate_alamat_data('', true);
if (!$address_validation['valid']) {
    $error = 'Alamat belum lengkap: ' . implode(', ', $address_validation['errors']);
} else {
    $alamat_data = $address_validation['data'];
    // Proses data alamat...
}
```

### 5. Render JavaScript
```php
<?php render_alamat_script(''); ?>
```

### 6. Load Data Alamat
```php
$alamat_data = load_alamat_by_entity($user_id, 'user', $conn);
```

## 🎨 **Fungsi yang Tersedia**

### 📝 **Render Functions**
- `render_alamat_form()` - Render form alamat lengkap dengan card
- `render_alamat_script()` - Render JavaScript untuk autocomplete

### ✅ **Validation Functions**
- `validate_alamat_data()` - Validasi data alamat dari POST
- `setup_alamat_ajax_endpoints()` - Setup AJAX endpoints

### 🔄 **Data Functions**
- `load_alamat_by_entity()` - Load data alamat berdasarkan entity
- `get_kode_pos_by_desa()` - Ambil kode pos dari ID desa
- `format_alamat_lengkap()` - Format alamat untuk ditampilkan

## 🎛 **Parameter render_alamat_form()**

```php
render_alamat_form($prefix, $values, $required, $show_tipe_alamat, $show_kode_pos)
```

- `$prefix` - Prefix untuk nama field (default: '')
- `$values` - Array nilai default untuk field
- `$required` - Apakah field wajib diisi (default: true)
- `$show_tipe_alamat` - Apakah menampilkan field tipe alamat (default: true)
- `$show_kode_pos` - Apakah menampilkan field kode pos (default: true)

## 🎯 **Layout Form**

Form alamat menggunakan layout berikut:

1. **Card Header** - "Informasi Alamat" dengan icon
2. **Combo Wilayah** (baris pertama):
   - Propinsi + Kabupaten/Kota
3. **Combo Wilayah** (baris kedua):
   - Kecamatan + Kelurahan/Desa (dengan autocomplete)
4. **Tipe Alamat + Kode Pos** (baris ketiga, opsional)
5. **Alamat Jalan** (baris keempat, textarea)

## 🔄 **Autocomplete Desa**

- Ketik minimal 2 karakter untuk menampilkan dropdown
- Menampilkan nama desa + kode pos (jika ada)
- Otomatis mengisi field kode pos saat desa dipilih
- Cache data desa per kecamatan untuk performa

## 📊 **Database Migration**

Jalankan migration untuk menambah field `tipe_alamat`:

```sql
ALTER TABLE orang 
ADD COLUMN tipe_alamat ENUM('rumah', 'kantor', 'gudang', 'toko', 'pabrik', 'lainnya') NULL DEFAULT NULL 
AFTER postal_code;
```

## 🚀 **Migrasi dari File Lama**

### Langkah 1: Backup file lama
```bash
cp profile.php profile_backup.php
cp customers.php customers_backup.php
cp suppliers.php suppliers_backup.php
```

### Langkah 2: Ganti dengan file baru
```bash
cp profile_new.php profile.php
cp customers_new.php customers.php
# Lakukan hal yang sama untuk suppliers
```

### Langkah 3: Update view files
```bash
cp profile_view_new.php profile_view.php
cp customers_view_new.php customers_view.php
# Lakukan hal yang sama untuk suppliers
```

## 🎨 **Keuntungan**

✅ **Modular** - Semua fungsi alamat dalam 1 file  
✅ **Konsisten** - Layout alamat seragam di seluruh aplikasi  
✅ **Mudah Dikelola** - Update di 1 file saja  
✅ **Reusable** - Bisa di-include ke mana saja  
✅ **Validasi Terpusat** - Logic validasi yang sama  
✅ **AJAX Terpusat** - Endpoints yang sama untuk semua file  

## 🔄 **Contoh Implementasi Lengkap**

Lihat file-file berikut untuk implementasi lengkap:
- `register.php` - Contoh implementasi di form registrasi
- `profile_new.php` - Contoh implementasi di profil user
- `customers_new.php` - Contoh implementasi di data master

## 📞 **Support**

Jika ada masalah atau pertanyaan:
1. Periksa apakah file `alamat_manager.php` sudah di-include
2. Pastikan `setup_alamat_ajax_endpoints()` dipanggil
3. Cek console browser untuk error JavaScript
4. Verifikasi struktur database sudah sesuai migration
