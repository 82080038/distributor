# Panduan Prioritas Implementasi Sistem Distribusi

## **🎯 Fokus Utama: Flow & Logika Bisnis yang Benar**

### **❌ TIDAK PERLU DIKERJAKAN SAAT INI**
- **React Native app untuk iOS/Android**
- **Advanced AI/ML features**
- **Blockchain integration**
- **Complex third-party integrations**
- **Enterprise-level security features**
- **Multi-region deployment**

### **✅ YANG PERLU DIFOKUSKAN SAAT INI**

## **📋 Prioritas Implementasi (Phase 1-3)**

### **🥇 Phase 1: Foundation & Core Business Logic (Bulan 1-2)**

#### **1.1 Database Setup & Migration**
```sql
-- Import schema yang sudah dibuat
mysql -u username -p database_name < database_schema/crm_schema.sql
mysql -u username -p database_name < database_schema/accounting_schema.sql
mysql -u username -p database_name < database_schema/warehouse_management_schema.sql
mysql -u username -p database_name < database_schema/multi_channel_schema.sql
mysql -u username -p database_name < database_schema/identity_management_schema.sql
```

#### **1.2 Core PHP Implementation**
```php
// config.php - Konfigurasi dasar
<?php
// Database connection
$host = 'localhost';
$dbname = 'distribusi';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

// Error handling
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Session management
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Jakarta');
?>
```

#### **1.3 Authentication System**
```php
// auth.php - Sederhana namun aman
<?php
require_once 'config.php';

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

function current_user() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function login($username, $password) {
    global $conn;
    
    $sql = "SELECT * FROM users WHERE username = ? AND is_active = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['branch_id'] = $user['branch_id'];
        return true;
    }
    
    return false;
}

function logout() {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
```

#### **1.4 Basic CRUD Operations**
```php
// products.php - Master produk sederhana
<?php
require_once 'auth.php';
require_login();

// Create product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'create') {
        $name = $_POST['name'];
        $code = $_POST['code'];
        $unit = $_POST['unit'];
        $buy_price = $_POST['buy_price'];
        $sell_price = $_POST['sell_price'];
        $category_id = $_POST['category_id'];
        
        $sql = "INSERT INTO products (name, code, unit, buy_price, sell_price, category_id, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 1, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssdddi', $name, $code, $unit, $buy_price, $sell_price, $category_id);
        
        if ($stmt->execute()) {
            $success = "Produk berhasil ditambahkan";
        } else {
            $error = "Gagal menambahkan produk";
        }
    }
}

// Read products
$products = [];
$sql = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN product_categories c ON p.category_id = c.id 
          WHERE p.is_active = 1 
          ORDER BY p.name";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
?>

<!-- Simple HTML form -->
<form method="POST">
    <input type="hidden" name="action" value="create">
    <div class="form-group">
        <label>Nama Produk</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Kode Produk</label>
        <input type="text" name="code" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Satuan</label>
        <input type="text" name="unit" class="form-control" value="pcs" required>
    </div>
    <div class="form-group">
        <label>Harga Beli</label>
        <input type="number" name="buy_price" class="form-control" step="0.01" required>
    </div>
    <div class="form-group">
        <label>Harga Jual</label>
        <input type="number" name="sell_price" class="form-control" step="0.01" required>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
</form>
```

### **🥈 Phase 2: Core Business Logic (Bulan 3-4)**

#### **2.1 Purchase Management**
```php
// purchases.php - Modul pembelian yang sudah ada
// Fokus pada flow bisnis yang benar

// Generate invoice number otomatis
function generate_purchase_invoice_no($branch_id, $purchase_date) {
    $date_part = date('Ymd', strtotime($purchase_date));
    $prefix = 'PB';
    $branch_part = str_pad($branch_id, 3, '0', STR_PAD_LEFT);
    
    $sql = "SELECT invoice_no FROM purchases 
              WHERE branch_id = ? AND purchase_date = ? 
              AND invoice_no LIKE ? 
              ORDER BY invoice_no DESC 
              LIMIT 1";
    
    $like_pattern = $prefix . '-' . $branch_part . '-' . $date_part . '-%';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iss', $branch_id, $purchase_date, $like_pattern);
    $stmt->execute();
    
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $last = $row['invoice_no'];
        $parts = explode('-', $last);
        $last_seq = (int)end($parts);
        $next_number = $last_seq + 1;
    } else {
        $next_number = 1;
    }
    
    $seq_part = str_pad($next_number, 4, '0', STR_PAD_LEFT);
    return $prefix . '-' . $branch_part . '-' . $date_part . '-' . $seq_part;
}

// Stock update logic
function update_stock($product_id, $quantity, $branch_id, $type = 'in') {
    $sql = "UPDATE warehouse_stocks 
              SET quantity_on_hand = quantity_on_hand + ? 
              WHERE product_id = ? AND warehouse_id = ?";
    
    $adjustment = ($type === 'in') ? $quantity : -$quantity;
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('dii', $adjustment, $product_id, $branch_id);
    
    return $stmt->execute();
}

// Purchase validation
function validate_purchase($supplier_id, $items) {
    $errors = [];
    
    // Validate supplier
    $sql = "SELECT id FROM suppliers WHERE id = ? AND is_active = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $supplier_id);
    $stmt->execute();
    
    if (!$stmt->get_result()->fetch_assoc()) {
        $errors[] = "Supplier tidak valid atau tidak aktif";
    }
    
    // Validate items
    if (empty($items)) {
        $errors[] = "Minimal harus ada satu item pembelian";
    }
    
    foreach ($items as $item) {
        if ($item['quantity'] <= 0) {
            $errors[] = "Quantity harus lebih besar dari 0";
        }
        
        if ($item['price'] < 0) {
            $errors[] = "Harga tidak boleh negatif";
        }
    }
    
    return $errors;
}
```

