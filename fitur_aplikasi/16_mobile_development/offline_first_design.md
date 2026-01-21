# Offline-First Design

## **📱 Offline-First Architecture**

### **📊 Tujuan:**
- **Core functionality** tanpa internet
- **Data persistence** di local storage
- **Seamless sync** saat koneksi kembali
- **User experience** yang konsisten
- **Performance** yang optimal
- **Reliability** di berbagai kondisi

---

## **📱 Offline-First Strategy**

### **1. Data Storage Strategy**
```javascript
// ✅ Multi-layer storage strategy
class OfflineStorageManager {
  constructor() {
    this.storage = {
      // Layer 1: Memory (fastest, volatile)
      memory: new Map(),
      
      // Layer 2: IndexedDB (persistent, structured)
      indexedDB: null,
      
      // Layer 3: AsyncStorage (simple key-value)
      asyncStorage: null,
      
      // Layer 4: File System (large files)
      fileSystem: null
    };
  }

  async init() {
    // Initialize IndexedDB
    await this.initIndexedDB();
    
    // Initialize AsyncStorage
    await this.initAsyncStorage();
    
    // Initialize File System
    await this.initFileSystem();
  }

  // Initialize IndexedDB for structured data
  async initIndexedDB() {
    return new Promise((resolve, reject) => {
      const request = indexedDB.open('distribusi-offline', 1);
      
      request.onerror = () => reject(request.error);
      request.onsuccess = () => {
        this.storage.indexedDB = request.result;
        resolve();
      };
      
      request.onupgradeneeded = (event) => {
        const db = event.target.result;
        
        // Create object stores
        if (!db.objectStoreNames.contains('purchases')) {
          const purchaseStore = db.createObjectStore('purchases', { keyPath: 'id' });
          purchaseStore.createIndex('supplier_id', 'supplier_id', { unique: false });
          purchaseStore.createIndex('date', 'purchase_date', { unique: false });
          purchaseStore.createIndex('sync_status', 'sync_status', { unique: false });
        }
        
        if (!db.objectStoreNames.contains('products')) {
          const productStore = db.createObjectStore('products', { keyPath: 'id' });
          productStore.createIndex('category', 'category', { unique: false });
          productStore.createIndex('barcode', 'barcode', { unique: true });
          productStore.createIndex('sync_status', 'sync_status', { unique: false });
        }
        
        if (!db.objectStoreNames.contains('customers')) {
          const customerStore = db.createObjectStore('customers', { keyPath: 'id' });
          customerStore.createIndex('name', 'name', { unique: false });
          customerStore.createIndex('sync_status', 'sync_status', { unique: false });
        }
        
        if (!db.objectStoreNames.contains('sync_queue')) {
          const syncStore = db.createObjectStore('sync_queue', { keyPath: 'id', autoIncrement: true });
          syncStore.createIndex('timestamp', 'timestamp', { unique: false });
          syncStore.createIndex('status', 'status', { unique: false });
        }
        
        if (!db.objectStoreNames.contains('settings')) {
          const settingsStore = db.createObjectStore('settings', { keyPath: 'key' });
        }
      };
    });
  }

  // Initialize AsyncStorage for simple data
  async initAsyncStorage() {
    // For React Native
    if (typeof AsyncStorage !== 'undefined') {
      this.storage.asyncStorage = AsyncStorage;
    }
    // For Web
    else if (typeof localStorage !== 'undefined') {
      this.storage.asyncStorage = {
        getItem: (key) => Promise.resolve(localStorage.getItem(key)),
        setItem: (key, value) => Promise.resolve(localStorage.setItem(key, value)),
        removeItem: (key) => Promise.resolve(localStorage.removeItem(key)),
        clear: () => Promise.resolve(localStorage.clear())
      };
    }
  }

  // Initialize File System for large files
  async initFileSystem() {
    // For React Native
    if (typeof RNFS !== 'undefined') {
      this.storage.fileSystem = RNFS;
    }
    // For Web
    else if (typeof FileSystem !== 'undefined') {
      this.storage.fileSystem = FileSystem;
    }
  }

  // Get data with fallback strategy
  async getData(key, fallbackToMemory = true) {
    // Try memory first
    if (this.storage.memory.has(key)) {
      return this.storage.memory.get(key);
    }

    // Try IndexedDB
    if (this.storage.indexedDB) {
      try {
        const data = await this.getFromIndexedDB(key);
        if (data) {
          // Cache in memory
          this.storage.memory.set(key, data);
          return data;
        }
      } catch (error) {
        console.error('IndexedDB error:', error);
      }
    }

    // Try AsyncStorage
    if (this.storage.asyncStorage) {
      try {
        const data = await this.storage.asyncStorage.getItem(key);
        if (data) {
          const parsedData = JSON.parse(data);
          // Cache in memory
          this.storage.memory.set(key, parsedData);
          return parsedData;
        }
      } catch (error) {
        console.error('AsyncStorage error:', error);
      }
    }

    return null;
  }

  // Set data with multi-layer storage
  async setData(key, data, persistToAll = true) {
    // Always store in memory
    this.storage.memory.set(key, data);

    if (persistToAll) {
      // Store in IndexedDB
      if (this.storage.indexedDB) {
        try {
          await this.setToIndexedDB(key, data);
        } catch (error) {
          console.error('IndexedDB error:', error);
        }
      }

      // Store in AsyncStorage
      if (this.storage.asyncStorage) {
        try {
          await this.storage.asyncStorage.setItem(key, JSON.stringify(data));
        } catch (error) {
          console.error('AsyncStorage error:', error);
        }
      }
    }
  }

  // IndexedDB operations
  async getFromIndexedDB(storeName, key = null) {
    const transaction = this.storage.indexedDB.transaction([storeName], 'readonly');
    const store = transaction.objectStore(storeName);

    if (key) {
      return new Promise((resolve, reject) => {
        const request = store.get(key);
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
      });
    } else {
      return new Promise((resolve, reject) => {
        const request = store.getAll();
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
      });
    }
  }

  async setToIndexedDB(storeName, data) {
    const transaction = this.storage.indexedDB.transaction([storeName], 'readwrite');
    const store = transaction.objectStore(storeName);

    return new Promise((resolve, reject) => {
      const request = store.put(data);
      request.onsuccess = () => resolve(request.result);
      request.onerror = () => reject(request.error);
    });
  }
}
```

