# 🐳 Distributor Management System - Docker-Based Cross-Platform Application

## ⚠️ PENTING! Docker Development Pattern

**Aplikasi ini menggunakan Docker containerization untuk cross-platform compatibility.**

### 🎯 **Mengapa Docker?**
- **Windows = Linux = macOS** (Environment sama persis)
- **Tidak ada "works on my machine" issues**
- **Setup sekali, jalan di mana saja**
- **Focus pada coding, bukan configuration**

## 📋 Overview

Aplikasi Distributor Management System adalah sistem berbasis web untuk mengelola bisnis distributor, termasuk:

- **Manajemen Produk** - Katalog produk dengan harga dan stok
- **Manajemen Pelanggan** - Data pelanggan dan supplier  
- **Manajemen Pesanan** - Proses order dan fulfillment
- **Manajemen Pembelian** - Tracking pembelian dan purchase order
- **Laporan & Analisis** - Omzet, pesanan, SPPG, dan laporan pembelian
- **Manajemen User** - Sistem login dengan role-based access

## 🐳 Docker-Based Development

Aplikasi ini dikembangkan dengan **Docker containerization** untuk memastikan konsistensi di semua platform:

### ✅ **Keuntungan Docker:**
- **Cross-Platform**: Sama persis di Windows, Linux, macOS
- **Environment Consistent**: Tidak ada "works on my machine" issues
- **Easy Setup**: Cukup `docker-compose up`
- **Database Ready**: Otomatis import dari SQL files
- **Isolated Development**: Tidak mengganggu sistem host

## 🚀 Quick Start (Cross-Platform)

### **Method 1: One-Click Startup (Recommended)**
```bash
# Linux/macOS
./start.sh

# Windows
start.bat
```

### **Method 2: Manual Docker Setup**
```bash
# Clone repository
git clone <repository-url>
cd distribusi

# Start all containers
docker-compose up -d --build

# Access application
# Web: http://localhost:8080
# Database: http://localhost:8081 (PhpMyAdmin)
```

### **Method 3: Native Setup (Advanced)**
```bash
# Requirements: PHP 7.4+, MySQL/MariaDB, Apache
# Import database: mysql -u root -p distributor < db/distribusi.sql
# Access: http://localhost/distribusi
```

## 🏗️ Architecture

### **Container Structure:**
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Web Server   │    │   Database      │    │   Database UI   │
│                │    │                │    │                │
│ PHP 7.4       │    │ MariaDB 10.6   │    │ PhpMyAdmin      │
│ Apache 2.4     │◄──►│ Port: 3307     │    │ Port: 8081     │
│ Port: 8080     │    │                │    │                │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### **Data Persistence:**
- **Database**: Docker volume `mysql_data`
- **Application**: Live sync dari folder lokal
- **Configuration**: Auto-detect environment

## 🔧 Environment Configuration

### **Auto-Detection System:**
Aplikasi otomatis mendeteksi environment:

```php
// config.php - Auto Detection
if ($is_docker) {
    // Docker Environment
    DB_HOST = 'mysql'
    DB_PORT = 3307
    DB_USER = 'distributor_user'
} elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
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
├── 🐳 docker-compose.yml        # Container orchestration
├── 🐳 Dockerfile               # Web server build
├── ⚙️ config.php              # Auto-detect config
├── 📊 db/                     # Database schemas
│   ├── distribusi.sql          # Main database
│   └── distributor.sql        # Alternative schema
├── 🗃️ mysql-init/              # Database init scripts
├── 📝 catatan/                # Notes & parsers
├── 🌐 *.php                   # Application files
├── 📱 app.js                  # Frontend JavaScript
└── 📚 *.md                    # Documentation
```

## 💻 Development Workflow

### **1. Development di Docker (Recommended)**
```bash
# Start containers
docker-compose up -d

# Edit kode lokal (auto-sync ke container)
# Akses: http://localhost:8080

# View logs
docker-compose logs -f web
```

### **2. Development Native (Alternative)**

#### **Windows dengan XAMPP:**
```bash
# Stop Docker
docker-compose down

# Start XAMPP
# Akses: http://localhost/distribusi
```

#### **Linux dengan Native MySQL:**
```bash
# Stop Docker  
docker-compose down

# Start MySQL native
sudo systemctl start mysql

# Import database (jika perlu)
mysql -u root -p distributor < db/distribusi.sql

# Akses: http://localhost/distribusi
```

## 🗄️ Database Setup

### **Docker (Otomatis):**
- **Database 1**: `distributor` (di-import dari `db/distribusi.sql`)
- **Database 2**: `alamat_db` (dibuat kosong)
- **Access**: PhpMyAdmin di http://localhost:8081

