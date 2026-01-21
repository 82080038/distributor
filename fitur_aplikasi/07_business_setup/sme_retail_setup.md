# SME Retail Business Setup Guide

## **Overview**
Panduan implementasi sistem distribusi untuk **Usaha Kecil-Menengah (UKM/SME)** dengan fokus pada retail skala kecil hingga menengah (omzet di bawah 10 miliar per tahun).

## **Karakteristik Bisnis SME Retail**

### **1. Profil Bisnis Target**
- **Usaha Mikro:** Omzet < 300 juta/tahun
- **Usaha Kecil:** Omzet 300 juta - 2.5 miliar/tahun  
- **Usaha Menengah:** Omzet 2.5 - 50 miliar/tahun
- **Jenis:** Toko kelontong, minimarket, warung, toko modern

### **2. Kebutuhan Utama SME**
- **Simplicity** - Interface yang mudah digunakan
- **Affordability** - Biaya implementasi terjangkau
- **Scalability** - Dapat berkembang sesuai pertumbuhan
- **Mobile Priority** - Akses mobile lebih penting daripada desktop
- **Quick Setup** - Instalasi dan konfigurasi cepat
- **Local Compliance** - Sesuai regulasi Indonesia

## **Arsitektur Sistem untuk SME**

### **1. Single-Database Multi-Store**
```sql
-- Struktur tabel untuk SME dengan multiple cabang/toko
CREATE TABLE stores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_code VARCHAR(20) UNIQUE NOT NULL,
    store_name VARCHAR(100) NOT NULL,
    store_type ENUM('FLAGSHIP', 'BRANCH', 'FRANCHISE', 'ONLINE') DEFAULT 'FLAGSHIP',
    address TEXT,
    phone VARCHAR(50),
    email VARCHAR(100),
    owner_name VARCHAR(150),
    manager_id INT,
    tax_npwp VARCHAR(25),
    business_license VARCHAR(100),
    opening_date DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Produk dengan harga per toko
CREATE TABLE store_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_id INT NOT NULL,
    product_id INT NOT NULL,
    selling_price DECIMAL(15,2) NOT NULL,
    cost_price DECIMAL(15,2) NOT NULL,
    markup_percentage DECIMAL(5,2) DEFAULT 0,
    stock_quantity DECIMAL(15,3) DEFAULT 0,
    min_stock_level DECIMAL(15,3) DEFAULT 0,
    max_stock_level DECIMAL(15,3) DEFAULT 0,
    reorder_point DECIMAL(15,3) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE KEY unique_store_product (store_id, product_id)
);

-- Penjualan per toko
CREATE TABLE store_sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_id INT NOT NULL,
    sale_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT,
    sale_date DATETIME NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    final_amount DECIMAL(15,2) NOT NULL,
    payment_method ENUM('CASH', 'TRANSFER', 'E_WALLET', 'CARD', 'QRCODE') DEFAULT 'CASH',
    payment_status ENUM('PAID', 'PARTIAL', 'PENDING') DEFAULT 'PAID',
    cashier_id INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (store_id) REFERENCES stores(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    INDEX idx_store_date (store_id, sale_date),
    INDEX idx_customer_id (customer_id)
);
```

### **2. Simplified Feature Set untuk SME**

#### **Core Features (Wajib)**
1. **Manajemen Produk**
   - Master produk dengan harga beli dan jual
   - Kategori produk
   - Barcode/QR code generation
   - Stok otomatis per toko

2. **Transaksi Penjualan**
   - POS (Point of Sale) yang sederhana
   - Multiple payment methods (cash, transfer, e-wallet, QRIS)
   - Struk otomatis
   - Diskon dan promo sederhana

3. **Manajemen Pelanggan**
   - Database pelanggan sederhana
   - Riwayat pembelian
   - Program loyalitas dasar (poin)
   - Credit limit untuk pelanggan langganan

4. **Laporan Dasar**
   - Laporan penjualan harian/mingguan/bulanan
   - Laporan produk terlaris
   - Laporan stok
   - Laporan keuntungan sederhana

5. **Keuangan Dasar**
   - Jurnal kas sederhana
   - Laporan laba rugi
   - Manajemen utang piutang
   - Tracking pengeluaran

#### **Advanced Features (Opsional)**
1. **Multi-Toko Management**
   - Data terpusat dari beberapa toko
   - Transfer stok antar toko
   - Laporan konsolidasi

2. **Mobile POS**
   - Android app untuk kasir mobile
   - Offline mode untuk jaringan tidak stabil
   - Sync data real-time

