# 05_api_endpoints - Index

## **🔌 API Endpoints**

### **📊 File Overview:**
1. **00_INDEX.md** - Index API endpoints (file ini)
2. **crm_api.php** - RESTful API untuk CRM

---

## **📋 Quick Navigation:**

### **🔌 API Endpoints:**
- **[crm_api.php](./crm_api.php)** - RESTful API Implementation

---

## **📊 API Overview:**

### **👥 CRM API**
- **Framework:** PHP 8.1+ dengan RESTful design
- **Authentication:** JWT token-based
- **Response Format:** JSON
- **Error Handling:** Standard HTTP status codes

---

## **🔌 Available Endpoints:**

### **Customer Management:**
- **GET /api/customers** - Get all customers with pagination
- **GET /api/customers/{id}** - Get single customer
- **POST /api/customers** - Create new customer
- **PUT /api/customers/{id}** - Update customer
- **GET /api/customers/{id}/analytics** - Get customer analytics

---

## **🔧 Technical Details:**

### **API Features:**
- **RESTful Design** - Standard HTTP methods
- **JSON Responses** - Structured data format
- **Error Handling** - Proper HTTP status codes
- **Pagination** - Large dataset handling
- **Filtering** - Search and filter capabilities
- **Validation** - Input validation and sanitization

### **Security Features:**
- **Authentication** - JWT token validation
- **Authorization** - Role-based access control
- **Input Validation** - SQL injection prevention
- **Rate Limiting** - Request throttling

---

## **📋 API Status:**
- ✅ **crm_api.php** - Complete RESTful API
- ✅ **00_INDEX.md** - Navigation index (file ini)

---

## **🎯 Usage Examples:**

### **Get Customers:**
```bash
curl -X GET "http://localhost/api/customers?page=1&limit=20" \
     -H "Authorization: Bearer YOUR_TOKEN"
```

### **Create Customer:**
```bash
curl -X POST "http://localhost/api/customers" \
     -H "Content-Type: application/json" \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -d '{"name":"John Doe","email":"john@example.com"}'
```

---

## **📊 Response Format:**
```json
{
    "success": true,
    "data": {
        "customers": [...],
        "pagination": {
            "page": 1,
            "limit": 20,
            "total": 100,
            "total_pages": 5
        }
    },
    "message": "Success",
    "timestamp": "2026-01-19T20:00:00Z"
}
```

---

**Last Updated:** 19 Januari 2026
**Total Files:** 2 files
**Status:** ✅ Complete
