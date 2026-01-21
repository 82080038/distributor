# Deep Learning Integrations

## **🤖 Best Practices dari Internet untuk Sistem Distribusi**

### **📊 Sumber Research:**
- **Industry Standards:** ERP & distribution systems
- **Modern PHP Patterns:** Best practices 2024
- **Database Optimization:** MySQL 8.0+ performance
- **Security Standards:** OWASP 2024 guidelines
- **UI/UX Patterns:** Modern web applications
- **Scalability Principles:** High-performance systems

---

## **🏗️ Modern Architecture Patterns**

### **1. Repository Pattern**
```php
// ✅ Modern repository pattern
interface PurchaseRepositoryInterface {
    public function findById(int $id): ?Purchase;
    public function findAll(array $criteria = []): array;
    public function save(Purchase $purchase): bool;
    public function delete(int $id): bool;
}

class PurchaseRepository implements PurchaseRepositoryInterface {
    public function __construct(private PDO $db) {}
    
    public function findById(int $id): ?Purchase {
        $sql = "SELECT * FROM purchases WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? Purchase::fromArray($data) : null;
    }
    
    public function findAll(array $criteria = []): array {
        $sql = "SELECT * FROM purchases WHERE 1=1";
        $params = [];
        
        if (!empty($criteria['supplier_id'])) {
            $sql .= " AND supplier_id = ?";
            $params[] = $criteria['supplier_id'];
        }
        
        if (!empty($criteria['date_from'])) {
            $sql .= " AND purchase_date >= ?";
            $params[] = $criteria['date_from'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return array_map(
            fn($data) => Purchase::fromArray($data),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
```

### **2. Service Layer Pattern**
```php
// ✅ Service layer dengan dependency injection
class PurchaseService {
    public function __construct(
        private PurchaseRepositoryInterface $purchaseRepository,
        private ProductRepositoryInterface $productRepository,
        private AuditServiceInterface $auditService,
        private ValidationServiceInterface $validationService
    ) {}
    
    public function createPurchase(array $data): Result {
        // Validation
        $validation = $this->validationService->validate($data, 'purchase');
        if (!$validation->isValid()) {
            return Result::failure($validation->getErrors());
        }
        
        // Business logic
        $purchase = Purchase::fromArray($data);
        $purchase->calculateTotals();
        
        // Database transaction
        try {
            $this->purchaseRepository->beginTransaction();
            
            $saved = $this->purchaseRepository->save($purchase);
            if (!$saved) {
                throw new Exception("Failed to save purchase");
            }
            
            // Update stock
            $this->updateStock($purchase->getItems());
            
            // Audit logging
            $this->auditService->log('purchase_created', $purchase->toArray());
            
            $this->purchaseRepository->commit();
            
            return Result::success($purchase);
            
        } catch (Exception $e) {
            $this->purchaseRepository->rollback();
            $this->auditService->log('purchase_failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            
            return Result::failure("Failed to create purchase: " . $e->getMessage());
        }
    }
}
```

### **3. Data Transfer Object (DTO) Pattern**
```php
// ✅ DTO untuk data transfer
class PurchaseDTO {
    public function __construct(
        public readonly ?int $id,
        public readonly int $supplierId,
        public readonly string $supplierName,
        public readonly string $invoiceNo,
        public readonly DateTime $purchaseDate,
        public readonly float $totalAmount,
        public readonly array $items,
        public readonly ?string $notes
    ) {}
    
    public static function fromArray(array $data): self {
        return new self(
            id: $data['id'] ?? null,
            supplierId: (int)$data['supplier_id'],
            supplierName: $data['supplier_name'],
            invoiceNo: $data['invoice_no'],
            purchaseDate: new DateTime($data['purchase_date']),
            totalAmount: (float)$data['total_amount'],
            items: $data['items'] ?? [],
            notes: $data['notes'] ?? null
        );
    }
    
    public function toArray(): array {
        return [
            'id' => $this->id,
            'supplier_id' => $this->supplierId,
            'supplier_name' => $this->supplierName,
            'invoice_no' => $this->invoiceNo,
            'purchase_date' => $this->purchaseDate->format('Y-m-d'),
            'total_amount' => $this->totalAmount,
            'items' => $this->items,
            'notes' => $this->notes
        ];
    }
}
```

---

## **🔒 Modern Security Practices**

### **1. Input Validation & Sanitization**
```php
// ✅ Modern validation library
class ValidationService {
    public function validate(array $data, string $context): ValidationResult {
        $rules = $this->getRules($context);
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            foreach ($fieldRules as $rule => $params) {
                $result = $this->applyRule($rule, $value, $params);
                if (!$result->isValid()) {
                    $errors[$field][$rule] = $result->getMessage();
                }
            }
        }
        
        return new ValidationResult(empty($errors), $errors);
    }
    
    private function applyRule(string $rule, $value, array $params): ValidationResult {
        return match($rule) {
            'required' => $this->validateRequired($value),
            'email' => $this->validateEmail($value),
            'numeric' => $this->validateNumeric($value),
            'min' => $this->validateMin($value, $params['min']),
            'max' => $this->validateMax($value, $params['max']),
            default => ValidationResult::success()
        };
    }
}
```

