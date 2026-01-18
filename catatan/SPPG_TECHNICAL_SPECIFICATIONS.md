# 🚀 SPPG TECHNICAL SPECIFICATIONS
# Complete System Architecture for SPPG Distribution Management

## 📋 OVERVIEW

Dokumen ini berisi spesifikasi teknis lengkap untuk implementasi SPPG:
- Mobile App dengan QR Scanner
- AI-Powered Menu Planning
- Advanced Analytics Dashboard
- Supply Chain Integration
- Smart Warehouse Management
- Compliance & Reporting
- Multi-Location Support
- User Management System
- Financial Integration
- Security & Data Protection

---

## 🏗️ SYSTEM ARCHITECTURE

### High-Level Architecture
```
┌─────────────────────────────────────────────────────────────┐
│                    SPPG CLOUD PLATFORM                    │
├─────────────────────────────────────────────────────────────┤
│  Frontend (Web)        │  Backend API     │  Database     │  AI/ML      │
├─────────────────────────┼─────────────────┼─────────────┼─────────────┤
│  Bootstrap Web App     │  PHP/Laravel     │  MySQL 8.0+  │  Python/FastAPI│
│  jQuery + Alpine.js   │  REST API        │  Redis Cache  │  TensorFlow    │
│  Admin Dashboard      │  JWT Auth        │  File Storage │  scikit-learn │
└─────────────────────────┴─────────────────┴─────────────┴─────────────┘
```

---

## 📱 MOBILE APPLICATION

### 1. QR Code Scanner Module
```javascript
// QR Scanner Component
const QRScanner = {
  scanBarcode: async () => {
    const result = await BarCodeScanner.scan();
    return {
      code: result.data,
      type: result.format, // PLU, EAN13, CODE128, etc
      timestamp: new Date()
    };
  },
  
  lookupProduct: async (barcode) => {
    const response = await api.get(`/products/lookup/${barcode}`);
    return {
      product: response.data,
      nutrition: response.nutrition,
      stock: response.inventory,
      allergens: response.allergens
    };
  }
};
```

### 2. Offline Mode Support
```javascript
// Offline Storage Strategy
const OfflineManager = {
  cacheSize: '100MB', // Limit cache size
  
  syncData: async () => {
    const localData = await LocalStorage.get('sppg_cache');
    const serverData = await api.syncData();
    
    // Merge and resolve conflicts
    const mergedData = this.mergeData(localData, serverData);
    await LocalStorage.set('sppg_cache', mergedData);
  },
  
  isOnline: () => navigator.onLine,
  
  queueActions: (action) => {
    const queue = await LocalStorage.get('action_queue') || [];
    queue.push({...action, timestamp: Date.now()});
    await LocalStorage.set('action_queue', queue);
  }
};
```

### 3. Real-time Sync
```javascript
// WebSocket Connection
const RealtimeSync = {
  connect: () => {
    this.ws = new WebSocket(WS_URL);
    this.ws.onmessage = this.handleMessage;
    this.ws.onclose = this.reconnect;
  },
  
  handleMessage: (event) => {
    const data = JSON.parse(event.data);
    switch(data.type) {
      case 'stock_update':
        this.updateInventory(data.payload);
        break;
      case 'price_change':
        this.updatePricing(data.payload);
        break;
      case 'new_product':
        this.addProduct(data.payload);
        break;
    }
  }
};
```

---

## 🤖 AI-POWERED MENU PLANNING

### 1. Menu Optimization Engine
```python
# AI Menu Optimizer
class MenuOptimizer:
    def __init__(self):
        self.nutrition_db = NutritionDatabase()
        self.cost_db = CostDatabase()
        self.constraints = BGNConstraints()
    
    def optimize_menu(self, target_group, budget_constraint, preferences):
        """
        Generate optimal menu based on:
        - BGN nutrition requirements
        - Budget constraints (Rp 10,000/porsi)
        - Local preferences
        - Seasonal availability
        """
        
        # Get base menu candidates
        candidates = self.get_menu_candidates(target_group)
        
        # Apply genetic algorithm for optimization
        optimized_menu = self.genetic_algorithm(
            candidates=candidates,
            fitness_function=self.calculate_fitness,
            constraints=self.constraints,
            generations=100
        )
        
        return optimized_menu
    
    def calculate_fitness(self, menu):
        """Calculate fitness score based on multiple factors"""
        nutrition_score = self.nutrition_score(menu)
        cost_score = self.cost_score(menu)
        preference_score = self.preference_score(menu)
        variety_score = self.variety_score(menu)
        
        return (
            nutrition_score * 0.4 +
            cost_score * 0.3 +
            preference_score * 0.2 +
            variety_score * 0.1
        )
```

### 2. Cost Analysis Module
```python
# Cost Analysis Engine
class CostAnalyzer:
    def calculate_portion_cost(self, recipe, serving_size):
        """Calculate cost per portion"""
        ingredient_costs = []
        
        for ingredient in recipe.ingredients:
            unit_cost = self.get_ingredient_cost(ingredient.name)
            total_cost = unit_cost * ingredient.quantity
            ingredient_costs.append(total_cost)
        
        total_recipe_cost = sum(ingredient_costs)
        portion_cost = (total_recipe_cost / recipe.servings) * serving_size
        
        return {
            'portion_cost': portion_cost,
            'ingredient_costs': ingredient_costs,
            'cost_breakdown': self.cost_breakdown(ingredient_costs)
        }
    
    def predict_price_trends(self, ingredient, days=30):
        """Predict future price trends using ML"""
        historical_data = self.get_price_history(ingredient, days=180)
        
        # Use ARIMA for time series prediction
        model = ARIMA(historical_data)
        predictions = model.forecast(steps=days)
        
        return {
            'predicted_prices': predictions,
            'confidence_interval': model.confidence_interval(),
            'trend_direction': self.analyze_trend(predictions)
        }
```

### 3. Nutrition Tracking
```javascript
// Daily Nutrition Tracker
const NutritionTracker = {
  calculateDailyIntake: (meals) => {
    let totalNutrition = {
      protein: 0, carbs: 0, fat: 0, fiber: 0,
      calories: 0, vitamins: {}, minerals: {}
    };
    
    meals.forEach(meal => {
      meal.items.forEach(item => {
        const nutrition = this.getItemNutrition(item.product_id);
        const multiplier = item.quantity / 100; // Per 100g
        
        Object.keys(nutrition).forEach(key => {
          if (typeof nutrition[key] === 'number') {
            totalNutrition[key] += nutrition[key] * multiplier;
          }
        });
      });
    });
    
    return totalNutrition;
  },
  
  compareWithBGNStandards: (dailyIntake, targetGroup) => {
    const bgnStandards = this.getBGNStandards(targetGroup);
    
    return {
      protein: {
        target: bgnStandards.protein,
        actual: dailyIntake.protein,
        percentage: (dailyIntake.protein / bgnStandards.protein) * 100,
        status: this.getStatus(dailyIntake.protein, bgnStandards.protein)
      },
      // ... repeat for other nutrients
    };
  }
};
```

---

## 📊 ADVANCED ANALYTICS DASHBOARD

### 1. Consumption Patterns Analysis
```sql
-- Consumption Analytics View
CREATE VIEW v_consumption_patterns AS
SELECT 
    DATE_TRUNC(menu_date, 'week') as week,
    DATE_TRUNC(menu_date, 'month') as month,
    target_group,
    COUNT(DISTINCT beneficiary_id) as unique_beneficiaries,
    SUM(total_portions) as total_portions_served,
    AVG(total_calories) as avg_calories_per_portion,
    AVG(total_protein) as avg_protein_per_portion,
    -- Most consumed items
    MODE() WITHIN GROUP (ORDER BY COUNT(*) DESC) (product_name) as most_consumed_item,
    -- Least consumed items (potential waste)
    MODE() WITHIN GROUP (ORDER BY COUNT(*) ASC) (product_name) as least_consumed_item
FROM sppg_menu_logs
GROUP BY DATE_TRUNC(menu_date, 'week'), DATE_TRUNC(menu_date, 'month'), target_group;
```

### 2. Waste Reduction Analytics
```python
# Waste Analysis Engine
class WasteAnalyzer:
    def identify_waste_patterns(self, consumption_data, inventory_data):
        """Identify patterns of food waste"""
        
        # Calculate waste percentage
        waste_percentage = self.calculate_waste_percentage(
            consumption_data, inventory_data
        )
        
        # Identify high-waste items
        high_waste_items = self.identify_high_waste_items(
            consumption_data, inventory_data
        )
        
        # Generate recommendations
        recommendations = self.generate_waste_recommendations(
            high_waste_items, waste_percentage
        )
        
        return {
            'overall_waste_percentage': waste_percentage,
            'high_waste_items': high_waste_items,
            'recommendations': recommendations,
            'potential_savings': self.calculate_potential_savings(waste_percentage)
        }
    
    def calculate_waste_percentage(self, consumption, inventory):
        """Calculate percentage of food wasted"""
        theoretical_consumption = self.calculate_theoretical_consumption(inventory)
        actual_consumption = sum(consumption['portions_served'])
        
        waste_percentage = ((theoretical_consumption - actual_consumption) / 
                          theoretical_consumption) * 100
        
        return waste_percentage
```

