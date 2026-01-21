# 📋 Panduan CRUD Alamat Manager

## 🎯 **Fitur CRUD Alamat**

File `alamat_manager.php` sekarang mendukung operasi CRUD lengkap untuk manajemen alamat:

### 📝 **CRUD Operations**

#### 1. **CREATE** - Buat Alamat Baru
```php
$data = [
    'street_address' => 'Jl. Merdeka No. 123',
    'province_id' => 11,
    'regency_id' => 1101,
    'district_id' => 1101010,
    'village_id' => 1101010001,
    'postal_code' => '12345',
    'address_type' => 'personal'
];
$result = create_alamat($data, $conn);
```

#### 2. **READ** - Baca Data Alamat
```php
// Get alamat by ID
$alamat = get_alamat_by_id($alamat_id, $conn);

// Get semua alamat untuk entity
$alamats = get_alamats_by_entity($user_id, 'user', $conn);

// Get alamat utama
$primary = get_primary_alamat($user_id, 'user', $conn);
```

#### 3. **UPDATE** - Update Alamat
```php
$data = [
    'street_address' => 'Jl. Sudirman No. 456',
    'province_id' => 12,
    // ... data lainnya
];
$result = update_alamat($alamat_id, $data, $conn);
```

#### 4. **DELETE** - Hapus Alamat
```php
$result = delete_alamat($alamat_id, $conn);
// Soft delete - menonaktifkan alamat
```

### 🔗 **Additional Operations**

#### 5. **LINK** - Hubungkan Alamat ke Entity
```php
$result = link_alamat_to_entity($user_id, $alamat_id, 'user', 'personal', $conn);
```

#### 6. **SET PRIMARY** - Set Alamat Utama
```php
$result = set_primary_alamat($user_id, $alamat_id, 'user', $conn);
```

### 🌐 **AJAX Endpoints**

Semua operasi CRUD tersedia via AJAX:

#### **Create Alamat**
```
POST /alamat_crud.php?alamat_crud=1&action=create
```
Data:
```json
{
    "street_address": "Jl. Merdeka No. 123",
    "province_id": 11,
    "regency_id": 1101,
    "district_id": 1101010,
    "village_id": 1101010001,
    "postal_code": "12345",
    "address_type": "personal"
}
```

#### **Update Alamat**
```
POST /alamat_crud.php?alamat_crud=1&action=update
```
Data:
```json
{
    "alamat_id": 123,
    "street_address": "Jl. Sudirman No. 456",
    // ... data lainnya
}
```

#### **Delete Alamat**
```
POST /alamat_crud.php?alamat_crud=1&action=delete
```
Data:
```json
{
    "alamat_id": 123
}
```

#### **Get Alamat**
```
GET /alamat_crud.php?alamat_crud=1&action=get&alamat_id=123
```

#### **List Alamat**
```
GET /alamat_crud.php?alamat_crud=1&action=list&entity_type=user&entity_id=1
```

#### **Link Alamat**
```
POST /alamat_crud.php?alamat_crud=1&action=link
```
Data:
```json
{
    "entity_id": 1,
    "alamat_id": 123,
    "entity_type": "user",
    "address_type": "personal"
}
```

#### **Set Primary**
```
POST /alamat_crud.php?alamat_crud=1&action=set_primary
```
Data:
```json
{
    "entity_id": 1,
    "alamat_id": 123,
    "entity_type": "user"
}
```

### 📊 **Response Format**

Semua AJAX responses memiliki format yang konsisten:

#### **Success Response**
```json
{
    "success": true,
    "message": "Alamat berhasil dibuat",
    "alamat_id": 123
}
```

#### **Error Response**
```json
{
    "success": false,
    "message": "Gagal membuat alamat"
}
```

#### **Data Response**
```json
{
    "success": true,
    "data": {
        "id": 123,
        "street_address": "Jl. Merdeka No. 123",
        "province_name": "DKI Jakarta",
        // ... data lengkap
    }
}
```

### 🗄️ **Database Schema**

CRUD alamat menggunakan tabel berikut:

#### **addresses** (tabel utama)
- `id` - Primary Key
- `street_address` - Alamat jalan
- `province_id` - ID provinsi
- `regency_id` - ID kabupaten
- `district_id` - ID kecamatan
- `village_id` - ID desa
- `postal_code` - Kode pos
- `address_type` - Tipe alamat (personal, office, warehouse, dll)
- `is_primary` - Apakah alamat utama
- `created_at` - Waktu dibuat
- `updated_at` - Waktu diupdate

