<?php
/**
 * Inventory Valuation Business Logic
 * FIFO, LIFO, Weighted Average Cost Methods
 */

class InventoryValuationLogic {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Calculate inventory value using FIFO method
     */
    public function calculateFIFO($product_id, $quantity_needed = null) {
        $sql = "
            SELECT 
                sb.id,
                sb.batch_number,
                sb.current_quantity,
                sb.unit_cost,
                sb.expiry_date,
                sb.created_at
            FROM stock_batches sb
            WHERE sb.product_id = ? 
            AND sb.status = 'ACTIVE'
            AND sb.current_quantity > 0
            ORDER BY sb.created_at ASC, sb.expiry_date ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $total_value = 0;
        $remaining_quantity = $quantity_needed;
        $used_batches = [];
        
        while ($batch = $result->fetch_assoc()) {
            $quantity_to_use = min($batch['current_quantity'], $remaining_quantity);
            $batch_value = $quantity_to_use * $batch['unit_cost'];
            $total_value += $batch_value;
            
            $used_batches[] = [
                'batch_id' => $batch['id'],
                'quantity_used' => $quantity_to_use,
                'unit_cost' => $batch['unit_cost'],
                'total_value' => $batch_value,
                'remaining_quantity' => $batch['current_quantity'] - $quantity_to_use
            ];
            
            $remaining_quantity -= $quantity_to_use;
            
            if ($remaining_quantity <= 0) break;
        }
        
        $stmt->close();
        
        return [
            'method' => 'FIFO',
            'total_value' => $total_value,
            'average_cost' => $quantity_needed ? ($total_value / $quantity_needed) : 0,
            'used_batches' => $used_batches,
            'remaining_quantity' => max(0, $remaining_quantity)
        ];
    }
    
    /**
     * Calculate inventory value using LIFO method
     */
    public function calculateLIFO($product_id, $quantity_needed = null) {
        $sql = "
            SELECT 
                sb.id,
                sb.batch_number,
                sb.current_quantity,
                sb.unit_cost,
                sb.expiry_date,
                sb.created_at
            FROM stock_batches sb
            WHERE sb.product_id = ? 
            AND sb.status = 'ACTIVE'
            AND sb.current_quantity > 0
            ORDER BY sb.created_at DESC, sb.expiry_date ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $total_value = 0;
        $remaining_quantity = $quantity_needed;
        $used_batches = [];
        
        while ($batch = $result->fetch_assoc()) {
            $quantity_to_use = min($batch['current_quantity'], $remaining_quantity);
            $batch_value = $quantity_to_use * $batch['unit_cost'];
            $total_value += $batch_value;
            
            $used_batches[] = [
                'batch_id' => $batch['id'],
                'quantity_used' => $quantity_to_use,
                'unit_cost' => $batch['unit_cost'],
                'total_value' => $batch_value,
                'remaining_quantity' => $batch['current_quantity'] - $quantity_to_use
            ];
            
            $remaining_quantity -= $quantity_to_use;
            
            if ($remaining_quantity <= 0) break;
        }
        
        $stmt->close();
        
        return [
            'method' => 'LIFO',
            'total_value' => $total_value,
            'average_cost' => $quantity_needed ? ($total_value / $quantity_needed) : 0,
            'used_batches' => $used_batches,
            'remaining_quantity' => max(0, $remaining_quantity)
        ];
    }
    
    /**
     * Calculate inventory value using Weighted Average method
     */
    public function calculateWeightedAverage($product_id, $quantity_needed = null) {
        $sql = "
            SELECT 
                SUM(sb.current_quantity * sb.unit_cost) / SUM(sb.current_quantity) as weighted_cost
            FROM stock_batches sb
            WHERE sb.product_id = ? 
            AND sb.status = 'ACTIVE'
            AND sb.current_quantity > 0
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        $weighted_cost = $row ? $row['weighted_cost'] : 0;
        $total_value = $quantity_needed * $weighted_cost;
        
        return [
            'method' => 'WEIGHTED_AVERAGE',
            'total_value' => $total_value,
            'average_cost' => $weighted_cost,
            'quantity_needed' => $quantity_needed
        ];
    }
    
