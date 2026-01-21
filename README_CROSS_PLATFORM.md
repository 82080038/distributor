# 🔄 Cross-Platform Development Guide

## 🎯 **Tujuan**
Aplikasi distributor dirancang untuk development di Windows (XAMPP) dan Linux, dengan deployment ke server Linux. Kode seragam di semua platform.

## 📋 **Persiapan Awal**

### **Windows Development (XAMPP)**
1. **Install XAMPP** dari https://www.apachefriends.org
2. **Install Visual Studio Code** atau editor favorit
3. **Install Git** untuk version control

### **Linux Development**
1. **Install LAMP/LEMP Stack**:
   ```bash
   # Ubuntu/Debian
   sudo apt update
   sudo apt install apache2 mysql-server php php-mysqli php-json php-mbstring git
   
   # CentOS/RHEL
   sudo yum install httpd mariadb-server php php-mysqlnd php-json php-mbstring git
   ```

2. **Install VS Code**:
   ```bash
   wget -qO- https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o microsoft.asc.gpg
   sudo install -o root -g root -m 644 microsoft.asc.gpg /etc/apt/trusted.gpg.d/
   sudo sh -c 'echo "deb [arch=amd64,arm64,armhf signed-by=/etc/apt/trusted.gpg.d/microsoft.asc.gpg] https://packages.microsoft.com/repos/code stable main" > /etc/apt/sources.list.d/vscode.list'
   sudo apt update
   sudo apt install code
   ```

## 🚀 **Quick Start**

### **Opsi 1: Setup Otomatis**
1. Buka browser: `http://localhost/distributor/setup_cross_platform.php`
2. Ikuti instruksi yang ditampilkan
3. Download config file yang sesuai

### **Opsi 2: Manual Setup**
1. **Copy config file** sesuai platform:
   - Windows: `cp config.windows.php config.php`
   - Linux: `cp config.linux.php config.php`

