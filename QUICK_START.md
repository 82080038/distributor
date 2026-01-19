# 🐳 Docker Development - Quick Reference

## 🚀 One-Command Setup (All Platforms)

```bash
# Install Docker (sesuai OS)
# Windows: Download Docker Desktop dari docker.com
# Linux: sudo apt install docker.io docker-compose && sudo usermod -aG docker $USER
# macOS: Download Docker Desktop dari docker.com

# Clone dan Start
git clone <repository-url>
cd distribusi
docker-compose up -d --build

# Akses (SEMUA PLATFORM SAMA)
# Web: http://localhost:8080
# Database: http://localhost:8081
```

## 🔍 Port Detection (NEW!)

Aplikasi sekarang memiliki **automatic port detection** untuk menemukan port MySQL yang tersedia:

### **Cek Port yang Tersedia:**
```bash
# Jalankan port detector
php port_detector.php

# Output akan menunjukkan port yang bisa digunakan
# Contoh:
# 🎯 Found working MySQL port: 3307
```

### **Konfigurasi Otomatis:**
- **Docker**: Port 3307, 3306, 3308, 3309 (auto-scan)
- **Windows**: Port 3306, 3307, 3308 (auto-scan)  
- **Linux**: Port 3306, 3307, 3308 (auto-scan)

## 📋 Platform Matrix

| Platform | Docker Tool | Install Command | Access URL |
|----------|---------------|----------------|-------------|
| Windows 10/11 | Docker Desktop | Download & Install | http://localhost:8080 |
| Ubuntu/Debian | Docker Engine | `sudo apt install docker.io docker-compose` | http://localhost:8080 |
| CentOS/RHEL | Docker Engine | `sudo yum install docker docker-compose` | http://localhost:8080 |
| macOS 10.14+ | Docker Desktop | Download & Install | http://localhost:8080 |

## 🔧 Common Commands

```bash
# Start development
docker-compose up -d

# View logs
docker-compose logs -f web

# Stop development
docker-compose down

# Rebuild (jika perubahan Dockerfile)
docker-compose up -d --build

# Access container shell
docker exec -it distributor_web bash

# Database access
docker exec -it distributor_mysql mysql -u root -p

# Reset database
docker-compose down -v && docker-compose up -d --build
```

## 🐛 Quick Fixes

```bash
# Port conflict? Ganti di docker-compose.yml:
ports:
  - "8082:80"    # Web
  - "8083:80"    # PhpMyAdmin  
  - "3308:3306"  # Database

# Permission error (Linux)?
sudo chown -R $USER:$USER .

# Docker not running?
# Windows: Restart Docker Desktop
# Linux: sudo systemctl restart docker

# Build error?
docker-compose down && docker system prune -f && docker-compose up -d --build
```

## 🎯 Development Benefits

✅ **Same Experience Everywhere**  
✅ **No Environment Setup Issues**  
✅ **Easy Team Collaboration**  
✅ **Production-Ready Containers**  
✅ **Cross-Platform Compatibility**

---
**🐳 Docker = Windows + Linux + macOS Sama Persis!**
