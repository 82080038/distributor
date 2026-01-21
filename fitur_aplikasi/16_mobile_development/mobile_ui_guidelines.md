# Mobile UI Guidelines

## **📱 Mobile Interface Design**

### **📊 Tujuan:**
- **Touch-friendly** interface untuk mobile devices
- **Responsive design** untuk berbagai screen sizes
- **Intuitive navigation** yang mudah digunakan
- **Consistent design** dengan brand identity
- **Accessibility** untuk semua users
- **Performance** yang optimal di mobile

---

## **📱 Design Principles**

### **1. Mobile-First Design**
- **Touch targets** minimum 44px x 44px
- **Readable text** minimum 16px
- **Simplified layouts** untuk small screens
- **Thumb-friendly** navigation
- **Gesture support** untuk common actions

### **2. Visual Hierarchy**
- **Clear typography** dengan proper sizing
- **Consistent spacing** untuk readability
- **Color contrast** minimum 4.5:1
- **Visual feedback** untuk user actions
- **Progressive disclosure** untuk complex features

### **3. Performance Optimization**
- **Fast loading** dengan optimized assets
- **Smooth animations** dengan 60fps
- **Efficient scrolling** untuk long lists
- **Lazy loading** untuk images dan data
- **Minimal network requests**

---

## **📱 Component Library**

### **1. Typography System**
```css
/* ✅ Typography system for mobile */
:root {
  /* Font sizes */
  --font-size-xs: 0.75rem;    /* 12px */
  --font-size-sm: 0.875rem;   /* 14px */
  --font-size-base: 1rem;     /* 16px */
  --font-size-lg: 1.125rem;   /* 18px */
  --font-size-xl: 1.25rem;    /* 20px */
  --font-size-2xl: 1.5rem;    /* 24px */
  --font-size-3xl: 1.875rem;  /* 30px */
  --font-size-4xl: 2.25rem;   /* 36px */
  
  /* Font weights */
  --font-weight-light: 300;
  --font-weight-normal: 400;
  --font-weight-medium: 500;
  --font-weight-semibold: 600;
  --font-weight-bold: 700;
  
  /* Line heights */
  --line-height-tight: 1.25;
  --line-height-normal: 1.5;
  --line-height-relaxed: 1.75;
  
  /* Letter spacing */
  --letter-spacing-tight: -0.025em;
  --letter-spacing-normal: 0;
  --letter-spacing-wide: 0.025em;
}

/* Typography classes */
.text-xs { font-size: var(--font-size-xs); }
.text-sm { font-size: var(--font-size-sm); }
.text-base { font-size: var(--font-size-base); }
.text-lg { font-size: var(--font-size-lg); }
.text-xl { font-size: var(--font-size-xl); }
.text-2xl { font-size: var(--font-size-2xl); }
.text-3xl { font-size: var(--font-size-3xl); }
.text-4xl { font-size: var(--font-size-4xl); }

.font-light { font-weight: var(--font-weight-light); }
.font-normal { font-weight: var(--font-weight-normal); }
.font-medium { font-weight: var(--font-weight-medium); }
.font-semibold { font-weight: var(--font-weight-semibold); }
.font-bold { font-weight: var(--font-weight-bold); }

.leading-tight { line-height: var(--line-height-tight); }
.leading-normal { line-height: var(--line-height-normal); }
.leading-relaxed { line-height: var(--line-height-relaxed); }

.tracking-tight { letter-spacing: var(--letter-spacing-tight); }
.tracking-normal { letter-spacing: var(--letter-spacing-normal); }
.tracking-wide { letter-spacing: var(--letter-spacing-wide); }
```