### **Native Setup:**
```sql
-- Buat databases
CREATE DATABASE distributor;
CREATE DATABASE alamat_db;

-- Import schema
mysql -u root -p distributor < db/distribusi.sql
```

## 🔍 Troubleshooting

### **Port Conflicts:**
```bash
# Cek port yang digunakan
ss -tlnp | grep :3306

# Ganti port di docker-compose.yml
ports:
  - "3307:3306"  # Gunakan port lain
```

### **Container Issues:**
```bash
# Lihat semua containers
docker-compose ps

# Restart container
docker-compose restart web

# Lihat logs
docker-compose logs web
```

### **Database Connection:**
```bash
# Test koneksi database
docker exec web php -r "require_once 'config.php'; echo 'Connection: ' . (\$conn->connect_error ? 'FAILED' : 'OK');"
```

## 🌐 Access Information

### **Docker Environment:**
- **Aplikasi**: http://localhost:8080
- **PhpMyAdmin**: http://localhost:8081
  - Server: mysql
  - Username: root
  - Password: (kosong)

### **Native Environment:**
- **Windows (XAMPP)**: http://localhost/distribusi
- **Linux**: http://localhost/distribusi
- **Database**: localhost:3306 (root/empty password)

## 🔧 System Requirements

### **Docker (Recommended):**
- Docker Desktop (Windows/Mac) atau Docker Engine (Linux)
- 4GB RAM minimum
- 2GB disk space

### **Native Setup:**
- PHP 7.4+ dengan ekstensi: mysqli, gd, zip, mbstring
- MySQL 5.7+ atau MariaDB 10.3+
- Apache 2.4+ dengan mod_rewrite
- 2GB RAM minimum

## 📱 Features & Modules

### **Core Modules:**
1. **Dashboard** - Overview sistem
2. **Products** - Manajemen produk & harga
3. **Customers** - Data pelanggan
4. **Suppliers** - Data supplier/pemasok
5. **Orders** - Manajemen pesanan
6. **Purchases** - Manajemen pembelian
7. **Sales** - Proses penjualan
8. **Reports** - Laporan & analisis
9. **Users** - Manajemen user & permissions
10. **Company** - Data perusahaan

### **Advanced Features:**
- **Excel Import/Export** - Parse file Excel untuk data SPPG
- **Multi-Branch** - Support multiple cabang
- **Role-Based Access** - Owner, Admin, Staff roles
- **Real-time Updates** - AJAX-based interactions
- **Responsive Design** - Mobile-friendly interface

## 🔒 Security Features

- **Session Management** - Secure login sessions
- **Input Validation** - XSS prevention
- **SQL Injection Protection** - Prepared statements
- **Role-Based Access** - Permission control
- **Password Hashing** - Secure password storage

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
- **Docker & Docker Compose** - Containerization
- **Git** - Version control
- **PhpMyAdmin** - Database management

## 🚀 Deployment

### **Development:**
```bash
docker-compose up -d --build
```

### **Production:**
```bash
# Build production image
docker build -t distributor:prod .

# Run with production settings
docker run -d -p 80:80 distributor:prod
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
4. **Test** di Docker environment
5. **Submit** pull request

## 📄 License

[Add your license information here]

## 📞 Support

- **Documentation**: Lihat file `DOCKER_SETUP.md`
- **Issues**: Report via GitHub Issues
- **Database Setup**: Lihat `DATABASE_SETUP.md`

---

## 🎯 Quick Start Summary

```bash
# 1. Clone & CD
git clone <repo-url>
cd distribusi

# 2. One-Click Startup
# Linux/macOS:
./start.sh

# Windows:
start.bat

# 3. Wait 1-2 minutes
# Database otomatis di-import

# 4. Akses aplikasi
# Web: http://localhost:8080
# DB: http://localhost:8081

# 5. Selesai! Ready untuk development
```

## 📁 Project Structure

```
distribusi/
├── 🚀 start.sh                # Linux/macOS startup script
├── 🚀 start.bat               # Windows startup script
├── 🐳 docker-compose.yml      # Container orchestration
├── 🐳 Dockerfile               # Web server build
├── ⚙️ config.php              # Auto-detect config
├── 📊 composer.json           # PHP dependencies
├── 🚫 .gitignore              # Git ignore file
├── 📊 db/                     # Database schemas
│   ├── distribusi.sql          # Main database
│   └── distributor.sql        # Alternative schema
├── 🗃️ mysql-init/              # Database init scripts
├── 📝 catatan/                # Notes & parsers
├── 🌐 *.php                   # Application files
├── 📱 app.js                  # Frontend JavaScript
└── 📚 *.md                    # Documentation
```

**🎉 Aplikasi siap digunakan di Windows, Linux, macOS, atau OS apapun dengan Docker!**
