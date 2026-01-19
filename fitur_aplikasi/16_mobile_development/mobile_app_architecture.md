# Mobile App Architecture

## **📱 Architecture Overview**

### **📊 Tujuan:**
- **Cross-platform** - Satu codebase untuk iOS & Android
- **Performance** - Aplikasi yang cepat dan responsif
- **Maintainability** - Code yang mudah di-maintain
- **Scalability** - Dapat menangani growth
- **Integration** - Seamless dengan backend yang ada

---

## **🏗️ Technology Stack**

### **📱 Mobile Framework:**
- **React Native** - Cross-platform framework
- **TypeScript** - Type safety dan developer experience
- **Expo** - Build tool dan deployment
- **Flipper** - Beta deployment platform

### **🔧 Backend Integration:**
- **RESTful API** - Existing backend API
- **WebSocket** - Real-time communication
- **JWT Authentication** - Single sign-on
- **GraphQL** - Efficient data fetching

### **📱 Storage & State:**
- **Redux Toolkit** - State management
- **AsyncStorage** - Local data persistence
- **SQLite** - Local database untuk mobile
- **MMKV** - Key-value storage

### **📱 Development Tools:**
- **Metro bundler** - Build optimization
- **Fastlane** - Automated testing
- **Flipper** - Beta deployment
- **Sentry** - Error tracking

---

## **📱 Architecture Layers**

### **1. Presentation Layer**
```typescript
// ✅ React Native presentation layer
import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet } from 'react-native';

interface AppState {
  user: User | null;
  purchases: Purchase[];
  products: Product[];
  isLoading: boolean;
  isOnline: boolean;
}

const App: React.FC = () => {
  const [state, setState] = useState<AppState>({
    user: null,
    purchases: [],
    products: [],
    isLoading: false,
    isOnline: true
  });
  
  // Network status monitoring
  useEffect(() => {
    const subscription = NetInfo.addEventListener('change', (state) => {
      setState(prev => ({ ...prev, isOnline: state.isConnected }));
    });
    
    return () => subscription?.remove();
  }, []);
  
  // Data synchronization
  useEffect(() => {
    if (state.isOnline) {
      syncData();
    }
  }, [state.isOnline]);
  
  return (
    <View style={styles.container}>
      <Header user={state.user} />
      <Navigation />
      <MainContent />
    </View>
  );
};
```

### **2. Business Logic Layer**
```typescript
// ✅ Business logic abstraction
interface BusinessLogicInterface {
  getPurchase(id: number): Promise<Purchase>;
  createPurchase(data: CreatePurchaseData): Promise<Purchase>;
  updatePurchase(id: number, data: UpdatePurchaseData): Promise<Purchase>;
  deletePurchase(id: Purchase['id']): Promise<void>;
}

class PurchaseService implements BusinessLogicInterface {
  constructor(private apiService: ApiService) {}
  
  async getPurchase(id: number): Promise<Purchase> {
    try {
      const response = await this.apiService.get(`/api/purchases/${id}`);
      return response.data;
    } catch (error) {
      throw new Error(`Failed to get purchase: ${error.message}`);
    }
  }
  
  async createPurchase(data: CreatePurchaseData): Promise<Purchase> {
    try {
      const response = await this.apiService.post('/api/purchases', data);
      return response.data;
    } catch (error) {
      throw new Error(`Failed to create purchase: ${error.message}`);
    }
  }
  
  async updatePurchase(id: number, data: UpdatePurchaseData): Promise<Purchase> {
    try {
      const response = await this.apiService.put(`/api/purchases/${id}`, data);
      return response.data;
    } catch (error) {
      throw new Error(`Failed to update purchase: ${error.message}`);
    }
  }
  
  async deletePurchase(id: Purchase['id']): Promise<void> {
    try {
      await this.apiService.delete(`/api/purchases/${id}`);
    } catch (error) {
      throw new Error(`Failed to delete purchase: ${error.message}`);
    }
  }
}
```