### **2. Color System**
```css
/* ✅ Color system for mobile */
:root {
  /* Primary colors */
  --color-primary-50: #eff6ff;
  --color-primary-100: #dbeafe;
  --color-primary-200: #bfdbfe;
  --color-primary-300: #93c5fd;
  --color-primary-400: #60a5fa;
  --color-primary-500: #3b82f6;
  --color-primary-600: #2563eb;
  --color-primary-700: #1d4ed8;
  --color-primary-800: #1e40af;
  --color-primary-900: #1e3a8a;
  
  /* Secondary colors */
  --color-secondary-50: #f8fafc;
  --color-secondary-100: #f1f5f9;
  --color-secondary-200: #e2e8f0;
  --color-secondary-300: #cbd5e1;
  --color-secondary-400: #94a3b8;
  --color-secondary-500: #64748b;
  --color-secondary-600: #475569;
  --color-secondary-700: #334155;
  --color-secondary-800: #1e293b;
  --color-secondary-900: #0f172a;
  
  /* Success colors */
  --color-success-50: #f0fdf4;
  --color-success-100: #dcfce7;
  --color-success-500: #22c55e;
  --color-success-600: #16a34a;
  --color-success-700: #15803d;
  
  /* Warning colors */
  --color-warning-50: #fffbeb;
  --color-warning-100: #fef3c7;
  --color-warning-500: #f59e0b;
  --color-warning-600: #d97706;
  --color-warning-700: #b45309;
  
  /* Error colors */
  --color-error-50: #fef2f2;
  --color-error-100: #fee2e2;
  --color-error-500: #ef4444;
  --color-error-600: #dc2626;
  --color-error-700: #b91c1c;
  
  /* Neutral colors */
  --color-white: #ffffff;
  --color-gray-50: #f9fafb;
  --color-gray-100: #f3f4f6;
  --color-gray-200: #e5e7eb;
  --color-gray-300: #d1d5db;
  --color-gray-400: #9ca3af;
  --color-gray-500: #6b7280;
  --color-gray-600: #4b5563;
  --color-gray-700: #374151;
  --color-gray-800: #1f2937;
  --color-gray-900: #111827;
}

/* Color utility classes */
.bg-primary-500 { background-color: var(--color-primary-500); }
.bg-primary-600 { background-color: var(--color-primary-600); }
.bg-secondary-100 { background-color: var(--color-secondary-100); }
.bg-success-500 { background-color: var(--color-success-500); }
.bg-warning-500 { background-color: var(--color-warning-500); }
.bg-error-500 { background-color: var(--color-error-500); }
.bg-white { background-color: var(--color-white); }
.bg-gray-50 { background-color: var(--color-gray-50); }
.bg-gray-100 { background-color: var(--color-gray-100); }

.text-primary-600 { color: var(--color-primary-600); }
.text-secondary-700 { color: var(--color-secondary-700); }
.text-success-600 { color: var(--color-success-600); }
.text-warning-600 { color: var(--color-warning-600); }
.text-error-600 { color: var(--color-error-600); }
.text-gray-900 { color: var(--color-gray-900); }
.text-gray-600 { color: var(--color-gray-600); }
.text-white { color: var(--color-white); }
```

