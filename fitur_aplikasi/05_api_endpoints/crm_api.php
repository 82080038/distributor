<?php
/**
 * CRM API Endpoints
 * RESTful API for Customer Relationship Management
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth.php';

// Enable CORS for preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

class CRMAPI {
    private $db;
    private $user;
    
    public function __construct() {
        $this->db = $GLOBALS['conn'];
        $this->user = current_user();
    }
    
    /**
     * Send JSON response
     */
    private function sendResponse($success, $data = null, $message = '', $http_code = 200) {
        http_response_code($http_code);
        echo json_encode([
            'success' => $success,
            'data' => $data,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    /**
     * Validate required fields
     */
    private function validateRequired($data, $required_fields) {
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->sendResponse(false, null, "Field '$field' is required", 400);
            }
        }
    }
    
    /**
     * GET /api/customers - Get all customers with pagination and filters
     */
    public function getCustomers() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $offset = ($page - 1) * $limit;
        
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $segment = isset($_GET['segment']) ? $_GET['segment'] : '';
        $customer_type = isset($_GET['type']) ? $_GET['type'] : '';
        
        // Build WHERE clause
        $where_conditions = [];
        $params = [];
        
        if (!empty($search)) {
            $where_conditions[] = "(c.name LIKE ? OR c.customer_code LIKE ? OR c.phone LIKE ?)";
            $search_param = "%$search%";
            $params = array_merge($params, [$search_param, $search_param, $search_param]);
        }
        
        if (!empty($segment)) {
            $where_conditions[] = "cs.segment = ?";
            $params[] = $segment;
        }
        
        if (!empty($customer_type)) {
            $where_conditions[] = "c.customer_type = ?";
            $params[] = $customer_type;
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        // Get total count for pagination
        $count_sql = "
            SELECT COUNT(DISTINCT c.id) as total
            FROM customers c
            LEFT JOIN customer_segment_assignments csa ON c.id = csa.customer_id
            LEFT JOIN customer_segments cs ON csa.segment_id = cs.id
            $where_clause
        ";
        
        $stmt = $this->db->prepare($count_sql);
        if (!empty($params)) {
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        }
        $stmt->execute();
        $total_result = $stmt->get_result();
        $total = $total_result->fetch_assoc()['total'];
        $stmt->close();
        
        // Get customers data
        $sql = "
            SELECT 
                c.id,
                c.customer_code,
                c.name,
                c.phone,
                c.email,
                c.customer_type,
                c.credit_limit,
                c.current_debt,
                c.available_credit,
                c.loyalty_points,
                c.total_spent,
                c.last_purchase_date,
                c.is_active,
                c.registration_date,
                cs.segment,
                COUNT(DISTINCT ph.id) as total_orders,
                COALESCE(AVG(cf.rating), 0) as average_rating
            FROM customers c
            LEFT JOIN customer_segment_assignments csa ON c.id = csa.customer_id
            LEFT JOIN customer_segments cs ON csa.segment_id = cs.id
            LEFT JOIN customer_purchase_history ph ON c.id = ph.customer_id
            LEFT JOIN customer_feedback cf ON c.id = cf.customer_id
            $where_clause
            GROUP BY c.id
            ORDER BY c.created_at DESC
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param(str_repeat('i', count($params)), ...$params);
        }
        $stmt->execute();
        $customers = [];
        
        while ($row = $stmt->get_result()->fetch_assoc()) {
            $customers[] = [
                'id' => (int)$row['id'],
                'customer_code' => $row['customer_code'],
                'name' => $row['name'],
                'phone' => $row['phone'],
                'email' => $row['email'],
                'customer_type' => $row['customer_type'],
                'credit_limit' => (float)$row['credit_limit'],
                'current_debt' => (float)$row['current_debt'],
                'available_credit' => (float)$row['available_credit'],
                'loyalty_points' => (int)$row['loyalty_points'],
                'total_spent' => (float)$row['total_spent'],
                'last_purchase_date' => $row['last_purchase_date'],
                'is_active' => (bool)$row['is_active'],
                'registration_date' => $row['registration_date'],
                'segment' => $row['segment'],
                'total_orders' => (int)$row['total_orders'],
                'average_rating' => (float)$row['average_rating']
            ];
        }
        
        $stmt->close();
        
        $this->sendResponse(true, [
            'customers' => $customers,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    /**
     * GET /api/customers/{id} - Get single customer
     */
    public function getCustomer($id) {
        $sql = "
            SELECT 
                c.*,
                cs.segment,
                GROUP_CONCAT(DISTINCT ct.tag_name SEPARATOR ', ') as tags
            FROM customers c
            LEFT JOIN customer_segment_assignments csa ON c.id = csa.customer_id
            LEFT JOIN customer_segments cs ON csa.segment_id = cs.id
            LEFT JOIN customer_tag_assignments cta ON c.id = cta.customer_id
            LEFT JOIN customer_tags ct ON cta.tag_id = ct.id
            WHERE c.id = ?
            GROUP BY c.id
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $customer = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$customer) {
            $this->sendResponse(false, null, 'Customer not found', 404);
        }
        
        $this->sendResponse(true, $customer);
    }
    
    /**
     * POST /api/customers - Create new customer
     */
    public function createCustomer() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $this->validateRequired($input, ['name', 'customer_type']);
        
        // Generate customer code
        $customer_code = $this->generateCustomerCode($input['customer_type']);
        
        $sql = "
            INSERT INTO customers (
                customer_code, name, phone, email, address, city, province, customer_type,
                credit_limit, current_debt, available_credit, registration_date, is_active, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), 1, NOW())
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssssssdiiis', 
            $customer_code,
            $input['name'],
            $input['phone'] ?? '',
            $input['email'] ?? '',
            $input['address'] ?? '',
            $input['city'] ?? '',
            $input['province'] ?? '',
            $input['customer_type'],
            $input['credit_limit'] ?? 0,
            $input['current_debt'] ?? 0,
            $input['credit_limit'] ?? 0 // available_credit = credit_limit - current_debt
        );
        
        if ($stmt->execute()) {
            $customer_id = $stmt->insert_id;
            $stmt->close();
            
            // Handle segment assignment if provided
            if (!empty($input['segment_id'])) {
                $this->assignCustomerToSegment($customer_id, $input['segment_id']);
            }
            
            $this->sendResponse(true, [
                'id' => $customer_id,
                'customer_code' => $customer_code
            ], 'Customer created successfully', 201);
        } else {
            $this->sendResponse(false, null, 'Failed to create customer', 500);
        }
    }
    
    /**
     * PUT /api/customers/{id} - Update customer
     */
    public function updateCustomer($id) {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input)) {
            $this->sendResponse(false, null, 'No data provided', 400);
        }
        
        // Build SET clause dynamically
        $set_clauses = [];
        $params = [];
        $allowed_fields = ['name', 'phone', 'email', 'address', 'city', 'province', 'customer_type', 'credit_limit'];
        
        foreach ($allowed_fields as $field) {
            if (isset($input[$field])) {
                $set_clauses[] = "$field = ?";
                $params[] = $input[$field];
            }
        }
        
        if (empty($set_clauses)) {
            $this->sendResponse(false, null, 'No valid fields to update', 400);
        }
        
        $set_clause = implode(', ', $set_clauses);
        $params[] = $id;
        
        $sql = "UPDATE customers SET $set_clause, updated_at = NOW() WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $bind_types = str_repeat('s', count($set_clauses)) . 'i';
        $stmt->bind_param($bind_types, ...$params);
        
        if ($stmt->execute()) {
            $stmt->close();
            $this->sendResponse(true, null, 'Customer updated successfully');
        } else {
            $this->sendResponse(false, null, 'Failed to update customer', 500);
        }
    }
    
    /**
     * GET /api/customers/{id}/analytics - Get customer analytics
     */
    public function getCustomerAnalytics($id) {
        $start_date = $_GET['start_date'] ?? date('Y-m-01', strtotime('-12 months'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');
        
        $sql = "
            SELECT 
                c.name,
                c.customer_type,
                c.registration_date,
                COUNT(DISTINCT ph.id) as total_orders,
                SUM(ph.total_amount) as total_spent,
                AVG(ph.total_amount) as avg_order_value,
                MAX(ph.total_amount) as max_order_value,
                MIN(ph.purchase_date) as first_purchase,
                MAX(ph.purchase_date) as last_purchase,
                COUNT(DISTINCT DATE(ph.purchase_date)) as active_days,
                COALESCE(AVG(cf.rating), 0) as avg_rating,
                COUNT(cf.id) as total_reviews,
                c.loyalty_points,
                c.current_debt,
                c.credit_limit,
                (c.credit_limit - c.current_debt) as available_credit
            FROM customers c
            LEFT JOIN customer_purchase_history ph ON c.id = ph.customer_id 
                AND ph.purchase_date BETWEEN ? AND ?
            LEFT JOIN customer_feedback cf ON c.id = cf.customer_id
            WHERE c.id = ?
            GROUP BY c.id
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssi', $start_date, $end_date, $id);
        $stmt->execute();
        $analytics = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$analytics) {
            $this->sendResponse(false, null, 'Customer not found', 404);
        }
        
        // Add calculated metrics
        $analytics['segment'] = $this->getCustomerSegmentation($id);
        $analytics['lifetime_value'] = $this->calculateCustomerLifetimeValue($id);
        $analytics['churn_risk'] = $this->calculateChurnRisk($analytics);
        
        $this->sendResponse(true, $analytics);
    }
    
    /**
     * Helper methods
     */
    private function generateCustomerCode($type) {
        $prefix = '';
        switch (strtoupper($type)) {
            case 'RETAIL': $prefix = 'RT'; break;
            case 'WHOLESALE': $prefix = 'WS'; break;
            case 'DISTRIBUTOR': $prefix = 'DS'; break;
            default: $prefix = 'CU';
        }
        
        $date = date('ymd');
        $sequence = $this->getCustomerSequence($date);
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
    
    private function getCustomerSequence($date) {
        $sql = "SELECT COUNT(*) as count FROM customers WHERE DATE(created_at) = ? AND customer_code LIKE ?";
        $stmt = $this->db->prepare($sql);
        $prefix = date('ymd') . '%';
        $stmt->bind_param('ss', $date, $prefix);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return ($row['count'] + 1);
    }
    
    private function assignCustomerToSegment($customer_id, $segment_id) {
        $sql = "INSERT INTO customer_segment_assignments (customer_id, segment_id, assigned_date) VALUES (?, ?, CURDATE())";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ii', $customer_id, $segment_id);
        $stmt->execute();
        $stmt->close();
    }
    
    private function getCustomerSegmentation($customer_id) {
        // Implementation would go here - simplified for example
        return 'LOYAL';
    }
    
    private function calculateCustomerLifetimeValue($customer_id) {
        // Implementation would go here - simplified for example
        return 5000000;
    }
    
    private function calculateChurnRisk($customer_data) {
        // Implementation would go here - simplified for example
        return 'LOW';
    }
}

// Route handling
$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_SERVER['REQUEST_URI'];
$path = parse_url($endpoint, PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));

