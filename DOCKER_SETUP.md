# Docker Setup untuk Aplikasi Distributor

## 📋 Persiapan

### 1. Install Docker
**Windows:**
- Download Docker Desktop dari https://www.docker.com/products/docker-desktop
- Install dan restart komputer
- Buka Docker Desktop

**Linux:**
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install docker.io docker-compose
sudo usermod -aG docker $USER
# Restart komputer

# CentOS/RHEL
sudo yum install docker docker-compose
sudo usermod -aG docker $USER
# Restart komputer
```

### 2. Verifikasi Install
```bash
docker --version
docker-compose --version
```

## 🚀 Menjalankan Aplikasi

### Langkah 1: Build dan Start Containers
```bash
# Di folder project distributor
docker-compose up -d --build
```

### Langkah 2: Tunggu Proses Selesai
- Database akan di-import otomatis dari `db/distribusi.sql`
- Proses ini memakan waktu 1-2 menit saat pertama kali

### Langkah 3: Akses Aplikasi
- **Aplikasi**: http://localhost:8080
- **PhpMyAdmin**: http://localhost:8081
  - Server: mysql
  - Username: root
  - Password: (kosong)

## 📁 Struktur File Docker

```
distribusi/
├── docker-compose.yml      # Konfigurasi containers
├── Dockerfile            # Build web server
├── mysql-init/
│   └── init-db.sh      # Script import database
├── db/
│   ├── distribusi.sql   # Database distributor
│   └── distributor.sql # Database distributor (alternatif)
└── config.php          # Konfigurasi auto-detect environment
```

## 🔧 Konfigurasi Database

### Database yang Di-import:
1. **distributor** - dari file `db/distribusi.sql`
2. **alamat_db** - dibuat kosong, siap untuk data

### Koneksi Database:
- **Host**: mysql (nama container)
- **User**: distributor_user
- **Password**: distributor_pass
- **Port**: 3306

## 🛠 Perintah Docker Berguna

### Melihat Log
```bash
# Log semua containers
docker-compose logs

# Log web server saja
docker-compose logs web

# Log database saja
docker-compose logs mysql
```

### Restart Containers
```bash
# Restart semua
docker-compose restart

# Restart web server saja
docker-compose restart web
```

### Stop Containers
```bash
docker-compose down
```

### Hapus Semua Data (Reset)
```bash
docker-compose down -v
docker-compose up -d --build
```

## 💾 Database Management

### Backup Database
```bash
# Export database distributor
docker exec distributor_mysql mysqldump -u root -p'' distributor > backup_distributor.sql

# Export database alamat_db
docker exec distributor_mysql mysqldump -u root -p'' alamat_db > backup_alamat_db.sql
```

### Restore Database
```bash
# Import ke database distributor
docker exec -i distributor_mysql mysql -u root -p'' distributor < backup_distributor.sql

# Import ke database alamat_db
docker exec -i distributor_mysql mysql -u root -p'' alamat_db < backup_alamat_db.sql
```

### Akses MySQL CLI
```bash
docker exec -it distributor_mysql mysql -u root -p''
```

## 🔍 Troubleshooting

### Port 8080/8081 Sudah Digunakan
```bash
# Edit docker-compose.yml, ubah ports:
ports:
  - "8082:80"  # Ganti 8080 ke 8082
  - "8083:80"  # Ganti 8081 ke 8083
```

### Database Connection Error
```bash
# Cek log MySQL
docker-compose logs mysql

# Restart MySQL container
docker-compose restart mysql
```

### Permission Error (Linux)
```bash
# Fix permission files
sudo chown -R $USER:$USER .
chmod +x mysql-init/init-db.sh
```

### Build Error
```bash
# Clear cache dan rebuild
docker-compose down
docker system prune -f
docker-compose up -d --build
```

## 🌐 Development Workflow

### 1. Development di Docker
```bash
# Start containers
docker-compose up -d

# Edit kode lokal (otomatis sync ke container)
# Akses http://localhost:8080
```

### 2. Switch ke Native (Windows XAMPP)
```bash
# Stop Docker
docker-compose down

# Start XAMPP
# Akses http://localhost/distribusi
```

### 3. Switch ke Native (Linux)
```bash
# Stop Docker
docker-compose down

# Start MySQL native
sudo systemctl start mysql
# Akses http://localhost/distribusi
```

## 📝 Catatan Penting

1. **Auto-detect Environment**: Config.php otomatis mendeteksi:
   - Docker (mysql host)
   - Windows XAMPP (localhost, no password)
   - Linux Native (localhost, socket file)

2. **Data Persistence**: Database tersimpan di Docker volume `mysql_data`
   - Data tidak hilang saat container restart
   - Hanya hilang jika `docker-compose down -v`

3. **File Sync**: Folder project lokal sync ke container
   - Edit kode lokal = langsung berubah di container
   - Tidak perlu copy file manual

4. **Cross-Platform**: Setup sama persis di Windows dan Linux
   - Tidak perlu konfigurasi ulang
   - Environment konsisten

## 🎯 Quick Start Commands

```bash
# First time setup
docker-compose up -d --build

# Daily development
docker-compose up -d

# Stop when done
docker-compose down

# Check status
docker-compose ps
```

Sekarang aplikasi distributor kamu siap dijalankan di **Windows dan Linux** dengan Docker! 🎉