#### **2.2 Sales Management**
```php
// sales.php - Modul penjualan sederhana
function generate_sales_invoice_no($branch_id, $sale_date) {
    $date_part = date('Ymd', strtotime($sale_date));
    $prefix = 'SJ';
    $branch_part = str_pad($branch_id, 3, '0', STR_PAD_LEFT);
    
    $sql = "SELECT invoice_no FROM sales 
              WHERE branch_id = ? AND sale_date = ? 
              AND invoice_no LIKE ? 
              ORDER BY invoice_no DESC 
              LIMIT 1";
    
    $like_pattern = $prefix . '-' . $branch_part . '-' . $date_part . '-%';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iss', $branch_id, $sale_date, $like_pattern);
    $stmt->execute();
    
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $last = $row['invoice_no'];
        $parts = explode('-', $last);
        $last_seq = (int)end($parts);
        $next_number = $last_seq + 1;
    } else {
        $next_number = 1;
    }
    
    $seq_part = str_pad($next_number, 4, '0', STR_PAD_LEFT);
    return $prefix . '-' . $branch_part . '-' . $date_part . '-' . $seq_part;
}

// Stock check before sale
function check_stock_availability($product_id, $quantity, $branch_id) {
    $sql = "SELECT quantity_available 
              FROM warehouse_stocks 
              WHERE product_id = ? AND warehouse_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $product_id, $branch_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    return $result && $result['quantity_available'] >= $quantity;
}

// Calculate profit margin
function calculate_profit_margin($buy_price, $sell_price) {
    if ($buy_price <= 0) return 0;
    
    $profit = $sell_price - $buy_price;
    $margin = ($profit / $buy_price) * 100;
    
    return [
        'profit' => $profit,
        'margin_percentage' => round($margin, 2)
    ];
}
```

#### **2.3 Inventory Management**
```php
// inventory.php - Manajemen stok yang realistis
function get_stock_alerts($branch_id) {
    $sql = "SELECT p.name, p.code, ws.quantity_available, ws.reorder_level, ws.max_level
              FROM warehouse_stocks ws
              JOIN products p ON ws.product_id = p.id
              WHERE ws.warehouse_id = ? 
              AND ws.quantity_available <= ws.reorder_level
              ORDER BY ws.quantity_available ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $branch_id);
    $stmt->execute();
    
    $alerts = [];
    while ($row = $stmt->get_result()->fetch_assoc()) {
        $alerts[] = [
            'product_name' => $row['name'],
            'product_code' => $row['code'],
            'current_stock' => $row['quantity_available'],
            'reorder_level' => $row['reorder_level'],
            'max_level' => $row['max_level'],
            'status' => 'CRITICAL'
        ];
    }
    
    return $alerts;
}

// Stock adjustment logic
function adjust_stock($product_id, $branch_id, $adjustment, $reason, $user_id) {
    $conn->begin_transaction();
    
    try {
        // Get current stock
        $sql = "SELECT quantity_available FROM warehouse_stocks 
                  WHERE product_id = ? AND warehouse_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $product_id, $branch_id);
        $stmt->execute();
        $current = $stmt->get_result()->fetch_assoc();
        
        if (!$current) {
            throw new Exception("Product not found in warehouse");
        }
        
        $new_quantity = $current['quantity_available'] + $adjustment;
        
        if ($new_quantity < 0) {
            throw new Exception("Stock cannot be negative");
        }
        
        // Update stock
        $sql = "UPDATE warehouse_stocks 
                  SET quantity_available = ?, updated_at = NOW() 
                  WHERE product_id = ? AND warehouse_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('dii', $new_quantity, $product_id, $branch_id);
        $stmt->execute();
        
        // Log adjustment
        $sql = "INSERT INTO stock_adjustments 
                  (product_id, warehouse_id, adjustment, reason, user_id, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iidsis', $product_id, $branch_id, $adjustment, $reason, $user_id);
        $stmt->execute();
        
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}
```

### **🥉 Phase 3: Reporting & Analytics (Bulan 5-6)**