3. **E-commerce Basic**
   - Website toko online sederhana
   - Integrasi dengan marketplace
   - Shopping cart dan checkout

4. **Supplier Management**
   - Database supplier
   - Purchase order sederhana
   - Tracking pengiriman

## **Implementasi Teknis untuk SME**

### **1. Database Design untuk SME**
```sql
-- Optimasi untuk performa dengan volume data SME
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(150) NOT NULL,
    category_id INT,
    unit VARCHAR(20) DEFAULT 'pcs',
    barcode VARCHAR(50),
    cost_price DECIMAL(15,2) NOT NULL,
    selling_price DECIMAL(15,2) NOT NULL,
    markup_percentage DECIMAL(5,2) GENERATED ALWAYS AS ((selling_price - cost_price) / cost_price * 100) STORED,
    min_stock_level DECIMAL(15,3) DEFAULT 0,
    max_stock_level DECIMAL(15,3) DEFAULT 999999,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_code (code),
    INDEX idx_barcode (barcode),
    INDEX idx_category (category_id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pelanggan sederhana
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(50),
    email VARCHAR(100),
    address TEXT,
    customer_type ENUM('WALK_IN', 'REGULAR', 'VIP', 'ONLINE') DEFAULT 'WALK_IN',
    credit_limit DECIMAL(15,2) DEFAULT 0,
    current_debt DECIMAL(15,2) DEFAULT 0,
    loyalty_points INT DEFAULT 0,
    total_purchases DECIMAL(15,2) DEFAULT 0,
    last_purchase_date DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_code (code),
    INDEX idx_phone (phone),
    INDEX idx_email (email),
    INDEX idx_type (customer_type),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### **2. Frontend yang User-Friendly untuk SME**
```html
<!-- POS Interface yang sederhana untuk SME -->
<div class="pos-container">
    <!-- Quick Product Search -->
    <div class="product-search">
        <input type="text" placeholder="Cari produk..." id="quick-search">
        <div class="search-results" id="search-results"></div>
    </div>
    
    <!-- Product Grid -->
    <div class="product-grid" id="product-grid">
        <!-- Products loaded here -->
    </div>
    
    <!-- Shopping Cart -->
    <div class="shopping-cart">
        <div class="cart-items" id="cart-items"></div>
        <div class="cart-summary">
            <div>Total: <span id="cart-total">0</span></div>
            <div>Items: <span id="cart-count">0</span></div>
        </div>
    </div>
    
    <!-- Payment Panel -->
    <div class="payment-panel">
        <div class="payment-methods">
            <button class="payment-btn" data-method="cash">Cash</button>
            <button class="payment-btn" data-method="transfer">Transfer</button>
            <button class="payment-btn" data-method="ewallet">E-Wallet</button>
            <button class="payment-btn" data-method="qrcode">QRIS</button>
        </div>
        <div class="payment-amount">
            <input type="number" id="payment-amount" placeholder="0">
        </div>
        <button class="complete-payment-btn">Bayar</button>
    </div>
</div>

