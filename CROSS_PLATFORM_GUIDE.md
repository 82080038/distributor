# 🔄 Cross-Platform Development Guide

## 🎯 **Tujuan**
Memastikan aplikasi distributor bisa dikembangkan di Windows (XAMPP) dan Linux, serta di-deploy di server Linux dengan kode yang seragam.

## 🔧 **Konfigurasi Database Universal**

### **1. File Konfigurasi Utama (config.php)**
```php
<?php
// Cross-Platform Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '8208'); // Sesuaikan dengan environment
define('DB_NAME', 'distributor');
define('DB_NAME_ALAMAT', 'alamat_db');
define('DB_PORT', 3306);

// Auto-detect environment
$is_xampp = false;
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows - cek XAMPP
    $xampp_paths = [
        'C:/xampp/mysql/bin/mysql.exe',
        'D:/xampp/mysql/bin/mysql.exe',
        'E:/xampp/mysql/bin/mysql.exe'
    ];
    foreach ($xampp_paths as $path) {
        if (file_exists($path)) {
            $is_xampp = true;
            break;
        }
    }
}

// Connection dengan error handling yang universal
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    $conn->set_charset('utf8mb4');

    $conn_alamat = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME_ALAMAT, DB_PORT);
    if ($conn_alamat->connect_error) {
        error_log("Alamat database connection failed: " . $conn_alamat->connect_error);
        $conn_alamat = null; // Non-fatal error
    } else {
        $conn_alamat->set_charset('utf8mb4');
    }
} catch (Exception $e) {
    die('Database Error: ' . $e->getMessage());
}
?>
```

### **2. Environment Detection Helper**
```php
<?php
function is_development() {
    return in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', '::1']);
}

function is_windows() {
    return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}

function is_xampp() {
    return is_windows() && 
           (file_exists('C:/xampp') || file_exists('D:/xampp') || file_exists('E:/xampp'));
}

function get_mysql_path() {
    if (is_windows()) {
        $paths = [
            'C:/xampp/mysql/bin/mysql.exe',
            'D:/xampp/mysql/bin/mysql.exe',
            'E:/xampp/mysql/bin/mysql.exe'
        ];
        foreach ($paths as $path) {
            if (file_exists($path)) return $path;
        }
    }
    return 'mysql'; // Linux/macOS
}
?>
```

## 📁 **Struktur Folder yang Seragam**

```
distributor/
├── config.php                 # Konfigurasi universal
├── database/                 # Database files & migrations
│   ├── schema_core.sql
│   ├── schema_add_tipe_alamat.sql
│   ├── setup_database.php
│   └── cross_platform_setup.php
├── logs/                     # Error logs
├── uploads/                  # File uploads
├── temp/                     # Temporary files
└── [semua file PHP aplikasi]
```

## 🪟 **Setup untuk Windows (XAMPP)**

### **1. XAMPP Configuration**
```apache
# C:/xampp/apache/conf/extra/httpd-vhosts.conf
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/distributor"
    ServerName distributor.local
    <Directory "C:/xampp/htdocs/distributor">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### **2. Windows Hosts File**
```bash
# C:/Windows/System32/drivers/etc/hosts
127.0.0.1 distributor.local
```

### **3. XAMPP MySQL Setup**
```sql
-- Import via XAMPP phpMyAdmin
-- URL: http://localhost/phpmyadmin
-- Database: distributor
-- Import file: schema_core.sql
```

## 🐧 **Setup untuk Linux Development**

### **1. LAMP/LEMP Stack**
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install apache2 mysql-server php php-mysqli php-json php-mbstring

# CentOS/RHEL
sudo yum install httpd mariadb-server php php-mysqlnd php-json php-mbstring
```

### **2. Linux Configuration**
```apache
# /etc/apache2/sites-available/distributor.conf
<VirtualHost *:80>
    DocumentRoot "/var/www/html/distributor"
    ServerName distributor.local
    <Directory "/var/www/html/distributor">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### **3. Linux Hosts File**
```bash
# /etc/hosts
127.0.0.1 distributor.local
```

## 🚀 **Production Server Setup (Linux)**

### **1. Server Requirements**
```bash
# Minimum Requirements
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.2+
- Apache 2.4+ / Nginx
- SSL Certificate
```

### **2. Production Config**
```php
<?php
// config.production.php (untuk production)
define('DB_HOST', 'localhost'); // atau IP database server
define('DB_USER', 'prod_user'); // user khusus production
define('DB_PASS', 'strong_password_here'); // password yang aman
define('DB_NAME', 'distributor_prod'); // database production
define('DB_NAME_ALAMAT', 'alamat_db_prod');
define('DB_PORT', 3306);

// Production settings
error_reporting(0); // Hide errors di production
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/production_errors.log');
?>
```

## 📝 **File Konfigurasi Multi-Environment**

### **config.php (Universal)**
```php
<?php
// Auto-detect environment
$environment = 'development'; // default
if (file_exists(__DIR__ . '/config.production.php')) {
    require_once __DIR__ . '/config.production.php';
    $environment = 'production';
} elseif (file_exists(__DIR__ . '/config.staging.php')) {
    require_once __DIR__ . '/config.staging.php';
    $environment = 'staging';
} else {
    // Development config
    require_once __DIR__ . '/config.development.php';
    $environment = 'development';
}

