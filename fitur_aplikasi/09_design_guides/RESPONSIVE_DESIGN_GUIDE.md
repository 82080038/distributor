# Responsive Design Guide - Sistem Distribusi

## **📱 Target Device Coverage**

### **Ukuran Layar yang Didukung**
- **Desktop:** 1920×1080+ (Full HD)
- **Laptop:** 1366×768+ (MDPI)
- **Tablet:** 1024×768+ (Portrait & Landscape)
- **Handphone:** 360×640+ (Small), 375×667+ (Medium), 414×736+ (Large)
- **Phablet:** 480×800+ (Medium-Large)

## **🎨 Responsive Design Strategy**

### **1. Mobile-First Approach**
- **Prioritas utama** untuk pengalaman mobile
- **Progressive Enhancement** untuk desktop
- **Touch-friendly interface** untuk semua device

### **2. Breakpoint System**
```css
/* Breakpoint untuk berbagai ukuran layar */
:root {
    -- Small phones
    --max-width: 480px;
    
    -- Large phones/Small tablets
    --max-width: 767px;
    
    -- Tablets
    --max-width: 1024px;
    
    -- Small desktop/Large tablets
    --max-width: 1366px;
    
    -- Desktop
    --max-width: 1920px;
    
    -- Large desktop
    --min-width: 1921px;
}

/* Container fluid untuk semua ukuran */
.container {
    width: 100%;
    max-width: 100%;
    padding: 0 15px;
}

/* Grid system yang responsif */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -15px;
}

.col {
    flex: 1;
    padding: 0 15px;
}

/* Mobile-first navigation */
.mobile-only {
    display: block;
}

.desktop-only {
    display: none;
}

@media (min-width: 768px) {
    .mobile-only {
        display: none;
    }
    
    .desktop-only {
        display: block;
    }
    
    .col {
        flex: 0 0 30px;
    }
}
```

### **3. Typography & Spacing**
```css
/* Typography yang skalabel */
html {
    font-size: 16px;
    line-height: 1.5;
}

/* Spacing yang konsisten */
.mb-1 { margin-bottom: 0.25rem; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-3 { margin-bottom: 1rem; }
.mb-4 { margin-bottom: 1.5rem; }

.p-1 { padding: 0.25rem; }
.p-2 { padding: 0.5rem; }
.p-3 { padding: 1rem; }
```

### **4. Touch-Friendly Components**
```css
/* Button yang mudah di-tap */
.btn {
    min-height: 44px; /* iOS touch target */
    padding: 12px 20px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

/* Input fields yang lebih besar di mobile */
.form-control {
    font-size: 16px;
    padding: 12px;
    min-height: 44px;
    border: 2px solid #ddd;
    border-radius: 5px;
}

/* Table yang dapat di-scroll horizontal */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
```

### **5. Performance Optimization**
```css
/* Optimasi untuk mobile */
img {
    max-width: 100%;
    height: auto;
    loading: lazy;
}

/* Reduce animation untuk mobile */
@media (max-width: 768px) {
    .no-animation {
        animation: none !important;
        transition: none !important;
    }
}
```

## **📋 Component Library**

### **1. Bootstrap 5 Enhancement**
```css
/* Custom utilities untuk mobile */
.d-block {
    display: block !important;
}

.d-none {
    display: none !important;
}

/* Mobile navigation */
.mobile-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1px solid #ddd;
    z-index: 1000;
}

/* Off-canvas menu */
.offcanvas {
    position: fixed;
    top: 0;
    left: -250px;
    width: 250px;
    height: 100vh;
    background: #fff;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    z-index: 1050;
}

.offcanvas.show {
    transform: translateX(0);
}
```

