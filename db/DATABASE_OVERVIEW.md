# Database Overview - Distributor Application

## Daftar Lengkap Database

Berikut adalah dokumentasi lengkap untuk semua file database yang tersedia dalam direktori `db`:

### 1. Database Utama (Core Databases)

#### 1.1. **alamat_db.sql**
- **Deskripsi**: Database untuk manajemen data alamat dan lokasi geografis Indonesia
- **Fitur Utama**:
  - Data provinsi, kabupaten/kota, kecamatan, dan desa/kelurahan
  - Integrasi dengan tabel user_addresses
  - Data lokasi cabang dan orang
- **Tabel Utama**: `provinces`, `regencies`, `districts`, `villages`, `user_addresses`
- **Status**: Sudah ada dan berisi data lengkap wilayah Indonesia

#### 1.2. **orang_schema.sql**
- **Deskripsi**: Database untuk manajemen data orang (person)
- **Fitur Utama**:
  - Data pribadi, kontak, dan alamat
  - Klasifikasi sebagai supplier, customer, atau karyawan
  - Riwayat pendidikan, pekerjaan, dan keluarga
  - Dokumen identitas dan sertifikasi
- **Tabel Utama**: `orang`, `orang_contacts`, `orang_addresses`, `orang_documents`, `orang_education`, `orang_work_history`, `orang_family`

#### 1.3. **barang_schema.sql**
- **Deskripsi**: Database untuk manajemen barang/products
- **Fitur Utama**:
  - Katalog produk dengan kategori multi-level
  - Manajemen stok dan harga
  - Barcode dan PLU codes
  - Supplier dan lokasi penyimpanan
  - Tracking expired date dan batch
- **Tabel Utama**: `barang`, `barang_categories`, `barang_prices`, `barang_stock`, `barang_barcodes`, `barang_suppliers`

#### 1.4. **waktu_schema.sql**
- **Deskripsi**: Database untuk manajemen waktu dan transaksi
- **Fitur Utama**:
  - Kalender dan jam operasional
  - Transaksi pembelian dan penjualan
  - Payment methods dan bank accounts
  - Audit trail untuk semua transaksi
- **Tabel Utama**: `calendar`, `working_hours`, `transactions`, `transaction_items`, `payment_methods`, `bank_accounts`

#### 1.5. **surat_laporan_schema.sql**
- **Deskripsi**: Database untuk manajemen dokumen dan laporan
- **Fitur Utama**:
  - Template dokumen (invoice, PO, surat jalan)
  - Generate dokumen otomatis
  - Arsip dan versi control
  - Digital signatures
- **Tabel Utama**: `document_templates`, `generated_documents`, `document_archives`, `digital_signatures`

#### 1.6. **aplikasi_schema.sql**
- **Deskripsi**: Database utama aplikasi distributor
- **Fitur Utama**:
  - Manajemen user dan roles
  - Cabang dan perusahaan
  - Menu dan permissions
  - System settings
  - Activity logs
- **Tabel Utama**: `users`, `roles`, `permissions`, `branches`, `companies`, `menus`, `system_settings`, `activity_logs`

### 2. Database Tambahan (Additional Databases)

#### 2.1. **analytics_schema.sql**
- **Deskripsi**: Database untuk analitik dan business intelligence
- **Fitur Utama**:
  - Data warehouse dengan fact dan dimension tables
  - Sales analytics dan inventory analytics
  - Customer behavior analysis
  - Dashboard dan reporting
- **Tabel Utama**: `fact_sales`, `fact_inventory`, `dim_date`, `dim_customer`, `dim_product`, `analytics_reports`

#### 2.2. **logistics_schema.sql**
- **Deskripsi**: Database untuk manajemen logistik
- **Fitur Utama**:
  - Manajemen pengiriman dan tracking
  - Fleet management
  - Route optimization
  - Warehouse management
- **Tabel Utama**: `shipments`, `shipment_tracking`, `vehicles`, `drivers`, `routes`, `warehouse_zones`

#### 2.3. **finance_schema.sql**
- **Deskripsi**: Database untuk manajemen keuangan
- **Fitur Utama**:
  - Accounting dan general ledger
  - Accounts payable/receivable
  - Budgeting dan cash flow
  - Tax management
- **Tabel Utama**: `chart_of_accounts`, `journal_entries`, `invoices`, `payments`, `budgets`, `tax_codes`

#### 2.4. **hrm_schema.sql**
- **Deskripsi**: Database untuk Human Resource Management
- **Fitur Utama**:
  - Employee data management
  - Payroll dan benefits
  - Attendance dan leave management
  - Performance reviews
- **Tabel Utama**: `employees`, `departments`, `positions`, `payroll`, `attendance`, `leave_requests`, `performance_reviews`

#### 2.5. **ecommerce_schema.sql**
- **Deskripsi**: Database untuk integrasi e-commerce
- **Fitur Utama**:
  - Multi-channel marketplace integration
  - Order synchronization
  - Inventory sync
  - Pricing dan promotion management
- **Tabel Utama**: `marketplaces`, `product_listings`, `order_sync`, `inventory_sync`, `pricing_rules`

#### 2.6. **communication_schema.sql**
- **Deskripsi**: Database untuk sistem komunikasi
- **Fitur Utama**:
  - Email, SMS, dan WhatsApp templates
  - Campaign management
  - Chat dan support tickets
  - Notification system