// Environment-specific settings
switch ($environment) {
    case 'production':
        error_reporting(0);
        ini_set('display_errors', 0);
        break;
    case 'staging':
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        break;
    default: // development
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        break;
}
?>
```

### **config.development.php**
```php
<?php
// Development settings (Windows/Linux)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '8208'); // XAMPP default: '' atau Linux password
define('DB_NAME', 'distributor');
define('DB_NAME_ALAMAT', 'alamat_db');
define('DB_PORT', 3306);

// Auto-detect XAMPP password
if (is_windows() && is_xampp() && DB_PASS === '8208') {
    // XAMPP default password biasanya kosong
    define('DB_PASS', '');
}
?>
```

## 🔧 **Database Migration Cross-Platform**

### **Universal Migration Script**
```php
<?php
// database/cross_platform_setup.php
require_once __DIR__ . '/../config.php';

echo "=== Cross-Platform Database Setup ===\n";
echo "OS: " . PHP_OS . "\n";
echo "Environment: " . (is_development() ? 'Development' : 'Production') . "\n";

// Import schema files
$schema_files = [
    'schema_core.sql',
    'schema_add_tipe_alamat.sql'
];

foreach ($schema_files as $file) {
    $filepath = __DIR__ . '/' . $file;
    if (file_exists($filepath)) {
        echo "Importing: $file\n";
        $sql = file_get_contents($filepath);
        
        // Remove comments and split statements
        $sql = preg_replace('/--.*$/m', '', $sql);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                if (!$conn->query($statement)) {
                    echo "Error: " . $conn->error . "\n";
                    echo "Statement: $statement\n";
                }
            }
        }
        echo "✓ $file imported successfully\n";
    } else {
        echo "✗ File not found: $file\n";
    }
}

// Insert sample data
echo "\n=== Inserting Sample Data ===\n";
// [Sample data insertion code]

echo "\n=== Setup Complete ===\n";
?>
```

## 📋 **Checklist Development Cross-Platform**

### **Windows (XAMPP) Setup**
- [ ] XAMPP installed (Apache + MySQL + PHP)
- [ ] Virtual host configured (distributor.local)
- [ ] Windows hosts file updated
- [ ] MySQL database created
- [ ] Schema imported via phpMyAdmin
- [ ] File permissions set (read/write for logs/uploads)

### **Linux Development Setup**
- [ ] LAMP/LEMP stack installed
- [ ] Virtual host configured
- [ ] Linux hosts file updated
- [ ] MySQL/MariaDB database created
- [ ] Schema imported
- [ ] File permissions set (chmod 755/644)

### **Production Server Setup**
- [ ] Server requirements met
- [ ] SSL certificate installed
- [ ] Production database created
- [ ] Environment-specific config applied
- [ ] File permissions secured
- [ ] Backup system configured

## 🔍 **Testing Cross-Platform**

### **Automated Testing Script**
```php
<?php
// test_platform_compatibility.php
require_once 'config.php';

$tests = [
    'Database Connection' => function() use ($conn) {
        return $conn->ping();
    },
    'Alamat Database Connection' => function() use ($conn_alamat) {
        return $conn_alamat ? $conn_alamat->ping() : false;
    },
    'Required Tables' => function() use ($conn) {
        $required = ['orang', 'user_accounts', 'roles', 'perusahaan'];
        foreach ($required as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows == 0) return false;
        }
        return true;
    },
    'File Permissions' => function() {
        $dirs = ['logs', 'uploads', 'temp'];
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            if (!is_writable($dir)) return false;
        }
        return true;
    }
];

echo "=== Platform Compatibility Test ===\n";
foreach ($tests as $test => $callback) {
    $result = $callback() ? '✓ PASS' : '✗ FAIL';
    echo "$test: $result\n";
}
?>
```

## 🚨 **Common Issues & Solutions**

### **Windows/XAMPP Issues**
1. **Connection Failed**: Password XAMPP default kosong
2. **Permission Denied**: Run XAMPP as Administrator
3. **Port 80 Busy**: Stop Skype/IIS or change Apache port

### **Linux Issues**
1. **Permission Denied**: sudo chown -R www-data:www-data /var/www/html
2. **MySQL Socket**: Check /var/run/mysqld/mysqld.sock
3. **SELinux**: sudo setsebool -P httpd_can_network_connect 1

### **Production Issues**
1. **Database Connection**: Check firewall and MySQL bind address
2. **File Uploads**: Check upload_max_filesize and post_max_size
3. **SSL Issues**: Verify certificate chain and Apache config

## 📦 **Deployment Package**

### **Package Structure**
```
distributor-package/
├── app/                      # All PHP files
├── database/
│   ├── schema_core.sql
│   ├── schema_add_tipe_alamat.sql
│   └── setup_database.php
├── config/
│   ├── config.php.template
│   ├── config.development.php
│   └── config.production.php
├── docs/
│   ├── CROSS_PLATFORM_GUIDE.md
│   └── DEPLOYMENT_GUIDE.md
├── scripts/
│   ├── deploy.sh
│   └── backup.sh
└── README.md
```

---

**📅 Update**: 21 Januari 2026  
**👤 Author**: Cascade AI Assistant  
**🎯 Goal**: Cross-platform compatibility achieved
