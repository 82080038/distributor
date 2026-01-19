# Logic Flow Optimization

## **🔄 Optimasi Logika dan Flow Sistem**

### **📊 Tujuan:**
- **Tidak ada linglung** di tengah implementasi
- **Flow yang jelas** dari awal hingga akhir
- **Logic yang konsisten** di seluruh sistem
- **Error handling** yang komprehensif
- **Performance optimal** untuk semua operasi

---

## **🎯 Core Business Logic Optimization**

### **1. State Machine untuk Transaction Flow**
```php
// ✅ State machine untuk transaction management
class TransactionStateMachine {
    private array $states = [
        'draft' => ['validate', 'submit'],
        'validated' => ['confirm', 'cancel'],
        'confirmed' => ['process', 'cancel'],
        'processing' => ['complete', 'fail'],
        'completed' => [],
        'failed' => ['retry', 'cancel'],
        'cancelled' => []
    ];
    
    private array $transitions = [
        'validate' => ['draft' => 'validated'],
        'submit' => ['validated' => 'confirmed'],
        'confirm' => ['confirmed' => 'processing'],
        'process' => ['processing' => 'completed'],
        'complete' => ['processing' => 'completed'],
        'fail' => ['processing' => 'failed'],
        'retry' => ['failed' => 'validated'],
        'cancel' => ['draft' => 'cancelled', 'validated' => 'cancelled', 'confirmed' => 'cancelled']
    ];
    
    public function canTransition(string $from, string $to): bool {
        return in_array($to, $this->states[$from] ?? []);
    }
    
    public function getValidTransitions(string $state): array {
        return $this->states[$state] ?? [];
    }
    
    public function transition(Transaction $transaction, string $action): TransitionResult {
        $currentState = $transaction->getState();
        
        if (!$this->canTransition($currentState, $action)) {
            return TransitionResult::failure("Cannot transition from {$currentState} to {$action}");
        }
        
        $newState = $this->transitions[$action][$currentState] ?? null;
        if (!$newState) {
            return TransitionResult::failure("Invalid transition: {$action}");
        }
        
        $transaction->setState($newState);
        $transaction->addStateChange($currentState, $newState, $action);
        
        return TransitionResult::success($newState);
    }
}
```

### **2. Business Rules Engine**
```php
// ✅ Centralized business rules engine
class BusinessRulesEngine {
    private array $rules = [];
    
    public function __construct() {
        $this->loadRules();
    }
    
    private function loadRules(): void {
        $this->rules = [
            'purchase_minimum_amount' => [
                'rule' => function($data) {
                    return $data['total_amount'] >= 10000; // Minimum 10k
                },
                'message' => 'Total pembelian minimal Rp 10.000'
            ],
            'supplier_credit_limit' => [
                'rule' => function($data) {
                    $supplier = $this->getSupplier($data['supplier_id']);
                    $currentDebt = $this->getSupplierDebt($data['supplier_id']);
                    $creditLimit = $supplier->getCreditLimit();
                    
                    return ($currentDebt + $data['total_amount']) <= $creditLimit;
                },
                'message' => 'Melebihi batas kredit supplier'
            ],
            'stock_minimum_level' => [
                'rule' => function($data) {
                    foreach ($data['items'] as $item) {
                        $product = $this->getProduct($item['product_id']);
                        $currentStock = $this->getStock($item['product_id']);
                        $minLevel = $product->getMinStockLevel();
                        
                        if ($currentStock < $minLevel) {
                            return false;
                        }
                    }
                    return true;
                },
                'message' => 'Beberapa produk mencapai minimum stock level'
            ],
            'purchase_approval_required' => [
                'rule' => function($data) {
                    return $data['total_amount'] > 50000000; // > 50 juta perlu approval
                },
                'message' => 'Pembelian di atas Rp 50 juta memerlukan approval'
            ]
        ];
    }
    
    public function validate(string $ruleName, array $data): RuleResult {
        if (!isset($this->rules[$ruleName])) {
            return RuleResult::failure("Rule {$ruleName} not found");
        }
        
        $rule = $this->rules[$ruleName];
        $isValid = $rule['rule']($data);
        
        return new RuleResult($isValid, $isValid ? '' : $rule['message']);
    }
    
    public function validateAll(array $data): RulesValidationResult {
        $results = [];
        $isValid = true;
        
        foreach ($this->rules as $ruleName => $rule) {
            $result = $this->validate($ruleName, $data);
            $results[$ruleName] = $result;
            
            if (!$result->isValid()) {
                $isValid = false;
            }
        }
        
        return new RulesValidationResult($isValid, $results);
    }
}
```