### **3. Data Layer**
```typescript
// ✅ Data access layer
interface DataAccessInterface {
  getLocalData(key: string): Promise<any>;
  setLocalData(key: string, data: any): Promise<void>;
  clearLocalData(key: string): Promise<void>;
  syncData(): Promise<void>;
}

class DataAccess implements DataAccessInterface {
  constructor(private storage: AsyncStorage) {}
  
  async getLocalData(key: string): Promise<any> {
    try {
      const data = await this.storage.getItem(key);
      return data ? JSON.parse(data) : null;
    } catch (error) {
      console.error('Failed to get local data:', error);
      return null;
    }
  }
  
  async setLocalData(key: string, data: any): Promise<void> {
    try {
      await this.storage.setItem(key, JSON.stringify(data));
    } catch (error) {
      console.error('Failed to set local data:', error);
    }
  }
  
  async clearLocalData(key: string): Promise<void> {
    try {
      await this.storage.removeItem(key);
    } catch (error) {
      console.error('Failed to clear local data:', error);
    }
  }
  
  async syncData(): Promise<void> {
    // Get all local data
    const purchases = await this.getLocalData('purchases');
    const products = await this.getLocalData('products');
    
    // Sync with backend
    try {
      const backendData = await this.fetchBackendData();
      
      // Merge local and backend data
      const mergedPurchases = this.mergeData(purchases, backendData.purchases);
      const mergedProducts = this.mergeData(products, backendData.products);
      
      // Update local storage
      await this.setLocalData('purchases', mergedPurchases);
      await this.setLocalData('products', mergedProducts);
      
      // Notify listeners
      this.notifyDataChange('sync_completed');
    } catch (error) {
      console.error('Failed to sync data:', error);
      // Queue for retry
      this.queueSync();
    }
  }
}
```

---

## **📱 Component Architecture**

### **1. Component Structure**
```typescript
// ✅ Component hierarchy
src/
├── components/
│   ├── common/
│   │   ├── Button/
│   │   ├── Input/
│   │   ├── Loading/
│   │   └── ErrorBoundary/
│   ├── screens/
│   │   ├── HomeScreen/
│   │   ├── PurchaseScreen/
│   │   ├── ProductScreen/
│   │   └── ReportScreen/
│   └── features/
│       ├── Purchase/
│       ├── Inventory/
│       └── Customer/
├── services/
│   ├── ApiService/
│   ├── PurchaseService/
│   ├── ProductService/
│   └── CustomerService/
├── utils/
│   ├── Validation/
│   ├── Formatting/
│   └── Storage/
└── types/
    ├── User.ts
    ├── Purchase.ts
    ├── Product.ts
    └── Customer.ts
```

### **2. Component Example**
```typescript
// ✅ Purchase screen component
import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity } from 'react-native';
import { PurchaseService } from '../services/PurchaseService';
import { LoadingIndicator } from '../components/common/LoadingIndicator';
import { ErrorBoundary } from '../components/common/ErrorBoundary';

interface PurchaseScreenState {
  purchases: Purchase[];
  isLoading: boolean;
  error: string | null;
}

const PurchaseScreen: React.FC = () => {
  const [state, setState] = useState<PurchaseScreenState>({
    purchases: [],
    isLoading: false,
    error: null
  });
  
  const purchaseService = new PurchaseService();
  
  useEffect(() => {
    loadPurchases();
  }, []);
  
  const loadPurchases = async () => {
    try {
      setState(prev => ({ ...prev, isLoading: true }));
      
      const purchases = await purchaseService.getPurchases();
      
      setState(prev => ({
        ...prev,
        purchases,
        isLoading: false,
        error: null
      }));
    } catch (error) {
      setState(prev => ({
        ...prev,
        isLoading: false,
        error: error.message
      }));
    }
  };
  
  const handleCreatePurchase = async (purchaseData: CreatePurchaseData) => {
    try {
      setState(prev => ({ ...prev, isLoading: true }));
      
      const purchase = await purchaseService.createPurchase(purchaseData);
      
      setState(prev => ({
        ...prev,
        purchases: [...prev.purchases, purchase],
        isLoading: false
      }));
      
      // Navigate to purchase detail
      navigation.navigate('PurchaseDetail', { purchaseId: purchase.id });
    } catch (error) {
      setState(prev => ({
        ...prev,
        isLoading: false,
        error: error.message
      }));
    }
  };
  
  return (
    <ErrorBoundary>
      <View style={styles.container}>
        <Text style={styles.title}>Pembelian</Text>
        
        {state.error && (
          <View style={styles.errorContainer}>
            <Text style={styles.errorText}>{state.error}</Text>
          </View>
        )}
        
        {state.isLoading ? (
          <LoadingIndicator />
        ) : (
          <FlatList
            data={state.purchases}
            keyExtractor={(item) => item.id.toString()}
            renderItem={({ item }) => (
              <View style={styles.purchaseItem}>
                <Text style={styles.purchaseNumber}>{item.invoice_no}</Text>
                <Text style={styles.supplierName}>{item.supplier_name}</Text>
                <Text style={styles.totalAmount}>
                  Rp {item.total_amount.toLocaleString('id-ID')}
                </Text>
                <Text style={styles.date}>
                  {new Date(item.purchase_date).toLocaleDateString('id-ID')}
                </Text>
              </View>
            )}
            ListHeaderComponent />
            <ActionButton
              title="Tambah Pembelian"
              onPress={() => navigation.navigate('CreatePurchase')}
              style={styles.actionButton}
            />
          </FlatList>
        )}
      </View>
    </ErrorBoundary>
  );
};
```

