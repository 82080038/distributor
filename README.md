# Distributor Management System - Cross-Platform Application

## 📋 Overview

Aplikasi Distributor Management System adalah sistem berbasis web untuk mengelola bisnis distributor, termasuk:

- **Manajemen Produk** - Katalog produk dengan harga dan stok
- **Manajemen Pelanggan** - Data pelanggan dan supplier  
- **Manajemen Pesanan** - Proses order dan fulfillment
- **Manajemen Pembelian** - Tracking pembelian dan purchase order
- **Laporan & Analisis** - Omzet, pesanan, SPPG, dan laporan pembelian
- **Manajemen User** - Sistem login dengan role-based access
- **🆕 Manajemen Alamat** - Sistem alamat terstruktur dengan autocomplete

## 🌟 **FITUR TERBARU - SISTEM ALAMAT TERSTRUKTUR**

### **🏠 Alamat Manager System**
Aplikasi sekarang dilengkapi dengan sistem alamat yang seragam dan modern:

#### **✅ Fitur Utama:**
- **Form Alamat Seragam** - Layout konsisten di seluruh aplikasi
- **Autocomplete Desa** - Pencarian real-time untuk desa/kelurahan
- **Cascading Selects** - Propinsi → Kabupaten → Kecamatan → Desa
- **Kode Pos Otomatis** - Terisi saat desa dipilih
- **Tipe Alamat** - Rumah, Kantor, Gudang, Toko, Pabrik, Lainnya
- **Validasi Lengkap** - Client dan server-side validation

#### **🎨 Layout Alamat:**
```
1. Combo Wilayah (paling atas):
   - Propinsi + Kabupaten/Kota
   - Kecamatan + Kelurahan/Desa (dengan autocomplete)

2. Tipe Alamat + Kode Pos:
   - Dropdown tipe alamat dengan icon
   - Kode pos otomatis dari database

3. Alamat Jalan (paling bawah):
   - Textarea untuk alamat lengkap
   - Placeholder dan petunjuk yang jelas
```

#### **📁 File Alamat Manager:**
- **`alamat_manager.php`** - File master dengan fungsi CRUD lengkap
- **`alamat_crud.php`** - Manajemen alamat terpisah
- **`schema_add_tipe_alamat.sql`** - Migration database

#### **🔧 Cara Penggunaan:**
```php
// Include file alamat manager
require_once __DIR__ . DIRECTORY_SEPARATOR . 'alamat_manager.php';

// Setup AJAX endpoints
setup_alamat_ajax_endpoints();

// Render form alamat
render_alamat_form('', $address_values, true, true, true);

// Validasi data alamat
$validation = validate_alamat_data('', true);
```

## 🚀 Quick Start (Cross-Platform)

### **Method 1: Native Setup (Recommended)**
```bash
# Requirements: PHP 7.4+, MySQL/MariaDB, Apache
# Import database: mysql -u root -p distributor < db/distribusi.sql
# Access: http://localhost/distribusi
```

### **Method 2: Manual Setup**
```bash
# Clone repository
git clone https://github.com/82080038/distributor.git
cd distribusi

# Import database
mysql -u root -p distributor < db/distribusi.sql

# Access application
# Web: http://localhost/distribusi
```

## 🏗️ Architecture

### **System Components:**
```
┌─────────────────┐    ┌─────────────────┐
│   Web Server   │    │   Database      │
│                │    │                │
│ PHP 7.4       │    │ MySQL/MariaDB   │
│ Apache 2.4     │◄──►│ Port: 3306     │
│ Port: 80       │    │                │
└─────────────────┘    └─────────────────┘
```

## 🔧 Environment Configuration

### **Auto-Detection System:**
Aplikasi otomatis mendeteksi environment:

```php
// config.php - Auto Detection
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows XAMPP
    DB_HOST = 'localhost'
    DB_PORT = 3306
    DB_USER = 'root'
} else {
    // Linux Native
    DB_HOST = 'localhost'
    DB_PORT = 3306
    DB_USER = 'root'
    // Auto-detect socket location
}
```