2. **Setup database**:
   - Windows: Via XAMPP phpMyAdmin (http://localhost/phpmyadmin)
   - Linux: Via command line atau phpMyAdmin

3. **Import schema**:
   ```bash
   # Windows (XAMPP)
   # Import via phpMyAdmin interface
   
   # Linux
   mysql -u root -p distributor < database/schema_core.sql
   mysql -u root -p distributor < database/schema_add_tipe_alamat.sql
   ```

## 📁 **Struktur Project**

```
distributor/
├── 📄 config.php                 # Konfigurasi utama (copy dari template)
├── 📄 config.windows.php          # Template config Windows
├── 📄 config.linux.php            # Template config Linux
├── 📄 config.functions.php         # Fungsi-fungsi universal
├── 📄 setup_cross_platform.php  # Setup wizard
├── 📁 database/                  # Schema & migration files
│   ├── schema_core.sql
│   ├── schema_add_tipe_alamat.sql
│   └── setup_database.php
├── 📁 logs/                     # Error logs
├── 📁 uploads/                  # File uploads
├── 📁 temp/                     # Temporary files
├── 🚀 deploy.sh                 # Production deployment script
├── 📖 README_CROSS_PLATFORM.md   # File ini
└── [📄 semua file aplikasi]
```

## 🔧 **Konfigurasi Per Platform**

### **Windows (XAMPP)**
```php
// config.windows.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default kosong
define('DB_NAME', 'distributor');
define('DB_NAME_ALAMAT', 'alamat_db');
```

**XAMPP Setup:**
1. Start XAMPP Control Panel
2. Start Apache & MySQL
3. Buka http://localhost/phpmyadmin
4. Create databases: `distributor`, `alamat_db`
5. Import schema files

### **Linux Development**
```php
// config.linux.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '8208'); // Password Linux
define('DB_NAME', 'distributor');
define('DB_NAME_ALAMAT', 'alamat_db');
```

**Linux Setup:**
1. Start MySQL: `sudo systemctl start mysql`
2. Setup password: `sudo mysql_secure_installation`
3. Create databases
4. Import schema files

### **Production Server**
```php
// config.production.php
define('DB_HOST', 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'prod_user');
define('DB_PASS', $_ENV['DB_PASS'] ?? 'secure_password');
define('DB_NAME', 'distributor_prod');
define('DB_NAME_ALAMAT', 'alamat_db_prod');
```

## 🌐 **Local Development URL**

### **Windows**
- **Default**: `http://localhost/distributor/`
- **Virtual Host**: `http://distributor.local/`

### **Linux**
- **Default**: `http://localhost/distributor/`
- **Virtual Host**: `http://distributor.local/`

## 🔄 **Sync Code Antar Platform**

### **Menggunakan Git**
```bash
# Di Windows
git init
git add .
git commit -m "Initial Windows setup"
git remote add origin https://github.com/username/distributor.git
git push -u origin main

# Di Linux
git clone https://github.com/username/distributor.git
cd distributor
cp config.linux.php config.php
# Setup database dan jalankan
```

### **Menggunakan Flash Drive/Cloud**
1. **Kembangkan di Windows**
2. **Copy ke cloud/flash drive**
3. **Paste di Linux**
4. **Copy config.linux.php ke config.php**
5. **Setup database**

## 🚨 **Troubleshooting Cross-Platform**

### **Windows Issues**
1. **Port 80 Busy**: Stop Skype/IIS atau change Apache port
2. **Permission Denied**: Run XAMPP as Administrator
3. **Database Connection**: Check XAMPP MySQL password (biasanya kosong)
4. **Virtual Host Not Working**: 
   - Edit `C:/Windows/System32/drivers/etc/hosts`
   - Restart browser

### **Linux Issues**
1. **Permission Denied**: 
   ```bash
   sudo chown -R www-data:www-data /var/www/html/distributor
   sudo chmod -R 755 /var/www/html/distributor
   ```
2. **MySQL Connection**: 
   ```bash
   sudo systemctl status mysql
   sudo mysql -u root -p
   ```
3. **Apache Not Working**:
   ```bash
   sudo systemctl status apache2
   sudo systemctl reload apache2
   ```

## 🚀 **Production Deployment**

### **Otomatis dengan Deploy Script**
```bash
# Di server production
cd /var/www/html/
git clone https://github.com/username/distributor.git
cd distributor
chmod +x deploy.sh
sudo ./deploy.sh
```

### **Manual Production Setup**
1. **Copy files** ke `/var/www/html/distributor/`
2. **Set permissions**:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/distributor
   sudo chmod -R 755 /var/www/html/distributor
   ```
3. **Setup production config**:
   ```bash
   cp config.production.php config.php
   # Edit database credentials
   ```
4. **Setup virtual host**:
   ```bash
   sudo a2ensite distributor.conf
   sudo systemctl reload apache2
   ```
5. **Setup SSL dengan Let's Encrypt**:
   ```bash
   sudo certbot --apache -d domain.com
   ```

## 📱 **Testing Cross-Platform**

### **Checklist Development**
- [ ] Config file sesuai platform
- [ ] Database connection berhasil
- [ ] Schema ter-import dengan benar
- [ ] Login berfungsi (admin/password)
- [ ] CRUD operations berjalan
- [ ] Address management berfungsi
- [ ] File permissions benar
- [ ] Error logging berfungsi

### **Automated Testing**
```bash
# Jalankan test script
php setup_cross_platform.php

# Atau test manual
curl -f http://localhost/distributor/login.php
```

## 🎯 **Best Practices**

### **Code Portability**
1. **Gunakan path separator**: `DIRECTORY_SEPARATOR` bukan `/` atau `\`
2. **Environment detection**: Gunakan fungsi `is_windows()`, `is_xampp()`
3. **Config management**: Pisahkan config per platform
4. **File permissions**: Cross-platform compatible

### **Database Consistency**
1. **Schema versioning**: Gunakan migration scripts
2. **Cross-platform SQL**: Avoid platform-specific features
3. **Backup strategy**: Automated backups

### **Development Workflow**
1. **Develop di Windows**: Test dengan XAMPP
2. **Test di Linux**: Verify compatibility
3. **Version control**: Commit per platform
4. **Deploy**: Gunakan deployment script

## 📞 **Support**

### **Debug Information**
Setiap config file include debug info:
```php
// Development mode
if (is_development()) {
    error_log("Platform: " . PHP_OS);
    error_log("XAMPP: " . (is_xampp() ? 'Yes' : 'No'));
    error_log("DB Connection: " . ($conn->ping() ? 'OK' : 'FAIL'));
}
```

### **Common Solutions**
1. **Clear browser cache** jika ada perubahan
2. **Restart web server** setelah config change
3. **Check error logs** di `logs/` folder
4. **Verify database credentials** di config file

---

**📅 Update**: 21 Januari 2026  
**👤 Author**: Cascade AI Assistant  
**🎯 Goal**: Cross-platform development achieved  

**Aplikasi siap untuk development di Windows (XAMPP) dan Linux!** 🚀
