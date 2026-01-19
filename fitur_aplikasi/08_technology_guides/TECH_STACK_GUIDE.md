# Teknologi Stack Guide - Sistem Distribusi

## **🛠️ Teknologi yang Digunakan**

### **Backend Stack**
- **PHP 8.1+** - Bahasa pemrograman utama
- **MySQL 8.0+** - Database management system
- **Composer** - Dependency management
- **Apache/Nginx** - Web server
- **jQuery** - JavaScript library
- **Bootstrap 5** - CSS framework

### **Frontend Stack**
- **HTML5** - Markup language
- **CSS3** - Styling
- **JavaScript (ES6)** - Client-side scripting
- **Bootstrap 5** - UI framework
- **Chart.js** - Data visualization

### **Development Tools**
- **VS Code** - IDE untuk PHP development
- **phpMyAdmin** - Database management
- **Git** - Version control
- **Postman** - API testing

## **📋 Persyaratan Sistem**

### **Minimum Requirements**
- **PHP 8.1+** dengan extension berikut:
  - `mysqli` untuk database MySQL
  - `gd` untuk image processing
  - `curl` untuk HTTP requests
  - `json` untuk API responses
  - `mbstring` untuk string multibyte
  - `openssl` untuk encryption (jika diperlukan)

- **MySQL 8.0+** dengan konfigurasi:
  - `innodb_buffer_pool_size = 128M` (untuk performa)
  - `max_allowed_packet = 64M`
  - `query_cache_size = 64M`

- **Web Server:**
  - Apache 2.4+ atau Nginx 1.18+
  - Modul `mod_rewrite` untuk clean URLs
  - SSL/TLS certificate (untuk production)

### **Recommended Browser Support**
- **Chrome 80+** - Development
- **Firefox 75+** - Development
- **Safari 12+** - Development
- **Edge 80+** - Development

## **🚀 Setup Development Environment**

### **1. Install XAMPP/WAMP/MAMP**
```bash
# Download XAMPP dari https://www.apachefriends.org/
# Install dan jalankan
# Akses phpMyAdmin via http://localhost/phpmyadmin
```

### **2. Install Composer Dependencies**
```bash
# Di folder project
composer install
composer update
composer require --dev phpunit/phpunit
```

### **3. Konfigurasi Virtual Host**
```apache
# Untuk XAMPP di C:\xampp\htdocs\distribusi
<VirtualHost *:80>
    ServerName distribusi.local
    DocumentRoot "C:/xampp/htdocs/distribusi"
    <Directory "C:/xampp/htdocs/distribusi">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### **4. Setup Database**
```sql
-- Import database schema
mysql -u root -p < database_name < schema.sql

-- Buat user untuk aplikasi
CREATE USER 'distribusi_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON database_name.* TO 'distribusi_user'@'localhost';
FLUSH PRIVILEGES;
```

## **📝 Struktur Project Direkomendasikan**

```
distribusi/
├── config.php                    # Konfigurasi database
├── auth.php                      # Autentikasi user
├── template.php                   # Template HTML
├── index.php                      # Halaman utama
├── purchases.php                 # Modul pembelian
├── purchases_view.php             # View pembelian
├── products.php                  # Master produk
├── suppliers.php                 # Master supplier
├── customers.php                 # Master pelanggan
├── sales.php                     # Modul penjualan
├── report_*.php                  # Laporan-laporan
├── assets/                       # Static files
│   ├── css/                   # Bootstrap + custom CSS
│   ├── js/                    # JavaScript files
│   └── images/               # Gambar produk
└── vendor/                       # Composer dependencies
    ├── composer.json
    └── autoload.php
```

## **🔧 Konfigurasi PHP (php.ini)**

```ini
; Direkomendasikan untuk performa dan keamanan
max_execution_time = 300
memory_limit = 512M
post_max_size = 64M
upload_max_filesize = 64M
max_file_uploads = 20
file_uploads = On
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
display_errors = On
log_errors = On
date.timezone = Asia/Jakarta

; Extension yang diperlukan
extension=mysqli
extension=gd
extension=curl
extension=json
extension=mbstring
extension=openssl
```

## **🎨 Best Practices Development**

### **1. Security**
```php
// Selalu gunakan prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

// Hindari SQL Injection
$user_id = (int)$_GET['id']; // Sanitasi input

