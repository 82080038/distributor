# Multi-Company Setup Guide

## **Overview**
Sistem ini dirancang untuk mendukung multiple perusahaan dalam satu instalasi dengan metode usaha yang sama (retail/distributor). Setiap perusahaan memiliki data terpisah namun menggunakan fitur dan logic yang sama.

## **Arsitektur Multi-Company**

### **1. Database Structure**

#### **Company-Based Table Prefixing**
```sql
-- Setiap perusahaan memiliki prefix tabel sendiri
-- Contoh: company_1_customers, company_2_products, dll

-- Dynamic table creation per company
CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_code VARCHAR(20) UNIQUE NOT NULL,
    company_name VARCHAR(150) NOT NULL,
    database_name VARCHAR(100) NOT NULL, -- Nama database terpisah
    table_prefix VARCHAR(20) NOT NULL, -- Prefix untuk tabel
    domain VARCHAR(150), -- Domain untuk akses
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contoh penggunaan dengan prefix dinamis
SET @company_id = 1;
SET @table_prefix = (SELECT table_prefix FROM companies WHERE id = @company_id);

-- Dynamic table creation
SET @sql = CONCAT('
    CREATE TABLE IF NOT EXISTS ', @table_prefix, 'customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_code VARCHAR(50) UNIQUE NOT NULL,
        name VARCHAR(150) NOT NULL,
        company_id INT NOT NULL DEFAULT ', @company_id, ',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
```

#### **Shared vs Company-Specific Data**
```sql
-- Tabel shared (digunakan semua perusahaan)
CREATE TABLE shared_configurations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) NOT NULL,
    config_value TEXT,
    config_type ENUM('SYSTEM', 'BUSINESS_RULE', 'FEATURE_FLAG') DEFAULT 'SYSTEM',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel company-specific
CREATE TABLE company_configurations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    config_key VARCHAR(100) NOT NULL,
    config_value TEXT,
    config_type ENUM('SYSTEM', 'BUSINESS_RULE', 'FEATURE_FLAG') DEFAULT 'SYSTEM',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);
```

### **2. Application Configuration**

#### **Multi-Database Connection Management**
```php
// config/multi_company_config.php
class MultiCompanyConfig {
    private static $companies = [];
    
    public static function initialize() {
        // Load company configurations
        $sql = "SELECT * FROM companies WHERE is_active = 1";
        $result = $GLOBALS['conn']->query($sql);
        
        while ($company = $result->fetch_assoc()) {
            self::$companies[$company['id']] = [
                'code' => $company['company_code'],
                'name' => $company['company_name'],
                'database' => $company['database_name'],
                'prefix' => $company['table_prefix'],
                'domain' => $company['domain'],
                'connection' => null // Will be initialized on demand
            ];
        }
    }
    
    public static function getCompanyConnection($company_id) {
        if (!isset(self::$companies[$company_id])) {
            throw new Exception("Company $company_id not found");
        }
        
        $company = self::$companies[$company_id];
        
        // Create connection if not exists
        if (!self::$companies[$company_id]['connection']) {
            self::$companies[$company_id]['connection'] = self::createCompanyConnection($company);
        }
        
        return self::$companies[$company_id]['connection'];
    }
    
    private static function createCompanyConnection($company) {
        try {
            $connection = new mysqli(
                DB_HOST, 
                DB_USER, 
                DB_PASS, 
                $company['database'], 
                DB_PORT
            );
            
            // Set character set
            $connection->set_charset('utf8mb4');
            
            return $connection;
        } catch (Exception $e) {
            error_log("Failed to connect to company {$company['database']}: " . $e->getMessage());
            return null;
        }
    }
    
    public static function getAllCompanies() {
        return self::$companies;
    }
    
    public static function getCompanyById($company_id) {
        return self::$companies[$company_id] ?? null;
    }
}
```