### **2. Custom JavaScript Utilities**
```javascript
// Touch event handling
class TouchHandler {
    constructor() {
        this.init();
    }
    
    init() {
        this.setupTouchEvents();
        this.setupScrollOptimization();
    }
    
    setupTouchEvents() {
        // Touch event untuk mobile interactions
        document.addEventListener('touchstart', this.handleTouchStart, {passive: true});
        document.addEventListener('touchend', this.handleTouchEnd, {passive: true});
    }
    
    setupScrollOptimization() {
        // Smooth scrolling untuk mobile
        if ('scrollBehavior' in document.documentElement.style) {
            document.documentElement.style.scrollBehavior = 'smooth';
        }
    }
    
    handleTouchStart(e) {
        // Handle touch start
    }
    
    handleTouchEnd(e) {
        // Handle touch end
    }
}

// Responsive image lazy loading
class LazyLoader {
    constructor() {
        this.init();
    }
    
    init() {
        if ('IntersectionObserver' in window) {
            this.setupIntersectionObserver();
        } else {
            this.setupScrollListener();
        }
    }
    
    setupIntersectionObserver() {
        const images = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
    
    setupScrollListener() {
        window.addEventListener('scroll', this.throttle(this.lazyLoadImages.bind(this), 200));
    }
    
    lazyLoadImages() {
        // Fallback lazy loading
    }
    
    throttle(callback, limit) {
        let lastCall = 0;
        return function() {
            const now = Date.now();
            if (now - lastCall >= limit) {
                callback.apply(this, arguments);
                lastCall = now;
            }
        };
    }
}
```

## **🎨 Progressive Web App Features**

### **1. Service Worker untuk Offline**
```javascript
// sw.js - Service Worker untuk PWA
const CACHE_NAME = 'distribusi-v1';
const urlsToCache = [
    '/',
    '/css/bootstrap.min.css',
    '/js/jquery.min.js',
    '/api/customers',
    '/api/products'
];

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(urlsToCache);
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request).then(response => {
                // Cache successful responses
                if (response.ok && urlsToCache.includes(new URL(event.request).pathname)) {
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, response.clone()));
                }
                return response;
            });
        })
    );
});
```

### **2. Web App Manifest**
```json
{
    "name": "Sistem Distribusi",
    "short_name": "Distribusi",
    "description": "Sistem distribusi multi-platform",
    "start_url": "/",
    "display": "standalone",
    "background_color": "#2196F3",
    "theme_color": "#1976D2",
    "orientation": "any",
    "scope": "/",
    "icons": [
        {
            "src": "assets/icons/icon-72x72.png",
            "sizes": "72x72",
            "type": "image/png"
        },
        {
            "src": "assets/icons/icon-96x96.png",
            "sizes": "96x96",
            "type": "image/png"
        },
        {
            "src": "assets/icons/icon-128x128.png",
            "sizes": "128x128",
            "type": "image/png"
        },
        {
            "src": "assets/icons/icon-144x144.png",
            "sizes": "144x144",
            "type": "interface/png"
        },
        {
            "src": "assets/icons/icon-192x192.png",
            "sizes": "192x192",
            "type": "image/png"
        }
    ]
}
```

## **📊 Testing Strategy**

### **1. Device Testing Matrix**
```javascript
// Test suite untuk berbagai device
const deviceTests = {
    'mobile-small': {
        width: 360,
        height: 640,
        userAgent: 'Mobile'
    },
    'mobile-medium': {
        width: 414,
        height: 736,
        userAgent: 'Mobile'
    },
    'tablet': {
        width: 1024,
        height: 768,
        userAgent: 'Tablet'
    },
    'desktop': {
        width: 1920,
        height: 1080,
        userAgent: 'Desktop'
    }
};

// Device detection
function detectDevice() {
    const width = window.innerWidth;
    const height = window.innerHeight;
    const userAgent = navigator.userAgent;
    
    for (const [device, config] of Object.entries(deviceTests)) {
        if (width <= config.width && height <= config.height && userAgent.includes(config.userAgent)) {
            return device;
        }
    }
    
    return 'unknown';
}
```

### **2. Cross-Browser Compatibility**
```javascript
// Polyfill untuk older browsers
if (!window.fetch) {
    // Implement fetch polyfill
}

if (!window.Promise) {
    // Implement Promise polyfill
}

// CSS fallbacks
.grid {
    display: flex;
    display: -webkit-box; /* Safari 6.1+ */
    display: -ms-flexbox; /* IE 10 */
}
```

## **🚀 Performance Optimization**

### **1. Critical Rendering Path**
```css
/* Prioritaskan content di atas fold */
.critical-above-fold {
    display: block;
}

.critical-below-fold {
    display: none;
}

/* Optimize font loading */
@font-face {
    font-display: swap;
}
```

### **2. Resource Loading**
```javascript
// Preload critical resources
const criticalResources = [
    '/css/bootstrap.min.css',
    '/js/jquery.min.js',
    '/api/products'
];

criticalResources.forEach(resource => {
    const link = document.createElement('link');
    link.rel = 'preload';
    link.href = resource;
    link.as = 'style';
    document.head.appendChild(link);
});
```