### **2. Rate Limiting**
```php
// ✅ Rate limiting dengan Redis
class RateLimiter {
    public function __construct(private Redis $redis) {}
    
    public function checkLimit(string $key, int $limit, int $window): bool {
        $current = $this->redis->incr($key);
        
        if ($current === 1) {
            $this->redis->expire($key, $window);
        }
        
        return $current <= $limit;
    }
    
    public function getRemainingRequests(string $key, int $limit): int {
        $current = (int)$this->redis->get($key);
        return max(0, $limit - $current);
    }
}
```

### **3. CSRF Protection**
```php
// ✅ Modern CSRF protection
class CSRFProtection {
    public function generateToken(): string {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        
        return $token;
    }
    
    public function validateToken(string $token): bool {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }
        
        $maxAge = 3600; // 1 hour
        if (time() - $_SESSION['csrf_token_time'] > $maxAge) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
```

---

## **⚡ Performance Optimization**

### **1. Database Query Optimization**
```php
// ✅ Optimized query dengan proper indexing
class OptimizedQueries {
    public function getPurchaseWithItems(int $purchaseId): ?array {
        $sql = "
            SELECT 
                p.*,
                pi.product_id,
                pi.quantity,
                pi.price,
                pi.subtotal,
                pr.name as product_name,
                pr.code as product_code,
                pr.unit as product_unit
            FROM purchases p
            LEFT JOIN purchase_items pi ON p.id = pi.purchase_id
            LEFT JOIN products pr ON pi.product_id = pr.id
            WHERE p.id = ?
            ORDER BY pi.id
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$purchaseId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getPurchaseList(array $filters): array {
        $sql = "
            SELECT 
                p.*,
                s.nama_lengkap as supplier_name,
                COUNT(pi.id) as item_count
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

### **2. Caching Strategy**
```php
// ✅ Multi-level caching
class CacheService {
    public function __construct(
        private Redis $redis,
        private FilesystemCache $fileCache
    ) {}
    
    public function get(string $key, callable $callback, int $ttl = 3600): mixed {
        // Try Redis first
        $data = $this->redis->get($key);
        if ($data !== false) {
            return json_decode($data, true);
        }
        
        // Try file cache
        $data = $this->fileCache->get($key);
        if ($data !== null) {
            // Store in Redis for next time
            $this->redis->setex($key, $ttl, json_encode($data));
            return $data;
        }
        
        // Execute callback and cache result
        $result = $callback();
        $this->redis->setex($key, $ttl, json_encode($result));
        $this->fileCache->set($key, $result, $ttl);
        
        return $result;
    }
    
    public function invalidate(string $pattern): void {
        $keys = $this->redis->keys($pattern);
        if (!empty($keys)) {
            $this->redis->del($keys);
        }
        
        $this->fileCache->clear($pattern);
    }
}
```

---

## **🎨 Modern Frontend Patterns**

### **1. Component-Based Architecture**
```javascript
// ✅ Modern JavaScript dengan ES6 modules
class PurchaseForm {
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            apiEndpoint: '/api/purchases',
            validationRules: {},
            ...options
        };
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.setupValidation();
        this.setupAutoComplete();
    }
    
    setupEventListeners() {
        this.container.addEventListener('submit', this.handleSubmit.bind(this));
        this.container.addEventListener('input', this.handleInput.bind(this));
    }
    
    async handleSubmit(event) {
        event.preventDefault();
        
        const formData = new FormData(this.container);
        const data = Object.fromEntries(formData);
        
        try {
            const response = await this.apiRequest('/api/purchases', {
                method: 'POST',
                body: JSON.stringify(data),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': this.getCSRFToken()
                }
            });
            
            if (response.success) {
                this.showSuccess('Purchase saved successfully');
                this.resetForm();
                this.updatePurchaseList();
            } else {
                this.showError(response.message);
            }
        } catch (error) {
            this.showError('Network error occurred');
        }
    }
}
```

### **2. State Management**
```javascript
// ✅ Modern state management
class StateManager {
    constructor() {
        this.state = {
            purchases: [],
            suppliers: [],
            products: [],
            filters: {},
            pagination: {
                page: 1,
                limit: 20,
                total: 0
            }
        };
        
        this.subscribers = [];
    }
    
    subscribe(callback) {
        this.subscribers.push(callback);
    }
    
    notify() {
        this.subscribers.forEach(callback => callback(this.state));
    }
    
