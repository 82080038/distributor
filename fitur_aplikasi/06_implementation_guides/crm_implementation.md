# CRM Implementation Guide

## **Overview**
Implementasi Customer Relationship Management (CRM) untuk sistem distribusi retail dengan fokus pada manajemen pelanggan, loyalty program, dan analisis perilaku pelanggan.

## **Prerequisites**
- PHP 8.0+
- MySQL 8.0+ 
- Web server (Apache/Nginx)
- Redis untuk caching (opsional)

## **Database Setup**

### **1. Import Schema**
```bash
mysql -u username -p database_name < database_schema/crm_schema.sql
```

### **2. Verify Tables**
```sql
SHOW TABLES LIKE 'customer%';
SHOW TABLES LIKE 'loyalty%';
SHOW TABLES LIKE 'communication%';
```

## **File Structure**

### **Backend Files**
```
src/
├── controllers/
│   ├── CustomerController.php
│   ├── CommunicationController.php
│   └── AnalyticsController.php
├── models/
│   ├── Customer.php
│   ├── LoyaltyProgram.php
│   └── CustomerAnalytics.php
├── services/
│   ├── CRMService.php
│   ├── EmailService.php
│   └── SMSService.php
└── middleware/
    ├── AuthMiddleware.php
    └── ValidationMiddleware.php
```

### **Frontend Files**
```
public/
├── crm/
│   ├── dashboard.html
│   ├── customers.html
│   ├── customer-detail.html
│   ├── segments.html
│   └── analytics.html
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
└── api/
    └── crm_api.php
```

## **Implementation Steps**

### **Phase 1: Basic CRUD (Week 1-2)**

#### **1. Customer Management**
```php
// src/controllers/CustomerController.php
class CustomerController {
    public function index() {
        // Display customer list with pagination
    }
    
    public function create() {
        // Create new customer form
    }
    
    public function store() {
        // Save new customer
        $customer = new Customer();
        $customer->create($_POST);
    }
    
    public function edit($id) {
        // Edit customer form
    }
    
    public function update($id) {
        // Update customer data
        $customer = new Customer();
        $customer->update($id, $_POST);
    }
    
    public function delete($id) {
        // Delete customer (soft delete)
        $customer = new Customer();
        $customer->deactivate($id);
    }
}
```

#### **2. Customer Model**
```php
// src/models/Customer.php
class Customer {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function create($data) {
        $sql = "INSERT INTO customers (...) VALUES (...)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM customers WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        // Dynamic field update
        foreach ($data as $key => $value) {
            if (in_array($key, $this->allowedFields)) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        $sql = "UPDATE customers SET " . implode(', ', $fields) . " WHERE id = ?";
        $values[] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    public function deactivate($id) {
        $sql = "UPDATE customers SET is_active = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
```

### **Phase 2: Advanced Features (Week 3-4)**

#### **1. Customer Segmentation**
```php
// src/models/CustomerSegmentation.php
class CustomerSegmentation {
    public function calculateRFMScore($customer_id) {
        // Recency, Frequency, Monetary analysis
        $recency = $this->calculateRecency($customer_id);
        $frequency = $this->calculateFrequency($customer_id);
        $monetary = $this->calculateMonetary($customer_id);
        
        return [
            'recency_score' => $recency,
            'frequency_score' => $frequency,
            'monetary_score' => $monetary,
            'total_score' => $recency + $frequency + $monetary,
            'segment' => $this->determineSegment($recency + $frequency + $monetary)
        ];
    }
    
    private function determineSegment($score) {
        if ($score >= 80) return 'CHAMPION';
        if ($score >= 60) return 'LOYAL';
        if ($score >= 40) return 'POTENTIAL_LOYAL';
        if ($score >= 20) return 'AT_RISK';
        return 'LOST';
    }
}
```

#### **2. Loyalty Program**
```php
// src/models/LoyaltyProgram.php
class LoyaltyProgram {
    public function addPoints($customer_id, $points, $reference_type, $reference_id) {
        $sql = "
            INSERT INTO loyalty_transactions 
            (customer_id, transaction_type, points, reference_type, reference_id, transaction_date) 
            VALUES (?, 'EARN', ?, ?, ?, NOW())
        ";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$customer_id, $points, $reference_type, $reference_id]);
    }
    
    public function redeemPoints($customer_id, $points, $reference_type, $reference_id) {
        $sql = "
            INSERT INTO loyalty_transactions 
            (customer_id, transaction_type, points, reference_type, reference_id, transaction_date) 
            VALUES (?, 'REDEEM', -?, ?, ?, NOW())
        ";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$customer_id, $points, $reference_type, $reference_id]);
    }
    
    public function getPointsBalance($customer_id) {
        $sql = "
            SELECT SUM(points) as balance 
            FROM loyalty_transactions 
            WHERE customer_id = ? 
            AND (expiry_date IS NULL OR expiry_date > CURDATE())
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $customer_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        return $result ? $result['balance'] : 0;
    }
}
```

