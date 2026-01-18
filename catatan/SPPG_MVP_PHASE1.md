# SPPG MVP Phase 1 Prototype

## Overview
Minimum Viable Product for SPPG AI-powered financial optimization focusing on basic stock price prediction and profit maximization features.

## Phase 1 Features

### 1. Data Collection Module

#### Database Schema
```sql
-- Historical price data
CREATE TABLE price_history (
    id SERIAL PRIMARY KEY,
    product_id VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    volume INTEGER,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    source VARCHAR(50) DEFAULT 'manual'
);

-- Product information
CREATE TABLE products (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category VARCHAR(100),
    current_price DECIMAL(10,2),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ML models storage
CREATE TABLE ml_models (
    id SERIAL PRIMARY KEY,
    model_name VARCHAR(100) NOT NULL,
    model_type VARCHAR(50) NOT NULL,
    version VARCHAR(20),
    accuracy DECIMAL(5,4),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    file_path VARCHAR(500)
);
```

Pada sisi aplikasi SPPG, kolom `ai_product_id` di tabel `sppg_material_ai_products` dirancang agar nilainya selalu sama dengan `products.id` di skema MySQL ini, sehingga setiap `material_code` SPPG memiliki relasi eksplisit ke satu atau beberapa produk yang diprediksi harganya oleh modul AI.

#### Python Implementation
```python
# data_collector.py
import pandas as pd
import numpy as np
from datetime import datetime, timedelta
import psycopg2
from typing import List, Dict, Optional

class DataCollector:
    def __init__(self, db_connection: str):
        self.db_conn = db_connection
        
    def add_price_data(self, product_id: str, price: float, volume: int = 0, source: str = 'manual'):
        """Add new price data point"""
        query = """
        INSERT INTO price_history (product_id, price, volume, source, timestamp)
        VALUES (%s, %s, %s, %s, %s)
        """
        with psycopg2.connect(self.db_conn) as conn:
            with conn.cursor() as cur:
                cur.execute(query, (product_id, price, volume, source, datetime.now()))
                
    def get_historical_data(self, product_id: str, days: int = 30) -> pd.DataFrame:
        """Get historical price data for a product"""
        query = """
        SELECT product_id, price, volume, timestamp, source
        FROM price_history
        WHERE product_id = %s AND timestamp >= %s
        ORDER BY timestamp ASC
        """
        with psycopg2.connect(self.db_conn) as conn:
            df = pd.read_sql_query(query, conn, params=(product_id, datetime.now() - timedelta(days=days)))
        return df
        
    def add_product(self, product_id: str, name: str, category: str, current_price: float):
        """Add new product"""
        query = """
        INSERT INTO products (id, name, category, current_price, last_updated)
        VALUES (%s, %s, %s, %s, %s)
        ON CONFLICT (id) DO UPDATE SET
        name = EXCLUDED.name,
        category = EXCLUDED.category,
        current_price = EXCLUDED.current_price,
        last_updated = EXCLUDED.last_updated
        """
        with psycopg2.connect(self.db_conn) as conn:
            with conn.cursor() as cur:
                cur.execute(query, (product_id, name, category, current_price, datetime.now()))
```

### 2. Basic ML Models