- **Tabel Utama**: `email_templates`, `sms_templates`, `campaigns`, `chat_conversations`, `support_tickets`

### 3. Database Extensions

#### 3.1. **extensions_schema.sql**
- **Deskripsi**: Database untuk ekstensi dan future-proofing
- **Fitur Utama**:
  - Dynamic entity extensions
  - Multi-tenant support
  - Custom fields dan workflows
  - Audit trails dan system metrics
  - Feature flags dan scheduled tasks
- **Tabel Utama**: `entity_extensions`, `application_registry`, `tenant_management`, `custom_fields`, `workflow_definitions`, `audit_trails`

### 4. Database Legacy/Eksisting

#### 4.1. **distributor.sql**
- **Deskripsi**: Database distributor yang sudah ada
- **Fitur Utama**:
  - Core distributor functionality
  - Product management dengan PLU codes
  - Sales dan purchases
  - User management
- **Status**: Legacy database yang masih digunakan

#### 4.2. **distribusi.sql**
- **Deskripsi**: Database distribusi dengan tambahan SPPG
- **Fitur Utama**:
  - Semua fitur distributor.sql
  - Tambahan SPPG (Sentra Pelayanan Pemenuhan Gizi)
  - Menu management untuk SPPG
  - Material demand tracking
- **Status**: Extended version dengan SPPG functionality

### 5. Dokumentasi

#### 5.1. **COMPLETE_DATABASE_GUIDE.md**
- **Deskripsi**: Panduan lengkap implementasi database
- **Isi**: Best practices, integration guidelines, dan technical considerations

#### 5.2. **DATABASE_DOCUMENTATION.md**
- **Deskripsi**: Dokumentasi teknis detail
- **Isi**: ERD descriptions, table relationships, dan data flow

#### 5.3. **FUTURE_PROOF_EXTENSIONS_GUIDE.md**
- **Deskripsi**: Panduan untuk ekstensi database
- **Isi**: Cara menggunakan extensions database untuk aplikasi masa depan

#### 5.4. **NORMALIZATION_ANALYSIS.md**
- **Deskripsi**: Analisis normalisasi database
- **Isi**: Identifikasi area untuk improvement dan 3NF compliance

## Arsitektur Database

### Hubungan Antar Database

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   alamat_db     │    │     orang       │    │     barang      │
│   (Lokasi)      │◄──►│   (Person)      │◄──►│   (Products)    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     waktu       │    │   aplikasi      │    │ surat_laporan   │
│  (Transaksi)    │◄──►│  (Main App)     │◄──►│  (Documents)    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                 │
         ┌───────────────────────┼───────────────────────┐
         │                       │                       │
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│    analytics    │    │    logistics    │    │     finance     │
│   (Analytics)   │    │  (Logistics)    │    │  (Finance)      │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│       hrm       │    │    ecommerce    │    │ communication   │
│      (HR)       │    │ (E-commerce)    │    │ (Communication) │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                 │
                         ┌─────────────────┐
                         │   extensions    │
                         │ (Future-proof)  │
                         └─────────────────┘
```

### Alur Data Utama

1. **Master Data**: `alamat_db`, `orang`, `barang`
2. **Transaksi**: `waktu`, `aplikasi`
3. **Dokumen**: `surat_laporan`
4. **Analytics**: `analytics` (aggregates dari semua transaksi)
5. **Support**: `logistics`, `finance`, `hrm`, `ecommerce`, `communication`
6. **Extensions**: `extensions` (untuk future applications)

## Rekomendasi Implementasi

### Phase 1: Core Implementation
1. Deploy `alamat_db` (sudah ada)
2. Implement `orang_schema.sql`
3. Implement `barang_schema.sql`
4. Implement `waktu_schema.sql`
5. Implement `surat_laporan_schema.sql`
6. Implement `aplikasi_schema.sql`

### Phase 2: Additional Modules
1. Implement `analytics_schema.sql`
2. Implement `logistics_schema.sql`
3. Implement `finance_schema.sql`
4. Implement `hrm_schema.sql`

### Phase 3: Advanced Features
1. Implement `ecommerce_schema.sql`
2. Implement `communication_schema.sql`
3. Implement `extensions_schema.sql`

### Migration Strategy
1. Gunakan `distributor.sql` sebagai baseline
2. Migrasi data ke schema baru secara bertahap
3. `distribusi.sql` untuk implementasi SPPG khusus
4. Gunakan `extensions_schema.sql` untuk custom requirements

## Best Practices

1. **Normalization**: Semua schema sudah mengikuti 3NF (lihat NORMALIZATION_ANALYSIS.md)
2. **Indexing**: Foreign keys dan search fields sudah di-index
3. **Audit Trail**: Gunakan `extensions_schema.sql` untuk tracking
4. **Multi-tenant**: Siap dengan `tenant_management` table
5. **Scalability**: Schema dirancang untuk horizontal scaling

## Catatan Penting

- Semua database menggunakan InnoDB engine untuk transactional support
- Character set: utf8mb4 untuk full Unicode support
- Collation: utf8mb4_unicode_ci untuk case-insensitive comparison
- Foreign key constraints di-enable untuk data integrity
- Created/updated timestamps untuk audit purposes

---

*Dokumentasi ini dibuat pada 19 Januari 2026 dan mencakup semua database schema yang tersedia.*