### **Phase 3: Communication & Analytics (Week 5-6)**

#### **1. Customer Communication**
```php
// src/services/EmailService.php
class EmailService {
    private $mailer;
    
    public function __construct() {
        // Initialize email service (PHPMailer, SendGrid, etc.)
        $this->mailer = new PHPMailer();
    }
    
    public function sendPromotion($customers, $promotion) {
        foreach ($customers as $customer) {
            $this->mailer->addAddress($customer['email'], $customer['name']);
            $this->mailer->Subject = $promotion['subject'];
            $this->mailer->Body = $this->personalizeEmail($customer, $promotion['template']);
            $this->mailer->send();
            
            // Log communication
            $this->logCommunication($customer['id'], 'EMAIL', $promotion['subject'], $promotion['template']);
        }
    }
    
    private function personalizeEmail($customer, $template) {
        // Replace placeholders with customer data
        $replacements = [
            '{customer_name}' => $customer['name'],
            '{loyalty_points}' => $customer['loyalty_points'],
            '{last_purchase}' => $customer['last_purchase_date']
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}
```

#### **2. Customer Analytics**
```php
// src/models/CustomerAnalytics.php
class CustomerAnalytics {
    public function getCustomerLifetimeValue($customer_id) {
        $sql = "
            SELECT 
                SUM(total_amount) as total_revenue,
                COUNT(*) as total_transactions,
                AVG(total_amount) as avg_transaction,
                DATEDIFF(CURDATE(), MIN(purchase_date)) as customer_age_days
            FROM customer_purchase_history 
            WHERE customer_id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $customer_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$data || $data['total_transactions'] == 0) {
            return 0;
        }
        
        $avg_transactions_per_year = ($data['total_transactions'] / max(1, $data['customer_age_days'] / 365)) * 365;
        $avg_order_value = $data['avg_transaction'];
        $gross_margin = 0.20; // Assume 20% gross margin
        
        return ($avg_transactions_per_year * $avg_order_value * $gross_margin) - ($avg_transactions_per_year * 50);
    }
    
    public function getChurnPrediction($customer_id) {
        // Machine learning prediction (simplified)
        $features = $this->extractCustomerFeatures($customer_id);
        $churn_probability = $this->predictChurn($features);
        
        return [
            'churn_probability' => $churn_probability,
            'risk_level' => $this->categorizeRisk($churn_probability),
            'recommended_action' => $this->getRecommendedAction($churn_probability)
        ];
    }
}
```

## **API Integration**

### **1. RESTful Endpoints**
```javascript
// JavaScript API client
class CRMApi {
    constructor(baseUrl) {
        this.baseUrl = baseUrl;
    }
    
    async getCustomers(page = 1, limit = 20, filters = {}) {
        const params = new URLSearchParams({
            page: page,
            limit: limit,
            ...filters
        });
        
        const response = await fetch(`${this.baseUrl}/api/customers?${params}`);
        return response.json();
    }
    
    async createCustomer(customerData) {
        const response = await fetch(`${this.baseUrl}/api/customers`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.getAuthToken()}`
            },
            body: JSON.stringify(customerData)
        });
        
        return response.json();
    }
    
    async getCustomerAnalytics(customerId) {
        const response = await fetch(`${this.baseUrl}/api/customers/${customerId}/analytics`);
        return response.json();
    }
}
```

### **2. Real-time Updates**
```javascript
// WebSocket for real-time updates
class CRMRealtime {
    constructor() {
        this.ws = new WebSocket('ws://localhost:8080/crm-updates');
        this.setupEventHandlers();
    }
    
    setupEventHandlers() {
        this.ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            
            switch (data.type) {
                case 'new_customer':
                    this.updateCustomerList(data.customer);
                    break;
                case 'customer_update':
                    this.updateCustomerCard(data.customer);
                    break;
                case 'loyalty_points':
                    this.updateLoyaltyPoints(data.customer_id, data.points);
                    break;
            }
        };
    }
    
    updateCustomerList(customer) {
        // Update UI with new customer
        const customerRow = this.createCustomerRow(customer);
        document.getElementById('customer-table').appendChild(customerRow);
    }
    
    updateLoyaltyPoints(customerId, points) {
        // Update loyalty points display
        const pointsElement = document.getElementById(`points-${customerId}`);
        if (pointsElement) {
            pointsElement.textContent = points.toLocaleString();
        }
    }
}
```

## **Security Considerations**

### **1. Input Validation**
```php
// Validation rules
$validation_rules = [
    'name' => 'required|string|max:150',
    'email' => 'email|max:100',
    'phone' => 'string|max:20',
    'credit_limit' => 'numeric|min:0'
];