// Gunakan password_hash() untuk password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// CSRF protection
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
}
```

### **2. Performance**
```php
// Gunakan persistent connection
$conn = new mysqli("localhost", "user", "password", "database");
$conn->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES'");

// Indexing untuk query cepat
CREATE INDEX idx_customer_name ON customers(name);
CREATE INDEX idx_product_category ON products(category_id);

// Caching untuk data yang sering diakses
$cache_key = "products_list_" . $category_id;
$cached_data = apcu_fetch($cache_key);
if ($cached_data === false) {
    $data = $db->query("SELECT * FROM products WHERE category_id = $category_id");
    apcu_store($cache_key, $data, 3600); // Cache 1 jam
}
```

### **3. Error Handling**
```php
// Custom error handler
set_error_handler(function($severity, $message, $file, $line) {
    error_log("[$severity] $message in $file on line $line");
    
    if (ini_get('display_errors')) {
        echo "<div class='alert alert-danger'>$message</div>";
    }
});

// Exception handling
try {
    $result = $conn->query($sql);
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    // Handle error gracefully
}
```

## **📱 Mobile Development**

### **Progressive Web App (PWA)**
```javascript
// Service Worker untuk offline capability
if ('serviceWorker' in navigator) {
    // Register service worker untuk offline functionality
}

// Web App Manifest
{
    "name": "Sistem Distribusi",
    "short_name": "Distribusi",
    "display": "standalone",
    "background_color": "#2196F3",
    "theme_color": "#1976D2",
    "start_url": "/",
    "scope": "/",
    "icons": [
        {
            "src": "assets/icons/icon-192x192.png",
            "sizes": "192x192",
            "type": "image/png"
        }
    ]
}
```

## **🚀 Deployment**

### **Production Server Setup**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/distribusi;
    index index.php;
    
    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml text/xml+rss text/javascript+xml;
    
    # Security headers
    add_header X-Frame-Options DENY;
    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection "1; mode=block";
    
    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        include fastcgi_params;
        fastcgi_index index.php;
        
        # Security
        fastcgi_param HTTPS on;
        fastcgi_param SERVER_NAME $host;
    }
    
    location / {
        try_files $uri $uri/ = /index.php?$args;
    }
}
```

## **📊 Monitoring & Logging**

### **Log Structure**
```bash
# Application logs
/var/log/distribusi/
├── access.log          # Access log
├── error.log           # Error log
├── sql.log             # Query log
└── audit.log           # Audit trail log

# Log rotation setup
logrotate /etc/logrotate.d/distribusi
```

### **Performance Monitoring**
```php
// Performance metrics collection
class PerformanceMonitor {
    public static function logQuery($sql, $execution_time) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'sql' => $sql,
            'execution_time' => $execution_time,
            'memory_usage' => memory_get_usage(true)
        ];
        
        file_put_contents('/var/log/distribusi/performance.log', json_encode($log_entry) . "\n", FILE_APPEND);
    }
}
```

## **🔧 Development Tools**

### **VS Code Extensions**
- **PHP Intelephense** - Autocompletion dan debugging
- **MySQL Tools** - Database management
- **GitLens** - Git integration
- **PHP Debug Bar** - Debug toolbar
- **Browser Sync** - Live reload browser

### **Testing Framework**
```json
{
    "require-dev": {
        "phpunit/phpunit": "^9.5",
        "symfony/phpunit": "^6.0",
        "mockery/mockery": "^1.4"
    },
    "require": {
        "php": ">=8.1",
        "ext-mysqli": "*",
        "ext-gd": "*",
        "ext-curl": "*",
        "ext-json": "*",
        "ext-mbstring": "*"
    }
}
```

## **📚 Referensi Belajar**

### **Dokumentasi PHP**
- https://www.php.net/manual/en/
- https://www.w3schools.com/php/
- https://developer.mozilla.org/en-US/docs/Web/HTML

### **Dokumentasi MySQL**
- https://dev.mysql.com/doc/
- https://www.mysqltutorial.org/
- https://www.phpmyadmin.com/docs/

### **Dokumentasi Bootstrap**
- https://getbootstrap.com/docs/
- https://www.w3schools.com/bootstrap/

### **Dokumentasi jQuery**
- https://api.jquery.com/
- https://www.w3schools.com/jquery/

---

**Timeline Setup:** 2-3 minggu untuk environment setup
**Team Size:** 1-2 developers
**Success Criteria:** Development environment siap dan dokumentasi lengkap