#### **Dynamic Model Loading**
```php
// models/BaseModel.php
class BaseModel {
    protected $db;
    protected $company_id;
    protected $table_prefix;
    
    public function __construct($company_id = null) {
        $this->company_id = $company_id ?: $this->getCurrentCompanyId();
        $company = MultiCompanyConfig::getCompanyById($this->company_id);
        
        $this->table_prefix = $company['prefix'];
        $this->db = MultiCompanyConfig::getCompanyConnection($this->company_id);
    }
    
    protected function getTableName($table_name) {
        return $this->table_prefix . $table_name;
    }
    
    protected function getCurrentCompanyId() {
        // Get from session, subdomain, or configuration
        return $_SESSION['company_id'] ?? 1; // Default to company 1
    }
}

// models/Customer.php
class Customer extends BaseModel {
    public function create($data) {
        $table_name = $this->getTableName('customers');
        $sql = "INSERT INTO $table_name (customer_code, name, email, company_id) VALUES (?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssi', 
            $data['customer_code'], 
            $data['name'], 
            $data['email'], 
            $this->company_id
        );
        
        return $stmt->execute();
    }
    
    public function findByCompany($company_id = null) {
        if ($company_id && $company_id != $this->company_id) {
            throw new Exception("Access denied: Different company");
        }
        
        $table_name = $this->getTableName('customers');
        $sql = "SELECT * FROM $table_name WHERE company_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $this->company_id);
        $stmt->execute();
        
        $customers = [];
        while ($row = $stmt->get_result()->fetch_assoc()) {
            $customers[] = $row;
        }
        
        $stmt->close();
        return $customers;
    }
}
```

### **3. Multi-Tenant Authentication**

#### **Company Identification via Subdomain**
```php
// middleware/CompanyDetection.php
class CompanyDetection {
    public static function detectCompany() {
        // Method 1: Subdomain-based
        $host = $_SERVER['HTTP_HOST'];
        $subdomain = explode('.', $host)[0] ?? '';
        
        // Method 2: Header-based
        $company_header = $_SERVER['HTTP_X_COMPANY_ID'] ?? '';
        
        // Method 3: Session-based
        $session_company = $_SESSION['company_id'] ?? '';
        
        // Method 4: URL parameter
        $url_company = $_GET['company'] ?? '';
        
        // Priority order
        $company_id = null;
        
        if (!empty($url_company)) {
            $company_id = $this->getCompanyByCode($url_company);
        } elseif (!empty($company_header)) {
            $company_id = $this->getCompanyByCode($company_header);
        } elseif (!empty($session_company)) {
            $company_id = $this->getCompanyByCode($session_company);
        } elseif (!empty($subdomain)) {
            $company_id = $this->getCompanyBySubdomain($subdomain);
        }
        
        return $company_id ?: $this->getDefaultCompanyId();
    }
    
    private static function getCompanyByCode($code) {
        $sql = "SELECT id FROM companies WHERE company_code = ? AND is_active = 1";
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param('s', $code);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row ? $row['id'] : null;
    }
    
    private static function getCompanyBySubdomain($subdomain) {
        // Map subdomain to company
        $mapping = [
            'company1' => 1,
            'company2' => 2,
            'pt-maju' => 3,
            'cv-berkah' => 4
        ];
        
        return $mapping[$subdomain] ?? null;
    }
    
    private static function getDefaultCompanyId() {
        return 1; // Default company
    }
}
```

#### **Session Management per Company**
```php
// auth/MultiCompanySession.php
class MultiCompanySession {
    public static function initialize($company_id) {
        $_SESSION['company_id'] = $company_id;
        $_SESSION['last_activity'] = time();
        
        // Load company-specific configurations
        $_SESSION['company_config'] = self::loadCompanyConfig($company_id);
    }
    
    public static function validateSession() {
        $company_id = $_SESSION['company_id'] ?? null;
        $last_activity = $_SESSION['last_activity'] ?? 0;
        
        if (!$company_id) {
            return false;
        }
        
        // Check session timeout (2 hours)
        if (time() - $last_activity > 7200) {
            self::destroy();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    public static function destroy() {
        session_destroy();
        session_regenerate_id(true);
    }
    
    private static function loadCompanyConfig($company_id) {
        $sql = "
            SELECT config_key, config_value 
            FROM company_configurations 
            WHERE company_id = ? AND is_active = 1
        ";
        
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param('i', $company_id);
        $stmt->execute();
        
        $config = [];
        while ($row = $stmt->get_result()->fetch_assoc()) {
            $config[$row['config_key']] = $row['config_value'];
        }
        
        $stmt->close();
        
        return $config;
    }
}
```