<style>
.pos-container {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.product-search input {
    width: 100%;
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    max-height: 400px;
    overflow-y: auto;
}

.product-card {
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    cursor: pointer;
    transition: all 0.2s;
}

.product-card:hover {
    border-color: #007bff;
    transform: translateY(-2px);
}

.shopping-cart {
    border: 2px solid #ddd;
    border-radius: 5px;
    padding: 15px;
}

.cart-items {
    max-height: 200px;
    overflow-y: auto;
    margin-bottom: 10px;
}

.cart-summary {
    font-weight: bold;
    text-align: right;
}

.payment-panel {
    border: 2px solid #ddd;
    border-radius: 5px;
    padding: 15px;
}

.payment-methods {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin-bottom: 15px;
}

.payment-btn {
    padding: 15px;
    border: 2px solid #ddd;
    border-radius: 5px;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.payment-btn:hover, .payment-btn.active {
    background: #007bff;
    color: white;
}

.payment-amount input {
    width: 100%;
    padding: 10px;
    font-size: 18px;
    border: 2px solid #ddd;
    border-radius: 5px;
    text-align: right;
}

.complete-payment-btn {
    width: 100%;
    padding: 15px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}
</style>
```

### **3. Backend Logic untuk SME**
```php
// Simplified POS untuk SME
class SMEPOS {
    private $db;
    private $store_id;
    
    public function __construct($database, $store_id) {
        $this->db = $database;
        $this->store_id = $store_id;
    }
    
    public function quickSearch($query) {
        $sql = "
            SELECT id, code, name, barcode, selling_price, stock_quantity 
            FROM store_products sp
            JOIN products p ON sp.product_id = p.id
            WHERE sp.store_id = ? 
            AND sp.is_active = 1
            AND (p.name LIKE ? OR p.code LIKE ? OR p.barcode LIKE ?)
            LIMIT 10
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('issss', $this->store_id, "%$query%", "%$query%", "%$query%");
        $stmt->execute();
        
        $results = [];
        while ($row = $stmt->get_result()->fetch_assoc()) {
            $results[] = $row;
        }
        
        $stmt->close();
        return $results;
    }
    
    public function addToCart($product_id, $quantity) {
        $product = $this->getProduct($product_id);
        
        if (!$product) {
            throw new Exception("Product not found");
        }
        
        if ($product['stock_quantity'] < $quantity) {
            throw new Exception("Insufficient stock");
        }
        
        // Add to session cart
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        $_SESSION['cart'][$product_id] = [
            'product_id' => $product_id,
            'name' => $product['name'],
            'price' => $product['selling_price'],
            'quantity' => $quantity,
            'subtotal' => $product['selling_price'] * $quantity
        ];
        
        return true;
    }
    
    public function processPayment($payment_data) {
        $cart = $_SESSION['cart'] ?? [];
        
        if (empty($cart)) {
            throw new Exception("Cart is empty");
        }
        
        $total_amount = 0;
        foreach ($cart as $item) {
            $total_amount += $item['subtotal'];
        }
        
        // Start transaction
        $this->db->begin_transaction();
        
        try {
            // Create sale record
            $sale_number = $this->generateSaleNumber();
            $sql = "
                INSERT INTO store_sales 
                (store_id, sale_number, customer_id, sale_date, total_amount, payment_method, payment_status, cashier_id) 
                VALUES (?, ?, ?, ?, NOW(), ?, ?, 'PAID', ?)
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('isiisds', 
                $this->store_id, 
                $sale_number, 
                $payment_data['customer_id'] ?? null, 
                $total_amount, 
                $payment_data['method'], 
                $_SESSION['user_id']
            );
            $stmt->execute();
            $sale_id = $stmt->insert_id;
            
            // Update stock
            foreach ($cart as $item) {
                $this->updateStock($item['product_id'], -$item['quantity']);
            }
            
            $this->db->commit();
            
            // Clear cart
            unset($_SESSION['cart']);
            
            return [
                'success' => true,
                'sale_id' => $sale_id,
                'sale_number' => $sale_number,
                'total_amount' => $total_amount,
                'change' => $payment_data['amount_received'] - $total_amount
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    private function getProduct($product_id) {
        $sql = "
            SELECT sp.*, p.name, p.barcode 
            FROM store_products sp
            JOIN products p ON sp.product_id = p.id
            WHERE sp.store_id = ? AND sp.product_id = ? AND sp.is_active = 1
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ii', $this->store_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        return $result;
    }
    
    private function updateStock($product_id, $quantity_change) {
        $sql = "
            UPDATE store_products 
            SET stock_quantity = stock_quantity + ?,
                updated_at = NOW()
            WHERE store_id = ? AND product_id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('dii', $quantity_change, $this->store_id, $product_id);
        $stmt->execute();
        $stmt->close();
    }
    
    private function generateSaleNumber() {
        $date = date('ymd');
        $sequence = $this->getSaleSequence($date);
        
        return 'SALE' . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
    
    private function getSaleSequence($date) {
        $sql = "SELECT COUNT(*) as count FROM store_sales WHERE DATE(sale_date) = ? AND store_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('si', $date, $this->store_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return ($row['count'] + 1);
    }
}
```

## **4. Mobile App untuk SME**

### **Fitur Mobile Prioritas**
1. **Quick Search** - Barcode scan dan text search
2. **Simple Cart** - Tambah/kurangi produk
3. **Multiple Payment** - Cash, transfer, e-wallet
4. **Offline Mode** - Bekerja tanpa internet
5. **Sync Data** - Sinkronisasi saat online
6. **Basic Reports** - Laporan penjualan sederhana

### **Implementasi Mobile**
```javascript
// React Native untuk SME POS
import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, TouchableOpacity, Alert } from 'react-native';

const SMEPOSApp = () => {
    const [cart, setCart] = useState([]);
    const [searchQuery, setSearchQuery] = useState('');
    const [isOnline, setIsOnline] = useState(true);
    
    // Quick search with barcode
    const handleBarcodeScan = (data) => {
        // Process barcode data
        addToCart(data);
    };
    
    const addToCart = (product) => {
        setCart(prevCart => {
            const existingItem = prevCart.find(item => item.id === product.id);
            if (existingItem) {
                return prevCart.map(item => 
                    item.id === product.id 
                        ? { ...item, quantity: item.quantity + 1 }
                        : item
                );
            }
            return [...prevCart, product];
        });
    };
    
    const processPayment = async (method) => {
        if (cart.length === 0) {
            Alert.alert('Cart is empty');
            return;
        }
        
        try {
            if (isOnline) {
                // Process online payment
                const response = await fetch('/api/sale', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        items: cart,
                        payment_method: method,
                        store_id: await getCurrentStoreId()
                    })
                });
                
                if (response.ok) {
                    setCart([]);
                    Alert.alert('Payment successful');
                }
            } else {
                // Store offline transaction
                await storeOfflineTransaction(cart, method);
                setCart([]);
                Alert.alert('Transaction stored offline');
            }
        } catch (error) {
            Alert.alert('Payment failed: ' + error.message);
        }
    };
    
    return (
        <View style={{ flex: 1, padding: 20 }}>
            {/* Search Bar */}
            <View style={{ flexDirection: 'row', marginBottom: 20 }}>
                <TextInput
                    style={{ flex: 1, marginRight: 10 }}
                    placeholder="Search products..."
                    value={searchQuery}
                    onChangeText={setSearchQuery}
                />
                <TouchableOpacity onPress={() => scanBarcode()}>
                    <Text>📷</Text>
                </TouchableOpacity>
            </View>
            
            {/* Product List */}
            <ScrollView style={{ flex: 1 }}>
                {/* Render products here */}
            </ScrollView>
            
            {/* Cart */}
            <View style={{ borderTopWidth: 1, borderTopColor: '#ddd', paddingTop: 20 }}>
                <Text style={{ fontSize: 18, fontWeight: 'bold', marginBottom: 10 }}>
                    Cart ({cart.length} items)
                </Text>
                
                {/* Payment Methods */}
                <View style={{ flexDirection: 'row', justifyContent: 'space-around' }}>
                    <TouchableOpacity 
                        style={{ 
                            backgroundColor: '#007bff', 
                            padding: 15, 
                            borderRadius: 5 
                        }}
                        onPress={() => processPayment('cash')}
                    >
                        <Text style={{ color: 'white' }}>Cash</Text>
                    </TouchableOpacity>
                    
                    <TouchableOpacity 
                        style={{ 
                            backgroundColor: '#28a745', 
                            padding: 15, 
                            borderRadius: 5 
                        }}
                        onPress={() => processPayment('transfer')}
                    >
                        <Text style={{ color: 'white' }}>Transfer</Text>
                    </TouchableOpacity>
                    
                    <TouchableOpacity 
                        style={{ 
                            backgroundColor: '#17a2b8', 
                            padding: 15, 
                            borderRadius: 5 
                        }}
                        onPress={() => processPayment('ewallet')}
                    >
                        <Text style={{ color: 'white' }}>E-Wallet</Text>
                    </TouchableOpacity>
                </View>
            </View>
        </View>
    );
};
```

## **5. Deployment untuk SME**

### **Cloud Hosting yang Terjangkau**
- **Shared Hosting** untuk menghemat biaya
- **Auto-scaling** untuk pertumbuhan
- **Daily Backup** otomatis
- **SSL Certificate** gratis dari Let's Encrypt

### **Hardware yang Direkomendasikan**
- **Tablet/Android** untuk POS mobile
- **Thermal Printer** untuk struk
- **Barcode Scanner** USB atau built-in camera
- **Cash Drawer** terintegrasi

### **Biaya Implementasi Estimasi**
- **Software Development:** 15-25 juta (3-6 bulan)
- **Hardware Setup:** 5-10 juta
- **Cloud Hosting:** 500ribu - 1 juta/bulan
- **Maintenance:** 1-2 juta/bulan

## **6. Pelatihan dan Dukungan**

### **Training untuk User SME**
1. **Basic POS Operation** - 1 hari
2. **Product Management** - 1 hari
3. **Basic Reporting** - 1 hari
4. **Mobile App Usage** - 1 hari
5. **Troubleshooting Common Issues** - 1 hari

### **Support Channels**
- **WhatsApp Business** untuk quick support
- **Email Support** priority response < 4 jam
- **Video Call** untuk remote assistance
- **Knowledge Base** online 24/7

---

**Kesimpulan:** Sistem ini dirancang khusus untuk **UKM/SME retail** dengan fokus pada **kesederhanaan**, **kemudahan penggunaan**, **biaya terjangkau**, dan **scalabilitas** sesuai pertumbuhan bisnis dari mikro ke menengah.