### 3. Supplier Performance Metrics
```sql
-- Supplier Performance Dashboard
CREATE VIEW v_supplier_performance AS
SELECT 
    s.supplier_name,
    COUNT(DISTINCT po.id) as total_orders,
    AVG(po.total_amount) as avg_order_value,
    AVG(DATEDIFF(po.delivery_date, po.order_date)) as avg_delivery_days,
    COUNT(CASE WHEN po.delivery_date > po.expected_delivery_date THEN 1 END) * 100.0 / 
        COUNT(DISTINCT po.id) as late_delivery_percentage,
    AVG(qi.quality_score) as avg_quality_score,
    COUNT(CASE WHEN qi.has_issues = TRUE THEN 1 END) * 100.0 / 
        COUNT(DISTINCT qi.id) as issue_rate,
    -- Supplier reliability score
    (AVG(qi.quality_score) * 0.6 + 
     (100 - AVG(DATEDIFF(po.delivery_date, po.expected_delivery_date))) * 0.4) as reliability_score
FROM suppliers s
JOIN purchase_orders po ON s.id = po.supplier_id
JOIN quality_inspections qi ON po.id = qi.purchase_order_id
GROUP BY s.supplier_name;
```

---

## 🔗 SUPPLY CHAIN INTEGRATION

### 1. Supplier Portal
```typescript
// Supplier Portal Interface
interface SupplierPortal {
  // Product Management
  registerProduct(product: Product): Promise<Product>;
  updatePricing(productId: string, pricing: Pricing): Promise<void>;
  updateInventory(productId: string, quantity: number): Promise<void>;
  
  // Order Management
  viewOrders(): Promise<PurchaseOrder[]>;
  updateOrderStatus(orderId: string, status: OrderStatus): Promise<void>;
  uploadDeliveryProof(orderId: string, proof: File): Promise<void>;
  
  // Performance Dashboard
  getPerformanceMetrics(): Promise<SupplierMetrics>;
  getQualityReports(): Promise<QualityReport[]>;
}

// Supplier Portal Backend
class SupplierController {
  async registerProduct(req: Request, res: Response) {
    const product = await Product.create({
      ...req.body,
      supplier_id: req.user.supplier_id,
      status: 'pending_approval',
      created_at: new Date()
    });
    
    // Notify admin for approval
    await NotificationService.notifyAdmins({
      type: 'new_product_approval',
      data: product
    });
    
    res.json(product);
  }
}
```

### 2. Order Automation
```python
# Automated Order System
class OrderAutomation:
    def __init__(self):
        self.inventory_monitor = InventoryMonitor()
        self.supplier_manager = SupplierManager()
        self.po_generator = PurchaseOrderGenerator()
    
    def check_reorder_points(self):
        """Check if any items need reordering"""
        reorder_points = await self.inventory_monitor.get_reorder_points()
        
        for item in reorder_points:
            if item.current_stock <= item.reorder_point:
                await self.create_auto_order(item)
    
    async def create_auto_order(self, item):
        """Create automatic purchase order"""
        # Get best supplier for this item
        best_supplier = await self.supplier_manager.get_best_supplier(
            item.product_id, 
            criteria=['price', 'quality', 'delivery_time']
        )
        
        # Generate PO
        po = await self.po_generator.create({
            supplier_id: best_supplier.id,
            items: [{
                product_id: item.product_id,
                quantity: item.reorder_quantity,
                unit_price: best_supplier.unit_price
            }],
            auto_generated: True,
            expected_delivery_date: self.calculate_delivery_date(best_supplier)
        })
        
        # Send to supplier
        await self.send_to_supplier(po, best_supplier)
        
        return po
```

---

## 🚚 DISTRIBUTOR MANAGEMENT & FARMER SOURCING

### 1. Domain Overview
Distributor berperan sebagai penghubung antara petani, peternak, UMKM pangan lokal dengan dapur SPPG. Sesuai arahan program MBG, bahan pangan diutamakan dibeli langsung dari produsen rakyat di sekitar lokasi SPPG untuk memperkuat ekonomi lokal dan menstabilkan harga pangan.

Modul ini mengelola:
- Data petani/peternak/UMKM dan komoditas yang disuplai
- Kontrak dan kapasitas suplai per periode
- Perencanaan sourcing untuk memenuhi kebutuhan SPPG
- Perhitungan profitabilitas per batch distribusi
- Rekomendasi AI untuk kombinasi pemasok paling menguntungkan

### 2. Data Model & Profitability Tracking
```sql
CREATE TABLE farmers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('petani', 'peternak', 'umkm') NOT NULL,
    region_code VARCHAR(20) NOT NULL,
    district VARCHAR(100),
    subdistrict VARCHAR(100),
    village VARCHAR(100),
    latitude DECIMAL(10,6),
    longitude DECIMAL(10,6),
    contact_person VARCHAR(255),
    phone VARCHAR(50),
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE farmer_products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    farmer_id BIGINT UNSIGNED NOT NULL,
    plu_code VARCHAR(50) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quality_grade ENUM('A', 'B', 'C') NOT NULL,
    unit VARCHAR(20) NOT NULL,
    min_order_quantity DECIMAL(12,2),
    max_capacity_per_day DECIMAL(12,2),
    base_price DECIMAL(14,2) NOT NULL,
    lead_time_days INT,
    is_seasonal BOOLEAN DEFAULT FALSE,
    season_start DATE,
    season_end DATE,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES farmers(id)
);

CREATE TABLE sourcing_contracts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    farmer_id BIGINT UNSIGNED NOT NULL,
    sppg_distributor_id VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    contract_price DECIMAL(14,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'IDR',
    payment_terms VARCHAR(100),
    max_volume_per_period DECIMAL(14,2),
    quality_min_score DECIMAL(5,2),
    status ENUM('draft', 'active', 'suspended', 'ended') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES farmers(id)
);

CREATE TABLE distributor_sourcing_batches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_code VARCHAR(100) NOT NULL,
    sppg_id VARCHAR(50) NOT NULL,
    delivery_date DATE NOT NULL,
    commodity_group VARCHAR(100),
    total_quantity DECIMAL(14,2) NOT NULL,
    total_cost DECIMAL(16,2) NOT NULL,
    expected_revenue DECIMAL(16,2) NOT NULL,
    gross_margin DECIMAL(7,2) GENERATED ALWAYS AS
        ((expected_revenue - total_cost) / expected_revenue * 100),
    profit_per_unit DECIMAL(16,4) GENERATED ALWAYS AS
        ((expected_revenue - total_cost) / NULLIF(total_quantity, 0)),
    status ENUM('planned', 'ordered', 'delivered', 'settled') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE distributor_batch_lines (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_id BIGINT UNSIGNED NOT NULL,
    farmer_id BIGINT UNSIGNED NOT NULL,
    farmer_product_id BIGINT UNSIGNED NOT NULL,
    quantity DECIMAL(14,2) NOT NULL,
    unit_price DECIMAL(14,2) NOT NULL,
    line_cost DECIMAL(16,2) GENERATED ALWAYS AS (quantity * unit_price),
    logistics_cost DECIMAL(16,2) DEFAULT 0,
    quality_score DECIMAL(5,2),
    delivery_distance_km DECIMAL(8,2),
    FOREIGN KEY (batch_id) REFERENCES distributor_sourcing_batches(id),
    FOREIGN KEY (farmer_id) REFERENCES farmers(id),
    FOREIGN KEY (farmer_product_id) REFERENCES farmer_products(id)
);
```