### **2. Sync Queue Management**
```javascript
// ✅ Sync queue for offline actions
class SyncQueueManager {
  constructor(storageManager) {
    this.storage = storageManager;
    this.isProcessing = false;
    this.retryAttempts = new Map();
    this.maxRetries = 3;
    this.retryDelay = 5000; // 5 seconds
  }

  // Add action to sync queue
  async enqueue(action) {
    const queueItem = {
      id: this.generateId(),
      type: action.type,
      data: action.data,
      timestamp: Date.now(),
      status: 'pending',
      retryCount: 0
    };

    // Store in IndexedDB
    await this.storage.setToIndexedDB('sync_queue', queueItem);

    // Try to process immediately if online
    if (navigator.onLine) {
      await this.processQueue();
    }

    return queueItem.id;
  }

  // Process sync queue
  async processQueue() {
    if (this.isProcessing) {
      return;
    }

    this.isProcessing = true;

    try {
      while (navigator.onLine) {
        const pendingActions = await this.getPendingActions();
        
        if (pendingActions.length === 0) {
          break;
        }

        for (const action of pendingActions) {
          try {
            await this.executeAction(action);
            await this.markActionCompleted(action.id);
          } catch (error) {
            await this.handleActionError(action, error);
          }
        }
      }
    } finally {
      this.isProcessing = false;
    }
  }

  // Get pending actions
  async getPendingActions() {
    const allActions = await this.storage.getFromIndexedDB('sync_queue');
    return allActions.filter(action => action.status === 'pending');
  }

  // Execute individual action
  async executeAction(action) {
    switch (action.type) {
      case 'CREATE_PURCHASE':
        return this.createPurchase(action.data);
      case 'UPDATE_PURCHASE':
        return this.updatePurchase(action.id, action.data);
      case 'DELETE_PURCHASE':
        return this.deletePurchase(action.id);
      case 'CREATE_CUSTOMER':
        return this.createCustomer(action.data);
      case 'UPDATE_CUSTOMER':
        return this.updateCustomer(action.id, action.data);
      case 'CREATE_PRODUCT':
        return this.createProduct(action.data);
      case 'UPDATE_PRODUCT':
        return this.updateProduct(action.id, action.data);
      case 'UPDATE_INVENTORY':
        return this.updateInventory(action.data);
      default:
        throw new Error(`Unknown action type: ${action.type}`);
    }
  }

  // API calls for sync
  async createPurchase(data) {
    const response = await fetch('/api/purchases', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': this.getCSRFToken()
      },
      body: JSON.stringify(data)
    });

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    const result = await response.json();
    
    // Update local data with server response
    const localPurchase = await this.storage.getFromIndexedDB('purchases', data.local_id);
    if (localPurchase) {
      localPurchase.id = result.id;
      localPurchase.invoice_no = result.invoice_no;
      localPurchase.sync_status = 'synced';
      await this.storage.setToIndexedDB('purchases', localPurchase);
    }

    return result;
  }

  async updatePurchase(id, data) {
    const response = await fetch(`/api/purchases/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': this.getCSRFToken()
      },
      body: JSON.stringify(data)
    });

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    const result = await response.json();
    
    // Update local data
    const localPurchase = await this.storage.getFromIndexedDB('purchases', id);
    if (localPurchase) {
      Object.assign(localPurchase, data);
      localPurchase.sync_status = 'synced';
      await this.storage.setToIndexedDB('purchases', localPurchase);
    }

    return result;
  }

  async deletePurchase(id) {
    const response = await fetch(`/api/purchases/${id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-Token': this.getCSRFToken()
      }
    });

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    // Remove from local storage
    await this.deleteFromIndexedDB('purchases', id);

    return true;
  }

  // Handle action errors
  async handleActionError(action, error) {
    action.retryCount++;
    
    if (action.retryCount >= this.maxRetries) {
      // Mark as failed
      action.status = 'failed';
      action.error = error.message;
      await this.storage.setToIndexedDB('sync_queue', action);
      
      // Notify user
      this.notifySyncError(action, error);
    } else {
      // Schedule retry
      setTimeout(() => {
        this.processQueue();
      }, this.retryDelay * action.retryCount);
    }
  }

  // Mark action as completed
  async markActionCompleted(actionId) {
    const action = await this.storage.getFromIndexedDB('sync_queue', actionId);
    if (action) {
      action.status = 'completed';
      action.completed_at = Date.now();
      await this.storage.setToIndexedDB('sync_queue', action);
    }
  }

  // Delete from IndexedDB
  async deleteFromIndexedDB(storeName, key) {
    const transaction = this.storage.indexedDB.transaction([storeName], 'readwrite');
    const store = transaction.objectStore(storeName);

    return new Promise((resolve, reject) => {
      const request = store.delete(key);
      request.onsuccess = () => resolve();
      request.onerror = () => reject(request.error);
    });
  }

  // Generate unique ID
  generateId() {
    return Date.now().toString(36) + Math.random().toString(36).substr(2);
  }

  // Get CSRF token
  getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  }

  // Notify sync error
  notifySyncError(action, error) {
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.ready.then(registration => {
        registration.showNotification('Sinkronisasi Gagal', {
          body: `Gagal sinkronisasi ${action.type}: ${error.message}`,
          icon: '/icon-192x192.png',
          badge: '/badge-72x72.png',
          data: {
            type: 'sync_error',
            action: action,
            error: error.message
          }
        });
      });
    }
  }
}
```

---

## **📱 Offline Data Management**

### **1. Data Synchronization Strategy**
```javascript
// ✅ Data synchronization manager
class DataSyncManager {
  constructor(storageManager, syncQueueManager) {
    this.storage = storageManager;
    this.syncQueue = syncQueueManager;
    this.lastSyncTime = null;
    this.syncInterval = 60000; // 1 minute
    this.isInitialSync = true;
  }

  // Initialize sync process
  async init() {
    // Load last sync time
    this.lastSyncTime = await this.getLastSyncTime();
    
    // Start periodic sync
    this.startPeriodicSync();
    
    // Setup network monitoring
    this.setupNetworkMonitoring();
    
    // Initial sync
    if (navigator.onLine) {
      await this.performInitialSync();
    }
  }

  // Perform initial sync
  async performInitialSync() {
    try {
      // Sync all data types
      await this.syncPurchases();
      await this.syncProducts();
      await this.syncCustomers();
      await this.syncInventory();
      
      // Update last sync time
      this.lastSyncTime = Date.now();
      await this.setLastSyncTime(this.lastSyncTime);
      
      this.isInitialSync = false;
      
      // Process sync queue
      await this.syncQueue.processQueue();
      
      // Notify user
      this.notifySyncCompleted();
    } catch (error) {
      console.error('Initial sync failed:', error);
      this.notifySyncError(error);
    }
  }

  // Sync purchases
  async syncPurchases() {
    try {
      // Get server data
      const response = await fetch('/api/purchases', {
        headers: {
          'If-Modified-Since': this.lastSyncTime ? new Date(this.lastSyncTime).toUTCString() : ''
        }
      });

      if (response.status === 304) {
        // No changes
        return;
      }

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }

      const serverPurchases = await response.json();
      
      // Get local data
      const localPurchases = await this.storage.getFromIndexedDB('purchases');
      
      // Merge data
      const mergedPurchases = this.mergeData(localPurchases, serverPurchases, 'purchases');
      
      // Update local storage
      for (const purchase of mergedPurchases) {
        await this.storage.setToIndexedDB('purchases', purchase);
      }
      
      // Handle conflicts
      const conflicts = this.identifyConflicts(localPurchases, serverPurchases);
      if (conflicts.length > 0) {
        await this.handleConflicts(conflicts, 'purchases');
      }
      
    } catch (error) {
      console.error('Failed to sync purchases:', error);
      throw error;
    }
  }

  // Sync products
  async syncProducts() {
    try {
      const response = await fetch('/api/products', {
        headers: {
          'If-Modified-Since': this.lastSyncTime ? new Date(this.lastSyncTime).toUTCString() : ''
        }
      });

      if (response.status === 304) {
        return;
      }

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }

      const serverProducts = await response.json();
      const localProducts = await this.storage.getFromIndexedDB('products');
      
      const mergedProducts = this.mergeData(localProducts, serverProducts, 'products');
      
      for (const product of mergedProducts) {
        await this.storage.setToIndexedDB('products', product);
      }
      
    } catch (error) {
      console.error('Failed to sync products:', error);
      throw error;
    }
  }

  // Sync customers
  async syncCustomers() {
    try {
      const response = await fetch('/api/customers', {
        headers: {
          'If-Modified-Since': this.lastSyncTime ? new Date(this.lastSyncTime).toUTCString() : ''
        }
      });

      if (response.status === 304) {
        return;
      }

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }

      const serverCustomers = await response.json();
      const localCustomers = await this.storage.getFromIndexedDB('customers');
      
      const mergedCustomers = this.mergeData(localCustomers, serverCustomers, 'customers');
      
      for (const customer of mergedCustomers) {
        await this.storage.setToIndexedDB('customers', customer);
      }
      
    } catch (error) {
      console.error('Failed to sync customers:', error);
      throw error;
    }
  }

  // Merge local and server data
  mergeData(localData, serverData, dataType) {
    const merged = [];
    const serverMap = new Map(serverData.map(item => [item.id, item]));
    const localMap = new Map(localData.map(item => [item.id, item]));

    // Add server items
    serverData.forEach(serverItem => {
      const localItem = localMap.get(serverItem.id);
      
      if (!localItem) {
        // New item from server
        merged.push({
          ...serverItem,
          sync_status: 'synced'
        });
      } else {
        // Existing item - check timestamps
        const serverTimestamp = new Date(serverItem.updated_at).getTime();
        const localTimestamp = new Date(localItem.updated_at).getTime();
        
        if (serverTimestamp > localTimestamp) {
          // Server is newer
          merged.push({
            ...serverItem,
            sync_status: 'synced'
          });
        } else if (localTimestamp > serverTimestamp) {
          // Local is newer - needs sync
          merged.push({
            ...localItem,
            sync_status: 'needs_sync'
          });
        } else {
          // Same timestamp - no conflict
          merged.push({
            ...serverItem,
            sync_status: 'synced'
          });
        }
      }
    });

    // Add local-only items
    localData.forEach(localItem => {
      if (!serverMap.has(localItem.id)) {
        merged.push({
          ...localItem,
          sync_status: 'local_only'
        });
      }
    });

    return merged;
  }

  // Identify conflicts
  identifyConflicts(localData, serverData) {
    const conflicts = [];
    const serverMap = new Map(serverData.map(item => [item.id, item]));
    const localMap = new Map(localData.map(item => [item.id, item]));

    localData.forEach(localItem => {
      const serverItem = serverMap.get(localItem.id);
      
      if (serverItem && localItem.sync_status === 'modified') {
        const serverTimestamp = new Date(serverItem.updated_at).getTime();
        const localTimestamp = new Date(localItem.updated_at).getTime();
        
        if (serverTimestamp > localTimestamp && localTimestamp > serverTimestamp) {
          // Both modified - conflict
          conflicts.push({
            type: 'conflict',
            id: localItem.id,
            local: localItem,
            server: serverItem
          });
        }
      }
    });

    return conflicts;
  }

  // Handle conflicts
  async handleConflicts(conflicts, dataType) {
    for (const conflict of conflicts) {
      // For now, prefer server version
      // In production, show conflict resolution UI
      await this.resolveConflict(conflict, dataType);
    }
  }

  // Resolve individual conflict
  async resolveConflict(conflict, dataType) {
    // Store conflict for user resolution
    await this.storage.setToIndexedDB('conflicts', {
      ...conflict,
      data_type: dataType,
      resolved: false,
      created_at: Date.now()
    });

    // Notify user
    this.notifyConflict(conflict, dataType);
  }

  // Setup network monitoring
  setupNetworkMonitoring() {
    window.addEventListener('online', () => {
      this.performInitialSync();
    });

    window.addEventListener('offline', () => {
      // Stop periodic sync
      this.stopPeriodicSync();
    });
  }

  // Start periodic sync
  startPeriodicSync() {
    this.syncTimer = setInterval(() => {
      if (navigator.onLine) {
        this.performIncrementalSync();
      }
    }, this.syncInterval);
  }

  // Stop periodic sync
  stopPeriodicSync() {
    if (this.syncTimer) {
      clearInterval(this.syncTimer);
      this.syncTimer = null;
    }
  }

  // Perform incremental sync
  async performIncrementalSync() {
    try {
      await this.syncPurchases();
      await this.syncProducts();
      await this.syncCustomers();
      await this.syncInventory();
      
      this.lastSyncTime = Date.now();
      await this.setLastSyncTime(this.lastSyncTime);
      
      await this.syncQueue.processQueue();
    } catch (error) {
      console.error('Incremental sync failed:', error);
    }
  }

  // Get/set last sync time
  async getLastSyncTime() {
    const settings = await this.storage.getFromIndexedDB('settings', 'last_sync_time');
    return settings ? settings.value : null;
  }

  async setLastSyncTime(time) {
    await this.storage.setToIndexedDB('settings', {
      key: 'last_sync_time',
      value: time
    });
  }

  // Notify sync completed
  notifySyncCompleted() {
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.ready.then(registration => {
        registration.showNotification('Sinkronisasi Selesai', {
          body: 'Data berhasil disinkronkan dengan server',
          icon: '/icon-192x192.png',
          badge: '/badge-72x72.png'
        });
      });
    }
  }

  // Notify sync error
  notifySyncError(error) {
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.ready.then(registration => {
        registration.showNotification('Sinkronisasi Gagal', {
          body: `Gagal sinkronisasi: ${error.message}`,
          icon: '/icon-192x192.png',
          badge: '/badge-72x72.png'
        });
      });
    }
  }

  // Notify conflict
  notifyConflict(conflict, dataType) {
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.ready.then(registration => {
        registration.showNotification('Konflik Data', {
          body: `Konflik pada ${dataType} #${conflict.id}. Silakan resolve manual.`,
          icon: '/icon-192x192.png',
          badge: '/badge-72x72.png',
          data: {
            type: 'conflict',
            conflict: conflict
          }
        });
      });
    }
  }
}
```

---

## **📱 Offline UI Components**

### **1. Offline Status Indicator**
```javascript
// ✅ Offline status indicator component
class OfflineStatusIndicator {
  constructor() {
    this.indicator = null;
    this.status = 'online';
    this.setupIndicator();
    this.setupNetworkMonitoring();
  }

