# 🐳 Docker Development - Cross Platform Compatibility

## 📋 Penting! Baca Ini Terlebih Dahulu

**Aplikasi ini dikembangkan dengan Docker containerization untuk memastikan kompatibilitas di semua platform.**

### 🎯 **Kenapa Docker?**
- **Windows konsisten** dengan **Linux** dan **macOS**
- **Environment sama** persis di semua komputer
- **Tidak ada "works on my machine" issues**
- **Setup sekali jalan di mana saja**

## 🌍 Platform Support

### ✅ **Supported Operating Systems:**
- **Windows 10/11** - Docker Desktop
- **Ubuntu/Debian** - Docker Engine  
- **CentOS/RHEL** - Docker Engine
- **macOS 10.14+** - Docker Desktop
- **Distro Linux lainnya** - Docker Engine

### 🔄 **Development Pattern:**
```
Windows Developer ─┐
                  │
                  ├─> Docker Container ─┐
                  │                    │
Linux Developer  ─┤                    ├─> Aplikasi Sama Persis
                  │                    │
macOS Developer ─┘                    └─> Database & Environment Sama
```

## 🚀 Quick Start (Semua Platform)

### **Langkah 1: Install Docker**
Pilih sesuai OS kamu:

#### **Windows:**
1. Download Docker Desktop dari docker.com
2. Install dan restart
3. Buka Docker Desktop

#### **Linux:**
```bash
sudo apt update && sudo apt install -y docker.io docker-compose
sudo usermod -aG docker $USER
sudo systemctl start docker && sudo systemctl enable docker
# Restart komputer
```

#### **macOS:**
1. Download Docker Desktop dari docker.com
2. Install dan restart

### **Langkah 2: Setup Project**
```bash
# Clone (satu kali untuk semua platform)
git clone <repository-url>
cd distribusi

# Start (perintah sama untuk semua platform)
docker-compose up -d --build

# Akses (URL sama untuk semua platform)
# Aplikasi: http://localhost:8080
# Database: http://localhost:8081
```

## 🏗️ Architecture Container

```
┌─────────────────────────────────────────────────────────┐
│              Docker Environment                │
│                                               │
│  ┌─────────────┐  ┌─────────────────────┐  │
│  │   Web App   │  │    Database        │  │
│  │             │  │                   │  │
│  │ PHP 7.4    │  │ MariaDB 10.6      │  │
│  │ Apache 2.4   │  │ Port: 3307        │  │
│  │ Port: 8080   │  │                   │  │
│  └─────────────┘  └─────────────────────┘  │
│                                               │
│  ┌─────────────────────────────────────────┐     │
│  │         PhpMyAdmin               │     │
│  │                                 │     │
│  │ Port: 8081                      │     │
│  └─────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────┘
```

## 📱 Development Experience

### **Same Experience di Semua Platform:**
- **Windows**: Docker Desktop → Container → Aplikasi
- **Linux**: Docker Engine → Container → Aplikasi  
- **macOS**: Docker Desktop → Container → Aplikasi

### **Identical Setup:**
```bash
# Perintah sama persis
docker-compose up -d

# Hasil sama persis
# Web: http://localhost:8080
# DB: http://localhost:8081
# Database: distributor (auto-import dari distribusi.sql)
```

## 🔧 Auto-Configuration

Aplikasi otomatis mendeteksi environment:

### **Docker Detection:**
```php
if (file_exists('.dockerenv') || getenv('DOCKER_ENV')) {
    // Docker mode
    $db_host = 'mysql';
    $db_port = 3307;
    $db_user = 'distributor_user';
}
```

### **Native Detection:**
```php
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows XAMPP
    $db_host = 'localhost';
    $db_port = 3306;
    $db_user = 'root';
} else {
    // Linux Native
    $db_host = 'localhost';
    $db_port = 3306;
    $db_user = 'root';
    // Auto-detect socket
}
```

## 🎯 Development Workflow

### **1. Universal Setup:**
```bash
# Untuk SEMUA platform
git clone <repo>
cd distribusi
docker-compose up -d --build
```

### **2. Daily Development:**
```bash
# Sama untuk semua platform
docker-compose up -d          # Start
docker-compose logs -f web     # Debug
docker-compose down             # Stop
```