### 3. REST API Endpoints
```typescript
class FarmerController {
  async createFarmer(req: Request, res: Response) {
    const farmer = await Farmer.create({
      name: req.body.name,
      type: req.body.type,
      region_code: req.body.region_code,
      district: req.body.district,
      subdistrict: req.body.subdistrict,
      village: req.body.village,
      latitude: req.body.latitude,
      longitude: req.body.longitude,
      contact_person: req.body.contact_person,
      phone: req.body.phone,
      active: true
    });
    res.json(farmer);
  }

  async listFarmers(req: Request, res: Response) {
    const filters = {
      type: req.query.type,
      region_code: req.query.region_code,
      commodity: req.query.commodity
    };
    const farmers = await FarmerRepository.findWithFilters(filters);
    res.json(farmers);
  }

  async updateFarmer(req: Request, res: Response) {
    const farmer = await Farmer.findByIdAndUpdate(req.params.id, req.body, { new: true });
    res.json(farmer);
  }
}

class SourcingController {
  async generateSourcingPlan(req: Request, res: Response) {
    const plan = await SourcingService.generatePlan({
      sppgId: req.body.sppgId,
      deliveryDate: req.body.deliveryDate,
      demandLines: req.body.demandLines,
      budgetConstraint: req.body.budgetConstraint,
      maxSuppliersPerCommodity: req.body.maxSuppliersPerCommodity
    });
    res.json(plan);
  }

  async createBatchFromPlan(req: Request, res: Response) {
    const batch = await SourcingService.createBatchFromPlan(req.body.planId);
    res.json(batch);
  }

  async getBatchProfitability(req: Request, res: Response) {
    const metrics = await SourcingService.getBatchProfitability(req.params.batchId);
    res.json(metrics);
  }

  async getSupplierRecommendations(req: Request, res: Response) {
    const recommendations = await SourcingService.getSupplierRecommendations({
      pluCode: req.query.pluCode,
      sppgId: req.query.sppgId,
      deliveryDate: req.query.deliveryDate,
      maxPrice: req.query.maxPrice
    });
    res.json(recommendations);
  }
}
```

Endpoint ringkas:
- POST `/farmers`
- GET `/farmers`
- PATCH `/farmers/{id}`
- POST `/sourcing/plans/generate`
- POST `/sourcing/batches/from-plan`
- GET `/sourcing/batches/{id}/profitability`
- GET `/sourcing/recommendations`

### 4. AI Integration For Sourcing Recommendations
```python
class SourcingRecommender:
    def __init__(self):
        self.price_predictor = StockPricePredictor()
        self.profit_optimizer = ProfitOptimizer()
        self.farmer_repo = FarmerRepository()
        self.performance_repo = SupplierPerformanceRepository()

    def recommend_sources(self, commodity_plu, sppg_id, delivery_date, demand_quantity, budget_constraint):
        farmers = self.farmer_repo.get_eligible_farmers(
            commodity_plu=commodity_plu,
            sppg_id=sppg_id,
            delivery_date=delivery_date
        )

        price_forecast = self.price_predictor.predict_price_trends(
            product_id=commodity_plu,
            days_ahead=30
        )

        recommendations = []
        for farmer in farmers:
            capacity = farmer.max_capacity_for_date(delivery_date)
            max_qty = min(capacity, demand_quantity)
            if max_qty <= 0:
                continue

            profit_plan = self.profit_optimizer.optimize_stock_levels(
                product_id=commodity_plu,
                current_stock=0,
                budget_constraint=budget_constraint
            )

            performance = self.performance_repo.get_farmer_metrics(farmer.id)

            score = self.calculate_supplier_score(
                farmer=farmer,
                performance=performance,
                profit_plan=profit_plan,
                distance_km=farmer.distance_to_sppg(sppg_id),
                price_forecast=price_forecast
            )

            recommendations.append({
                "farmer_id": farmer.id,
                "farmer_name": farmer.name,
                "recommended_quantity": max_qty,
                "expected_profit": profit_plan["expected_profit"],
                "expected_margin": profit_plan["profit_margin"],
                "distance_km": farmer.distance_to_sppg(sppg_id),
                "reliability_score": performance.reliability_score,
                "quality_score": performance.quality_score,
                "score": score
            })

        sorted_recommendations = sorted(
            recommendations,
            key=lambda r: r["score"],
            reverse=True
        )

        return {
            "commodity_plu": commodity_plu,
            "sppg_id": sppg_id,
            "delivery_date": delivery_date,
            "demand_quantity": demand_quantity,
            "budget_constraint": budget_constraint,
            "recommendations": sorted_recommendations
        }

    def calculate_supplier_score(self, farmer, performance, profit_plan, distance_km, price_forecast):
        margin_score = profit_plan["profit_margin"]
        reliability_score = performance.reliability_score
        quality_score = performance.quality_score
        distance_penalty = max(0, 100 - distance_km)
        volatility_penalty = 100 - min(100, price_forecast["confidence_intervals"]["lstm"]["std_deviation"])

        return (
            margin_score * 0.35 +
            reliability_score * 0.25 +
            quality_score * 0.2 +
            distance_penalty * 0.1 +
            volatility_penalty * 0.1
        )
```

### 4.1 API Rekomendasi Pembelian Bahan

Endpoint backend SPPG untuk rekomendasi pembelian memanfaatkan hasil agregasi kebutuhan bahan dan layanan StockPricePredictor.

- Method: GET
- Route: `/api/sppg/purchase-recommendations`

Contoh payload request:

```json
{
  "sppg_id": "SPPG-001",
  "horizon_weeks": 2,
  "start_date": "2026-02-01",
  "risk_profile": "balanced"
}
```

Contoh payload response tingkat bahan:

```json
{
  "horizon_weeks": 2,
  "start_date": "2026-02-01",
  "recommendations": [
    {
      "material_code": "SPPG-B01-001",
      "material_name": "Beras Fortifikasi 5kg",
      "total_need_kg": 700,
      "current_stock_kg": 200,
      "inbound_po_kg": 100,
      "net_requirement_kg": 400,
      "recommended_buy_kg": 450,
      "forecast_price_now": 11000,
      "forecast_price_next_period": 11800,
      "shelf_life_days": 180,
      "risk_profile": "balanced"
    }
  ]
}
```

Secara internal, service ini:

- Membaca kebutuhan bahan dari `v_sppg_material_demand_weekly` atau `v_sppg_material_demand_monthly`.
- Mengambil stok saat ini dan inbound purchase order dari modul inventory.
- Menggunakan mapping `material_code` ke `product_id` modul AI untuk memanggil `/api/v1/predict` pada StockPricePredictor.
- Menggabungkan hasil prediksi harga dengan batas shelf life dan kapasitas gudang untuk menghasilkan rekomendasi pembelian.

### 5. Batch-Level Profitability Dashboard
```sql
CREATE VIEW v_distributor_batch_profitability AS
SELECT
    b.batch_code,
    b.sppg_id,
    b.delivery_date,
    b.commodity_group,
    b.total_quantity,
    b.total_cost,
    b.expected_revenue,
    b.gross_margin,
    b.profit_per_unit,
    SUM(l.logistics_cost) AS total_logistics_cost,
    AVG(l.quality_score) AS avg_quality_score,
    AVG(l.delivery_distance_km) AS avg_delivery_distance_km,
    COUNT(DISTINCT l.farmer_id) AS total_suppliers
FROM distributor_sourcing_batches b
JOIN distributor_batch_lines l ON b.id = l.batch_id
GROUP BY
    b.batch_code,
    b.sppg_id,
    b.delivery_date,
    b.commodity_group,
    b.total_quantity,
    b.total_cost,
    b.expected_revenue,
    b.gross_margin,
    b.profit_per_unit;
```

Dashboard ini memberikan pandangan cepat tentang profitabilitas per batch, distribusi pemasok, biaya logistik, dan kualitas rata-rata bahan baku yang dikirim ke SPPG.

---

## 🏪 SMART WAREHOUSE MANAGEMENT

### 1. IoT Sensor Integration
```cpp
// IoT Sensor Controller (Arduino/ESP32)
class WarehouseSensorController {
  private:
    DHT22 tempSensor;
    DHT22 humiditySensor;
    MQ135 gasSensor;
    WiFiClient wifi;
    
  public:
    void setup() {
      tempSensor.begin();
      humiditySensor.begin();
      gasSensor.begin();
      wifi.begin(WIFI_SSID, WIFI_PASSWORD);
    }
    
    void loop() {
      // Read sensors every 30 seconds
      float temperature = tempSensor.readTemperature();
      float humidity = humiditySensor.readHumidity();
      int gasLevel = gasSensor.read();
      
      // Create data packet
      SensorData data = {
        timestamp: millis(),
        temperature: temperature,
        humidity: humidity,
        gasLevel: gasLevel,
        warehouseId: WAREHOUSE_ID
      };
      
      // Send to cloud
      this.sendToCloud(data);
      
      delay(30000);
    }
    
    void sendToCloud(SensorData data) {
      HTTPClient http;
      http.begin(API_ENDPOINT);
      http.addHeader("Content-Type", "application/json");
      
      String json = this.sensorDataToJson(data);
      http.POST(json);
      
      http.end();
    }
};
```