---

## **📱 State Management**

### **1. Redux Store Configuration**
```typescript
// ✅ Redux store configuration
import { configureStore } from '@reduxjs/toolkit';
import { persistStore } from 'redux-persist';
import { combineReducers } from '@reduxjs/toolkit';
import AsyncStorage from '@react-native-async-storage/async-storage';

// Reducers
import purchasesReducer from './reducers/purchasesReducer';
import productsReducer from './reducers/productsReducer';
import userReducer from './reducers/userReducer';

// Store configuration
const store = configureStore({
  reducer: combineReducers({
    purchases: purchasesReducer,
    products: productsReducer,
    user: userReducer
  }),
  middleware: [
    persistStore({
      key: 'root',
      storage: AsyncStorage,
      whitelist: ['purchases', 'products', 'user']
    })
  ]
});

export default store;
```

### **2. State Management Pattern**
```typescript
// ✅ State management pattern
interface StoreState {
  user: User | null;
  purchases: Purchase[];
  products: Product[];
  settings: AppSettings;
  networkStatus: NetworkStatus;
}

class StoreManager {
  private store: AppStore;
  
  constructor() {
    this.store = store;
  }
  
  // Getters
  getUser(): User | null {
    return this.store.getState().user;
  }
  
  getPurchases(): Purchase[] {
    return this.store.getState().purchases;
  }
  
  getProducts(): Product[] {
    return this.store.getState().products;
  }
  
  // Actions
  setUserData(user: User): void {
    this.store.dispatch({ type: 'SET_USER', payload: user });
  }
  
  setPurchases(purchases: Purchase[]): void {
    this.store.dispatch({ type: 'SET_PURCHASES', payload: purchases });
  }
  
  setProducts(products: Product[]): void {
    this.store.dispatch({ type: 'SET_PRODUCTS', payload: products });
  }
  
  // Async actions
  async loadPurchases(): Promise<void> {
    try {
      const purchases = await purchaseService.getPurchases();
      this.setPurchases(purchases);
    } catch (error) {
      console.error('Failed to load purchases:', error);
    }
  }
}
```

---

## **📱 Network Layer**

### **1. API Service**
```typescript
// ✅ API service for network operations
class ApiService {
  private baseURL: string;
  private timeout: number;
  
  constructor(baseURL: string, timeout: number = 10000) {
    this.baseURL = baseURL;
    this.timeout = timeout;
  }
  
  async request<T>(endpoint: string, options: RequestOptions = {}): Promise<ApiResponse<T>> {
    const url = `${this.baseURL}${endpoint}`;
    const config: RequestConfig = {
      timeout: this.timeout,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': this.getCSRFToken(),
        'Authorization': `Bearer ${this.getAuthToken()}`,
        ...options.headers
      },
      ...options
    };
      
      try {
        const response = await fetch(url, config);
        
        if (!response.ok) {
          throw new Error(`HTTP error: ${response.status}`);
        }
        
        const data = await response.json();
        
        return {
          success: true,
          data: data,
          message: data.message || 'Success',
          status: response.status
        };
      } catch (error) {
        throw new Error(`Network error: ${error.message}`);
      }
    }
    
    private getCSRFToken(): string {
      return 'csrf_token_' + Date.now();
    }
    
    private getAuthToken(): string {
      return this.storage.getItem('auth_token');
    }
}
```

