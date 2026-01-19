# 03_business_logic - Index

## **🧠 Business Logic**

### **📊 File Overview:**
1. **00_INDEX.md** - Index business logic (file ini)
2. **crm_logic.php** - CRM Business Logic Implementation
3. **inventory_valuation_logic.php** - Inventory Valuation Logic

---

## **📋 Quick Navigation:**

### **🧠 Business Logic Files:**
- **[crm_logic.php](./crm_logic.php)** - CRM Business Logic
- **[inventory_valuation_logic.php](./inventory_valuation_logic.php)** - Inventory Valuation Logic

---

## **📊 Logic Overview:**

### **👥 CRM Logic**
- **Focus:** Customer relationship management
- **Features:** Customer segmentation, loyalty points, communication
- **Methods:** RFM analysis, credit management, analytics

### **📦 Inventory Valuation Logic**
- **Focus:** Inventory valuation methods
- **Features:** FIFO, LIFO, Average Cost calculations
- **Methods:** Stock movements, batch tracking, cost calculations

---

## **🔧 Implementation Details:**

### **CRM Logic Features:**
```php
// Customer segmentation based on RFM
public function getCustomerSegmentation($customer_id)

// Loyalty points calculation
public function calculateLoyaltyPoints($customer_id, $purchase_amount)

// Customer analytics
public function getCustomerAnalytics($customer_id, $start_date, $end_date)
```

### **Inventory Valuation Features:**
```php
// FIFO calculation
public function calculateFIFO($product_id, $quantity_needed = null)

// LIFO calculation
public function calculateLIFO($product_id, $quantity_needed = null)

// Weighted average calculation
public function calculateWeightedAverage($product_id, $quantity_needed = null)
```

---

## **📊 Dependencies:**
- **PHP 8.1+** dengan OOP support
- **MySQL 8.0+** untuk database operations
- **JSON support** untuk data structures
- **Exception handling** untuk error management

---

## **📋 Logic Status:**
- ✅ **crm_logic.php** - Complete CRM business logic
- ✅ **inventory_valuation_logic.php** - Complete inventory valuation
- ✅ **00_INDEX.md** - Navigation index (file ini)

---

## **🎯 Usage Examples:**

### **CRM Logic Usage:**
```php
$crm = new CRMLogic($database, $user_id);
$segment = $crm->getCustomerSegmentation($customer_id);
$analytics = $crm->getCustomerAnalytics($customer_id);
```

### **Inventory Logic Usage:**
```php
$inventory = new InventoryValuationLogic($database);
$fifo_value = $inventory->calculateFIFO($product_id, 100);
$average_cost = $inventory->calculateWeightedAverage($product_id);
```

---

**Last Updated:** 19 Januari 2026
**Total Files:** 3 files
**Status:** ✅ Complete