### 2. FIFO/FEFO Rotation
```python
# Inventory Rotation System
class InventoryRotation:
    def __init__(self):
        self.inventory = InventoryManager()
        self.rotation_rules = RotationRules()
    
    def calculate_rotation_priority(self, item):
        """Calculate rotation priority based on expiry"""
        days_to_expiry = (item.expiry_date - datetime.now()).days
        
        if days_to_expiry <= 7:
            return 1  # Critical - use immediately
        elif days_to_expiry <= 30:
            return 2  # High priority
        elif days_to_expiry <= 90:
            return 3  # Medium priority
        else:
            return 4  # Low priority
    
    def optimize_storage_location(self, item):
        """Optimize storage location based on rotation needs"""
        priority = self.calculate_rotation_priority(item)
        
        if priority == 1:
            # Store in easily accessible area
            return self.get_prime_location(item.category)
        elif priority == 2:
            # Store in secondary accessible area
            return self.get_secondary_location(item.category)
        else:
            # Store in regular storage
            return self.get_regular_location(item.category)
```

---

## 📋 COMPLIANCE & REPORTING

### 1. BGN Reporting System
```sql
-- BGN Compliance Reports
CREATE TABLE sppg_bgn_reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    report_date DATE NOT NULL,
    sppg_id VARCHAR(50) NOT NULL,
    target_group ENUM('anak', 'balita', 'remaja', 'dewasa', 'lansia') NOT NULL,
    
    -- Nutrition compliance
    avg_protein_per_portion DECIMAL(8,2),
    avg_carbs_per_portion DECIMAL(8,2),
    avg_fat_per_portion DECIMAL(8,2),
    avg_calories_per_portion INT,
    
    -- BGN compliance metrics
    protein_compliance BOOLEAN,
    carb_compliance BOOLEAN,
    fat_compliance BOOLEAN,
    calorie_compliance BOOLEAN,
    
    -- Cost compliance
    avg_cost_per_portion DECIMAL(10,2),
    cost_compliance BOOLEAN, -- Within Rp 10,000
    
    -- Coverage metrics
    total_beneficiaries INT,
    total_portions_served BIGINT,
    coverage_percentage DECIMAL(5,2),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- BGN Compliance View
CREATE VIEW v_bgn_compliance_summary AS
SELECT 
    DATE_TRUNC(report_date, 'month') as report_month,
    sppg_id,
    target_group,
    AVG(avg_protein_per_portion) as avg_protein,
    AVG(avg_carbs_per_portion) as avg_carbs,
    AVG(avg_fat_per_portion) as avg_fat,
    AVG(avg_calories_per_portion) as avg_calories,
    AVG(avg_cost_per_portion) as avg_cost,
    SUM(CASE WHEN protein_compliance THEN 1 ELSE 0 END) * 100.0 / COUNT(*) as protein_compliance_rate,
    SUM(CASE WHEN cost_compliance THEN 1 ELSE 0 END) * 100.0 / COUNT(*) as cost_compliance_rate,
    AVG(coverage_percentage) as avg_coverage
FROM sppg_bgn_reports
GROUP BY DATE_TRUNC(report_date, 'month'), sppg_id, target_group;
```

### 2. Certification Management
```typescript
// Certification Tracking System
interface Certification {
  id: string;
  type: 'halal' | 'bpom' | 'haccp' | 'organic';
  certificateNumber: string;
  issuedBy: string;
  issuedDate: Date;
  expiryDate: Date;
  status: 'active' | 'expired' | 'pending' | 'suspended';
  documentUrl: string;
  verified: boolean;
}

class CertificationManager {
  async checkCertification(productId: string, certificationType: string): Promise<boolean> {
    const product = await Product.findById(productId);
    const certification = await Certification.findOne({
      productId: productId,
      type: certificationType,
      status: 'active'
    });
    
    return certification && certification.expiryDate > new Date();
  }
  
  async expiringSoon(days: number = 30): Promise<Certification[]> {
    const expiryDate = new Date();
    expiryDate.setDate(expiryDate.getDate() + days);
    
    return await Certification.find({
      expiryDate: { $lte: expiryDate },
      status: 'active'
    });
  }
}
```

---

### 4. BGN Compliance Test Scenarios

- Verifikasi supplier:
  - Supplier tanpa dokumen wajib ditolak.
  - Dokumen kadaluarsa mencegah batch diterima.
- Verifikasi kualitas bahan baku:
  - Batch dengan hasil quality check gagal tidak dapat digunakan untuk produksi SPPG.
- Dokumentasi BGN:
  - Setiap pengiriman ke SPPG menghasilkan paket dokumen lengkap berstatus submitted.
- Pelacakan pengiriman:
  - Semua pengiriman memiliki rekaman waktu berangkat dan tiba.
  - Persentase pengiriman yang tiba dalam batas waktu toleransi dipantau.
- Penanganan keluhan:
  - Setiap keluhan tercatat dan memiliki status sampai closed.
  - Waktu penyelesaian keluhan digunakan sebagai indikator kinerja compliance.

---

### 3. BGN Supplier Compliance & Quality Control

#### 3.1 Sistem Verifikasi Bahan Baku

```typescript
type RawMaterialDocumentType =
  | 'legal_entity'
  | 'tax'
  | 'business_license'
  | 'halal'
  | 'bpom'
  | 'haccp'
  | 'lab_test'
  | 'origin_statement';

interface RawMaterialDocument {
  id: string;
  supplierId: string;
  fileUrl: string;
  documentType: RawMaterialDocumentType;
  number: string;
  issuedBy: string;
  issuedDate: Date;
  expiryDate?: Date;
  status: 'pending' | 'approved' | 'rejected';
  verifiedBy?: string;
  verifiedAt?: Date;
}

class RawMaterialVerificationService {
  async verifyIncomingBatch(batchId: string): Promise<void> {
    const batch = await DistributorBatchRepository.getById(batchId);
    const supplier = await SupplierRepository.getById(batch.supplierId);
    const documents = await SupplierDocumentRepository.getActiveDocuments(
      supplier.id
    );

    const hasMandatoryDocs =
      this.hasValidDocument(documents, 'business_license') &&
      this.hasValidDocument(documents, 'tax');

    if (!hasMandatoryDocs) {
      throw new Error('Supplier documents are incomplete or expired');
    }

    const qualityChecks =
      await RawMaterialQualityRepository.getChecksForBatch(batchId);

    const allChecksPassed = qualityChecks.every(
      check => check.status === 'passed'
    );

    if (!allChecksPassed) {
      throw new Error('Raw material quality checks not passed');
    }

    await DistributorBatchRepository.markVerified(batchId);
  }

  private hasValidDocument(
    documents: RawMaterialDocument[],
    type: RawMaterialDocumentType
  ): boolean {
    const now = new Date();
    return documents.some(
      doc =>
        doc.documentType === type &&
        doc.status === 'approved' &&
        (!doc.expiryDate || doc.expiryDate > now)
    );
  }
}
```

#### 3.2 Proses Dokumentasi Sesuai BGN

```typescript
interface BgnDocumentRequirement {
  code: string;
  name: string;
  description: string;
}

interface BgnDocumentPackageInput {
  sppgId: string;
  deliveryBatchId: string;
}

interface BgnDocumentPackage {
  id: string;
  sppgId: string;
  deliveryBatchId: string;
  documents: {
    type: string;
    fileUrl: string;
  }[];
  status: 'draft' | 'submitted' | 'accepted' | 'rejected';
}

class BgnDocumentationWorkflow {
  async generateRequiredDocuments(
    input: BgnDocumentPackageInput
  ): Promise<BgnDocumentRequirement[]> {
    const baseRequirements: BgnDocumentRequirement[] = [
      {
        code: 'BAST',
        name: 'Berita Acara Serah Terima',
        description: 'Dokumen serah terima makanan ke SPPG'
      },
      {
        code: 'DELIVERY_LOG',
        name: 'Log Distribusi',
        description: 'Catatan rute dan waktu pengiriman'
      },
      {
        code: 'QUALITY_LOG',
        name: 'Log Kualitas Bahan',
        description: 'Ringkasan hasil QC bahan baku dan produk jadi'
      }
    ];

    return baseRequirements;
  }

  async submitPackage(
    pkg: BgnDocumentPackage,
    submittedBy: string
  ): Promise<void> {
    if (!pkg.documents.length) {
      throw new Error('Document package is empty');
    }

    await BgnDocumentPackageRepository.save({
      ...pkg,
      status: 'submitted'
    });

    await AuditLogRepository.log({
      actorId: submittedBy,
      action: 'BGN_DOCUMENT_SUBMITTED',
      referenceType: 'bgn_document_package',
      referenceId: pkg.id
    });
  }
}
```

#### 3.3 Mekanisme Pelacakan Pengiriman