### **3. Optimized Database Operations**
```php
// ✅ Optimized database operations
class OptimizedDatabaseOperations {
    public function __construct(private PDO $db) {}
    
    // Batch insert untuk performance
    public function batchInsertPurchaseItems(int $purchaseId, array $items): bool {
        $sql = "INSERT INTO purchase_items (purchase_id, product_id, quantity, price, subtotal) VALUES ";
        $placeholders = [];
        $values = [];
        
        foreach ($items as $item) {
            $placeholders[] = "(?, ?, ?, ?, ?)";
            $values = array_merge($values, [
                $purchaseId,
                $item['product_id'],
                $item['quantity'],
                $item['price'],
                $item['subtotal']
            ]);
        }
        
        $sql .= implode(', ', $placeholders);
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    // Optimized stock update dengan single query
    public function updateStockBatch(array $stockUpdates): bool {
        $sql = "INSERT INTO warehouse_stocks (product_id, warehouse_id, quantity_on_hand, quantity_available, last_updated) 
                  VALUES (?, ?, ?, ?, NOW())
                  ON DUPLICATE KEY UPDATE 
                  quantity_on_hand = VALUES(quantity_on_hand),
                  quantity_available = VALUES(quantity_available),
                  last_updated = NOW()";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($stockUpdates as $update) {
            $stmt->execute([
                $update['product_id'],
                $update['warehouse_id'],
                $update['quantity_on_hand'],
                $update['quantity_available']
            ]);
        }
        
        return true;
    }
    
    // Optimized reporting dengan materialized views
    public function getPurchaseReport(array $filters): array {
        $sql = "
            SELECT 
                p.id,
                p.invoice_no,
                p.purchase_date,
                p.total_amount,
                s.nama_lengkap as supplier_name,
                COUNT(pi.id) as item_count,
                SUM(pi.subtotal) as total_verified
            FROM purchases p
            LEFT JOIN orang s ON p.supplier_id = s.id_orang
            LEFT JOIN purchase_items pi ON p.id = pi.purchase_id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($filters['supplier_id'])) {
            $sql .= " AND p.supplier_id = ?";
            $params[] = $filters['supplier_id'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND p.purchase_date >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND p.purchase_date <= ?";
            $params[] = $filters['date_to'];
        }
        
        $sql .= " GROUP BY p.id ORDER BY p.purchase_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
```

---

## **🔄 Flow Optimization Patterns**