### **3. Spacing System**
```css
/* ✅ Spacing system for mobile */
:root {
  /* Spacing scale (4px base unit) */
  --space-1: 0.25rem;   /* 4px */
  --space-2: 0.5rem;    /* 8px */
  --space-3: 0.75rem;   /* 12px */
  --space-4: 1rem;      /* 16px */
  --space-5: 1.25rem;   /* 20px */
  --space-6: 1.5rem;    /* 24px */
  --space-8: 2rem;      /* 32px */
  --space-10: 2.5rem;   /* 40px */
  --space-12: 3rem;     /* 48px */
  --space-16: 4rem;     /* 64px */
  --space-20: 5rem;     /* 80px */
  --space-24: 6rem;     /* 96px */
  
  /* Component-specific spacing */
  --spacing-container-padding: var(--space-4);
  --spacing-card-padding: var(--space-4);
  --spacing-button-padding-x: var(--space-4);
  --spacing-button-padding-y: var(--space-3);
  --spacing-input-padding-x: var(--space-3);
  --spacing-input-padding-y: var(--space-2);
  --spacing-gap-xs: var(--space-1);
  --spacing-gap-sm: var(--space-2);
  --spacing-gap-md: var(--space-3);
  --spacing-gap-lg: var(--space-4);
  --spacing-gap-xl: var(--space-6);
}

/* Spacing utility classes */
.p-1 { padding: var(--space-1); }
.p-2 { padding: var(--space-2); }
.p-3 { padding: var(--space-3); }
.p-4 { padding: var(--space-4); }
.p-5 { padding: var(--space-5); }
.p-6 { padding: var(--space-6); }
.p-8 { padding: var(--space-8); }
.p-10 { padding: var(--space-10); }
.p-12 { padding: var(--space-12); }

.px-1 { padding-left: var(--space-1); padding-right: var(--space-1); }
.px-2 { padding-left: var(--space-2); padding-right: var(--space-2); }
.px-3 { padding-left: var(--space-3); padding-right: var(--space-3); }
.px-4 { padding-left: var(--space-4); padding-right: var(--space-4); }
.px-5 { padding-left: var(--space-5); padding-right: var(--space-5); }
.px-6 { padding-left: var(--space-6); padding-right: var(--space-6); }

.py-1 { padding-top: var(--space-1); padding-bottom: var(--space-1); }
.py-2 { padding-top: var(--space-2); padding-bottom: var(--space-2); }
.py-3 { padding-top: var(--space-3); padding-bottom: var(--space-3); }
.py-4 { padding-top: var(--space-4); padding-bottom: var(--space-4); }
.py-5 { padding-top: var(--space-5); padding-bottom: var(--space-5); }
.py-6 { padding-top: var(--space-6); padding-bottom: var(--space-6); }

.m-1 { margin: var(--space-1); }
.m-2 { margin: var(--space-2); }
.m-3 { margin: var(--space-3); }
.m-4 { margin: var(--space-4); }
.m-5 { margin: var(--space-5); }
.m-6 { margin: var(--space-6); }
.m-8 { margin: var(--space-8); }
.m-10 { margin: var(--space-10); }
.m-12 { margin: var(--space-12); }

.mx-1 { margin-left: var(--space-1); margin-right: var(--space-1); }
.mx-2 { margin-left: var(--space-2); margin-right: var(--space-2); }
.mx-3 { margin-left: var(--space-3); margin-right: var(--space-3); }
.mx-4 { margin-left: var(--space-4); margin-right: var(--space-4); }
.mx-5 { margin-left: var(--space-5); margin-right: var(--space-5); }
.mx-6 { margin-left: var(--space-6); margin-right: var(--space-6); }

.my-1 { margin-top: var(--space-1); margin-bottom: var(--space-1); }
.my-2 { margin-top: var(--space-2); margin-bottom: var(--space-2); }
.my-3 { margin-top: var(--space-3); margin-bottom: var(--space-3); }
.my-4 { margin-top: var(--space-4); margin-bottom: var(--space-4); }
.my-5 { margin-top: var(--space-5); margin-bottom: var(--space-5); }
.my-6 { margin-top: var(--space-6); margin-bottom: var(--space-6); }

.gap-1 { gap: var(--space-1); }
.gap-2 { gap: var(--space-2); }
.gap-3 { gap: var(--space-3); }
.gap-4 { gap: var(--space-4); }
.gap-5 { gap: var(--space-5); }
.gap-6 { gap: var(--space-6); }
```

---

## **📱 Mobile Components**

### **1. Button Component**
```css
/* ✅ Mobile button component */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: var(--spacing-button-padding-y) var(--spacing-button-padding-x);
  border: 1px solid transparent;
  border-radius: 0.5rem;
  font-size: var(--font-size-base);
  font-weight: var(--font-weight-medium);
  line-height: var(--line-height-normal);
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s ease;
  min-height: 44px; /* Minimum touch target */
  min-width: 44px; /* Minimum touch target */
  user-select: none;
  -webkit-tap-highlight-color: transparent;
}

.btn:active {
  transform: scale(0.98);
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

/* Button variants */
.btn-primary {
  background-color: var(--color-primary-500);
  color: var(--color-white);
  border-color: var(--color-primary-500);
}

.btn-primary:hover:not(:disabled) {
  background-color: var(--color-primary-600);
  border-color: var(--color-primary-600);
}

.btn-primary:active:not(:disabled) {
  background-color: var(--color-primary-700);
  border-color: var(--color-primary-700);
}

.btn-secondary {
  background-color: var(--color-white);
  color: var(--color-gray-700);
  border-color: var(--color-gray-300);
}

.btn-secondary:hover:not(:disabled) {
  background-color: var(--color-gray-50);
  border-color: var(--color-gray-400);
}

.btn-secondary:active:not(:disabled) {
  background-color: var(--color-gray-100);
  border-color: var(--color-gray-500);
}

.btn-success {
  background-color: var(--color-success-500);
  color: var(--color-white);
  border-color: var(--color-success-500);
}

.btn-success:hover:not(:disabled) {
  background-color: var(--color-success-600);
  border-color: var(--color-success-600);
}

.btn-warning {
  background-color: var(--color-warning-500);
  color: var(--color-white);
  border-color: var(--color-warning-500);
}

.btn-warning:hover:not(:disabled) {
  background-color: var(--color-warning-600);
  border-color: var(--color-warning-600);
}

.btn-error {
  background-color: var(--color-error-500);
  color: var(--color-white);
  border-color: var(--color-error-500);
}

.btn-error:hover:not(:disabled) {
  background-color: var(--color-error-600);
  border-color: var(--color-error-600);
}

/* Button sizes */
.btn-sm {
  padding: var(--space-2) var(--space-3);
  font-size: var(--font-size-sm);
  min-height: 36px;
  min-width: 36px;
}

.btn-lg {
  padding: var(--space-4) var(--space-6);
  font-size: var(--font-size-lg);
  min-height: 52px;
  min-width: 52px;
}

/* Button with icon */
.btn-icon {
  padding: var(--space-3);
  border-radius: 50%;
}

.btn-icon-sm {
  padding: var(--space-2);
  border-radius: 50%;
  min-height: 36px;
  min-width: 36px;
}

.btn-icon-lg {
  padding: var(--space-4);
  border-radius: 50%;
  min-height: 52px;
  min-width: 52px;
}
```