$api = new CRMAPI();

try {
    switch ($method) {
        case 'GET':
            if (count($path_parts) >= 3 && $path_parts[2] === 'customers') {
                if (count($path_parts) >= 4 && is_numeric($path_parts[3])) {
                    // GET /api/customers/{id}
                    $api->getCustomer($path_parts[3]);
                } elseif (count($path_parts) >= 4 && $path_parts[3] === 'analytics') {
                    // GET /api/customers/{id}/analytics
                    $api->getCustomerAnalytics($path_parts[3]);
                } else {
                    // GET /api/customers
                    $api->getCustomers();
                }
            }
            break;
            
        case 'POST':
            if (count($path_parts) >= 3 && $path_parts[2] === 'customers') {
                // POST /api/customers
                $api->createCustomer();
            }
            break;
            
        case 'PUT':
            if (count($path_parts) >= 3 && $path_parts[2] === 'customers' && count($path_parts) >= 4 && is_numeric($path_parts[3])) {
                // PUT /api/customers/{id}
                $api->updateCustomer($path_parts[3]);
            }
            break;
            
        default:
            $api->sendResponse(false, null, 'Method not allowed', 405);
    }
} catch (Exception $e) {
    $api->sendResponse(false, null, $e->getMessage(), 500);
}
?>