## 📁 Project Structure

```
distribusi/
├── 📄 README.md                 # Dokumentasi ini
├── ⚙️ config.php              # Auto-detect config
├── 📊 db/                     # Database schemas
│   ├── distribusi.sql          # Main database
│   ├── distributor.sql        # Alternative schema
│   └── alamat_db.sql          # Alamat database
├── 📝 catatan/                # Notes & parsers
├── 🌐 *.php                   # Application files
├── 📱 app.js                  # Frontend JavaScript
├── 🏠 alamat_manager.php       # Alamat manager system
├── 🏠 alamat_crud.php          # CRUD alamat
└── 📚 *.md                    # Documentation
```

## 💻 Development Workflow

### **1. Native Development (Recommended)**
```bash
# Start web server (Apache/Nginx)
# Start MySQL service
# Edit kode lokal
# Akses: http://localhost/distribusi
```

## 🗄️ Database Setup

### **Native Setup:**
```sql
-- Buat databases
CREATE DATABASE distributor;
CREATE DATABASE alamat_db;

-- Import schema
mysql -u root -p distributor < db/distribusi.sql

-- Migration untuk tipe_alamat
USE distributor;
ALTER TABLE orang 
ADD COLUMN tipe_alamat ENUM('rumah', 'kantor', 'gudang', 'toko', 'pabrik', 'lainnya') NULL DEFAULT NULL 
AFTER postal_code;
```

## 🔍 Troubleshooting

### **Port Conflicts:**
```bash
# Cek port yang digunakan
netstat -tlnp | grep :3306

# Edit config.php untuk port berbeda
define('DB_PORT', 3307);
```

### **JavaScript Errors:**
```bash
# Clear browser cache
# Hard refresh (Ctrl+Shift+R)
# Check console untuk error
```

## 🌐 Access Information

### **Native Environment:**
- **Windows (XAMPP)**: http://localhost/distribusi
- **Linux**: http://localhost/distribusi
- **Database**: localhost:3306 (root/empty password)

## 🔧 System Requirements

### **Native Setup:**
- PHP 7.4+ dengan ekstensi: mysqli, gd, zip, mbstring
- MySQL 5.7+ atau MariaDB 10.3+
- Apache 2.4+ dengan mod_rewrite
- 2GB RAM minimum

## 📱 Features & Modules

### **Core Modules:**
1. **Dashboard** - Overview sistem
2. **Products** - Manajemen produk & harga
3. **Customers** - Data pelanggan dengan alamat terstruktur
4. **Suppliers** - Data supplier/pemasok dengan alamat terstruktur
5. **Orders** - Manajemen pesanan
6. **Purchases** - Manajemen pembelian
7. **Sales** - Proses penjualan
8. **Reports** - Laporan & analisis
9. **Users** - Manajemen user & permissions
10. **Company** - Data perusahaan

### **🆕 Alamat System Features:**
- **Form Alamat Seragam** - Layout konsisten di seluruh aplikasi
- **Autocomplete Desa** - Pencarian real-time dengan dropdown
- **Cascading Selects** - 4 level wilayah otomatis
- **Tipe Alamat** - 6 tipe alamat dengan icon
- **Kode Pos Otomatis** - Terisi dari database
- **CRUD Operations** - Create, Read, Update, Delete alamat
- **Entity Linking** - Hubungkan alamat ke user/customer/supplier
- **Validation** - Client dan server-side validation

### **Advanced Features:**
- **Excel Import/Export** - Parse file Excel untuk data SPPG
- **Multi-Branch** - Support multiple cabang
- **Role-Based Access** - Owner, Admin, Staff roles
- **Real-time Updates** - AJAX-based interactions
- **Responsive Design** - Mobile-friendly interface
- **🆕 Error Handling** - Graceful handling untuk Chrome extension errors