### **2. Input Component**
```css
/* ✅ Mobile input component */
.input {
  display: block;
  width: 100%;
  padding: var(--spacing-input-padding-y) var(--spacing-input-padding-x);
  border: 1px solid var(--color-gray-300);
  border-radius: 0.5rem;
  font-size: var(--font-size-base);
  line-height: var(--line-height-normal);
  color: var(--color-gray-900);
  background-color: var(--color-white);
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  min-height: 44px; /* Minimum touch target */
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

.input:focus {
  outline: none;
  border-color: var(--color-primary-500);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.input:disabled {
  background-color: var(--color-gray-100);
  color: var(--color-gray-500);
  cursor: not-allowed;
}

.input::placeholder {
  color: var(--color-gray-400);
}

/* Input variants */
.input-error {
  border-color: var(--color-error-500);
}

.input-error:focus {
  border-color: var(--color-error-500);
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.input-success {
  border-color: var(--color-success-500);
}

.input-success:focus {
  border-color: var(--color-success-500);
  box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

/* Input group */
.input-group {
  position: relative;
  display: flex;
  align-items: stretch;
}

.input-group .input {
  flex: 1;
}

.input-group-prepend,
.input-group-append {
  display: flex;
  align-items: center;
  padding: 0 var(--space-3);
  background-color: var(--color-gray-100);
  border: 1px solid var(--color-gray-300);
  color: var(--color-gray-600);
  font-size: var(--font-size-sm);
}

.input-group-prepend {
  border-right: none;
  border-radius: 0.5rem 0 0 0.5rem;
}

.input-group-append {
  border-left: none;
  border-radius: 0 0.5rem 0.5rem 0;
}

.input-group .input {
  border-radius: 0;
}

.input-group-prepend + .input {
  border-radius: 0 0.5rem 0.5rem 0;
}

.input + .input-group-append {
  border-radius: 0.5rem 0 0 0.5rem;
}
```

### **3. Card Component**
```css
/* ✅ Mobile card component */
.card {
  background-color: var(--color-white);
  border: 1px solid var(--color-gray-200);
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  overflow: hidden;
  transition: box-shadow 0.2s ease, transform 0.2s ease;
}

.card:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  transform: translateY(-1px);
}

.card:active {
  transform: translateY(0);
}

.card-header {
  padding: var(--spacing-card-padding);
  border-bottom: 1px solid var(--color-gray-200);
  background-color: var(--color-gray-50);
}

.card-title {
  margin: 0;
  font-size: var(--font-size-lg);
  font-weight: var(--font-weight-semibold);
  color: var(--color-gray-900);
}

.card-subtitle {
  margin: var(--space-1) 0 0 0;
  font-size: var(--font-size-sm);
  color: var(--color-gray-600);
}

.card-body {
  padding: var(--spacing-card-padding);
}

.card-text {
  margin: 0;
  color: var(--color-gray-700);
  line-height: var(--line-height-relaxed);
}

.card-footer {
  padding: var(--spacing-card-padding);
  border-top: 1px solid var(--color-gray-200);
  background-color: var(--color-gray-50);
}

/* Card variants */
.card-compact .card-header,
.card-compact .card-body,
.card-compact .card-footer {
  padding: var(--space-3);
}

.card-elevated {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.card-elevated:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
```