  setupIndicator() {
    // Create indicator element
    this.indicator = document.createElement('div');
    this.indicator.id = 'offline-indicator';
    this.indicator.className = 'offline-indicator';
    this.indicator.innerHTML = `
      <div class="offline-content">
        <span class="status-icon">📱</span>
        <span class="status-text">Anda sedang offline</span>
        <button class="retry-button" onclick="window.location.reload()">Coba Lagi</button>
      </div>
    `;
    
    // Add styles
    const style = document.createElement('style');
    style.textContent = `
      .offline-indicator {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background-color: #dc3545;
        color: white;
        padding: 1rem;
        z-index: 9999;
        transform: translateY(-100%);
        transition: transform 0.3s ease;
      }
      
      .offline-indicator.show {
        transform: translateY(0);
      }
      
      .offline-content {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        max-width: 1200px;
        margin: 0 auto;
      }
      
      .status-icon {
        font-size: 1.5rem;
      }
      
      .status-text {
        flex: 1;
        font-weight: 500;
      }
      
      .retry-button {
        background-color: white;
        color: #dc3545;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
      }
      
      .retry-button:hover {
        background-color: #f8f9fa;
      }
    `;
    
    document.head.appendChild(style);
    document.body.appendChild(this.indicator);
  }

  setupNetworkMonitoring() {
    window.addEventListener('online', () => {
      this.setStatus('online');
    });

    window.addEventListener('offline', () => {
      this.setStatus('offline');
    });

    // Initial check
    if (!navigator.onLine) {
      this.setStatus('offline');
    }
  }