```typescript
type DeliveryStatus =
  | 'scheduled'
  | 'in_transit'
  | 'delivered'
  | 'failed'
  | 'returned';

interface DeliveryTrackingPoint {
  timestamp: Date;
  latitude: number;
  longitude: number;
  status: DeliveryStatus;
  note?: string;
}

interface DeliveryTrackingRecord {
  id: string;
  deliveryId: string;
  sppgId: string;
  routeId: string;
  plannedDeparture: Date;
  plannedArrival: Date;
  actualDeparture?: Date;
  actualArrival?: Date;
  status: DeliveryStatus;
  trackingPoints: DeliveryTrackingPoint[];
}

class DeliveryTrackingService {
  async updateTracking(
    deliveryId: string,
    point: DeliveryTrackingPoint
  ): Promise<void> {
    const record =
      await DeliveryTrackingRepository.getByDeliveryId(deliveryId);

    const updatedPoints = [...record.trackingPoints, point];

    await DeliveryTrackingRepository.update(deliveryId, {
      status: point.status,
      trackingPoints: updatedPoints,
      actualDeparture: record.actualDeparture || point.timestamp,
      actualArrival:
        point.status === 'delivered' ? point.timestamp : record.actualArrival
    });
  }

  async calculateTransitCompliance(
    deliveryId: string
  ): Promise<{ withinTimeWindow: boolean }> {
    const record =
      await DeliveryTrackingRepository.getByDeliveryId(deliveryId);

    if (!record.actualDeparture || !record.actualArrival) {
      return { withinTimeWindow: false };
    }

    const durationMinutes =
      (record.actualArrival.getTime() - record.actualDeparture.getTime()) /
      60000;

    const withinTimeWindow = durationMinutes <= 30;

    return { withinTimeWindow };
  }
}
```

#### 3.4 Protokol Penanganan Keluhan

```typescript
type ComplaintChannel = 'internal' | 'sppg' | 'bgn_portal';

type ComplaintStatus =
  | 'open'
  | 'in_review'
  | 'resolved'
  | 'escalated'
  | 'closed';

interface Complaint {
  id: string;
  source: ComplaintChannel;
  referenceType: 'delivery' | 'product' | 'service';
  referenceId: string;
  reportedBy: string;
  reportedAt: Date;
  description: string;
  severity: 'low' | 'medium' | 'high' | 'critical';
  status: ComplaintStatus;
  resolutionNote?: string;
  resolvedAt?: Date;
}

class ComplaintHandler {
  async createComplaint(input: Omit<Complaint, 'id' | 'status'>): Promise<void> {
    const complaint: Complaint = {
      ...input,
      id: IdGenerator.generate(),
      status: 'open'
    };

    await ComplaintRepository.create(complaint);
    await NotificationService.notifyComplianceTeam(complaint);
  }

  async resolveComplaint(
    complaintId: string,
    resolutionNote: string,
    resolvedBy: string
  ): Promise<void> {
    await ComplaintRepository.update(complaintId, {
      status: 'resolved',
      resolutionNote,
      resolvedAt: new Date()
    });

    await AuditLogRepository.log({
      actorId: resolvedBy,
      action: 'COMPLAINT_RESOLVED',
      referenceType: 'complaint',
      referenceId: complaintId
    });
  }
}
```

---

## 🌐 MULTI-LOCATION SUPPORT

### 1. Central Management Dashboard
```typescript
// Multi-Location Management
interface Location {
  id: string;
  name: string;
  type: 'sppg' | 'warehouse' | 'supplier';
  address: Address;
  coordinates: { lat: number; lng: number };
  timezone: string;
  capacity: number;
  manager: User;
  isActive: boolean;
}

class MultiLocationManager {
  async getGlobalDashboard(): Promise<GlobalDashboard> {
    const locations = await Location.find({ isActive: true });
    
    const dashboard = {
      totalLocations: locations.length,
      activeLocations: locations.filter(l => l.isActive).length,
      totalBeneficiaries: await this.getTotalBeneficiaries(locations),
      totalPortionsServed: await this.getTotalPortionsServed(locations),
      averageCompliance: await this.getAverageCompliance(locations),
      locationPerformance: await this.getLocationPerformance(locations)
    };
    
    return dashboard;
  }
  
  async syncDataAcrossLocations(): Promise<void> {
    const locations = await Location.find({ isActive: true });
    
    for (const location of locations) {
      await this.syncLocationData(location);
    }
  }
}
```

### 2. Regional Customization
```python
# Regional Preferences Engine
class RegionalCustomization:
    def __init__(self):
        self.regional_db = RegionalDatabase()
        self.preference_engine = PreferenceEngine()
    
    def get_local_menu_preferences(self, location_id):
        """Get menu preferences based on regional data"""
        region_data = await self.regional_db.get_region(location_id)
        
        preferences = {
          preferred_ingredients: region_data.local_ingredients,
          avoided_ingredients: region_data.avoided_ingredients,
          spice_level: region_data.spice_tolerance,
          portion_sizes: region_data.typical_portions,
          meal_times: region_data.meal_schedule,
          religious_restrictions: region_data.religious_requirements
        }
        
        return preferences
    
    def adapt_menu_for_region(self, base_menu, location_id):
        """Adapt base menu for regional preferences"""
        preferences = await self.get_local_menu_preferences(location_id)
        
        adapted_menu = []
        for item in base_menu:
          if self.is_compatible(item, preferences):
            adapted_item = self.adapt_item(item, preferences)
            adapted_menu.append(adapted_item)
        
        return adapted_menu
```

---

## 👥 USER MANAGEMENT SYSTEM

### 1. Role-Based Access Control
```typescript
// Role-Based Access Control
enum UserRole {
  ADMIN = 'admin',
  SPPG_HEAD = 'sppg_head',
  NUTRITIONIST = 'nutritionist',
  WAREHOUSE_STAFF = 'warehouse_staff',
  PROCUREMENT = 'procurement',
  REPORTER = 'reporter'
}

interface Permission {
  resource: string;
  action: string;
  conditions?: any;
}

const ROLE_PERMISSIONS = {
  [UserRole.ADMIN]: [
    { resource: '*', action: '*' }, // Full access
  ],
  [UserRole.SPPG_HEAD]: [
    { resource: 'menu', action: ['create', 'read', 'update', 'delete'] },
    { resource: 'inventory', action: ['read', 'update'] },
    { resource: 'staff', action: ['read', 'update', 'delete'] },
    { resource: 'reports', action: ['read', 'create'] }
  ],
  [UserRole.NUTRITIONIST]: [
    { resource: 'menu', action: ['read', 'update'] },
    { resource: 'nutrition', action: ['read', 'create', 'update'] },
    { resource: 'reports', action: ['read'] }
  ]
};

class AccessControl {
  hasPermission(user: User, resource: string, action: string): boolean {
    const userRole = user.role;
    const permissions = ROLE_PERMISSIONS[userRole] || [];
    
    return permissions.some(permission => 
      permission.resource === resource && 
      permission.action.includes(action)
    );
  }
}
```

### 2. Performance Tracking
```sql
-- Staff Performance Tracking
CREATE TABLE staff_performance (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    location_id VARCHAR(50) NOT NULL,
    performance_date DATE NOT NULL,
    
    -- Productivity metrics
    meals_prepared INT DEFAULT 0,
    portions_served INT DEFAULT 0,
    efficiency_score DECIMAL(5,2) DEFAULT 0,
    
    -- Quality metrics
    quality_score DECIMAL(5,2) DEFAULT 0,
    customer_satisfaction DECIMAL(5,2) DEFAULT 0,
    compliance_rate DECIMAL(5,2) DEFAULT 0,
    
    -- Training metrics
    training_hours_completed DECIMAL(5,2) DEFAULT 0,
    certifications_obtained INT DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_performance_user (user_id),
    INDEX idx_performance_date (performance_date),
    INDEX idx_performance_location (location_id)
);
```

---

## 💰 FINANCIAL INTEGRATION

### 1. Budget Planning & Tracking
```python
# Financial Management System
class FinancialManager:
    def __init__(self):
        self.budget_db = BudgetDatabase()
        self.expense_tracker = ExpenseTracker()
        self.cost_analyzer = CostAnalyzer()
    
    def create_annual_budget(self, sppg_id, year, total_beneficiaries):
        """Create annual budget based on BGN standards"""
        # Base calculation: Rp 10,000 per portion per day
        daily_budget = 10000 * total_beneficiaries
        annual_budget = daily_budget * 365
        
        # Add buffer for inflation and contingencies
        contingency_buffer = annual_budget * 0.1  # 10% buffer
        total_budget = annual_budget + contingency_buffer
        
        budget = {
          sppg_id: sppg_id,
          year: year,
          total_beneficiaries: total_beneficiaries,
          daily_per_portion_budget: 10000,
          annual_budget: annual_budget,
          contingency_buffer: contingency_buffer,
          total_budget: total_budget,
          created_at: datetime.now()
        }
        
        return await self.budget_db.create(budget)
    
    def track_actual_vs_budget(self, sppg_id, month):
        """Compare actual expenses vs budget"""
        budget = await self.budget_db.get_monthly_budget(sppg_id, month)
        actual_expenses = await self.expense_tracker.get_monthly_expenses(sppg_id, month)
        
        variance = actual_expenses - budget.monthly_budget
        variance_percentage = (variance / budget.monthly_budget) * 100
        
        return {
          budget: budget.monthly_budget,
          actual: actual_expenses,
          variance: variance,
          variance_percentage: variance_percentage,
          status: 'over_budget' if variance > 0 else 'under_budget'
        }
```