### **1. Command Pattern untuk Complex Operations**
```php
// ✅ Command pattern untuk complex operations
interface CommandInterface {
    public function execute(): CommandResult;
    public function undo(): CommandResult;
    public function getDescription(): string;
}

class CreatePurchaseCommand implements CommandInterface {
    public function __construct(
        private array $purchaseData,
        private PurchaseService $purchaseService,
        private InventoryService $inventoryService,
        private AccountingService $accountingService,
        private AuditService $auditService
    ) {}
    
    public function execute(): CommandResult {
        try {
            // Step 1: Validate
            $validation = $this->validatePurchaseData();
            if (!$validation->isValid()) {
                return CommandResult::failure($validation->getErrors());
            }
            
            // Step 2: Create purchase
            $purchase = $this->purchaseService->create($this->purchaseData);
            
            // Step 3: Update inventory
            $this->inventoryService->updateStockFromPurchase($purchase);
            
            // Step 4: Update accounting
            $this->accountingService->recordPurchaseTransaction($purchase);
            
            // Step 5: Audit
            $this->auditService->log('purchase_created', $purchase->toArray());
            
            return CommandResult::success($purchase);
            
        } catch (Exception $e) {
            return CommandResult::failure('Failed to create purchase: ' . $e->getMessage());
        }
    }
    
    public function undo(): CommandResult {
        try {
            // Reverse accounting
            $this->accountingService->reversePurchaseTransaction($this->purchaseData['id']);
            
            // Reverse inventory
            $this->inventoryService->reverseStockFromPurchase($this->purchaseData['id']);
            
            // Delete purchase
            $this->purchaseService->delete($this->purchaseData['id']);
            
            // Audit
            $this->auditService->log('purchase_deleted', $this->purchaseData);
            
            return CommandResult::success('Purchase successfully reversed');
            
        } catch (Exception $e) {
            return CommandResult::failure('Failed to reverse purchase: ' . $e->getMessage());
        }
    }
    
    public function getDescription(): string {
        return 'Create Purchase';
    }
}

class CommandInvoker {
    private array $history = [];
    
    public function execute(CommandInterface $command): CommandResult {
        $result = $command->execute();
        
        if ($result->isSuccess()) {
            $this->history[] = $command;
        }
        
        return $result;
    }
    
    public function undo(int $steps = 1): array {
        $results = [];
        
        for ($i = 0; $i < $steps && !empty($this->history); $i++) {
            $command = array_pop($this->history);
            $results[] = $command->undo();
        }
        
        return $results;
    }
}
```

### **2. Strategy Pattern untuk Business Logic**
```php
// ✅ Strategy pattern untuk different business logic
interface PricingStrategyInterface {
    public function calculatePrice(array $product, array $context): float;
    public function getDescription(): string;
}

class StandardPricingStrategy implements PricingStrategyInterface {
    public function calculatePrice(array $product, array $context): float {
        return $product['base_price'];
    }
    
    public function getDescription(): string {
        return 'Standard Pricing';
    }
}

class CustomerTierPricingStrategy implements PricingStrategyInterface {
    public function calculatePrice(array $product, array $context): float {
        $customer = $context['customer'];
        $basePrice = $product['base_price'];
        
        return match($customer['tier']) {
            'bronze' => $basePrice * 1.0,
            'silver' => $basePrice * 0.95,
            'gold' => $basePrice * 0.90,
            'platinum' => $basePrice * 0.85,
            default => $basePrice
        };
    }
    
    public function getDescription(): string {
        return 'Customer Tier Pricing';
    }
}

class VolumeDiscountStrategy implements PricingStrategyInterface {
    public function calculatePrice(array $product, array $context): float {
        $quantity = $context['quantity'];
        $basePrice = $product['base_price'];
        
        return match(true) {
            $quantity >= 100 => $basePrice * 0.85,
            $quantity >= 50 => $basePrice * 0.90,
            $quantity >= 25 => $basePrice * 0.95,
            default => $basePrice
        };
    }
    
    public function getDescription(): string {
        return 'Volume Discount Pricing';
    }
}

class PricingContext {
    public function __construct(private PricingStrategyInterface $strategy) {}
    
    public function setStrategy(PricingStrategyInterface $strategy): void {
        $this->strategy = $strategy;
    }
    
    public function calculatePrice(array $product, array $context): float {
        return $this->strategy->calculatePrice($product, $context);
    }
}
```