  setStatus(status) {
    this.status = status;
    
    if (status === 'offline') {
      this.indicator.classList.add('show');
      this.indicator.querySelector('.status-text').textContent = 'Anda sedang offline';
    } else {
      this.indicator.classList.remove('show');
    }
  }

  showSyncStatus(message) {
    this.indicator.classList.add('show');
    this.indicator.querySelector('.status-text').textContent = message;
    this.indicator.querySelector('.status-icon').textContent = '🔄';
    
    setTimeout(() => {
      if (this.status === 'online') {
        this.indicator.classList.remove('show');
      }
    }, 3000);
  }
}
```

### **2. Offline Form Handler**
```javascript
// ✅ Offline form handler
class OfflineFormHandler {
  constructor(syncQueueManager) {
    this.syncQueue = syncQueueManager;
    this.setupFormHandlers();
  }

  setupFormHandlers() {
    // Handle purchase form
    const purchaseForm = document.getElementById('purchase-form');
    if (purchaseForm) {
      purchaseForm.addEventListener('submit', (e) => {
        e.preventDefault();
        this.handlePurchaseSubmit(purchaseForm);
      });
    }

    // Handle customer form
    const customerForm = document.getElementById('customer-form');
    if (customerForm) {
      customerForm.addEventListener('submit', (e) => {
        e.preventDefault();
        this.handleCustomerSubmit(customerForm);
      });
    }

    // Handle product form
    const productForm = document.getElementById('product-form');
    if (productForm) {
      productForm.addEventListener('submit', (e) => {
        e.preventDefault();
        this.handleProductSubmit(productForm);
      });
    }
  }

