# 🔍 Port Detection & Configuration Guide

## 📋 Overview

Aplikasi sekarang memiliki **automatic port detection** untuk menemukan port MySQL yang tersedia dan mengkonfigurasi aplikasi secara otomatis.

## 🎯 Fitur Port Detection

### **Automatic Port Scanning:**
Aplikasi akan mencoba port-port berikut secara berurutan:
- **Docker**: 3307, 3306, 3308, 3309
- **Windows**: 3306, 3307, 3308  
- **Linux**: 3306, 3307, 3308

### **Environment Detection:**
```php
// config.php - Automatic Detection
if ($is_docker) {
    $available_port = find_available_mysql_port(3307);
    define('DB_PORT', $available_port);
    error_log("Docker: Using MySQL port " . $available_port);
} elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $available_port = find_available_mysql_port(3306);
    define('DB_PORT', $available_port);
} else {
    $available_port = find_available_mysql_port(3306);
    define('DB_PORT', $available_port);
    error_log("Linux: Using MySQL port " . $available_port);
}
```

## 🔧 Menggunakan Port Detector

### **1. Run Port Scanner:**
```bash
# Di folder project
php port_detector.php
```

### **2. Output Examples:**

#### **Docker Environment:**
```
=== MySQL Port Detection Utility ===

Environment: Docker
Scanning available ports on 127.0.0.1...
  Trying port 3307... ✅ Port 3307: SUCCESS - Connected to MySQL
🎯 Found working MySQL port: 3307

🐳 Docker Configuration:
  Update docker-compose.yml ports:
    ports:
      - "3307:3306"

  Then restart: docker-compose up -d
```

#### **Windows Environment:**
```
=== MySQL Port Detection Utility ===

Environment: Windows
Scanning available ports on 127.0.0.1...
  Trying port 3306... ❌ Port 3306: FAILED - Connection refused
  Trying port 3307... ✅ Port 3307: SUCCESS - Connected to MySQL
🎯 Found working MySQL port: 3307

🪟 Windows Configuration:
  Update config.php DB_PORT to: 3307
  Or change XAMPP MySQL port to: 3307
```

#### **Linux Environment:**
```
=== MySQL Port Detection Utility ===

Environment: Linux
Scanning available ports on 127.0.0.1...
  Trying port 3306... ❌ Port 3306: FAILED - Connection refused
  Trying port 3307... ✅ Port 3307: SUCCESS - Connected to MySQL
🎯 Found working MySQL port: 3307

🐧 Linux Configuration:
  Update config.php DB_PORT to: 3307
  Or change MySQL service port to: 3307
  Then restart: sudo systemctl restart mysql
```

## 🔄 Konfigurasi Otomatis

### **Docker Environment:**
1. **Port Detection**: Aplikasi otomatis mencari port yang tersedia
2. **Dynamic Configuration**: `DB_PORT` diset ke port yang ditemukan
3. **Logging**: Port yang digunakan di-log untuk debugging
4. **Fallback**: Jika tidak ada port yang cocok, gunakan default

### **Native Environment:**
1. **Port Scanning**: Cek port 3306, 3307, 3308, dll
2. **Socket Detection**: Auto-detect lokasi socket file
3. **Configuration**: Update `DB_PORT` dan `DB_SOCKET`
4. **Error Logging**: Log konfigurasi untuk troubleshooting

## 🛠 Manual Configuration (Jika Otomatis Gagal)

### **Docker - Ganti Port:**
```yaml
# docker-compose.yml
services:
  mysql:
    ports:
      - "3308:3306"  # Ganti ke port yang tersedia
```

### **Windows - XAMPP:**
```ini
# C:\xampp\mysql\bin\my.ini
[mysqld]
port=3307  # Ganti ke port yang tersedia
```

### **Linux - MySQL Service:**
```bash
# /etc/mysql/mysql.conf.d/mysqld.cnf
[mysqld]
port=3307  # Tambahkan baris ini

# Restart service
sudo systemctl restart mysql
```

## 🐛 Troubleshooting Port Issues

### **Port Already Used:**
```bash
# Cek port yang digunakan
# Windows: netstat -an | findstr :3306
# Linux: ss -tlnp | grep :3306

# Solusi: Gunakan port lain
# Cari port kosong: 3308, 3309, 3310
```

### **Firewall Issues:**
```bash
# Windows
# Allow port di Windows Firewall
netsh advfirewall firewall add rule name="MySQL" dir=in action=allow protocol=TCP localport=3307

# Linux (Ubuntu)
sudo ufw allow 3307/tcp
sudo ufw reload

# Linux (CentOS)
sudo firewall-cmd --permanent --add-port=3307/tcp
sudo firewall-cmd --reload
```

### **Docker Port Issues:**
```bash
# Cek container mapping
docker-compose ps

# Cek logs
docker-compose logs mysql

# Restart dengan port baru
docker-compose down
# Edit docker-compose.yml (ganti port)
docker-compose up -d
```

## 📱 Best Practices

### **1. Sebelum Development:**
```bash
# Jalankan port detector
php port_detector.php

# Catat port yang direkomendasikan
# Update konfigurasi sesuai saran
```

### **2. Development Workflow:**
```bash
# Start development
docker-compose up -d

# Cek logs untuk port info
docker-compose logs mysql | grep "Using MySQL port"

# Akses aplikasi
# Web: http://localhost:8080
# Database: http://localhost:8081
```

### **3. Team Collaboration:**
```bash
# Commit port configuration
git add config.php docker-compose.yml
git commit -m "Configure MySQL port for [environment]"

# Share dengan team
git push origin main

# Team member tinggal:
git pull
docker-compose up -d
# Port otomatis terdeteksi!
```

## 🎯 Quick Reference Commands

```bash
# Scan ports
php port_detector.php

# Test specific port
php -r "test_mysql_port('127.0.0.1', 3307);"

# Check current config
php -r "require_once 'config.php'; echo 'Current port: ' . DB_PORT;"

# Docker restart dengan port baru
docker-compose down && docker-compose up -d

# Windows XAMPP restart
# Restart XAMPP Control Panel

# Linux MySQL restart
sudo systemctl restart mysql
```

## 📊 Environment Matrix

| Environment | Default Ports | Detection Method | Config File |
|-------------|----------------|-------------------|-------------|
| Docker | 3307, 3306, 3308 | TCP Scan | docker-compose.yml |
| Windows XAMPP | 3306, 3307 | TCP Scan | config.php |
| Linux Native | 3306, 3307 | TCP + Socket | config.php |

---

## 🎉 Hasil

Dengan **automatic port detection**:
- ✅ **Tidak perlu manual configuration**
- ✅ **Otomatis cari port yang tersedia**
- ✅ **Cross-platform compatible**
- ✅ **Logging untuk debugging**
- ✅ **Fallback ke default jika perlu**

**Aplikasi sekarang bisa menemukan port MySQL yang optimal di komputer apapun!**