    setState(updates) {
        this.state = { ...this.state, ...updates };
        this.notify();
    }
    
    getPurchases() {
        return this.state.purchases;
    }
    
    addPurchase(purchase) {
        this.setState({
            purchases: [...this.state.purchases, purchase]
        });
    }
}
```

---

## **🔧 Modern Development Practices**

### **1. Dependency Injection Container**
```php
// ✅ Modern DI container
class Container {
    private array $bindings = [];
    private array $instances = [];
    
    public function bind(string $abstract, callable $concrete): void {
        $this->bindings[$abstract] = $concrete;
    }
    
    public function singleton(string $abstract, callable $concrete): void {
        $this->bindings[$abstract] = $concrete;
        $this->instances[$abstract] = null;
    }
    
    public function make(string $abstract): mixed {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        
        if (!isset($this->bindings[$abstract])) {
            throw new Exception("No binding found for {$abstract}");
        }
        
        $concrete = $this->bindings[$abstract];
        $instance = $concrete($this);
        
        if (method_exists($instance, '__construct')) {
            $instance = $concrete($this);
        }
        
        if (isset($this->instances[$abstract])) {
            $this->instances[$abstract] = $instance;
        }
        
        return $instance;
    }
}
```

### **2. Event System**
```php
// ✅ Event-driven architecture
class EventDispatcher {
    private array $listeners = [];
    
    public function listen(string $event, callable $listener, int $priority = 0): void {
        $this->listeners[$event][] = [
            'listener' => $listener,
            'priority' => $priority
        ];
        
        // Sort by priority
        usort($this->listeners[$event], fn($a, $b) => $b['priority'] <=> $a['priority']);
    }
    
    public function dispatch(string $event, array $data = []): void {
        if (!isset($this->listeners[$event])) {
            return;
        }
        
        foreach ($this->listeners[$event] as $listenerInfo) {
            $listenerInfo['listener']($data);
        }
    }
}

// Usage
$eventDispatcher->listen('purchase.created', function($data) {
    // Send notification
    // Update cache
    // Log activity
});
```

---

## **📊 Monitoring & Analytics**

### **1. Performance Monitoring**
```php
// ✅ Modern performance monitoring
class PerformanceMonitor {
    public function startTimer(string $name): void {
        $_SERVER['perf_start_' . $name] = microtime(true);
    }
    
    public function endTimer(string $name): float {
        $start = $_SERVER['perf_start_' . $name] ?? 0;
        $duration = microtime(true) - $start;
        
        $this->logMetric($name, $duration);
        
        return $duration;
    }
    
    private function logMetric(string $name, float $duration): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'metric' => $name,
            'duration' => $duration,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];
        
        error_log(json_encode($logEntry), 3, '/var/log/performance.log');
    }
}
```

### **2. Error Tracking**
```php
// ✅ Comprehensive error tracking
class ErrorTracker {
    public function trackException(Throwable $exception, array $context = []): void {
        $errorData = [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'context' => $context,
            'user_id' => $_SESSION['user_id'] ?? null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Send to error tracking service
        $this->sendToErrorService($errorData);
        
        // Log locally
        error_log(json_encode($errorData), 3, '/var/log/exceptions.log');
    }
}
```

---

## **🎯 Implementation Recommendations**

### **1. Immediate Actions (Week 1-2)**
- [ ] Implement repository pattern untuk data access
- [ ] Create service layer untuk business logic
- [ ] Add comprehensive validation system
- [ ] Implement CSRF protection
- [ ] Add rate limiting

### **2. Short Term (Week 3-4)**
- [ ] Refactor existing code ke MVC pattern
- [ ] Implement caching strategy
- [ ] Add performance monitoring
- [ ] Create modern frontend components
- [ ] Implement state management

### **3. Long Term (Week 5-8)**
- [ ] Add comprehensive error tracking
- [ ] Implement event-driven architecture
- [ ] Add automated testing
- [ ] Create deployment pipeline
- [ ] Add monitoring dashboard

---

## **📚 Resources & References**

### **📖 Documentation:**
- **PHP Standards:** https://www.php-fig.org/psr/
- **MySQL Best Practices:** https://dev.mysql.com/doc/refman/8.0/en/
- **Security Guidelines:** https://owasp.org/
- **Performance Tuning:** https://www.php.net/manual/en/book.apcu.php

### **🔧 Tools & Libraries:**
- **Dependency Injection:** PHP-DI, Symfony DI
- **Validation:** Respect/Validation, Valitron
- **Caching:** Redis, APCu, Memcached
- **Monitoring:** New Relic, DataDog, Sentry
- **Testing:** PHPUnit, Pest

---

**Status:** ✅ **Deep learning integrations completed - Ready for implementation**

**Priority:** Critical - Foundation untuk modern architecture