  async handlePurchaseSubmit(form) {
    const formData = new FormData(form);
    const purchaseData = {
      local_id: this.generateLocalId(),
      supplier_id: parseInt(formData.get('supplier_id')),
      purchase_date: formData.get('purchase_date'),
      items: this.parseItems(formData.get('items')),
      total_amount: parseFloat(formData.get('total_amount')),
      notes: formData.get('notes'),
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
      sync_status: 'pending'
    };

    try {
      // Save locally first
      await this.savePurchaseLocally(purchaseData);
      
      // Add to sync queue
      await this.syncQueue.enqueue({
        type: 'CREATE_PURCHASE',
        data: purchaseData
      });
      
      // Show success message
      this.showSuccessMessage('Pembelian berhasil ditambahkan (offline)');
      
      // Reset form
      form.reset();
      
      // Update UI
      this.updatePurchaseList();
      
    } catch (error) {
      console.error('Failed to save purchase:', error);
      this.showErrorMessage('Gagal menyimpan pembelian');
    }
  }

  async handleCustomerSubmit(form) {
    const formData = new FormData(form);
    const customerData = {
      local_id: this.generateLocalId(),
      name: formData.get('name'),
      email: formData.get('email'),
      phone: formData.get('phone'),
      address: formData.get('address'),
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
      sync_status: 'pending'
    };

    try {
      // Save locally first
      await this.saveCustomerLocally(customerData);
      
      // Add to sync queue
      await this.syncQueue.enqueue({
        type: 'CREATE_CUSTOMER',
        data: customerData
      });
      
      // Show success message
      this.showSuccessMessage('Pelanggan berhasil ditambahkan (offline)');
      
      // Reset form
      form.reset();
      
      // Update UI
      this.updateCustomerList();
      
    } catch (error) {
      console.error('Failed to save customer:', error);
      this.showErrorMessage('Gagal menyimpan pelanggan');
    }
  }

