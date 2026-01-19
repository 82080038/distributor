# Database Setup Guide for Cross-Platform Development

## Windows (XAMPP) Setup

### 1. Install XAMPP
- Download XAMPP dari https://www.apachefriends.org/
- Install di `C:\xampp` (default)

### 2. Start Services
- Buka XAMPP Control Panel
- Start Apache dan MySQL

### 3. Database Setup
```sql
-- Buka http://localhost/phpmyadmin di browser
-- Create databases:
CREATE DATABASE distributor;
CREATE DATABASE alamat_db;

-- Import schema files:
-- 1. schema_core.sql
-- 2. schema_migration_orang_user.sql  
-- 3. schema_migration_purchases.sql
```

### 4. XAMPP Configuration
- MySQL User: `root`
- Password: (kosong/default)
- Port: 3306
- Host: localhost

## Linux Setup

### Ubuntu/Debian
```bash
sudo apt update
sudo apt install mysql-server php-mysql
sudo systemctl start mysql
sudo systemctl enable mysql

# Secure installation
sudo mysql_secure_installation

# Create databases and user
sudo mysql -u root -p
```

### CentOS/RHEL/Fedora
```bash
sudo yum install mysql-server php-mysqlnd
# atau untuk Fedora
sudo dnf install mysql-server php-mysqlnd

sudo systemctl start mysqld
sudo systemctl enable mysqld
```

### Database Commands (Linux)
```sql
CREATE DATABASE distributor;
CREATE DATABASE alamat_db;
CREATE USER 'distributor_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON distributor.* TO 'distributor_user'@'localhost';
GRANT ALL PRIVILEGES ON alamat_db.* TO 'distributor_user'@'localhost';
FLUSH PRIVILEGES;
```

## Cross-Platform Development Strategy

### 1. **Sync Database Schema**
```bash
# Export dari Windows (XAMPP)
mysqldump -u root -p distributor > schema.sql
mysqldump -u root -p alamat_db > alamat_schema.sql

# Import ke Linux
mysql -u root -p distributor < schema.sql
mysql -u root -p alamat_db < alamat_schema.sql
```

### 2. **Configuration File**
Config.php otomatis mendeteksi:
- Windows: TCP/IP connection
- Linux: Socket file (auto-detect lokasi)

### 3. **Development Workflow**

#### Opsi A: Git + SQL Dump
```bash
# Commit schema changes
git add schema_core.sql schema_*.sql
git commit -m "Update database schema"

# Sync antar environment
scp schema_*.sql user@server:/path/to/project/
```

#### Opsi B: Migration Scripts
Buat file `migrations/` untuk tracking perubahan schema.

#### Opsi C: Docker (Recommended)
```dockerfile
# docker-compose.yml
version: '3'
services:
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: ''
      MYSQL_DATABASE: distributor
    ports:
      - "3306:3306"
    volumes:
      - ./schema:/docker-entrypoint-initdb.d
```

## Testing Connection

### Windows Test
```php
// test_connection.php
<?php
require_once 'config.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully on Windows!";
?>
```

### Linux Test
```bash
php test_connection.php
# Should output: Connected successfully on Linux!
```

## Troubleshooting

### Windows Issues
- Port 3306 blocked: Check Windows Firewall
- Access denied: Reset XAMPP MySQL password
- Service not running: Start MySQL di XAMPP Control Panel

### Linux Issues
- Socket not found: Check MySQL service status
- Permission denied: Fix MySQL socket permissions
- Connection refused: Check if MySQL is running

## Best Practices

1. **Environment Variables**
   ```php
   // .env file (Windows)
   DB_PASSWORD=
   
   // .env file (Linux)  
   DB_PASSWORD=your_secure_password
   ```

2. **Backup Strategy**
   ```bash
   # Automated backup script
   mysqldump --single-transaction -u root -p distributor > backup_$(date +%Y%m%d).sql
   ```

3. **Version Control**
   - Track schema changes dengan SQL files
   - Jangan track production data
   - Use .gitignore untuk sensitive data