## **📱 Mobile-Specific Features**

### **1. Touch Gestures**
```javascript
// Swipe gestures untuk mobile
class SwipeGestures {
    constructor(element) {
        this.element = element;
        this.startX = 0;
        this.startY = 0;
        this.threshold = 50;
        this.init();
    }
    
    init() {
        this.element.addEventListener('touchstart', this.handleTouchStart.bind(this));
        this.element.addEventListener('touchmove', this.handleTouchMove.bind(this));
        this.element.addEventListener('touchend', this.handleTouchEnd.bind(this));
    }
    
    handleTouchStart(e) {
        this.startX = e.touches[0].clientX;
        this.startY = e.touches[0].clientY;
    }
    
    handleTouchMove(e) {
        const currentX = e.touches[0].clientX;
        const deltaX = currentX - this.startX;
        
        if (Math.abs(deltaX) > this.threshold) {
            const direction = deltaX > 0 ? 'right' : 'left';
            this.element.dispatchEvent(new CustomEvent('swipe', {detail: {direction}}));
        }
    }
    
    handleTouchEnd(e) {
        // Reset untuk next gesture
    }
}
```

### **2. Mobile Navigation**
```javascript
// Bottom navigation yang mobile-friendly
class MobileNavigation {
    constructor() {
        this.currentView = 'list';
        this.init();
    }
    
    init() {
        this.setupNavigation();
        this.setupBackButton();
    }
    
    setupNavigation() {
        // Setup bottom navigation
        const navItems = document.querySelectorAll('.mobile-nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', this.handleNavigation.bind(this));
        });
    }
    
    setupBackButton() {
        // Handle hardware back button
        if ('onpopstate' in window) {
            window.addEventListener('popstate', this.handleBackButton.bind(this));
        }
    }
    
    handleNavigation(e) {
        const view = e.target.dataset.view;
        this.switchView(view);
    }
    
    switchView(view) {
        // Switch between different views
        document.querySelectorAll('.mobile-view').forEach(v => {
            v.classList.remove('active');
        });
        
        document.querySelector(`[data-view="${view}"]`).classList.add('active');
        this.currentView = view;
    }
    
    handleBackButton(e) {
        if (this.currentView !== 'home') {
            this.switchView('home');
        }
    }
}
```

## **📊 Analytics & Monitoring**

### **1. Device Performance Tracking**
```javascript
// Performance monitoring untuk berbagai device
class DevicePerformanceMonitor {
    constructor() {
        this.metrics = {};
        this.init();
    }
    
    init() {
        this.detectDevice();
        this.measurePerformance();
        this.setupErrorTracking();
    }
    
    detectDevice() {
        this.metrics.device = detectDevice();
        this.metrics.screen = {
            width: screen.width,
            height: screen.height,
            pixelRatio: window.devicePixelRatio || 1
        };
    }
    
    measurePerformance() {
        // Measure loading time
        window.addEventListener('load', () => {
            setTimeout(() => {
                this.metrics.loadTime = performance.now() - performance.timing.navigationStart;
            }, 0);
        });
    }
    
    setupErrorTracking() {
        window.addEventListener('error', (e) => {
            this.logError(e.error, e.filename, e.lineno, e.colno);
        });
    }
    
    logError(error, filename, lineno, colno) {
        // Send error logs to server
        const errorData = {
            error: error,
            device: this.metrics.device,
            timestamp: new Date().toISOString(),
            url: window.location.href
        };
        
        // Send to logging endpoint
        fetch('/api/device-errors', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(errorData)
        });
    }
}
```

---

**Status:** ✅ **Responsive design guide lengkap siap untuk implementasi!**

Panduan ini mencakup:
- **📱 Multi-device coverage** untuk semua ukuran layar
- **🎨 Modern responsive design** dengan mobile-first approach
- **⚡ Performance optimization** untuk loading cepat
- **🔧 Progressive enhancement** untuk PWA capability
- **📊 Cross-browser compatibility** untuk maksimal reach
- **📱 Touch-friendly interface** untuk pengalaman mobile
- **🧪 Testing strategy** untuk quality assurance

Sistem akan memberikan **pengalaman optimal** di semua device dari handphone kecil hingga desktop dengan interface yang responsif dan user-friendly.