### 2. Subsidy Tracking
```sql
-- Subsidy Management
CREATE TABLE subsidy_tracking (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subsidy_type ENUM('government', 'corporate', 'community') NOT NULL,
    subsidy_provider VARCHAR(255) NOT NULL,
    sppg_id VARCHAR(50) NOT NULL,
    
    -- Financial details
    subsidy_amount DECIMAL(15,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'IDR',
    disbursement_date DATE NOT NULL,
    coverage_period_start DATE NOT NULL,
    coverage_period_end DATE NOT NULL,
    
    -- Usage tracking
    total_allocated_portions BIGINT DEFAULT 0,
    used_portions BIGINT DEFAULT 0,
    remaining_portions BIGINT GENERATED ALWAYS AS (total_allocated_portions - used_portions),
    
    -- Compliance
    utilization_rate DECIMAL(5,2) GENERATED ALWAYS AS (used_portions / total_allocated_portions) * 100,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sppg_id) REFERENCES locations(id),
    INDEX idx_subsidy_sppg (sppg_id),
    INDEX idx_subsidy_period (coverage_period_start, coverage_period_end)
);
```

---

## 🔒 SECURITY & DATA PROTECTION

### 1. End-to-End Encryption
```typescript
// Encryption Service
class EncryptionService {
  private readonly algorithm = 'AES-256-GCM';
  private readonly keyLength = 32;
  
  encryptSensitiveData(data: any): string {
    const key = this.getEncryptionKey();
    const iv = crypto.randomBytes(16);
    
    const cipher = crypto.createCipher(this.algorithm, key, iv);
    
    let encrypted = cipher.update(JSON.stringify(data), 'utf8', 'hex');
    encrypted += cipher.final('hex');
    
    return {
      encrypted: encrypted,
      iv: iv.toString('hex'),
      keyId: this.keyId
    };
  }
  
  decryptSensitiveData(encryptedData: string, iv: string): any {
    const key = this.getEncryptionKey();
    const decipher = crypto.createDecipher(this.algorithm, key, Buffer.from(iv, 'hex'));
    
    let decrypted = decipher.update(encryptedData, 'hex', 'utf8');
    decrypted += decipher.final('utf8');
    
    return JSON.parse(decrypted);
  }
}
```

### 2. Multi-Factor Authentication
```typescript
// MFA Implementation
class AuthenticationService {
  async login(username: string, password: string, mfaToken?: string): Promise<AuthResult> {
    // Step 1: Validate credentials
    const user = await this.validateCredentials(username, password);
    if (!user) {
      throw new Error('Invalid credentials');
    }
    
    // Step 2: Check if MFA is required
    if (user.mfaEnabled) {
      if (!mfaToken) {
        // Send MFA token
        await this.sendMFAToken(user);
        return { requiresMFA: true, method: user.mfaMethod };
      }
      
      // Verify MFA token
      const mfaValid = await this.verifyMFAToken(user, mfaToken);
      if (!mfaValid) {
        throw new Error('Invalid MFA token');
      }
    }
    
    // Generate JWT token
    const token = this.generateJWT(user);
    
    return {
      success: true,
      token: token,
      user: this.sanitizeUser(user),
      expiresAt: new Date(Date.now() + 24 * 60 * 60 * 1000)
    };
  }
  
  private async sendMFAToken(user: User): Promise<void> {
    const token = this.generateMFAToken();
    
    if (user.mfaMethod === 'totp') {
      await this.emailService.sendMFAToken(user.email, token);
    } else if (user.mfaMethod === 'sms') {
      await this.smsService.sendMFAToken(user.phone, token);
    }
  }
}
```

---

## 📋 IMPLEMENTATION ROADMAP

### Phase 1: Foundation (Months 1-3)
- [ ] Database setup and migration
- [ ] Basic CRUD APIs
- [ ] Simple mobile app dengan QR scanner untuk tim gudang dan distribusi
- [ ] Basic inventory management
- [ ] User authentication system
- [ ] Modul dasar master farmer dan farmer_products
- [ ] Verifikasi awal supplier dan dokumen legalitas

### Phase 2: Core Features (Months 4-6)
- [ ] AI menu planning engine
- [ ] Advanced analytics dashboard
- [ ] Supplier portal
- [ ] Offline mobile support
- [ ] Real-time synchronization
- [ ] Modul sourcing planner untuk petani/peternak
- [ ] Batch-level profitability untuk distribusi ke SPPG
- [ ] Workflow dokumentasi BGN (BAST, log distribusi, log kualitas)

### Phase 3: Advanced Features (Months 7-12)
- [ ] IoT sensor integration
- [ ] Multi-location support
- [ ] Advanced financial tracking
- [ ] Regional customization
- [ ] Performance optimization
- [ ] Full compliance reporting
- [ ] Pelacakan pengiriman real-time dan analitik kepatuhan waktu distribusi
- [ ] Sistem keluhan terintegrasi dengan modul distribusi

### Phase 4: Enterprise Features (Months 13-18)
- [ ] Machine learning predictions
- [ ] Blockchain traceability
- [ ] Advanced security features
- [ ] API ecosystem
- [ ] Third-party integrations

---

## 🛠️ TECHNOLOGY STACK

### Frontend
- **Web**: Bootstrap 5.x + jQuery 3.x
- **Admin**: Laravel Blade + Tailwind CSS
- **UI Components**: Alpine.js for interactivity
- **Charts**: Chart.js / ApexCharts
- **Tables**: DataTables
- **Notifications**: SweetAlert2

### Backend
- **API**: PHP 8.1+ with Laravel 10.x
- **Database**: MySQL 8.0+ (primary)
- **Cache**: Redis
- **Queue**: Laravel Queue with Redis
- **File Storage**: Local storage with cloud backup
- **Authentication**: JWT + Laravel Sanctum

### AI/ML Server
- **Framework**: Python 3.9+ with FastAPI
- **ML Libraries**: TensorFlow 2.x, scikit-learn
- **Data Processing**: pandas, numpy
- **API Documentation**: Auto-generated by FastAPI
- **Model Deployment**: Docker containers

### Infrastructure
- **Cloud**: AWS/Azure/GCP
- **CDN**: CloudFront
- **Load Balancer**: Application Load Balancer
- **Monitoring**: Prometheus + Grafana
- **Logging**: ELK Stack

### Security
- **Authentication**: JWT + MFA
- **Encryption**: AES-256
- **API Security**: Rate limiting, CORS
- **Compliance**: GDPR, ISO 27001

---

## 💰 AI-POWERED FINANCIAL OPTIMIZATION