### **4. Multi-Company API**

#### **Company Context in API**
```php
// api/multi_company_api.php
class MultiCompanyAPI {
    private $company_id;
    
    public function __construct() {
        // Detect company from request
        $this->company_id = CompanyDetection::detectCompany();
        
        if (!$this->company_id) {
            $this->sendResponse(false, null, 'Company not detected', 400);
        }
    }
    
    private function sendResponse($success, $data = null, $message = '', $http_code = 200) {
        http_response_code($http_code);
        echo json_encode([
            'success' => $success,
            'data' => $data,
            'message' => $message,
            'company_id' => $this->company_id,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    public function getCustomers() {
        $customer = new Customer($this->company_id);
        $customers = $customer->findByCompany();
        
        $this->sendResponse(true, $customers);
    }
    
    public function createCustomer() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Add company_id to input
        $input['company_id'] = $this->company_id;
        
        $customer = new Customer($this->company_id);
        $result = $customer->create($input);
        
        if ($result) {
            $this->sendResponse(true, ['customer_id' => $customer->getLastInsertId()], 'Company created successfully');
        } else {
            $this->sendResponse(false, null, 'Failed to create customer');
        }
    }
}
```

### **5. Frontend Multi-Company Support**

#### **Dynamic Theme per Company**
```javascript
// public/js/multi_company.js
class MultiCompanyFrontend {
    constructor() {
        this.companyId = this.detectCompany();
        this.companyConfig = null;
        this.initializeCompany();
    }
    
    detectCompany() {
        // Method 1: URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const urlCompany = urlParams.get('company');
        
        // Method 2: Subdomain
        const subdomain = window.location.hostname.split('.')[0];
        
        // Method 3: LocalStorage
        const storedCompany = localStorage.getItem('company_id');
        
        return urlCompany || this.getCompanyBySubdomain(subdomain) || storedCompany || 1;
    }
    
    async initializeCompany() {
        try {
            // Load company configuration
            this.companyConfig = await this.loadCompanyConfig(this.companyId);
            
            // Apply company-specific theme
            this.applyCompanyTheme(this.companyConfig);
            
            // Update API base URL
            this.updateApiBaseUrl(this.companyConfig);
            
            // Initialize company-specific features
            this.initializeFeatures(this.companyConfig);
            
        } catch (error) {
            console.error('Failed to initialize company:', error);
        }
    }
    
    async loadCompanyConfig(companyId) {
        const response = await fetch(`/api/company-config/${companyId}`);
        return response.json();
    }
    
    applyCompanyTheme(config) {
        // Apply company colors, logo, branding
        if (config.primary_color) {
            document.documentElement.style.setProperty('--primary-color', config.primary_color);
        }
        
        if (config.company_logo) {
            document.getElementById('company-logo').src = config.company_logo;
        }
        
        if (config.company_name) {
            document.title = config.company_name + ' - Distribusi System';
        }
    }
    
    updateApiBaseUrl(config) {
        // Update global API base URL for company-specific endpoints
        window.API_BASE_URL = config.api_base_url || '/api';
    }
    
    initializeFeatures(config) {
        // Enable/disable features based on company configuration
        const features = config.enabled_features || {};
        
        Object.keys(features).forEach(feature => {
            const element = document.getElementById(`feature-${feature}`);
            if (element) {
                element.style.display = features[feature] ? 'block' : 'none';
            }
        });
    }
    
    getCompanyBySubdomain(subdomain) {
        const mapping = {
            'company1': 1,
            'company2': 2,
            'pt-maju': 3,
            'cv-berkah': 4
        };
        
        return mapping[subdomain];
    }
}

// Initialize multi-company support
document.addEventListener('DOMContentLoaded', () => {
    new MultiCompanyFrontend();
});
```

### **6. Data Isolation & Security**

