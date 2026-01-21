<?php
/**
 * CRM Business Logic
 * Customer Relationship Management Logic
 */

class CRMLogic {
    private $db;
    private $user_id;
    
    public function __construct($database, $user_id) {
        $this->db = $database;
        $this->user_id = $user_id;
    }
    
    /**
     * Generate unique customer code
     */
    public function generateCustomerCode($type = 'RETAIL') {
        $prefix = '';
        switch($strtoupper($type)) {
            case 'RETAIL':
                $prefix = 'RT';
                break;
            case 'WHOLESALE':
                $prefix = 'WS';
                break;
            case 'DISTRIBUTOR':
                $prefix = 'DS';
                break;
            case 'AGENCY':
                $prefix = 'AG';
                break;
            default:
                $prefix = 'CU';
        }
        
        $date = date('ymd');
        $sequence = $this->getCustomerSequence($date);
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get customer sequence for the day
     */
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
    
    /**
     * Calculate customer credit limit based on history
     */
    public function calculateCreditLimit($customer_id) {
        $sql = "
            SELECT 
                COUNT(*) as total_orders,
                AVG(total_amount) as avg_order_value,
                MAX(total_amount) as max_order_value,
                SUM(CASE WHEN payment_status = 'PAID' THEN 1 ELSE 0 END) as paid_orders,
                SUM(CASE WHEN payment_status != 'PAID' THEN 1 ELSE 0 END) as unpaid_orders
            FROM customer_purchase_history 
            WHERE customer_id = ? 
            AND purchase_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        if ($data['total_orders'] == 0) {
            return 1000000; // Default limit for new customers
        }
        
        $payment_ratio = $data['paid_orders'] / $data['total_orders'];
        $base_limit = $data['avg_order_value'] * 10;
        
        // Adjust based on payment history
        if ($payment_ratio >= 0.95) {
            $base_limit *= 1.5;
        } elseif ($payment_ratio >= 0.85) {
            $base_limit *= 1.2;
        }
        
        return round($base_limit, -2);
    }
    
    /**
     * Calculate customer loyalty points
     */
    public function calculateLoyaltyPoints($customer_id, $purchase_amount) {
        $sql = "SELECT customer_type FROM customers WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();
        $stmt->close();
        
        $points_per_1000 = 0;
        switch ($customer['customer_type']) {
            case 'RETAIL':
                $points_per_1000 = 10;
                break;
            case 'WHOLESALE':
                $points_per_1000 = 15;
                break;
            case 'DISTRIBUTOR':
                $points_per_1000 = 20;
                break;
            case 'AGENCY':
                $points_per_1000 = 25;
                break;
        }
        
        return intval(($purchase_amount / 1000) * $points_per_1000);
    }
    
    /**
     * Get customer segmentation based on RFM analysis
     */
    public function getCustomerSegmentation($customer_id) {
        $sql = "
            SELECT 
                c.id,
                c.name,
                c.total_spent,
                c.last_purchase_date,
                COUNT(ph.id) as frequency,
                MAX(ph.purchase_date) as last_purchase,
                AVG(ph.total_amount) as avg_order_value,
                DATEDIFF(CURDATE(), MAX(ph.purchase_date)) as recency_days
            FROM customers c
            LEFT JOIN customer_purchase_history ph ON c.id = ph.customer_id
            WHERE c.id = ?
            GROUP BY c.id
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        if (!$data) {
            return 'NEW';
        }
        
        $recency_score = $this->calculateRecencyScore($data['recency_days']);
        $frequency_score = $this->calculateFrequencyScore($data['frequency']);
        $monetary_score = $this->calculateMonetaryScore($data['total_spent'], $data['avg_order_value']);
        
        $rfm_score = $recency_score + $frequency_score + $monetary_score;
        
        if ($rfm_score >= 80) {
            return 'CHAMPION';
        } elseif ($rfm_score >= 60) {
            return 'LOYAL';
        } elseif ($rfm_score >= 40) {
            return 'POTENTIAL_LOYAL';
        } elseif ($rfm_score >= 20) {
            return 'AT_RISK';
        } else {
            return 'LOST';
        }
    }
    
    /**
     * Calculate recency score
     */
    private function calculateRecencyScore($days) {
        if ($days <= 30) return 40;
        if ($days <= 60) return 30;
        if ($days <= 90) return 20;
        if ($days <= 180) return 10;
        return 0;
    }
    
    /**
     * Calculate frequency score
     */
    private function calculateFrequencyScore($frequency) {
        if ($frequency >= 20) return 40;
        if ($frequency >= 10) return 30;
        if ($frequency >= 5) return 20;
        if ($frequency >= 3) return 10;
        return 0;
    }
    
    /**
     * Calculate monetary score
     */
    private function calculateMonetaryScore($total_spent, $avg_order_value) {
        $score = 0;
        
        // Score based on total spending
        if ($total_spent >= 10000000) $score += 20;
        elseif ($total_spent >= 5000000) $score += 15;
        elseif ($total_spent >= 1000000) $score += 10;
        elseif ($total_spent >= 500000) $score += 5;
        
        // Score based on average order value
        if ($avg_order_value >= 1000000) $score += 20;
        elseif ($avg_order_value >= 500000) $score += 15;
        elseif ($avg_order_value >= 200000) $score += 10;
        elseif ($avg_order_value >= 100000) $score += 5;
        
        return $score;
    }
    
    /**
     * Process customer communication
     */
    public function processCustomerCommunication($customer_ids, $message, $type, $subject = '', $scheduled_date = null) {
        $communication_type = ['EMAIL', 'SMS', 'WHATSAPP', 'PHONE', 'LETTER'];
        
        if (!in_array($type, $communication_type)) {
            throw new Exception("Invalid communication type");
        }
        
        $sql = "
            INSERT INTO customer_communications 
            (customer_id, communication_type, subject, message, status, sent_date, scheduled_date, sent_by) 
            VALUES (?, ?, ?, ?, 'SENT', ?, ?, ?)
        ";
        
        $stmt = $this->db->prepare($sql);
        $sent_date = $scheduled_date ? $scheduled_date : date('Y-m-d H:i:s');
        
        foreach ($customer_ids as $customer_id) {
            $stmt->bind_param('isssssi', $customer_id, $type, $subject, $message, $sent_date, $scheduled_date, $this->user_id);
            $stmt->execute();
        }
        
        $stmt->close();
        
        return count($customer_ids);
    }
    
    /**
     * Update customer debt and available credit
     */
    public function updateCustomerDebt($customer_id, $amount, $type = 'ADD') {
        $this->db->begin_transaction();
        
        try {
            $sql = "SELECT current_debt, credit_limit FROM customers WHERE id = ? FOR UPDATE";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('i', $customer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $customer = $result->fetch_assoc();
            $stmt->close();
            
            if (!$customer) {
                throw new Exception("Customer not found");
            }
            
            $new_debt = $type == 'ADD' 
                ? $customer['current_debt'] + $amount 
                : max(0, $customer['current_debt'] - $amount);
            
            if ($new_debt > $customer['credit_limit']) {
                throw new Exception("Credit limit exceeded");
            }
            
            $sql = "UPDATE customers SET current_debt = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('di', $new_debt, $customer_id);
            $stmt->execute();
            $stmt->close();
            
            $this->db->commit();
            
            return [
                'success' => true,
                'previous_debt' => $customer['current_debt'],
                'new_debt' => $new_debt,
                'available_credit' => $customer['credit_limit'] - $new_debt
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Get customer analytics
     */
    public function getCustomerAnalytics($customer_id, $start_date = null, $end_date = null) {
        $start_date = $start_date ?: date('Y-m-01', strtotime('-12 months'));
        $end_date = $end_date ?: date('Y-m-d');
        
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
        $stmt->bind_param('ssi', $start_date, $end_date, $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        // Add calculated metrics
        if ($data) {
            $data['segment'] = $this->getCustomerSegmentation($customer_id);
            $data['lifetime_value'] = $this->calculateCustomerLifetimeValue($customer_id);
            $data['churn_risk'] = $this->calculateChurnRisk($data);
        }
        
        return $data;
    }
    
    /**
     * Calculate customer lifetime value
     */
    private function calculateCustomerLifetimeValue($customer_id) {
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
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        if (!$data || $data['total_transactions'] == 0) {
            return 0;
        }
        
        $avg_transactions_per_year = ($data['total_transactions'] / max(1, $data['customer_age_days'] / 365)) * 365;
        $avg_order_value = $data['avg_transaction'];
        $gross_margin = 0.20; // Assume 20% gross margin
        
        return ($avg_transactions_per_year * $avg_order_value * $gross_margin) - ($avg_transactions_per_year * 50); // Subtract acquisition cost
    }
    
    /**
     * Calculate churn risk
     */
    private function calculateChurnRisk($customer_data) {
        $risk_score = 0;
        
        // Recency risk
        $days_since_last_purchase = $customer_data['last_purchase'] 
            ? (strtotime(date('Y-m-d')) - strtotime($customer_data['last_purchase'])) / 86400 
            : 999;
        
        if ($days_since_last_purchase > 180) $risk_score += 30;
        elseif ($days_since_last_purchase > 90) $risk_score += 20;
        elseif ($days_since_last_purchase > 60) $risk_score += 10;
        
        // Payment behavior risk
        if ($customer_data['current_debt'] > 0) {
            $debt_ratio = $customer_data['current_debt'] / $customer_data['credit_limit'];
            if ($debt_ratio > 0.8) $risk_score += 25;
            elseif ($debt_ratio > 0.6) $risk_score += 15;
            elseif ($debt_ratio > 0.4) $risk_score += 10;
        }
        
        // Order frequency risk
        if ($customer_data['total_orders'] < 3 && $customer_data['registration_date'] < date('Y-m-d', strtotime('-6 months'))) {
            $risk_score += 20;
        }
        
        if ($risk_score >= 50) return 'HIGH';
        if ($risk_score >= 30) return 'MEDIUM';
        if ($risk_score >= 15) return 'LOW';
        return 'VERY_LOW';
    }
}