#### **3.1 Basic Reporting**
```php
// report_sales.php - Laporan penjualan sederhana
function get_sales_report($branch_id, $start_date, $end_date) {
    $sql = "SELECT s.invoice_no, s.sale_date, c.name as customer_name, 
                     s.total_amount, s.payment_method, u.username as cashier
              FROM sales s
              JOIN customers c ON s.customer_id = c.id
              JOIN users u ON s.cashier_id = u.id
              WHERE s.branch_id = ? 
              AND DATE(s.sale_date) BETWEEN ? AND ?
              ORDER BY s.sale_date DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iss', $branch_id, $start_date, $end_date);
    $stmt->execute();
    
    $reports = [];
    while ($row = $stmt->get_result()->fetch_assoc()) {
        $reports[] = $row;
    }
    
    return $reports;
}

// Calculate daily summary
function get_daily_summary($branch_id, $date) {
    $sql = "SELECT 
              COUNT(*) as total_sales,
              SUM(total_amount) as total_revenue,
              AVG(total_amount) as average_sale,
              COUNT(DISTINCT customer_id) as unique_customers
              FROM sales 
              WHERE branch_id = ? AND DATE(sale_date) = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('is', $branch_id, $date);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_assoc();
}

// Product performance report
function get_product_performance($branch_id, $start_date, $end_date) {
    $sql = "SELECT p.name, p.code,
                     SUM(si.quantity) as total_sold,
                     SUM(si.subtotal) as total_revenue,
                     COUNT(DISTINCT s.id) as sales_count
              FROM sales s
              JOIN sale_items si ON s.id = si.sale_id
              JOIN products p ON si.product_id = p.id
              WHERE s.branch_id = ? 
              AND DATE(s.sale_date) BETWEEN ? AND ?
              GROUP BY p.id, p.name, p.code
              ORDER BY total_revenue DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iss', $branch_id, $start_date, $end_date);
    $stmt->execute();
    
    $performance = [];
    while ($row = $stmt->get_result()->fetch_assoc()) {
        $performance[] = $row;
    }
    
    return $performance;
}
```

#### **3.2 Financial Reports**
```php
// report_financial.php - Laporan keuangan
function get_profit_loss_report($branch_id, $start_date, $end_date) {
    $sql = "SELECT 
              DATE(s.sale_date) as report_date,
              SUM(s.total_amount) as revenue,
              SUM(si.subtotal) as cost_of_goods_sold,
              SUM(s.total_amount - si.subtotal) as gross_profit
              FROM sales s
              JOIN sale_items si ON s.id = si.sale_id
              WHERE s.branch_id = ? 
              AND DATE(s.sale_date) BETWEEN ? AND ?
              GROUP BY DATE(s.sale_date)
              ORDER BY report_date";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iss', $branch_id, $start_date, $end_date);
    $stmt->execute();
    
    $reports = [];
    while ($row = $stmt->get_result()->fetch_assoc()) {
        $reports[] = $row;
    }
    
    return $reports;
}

// Cash flow report
function get_cash_flow_report($branch_id, $start_date, $end_date) {
    $sql = "SELECT 
              DATE(created_at) as report_date,
              SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) as cash_in,
              SUM(CASE WHEN amount < 0 THEN amount ELSE 0 END) as cash_out
              FROM cash_transactions
              WHERE branch_id = ? 
              AND DATE(created_at) BETWEEN ? AND ?
              GROUP BY DATE(created_at)
              ORDER BY report_date";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iss', $branch_id, $start_date, $end_date);
    $stmt->execute();
    
    $reports = [];
    while ($row = $stmt->get_result()->fetch_assoc()) {
        $reports[] = $row;
    }
    
    return $reports;
}
```

## **🎯 Flow Bisnis yang Benar (Best Practices)**

### **1. Purchase Flow**
1. **Supplier Selection** → Validasi supplier aktif
2. **Purchase Order** → Generate nomor otomatis
3. **Goods Receipt** → Update stok otomatis
4. **Invoice Processing** → Update hutang usaha
5. **Payment** → Update cash flow

### **2. Sales Flow**
1. **Customer Selection** → Validasi kredit (jika ada)
2. **Stock Check** → Validasi ketersediaan stok
3. **Order Processing** → Generate nomor otomatis
4. **Payment** → Multiple payment methods
5. **Delivery** → Update stok otomatis

### **3. Inventory Flow**
1. **Stock In** → Purchase, return, adjustment
2. **Stock Out** → Sales, transfer, disposal
3. **Stock Alert** → Reorder point notification
4. **Stock Count** → Physical vs system validation
5. **Stock Valuation** → FIFO/LIFO/Average cost

## **📊 Metrics yang Perlu Dimonitor**

### **Daily Metrics**
- Total penjualan
- Total pembelian
- Cash flow
- Stok levels
- Profit margin

### **Weekly Metrics**
- Customer acquisition
- Product performance
- Supplier performance
- Inventory turnover

### **Monthly Metrics**
- Revenue growth
- Cost analysis
- Customer retention
- Profit & loss

---

**Timeline Implementasi:** 6 bulan untuk core business logic
**Team Size:** 2-3 developers
**Success Criteria:** Flow bisnis benar, data akurat, laporan lengkap

**Kesimpulan:** Fokus pada implementasi **flow bisnis yang benar dan logika yang realistis** sebelum menambah fitur-fitur advanced. Sistem akan lebih stabil dan sesuai dengan kebutuhan aktual di lapangan.