  async savePurchaseLocally(purchaseData) {
    // Save to IndexedDB
    const storageManager = new OfflineStorageManager();
    await storageManager.setToIndexedDB('purchases', purchaseData);
  }

  async saveCustomerLocally(customerData) {
    // Save to IndexedDB
    const storageManager = new OfflineStorageManager();
    await storageManager.setToIndexedDB('customers', customerData);
  }

  parseItems(itemsString) {
    try {
      return JSON.parse(itemsString);
    } catch (error) {
      return [];
    }
  }

  generateLocalId() {
    return 'local_' + Date.now().toString(36) + Math.random().toString(36).substr(2);
  }

  showSuccessMessage(message) {
    this.showToast(message, 'success');
  }

  showErrorMessage(message) {
    this.showToast(message, 'error');
  }

  showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    
    // Add styles
    const style = document.createElement('style');
    style.textContent = `
      .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 4px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
      }
      
      .toast.show {
        transform: translateX(0);
      }
      
      .toast-success {
        background-color: #28a745;
      }
      
      .toast-error {
        background-color: #dc3545;
      }
      
      .toast-info {
        background-color: #17a2b8;
      }
    `;
    
    if (!document.querySelector('style[data-toast]')) {
      style.setAttribute('data-toast', '');
      document.head.appendChild(style);
    }
    