#### LSTM Model Implementation
```python
# lstm_predictor.py
import numpy as np
import pandas as pd
from sklearn.preprocessing import MinMaxScaler
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import LSTM, Dense, Dropout
from tensorflow.keras.optimizers import Adam
import joblib
from typing import Tuple, Dict, List

class LSTMPredictor:
    def __init__(self, sequence_length: int = 10):
        self.sequence_length = sequence_length
        self.model = None
        self.scaler = MinMaxScaler()
        self.is_trained = False
        
    def prepare_data(self, df: pd.DataFrame) -> Tuple[np.ndarray, np.ndarray]:
        """Prepare data for LSTM training"""
        prices = df['price'].values.reshape(-1, 1)
        scaled_prices = self.scaler.fit_transform(prices)
        
        X, y = [], []
        for i in range(len(scaled_prices) - self.sequence_length):
            X.append(scaled_prices[i:i+self.sequence_length])
            y.append(scaled_prices[i+self.sequence_length])
            
        return np.array(X), np.array(y)
        
    def build_model(self, input_shape: Tuple[int, int]):
        """Build LSTM model architecture"""
        self.model = Sequential([
            LSTM(50, return_sequences=True, input_shape=input_shape),
            Dropout(0.2),
            LSTM(50, return_sequences=False),
            Dropout(0.2),
            Dense(25),
            Dense(1)
        ])
        
        self.model.compile(optimizer=Adam(learning_rate=0.001), loss='mse')
        
    def train(self, df: pd.DataFrame, epochs: int = 50, batch_size: int = 32):
        """Train LSTM model"""
        X, y = self.prepare_data(df)
        
        # Split data
        split_idx = int(0.8 * len(X))
        X_train, X_test = X[:split_idx], X[split_idx:]
        y_train, y_test = y[:split_idx], y[split_idx:]
        
        # Build and train model
        self.build_model((X_train.shape[1], X_train.shape[2]))
        history = self.model.fit(
            X_train, y_train,
            epochs=epochs,
            batch_size=batch_size,
            validation_data=(X_test, y_test),
            verbose=0
        )
        
        self.is_trained = True
        return history
        
    def predict(self, df: pd.DataFrame, days_ahead: int = 7) -> Dict:
        """Make price predictions"""
        if not self.is_trained:
            raise ValueError("Model must be trained before making predictions")
            
        # Get last sequence
        prices = df['price'].values.reshape(-1, 1)
        scaled_prices = self.scaler.transform(prices)
        last_sequence = scaled_prices[-self.sequence_length:]
        
        predictions = []
        current_sequence = last_sequence.copy()
        
        for _ in range(days_ahead):
            # Reshape for prediction
            X_pred = current_sequence.reshape(1, self.sequence_length, 1)
            pred_scaled = self.model.predict(X_pred, verbose=0)
            predictions.append(pred_scaled[0, 0])
            
            # Update sequence
            current_sequence = np.roll(current_sequence, -1)
            current_sequence[-1] = pred_scaled[0, 0]
            
        # Inverse transform predictions
        predictions = np.array(predictions).reshape(-1, 1)
        predictions_actual = self.scaler.inverse_transform(predictions)
        
        return {
            'predictions': predictions_actual.flatten().tolist(),
            'model_type': 'LSTM',
            'confidence': self._calculate_confidence(df)
        }
        
    def _calculate_confidence(self, df: pd.DataFrame) -> float:
        """Calculate prediction confidence based on historical volatility"""
        returns = df['price'].pct_change().dropna()
        volatility = returns.std()
        # Lower volatility = higher confidence
        confidence = max(0.5, min(0.95, 1.0 - volatility))
        return confidence
        
    def save_model(self, filepath: str):
        """Save trained model"""
        if self.model:
            self.model.save(filepath)
            joblib.dump(self.scaler, filepath.replace('.h5', '_scaler.pkl'))
            
    def load_model(self, filepath: str):
        """Load trained model"""
        from tensorflow.keras.models import load_model
        self.model = load_model(filepath)
        self.scaler = joblib.load(filepath.replace('.h5', '_scaler.pkl'))
        self.is_trained = True
```

