# 🐳 Automatic Port Detection - Quick Guide

## ⚠️ PENTING: Port Detection Otomatis

**Aplikasi sekarang bisa otomatis menemukan port MySQL yang tersedia!**

## 🎯 Cara Menggunakan Port Detection

### **1. Sebelum Development:**
```bash
# Di folder distribusi, jalankan:
php port_detector.php
```

### **2. Output Examples:**

#### **Port 3307 Tersedia:**
```
🎯 Found working MySQL port: 3307

🐳 Docker Configuration:
  Update docker-compose.yml ports:
    ports:
      - "3307:3306"

  Then restart: docker-compose up -d
```

#### **Port 3306 Tersedia:**
```
🎯 Found working MySQL port: 3306

🪟 Windows Configuration:
  Update config.php DB_PORT to: 3306
  Or change XAMPP MySQL port to: 3306

🐧 Linux Configuration:
  Update config.php DB_PORT to: 3306
  Or change MySQL service port to: 3306
```

## 🔧 Konfigurasi Otomatis di Aplikasi

### **Config.php - Auto Detection:**
```php
// Fungsi otomatis cek port
function find_available_mysql_port($default_port = 3307) {
    $ports_to_try = [$default_port, 3306, 3308, 3309];
    foreach ($ports_to_try as $port) {
        if (test_mysql_connection($port)) {
            return $port;
        }
    }
    return $default_port;
}

// Konfigurasi otomatis
if ($is_docker) {
    $available_port = find_available_mysql_port(3307);
    define('DB_PORT', $available_port);
    error_log("Docker: Using MySQL port " . $available_port);
}
```

### **Environment Detection:**
- **Docker**: Cek port 3307, 3306, 3308, 3309
- **Windows**: Cek port 3306, 3307, 3308
- **Linux**: Cek port 3306, 3307, 3308 + socket detection

## 📱 Workflow Development

### **Setup Awal (Semua Platform):**
```bash
# 1. Clone repository
git clone https://github.com/82080038/distributor.git
cd distribusi

# 2. Cek port yang tersedia
php port_detector.php

# 3. Start Docker (port otomatis terdeteksi)
docker-compose up -d --build

# 4. Akses aplikasi
# Web: http://localhost:8080
# Database: http://localhost:8081
```

### **Jika Port Conflict:**
```bash
# Jalankan port detector lagi
php port_detector.php

# Aplikasi akan menunjukkan port alternatif
# Contoh: "Port 3308 tersedia, gunakan port 3308"

# Update konfigurasi sesuai saran
# Docker: Edit docker-compose.yml ports
# Windows: Edit config.php DB_PORT
# Linux: Edit config.php DB_PORT
```

## 🎯 Keuntungan Port Detection

✅ **No More Manual Configuration**  
✅ **Automatic Port Resolution**  
✅ **Cross-Platform Compatible**  
✅ **Easy Troubleshooting**  
✅ **Team Collaboration Ready**  

## 📚 Dokumentasi Lengkap

- **`PORT_DETECTION.md`** - Panduan lengkap port detection
- **`port_detector.php`** - Utility untuk scanning port
- **`config.php`** - Auto-detection logic
- **`docker-compose.yml`** - Dynamic port configuration

---

## 🚀 Quick Start (FINAL)

```bash
# Untuk SEMUA platform (Windows/Linux/macOS):

git clone https://github.com/82080038/distributor.git
cd distribusi
php port_detector.php    # Cek port yang tersedia
docker-compose up -d --build  # Start dengan port otomatis

# APLIKASI SIAP! 
# Web: http://localhost:8080
# Database: http://localhost:8081
```

**🎉 Aplikasi sekarang bisa menemukan port MySQL yang optimal di KOMPUTER APAPUN!**