### 1. Stock Price Prediction Engine
```python
# Advanced Price Prediction System
class StockPricePredictor:
    def __init__(self):
        self.price_history = PriceHistoryDatabase()
        self.market_data = MarketDataAPI()
        self.ml_models = {
            'lstm': LSTMModel(),  # For time series
            'arima': ARIMAModel(),  # For seasonal patterns
            'prophet': ProphetModel(),  # For trend forecasting
            'xgboost': XGBoostModel()  # For feature importance
        }
        self.ensemble_weights = {
            'lstm': 0.3,
            'arima': 0.25,
            'prophet': 0.25,
            'xgboost': 0.2
        }
    
    def predict_price_trends(self, product_id, days_ahead=30):
        """Predict stock prices using ensemble of ML models"""
        
        # Get historical data
        historical_data = self.price_history.get_data(product_id, days=365)
        market_data = self.market_data.get_market_data(product_id, days=365)
        
        # Feature engineering
        features = self.engineer_features(historical_data, market_data)
        
        # Ensemble predictions
        predictions = {}
        for model_name, model in self.ml_models.items():
            model.train(features, historical_data['price'])
            pred = model.predict(days_ahead)
            predictions[model_name] = pred
        
        # Weighted ensemble
        ensemble_prediction = self.weighted_ensemble(predictions)
        
        return {
            'predictions': ensemble_prediction,
            'confidence_intervals': self.calculate_confidence(predictions),
            'feature_importance': self.get_feature_importance(),
            'market_factors': self.get_market_factors(market_data),
            'seasonal_patterns': self.analyze_seasonal_patterns(historical_data)
        }
    
    def engineer_features(self, historical_data, market_data):
        """Create features for ML models"""
        features = {
            'price_lag_1': historical_data['price'].shift(1),
            'price_lag_7': historical_data['price'].shift(7),
            'price_lag_30': historical_data['price'].shift(30),
            'moving_avg_7': historical_data['price'].rolling(7).mean(),
            'moving_avg_30': historical_data['price'].rolling(30).mean(),
            'volatility': historical_data['price'].rolling(30).std(),
            'trend': self.calculate_trend(historical_data['price']),
            'seasonal': self.get_seasonal_features(historical_data),
            'market_demand': market_data['demand_index'],
            'commodity_price': market_data['commodity_index'],
            'exchange_rate': market_data['exchange_rate'],
            'inflation_rate': market_data['inflation_rate'],
            'weather_index': market_data['weather_impact'],
            'supply_disruption': market_data['supply_disruption_index']
        }
        
        return pd.DataFrame(features)
    
    def weighted_ensemble(self, predictions):
        """Combine predictions using weighted ensemble"""
        ensemble_pred = np.zeros(len(predictions[list(predictions.keys())[0]]))
        
        for model_name, pred in predictions.items():
            weight = self.ensemble_weights[model_name]
            ensemble_pred += weight * pred
        
        return ensemble_pred
    
    def calculate_confidence(self, predictions):
        """Calculate confidence intervals for predictions"""
        confidence_intervals = {}
        
        for model_name, pred in predictions.items():
            std_dev = np.std(pred)
            mean_pred = np.mean(pred)
            
            confidence_intervals[model_name] = {
                'lower_95': mean_pred - 1.96 * std_dev,
                'upper_95': mean_pred + 1.96 * std_dev,
                'lower_80': mean_pred - 1.28 * std_dev,
                'upper_80': mean_pred + 1.28 * std_dev,
                'std_deviation': std_dev
            }
        
        return confidence_intervals
```

### 2. Profit Optimization Engine
```python
# Profit Maximization System
class ProfitOptimizer:
    def __init__(self):
        self.price_predictor = StockPricePredictor()
        self.cost_analyzer = CostAnalyzer()
        self.demand_forecaster = DemandForecaster()
        self.inventory_manager = InventoryManager()
        self.financial_calculator = FinancialCalculator()
    
    def optimize_stock_levels(self, product_id, current_stock, budget_constraint):
        """Optimize stock levels for maximum profit"""
        
        # Get price predictions
        price_predictions = self.price_predictor.predict_price_trends(product_id)
        expected_prices = price_predictions['predictions']
        
        # Get demand forecast
        demand_forecast = self.demand_forecaster.forecast_demand(product_id, days=30)
        
        # Calculate optimal stock levels
        optimal_stock = self.calculate_optimal_stock(
            expected_prices,
            demand_forecast,
            current_stock,
            budget_constraint
        )
        
        # Calculate profit metrics
        profit_metrics = self.calculate_profit_metrics(
            optimal_stock,
            expected_prices,
            demand_forecast
        )
        
        return {
            'optimal_stock_level': optimal_stock,
            'reorder_point': optimal_stock * 0.3,  # 30% safety stock
            'max_stock_level': optimal_stock * 1.5,  # Maximum storage capacity
            'expected_profit': profit_metrics['expected_profit'],
            'profit_margin': profit_metrics['profit_margin'],
            'roi': profit_metrics['roi'],
            'risk_assessment': profit_metrics['risk_assessment'],
            'recommendations': self.generate_recommendations(optimal_stock, profit_metrics)
        }
    
    def calculate_optimal_stock(self, expected_prices, demand_forecast, current_stock, budget_constraint):
        """Calculate optimal stock level using mathematical optimization"""
        
        # Economic Order Quantity (EOQ) with price uncertainty
        holding_cost = self.cost_analyzer.get_holding_cost()
        ordering_cost = self.cost_analyzer.get_ordering_cost()
        
        # Expected price volatility
        price_volatility = np.std(expected_prices)
        
        # Optimize for maximum profit
        optimal_quantity = self.optimize_for_profit(
            expected_prices,
            demand_forecast,
            holding_cost,
            ordering_cost,
            price_volatility,
            budget_constraint
        )
        
        return optimal_quantity
    
    def calculate_profit_metrics(self, optimal_stock, expected_prices, demand_forecast):
        """Calculate comprehensive profit metrics"""
        
        expected_revenue = np.sum(expected_prices * demand_forecast)
        expected_cost = self.calculate_expected_cost(optimal_stock, expected_prices, demand_forecast)
        
        gross_profit = expected_revenue - expected_cost
        profit_margin = (gross_profit / expected_revenue) * 100
        roi = (gross_profit / expected_cost) * 100
        
        return {
            'expected_profit': gross_profit,
            'profit_margin': profit_margin,
            'roi': roi,
            'expected_revenue': expected_revenue,
            'expected_cost': expected_cost,
            'break_even_point': self.calculate_break_even_point(expected_prices, demand_forecast)
        }
```

### 3. Market Intelligence System
```python
# Market Intelligence for Price Optimization
class MarketIntelligence:
    def __init__(self):
        self.data_sources = {
            'commodity_markets': CommodityMarketAPI(),
            'weather_service': WeatherAPI(),
            'economic_indicators': EconomicDataAPI(),
            'competitor_pricing': CompetitorPricingAPI(),
            'social_media': SocialMediaAPI()
        }
    
    def analyze_market_factors(self, product_category):
        """Analyze all market factors affecting prices"""
        
        market_data = {}
        
        # Commodity market trends
        commodity_data = self.data_sources['commodity_markets'].get_data(product_category)
        market_data['commodity_trends'] = self.analyze_commodity_trends(commodity_data)
        
        # Weather impact analysis
        weather_data = self.data_sources['weather_service'].get_forecast(days=30)
        market_data['weather_impact'] = self.analyze_weather_impact(weather_data, product_category)
        
        # Economic indicators
        economic_data = self.data_sources['economic_indicators'].get_current_data()
        market_data['economic_factors'] = {
            'inflation_rate': economic_data['inflation'],
            'exchange_rate': economic_data['exchange_rate'],
            'interest_rate': economic_data['interest_rate'],
            'gdp_growth': economic_data['gdp_growth']
        }
        
        # Competitor pricing
        competitor_data = self.data_sources['competitor_pricing'].get_pricing(product_category)
        market_data['competitor_analysis'] = self.analyze_competitor_pricing(competitor_data)
        
        # Social media sentiment
        social_data = self.data_sources['social_media'].analyze_sentiment(product_category)
        market_data['social_sentiment'] = social_data
        
        return market_data
    
    def generate_pricing_strategy(self, product_id, market_data):
        """Generate dynamic pricing strategy based on market intelligence"""
        
        current_price = self.get_current_price(product_id)
        market_trends = market_data['commodity_trends']
        weather_impact = market_data['weather_impact']
        economic_factors = market_data['economic_factors']
        competitor_pricing = market_data['competitor_analysis']
        social_sentiment = market_data['social_sentiment']
        
        # Calculate optimal price
        base_price = current_price
        
        # Adjust for market trends
        if market_trends['direction'] == 'increasing':
            base_price *= 1.02  # Increase by 2%
        elif market_trends['direction'] == 'decreasing':
            base_price *= 0.98  # Decrease by 2%
        
        # Adjust for weather impact
        if weather_impact['severity'] == 'high':
            base_price *= 1.05  # Increase by 5% for supply disruption
        elif weather_impact['severity'] == 'low':
            base_price *= 0.99  # Decrease by 1% for favorable conditions
        
        # Adjust for economic factors
        if economic_factors['inflation_rate'] > 0.05:  # High inflation
            base_price *= 1.03  # Increase by 3%
        
        # Competitor-based pricing
        if competitor_pricing['avg_price'] > base_price * 1.1:
            base_price = competitor_pricing['avg_price'] * 0.95  # Match competition
        
        # Social sentiment adjustment
        if social_sentiment['sentiment_score'] > 0.7:  # Positive sentiment
            base_price *= 1.02  # Increase by 2%
        elif social_sentiment['sentiment_score'] < 0.3:  # Negative sentiment
            base_price *= 0.98  # Decrease by 2%
        
        return {
            'recommended_price': base_price,
            'price_strategy': self.determine_pricing_strategy(market_data),
            'confidence_score': self.calculate_pricing_confidence(market_data),
            'expected_demand_change': self.predict_demand_change(base_price, market_data),
            'competitor_position': self.analyze_competitor_position(base_price, competitor_pricing)
        }
```