#### ARIMA Model Implementation
```python
# arima_predictor.py
import pandas as pd
import numpy as np
from statsmodels.tsa.arima.model import ARIMA
from statsmodels.tsa.stattools import adfuller
from typing import Dict, Tuple
import warnings
warnings.filterwarnings('ignore')

class ARIMAPredictor:
    def __init__(self):
        self.model = None
        self.is_trained = False
        
    def check_stationarity(self, series: pd.Series) -> bool:
        """Check if time series is stationary"""
        result = adfuller(series.dropna())
        return result[1] <= 0.05  # p-value <= 0.05 means stationary
        
    def find_best_order(self, series: pd.Series) -> Tuple[int, int, int]:
        """Find best ARIMA order using simple grid search"""
        best_aic = float('inf')
        best_order = (1, 1, 1)
        
        for p in range(0, 3):
            for d in range(0, 2):
                for q in range(0, 3):
                    try:
                        model = ARIMA(series, order=(p, d, q))
                        fitted = model.fit()
                        if fitted.aic < best_aic:
                            best_aic = fitted.aic
                            best_order = (p, d, q)
                    except:
                        continue
                        
        return best_order
        
    def train(self, df: pd.DataFrame):
        """Train ARIMA model"""
        series = df['price']
        
        # Find best order
        order = self.find_best_order(series)
        
        # Fit model
        self.model = ARIMA(series, order=order)
        self.fitted_model = self.model.fit()
        self.is_trained = True
        
        return {
            'order': order,
            'aic': self.fitted_model.aic
        }
        
    def predict(self, df: pd.DataFrame, days_ahead: int = 7) -> Dict:
        """Make price predictions"""
        if not self.is_trained:
            raise ValueError("Model must be trained before making predictions")
            
        # Make predictions
        forecast = self.fitted_model.forecast(steps=days_ahead)
        confidence_intervals = self.fitted_model.get_forecast(steps=days_ahead).conf_int()
        
        return {
            'predictions': forecast.tolist(),
            'confidence_intervals': confidence_intervals.values.tolist(),
            'model_type': 'ARIMA',
            'confidence': 0.75  # Fixed confidence for ARIMA
        }
```

### 3. Simple Price Prediction API

#### FastAPI Implementation
```python
# prediction_api.py
from fastapi import FastAPI, HTTPException, Depends
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List, Dict, Optional
import psycopg2
from datetime import datetime, timedelta
import os

from data_collector import DataCollector
from lstm_predictor import LSTMPredictor
from arima_predictor import ARIMAPredictor

app = FastAPI(title="SPPG Price Prediction API", version="1.0.0")

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Database connection
DB_URL = os.getenv("DATABASE_URL", "mysql://user:password@localhost/sppg")

# Pydantic models
class PriceData(BaseModel):
    product_id: str
    price: float
    volume: int = 0
    source: str = 'manual'

class Product(BaseModel):
    id: str
    name: str
    category: str
    current_price: float

class PredictionRequest(BaseModel):
    product_id: str
    days_ahead: int = 7
    models: List[str] = ['LSTM', 'ARIMA']

class PredictionResponse(BaseModel):
    product_id: str
    predictions: Dict
    generated_at: datetime

# Dependency injection
def get_data_collector():
    return DataCollector(DB_URL)

# Initialize predictors
lstm_predictor = LSTMPredictor()
arima_predictor = ARIMAPredictor()

@app.post("/api/v1/prices")
async def add_price_data(price_data: PriceData, collector: DataCollector = Depends(get_data_collector)):
    """Add new price data"""
    try:
        collector.add_price_data(
            price_data.product_id,
            price_data.price,
            price_data.volume,
            price_data.source
        )
        return {"status": "success", "message": "Price data added successfully"}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/api/v1/products")
async def add_product(product: Product, collector: DataCollector = Depends(get_data_collector)):
    """Add new product"""
    try:
        collector.add_product(
            product.id,
            product.name,
            product.category,
            product.current_price
        )
        return {"status": "success", "message": "Product added successfully"}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/api/v1/products/{product_id}/history")
async def get_price_history(product_id: str, days: int = 30, collector: DataCollector = Depends(get_data_collector)):
    """Get price history for a product"""
    try:
        df = collector.get_historical_data(product_id, days)
        if df.empty:
            raise HTTPException(status_code=404, detail="No data found for product")
        
        return {
            "product_id": product_id,
            "data_points": len(df),
            "data": df.to_dict('records')
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/api/v1/predict", response_model=PredictionResponse)
async def predict_prices(request: PredictionRequest, collector: DataCollector = Depends(get_data_collector)):
    """Predict prices for a product"""
    try:
        # Get historical data
        df = collector.get_historical_data(request.product_id, 60)  # Get 60 days for training
        
        if df.empty or len(df) < 20:
            raise HTTPException(status_code=400, detail="Insufficient data for prediction")
        
        predictions = {}
        
        # LSTM prediction
        if 'LSTM' in request.models:
            if not lstm_predictor.is_trained:
                lstm_predictor.train(df)
            lstm_pred = lstm_predictor.predict(df, request.days_ahead)
            predictions['LSTM'] = lstm_pred
        
        # ARIMA prediction
        if 'ARIMA' in request.models:
            if not arima_predictor.is_trained:
                arima_predictor.train(df)
            arima_pred = arima_predictor.predict(df, request.days_ahead)
            predictions['ARIMA'] = arima_pred
        
        return PredictionResponse(
            product_id=request.product_id,
            predictions=predictions,
            generated_at=datetime.now()
        )
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/api/v1/models")
async def get_available_models():
    """Get list of available prediction models"""
    return {
        "models": [
            {"name": "LSTM", "type": "Deep Learning", "status": "available"},
            {"name": "ARIMA", "type": "Statistical", "status": "available"}
        ]
    }

@app.get("/api/v1/health")
async def health_check():
    """Health check endpoint"""
    return {"status": "healthy", "timestamp": datetime.now()}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
```