#### **Row-Level Security**
```sql
-- Add company_id to all tables for data isolation
ALTER TABLE customers ADD COLUMN company_id INT NOT NULL DEFAULT 1;
ALTER TABLE products ADD COLUMN company_id INT NOT NULL DEFAULT 1;
ALTER TABLE purchases ADD COLUMN company_id INT NOT NULL DEFAULT 1;

-- Create views with company filtering
CREATE VIEW company_customers_view AS
SELECT * FROM customers WHERE company_id = CURRENT_COMPANY_ID();

-- Row-level security policies
CREATE POLICY company_isolation_policy ON customers
    FOR ALL TO app_user
    USING (company_id = CURRENT_COMPANY_ID())
    WITH CHECK (true);
```

#### **Cross-Company Data Prevention**
```php
// Security middleware
class CompanyDataIsolation {
    public static function enforceCompanyIsolation($user_company_id, $requested_company_id) {
        if ($user_company_id !== $requested_company_id) {
            throw new Exception("Access denied: Cannot access data from different company");
        }
    }
    
    public static function getCurrentCompanyId() {
        return $_SESSION['company_id'] ?? 1;
    }
    
    public static function validateDataAccess($table_name, $record_id) {
        $current_company = self::getCurrentCompanyId();
        
        $sql = "SELECT company_id FROM $table_name WHERE id = ?";
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param('i', $record_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$result || $result['company_id'] !== $current_company) {
            throw new Exception("Access denied: Record belongs to different company");
        }
        
        return true;
    }
}
```

## **7. Deployment Configuration**

### **Multi-Company Setup**
```bash
# 1. Create company databases
mysql -u root -p -e "
CREATE DATABASE company_1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE company_2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE company_3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
"

# 2. Import schema to each database
mysql company_1 < database_schema/crm_schema.sql
mysql company_1 < database_schema/accounting_schema.sql
mysql company_2 < database_schema/crm_schema.sql
mysql company_2 < database_schema/accounting_schema.sql

# 3. Create company configuration
mysql main_database < multi_company_setup.sql
```

### **Nginx Configuration for Multi-Company**
```nginx
server {
    listen 80;
    server_name company1.domain.com company2.domain.com company3.domain.com;
    
    # Company 1
    location / {
        root /var/www/company1;
        index index.php;
        fastcgi_param COMPANY_ID 1;
    }
    
    # Company 2
    location / {
        root /var/www/company2;
        index index.php;
        fastcgi_param COMPANY_ID 2;
    }
    
    # Company 3
    location / {
        root /var/www/company3;
        index index.php;
        fastcgi_param COMPANY_ID 3;
    }
    
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_index index.php;
    }
}
```

## **8. Benefits of Multi-Company Setup**

### **For System Administrator:**
- **Centralized Management** - Satu kode base untuk semua perusahaan
- **Easy Scaling** - Tambah perusahaan baru tanpa deployment baru
- **Cost Efficient** - Shared infrastructure, reduced maintenance
- **Unified Updates** - Update semua perusahaan sekaligus
- **Consistent Data** - Sama struktur dan logic di semua perusahaan

### **For Each Company:**
- **Data Isolation** - Data aman dan terpisah
- **Custom Configuration** - Setting per perusahaan
- **Independent Branding** - Logo, warna, domain sendiri
- **Flexible Features** - Enable/disable fitur per kebutuhan
- **Dedicated Support** - Bantuan fokus per perusahaan

## **9. Implementation Checklist**

### **Phase 1: Foundation (Week 1-2)**
- [ ] Multi-company database structure
- [ ] Company detection middleware
- [ ] Dynamic connection management
- [ ] Basic CRUD operations with company isolation

### **Phase 2: Security (Week 3-4)**
- [ ] Row-level security policies
- [ ] Cross-company access prevention
- [ ] Company-specific authentication
- [ ] Audit logging per company

### **Phase 3: Frontend (Week 5-6)**
- [ ] Dynamic theme loading
- [ ] Company switching interface
- [ ] Company-specific branding
- [ ] Multi-company API endpoints

### **Phase 4: Advanced (Week 7-8)**
- [ ] Company-specific configurations
- [ ] Cross-company reporting (aggregated)
- [ ] Company migration tools
- [ ] Performance monitoring per company

---

**Kesimpulan:** Ya, aplikasi ini dapat digunakan oleh multiple perusahaan dengan metode usaha yang sama. Setiap perusahaan akan memiliki database terpisah tetapi menggunakan kode base dan fitur yang sama, dengan sistem keamanan yang menjamin isolasi data antar perusahaan.