### **2. Network Monitoring**
```typescript
// ✅ Network status monitoring
class NetworkMonitor {
  private listeners: NetworkListener[] = [];
  
  addListener(listener: NetworkListener): void {
    this.listeners.push(listener);
  }
  
  removeListener(listener: NetworkListener): void {
    this.listeners = this.listeners.filter(l => l !== listener);
  }
  
  notifyListeners(status: NetworkStatus): void {
    this.listeners.forEach(listener => {
      listener(status);
    });
  }
  
  checkNetworkStatus(): NetworkStatus {
    return NetInfo.getConnectionInfo();
  }
}
```

---

## **📱 Offline Support**

### **1. Offline Queue System**
```typescript
// ✅ Offline queue for actions
class OfflineQueue {
  private queue: OfflineAction[] = [];
  private isProcessing: boolean = false;
  
  constructor(private dataAccess: DataAccess) {}
  
  enqueue(action: OfflineAction): void {
    this.queue.push(action);
    this.processQueue();
  }
  
  async processQueue(): Promise<void> {
    if (this.isProcessing || this.queue.length === 0) {
      return;
    }
    
    this.isProcessing = true;
    
    while (this.queue.length > 0 && this.isOnline()) {
      const action = this.queue.shift();
      
      try {
        await this.executeAction(action);
        await this.dataAccess.removeLocalData(action.id);
      } catch (error) {
        console.error('Failed to execute offline action:', error);
        // Re-queue action
        this.queue.unshift(action);
      }
    }
    
    this.isProcessing = false;
  }
  
  private isOnline(): boolean {
    return NetInfo.isConnected;
  }
  
  private async executeAction(action: OfflineAction): Promise<any> {
    switch (action.type) {
      case 'CREATE_PURCHASE':
        return this.createPurchaseOffline(action.data);
      case 'UPDATE_PURCHASE':
        return this.updatePurchaseOffline(action.id, action.data);
      case 'DELETE_PURCHASE':
        return this.deletePurchaseOffline(action.id);
      default:
        throw new Error(`Unknown action type: ${action.type}`);
    }
  }
}
```

### **2. Sync Strategy**
```typescript
// ✅ Data synchronization strategy
class SyncManager {
  constructor(
    private dataAccess: DataAccess,
    private apiService: ApiService,
    private networkMonitor: NetworkMonitor
  ) {}
  
  async syncData(): Promise<SyncResult> {
    try {
      // Get local data
      const localPurchases = await this.dataAccess.getLocalData('purchases');
      
      // Get backend data
      const backendPurchases = await this.apiService.getPurchases();
      
      // Merge data
      const mergedPurchases = this.mergeData(localPurchases, backendPurchases);
      
      // Update local storage
      await this.dataAccess.setLocalData('purchases', mergedPurchases);
      
      // Update backend with local changes
      await this.syncLocalChangesToBackend();
      
      return {
        success: true,
        synced_count: count($backendPurchases),
        merged_count: count($mergedPurchases),
        conflicts: this.identifyConflicts(localPurchases, backendPurchases)
      };
    } catch (error) {
      return {
        success: false,
        error: error.message,
        queued_for_retry: true
      };
    }
  }
  
  private mergeData(local: any[], backend: any[]): any[] {
    const merged = [...local];
    const backendMap = new Map(backend.map(item => [item.id, item]));
    
    // Add local items not in backend
    local.forEach(localItem => {
      if (!backendMap.has(localItem.id)) {
        merged.push({
          ...localItem,
          sync_status: 'local_only'
        });
      }
    });
    
    return merged;
  }
}
```

---

## **📱 Performance Optimization**

### **1. Image Optimization**
```typescript
// ✅ Image optimization for mobile
class ImageOptimizer {
  static optimizeImage(imageUri: string): string {
    // Resize for mobile
    return `${imageUri}?w=800&h=600&f=webp`;
  }
  
  static cacheImage(imageUri: string): string {
    // Cache optimized image
    return imageUri;
  }
  
  static preloadImages(imageUris: string[]): void {
    // Preload critical images
    imageUris.forEach(uri => {
      Image.prefetch(uri);
    });
  }
}
```

### **2. Memory Management**
```typescript
// ✅ Memory management for mobile
class MemoryManager {
  private static MAX_MEMORY_USAGE = 100 * 1024 * 1024; // 100MB
  
  static checkMemoryUsage(): boolean {
    return (
      typeof performance !== 'undefined' &&
      performance.memory && 
      performance.memory.usedJSHeapSize < this.MAX_MEMORY_USAGE
    );
  }
  
  static optimizeMemory(): void {
    // Clear unused data
    if (!this.checkMemoryUsage()) {
      // Clear cache
      this.clearCache();
      
      // Force garbage collection
      if (typeof gc !== 'undefined') {
        gc();
      }
    }
  }
  
  private static clearCache(): void {
    // Clear image cache
    // Clear unused components
    // Clear unused data
  }
}
```