### 4. Real-Time Trading Dashboard
```typescript
// Real-Time Trading Dashboard Interface
interface TradingDashboard {
  // Price Prediction Display
  pricePrediction: {
    productId: string;
    currentPrice: number;
    predictedPrices: number[];
    confidenceIntervals: ConfidenceInterval[];
    trendDirection: 'up' | 'down' | 'stable';
    volatilityLevel: 'low' | 'medium' | 'high';
    lastUpdated: Date;
  };
  
  // Profit Metrics
  profitMetrics: {
    totalValue: number;
    unrealizedProfit: number;
    realizedProfit: number;
    profitMargin: number;
    roi: number;
    sharpeRatio: number;
    maxDrawdown: number;
  };
  
  // Market Intelligence
  marketIntelligence: {
    commodityTrends: MarketTrend[];
    competitorPricing: CompetitorPrice[];
    economicIndicators: EconomicIndicator[];
    weatherImpact: WeatherImpact;
    socialSentiment: SocialSentiment;
  };
  
  // Trading Actions
  tradingActions: {
    buyRecommendations: BuyRecommendation[];
    sellRecommendations: SellRecommendation[];
    holdRecommendations: HoldRecommendation[];
    riskAlerts: RiskAlert[];
  };
}

// Real-time Dashboard Component
const TradingDashboard: React.FC = () => {
  const [pricePredictions, setPricePredictions] = useState<PricePrediction[]>([]);
  const [profitMetrics, setProfitMetrics] = useState<ProfitMetrics | null>(null);
  const [marketIntelligence, setMarketIntelligence] = useState<MarketIntelligence | null>(null);
  
  // Real-time WebSocket connection
  useEffect(() => {
    const ws = new WebSocket(WS_TRADING_URL);
    
    ws.onmessage = (event) => {
      const data = JSON.parse(event.data);
      
      switch(data.type) {
        case 'price_update':
          setPricePredictions(prev => 
            prev.map(p => p.productId === data.productId ? data : p)
          );
          break;
          
        case 'profit_update':
          setProfitMetrics(data.payload);
          break;
          
        case 'market_intelligence':
          setMarketIntelligence(data.payload);
          break;
          
        case 'trading_alert':
          handleTradingAlert(data.payload);
          break;
      }
    };
    
    return () => {
      ws.close();
    };
  }, []);
  
  return (
    <div className="trading-dashboard">
      <PricePredictionPanel predictions={pricePredictions} />
      <ProfitMetricsPanel metrics={profitMetrics} />
      <MarketIntelligencePanel intelligence={marketIntelligence} />
      <TradingActionsPanel />
      <RiskAlertsPanel />
    </div>
  );
};
```

### 5. Automated Trading Bot
```python
# Automated Trading Bot for Stock Optimization
class TradingBot:
    def __init__(self):
        self.api_client = TradingAPI()
        self.price_predictor = StockPricePredictor()
        self.risk_manager = RiskManager()
        self.portfolio_manager = PortfolioManager()
        
    def execute_trading_strategy(self, product_id):
        """Execute automated trading strategy"""
        
        # Get current market data
        current_price = self.api_client.get_current_price(product_id)
        predictions = self.price_predictor.predict_price_trends(product_id)
        
        # Risk assessment
        risk_level = self.risk_manager.assess_risk(product_id, current_price, predictions)
        
        # Generate trading signals
        trading_signals = self.generate_trading_signals(current_price, predictions, risk_level)
        
        # Execute trades based on signals
        for signal in trading_signals:
            if signal.action == 'buy' and signal.confidence > 0.7:
                self.execute_buy_order(product_id, signal)
            elif signal.action == 'sell' and signal.confidence > 0.7:
                self.execute_sell_order(product_id, signal)
        
        return {
            'executed_trades': self.get_executed_trades(),
            'portfolio_value': self.portfolio_manager.get_total_value(),
            'profit_loss': self.calculate_total_profit_loss(),
            'risk_metrics': self.risk_manager.get_current_risk_metrics()
        }
    
    def generate_trading_signals(self, current_price, predictions, risk_level):
        """Generate buy/sell signals based on predictions and risk"""
        signals = []
        
        # Buy signals
        if self.should_buy(current_price, predictions, risk_level):
            signals.append({
                'action': 'buy',
                'confidence': self.calculate_signal_confidence(predictions, risk_level),
                'quantity': self.calculate_optimal_quantity(predictions),
                'max_price': current_price * 1.02,  # 2% above current
                'reason': 'Price dip detected with upward trend'
            })
        
        # Sell signals
        if self.should_sell(current_price, predictions, risk_level):
            signals.append({
                'action': 'sell',
                'confidence': self.calculate_signal_confidence(predictions, risk_level),
                'quantity': self.calculate_sell_quantity(predictions),
                'min_price': current_price * 0.98,  # 2% below current
                'reason': 'Price peak detected with downward trend'
            })
        
        return signals
```

---

## 📊 FINANCIAL ANALYTICS DASHBOARD

### 1. Real-Time Profit Tracking
```sql
-- Real-Time Profit Analytics View
CREATE VIEW v_realtime_profit_analytics AS
SELECT 
    p.product_id,
    p.product_name,
    p.current_price,
    p.predicted_price,
    p.price_trend,
    p.volatility_index,
    
    -- Current inventory value
    (p.current_price * i.current_stock) as current_inventory_value,
    (p.predicted_price * i.current_stock) as predicted_inventory_value,
    
    -- Profit metrics
    (p.current_price - i.avg_cost) * i.current_stock as current_gross_profit,
    ((p.current_price - i.avg_cost) / p.current_price) * 100 as current_profit_margin,
    
    -- Historical performance
    AVG(p.daily_profit) OVER (PARTITION BY p.product_id ORDER BY p.date DESC ROWS BETWEEN 1 AND 30) as avg_30day_profit,
    MAX(p.daily_profit) OVER (PARTITION BY p.product_id ORDER BY p.date DESC ROWS BETWEEN 1 AND 30) as max_30day_profit,
    
    -- Risk metrics
    STDDEV(p.price_change) OVER (PARTITION BY p.product_id ORDER BY p.date DESC ROWS BETWEEN 1 AND 30) as price_volatility,
    (p.current_price - p.moving_avg_30) / p.moving_avg_30 as deviation_from_moving_avg
    
FROM product_price_predictions p
JOIN inventory i ON p.product_id = i.product_id
WHERE p.date = CURRENT_DATE;
```

### 2. Portfolio Performance Metrics
```sql
-- Portfolio Performance Dashboard
CREATE VIEW v_portfolio_performance AS
SELECT 
    DATE_TRUNC(p.date, 'month') as performance_month,
    
    -- Portfolio metrics
    SUM(p.total_value) as total_portfolio_value,
    SUM(p.realized_profit) as total_realized_profit,
    SUM(p.unrealized_profit) as total_unrealized_profit,
    
    -- Performance ratios
    (SUM(p.realized_profit) / NULLIF(SUM(p.total_value), 0)) * 100 as portfolio_return,
    (SUM(p.realized_profit) / SUM(p.total_investment)) * 100 as roi,
    
    -- Risk metrics
    STDDEV(p.daily_return) as portfolio_volatility,
    MAX(p.max_drawdown) as max_drawdown,
    
    -- Sharpe ratio
    AVG(p.daily_return) / NULLIF(STDDEV(p.daily_return), 0) as sharpe_ratio,
    
    -- Top performers
    (SELECT product_name FROM product_performance pp 
     WHERE pp.performance_month = DATE_TRUNC(p.date, 'month') 
     ORDER BY pp.total_profit DESC LIMIT 5) as top_performers
    
FROM portfolio_performance p
GROUP BY DATE_TRUNC(p.date, 'month');
```

---

## 🎯 IMPLEMENTATION PRIORITY

### Phase 1: Foundation (Months 1-3)
- [ ] Historical price data collection
- [ ] Basic ML models training
- [ ] Simple price prediction API
- [ ] Basic profit calculation
- [ ] Manual trading interface

### Phase 2: Advanced Features (Months 4-6)
- [ ] Ensemble ML models
- [ ] Real-time market data integration
- [ ] Automated trading bot
- [ ] Advanced analytics dashboard
- [ ] Risk management system

### Phase 3: Enterprise Features (Months 7-12)
- [ ] AI-powered market prediction
- [ ] Multi-asset portfolio optimization
- [ ] Advanced risk management
- [ ] Regulatory compliance automation
- [ ] Third-party trading integration

---

## 💡 KEY BENEFITS

1. **Maximized Profit** - AI optimization for maximum profitability
2. **Risk Management** - Advanced risk assessment and mitigation
3. **Market Intelligence** - Real-time market analysis
4. **Automated Trading** - 24/7 automated trading opportunities
5. **Portfolio Analytics** - Comprehensive performance tracking
6. **Regulatory Compliance** - Automated compliance checking

---

*Sistem ini akan mengubah SPPG dari sistem distribusi biasa menjadi **platform trading yang cerdas** dengan kemampuan memaksimalkan keuntungan melalui manajemen risiko yang canggih.*