### **3. Cross-Platform Collaboration:**
```bash
# Developer A (Windows)
docker-compose up -d
# Edit kode → push ke Git

# Developer B (Linux)  
git pull
docker-compose up -d
# Environment sama persis!
```

## 📊 File Structure (Cross-Platform)

```
distribusi/                    # Same di semua platform
├── 🐳 docker-compose.yml   # Container orchestration
├── 🐳 Dockerfile          # Build configuration  
├── ⚙️ config.php          # Auto-detect setup
├── 📊 db/                # Database schemas
│   └── distribusi.sql     # Auto-import
├── 🌐 *.php              # Application files
├── 📱 app.js             # Frontend logic
└── 📚 *.md               # Documentation
```

## 🔄 Environment Switching

### **Docker → Native:**
```bash
# Stop Docker
docker-compose down

# Start Native
# Windows: Start XAMPP
# Linux: sudo systemctl start mysql

# Akses: http://localhost/distribusi
```

### **Native → Docker:**
```bash
# Stop Native
# Windows: Stop XAMPP
# Linux: sudo systemctl stop mysql

# Start Docker
docker-compose up -d

# Akses: http://localhost:8080
```

## 🐛 Troubleshooting (Cross-Platform)

### **Port Issues (Semua Platform):**
```bash
# Cek port yang digunakan
# Windows: netstat -an | findstr :8080
# Linux: ss -tlnp | grep :8080

# Solusi: Ganti port di docker-compose.yml
ports:
  - "8082:80"    # Alternative port
```

### **Docker Issues (Semua Platform):**
```bash
# Docker tidak running
docker --version
docker-compose --version

# Solusi: Restart Docker service
# Windows: Restart Docker Desktop
# Linux: sudo systemctl restart docker
```

### **Database Issues (Semua Platform):**
```bash
# Test koneksi
docker exec web php -r "require_once 'config.php'; echo 'DB: ' . (\$conn->connect_error ? 'ERROR' : 'OK');"

# Reset database
docker-compose down -v
docker-compose up -d --build
```

## 🎯 Best Practices

### **1. Use Docker untuk Development:**
- Konsisten di semua platform
- Mudah setup dan maintenance
- Isolated dari host system

### **2. Git untuk Version Control:**
```bash
# Track perubahan kode
git add .
git commit -m "Cross-platform compatible changes"
git push origin main
```

### **3. Documentation Updates:**
- Update README.md untuk perubahan setup
- Catat platform-specific requirements
- Maintain troubleshooting guide

## 📱 Testing Strategy

### **Cross-Platform Testing:**
```bash
# Test di Docker (recommended)
docker-compose up -d
# Verify: http://localhost:8080

# Test Native (optional)
docker-compose down
# Start native server
# Verify: http://localhost/distribusi
```

### **Continuous Integration:**
```yaml
# .github/workflows/docker.yml
name: Test Cross-Platform
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Start Docker
        run: docker-compose up -d --build
      - name: Test Application
        run: curl -f http://localhost:8080
```

## 🎉 Summary

### **Docker Development Benefits:**
1. **🌍 Cross-Platform** - Windows, Linux, macOS sama persis
2. **🔧 Zero Configuration** - Setup sekali jalan di mana saja
3. **📱 Consistent Environment** - Tidak ada platform-specific bugs
4. **🚀 Easy Deployment** - Container siap untuk production
5. **🤝 Team Collaboration** - Sama environment untuk semua developer

### **Getting Started Checklist:**
- [ ] Docker terinstall (sesuai OS)
- [ ] Repository di-clone
- [ ] `docker-compose up -d` berhasil
- [ ] Aplikasi accessible di localhost:8080
- [ ] Database ter-import otomatis
- [ ] Ready untuk development!

---

## 📞 Support

### **Platform-Specific Help:**
- **Windows**: Docker Desktop documentation
- **Linux**: `man docker-compose` atau `docker --help`
- **macOS**: Docker Desktop documentation

### **General Issues:**
1. Check Docker service status
2. Verify port availability
3. Review container logs
4. Check system resources (RAM, disk space)

**🎯 Docker memastikan aplikasi kamu berjalan sama persis di SEMUA PLATFORM!**
