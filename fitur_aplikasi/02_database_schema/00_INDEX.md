# 02_database_schema - Index

## **🗄️ Database Schemas**

### **📊 File Overview:**
1. **00_INDEX.md** - Index database schema (file ini)
2. **crm_schema.sql** - CRM & Customer Management
3. **accounting_schema.sql** - Complete Accounting System
4. **multi_channel_schema.sql** - Multi-Channel Sales
5. **warehouse_management_schema.sql** - Advanced Warehouse Management
6. **identity_management_schema.sql** - Identity & People Management

---

## **📋 Quick Navigation:**

### **🗄️ Database Schemas:**
- **[crm_schema.sql](./crm_schema.sql)** - CRM & Customer Management
- **[accounting_schema.sql](./accounting_schema.sql)** - Complete Accounting System
- **[multi_channel_schema.sql](./multi_channel_schema.sql)** - Multi-Channel Sales
- **[warehouse_management_schema.sql](./warehouse_management_schema.sql)** - Advanced Warehouse Management
- **[identity_management_schema.sql](./identity_management_schema.sql)** - Identity & People Management

---

## **📊 Schema Overview:**

### **👥 CRM Schema**
- **Tables:** 15 tabel
- **Focus:** Customer management, loyalty, communication
- **Features:** Customer data, segmentation, loyalty points, feedback

### **💰 Accounting Schema**
- **Tables:** 12 tabel
- **Focus:** Complete accounting system
- **Features:** Chart of accounts, journal, AR/AP, fixed assets

### **🛒 Multi-Channel Schema**
- **Tables:** 10 tabel
- **Focus:** Multi-channel sales management
- **Features:** Marketplace integration, order fulfillment, analytics

### **🏭 Warehouse Schema**
- **Tables:** 15 tabel
- **Focus:** Advanced warehouse management
- **Features:** Multi-warehouse, batch tracking, stock movements

### **👤 Identity Schema**
- **Tables:** 12 tabel
- **Focus:** Identity & people management
- **Features:** Person data, addresses, documents, BPS integration

---

## **📊 Total Tables:**
- **CRM:** 15 tables
- **Accounting:** 12 tables
- **Multi-Channel:** 10 tables
- **Warehouse:** 15 tables
- **Identity:** 12 tables
- **Total:** 64 tables

---

## **🔧 Setup Instructions:**

### **Import All Schemas:**
```bash
# Import semua schema ke database
mysql -u username -p database_name < 02_database_schema/crm_schema.sql
mysql -u username -p database_name < 02_database_schema/accounting_schema.sql
mysql -u username -p database_name < 02_database_schema/multi_channel_schema.sql
mysql -u username -p database_name < 02_database_schema/warehouse_management_schema.sql
mysql -u username -p database_name < 02_database_schema/identity_management_schema.sql
```

### **Dependencies:**
- **MySQL 8.0+** dengan support untuk JSON
- **InnoDB engine** untuk foreign key constraints
- **UTF8MB4 charset** untuk Unicode support

---

## **📋 Schema Status:**
- ✅ **crm_schema.sql** - Complete CRM system
- ✅ **accounting_schema.sql** - Full accounting system
- ✅ **multi_channel_schema.sql** - Multi-channel sales
- ✅ **warehouse_management_schema.sql** - Advanced warehouse
- ✅ **identity_management_schema.sql** - Identity management
- ✅ **00_INDEX.md** - Navigation index (file ini)

---

**Last Updated:** 19 Januari 2026
**Total Files:** 6 files
**Total Tables:** 64 tables
**Status:** ✅ Complete