---

## **📱 Navigation Patterns**

### **1. Bottom Navigation**
```css
/* ✅ Bottom navigation for mobile */
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: var(--color-white);
  border-top: 1px solid var(--color-gray-200);
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding: var(--space-2) 0;
  z-index: 1000;
  padding-bottom: env(safe-area-inset-bottom);
}

.bottom-nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: var(--space-2);
  border: none;
  background: none;
  color: var(--color-gray-500);
  text-decoration: none;
  font-size: var(--font-size-xs);
  font-weight: var(--font-weight-medium);
  min-width: 44px;
  min-height: 44px;
  transition: color 0.2s ease;
  cursor: pointer;
  -webkit-tap-highlight-color: transparent;
}

.bottom-nav-item:hover,
.bottom-nav-item:focus {
  color: var(--color-primary-500);
}

.bottom-nav-item.active {
  color: var(--color-primary-600);
}

.bottom-nav-icon {
  width: 24px;
  height: 24px;
  margin-bottom: var(--space-1);
}

.bottom-nav-label {
  margin-top: var(--space-1);
}

/* Safe area handling */
.bottom-nav {
  padding-bottom: calc(var(--space-2) + env(safe-area-inset-bottom));
}
```

### **2. Tab Navigation**
```css
/* ✅ Tab navigation for mobile */
.tab-nav {
  display: flex;
  overflow-x: auto;
  background-color: var(--color-white);
  border-bottom: 1px solid var(--color-gray-200);
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE/Edge */
}

.tab-nav::-webkit-scrollbar {
  display: none; /* Chrome/Safari */
}

.tab-nav-item {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--space-3) var(--space-4);
  border: none;
  background: none;
  color: var(--color-gray-500);
  text-decoration: none;
  font-size: var(--font-size-base);
  font-weight: var(--font-weight-medium);
  white-space: nowrap;
  min-height: 44px;
  transition: color 0.2s ease, border-color 0.2s ease;
  cursor: pointer;
  -webkit-tap-highlight-color: transparent;
  position: relative;
}

.tab-nav-item:hover,
.tab-nav-item:focus {
  color: var(--color-primary-500);
}

.tab-nav-item.active {
  color: var(--color-primary-600);
}

.tab-nav-item.active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 2px;
  background-color: var(--color-primary-600);
}

.tab-nav-indicator {
  position: absolute;
  bottom: 0;
  height: 2px;
  background-color: var(--color-primary-600);
  transition: transform 0.3s ease, width 0.3s ease;
}
```

---

## **📱 Responsive Design**

### **1. Breakpoint System**
```css
/* ✅ Mobile-first breakpoint system */
:root {
  /* Breakpoints */
  --breakpoint-sm: 640px;   /* Small phones */
  --breakpoint-md: 768px;   /* Tablets */
  --breakpoint-lg: 1024px;  /* Small laptops */
  --breakpoint-xl: 1280px;  /* Desktops */
  --breakpoint-2xl: 1536px; /* Large desktops */
}

/* Mobile-first approach - base styles for mobile */
.container {
  width: 100%;
  padding: 0 var(--spacing-container-padding);
  margin: 0 auto;
}

/* Small phones and up */
@media (min-width: 640px) {
  .container {
    max-width: 640px;
  }
}

/* Tablets and up */
@media (min-width: 768px) {
  .container {
    max-width: 768px;
    padding: 0 var(--space-6);
  }
}

/* Small laptops and up */
@media (min-width: 1024px) {
  .container {
    max-width: 1024px;
    padding: 0 var(--space-8);
  }
}

/* Desktops and up */
@media (min-width: 1280px) {
  .container {
    max-width: 1280px;
  }
}

/* Large desktops and up */
@media (min-width: 1536px) {
  .container {
    max-width: 1536px;
  }
}

/* Responsive utilities */
.hidden-sm { display: none; }
.hidden-md { display: none; }
.hidden-lg { display: none; }
.hidden-xl { display: none; }

@media (min-width: 640px) {
  .hidden-sm { display: block; }
}

@media (min-width: 768px) {
  .hidden-md { display: block; }
}

@media (min-width: 1024px) {
  .hidden-lg { display: block; }
}

@media (min-width: 1280px) {
  .hidden-xl { display: block; }
}

/* Responsive grid */
.grid {
  display: grid;
  gap: var(--spacing-gap-md);
}

.grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
.grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

@media (min-width: 768px) {
  .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .md\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  .md\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
}

@media (min-width: 1024px) {
  .lg\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
  .lg\:grid-cols-6 { grid-template-columns: repeat(6, minmax(0, 1fr)); }
}
```