#### **orang_addresses** (tabel penghubung)
- `id` - Primary Key
- `orang_id` - ID orang/entity
- `address_id` - ID alamat
- `address_type` - Tipe alamat untuk entity
- `is_active` - Status aktif
- `created_at` - Waktu dibuat

### 🎯 **Tipe Alamat yang Didukung**

- **personal** - Alamat pribadi
- **office** - Alamat kantor
- **warehouse** - Alamat gudang
- **pickup** - Titik pickup
- **delivery** - Titik delivery
- **other** - Lainnya

### 📱 **Implementasi UI**

File implementasi lengkap:
- **`alamat_crud.php`** - Controller untuk CRUD alamat
- **`alamat_crud_view.php`** - View dengan tabel dan modal CRUD

### 🔧 **Setup di File Lain**

Untuk menggunakan CRUD alamat di file lain:

```php
<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'alamat_manager.php';

// Setup AJAX endpoints
setup_alamat_ajax_endpoints();

// Contoh: Buat alamat baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_alamat'])) {
    $data = [
        'street_address' => clean($_POST['street_address']),
        'province_id' => (int)$_POST['province_id'],
        // ... data lainnya
    ];
    
    $result = create_alamat($data, $conn);
    
    if ($result['success']) {
        // Hubungkan ke entity
        link_alamat_to_entity($user_id, $result['alamat_id'], 'user', 'personal', $conn);
    }
}
?>
```

### 🎨 **Fitur UI CRUD**

#### **Table Features**
- **Sorting** - Klik header untuk mengurutkan
- **Filtering** - Filter berdasarkan tipe alamat
- **Pagination** - Navigasi halaman
- **Search** - Pencarian alamat
- **Status Badge** - Indikator aktif/nonaktif
- **Primary Badge** - Indikator alamat utama

#### **Modal Features**
- **Form Validation** - Validasi client dan server
- **Autocomplete** - Pencarian desa otomatis
- **Dynamic Loading** - Load data wilayah dinamis
- **Error Handling** - Pesan error yang jelas
- **Success Feedback** - Notifikasi sukses

#### **Action Buttons**
- **Edit** - Edit alamat
- **Delete** - Hapus alamat (soft delete)
- **Set Primary** - Jadikan alamat utama
- **Link** - Hubungkan ke entity
- **View** - Lihat detail alamat

### 🔄 **Workflow CRUD**

1. **Create**:
   - User klik "Tambah Alamat"
   - Modal form muncul
   - User isi form alamat
   - Submit → Create alamat → Link ke entity

2. **Read**:
   - Load data alamat via AJAX
   - Tampilkan di table
   - Detail alamat dengan format lengkap

3. **Update**:
   - User klik "Edit"
   - Load data alamat ke modal
   - User edit data
   - Submit → Update alamat

4. **Delete**:
   - User klik "Delete"
   - Konfirmasi modal muncul
   - User konfirmasi → Soft delete

### 📝 **Best Practices**

1. **Validation** - Selalu validasi data di client dan server
2. **Error Handling** - Tangani error dengan baik
3. **User Feedback** - Berikan feedback yang jelas
4. **Security** - Sanitasi input data
5. **Performance** - Gunakan caching untuk data wilayah
6. **Consistency** - Format response yang konsisten

### 🚀 **Advanced Features**

#### **Bulk Operations**
```php
// Bulk update alamat
foreach ($alamat_ids as $id) {
    update_alamat($id, $data, $conn);
}
```

#### **Import/Export**
```php
// Export alamat ke CSV
$alamats = get_alamats_by_entity($user_id, 'user', $conn);
export_to_csv($alamats, 'alamats.csv');
```

#### **Duplicate Detection**
```php
// Cek alamat duplikat
if (is_duplicate_alamat($data, $conn)) {
    return ['success' => false, 'message' => 'Alamat sudah ada'];
}
```

### 📞 **Support**

Untuk bantuan lebih lanjut:
1. Periksa console browser untuk error JavaScript
2. Verifikasi struktur database
3. Cek log error PHP
4. Test dengan data dummy
5. Debug step by step

### 🎯 **Next Steps**

1. Implementasi di semua module
2. Add advanced filtering
3. Implementasi bulk operations
4. Add import/export functionality
5. Mobile responsive design
