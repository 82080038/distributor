# 🐳 Docker Development Setup

## 📋 Persyaratan Sistem

### **Minimum Requirements:**
- **RAM**: 4GB+ (recommended 8GB)
- **Storage**: 10GB+ free space
- **OS**: Windows 10/11, Ubuntu 18.04+, CentOS 7+, macOS 10.14+

### **Software yang Diperlukan:**
1. **Docker Desktop** (Windows/Mac) atau **Docker Engine** (Linux)
2. **Git** untuk version control
3. **Text Editor/IDE** (VS Code, PhpStorm, dll)

## 🚀 Instalasi Docker

### **Windows 10/11:**
1. Download **Docker Desktop** dari https://www.docker.com/products/docker-desktop
2. Install dan restart komputer
3. Buka Docker Desktop (auto-start dengan Windows)
4. Verifikasi: Buka Command Prompt dan jalankan `docker --version`

### **Linux (Ubuntu/Debian):**
```bash
# Update package manager
sudo apt update

# Install Docker
sudo apt install -y docker.io docker-compose

# Add user ke docker group
sudo usermod -aG docker $USER

# Enable dan start Docker
sudo systemctl enable docker
sudo systemctl start docker

# Restart komputer untuk apply group changes
```

### **Linux (CentOS/RHEL):**
```bash
# Install EPEL repository
sudo yum install -y epel-release

# Install Docker
sudo yum install -y docker docker-compose

# Start Docker
sudo systemctl start docker
sudo systemctl enable docker

# Add user ke docker group
sudo usermod -aG docker $USER
```

### **macOS:**
1. Download **Docker Desktop** dari https://www.docker.com/products/docker-desktop
2. Install dan follow setup instructions
3. Restart komputer
4. Buka Docker Desktop dari Applications

## 🔧 Verifikasi Instalasi

```bash
# Cek Docker version
docker --version

# Cek Docker Compose version  
docker-compose --version

# Test Docker (run hello-world)
docker run hello-world
```

## 📁 Setup Project

### **1. Clone Repository:**
```bash
# Clone dari Git
git clone <repository-url-distributor>
cd distribusi

# Lihat struktur project
ls -la
```

### **2. Konfigurasi Environment:**
```bash
# Copy environment template (jika perlu)
cp .env.docker .env.local

# Edit konfigurasi khusus (opsional)
nano .env.local
```

### **3. Start Containers:**
```bash
# Build dan start semua containers
docker-compose up -d --build

# Proses ini akan:
# - Download MariaDB image (~200MB)
# - Build PHP web image (~5-10 menit)
# - Import database otomatis
# - Start semua containers
```

## 🌐 Akses Aplikasi

### **Setelah Setup Selesai:**
- **Aplikasi Web**: http://localhost:8080
- **Database Admin**: http://localhost:8081 (PhpMyAdmin)
- **Database Connection**: 
  - Host: `mysql` (Docker internal network)
  - Port: `3306` (internal) / `3307` (external)
  - User: `root` (password kosong)
  - Database: `distributor`

### **Login Pertama Kali:**
1. Buka http://localhost:8080
2. Register user pertama (jika database kosong)
3. Login dengan user yang dibuat

## 🔄 Development Workflow

### **Daily Development:**
```bash
# Start containers (jika belum running)
docker-compose up -d

# Edit kode di text editor
# Perubahan otomatis sync ke container

# View logs (jika ada error)
docker-compose logs -f web

# Stop saat selesai
docker-compose down
```

### **Database Management:**
```bash
# Akses PhpMyAdmin
# http://localhost:8081
# Login: root, password kosong

# Atau via command line
docker exec -it distributor_mysql mysql -u root -p

# Export database
docker exec distributor_mysql mysqldump -u root distributor > backup.sql

# Import database  
docker exec -i distributor_mysql mysql -u root distributor < import.sql
```

## 🐛 Troubleshooting

### **Port Conflict:**
```bash
# Error: Port 8080/8081/3307 sudah digunakan
# Solusi: Ganti port di docker-compose.yml

ports:
  - "8082:80"    # Ganti 8080 ke 8082
  - "8083:80"    # Ganti 8081 ke 8083
  - "3308:3306"  # Ganti 3307 ke 3308
```

### **Build Error:**
```bash
# Error: Build gagal
# Solusi: Clear cache dan rebuild

docker-compose down
docker system prune -f
docker-compose up -d --build
```

### **Database Connection Error:**
```bash
# Error: Cannot connect to database
# Cek container status
docker-compose ps

# Cek database logs
docker-compose logs mysql

# Restart database container
docker-compose restart mysql
```

### **Permission Error (Linux):**
```bash
# Error: Permission denied
# Solusi: Fix permission dan restart

sudo chown -R $USER:$USER .
sudo chmod +x mysql-init/init-db.sh
docker-compose down
docker-compose up -d
```

### **Out of Space:**
```bash
# Error: No space left on device
# Solusi: Clean Docker

docker system prune -a
docker volume prune -f
```

## 📱 Development Tips

### **1. Hot Reload:**
- Edit file lokal → otomatis sync ke container
- Tidak perlu rebuild untuk perubahan kode

### **2. Debug Mode:**
```bash
# View real-time logs
docker-compose logs -f web

# Access container shell
docker exec -it distributor_web bash
```

### **3. Database Reset:**
```bash
# Reset database ke fresh state
docker-compose down -v
docker-compose up -d --build
```

### **4. Performance Monitoring:**
```bash
# Monitor resource usage
docker stats

# Check container health
docker-compose ps
```

## 🔧 Konfigurasi Lanjutan

### **Custom Environment Variables:**
```bash
# Buat .env.local file
cat > .env.local << EOF
# Custom Configuration
DB_PASSWORD=custom_password
APP_DEBUG=true
APP_ENV=development
EOF

# Load di docker-compose.yml
env_file:
  - .env.local
```

### **Production Setup:**
```bash
# Build production image
docker build -t distributor:production .

# Run dengan production settings
docker run -d \
  --name distributor_prod \
  -p 80:80 \
  -e APP_ENV=production \
  distributor:production
```

## 📚 Referensi

### **Docker Commands:**
```bash
# Daftar containers
docker ps

# Daftar images
docker images

# Hentikan container
docker stop <container_name>

# Hapus container
docker rm <container_name>

# View logs
docker logs <container_name>

# Access container shell
docker exec -it <container_name> bash
```

### **Docker Compose Commands:**
```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs

# Rebuild services
docker-compose up -d --build

# Scale services
docker-compose up -d --scale web=3
```

## 🎯 Checklist Sebelum Development

### **✅ Prerequisites:**
- [ ] Docker terinstall dan running
- [ ] Git terinstall
- [ ] Repository sudah di-clone
- [ ] Port 8080, 8081, 3307 available

### **✅ Setup:**
- [ ] `docker-compose up -d` berhasil
- [ ] Database otomatis di-import
- [ ] Aplikasi accessible di http://localhost:8080
- [ ] PhpMyAdmin accessible di http://localhost:8081

### **✅ Verification:**
- [ ] Login page muncul
- [ ] Database connection successful
- [ ] Bisa register user baru
- [ ] Dashboard accessible setelah login

---

## 🎉 Selamat Development!

Dengan Docker setup ini, kamu bisa:
- **Develop di Windows, Linux, atau macOS**
- **Environment konsisten di semua platform**
- **Tidak perlu setup manual database**
- **Focus pada coding, bukan configuration**

**Happy Coding! 🚀**