    /**
     * Process stock movement with valuation
     */
    public function processStockMovement($movement_data, $valuation_method = 'FIFO') {
        $this->db->begin_transaction();
        
        try {
            $product_id = $movement_data['product_id'];
            $quantity = $movement_data['quantity'];
            $movement_type = $movement_data['movement_type'];
            
            // Get current valuation
            $current_valuation = $this->getCurrentValuation($product_id, $valuation_method);
            
            // Process based on movement type
            switch ($movement_type) {
                case 'OUT':
                $result = $this->processStockOut($product_id, $quantity, $valuation_method);
                    break;
                case 'IN':
                    $result = $this->processStockIn($product_id, $quantity, $movement_data['unit_cost'], $movement_data);
                    break;
                case 'TRANSFER':
                    $result = $this->processStockTransfer($movement_data, $valuation_method);
                    break;
                case 'ADJUSTMENT':
                    $result = $this->processStockAdjustment($movement_data, $valuation_method);
                    break;
                default:
                    throw new Exception("Invalid movement type: $movement_type");
            }
            
            $this->db->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Process stock out (sales, transfers out)
     */
    private function processStockOut($product_id, $quantity, $valuation_method) {
        $valuation = $this->calculateFIFO($product_id, $quantity);
        
        // Update batch quantities
        foreach ($valuation['used_batches'] as $batch) {
            $sql = "
                UPDATE stock_batches 
                SET current_quantity = current_quantity - ?,
                    updated_at = NOW()
                WHERE id = ?
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('di', $batch['quantity_used'], $batch['batch_id']);
            $stmt->execute();
            $stmt->close();
        }
        
        // Update warehouse stock
        $this->updateWarehouseStock($product_id, -$quantity);
        
        return [
            'success' => true,
            'valuation_method' => $valuation_method,
            'total_cost' => $valuation['total_value'],
            'average_cost' => $valuation['average_cost'],
            'batches_used' => $valuation['used_batches']
        ];
    }
    
    /**
     * Process stock in (purchases, returns)
     */
    private function processStockIn($product_id, $quantity, $unit_cost, $movement_data) {
        $batch_number = $movement_data['batch_number'] ?? $this->generateBatchNumber($product_id);
        
        // Create or update batch
        $sql = "
            INSERT INTO stock_batches 
            (batch_number, product_id, warehouse_id, initial_quantity, current_quantity, unit_cost, manufacture_date, supplier_id, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'ACTIVE', NOW())
            ON DUPLICATE KEY UPDATE
            SET current_quantity = current_quantity + ?,
                updated_at = NOW()
        ";
        
        $stmt = $this->db->prepare($sql);
        $warehouse_id = $movement_data['warehouse_id'] ?? 1;
        $supplier_id = $movement_data['supplier_id'] ?? null;
        $manufacture_date = $movement_data['manufacture_date'] ?? date('Y-m-d');
        
        $stmt->bind_param('siidddis', 
            $batch_number, $product_id, $warehouse_id, $quantity, $quantity, $unit_cost, $manufacture_date, $supplier_id, $quantity);
        $stmt->execute();
        $stmt->close();
        
        // Update warehouse stock
        $this->updateWarehouseStock($product_id, $quantity);
        
        return [
            'success' => true,
            'batch_number' => $batch_number,
            'unit_cost' => $unit_cost,
            'total_cost' => $quantity * $unit_cost
        ];
    }
    
    /**
     * Process stock adjustment
     */
    private function processStockAdjustment($movement_data, $valuation_method) {
        $product_id = $movement_data['product_id'];
        $quantity = $movement_data['quantity'];
        $reason = $movement_data['reason_code'];
        
        $current_stock = $this->getCurrentStock($product_id);
        $new_quantity = $current_stock + $quantity;
        
        if ($new_quantity < 0) {
            // Stock decrease - use valuation method
            $valuation = $this->calculateFIFO($product_id, abs($quantity));
            $cost_change = -$valuation['total_value'];
        } else {
            // Stock increase - use current average cost
            $avg_cost = $this->getCurrentAverageCost($product_id);
            $cost_change = $quantity * $avg_cost;
        }
        
        // Create adjustment record
        $sql = "
            INSERT INTO stock_movements 
            (movement_number, movement_type, reference_type, product_id, quantity, unit_cost, total_value, reason_code, reason_description, status, created_by, created_at)
            VALUES (?, 'ADJUSTMENT', 'ADJUSTMENT', ?, ?, ?, ?, ?, ?, 'CONFIRMED', ?, NOW())
        ";
        
        $stmt = $this->db->prepare($sql);
        $movement_number = $this->generateMovementNumber();
        
        $stmt->bind_param('siiidssd', 
            $movement_number, $product_id, $quantity, abs($cost_change / $quantity), $cost_change, $reason, $movement_data['reason_description'] ?? '', $this->getCurrentUserId());
        $stmt->execute();
        $stmt->close();
        
        // Update warehouse stock
        $this->updateWarehouseStock($product_id, $quantity);
        
        return [
            'success' => true,
            'adjustment_amount' => $quantity,
            'cost_change' => $cost_change,
            'new_total_stock' => $new_quantity
        ];
    }
    
    /**
     * Get current inventory valuation
     */
    public function getCurrentValuation($product_id, $valuation_method = 'FIFO') {
        switch ($valuation_method) {
            case 'FIFO':
                return $this->calculateFIFO($product_id);
            case 'LIFO':
                return $this->calculateLIFO($product_id);
            case 'WEIGHTED_AVERAGE':
                return $this->calculateWeightedAverage($product_id);
            default:
                throw new Exception("Invalid valuation method: $valuation_method");
        }
    }
    
    /**
     * Get current stock quantity
     */
    private function getCurrentStock($product_id) {
        $sql = "
            SELECT SUM(quantity_available) as total_stock
            FROM warehouse_stocks
            WHERE product_id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row ? $row['total_stock'] : 0;
    }
    
    /**
     * Get current average cost
     */
    private function getCurrentAverageCost($product_id) {
        $sql = "
            SELECT AVG(unit_cost) as avg_cost
            FROM stock_batches
            WHERE product_id = ? 
            AND status = 'ACTIVE'
            AND current_quantity > 0
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row ? $row['avg_cost'] : 0;
    }
    
    /**
     * Update warehouse stock
     */
    private function updateWarehouseStock($product_id, $quantity_change) {
        $sql = "
            UPDATE warehouse_stocks 
            SET quantity_on_hand = quantity_on_hand + ?,
                quantity_available = quantity_available + ?,
                updated_at = NOW()
            WHERE product_id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ddi', $quantity_change, $quantity_change, $product_id);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Generate batch number
     */
    private function generateBatchNumber($product_id) {
        $date = date('ymd');
        $sequence = $this->getBatchSequence($date);
        
        return 'B' . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get batch sequence for the day
     */
    private function getBatchSequence($date) {
        $sql = "SELECT COUNT(*) as count FROM stock_batches WHERE DATE(created_at) = ? AND batch_number LIKE ?";
        $stmt = $this->db->prepare($sql);
        $prefix = 'B' . $date . '%';
        $stmt->bind_param('ss', $date, $prefix);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return ($row['count'] + 1);
    }
    
    /**
     * Generate movement number
     */
    private function generateMovementNumber() {
        $date = date('ymd');
        $sequence = $this->getMovementSequence($date);
        
        return 'MV' . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get movement sequence for the day
     */
    private function getMovementSequence($date) {
        $sql = "SELECT COUNT(*) as count FROM stock_movements WHERE DATE(created_at) = ? AND movement_number LIKE ?";
        $stmt = $this->db->prepare($sql);
        $prefix = 'MV' . $date . '%';
        $stmt->bind_param('ss', $date, $prefix);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return ($row['count'] + 1);
    }
    
    /**
     * Get current user ID (placeholder)
     */
    private function getCurrentUserId() {
        // This should be replaced with actual session/user management
        return 1;
    }
    
    /**
     * Calculate inventory turnover
     */
    public function calculateInventoryTurnover($product_id, $period_days = 365) {
        $sql = "
            SELECT 
                COALESCE(SUM(CASE WHEN movement_type = 'OUT' THEN quantity ELSE 0 END), 0) as quantity_out,
                COALESCE(AVG(quantity_available), 0) as avg_stock
            FROM warehouse_stocks ws
            WHERE ws.product_id = ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        if (!$data || $data['avg_stock'] == 0) {
            return 0;
        }
        
        $annual_turnover = ($data['quantity_out'] / $period_days) * 365;
        return $annual_turnover / $data['avg_stock'];
    }
    
    /**
     * Get inventory aging report
     */
    public function getInventoryAging($product_id = null) {
        $where_clause = $product_id ? "AND sb.product_id = ?" : "";
        $params = $product_id ? [$product_id] : [];
        
        $sql = "
            SELECT 
                p.name as product_name,
                p.code as product_code,
                sb.batch_number,
                sb.current_quantity,
                sb.unit_cost,
                sb.expiry_date,
                DATEDIFF(sb.expiry_date, CURDATE()) as days_to_expiry,
                sb.current_quantity * sb.unit_cost as total_value,
                CASE 
                    WHEN DATEDIFF(sb.expiry_date, CURDATE()) <= 0 THEN 'EXPIRED'
                    WHEN DATEDIFF(sb.expiry_date, CURDATE()) <= 30 THEN 'EXPIRING_SOON'
                    WHEN DATEDIFF(sb.expiry_date, CURDATE()) <= 90 THEN 'EXPIRING_90_DAYS'
                    ELSE 'GOOD'
                END as expiry_status
            FROM stock_batches sb
            JOIN products p ON sb.product_id = p.id
            WHERE sb.status = 'ACTIVE' 
            AND sb.current_quantity > 0
            $where_clause
            ORDER BY sb.expiry_date ASC
        ";
        
        if ($product_id) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('i', $product_id);
        } else {
            $stmt = $this->db->query($sql);
        }
        
        $result = $stmt->get_result();
        $aging_data = [];
        
        while ($row = $result->fetch_assoc()) {
            $aging_data[] = $row;
        }
        
        if ($product_id) {
            $stmt->close();
        }
        
        return $aging_data;
    }
}