## 🔒 Security Features

- **Session Management** - Secure login sessions
- **Input Validation** - XSS prevention
- **SQL Injection Protection** - Prepared statements
- **Role-Based Access** - Permission control
- **Password Hashing** - Secure password storage
- **🆕 Error Suppression** - Handle Chrome extension errors gracefully

## 📊 Technology Stack

### **Backend:**
- **PHP 7.4** - Server-side scripting
- **MariaDB 10.6** - Database engine
- **Apache 2.4** - Web server
- **Bootstrap 5** - CSS framework

### **Frontend:**
- **JavaScript (ES6+)** - Client-side logic
- **jQuery** - DOM manipulation
- **Flatpickr** - Date picker
- **Chart.js** - Data visualization (optional)

### **DevOps:**
- **Git** - Version control

## 🚀 Deployment

### **Development:**
```bash
# Setup local web server and database
# Import database schema
# Configure config.php for your environment
```

## 📝 Development Guidelines

### **Coding Standards:**
- **PHP**: PSR-4 coding standards
- **JavaScript**: ES6+ modern practices
- **Database**: Normalized schema with foreign keys
- **Security**: Always validate and sanitize inputs

### **Git Workflow:**
```bash
# Feature branch
git checkout -b feature/new-module

# Commit changes
git add .
git commit -m "Add new module"

# Push and merge
git push origin feature/new-module
# Create pull request
```

## 🤝 Contributing

1. **Fork** repository
2. **Create** feature branch
3. **Develop** dengan standards
4. **Test** di XAMPAMP environment
5. **Submit** pull request

## 📄 License

[Add your license information here]

## 📞 Support

- **Documentation**: Lihat file README ini
- **Issues**: Report via GitHub Issues
- **Database Setup**: Lihat schema files di folder `db/`
- **Alamat System**: Lihat `alamat_manager.php`

---

## 🎯 Quick Start Summary

```bash
# 1. Clone & CD
git clone https://github.com/82080038/distributor.git
cd distribusi

# 2. Setup database
mysql -u root -p -e "CREATE DATABASE distributor; CREATE DATABASE alamat_db;"
mysql -u root -p distributor < db/distribusi.sql

# 3. Configure web server
# Point Apache/Nginx to this folder
# Access: http://localhost/distribusi

# 4. Selesai! Ready untuk development
```

## 📁 Project Structure

```
distribusi/
├── ⚙️ config.php              # Auto-detect config
├──  .gitignore              # Git ignore file
├── 📊 db/                     # Database schemas
│   ├── distribusi.sql          # Main database
│   ├── distributor.sql        # Alternative schema
│   └── alamat_db.sql          # Alamat database
├── 📝 catatan/                # Notes & parsers
├── 🌐 *.php                   # Application files
├── 📱 app.js                  # Frontend JavaScript
├── 🏠 alamat_manager.php       # Alamat manager system
├── 🏠 alamat_crud.php          # CRUD alamat
└── 📚 *.md                    # Documentation
```

## 🆕 **Changelog & Updates**

### **Version 1.1.0 - Alamat System Integration**
- ✅ **Alamat Manager System** - Sistem alamat terstruktur
- ✅ **Autocomplete Desa** - Pencarian real-time
- ✅ **CRUD Operations** - Create, Read, Update, Delete
- ✅ **Form Seragam** - Layout konsisten di seluruh aplikasi
- ✅ **Error Handling** - Graceful Chrome extension error handling
- ✅ **Database Migration** - Field tipe_alamat untuk orang table

### **Version 1.0.0 - Base System**
- ✅ **Core Modules** - Products, Customers, Orders, Purchases
- ✅ **User Management** - Role-based access control
- ✅ **Reporting System** - Laporan dan analisis

**🎉 Aplikasi siap digunakan di Windows, Linux, atau OS apapun dengan web server dan MySQL!**