    document.body.appendChild(toast);
    
    // Show toast
    setTimeout(() => {
      toast.classList.add('show');
    }, 100);
    
    // Hide toast
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => {
        document.body.removeChild(toast);
      }, 300);
    }, 3000);
  }

  updatePurchaseList() {
    // Trigger purchase list update
    window.dispatchEvent(new CustomEvent('purchase:updated'));
  }

  updateCustomerList() {
    // Trigger customer list update
    window.dispatchEvent(new CustomEvent('customer:updated'));
  }
}
```

---

## **📊 Success Metrics**

### **📈 Offline Performance:**
- **Offline functionality:** 100% core features
- **Data sync success:** > 95%
- **Conflict resolution:** < 1% of sync operations
- **Local storage usage:** < 50MB
- **Sync time:** < 30 seconds for full sync

### **📱 User Experience:**
- **Offline detection:** < 100ms
- **Form submission:** < 200ms
- **Data loading:** < 500ms from local storage
- **Sync notification:** Real-time
- **Error handling:** Graceful degradation

### **📊 Reliability Metrics:**
- **Data integrity:** 100%
- **Recovery from errors:** > 90%
- **Conflict resolution:** > 95%
- **User data loss:** 0%
- **App crashes:** < 0.1%

---

**Status:** ✅ **Offline-first design selesai - Ready for implementation**

**Priority:** Critical - Essential untuk reliability di berbagai kondisi