---

## **📱 Testing Strategy**

### **1. Unit Testing**
```typescript
// ✅ Unit testing with Jest
import { render, fireEvent, act } from '@testing-library/react-native';
import { PurchaseService } from '../services/PurchaseService';

describe('PurchaseService', () => {
  let purchaseService: PurchaseService;
  
  beforeEach(() => {
    purchaseService = new PurchaseService();
  });
  
  test('should create purchase successfully', async () => {
    const purchaseData = {
      supplier_id: 1,
      items: [
        {
          product_id: 1,
          quantity: 10,
          price: 50000
        }
      ]
    };
    
    const result = await purchaseService.createPurchase(purchaseData);
    
    expect(result.success).toBe(true);
    expect(result.data).toHaveProperty('id');
    expect(result.data.supplier_id).toBe(1);
  });
  
  test('should handle API error gracefully', async () => {
    const purchaseData = {
      supplier_id: 999, // Invalid supplier
      items: []
    };
    
    await expect(purchaseService.createPurchase(purchaseData))
      .rejects.toThrow('Invalid supplier ID');
  });
});
```

### **2. Integration Testing**
```typescript
// ✅ Integration testing with Detox
import { render, fireEvent, act } from '@testing-library/react-native';
import { Provider } from 'react-redux';
import { store } from '../store';
import { PurchaseScreen } from '../screens/PurchaseScreen';

describe('Purchase Integration', () => {
  let wrapper: any;
  
  beforeEach(() => {
    wrapper = render(
      <Provider store={store}>
        <PurchaseScreen />
      </Provider>
    );
  });
  
  test('should load purchases from API', async () => {
    // Mock API responses
    jest.mock('../services/PurchaseService').mockResolvedValue({
      success: true,
      data: [
        {
        id: 1,
        supplier_name: 'Test Supplier',
        total_amount: 500000
      }
      ]
    });
    
    // Trigger data load
    const { getByTestId } = wrapper.getByTestId('purchase-list');
    
    // Wait for data to load
    await waitFor(() => {
      const items = getByTestId('purchase-item');
      return items.length > 0;
    }, { timeout: 5000 });
    
    // Verify data loaded
    const items = getByTestId('purchase-item');
    expect(items).toHaveLength(1);
    expect(items[0].textContent).toContain('Test Supplier');
  });
});
```

---

## **📱 Deployment Strategy**

### **1. Build Configuration**
```javascript
// ✅ Metro bundler configuration
module.exports = {
  presets: ['module:android', 'module:ios'],
  projects: [
    {
      name: 'mobile',
      entry: './index.js',
      output: 'index.js',
      bundle: 'index.js',
      assets: './assets',
      plugins: [
        ['react-native-reanimated'],
        ['@react-native-async-storage'],
        ['@react-native-mmkdocs']
      ]
    }
  ]
};
```

### **2. Environment Configuration**
```javascript
// ✅ Environment configuration
const ENV = {
  API_BASE_URL: process.env.API_BASE_URL || 'http://localhost:3000/api',
  ENVIRONMENT: process.env.NODE_ENV || 'development',
  API_TIMEOUT: process.env.API_TIMEOUT || 10000,
  LOG_LEVEL: process.env.LOG_LEVEL || 'info'
};
```

---

## **📊 Success Metrics**

### **📈 Performance Metrics:**
- **App startup time:** < 3 detik
- **Memory usage:** < 100MB peak
- **Network efficiency:** < 50% bandwidth usage
- **Battery impact:** < 10% battery impact

### **📱 User Experience Metrics:**
- **Load time:** < 2 detik
- **Touch response:** < 100ms
- **Offline functionality:** 100% core features
- **Push notification delivery:** > 95%
- **User satisfaction:** > 90%

### **📊 Development Metrics:**
- **Code coverage:** > 80%
- **Test coverage:** > 75%
- **Build time:** < 10 menit
- **Deployment success:** > 95%
- **Bug rate:** < 1% of users

---

**Status:** ✅ **Mobile app architecture selesai - Ready for implementation**

**Priority:** High - Essential untuk mobile experience enhancement