### 4. Basic Profit Calculator

#### Profit Calculation Module
```python
# profit_calculator.py
import pandas as pd
import numpy as np
from typing import Dict, List, Tuple
from datetime import datetime, timedelta

class ProfitCalculator:
    def __init__(self, transaction_fee: float = 0.001):
        self.transaction_fee = transaction_fee  # 0.1% transaction fee
        
    def calculate_simple_profit(self, buy_price: float, sell_price: float, quantity: int) -> Dict:
        """Calculate simple profit for a single transaction"""
        buy_cost = buy_price * quantity
        sell_revenue = sell_price * quantity
        transaction_costs = (buy_cost + sell_revenue) * self.transaction_fee
        
        gross_profit = sell_revenue - buy_cost
        net_profit = gross_profit - transaction_costs
        profit_percentage = (net_profit / buy_cost) * 100 if buy_cost > 0 else 0
        
        return {
            'buy_price': buy_price,
            'sell_price': sell_price,
            'quantity': quantity,
            'buy_cost': buy_cost,
            'sell_revenue': sell_revenue,
            'transaction_costs': transaction_costs,
            'gross_profit': gross_profit,
            'net_profit': net_profit,
            'profit_percentage': profit_percentage
        }
        
    def calculate_portfolio_profit(self, transactions: List[Dict]) -> Dict:
        """Calculate profit for multiple transactions"""
        portfolio_value = 0
        total_invested = 0
        total_costs = 0
        positions = {}
        
        for transaction in transactions:
            product_id = transaction['product_id']
            price = transaction['price']
            quantity = transaction['quantity']
            transaction_type = transaction['type']  # 'buy' or 'sell'
            
            if transaction_type == 'buy':
                cost = price * quantity * (1 + self.transaction_fee)
                total_invested += cost
                total_costs += price * quantity * self.transaction_fee
                
                if product_id in positions:
                    positions[product_id]['quantity'] += quantity
                    positions[product_id]['avg_price'] = (
                        (positions[product_id]['avg_price'] * positions[product_id]['quantity_before'] + price * quantity) /
                        positions[product_id]['quantity']
                    )
                else:
                    positions[product_id] = {
                        'quantity': quantity,
                        'avg_price': price,
                        'quantity_before': 0
                    }
                positions[product_id]['quantity_before'] = positions[product_id]['quantity']
                
            elif transaction_type == 'sell':
                if product_id in positions and positions[product_id]['quantity'] >= quantity:
                    revenue = price * quantity * (1 - self.transaction_fee)
                    portfolio_value += revenue
                    total_costs += price * quantity * self.transaction_fee
                    positions[product_id]['quantity'] -= quantity
        
        # Calculate unrealized profit for remaining positions
        unrealized_profit = 0
        for product_id, position in positions.items():
            if position['quantity'] > 0:
                # This would need current market price
                # For now, use average price as placeholder
                current_value = position['avg_price'] * position['quantity']
                cost_basis = position['avg_price'] * position['quantity']
                unrealized_profit += current_value - cost_basis
        
        total_profit = portfolio_value - total_invested + unrealized_profit
        profit_percentage = (total_profit / total_invested * 100) if total_invested > 0 else 0
        
        return {
            'total_invested': total_invested,
            'portfolio_value': portfolio_value,
            'total_costs': total_costs,
            'unrealized_profit': unrealized_profit,
            'total_profit': total_profit,
            'profit_percentage': profit_percentage,
            'positions': positions
        }
        
    def calculate_risk_metrics(self, price_history: pd.DataFrame) -> Dict:
        """Calculate basic risk metrics"""
        if len(price_history) < 2:
            return {'volatility': 0, 'max_drawdown': 0, 'sharpe_ratio': 0}
        
        # Calculate daily returns
        price_history['returns'] = price_history['price'].pct_change()
        
        # Volatility (annualized)
        volatility = price_history['returns'].std() * np.sqrt(252)
        
        # Maximum drawdown
        price_history['cummax'] = price_history['price'].cummax()
        price_history['drawdown'] = (price_history['price'] - price_history['cummax']) / price_history['cummax']
        max_drawdown = price_history['drawdown'].min()
        
        # Sharpe ratio (simplified)
        mean_return = price_history['returns'].mean()
        sharpe_ratio = (mean_return * 252) / volatility if volatility > 0 else 0
        
        return {
            'volatility': volatility,
            'max_drawdown': max_drawdown,
            'sharpe_ratio': sharpe_ratio,
            'var_95': price_history['returns'].quantile(0.05)  # Value at Risk
        }
```