### **3. Observer Pattern untuk Event Handling**
```php
// ✅ Observer pattern untuk event handling
interface ObserverInterface {
    public function update(string $event, array $data): void;
}

interface SubjectInterface {
    public function attach(ObserverInterface $observer): void;
    public function detach(ObserverInterface $observer): void;
    public function notify(string $event, array $data): void;
}

class PurchaseSubject implements SubjectInterface {
    private array $observers = [];
    
    public function attach(ObserverInterface $observer): void {
        $this->observers[] = $observer;
    }
    
    public function detach(ObserverInterface $observer): void {
        $this->observers = array_filter($this->observers, fn($o) => $o !== $observer);
    }
    
    public function notify(string $event, array $data): void {
        foreach ($this->observers as $observer) {
            $observer->update($event, $data);
        }
    }
}

class InventoryObserver implements ObserverInterface {
    public function update(string $event, array $data): void {
        switch ($event) {
            case 'purchase.created':
                $this->handlePurchaseCreated($data);
                break;
            case 'sales.created':
                $this->handleSalesCreated($data);
                break;
            case 'stock.adjustment':
                $this->handleStockAdjustment($data);
                break;
        }
    }
    
    private function handlePurchaseCreated(array $data): void {
        // Update inventory dari purchase
        foreach ($data['items'] as $item) {
            $this->increaseStock($item['product_id'], $item['quantity']);
        }
    }
    
    private function handleSalesCreated(array $data): void {
        // Update inventory dari sales
        foreach ($data['items'] as $item) {
            $this->decreaseStock($item['product_id'], $item['quantity']);
        }
    }
    
    private function handleStockAdjustment(array $data): void {
        // Handle stock adjustment
        $this->adjustStock($data['product_id'], $data['adjustment']);
    }
}
```

---

## **🔍 Error Prevention & Recovery**

### **1. Validation Pipeline**
```php
// ✅ Comprehensive validation pipeline
class ValidationPipeline {
    private array $validators = [];
    
    public function __construct() {
        $this->setupValidators();
    }
    
    private function setupValidators(): void {
        $this->validators = [
            'input_sanitization' => new InputSanitizationValidator(),
            'required_fields' => new RequiredFieldsValidator(),
            'data_types' => new DataTypesValidator(),
            'business_rules' => new BusinessRulesValidator(),
            'database_integrity' => new DatabaseIntegrityValidator(),
            'security_checks' => new SecurityChecksValidator()
        ];
    }
    
    public function validate(array $data, string $context): ValidationResult {
        $errors = [];
        
        foreach ($this->validators as $validatorName => $validator) {
            $result = $validator->validate($data, $context);
            
            if (!$result->isValid()) {
                $errors[$validatorName] = $result->getErrors();
            }
        }
        
        return new ValidationResult(empty($errors), $errors);
    }
}
```

### **2. Deadlock Prevention**
```php
// ✅ Deadlock prevention strategies
class DeadlockPrevention {
    public function __construct(private PDO $db) {}
    
    public function executeWithDeadlockPrevention(callable $operation, int $maxRetries = 3): mixed {
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            try {
                // Start transaction dengan consistent ordering
                $this->db->exec("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
                $this->db->beginTransaction();
                
                $result = $operation();
                
                $this->db->commit();
                return $result;
                
            } catch (PDOException $e) {
                $this->db->rollback();
                
                // Check jika deadlock
                if ($this->isDeadlock($e)) {
                    $retryCount++;
                    $delay = min(pow(2, $retryCount) * 100000, 1000000); // Exponential backoff
                    usleep($delay);
                    continue;
                }
                
                // Re-throw non-delock errors
                throw $e;
            }
        }
        
        throw new Exception("Operation failed after {$maxRetries} retries due to deadlock");
    }
    
    private function isDeadlock(PDOException $e): bool {
        return str_contains($e->getMessage(), 'Deadlock') || 
               str_contains($e->getMessage(), 'Lock wait timeout') ||
               $e->getCode() === 40001 || // MySQL deadlock
               $e->getCode() === 1205;  // MySQL lock wait timeout
    }
}
```

---

## **📊 Performance Optimization**

### **1. Query Optimization**
```php
// ✅ Query optimization techniques
class QueryOptimizer {
    public function __construct(private PDO $db) {}
    
    // Use proper indexing
    public function createOptimalIndexes(): void {
        $indexes = [
            "CREATE INDEX idx_purchases_supplier_date ON purchases(supplier_id, purchase_date)",
            "CREATE INDEX idx_purchase_items_purchase_product ON purchase_items(purchase_id, product_id)",
            "CREATE INDEX idx_products_category_active ON products(category_id, is_active)",
            "CREATE INDEX idx_warehouse_stocks_product_warehouse ON warehouse_stocks(product_id, warehouse_id)"
        ];
        
        foreach ($indexes as $index) {
            try {
                $this->db->exec($index);
            } catch (PDOException $e) {
                // Index mungkin sudah ada
            }
        }
    }
    
    // Optimized pagination
    public function getPaginatedResults(string $sql, array $params, int $page, int $limit): array {
        $offset = ($page - 1) * $limit;
        
        // Get total count
        $countSql = "SELECT COUNT(*) FROM ({$sql}) as count_query";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();
        
        // Get paginated results
        $paginatedSql = "{$sql} LIMIT {$limit} OFFSET {$offset}";
        $dataStmt = $this->db->prepare($paginatedSql);
        $dataStmt->execute($params);
        $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'data' => $data,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ];
    }
}
```