---

## **📱 Gesture Support**

### **1. Swipe Actions**
```css
/* ✅ Swipe actions for mobile lists */
.swipe-container {
  position: relative;
  overflow: hidden;
  touch-action: pan-y;
}

.swipe-content {
  position: relative;
  z-index: 1;
  background-color: var(--color-white);
  transition: transform 0.3s ease;
}

.swipe-actions {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  z-index: 0;
}

.swipe-action {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 var(--space-4);
  color: var(--color-white);
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-medium);
  min-width: 80px;
  height: 100%;
  border: none;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.swipe-action-delete {
  background-color: var(--color-error-500);
}

.swipe-action-delete:hover {
  background-color: var(--color-error-600);
}

.swipe-action-edit {
  background-color: var(--color-warning-500);
}

.swipe-action-edit:hover {
  background-color: var(--color-warning-600);
}

.swipe-action-primary {
  background-color: var(--color-primary-500);
}

.swipe-action-primary:hover {
  background-color: var(--color-primary-600);
}
```

### **2. Pull to Refresh**
```css
/* ✅ Pull to refresh component */
.pull-to-refresh {
  position: relative;
  overflow: hidden;
  touch-action: pan-y;
}

.pull-to-refresh-indicator {
  position: absolute;
  top: -60px;
  left: 0;
  right: 0;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--color-gray-50);
  border-bottom: 1px solid var(--color-gray-200);
  transition: transform 0.3s ease;
  z-index: 10;
}

.pull-to-refresh-indicator.active {
  transform: translateY(60px);
}

.pull-to-refresh-spinner {
  width: 24px;
  height: 24px;
  border: 2px solid var(--color-gray-300);
  border-top: 2px solid var(--color-primary-500);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
```

---

## **📱 Accessibility**

### **1. Focus Management**
```css
/* ✅ Focus management for mobile */
.focus-visible {
  outline: 2px solid var(--color-primary-500);
  outline-offset: 2px;
}

.focus-ring {
  outline: none;
}

.focus-ring:focus-visible {
  outline: 2px solid var(--color-primary-500);
  outline-offset: 2px;
}

/* Skip link for accessibility */
.skip-link {
  position: absolute;
  top: -40px;
  left: 6px;
  background: var(--color-primary-600);
  color: var(--color-white);
  padding: 8px;
  text-decoration: none;
  border-radius: 4px;
  z-index: 1000;
  transition: top 0.3s ease;
}

.skip-link:focus {
  top: 6px;
}

/* Screen reader only content */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}
```

---

## **📊 Success Metrics**

### **📈 Usability Metrics:**
- **Touch target accuracy:** 100% (minimum 44px)
- **Text readability:** 100% (minimum 16px)
- **Color contrast:** 100% (minimum 4.5:1)
- **Gesture recognition:** > 95%
- **Navigation ease:** > 90% user satisfaction

### **📱 Performance Metrics:**
- **First paint:** < 1.5s
- **Interaction readiness:** < 2s
- **Animation smoothness:** 60fps
- **Scroll performance:** > 55fps
- **Memory usage:** < 100MB

### **📊 Accessibility Metrics:**
- **WCAG compliance:** 100%
- **Screen reader support:** 100%
- **Keyboard navigation:** 100%
- **Focus management:** 100%
- **Voice control:** 90%

---

**Status:** ✅ **Mobile UI guidelines selesai - Ready for implementation**

**Priority:** High - Essential untuk optimal mobile experience