### 5. Manual Trading Interface

#### Frontend Bootstrap Components
```php
// resources/views/trading/interface.blade.php
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Trading Interface</h5>
                </div>
                <div class="card-body">
                    <div id="trading-app">
                        <!-- Trading interface will be mounted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Trading interface with jQuery and Alpine.js
document.addEventListener('alpine:init', () => {
    Alpine.data('tradingInterface', () => ({
        products: [],
        selectedProduct: null,
        loading: false,
        
        init() {
            this.loadProducts();
        },
        
        async loadProducts() {
            this.loading = true;
            try {
                const response = await fetch('/api/products');
                this.products = await response.json();
            } catch (error) {
                console.error('Error loading products:', error);
            } finally {
                this.loading = false;
            }
        },
        
        selectProduct(product) {
            this.selectedProduct = product;
        }
    }));
});
</script>
@endsection
```
  const [products, setProducts] = useState([]);
  const [selectedProduct, setSelectedProduct] = useState('');
  const [priceHistory, setPriceHistory] = useState([]);
  const [predictions, setPredictions] = useState({});
  const [tradeForm, setTradeForm] = useState({
    type: 'buy',
    quantity: 1,
    price: 0
  });
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    fetchProducts();
  }, []);

  const fetchProducts = async () => {
    try {
      // Mock data - replace with actual API call
      const mockProducts = [
        { id: 'PROD001', name: 'Beras', category: 'Makanan', current_price: 15000 },
        { id: 'PROD002', name: 'Minyak Goreng', category: 'Makanan', current_price: 25000 },
        { id: 'PROD003', name: 'Gula', category: 'Makanan', current_price: 18000 }
      ];
      setProducts(mockProducts);
    } catch (error) {
      console.error('Error fetching products:', error);
    }
  };

  const fetchPriceHistory = async (productId) => {
    setLoading(true);
    try {
      const response = await axios.get(`/api/v1/products/${productId}/history`);
      setPriceHistory(response.data.data);
    } catch (error) {
      console.error('Error fetching price history:', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchPredictions = async (productId) => {
    setLoading(true);
    try {
      const response = await axios.post('/api/v1/predict', {
        product_id: productId,
        days_ahead: 7,
        models: ['LSTM', 'ARIMA']
      });
      setPredictions(response.data.predictions);
    } catch (error) {
      console.error('Error fetching predictions:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleProductChange = (productId) => {
    setSelectedProduct(productId);
    fetchPriceHistory(productId);
    fetchPredictions(productId);
    
    const product = products.find(p => p.id === productId);
    if (product) {
      setTradeForm(prev => ({ ...prev, price: product.current_price }));
    }
  };

  const handleTrade = async () => {
    try {
      const tradeData = {
        product_id: selectedProduct,
        type: tradeForm.type,
        quantity: tradeForm.quantity,
        price: tradeForm.price
      };
      
      // Mock API call - replace with actual implementation
      console.log('Executing trade:', tradeData);
      alert('Trade executed successfully!');
      
    } catch (error) {
      console.error('Error executing trade:', error);
      alert('Error executing trade');
    }
  };

  const renderPredictionChart = () => {
    if (!predictions || Object.keys(predictions).length === 0) return null;

    return (
      <div className="bg-white p-4 rounded-lg shadow">
        <h3 className="text-lg font-semibold mb-4">Price Predictions</h3>
        {Object.entries(predictions).map(([model, data]) => (
          <div key={model} className="mb-4">
            <h4 className="font-medium">{model} Model</h4>
            <p className="text-sm text-gray-600">
              Confidence: {(data.confidence * 100).toFixed(1)}%
            </p>
            <div className="mt-2">
              {data.predictions.slice(0, 5).map((price, idx) => (
                <div key={idx} className="flex justify-between text-sm">
                  <span>Day {idx + 1}:</span>
                  <span>Rp {price.toFixed(2)}</span>
                </div>
              ))}
            </div>
          </div>
        ))}
      </div>
    );
  };

  return (
    <div className="min-h-screen bg-gray-100 p-4">
      <div className="max-w-6xl mx-auto">
        <h1 className="text-2xl font-bold mb-6">SPPG Trading Interface</h1>
        
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* Product Selection and Trading Form */}
          <div className="bg-white p-6 rounded-lg shadow">
            <h2 className="text-xl font-semibold mb-4">Trading</h2>
            
            <div className="mb-4">
              <label className="block text-sm font-medium mb-2">Product</label>
              <select
                value={selectedProduct}
                onChange={(e) => handleProductChange(e.target.value)}
                className="w-full p-2 border rounded"
              >
                <option value="">Select a product</option>
                {products.map(product => (
                  <option key={product.id} value={product.id}>
                    {product.name} - Rp {product.current_price}
                  </option>
                ))}
              </select>
            </div>

            <div className="mb-4">
              <label className="block text-sm font-medium mb-2">Trade Type</label>
              <div className="flex space-x-4">
                <label>
                  <input
                    type="radio"
                    value="buy"
                    checked={tradeForm.type === 'buy'}
                    onChange={(e) => setTradeForm(prev => ({ ...prev, type: e.target.value }))}
                  />
                  Buy
                </label>
                <label>
                  <input
                    type="radio"
                    value="sell"
                    checked={tradeForm.type === 'sell'}
                    onChange={(e) => setTradeForm(prev => ({ ...prev, type: e.target.value }))}
                  />
                  Sell
                </label>
              </div>
            </div>

            <div className="mb-4">
              <label className="block text-sm font-medium mb-2">Quantity</label>
              <input
                type="number"
                min="1"
                value={tradeForm.quantity}
                onChange={(e) => setTradeForm(prev => ({ ...prev, quantity: parseInt(e.target.value) }))}
                className="w-full p-2 border rounded"
              />
            </div>

            <div className="mb-4">
              <label className="block text-sm font-medium mb-2">Price</label>
              <input
                type="number"
                step="0.01"
                value={tradeForm.price}
                onChange={(e) => setTradeForm(prev => ({ ...prev, price: parseFloat(e.target.value) }))}
                className="w-full p-2 border rounded"
              />
            </div>

            <button
              onClick={handleTrade}
              disabled={!selectedProduct || loading}
              className="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600 disabled:bg-gray-300"
            >
              {loading ? 'Processing...' : `Execute ${tradeForm.type}`}
            </button>
          </div>

          {/* Predictions */}
          <div>
            {renderPredictionChart()}
          </div>
        </div>

        {/* Price History */}
        {priceHistory.length > 0 && (
          <div className="mt-6 bg-white p-6 rounded-lg shadow">
            <h2 className="text-xl font-semibold mb-4">Price History</h2>
            <div className="overflow-x-auto">
              <table className="min-w-full">
                <thead>
                  <tr>
                    <th className="px-4 py-2 text-left">Date</th>
                    <th className="px-4 py-2 text-left">Price</th>
                    <th className="px-4 py-2 text-left">Volume</th>
                    <th className="px-4 py-2 text-left">Source</th>
                  </tr>
                </thead>
                <tbody>
                  {priceHistory.slice(-10).reverse().map((record, idx) => (
                    <tr key={idx}>
                      <td className="px-4 py-2">
                        {new Date(record.timestamp).toLocaleDateString()}
                      </td>
                      <td className="px-4 py-2">Rp {record.price.toFixed(2)}</td>
                      <td className="px-4 py-2">{record.volume || '-'}</td>
                      <td className="px-4 py-2">{record.source}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default TradingInterface;
```

## Installation and Setup

### Requirements
```bash
# Backend requirements
pip install fastapi uvicorn pymysql pandas numpy scikit-learn tensorflow statsmodels

# Frontend requirements (included in Laravel)
# jQuery, Bootstrap, Alpine.js are included via CDN/Laravel Mix
```

### Database Setup
```bash
# Create database
createdb sppg

# Run schema
psql sppg < schema.sql
```

### Running the Application
```bash
# Start backend server
python prediction_api.py

# Start frontend (in separate terminal)
npm start
```

## Testing

### Unit Tests
```python
# test_predictors.py
import pytest
import pandas as pd
import numpy as np
from lstm_predictor import LSTMPredictor
from arima_predictor import ARIMAPredictor

def test_lstm_predictor():
    # Create sample data
    dates = pd.date_range('2023-01-01', periods=100)
    prices = np.random.normal(100, 10, 100)
    df = pd.DataFrame({'timestamp': dates, 'price': prices})
    
    predictor = LSTMPredictor(sequence_length=10)
    
    # Test training
    history = predictor.train(df, epochs=1)
    assert predictor.is_trained
    
    # Test prediction
    predictions = predictor.predict(df, days_ahead=5)
    assert 'predictions' in predictions
    assert len(predictions['predictions']) == 5

def test_arima_predictor():
    # Create sample data
    dates = pd.date_range('2023-01-01', periods=100)
    prices = np.random.normal(100, 10, 100)
    df = pd.DataFrame({'timestamp': dates, 'price': prices})
    
    predictor = ARIMAPredictor()
    
    # Test training
    result = predictor.train(df)
    assert predictor.is_trained
    assert 'order' in result
    
    # Test prediction
    predictions = predictor.predict(df, days_ahead=5)
    assert 'predictions' in predictions
    assert len(predictions['predictions']) == 5
```

## Next Steps

1. **Enhance Data Collection**: Add automated data sources
2. **Improve Models**: Add more sophisticated algorithms
3. **Real-time Features**: Implement WebSocket for live updates
4. **Risk Management**: Add advanced risk controls
5. **User Authentication**: Add user accounts and permissions

## Success Metrics

- **Prediction Accuracy**: Target >70% for Phase 1
- **API Response Time**: <500ms
- **System Uptime**: >95%
- **User Interface**: Intuitive and responsive design