### **2. Caching Strategy**
```php
// ✅ Multi-level caching strategy
class CacheManager {
    public function __construct(
        private Redis $redis,
        private FileCache $fileCache
    ) {}
    
    public function remember(string $key, callable $callback, int $ttl = 3600): mixed {
        // Level 1: Memory cache (Redis)
        $data = $this->redis->get($key);
        if ($data !== false) {
            return json_decode($data, true);
        }
        
        // Level 2: File cache
        $data = $this->fileCache->get($key);
        if ($data !== null) {
            // Store ke Redis untuk next time
            $this->redis->setex($key, $ttl, json_encode($data));
            return $data;
        }
        
        // Level 3: Execute callback
        $data = $callback();
        
        // Store ke semua cache levels
        $this->redis->setex($key, $ttl, json_encode($data));
        $this->fileCache->set($key, $data, $ttl);
        
        return $data;
    }
    
    public function invalidatePattern(string $pattern): void {
        // Invalidate Redis keys
        $keys = $this->redis->keys($pattern);
        if (!empty($keys)) {
            $this->redis->del($keys);
        }
        
        // Invalidate file cache
        $this->fileCache->clearPattern($pattern);
    }
}
```

---

## **🎯 Implementation Roadmap**

### **✅ Phase 1: Foundation (Week 1-2)**
- [ ] **State Machine** - Implement untuk transaction management
- [ ] **Business Rules Engine** - Centralized business logic
- [ ] **Validation Pipeline** - Comprehensive validation
- [ ] **Error Recovery** - Robust error handling
- [ ] **Deadlock Prevention** - Database optimization

### **✅ Phase 2: Patterns (Week 3-4)**
- [ ] **Command Pattern** - Complex operations
- [ ] **Strategy Pattern** - Business logic variations
- [ ] **Observer Pattern** - Event handling
- [ ] **Repository Pattern** - Data access layer
- [ ] **Service Layer** - Business logic separation

### **✅ Phase 3: Performance (Week 5-6)**
- [ ] **Query Optimization** - Database performance
- [ ] **Caching Strategy** - Multi-level caching
- [ ] **Batch Operations** - Bulk data processing
- [ ] **Connection Pooling** - Database connections
- [ ] **Memory Management** - Resource optimization

### **✅ Phase 4: Monitoring (Week 7-8)**
- [ ] **Performance Monitoring** - Real-time metrics
- [ ] **Error Tracking** - Comprehensive logging
- [ ] **Health Checks** - System monitoring
- [ ] **Alert System** - Proactive notifications
- [ ] **Analytics Dashboard** - Performance insights

---

## **📊 Success Metrics**

### **📈 Performance Targets:**
- **Transaction Time:** < 500ms average
- **Database Queries:** < 50ms average
- **Memory Usage:** < 50MB average
- **Cache Hit Rate:** > 80%

### **🔒 Reliability Targets:**
- **Transaction Success Rate:** > 99.9%
- **Error Recovery Rate:** > 95%
- **Deadlock Rate:** < 0.1%
- **Data Consistency:** 100%

### **📊 Business Logic Targets:**
- **Zero Logic Gaps** dalam flow
- **Complete Validation** untuk semua inputs
- **Consistent Rules** di seluruh sistem
- **Traceable Decisions** untuk audit

---

**Status:** ✅ **Logic flow optimization completed - Ready for implementation**

**Priority:** Critical - Foundation untuk reliable system operation