function validateCustomerData($data, $rules) {
    foreach ($rules as $field => $rule) {
        $rule_parts = explode('|', $rule);
        
        foreach ($rule_parts as $rule_part) {
            if ($rule_part === 'required' && empty($data[$field])) {
                return ["Field $field is required"];
            }
            
            if (strpos($rule_part, 'max:') !== false) {
                $max_length = explode(':', $rule_part)[1];
                if (strlen($data[$field]) > $max_length) {
                    return ["Field $field exceeds maximum length of $max_length"];
                }
            }
        }
    }
    
    return [];
}
```

### **2. Authentication & Authorization**
```php
// Middleware for API protection
class AuthMiddleware {
    public static function authenticate() {
        $headers = getallheaders();
        $auth_header = $headers['Authorization'] ?? '';
        
        if (empty($auth_header)) {
            http_response_code(401);
            echo json_encode(['error' => 'Authorization required']);
            exit();
        }
        
        $token = str_replace('Bearer ', '', $auth_header);
        $user_data = JWT::decode($token);
        
        if (!$user_data || $user_data['exp'] < time()) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token']);
            exit();
        }
        
        return $user_data;
    }
}
```

## **Performance Optimization**

### **1. Database Indexes**
```sql
-- Critical indexes for CRM performance
CREATE INDEX idx_customer_search ON customers(name, customer_code);
CREATE INDEX idx_customer_type ON customers(customer_type);
CREATE INDEX idx_customer_active ON customers(is_active);
CREATE INDEX idx_loyalty_customer ON loyalty_transactions(customer_id, transaction_date);
CREATE INDEX idx_communication_date ON customer_communications(customer_id, sent_date);
```

### **2. Caching Strategy**
```php
// Redis caching for frequently accessed data
class CacheManager {
    private $redis;
    
    public function __construct() {
        $this->redis = new Redis();
    }
    
    public function getCustomer($id) {
        $cache_key = "customer:$id";
        $cached = $this->redis->get($cache_key);
        
        if ($cached) {
            return json_decode($cached, true);
        }
        
        $customer = $this->fetchCustomerFromDB($id);
        $this->redis->setex($cache_key, 3600, json_encode($customer)); // 1 hour cache
        
        return $customer;
    }
    
    public function invalidateCustomer($id) {
        $this->redis->del("customer:$id");
    }
}
```

## **Testing Strategy**

### **1. Unit Tests**
```php
// tests/CustomerTest.php
class CustomerTest extends PHPUnit\Framework\TestCase {
    public function testCreateCustomer() {
        $customer = new Customer();
        $data = [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'customer_type' => 'RETAIL'
        ];
        
        $result = $customer->create($data);
        $this->assertTrue($result);
        
        $created = $customer->findById($customer->getLastInsertId());
        $this->assertEquals('Test Customer', $created['name']);
    }
    
    public function testCustomerCodeGeneration() {
        $crm = new CRMLogic($this->db);
        $code = $crm->generateCustomerCode('RETAIL');
        
        $this->assertStringStartsWith('RT', $code);
        $this->assertEquals(10, strlen($code));
    }
}
```

### **2. Integration Tests**
```php
// tests/CRMIntegrationTest.php
class CRMIntegrationTest extends PHPUnit\Framework\TestCase {
    public function testCustomerAPI() {
        $response = $this->api->createCustomer([
            'name' => 'API Test Customer',
            'email' => 'api@test.com'
        ]);
        
        $this->assertEquals(201, $response['status']);
        $this->assertArrayHasKey('id', $response['data']);
    }
    
    public function testCustomerAnalytics() {
        $response = $this->api->getCustomerAnalytics(1);
        
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('segment', $response['data']);
        $this->assertArrayHasKey('lifetime_value', $response['data']);
    }
}
```

## **Deployment Checklist**

### **Pre-deployment**
- [ ] Database schema imported
- [ ] All unit tests passing
- [ ] Integration tests passing
- [ ] Performance benchmarks met
- [ ] Security audit completed
- [ ] Documentation updated

### **Post-deployment**
- [ ] Monitor API response times
- [ ] Check database query performance
- [ ] Verify real-time features working
- [ ] Test mobile responsiveness
- [ ] Validate data integrity

## **Monitoring & Maintenance**

### **Key Metrics to Monitor**
- API response time < 200ms
- Database query time < 100ms
- Customer search performance
- Memory usage < 512MB
- Error rate < 0.1%

### **Regular Maintenance Tasks**
- Weekly database optimization
- Monthly cache cleanup
- Quarterly performance review
- Annual security audit

## **Troubleshooting Common Issues**

### **Performance Issues**
1. **Slow customer search** → Add proper indexes
2. **High memory usage** → Optimize queries, implement pagination
3. **API timeouts** → Check database connections, implement caching

### **Data Issues**
1. **Duplicate customers** → Add unique constraints on email/phone
2. **Incorrect segmentation** → Review RFM logic
3. **Loyalty points errors** → Verify transaction logging

### **Integration Issues**
1. **CORS errors** → Check headers, preflight handling
2. **Authentication failures** → Verify JWT implementation
3. **Real-time updates not working** → Check WebSocket configuration

---

**Timeline Estimate:** 6 weeks for full implementation
**Team Size:** 2-3 developers
**Success Criteria:** All CRUD operations working, basic analytics functional, performance benchmarks met
