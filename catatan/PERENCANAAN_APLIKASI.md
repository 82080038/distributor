# PERENCANAAN APLIKASI SPPG DISTRIBUTION SYSTEM
## Badan Gizi Nasional Partnership & Business Management

---

## 📋 DAFTAR ISI

1. [Analisis Regulasi & Tata Laksana](#analisis-regulasi--tata-laksana)
2. [Profil Bisnis & Kebutuhan](#profil-bisnis--kebutuhan)
3. [Flow Proses Bisnis](#flow-proses-bisnis)
4. [Arsitektur Sistem](#arsitektur-sistem)
5. [Modul Aplikasi](#modul-aplikasi)
6. [Database Design](#database-design)
7. [User Management & Access Control](#user-management--access-control)
8. [Compliance & Audit Trail](#compliance--audit-trail)
9. [Integration Requirements](#integration-requirements)
10. [Implementation Roadmap](#implementation-roadmap)

---

## 🏛️ ANALISIS REGULASI & TATA LAKSANA

### 1.1 Badan Gizi Nasional (BGN) - Framework

**Mandat BGN:**
- Penanggulangan masalah gizi masyarakat
- Distribusi makanan bergizi kepada kelompok rentan
- Standardisasi nilai gizi produk
- Monitoring evaluasi program gizi

**Regulasi Utama:**
- Perpres No. 42/2013 tentang Gerakan Nasional Sadar Gizi
- Permenkes No. 28/2019 tentang Standar Gizi
- Kepmenkes No. HK.01.07/MENKES/1049/2019 tentang Pangan Olahan

### 1.4 Kriteria Pemasok & SOP BGN untuk Dapur SPPG

**Prinsip Umum Pemasok:**
- Mengutamakan bahan baku dari produsen lokal: petani, peternak, nelayan, UMKM, koperasi, BUMDes.
- Mampu menyediakan bahan yang segar, aman, dan bergizi sesuai standar menu MBG.
- Memiliki legalitas usaha dasar dan identitas yang jelas.

**Standar Mutu Bahan Baku:**
- Kesegaran fisik: warna, tekstur, dan aroma sesuai karakter bahan segar.
- Bebas dari tanda pembusukan dan kontaminasi yang terlihat.
- Bahan wajib fortifikasi (tepung terigu, minyak sawit kemasan, garam beryodium, beras terfortifikasi bila tersedia) digunakan sesuai pedoman.
- Untuk bahan hewani dan susu, mengikuti panduan keamanan pangan dan penyimpanan dingin bila diperlukan.

**Standar Pengemasan & Distribusi:**
- Wadah makanan higienis dan food grade, mudah dibersihkan dan digunakan ulang bila diperlukan.
- Kendaraan distribusi mendukung kebersihan dan, untuk bahan sensitif, memungkinkan pengendalian suhu.
- Jarak dan waktu distribusi diatur agar makanan tetap aman dan layak konsumsi setelah keluar dari dapur SPPG.

**Dokumentasi yang Wajib Tersedia:**
- Data identitas pemasok dan legalitas usaha.
- Catatan penerimaan bahan (tanggal, pemasok, jumlah, hasil QC).
- Log distribusi dari distributor ke SPPG dan dari SPPG ke penerima manfaat.
- Berita acara serah terima dan laporan berkala untuk pelaporan ke BGN.

### 1.2 Makanan Bergizi (MBG) - Kriteria

**Kategori MBG:**
1. **Karbohidrat:** Beras, terigu, mi instan
2. **Protein Hewani:** Telur, daging, ikan kaleng
3. **Protein Nabati:** Tempe, tahu, kacang-kacangan
4. **Lemak:** Minyak goreng, margarin
5. **Vitamin & Mineral:** Susu, gula yodium, garam beryodium

**Standar Nilai Gizi per 100g:**
- Energi: 200-300 kcal
- Protein: minimal 5g
- Lemak: maksimal 20g
- Vitamin & Mineral: Sesuai AKG

### 1.3 SPPG (Surat Permintaan Penyaluran Barang) - Prosedur

**Syarat Penerima SPPG:**
- Institusi pemerintah (puskesmas, rumah sakit, sekolah)
- Lembaga sosial (panti asuhan, yayasan)
- Kelompok masyarakat rentan
- Daerah terpencil/krisis pangan

**Dokumen Persyaratan:**
1. Surat permintaan resmi
2. Profil institusi
3. Data penerima manfaat
4. Rencana distribusi
5. Laporan pertanggungjawaban sebelumnya

**Flow Approval SPPG:**
```
Pengajuan → Verifikasi → Review → Approval → Distribusi → Monitoring → Laporan
```

---

## 🏢 PROFIL BISNIS & KEBUTUHAN

### 2.1 Profil Perusahaan Distribusi

**Jenis Usaha:** Distributor Makanan Bergizi Independen

**Model Bisnis:**
- **Procurement:** Pembelian dari petani, peternak, koperasi, dan pabrik makanan bergizi
- **Distribution:** Penjualan dan penyaluran ke institusi penerima SPPG serta pelanggan lain
- **Value Chain:** Petani/Peternak → Distributor → Program SPPG/Customer Lain

**Layanan Utama:**
- Procurement & supplier management
- Inventory & warehouse management
- Sales & order processing (SPPG + non-SPPG)
- Financial management & accounting
- Human resource management
- Profit & loss tracking
- Business intelligence & reporting

**Stakeholder Internal:**
- Owner/Direktur
- Procurement Team
- Sales Team (SPPG & Regular)
- Warehouse Team
- Finance/Accounting Team
- Delivery/Logistics Team
- HR/Admin Team

**Stakeholder Eksternal:**
- Petani, peternak, koperasi, dan supplier makanan bergizi
- Customer SPPG (Institusi Pemerintah/Sosial: sekolah, puskesmas, dsb.)
- Customer Regular (Bisnis Lain)
- Bank & Financial Institutions
- Tax Authorities
- Auditor Eksternal

### 2.2 Kebutuhan Bisnis

**🛒 Procurement & Supply Chain:**
- ✅ Supplier management & evaluation
- ✅ Purchase order management
- ✅ Price comparison & negotiation tracking
- ✅ Quality control incoming goods
- ✅ Supplier payment tracking
- ✅ Procurement analytics

**💰 Sales & Customer Management:**
- ✅ SPPG order processing (institusi)
- ✅ Regular sales order processing (bisnis lain)
- ✅ Customer database management
- ✅ Pricing management (per customer type)
- ✅ Credit limit & payment terms
- ✅ Sales performance tracking

**📦 Inventory & Warehouse:**
- ✅ Real-time stock monitoring
- ✅ Multi-warehouse management
- ✅ Batch/lot tracking
- ✅ Expiry management
- ✅ Stock valuation (FIFO/LIFO)
- ✅ Warehouse optimization

**🚚 Logistics & Distribution:**
- ✅ Order fulfillment (SPPG & regular)
- ✅ Route planning & optimization
- ✅ Delivery tracking
- ✅ Proof of delivery (digital)
- ✅ Fleet management
- ✅ Shipping cost analysis

**💸 Financial & Accounting:**
- ✅ Complete accounting system
- ✅ Purchase & sales journal
- ✅ Cash flow management
- ✅ Accounts payable/receivable
- ✅ Tax calculation & reporting
- ✅ Financial statement generation
- ✅ Cost center management
- ✅ Profit analysis per product/customer

**👥 Human Resource Management:**
- ✅ Employee database & profiles
- ✅ Payroll management
- ✅ Performance tracking (KPI)
- ✅ Commission & bonus calculation
- ✅ Attendance & leave management
- ✅ Training & certification tracking
- ✅ Team productivity analysis

**📊 Business Intelligence:**
- ✅ Profit & loss analysis
- ✅ Cost analysis (per unit, per order)
- ✅ Customer profitability analysis
- ✅ Product performance analysis
- ✅ Sales trend analysis
- ✅ Operational efficiency metrics
- ✅ Custom reporting (BGN format available)

### 2.3 Peran Distributor dalam Ekosistem SPPG

Peran utama aplikasi ini adalah membantu pemilik usaha distribusi untuk:
- Menterjemahkan kebutuhan SPPG (jenis bahan, kualitas gizi, volume, jadwal) menjadi rencana pasokan konkret dari petani/peternak.
- Memilih kombinasi pemasok yang optimal (siapa, dari wilayah mana, kapan beli, berapa harga) dengan mempertimbangkan harga, kapasitas, kualitas, dan risiko.
- Mengelola seluruh siklus: beli dari petani/peternak → simpan di gudang → kirim ke titik SPPG → hitung laba rugi dan kinerja pemasok.
- Merekam setiap permintaan resmi SPPG sebagai sales order bertipe `sppg`, sehingga setiap order memiliki nomor, jadwal pengiriman, nilai penjualan, dan dapat ditelusuri kembali ke batch pembelian dari petani.

Dengan demikian, aplikasi tidak hanya mempermudah administrasi, tetapi menjadi “otak operasional” bagi distributor dalam ekosistem program SPPG.

### 2.4 Tujuan Aplikasi dari Sudut Pandang Pengusaha

Dari perspektif pemilik usaha distribusi, tujuan utama aplikasi ini:
- Menjamin ketersediaan bahan baku untuk memenuhi permintaan SPPG tepat waktu, tepat kualitas, dan tepat volume.
- Mengoptimalkan keuntungan:
  - Menekan harga beli tanpa mengorbankan kualitas dan hubungan dengan petani/peternak.
  - Mengendalikan biaya logistik, gudang, dan susut.
  - Memaksimalkan margin per produk, per pemasok, dan per program SPPG.
- Menyediakan visibilitas real-time:
  - Berapa stok yang dimiliki sekarang vs kebutuhan ke depan.
  - Berapa keuntungan yang sudah tercapai per periode.
  - Pemasok mana yang paling menguntungkan dan paling andal.
- Menggunakan data dan AI untuk pengambilan keputusan:
  - Prediksi harga komoditas.
  - Rekomendasi rencana pembelian dan distribusi.
  - Analisis risiko (volatilitas harga, potensi gagal pasok).
- Menghubungkan setiap order SPPG dengan batch pembelian dan biaya riil di lapangan, sehingga perhitungan laba rugi per order dapat dilakukan secara otomatis berdasarkan data operasional.

**🔒 Compliance & Audit:**
- ✅ Audit trail lengkap
- ✅ Document management
- ✅ Tax compliance reporting
- ✅ BGN format reporting (untuk SPPG)
- ✅ Quality assurance tracking
- ✅ Internal control system

---

## 🔄 FLOW PROSES BISNIS

### 3.1 Flow Utama SPPG (Customer Institusi)

```mermaid
graph TD
    A[Institusi Ajukan SPPG] --> B[Customer Verification]
    B --> C{Customer Valid?}
    C -->|Tidak| D[Reject & Inform]
    C -->|Ya| E[Order Processing]
    E --> F[Check Inventory]
    F --> G{Stock Available?}
    G -->|Tidak| H[Procurement Planning]
    G -->|Ya| I[Price Calculation]
    H --> I
    I --> J[Create Sales Order]
    J --> K[Customer Approval]
    K --> L{Order Approved?}
    L -->|Tidak| M[Negotiation/Revision]
    L -->|Ya| N[Order Confirmation]
    M --> N
    N --> O[Prepare Delivery]
    O --> P[Quality Check]
    P --> Q[Packaging & Labeling]
    Q --> R[Schedule Delivery]
    R --> S[Delivery Execution]
    S --> T[Proof of Delivery]
    T --> U[Update Inventory]
    U --> V[Generate Invoice]
    V --> W[Accounts Receivable]
    W --> X[Payment Collection]
    X --> Y[Revenue Recognition]
    Y --> Z[Generate BGN Format Report]
    Z --> AA[Archive Documents]
    D --> BB[End]
    AA --> BB
```

Dalam konteks distributor, node **Create Sales Order** pada flow di atas direalisasikan sebagai pencatatan order SPPG di modul sales order dengan `order_type = 'sppg'`. Order ini kemudian dihubungkan ke batch pembelian dari petani/pemasok dan ke pengiriman ke titik SPPG, sehingga status pemenuhan dan profit per order dapat dilacak.

### 3.2 Flow Regular Sales (Customer Bisnis Lain)

```mermaid
graph TD
    A[Customer Request] --> B[Create Quotation]
    B --> C[Price Negotiation]
    C --> D{Agreement?}
    D -->|Tidak| E[Revise Quotation]
    D -->|Ya| F[Create Sales Order]
    E --> C
    F --> G[Check Credit Limit]
    G --> H{Credit OK?}
    H -->|Tidak| I[Request Payment/Prepayment]
    H -->|Ya| J[Process Order]
    I --> J
    J --> K[Inventory Allocation]
    K --> L[Order Fulfillment]
    L --> M[Delivery/Pickup]
    M --> N[Invoice Generation]
    N --> O[Payment Processing]
    O --> P[Revenue Recording]
    P --> Q[Customer Payment Update]
```

### 3.3 Flow Procurement & Supply Chain

```mermaid
graph TD
    A[Identify Need] --> B[Supplier Selection]
    B --> C[Request Quotation]
    C --> D[Compare Offers]
    D --> E[Negotiate Terms]
    E --> F[Create Purchase Order]
    F --> G[Supplier Confirmation]
    G --> H[Goods Receiving]
    H --> I[Quality Inspection]
    I --> J{QC Passed?}
    J -->|Tidak| K[Return to Supplier]
    J -->|Ya| L[Record in Inventory]
    L --> M[Update Stock Valuation]
    M --> N[Process Payment]
    N --> O[Update Supplier Account]
    O --> P[Record Cost Analysis]
    P --> Q[Update Product Costing]
    K --> R[Claim/Dispute Process]
    R --> S[Find Alternative Supplier]
    S --> B
```

### 3.7 Flow Verifikasi Bahan Baku dan Kepatuhan BGN

Flow ini menghubungkan proses pembelian dari petani/peternak dengan kewajiban verifikasi BGN sebelum bahan digunakan untuk produksi makanan SPPG.

```mermaid
graph TD
    A[Terima Barang dari Petani/Peternak] --> B[Input Data Penerimaan]
    B --> C[Periksa Dokumen Pemasok]
    C --> D{Dokumen Lengkap & Valid?}
    D -->|Tidak| E[Tolak Batch & Catat Alasan]
    D -->|Ya| F[Quality Check Fisik & Mutu]
    F --> G{Lulus QC?}
    G -->|Tidak| H[Return/Claim ke Pemasok]
    G -->|Ya| I[Update Stok & Batch Tracking]
    I --> J[Generate Log Kualitas Bahan]
    J --> K[Hubungkan ke Rencana Produksi SPPG]
    K --> L[Siapkan Paket Dokumen BGN (BAST, Log, QC)]
    L --> M[Siap untuk Produksi & Distribusi SPPG]
    E --> N[Evaluasi Kinerja Pemasok]
    H --> N
```

### 3.6 Flow Distribusi SPPG Berbasis Petani/Peternak

Flow ini menekankan alur lengkap dari permintaan SPPG sampai pembelian dari petani/peternak dan distribusi kembali ke titik SPPG:

```mermaid
graph TD
    A[SPPG Mengajukan Kebutuhan Bahan] --> B[Agregasi Kebutuhan per Produk & Periode]
    B --> C[Hitung Kebutuhan Pasokan Distributor]
    C --> D[Analisis Pemasok: Petani/Peternak/Koperasi]
    D --> E{Kombinasi Pemasok Optimal?}
    E -->|Belum| F[Simulasi Harga & Biaya Logistik]
    F --> D
    E -->|Ya| G[Buat Rencana Pembelian per Pemasok]
    G --> H[Terbitkan Purchase Order ke Petani/Peternak]
    H --> I[Penerimaan Barang di Gudang]
    I --> J[Quality Check & Update Stok]
    J --> K[Susun Rencana Pengiriman ke Titik SPPG]
    K --> L[Eksekusi Pengiriman]
    L --> M[Konfirmasi Penerimaan di Titik SPPG]
    M --> N[Hitung Laba Rugi per Pengiriman dan per Pemasok]
    N --> O[Analitik Kinerja Pemasok & Margin]
```

Flow ini menjadi jembatan antara kebutuhan SPPG dan realitas operasional distributor yang membeli langsung dari hulu (petani/peternak) lalu memasok ke hilir (program SPPG).

---

## 📘 PANDUAN PENGGUNAAN APLIKASI DISTRIBUTOR

### 4.1 Alur Utama Pengguna Distributor

1. Registrasi dan login sebagai pengguna distributor.
2. Mendaftarkan pemasok: petani, peternak, koperasi, dan UMKM.
3. Mendaftarkan komoditas yang disuplai, termasuk satuan, harga, dan kapasitas.
4. Menginput permintaan SPPG yang diterima sebagai sales order bertipe `sppg` dan mengubahnya menjadi rencana pasokan yang terhubung ke batch pembelian.
5. Menggunakan modul rekomendasi untuk memilih kombinasi pemasok yang optimal.
6. Membuat purchase order ke pemasok terpilih.
7. Melakukan penerimaan barang, quality check, dan mencatat hasilnya di sistem.
8. Menyusun rencana pengiriman ke titik SPPG dan memantau status pengiriman.
9. Mengelola dokumen yang diwajibkan BGN sebelum dan sesudah distribusi.
10. Melihat laporan laba rugi per batch, per pemasok, dan per program SPPG.

### 4.2 Perspektif Kepatuhan BGN

Dalam setiap langkah di atas, aplikasi membantu memastikan:
- Bahan baku berasal dari pemasok yang terdokumentasi dengan baik.
- Hasil quality check tercatat dan dapat diaudit.
- Pengiriman ke SPPG terekam dengan waktu dan rute yang jelas.
- Dokumen BGN (BAST, log distribusi, log kualitas) terbentuk dari data operasional tanpa input manual berulang.

---

### 4.3 Workflow Purchasing dan Gudang untuk Pesanan SPPG

#### 4.3.1 Purchasing dari Permintaan SPPG
1. Sales order bertipe `sppg` dibuat berdasarkan permintaan menu dan volume dari SPPG (per periode pengiriman).
2. Sistem mengagregasi kebutuhan per `material_code` SPPG (misalnya `SPPG-B01-001`, `SPPG-O03-001`) dengan mempertimbangkan konversi porsi ke satuan pembelian.
3. Untuk setiap `material_code`, sistem mencari kandidat komoditas di sisi hulu menggunakan:
   - Mapping `sppg_material_plu_mappings` untuk menemukan PLU yang relevan.
   - Data pemasok dan kapasitas dari petani, peternak, koperasi, dan UMKM.
4. Pengguna purchasing melihat layar rekomendasi yang menampilkan:
   - Kolom SPPG Material (kode, nama, ringkasan nutrisi utama).
   - Kolom PLU (kode, nama produk, satuan standar).
   - Kapasitas dan harga dari pemasok yang tersedia.
   - Estimasi biaya total dan margin kotor per material.
5. Pengguna memilih kombinasi pemasok dan kuantitas, lalu sistem membentuk draft purchase order per pemasok.
6. Setelah disetujui, purchase order dikirim ke pemasok dan statusnya dipantau sampai barang tiba di gudang.

#### 4.3.2 Workflow Gudang
1. Barang datang ke gudang dengan referensi purchase order dan PLU atau SKU internal.
2. Petugas gudang melakukan penerimaan:
   - Scan barcode/PLU atau SKU.
   - Verifikasi jumlah, kualitas, dan masa simpan.
   - Konfirmasi `sppg_material_code` yang dicakup barang tersebut.
3. Sistem menambah stok di `inventory` per produk dan gudang, sekaligus menyimpan hubungan ke batch sourcing untuk keperluan alokasi biaya.
4. Quality check direkam dengan skor dan catatan; hasilnya memengaruhi performa pemasok dan keputusan purchasing berikutnya.
5. Ketika ada sales order SPPG yang siap diproses:
   - Sistem menghasilkan picking list per gudang berdasarkan kebutuhan `sppg_material_code`.
   - Petugas melakukan picking berdasarkan lokasi stok, lalu packing sesuai kebutuhan SPPG.
6. Setelah pengiriman, stok berkurang dan sistem mencatat keterkaitan antara sales order, batch sourcing, dan material yang digunakan.

#### 4.3.3 Keterkaitan dengan PLU, SPPG Material, dan Profit
- Mapping `sppg_material_plu_mappings` memastikan setiap material SPPG yang relevan bisa diturunkan ke komoditas pasar berbasis PLU untuk keperluan purchasing dan analitik harga.
- View `v_sppg_material_plu_nutrition` menyediakan pandangan terpadu: material SPPG, PLU, satuan, dan data nutrisi, sehingga purchasing dan gudang dapat menjaga kepatuhan gizi sambil tetap efisien secara biaya.
- View `v_sppg_order_item_profit` memungkinkan analisis laba rugi per baris sales order:
  - Per `sppg_material_code` dan PLU.
  - Per kombinasi pemasok dan batch sourcing.
  - Per periode dan program SPPG tertentu.

#### 4.3.4 Perencanaan Pembelian Berbasis Prediksi Harga
- Tabel `sppg_daily_material_demand` menyimpan kebutuhan harian per SPPG dan per `material_code` dalam gram, termasuk informasi `target_group` dan jumlah penerima.
- View `v_sppg_material_demand_weekly` mengagregasikan data tersebut per minggu dan per SPPG menjadi total kebutuhan kilogram per material, sehingga tim purchasing tahu kebutuhan mingguan bersih per bahan kunci (beras, protein hewani, sayur, buah, dsb).
- View `v_sppg_material_demand_monthly` melakukan agregasi serupa di tingkat bulanan untuk keperluan perencanaan kontrak dan evaluasi jangka panjang.
- Modul AI `StockPricePredictor` menggunakan hasil view demand tersebut (per material per periode) sebagai input demand, lalu menggabungkannya dengan prediksi harga komoditas per bahan untuk menghasilkan rekomendasi pembelian:
  - Untuk setiap material, sistem menghitung kekurangan stok terhadap kebutuhan minggu/bulan berikutnya.
  - Berdasarkan tren harga yang diprediksi, masa simpan bahan dari `SPPG_NUTRITION_GUIDE.md`, dan kapasitas gudang, sistem menentukan apakah perlu membeli lebih awal (saat harga masih rendah) atau menunda sebagian pembelian.
  - Output yang muncul di layar rekomendasi purchasing berupa ringkasan seperti: “Minggu ini beli beras SPPG-B01-001 sebanyak X kg, ayam SPPG-B03-002 sebanyak Y kg, sayur SPPG-B04-001 sebanyak Z kg” lengkap dengan alasan singkat: kebutuhan nutrisi, prediksi harga, dan batas shelf life.
- Dengan kombinasi view kebutuhan bahan dan modul prediksi harga ini, keputusan pembelian berjalan mirip sistem saham: tidak hanya memenuhi kebutuhan gizi, tetapi juga mengoptimalkan waktu dan volume pembelian untuk memaksimalkan margin dan meminimalkan risiko kenaikan harga atau pemborosan.

#### 4.3.5 Integrasi Menu Harian ke Kebutuhan Bahan
- Master menu disimpan di `sppg_menus` dan komposisi resep per porsi disimpan di `sppg_menu_items`, sehingga setiap menu SPPG dapat di-breakdown menjadi daftar `material_code` dan jumlah gram per porsi.
- Jadwal menu harian per SPPG dicatat di `sppg_menu_logs` dengan informasi tanggal, jenis makan (pagi/siang/malam), menu yang disajikan, jumlah porsi, dan jumlah penerima.
- Berdasarkan jadwal tersebut, sistem menghitung kebutuhan bahan harian dengan cara mengalikan `quantity_grams_per_portion` pada `sppg_menu_items` dengan jumlah porsi dari `sppg_menu_logs`, lalu menjumlahkannya per `material_code` dan menyimpannya sebagai `total_quantity_grams` di `sppg_daily_material_demand`.
- Sebuah query agregasi untuk 7 hari ke depan per SPPG mengubah jadwal menu mingguan menjadi kebutuhan bahan mingguan (dalam kilogram) per `material_code`, yang kemudian dapat langsung dipakai oleh modul rekomendasi purchasing dan modul prediksi harga untuk menyusun rencana pembelian mingguan.

#### 4.3.6 Desain Endpoint API Inti

Untuk menghubungkan modul perencanaan menu dan pembelian dengan aplikasi front-end, disiapkan tiga kelompok endpoint utama.

**1) Input Jadwal Menu SPPG**

- Method: `POST`
- Route: `/api/sppg/menu-schedule/bulk`
- Tujuan: menerima dan menyimpan jadwal menu harian per SPPG ke `sppg_menu_logs`, sekaligus menghitung kebutuhan bahan harian ke `sppg_daily_material_demand`.

Contoh payload:

```json
{
  "sppg_id": "SPPG-001",
  "items": [
    {
      "menu_date": "2026-02-01",
      "meal_type": "lunch",
      "menu_code": "MENU-GIZI-001",
      "target_group": "anak",
      "portions": 150,
      "beneficiaries_count": 140
    },
    {
      "menu_date": "2026-02-01",
      "meal_type": "dinner",
      "menu_code": "MENU-GIZI-002",
      "target_group": "anak",
      "portions": 150,
      "beneficiaries_count": 140
    }
  ]
}
```

Sketsa logika backend:
- Validasi bahwa `menu_code` sudah ada di `sppg_menus`.
- Resolve `menu_id` dari `menu_code`.
- Insert baris ke `sppg_menu_logs`.
- Ambil komposisi bahan dari `sppg_menu_items` dan hitung total gram per bahan.
- Upsert hasilnya ke `sppg_daily_material_demand` per kombinasi `sppg_id`, `demand_date`, `material_code`, `target_group`.

Contoh response ringkas:

```json
{
  "sppg_id": "SPPG-001",
  "inserted_logs": 2,
  "affected_material_rows": 8
}
```

**2) Baca Kebutuhan Bahan Mingguan/Bulanan**

- Method: `GET`
- Route: `/api/sppg/material-demand`
- Tujuan: membaca kebutuhan bahan teragregasi dari `v_sppg_material_demand_weekly` atau `v_sppg_material_demand_monthly`.

Parameter query yang disarankan:
- `sppg_id` (wajib)
- `period_type` = `weekly` atau `monthly`
- Untuk `weekly`: `week` dalam format `YYYYWW` atau pasangan `start_date` dan `end_date`.
- Untuk `monthly`: `month` dalam format `YYYY-MM`.

Contoh request:

`GET /api/sppg/material-demand?sppg_id=SPPG-001&period_type=weekly&week=202605`

Contoh response (weekly):

```json
{
  "sppg_id": "SPPG-001",
  "period_type": "weekly",
  "period_key": "202605",
  "materials": [
    {
      "material_code": "SPPG-B01-001",
      "material_name": "Beras Fortifikasi 5kg",
      "target_group": "anak",
      "total_quantity_kg": 350.5,
      "total_beneficiaries": 140
    },
    {
      "material_code": "SPPG-D01-001",
      "material_name": "Daging Ayam Segar",
      "target_group": "anak",
      "total_quantity_kg": 210.0,
      "total_beneficiaries": 140
    }
  ]
}
```

**3) Endpoint Rekomendasi Pembelian Berbasis Prediksi Harga**

- Method: `GET`
- Route: `/api/sppg/purchase-recommendations`
- Tujuan: mengembalikan rekomendasi pembelian bahan berdasarkan kebutuhan bahan per periode, prediksi harga dari `StockPricePredictor`, batas shelf life, kapasitas gudang, dan stok yang sudah ada.

Parameter query yang disarankan:
- `sppg_id` (opsional, jika kosong berarti agregat multi-SPPG).
- `horizon_weeks` (misalnya 1, 2, atau 4).
- `start_date` (tanggal awal horizon).
- `risk_profile` (misalnya `conservative`, `balanced`, `aggressive`).

Contoh request:

`GET /api/sppg/purchase-recommendations?horizon_weeks=2&start_date=2026-02-01&risk_profile=balanced`

Contoh response:

```json
{
  "horizon_weeks": 2,
  "start_date": "2026-02-01",
  "recommendations": [
    {
      "material_code": "SPPG-B01-001",
      "material_name": "Beras Fortifikasi 5kg",
      "total_need_kg": 700.0,
      "current_stock_kg": 200.0,
      "inbound_po_kg": 100.0,
      "net_requirement_kg": 400.0,
      "recommended_buy_kg": 450.0,
      "recommended_purchase_window_start": "2026-01-28",
      "recommended_purchase_window_end": "2026-01-30",
      "forecast_price_now": 11000,
      "forecast_price_next_week": 11800,
      "shelf_life_days": 180,
      "reason": "Harga diprediksi naik, shelf life panjang, stok saat ini belum cukup."
    },
    {
      "material_code": "SPPG-D01-001",
      "material_name": "Daging Ayam Segar",
      "total_need_kg": 300.0,
      "current_stock_kg": 50.0,
      "inbound_po_kg": 80.0,
      "net_requirement_kg": 170.0,
      "recommended_buy_kg": 120.0,
      "recommended_purchase_window_start": "2026-02-02",
      "recommended_purchase_window_end": "2026-02-04",
      "forecast_price_now": 35000,
      "forecast_price_next_week": 34000,
      "shelf_life_days": 5,
      "reason": "Bahan sangat mudah rusak, beli bertahap meski harga turun sedikit."
    }
  ]
}
```

Endpoint ini dapat dikembangkan lebih lanjut untuk mengembalikan detail kandidat pemasok dan pembagian volume per pemasok sesuai modul sourcing.

#### 4.3.7 Algoritma Rekomendasi Pembelian

Algoritma rekomendasi pembelian yang mendukung endpoint di atas bekerja secara garis besar sebagai berikut.

1. Estimasi kebutuhan bersih per bahan
   - Ambil kebutuhan bahan per periode dari `v_sppg_material_demand_weekly` atau `v_sppg_material_demand_monthly`.
   - Ambil stok saat ini dan stok mengikat dari modul inventory (`current_inventory`) beserta inbound dari purchase order yang belum datang.
   - Hitung kebutuhan bersih per bahan:
     - `net_requirement = total_need - current_stock - inbound_po`, dengan batas bawah nol.

2. Hitung batas pembelian karena shelf life dan kapasitas gudang
   - Ambil informasi shelf life dan kebutuhan suhu penyimpanan dari `shelf_life_months` di `sppg_materials` atau dari `plu_codes`.
   - Hitung horizon aman penyimpanan untuk setiap bahan:
     - Bahan sangat mudah rusak: hanya beberapa hari ke depan.
     - Bahan kering: bisa mencakup beberapa minggu atau bulan.
   - Terapkan batas maksimum pembelian:
     - `max_buy = kebutuhan_periode_yang_masih_aman_disimpan`.
   - Jika kapasitas gudang per kategori bahan terbatas, gunakan informasi kapasitas tersebut untuk menahan pembelian berlebih.

3. Ambil prediksi harga dan bentuk kurva harga
   - Untuk setiap `material_code` atau PLU yang terkait, panggil modul `StockPricePredictor` dengan horizon waktu yang sama dengan `horizon_weeks`.
   - Dapatkan deret harga harian atau mingguan yang diprediksi.
   - Turunkan indikator:
     - Tren (naik, turun, stabil).
     - Volatilitas (tinggi/rendah).

4. Simulasi skenario pembelian
   - Bentuk beberapa skenario sederhana per bahan:
     - Skenario A: beli sekarang untuk seluruh `net_requirement` (selama tidak melampaui `max_buy`).
     - Skenario B: beli sebagian sekarang, sebagian mendekati tanggal kebutuhan.
     - Skenario C: beli minimal sekarang, mayoritas mendekati tanggal kebutuhan.
   - Untuk setiap skenario, hitung:
     - Estimasi biaya total pembelian dengan menggunakan harga prediksi pada tanggal pembelian.
     - Risiko pembusukan (semakin lama disimpan, risiko lebih tinggi).
     - Risiko kenaikan harga jika menunda pembelian.

5. Skoring dan pemilihan skenario
   - Definisikan fungsi skor yang menggabungkan:
     - Biaya total (semakin rendah semakin baik).
     - Risiko pembusukan (semakin rendah semakin baik).
     - Risiko kenaikan harga (bergantung profil risiko).
   - Sesuaikan bobot skor berdasarkan `risk_profile`:
     - `conservative`: menekankan keamanan stok dan risiko kekurangan stok, cenderung beli lebih awal.
     - `balanced`: menyeimbangkan antara harga dan risiko stok.
     - `aggressive`: menekankan optimasi harga, lebih berani menunda pembelian.
   - Pilih skenario dengan skor terbaik per bahan, dan turunkan ke angka `recommended_buy_kg` beserta jendela waktu pembelian yang disarankan.

6. Integrasi dengan parameter gizi
   - Pastikan kombinasi bahan yang direkomendasikan tetap memenuhi kebutuhan gizi agregat per kelompok target:
     - Protein, karbohidrat, lemak, serat dan kalori target dapat diambil dari kombinasi menu dan komposisi gizi di `sppg_materials` atau view nutrisi.
   - Jika ada penghematan biaya dengan mengganti bahan (misalnya beras jenis A ke jenis B) namun mengurangi kualitas gizi di bawah batas BGN, algoritma menolak skenario tersebut.

7. Output dan explainability
   - Untuk setiap bahan, algoritma mengeluarkan alasan singkat:
     - Ringkasan kebutuhan, stok, dan inbound.
     - Ringkasan tren harga dan batas shelf life.
     - Alasan memilih jumlah dan waktu pembelian.
   - Informasi ini ditampilkan di UI dan dikirim sebagai bagian field `reason` di response API, sehingga tim purchasing bisa memahami logika di balik rekomendasi dan mengambil keputusan akhir dengan lebih percaya diri.

## 🖥️ DESAIN UI MODUL DISTRIBUTOR

### 5.1 Dashboard Utama

- Panel ikhtisar dengan kartu statistik:
  - Ringkasan order SPPG: order baru hari ini, in progress, selesai.
  - Status pemenuhan order: persentase volume yang sudah dialokasikan dan sudah lulus QC.
  - Ringkasan pengiriman ke SPPG: in transit, on time, delayed.
  - Snapshot profit: estimasi laba kotor per periode dan per pelanggan utama.
- Notifikasi dokumen yang akan atau sudah mendekati masa expired:
  - Daftar ringkas dengan warna per status dan tanggal kedaluwarsa.
  - Aksi cepat untuk membuka detail dokumen.
- Quick actions:
  - Tombol “Input Order SPPG”, “Upload Dokumen”, “Buat Quality Check”, dan “Lacak Pengiriman”.
  - Dapat diakses juga via shortcut keyboard.

### 5.2 Manajemen Dokumen

- Tabel daftar dokumen supplier:
  - Kolom: Nama dokumen, tipe (BGN/QC), supplier, status, upload date, expiration.
  - Filter dinamis per kolom dan kombinasi filter.
- Upload dokumen:
  - Area drag & drop untuk upload banyak file sekaligus.
  - Mapping dokumen ke supplier dan tipe dilakukan di panel kanan.
- Preview dokumen inline:
  - Tampilan PDF dan gambar langsung di modal preview tanpa meninggalkan halaman.
- Approval cepat:
  - Tombol “Approve/Reject” satu klik dengan opsi menambahkan catatan.
- Pencarian full-text:
  - Input search yang menyorot (highlight) kata kunci di baris hasil.

### 5.3 Modul Quality Check

- Form input QC:
  - Auto-complete batch_number dan material berdasarkan data batch dan materials.
  - Menampilkan informasi ringkas supplier, produk, dan batch.
- Template parameter uji:
  - Parameter otomatis tergantung material type (misal: daging, sayur, susu).
- Input hasil uji:
  - Field dengan validasi range nilai yang diperbolehkan.
  - Status otomatis disarankan (passed/failed) berdasarkan hasil.
- Visualisasi kualitas:
  - Grafik tren kualitas per material dan per supplier.
  - Filter periode dan jenis parameter uji.
- Generate report:
  - Tombol untuk membuat laporan QC per batch atau per periode yang siap diekspor.

### 5.4 Delivery Tracking

- Timeline visual:
  - Menampilkan tahapan pengiriman dari “Scheduled” hingga “Delivered”.
  - Status ditandai dengan ikon dan warna berbeda.
- Peta pengiriman:
  - Integrasi peta untuk melihat posisi armada secara real-time.
  - Pengiriman yang terlambat diberi indikator khusus.
- Alert otomatis:
  - Notifikasi ketika status berubah menjadi “delayed” atau “cancelled”.
- Dokumen terkait:
  - Panel samping yang menunjukan dokumen supplier dan QC yang terkait dengan pengiriman.

### 5.5 Fitur Tambahan & Usability

- Shortcut keyboard:
  - Kombinasi kunci untuk aksi sering digunakan seperti buka form upload, simpan QC, atau fokus ke pencarian.
- Mode tampilan:
  - Toggle dark/light mode agar nyaman digunakan di berbagai kondisi kerja.
- Dashboard yang bisa disesuaikan:
  - Pengguna dapat mengatur widget yang muncul di dashboard sesuai perannya.
- Ekspor data:
  - Ekspor tabel dokumen, QC, dan tracking ke Excel atau PDF.
- Audit trail:
  - Setiap perubahan status dokumen, hasil QC, dan tracking tersimpan dalam log sehingga mudah diaudit.

### 5.6 Laporan Profit per Order SPPG

- Filter dan kontrol di bagian atas:
  - Filter periode (tanggal order, tanggal delivery).
  - Filter customer (SPPG tertentu atau semua).
  - Filter status order (draft, confirmed, delivered, paid).
  - Tombol toggle tampilan ringkas/detail.
- Kartu ringkasan di atas tabel:
  - Total revenue SPPG pada periode terpilih.
  - Total biaya pembelian yang dialokasikan.
  - Total laba kotor dan rata-rata margin (%).
  - Jumlah order dan jumlah pelanggan SPPG yang aktif.
- Tabel utama “Profit per Order”:
  - Kolom: Order No, Tgl Order, Customer, Status, Revenue, Biaya, Laba, Margin %, Qty Terjual, Qty Terpenuhi (%), Jumlah Batch, Jumlah Pemasok, Indikator QC/BGN.
  - Setiap baris bisa diklik untuk membuka detail order.
- Panel detail order (side drawer atau modal):
  - Rincian produk yang dijual ke SPPG (sales_order_items).
  - Daftar batch pembelian yang dialokasikan ke order ini, termasuk pemasok dan harga beli.
  - Ringkasan QC dan status dokumen untuk batch yang dipakai.
  - Timeline dari order dibuat sampai pengiriman delivered.
- Visualisasi grafis (opsional tahap awal):
  - Grafik batang laba per order (top N order paling untung/rugi).
  - Grafik pie kontribusi pelanggan SPPG terhadap total laba periode.
  - Grafik tren margin rata-rata per bulan.

Kriteria kesuksesan UX yang dituju:
- Mengurangi waktu penyelesaian task seputar dokumen dan QC sebesar ±40%.
- Menekan error input data menjadi di bawah 1% melalui validasi dan auto-complete.
- Mencapai rating kepuasan pengguna di atas 4.5/5 melalui antarmuka yang sederhana dan konsisten.
- Membatasi waktu training user baru di bawah 30 menit karena alur yang intuitif dan terfokus pada tugas harian mereka.

### 5.7 Modul Analitik Profitabilitas

- Tujuan modul:
  - Memberikan insight tingkat tinggi tentang profitabilitas program SPPG dari sudut pandang distributor.
  - Menjawab pertanyaan bisnis seperti: pelanggan SPPG mana yang paling menguntungkan, pemasok mana yang paling efisien, dan bagaimana tren margin per bulan.
- Sumber data utama:
  - View `v_sppg_orders` untuk ringkasan order SPPG.
  - Tabel `sppg_order_batch_allocations` untuk alokasi biaya batch ke order.
  - Tabel `distributor_batch_lines` dan `farmers` untuk analisis kontribusi pemasok.
- Jenis analitik yang disajikan:
  - Profit per customer SPPG, dengan filter periode (bulan/tahun) dan status order.
  - Profit per pemasok/petani, termasuk kontribusi biaya dan laba yang dialokasikan.
  - Tren margin rata-rata per bulan dan per program SPPG.
- Contoh keputusan yang didukung:
  - Menentukan pelanggan mana yang perlu dinegosiasi ulang harganya atau syarat kontraknya.
  - Mengidentifikasi pemasok yang perlu diprioritaskan, dikembangkan, atau diganti.
  - Menentukan target margin per produk dan per program SPPG di periode berikutnya.
- Komponen UI utama:
  - Widget ringkasan KPI: kartu revenue total, laba kotor, margin rata-rata, jumlah customer aktif.
  - Grafik tren margin per bulan: line chart dengan sumbu X waktu dan sumbu Y margin (%), dapat difilter per customer atau keseluruhan.
  - Grafik batang profit per customer: bar chart yang menampilkan top N customer dengan laba tertinggi dan terendah.
  - Grafik batang profit per pemasok: bar chart yang menunjukkan pemasok dengan kontribusi laba paling besar dan yang menekan margin.
  - Tabel analitik: tabel drill-down yang dapat beralih tampilan antara mode per customer dan per pemasok, dengan kolom revenue, biaya, laba, margin, dan periode.
  - Filter global di sisi kiri atau atas: pemilihan periode (bulan/tahun), customer SPPG, dan pemasok tertentu.
- Skenario keputusan bisnis (use case):
  - Review bulanan: pemilik usaha membuka modul ini di awal bulan untuk melihat margin bulan sebelumnya dan memutuskan apakah perlu penyesuaian harga jual ke SPPG tertentu.
  - Evaluasi pemasok: manajer pembelian menggunakan grafik profit per pemasok untuk mengidentifikasi pemasok yang sering mengurangi margin dan menyiapkan strategi negosiasi ulang atau mencari alternatif pemasok.
  - Perencanaan kontrak: sebelum memperpanjang kontrak dengan SPPG atau pemasok kunci, tim keuangan meninjau tren margin beberapa bulan terakhir untuk menentukan syarat kontrak yang realistis dan menguntungkan.
  - Deteksi anomali: jika margin tiba-tiba turun di bulan tertentu, pemilik usaha menggunakan tabel analitik untuk menelusuri order, customer, dan pemasok mana yang menyebabkan penurunan dan tindakan korektif apa yang perlu dilakukan.

---

### 3.4 Flow Financial & Accounting

```mermaid
graph TD
    A[Sales Order] --> B[Calculate Revenue]
    B --> C[Record Sales Journal]
    C --> D[Update Accounts Receivable]
    D --> E[Generate Invoice]
    E --> F[Send to Customer]
    F --> G[Payment Collection]
    G --> H{Payment Received?}
    H -->|Tidak| I[Follow Up/Dunning]
    H -->|Ya| J[Record Payment]
    J --> K[Update Cash/Bank]
    K --> L[Reconcile Accounts]
    L --> M[Update Financial Statements]
    
    N[Purchase Order] --> O[Record Cost]
    O --> P[Update Inventory Value]
    P --> Q[Record Accounts Payable]
    Q --> R[Process Payment]
    R --> S[Update Cash/Bank]
    S --> T[Reconcile Accounts]
    T --> M
    
    I --> G
```

### 3.5 Flow Human Resource & Payroll

```mermaid
graph TD
    A[Employee Data] --> B[Time Attendance]
    B --> C[Calculate Working Hours]
    C --> D[Calculate Base Salary]
    D --> E[Add Overtime/Bonus]
    E --> F[Calculate Commission]
    F --> G[Calculate Benefits]
    G --> H[Calculate Tax Deductions]
    H --> I[Net Salary Calculation]
    I --> J[Generate Payroll]
    J --> K[Bank Transfer]
    K --> L[Update Financial Records]
    L --> M[Generate Tax Reports]
┌─────────────────────────────────────────────────────────────┐
│                    FRONTEND LAYER                          │
├─────────────────────────────────────────────────────────────┤
│  Web Dashboard  │  Mobile App  │  Customer Portal         │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│              ### 11.1.3 Database Connection Stack
**Complete Connection Architecture:**
```
┌─────────────────────────────────────────────────────────────────┐
│                    WEB APP (PHP/Laravel)                   │
│  Admin Panel  │  Dashboard  │  Reports                      │
└─────────────────────────────────┬───────────────────────────────┘
                              │ HTTPS/REST API
┌─────────────────────────────────┴───────────────────────────────┐
│                    LARAVEL API BACKEND                        │
│  API Gateway  │  Authentication  │  Rate Limiting          │
└─────────────────────────────────┬───────────────────────────────┘
                              │ Database Connections
┌─────────────────────────────────┴───────────────────────────────┐
│                DATABASE CONNECTION LAYER                      │
│  Connection Pooling  │  Query Builder  │  ORM             │
└─────────────────────────────────┬───────────────────────────────┘
                              │ Database Drivers
┌─────────────────────────────────┴───────────────────────────────┐
│                   DATABASE SYSTEM                        │
│  MySQL (Primary)  │  Redis (Cache)  │  AI Server (Python)      │
└─────────────────────────────────────────────────────────────────────┘
```

**Database Configuration (config/database.php):**
```php
<?php
return [
    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [
        // User Management Database (MySQL)
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'sppg_users'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'pool' => [
                'min_connections' => 10,
                'max_connections' => 50,
                'connect_timeout' => 5,
                'wait_timeout' => 30,
            ],
        ],

        // Primary Database (MySQL)
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT_PGSQL', '5432'),
            'database' => env('DB_DATABASE_PGSQL', 'sppg_main'),
            'username' => env('DB_USERNAME_PGSQL', 'forge'),
            'password' => env('DB_PASSWORD_PGSQL', ''),
            'charset' => 'utf8',
            'pool' => [
                'min_connections' => 10,
                'max_connections' => 50,
                'connect_timeout' => 5,
                'idle_timeout' => 300,
            ],
        ],

        // AI Analytics (External API)
        // Connect to Python FastAPI server for ML operations

        // Cache & Session (Redis)
        'redis' => [
            'client' => 'phpredis',
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
    ],
];
```

**Laravel Web API Integration:**
```php
// app/Services/AiService.php
namespace App\Services;

class AiService
{
    private $aiServerUrl;
    
    public function __construct()
    {
        $this->aiServerUrl = config('services.ai.server_url');
    }
    
    public function predictPrice($productId, $daysAhead)
    {
        $response = Http::post($this->aiServerUrl . '/predict', [
            'product_id' => $productId,
            'days_ahead' => $daysAhead
        ]);
        
        return $response->json();
    }
}
        this.client = axios.create({
            baseURL: 'https://api.sppg-app.com/v1',
            timeout: 10000,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        });

        // Request interceptor untuk auth token
        this.client.interceptors.request.use(async (config) => {
            const token = await AsyncStorage.getItem('auth_token');
            if (token) {
                config.headers.Authorization = `Bearer ${token}`;
            }
            return config;
        });
    }

    async getOrders() {
        try {
            const response = await this.client.get('/orders');
            return response.data;
        } catch (error) {
            console.error('Get orders error:', error);
            throw error;
        }
    }

    async createOrder(orderData) {
        try {
            const response = await this.client.post('/orders', orderData);
            return response.data;
        } catch (error) {
            console.error('Create order error:', error);
            throw error;
        }
    }
}

export default new ApiService();
```

**Queue Service:**
```php
// app/Jobs/SyncData.php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    private $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    public function handle()
    {
        // Process synchronization
        // Backup to local storage if offline
    }
}
    constructor() {
        this.isOnline = false;
        this.pendingOperations = [];
        this.setupNetworkListener();
    }

    setupNetworkListener() {
        NetInfo.addEventListener(state => {
            this.isOnline = state.isConnected;
            if (this.isOnline) {
                this.syncPendingOperations();
            }
        });
    }

    async saveOfflineData(key, data) {
        await AsyncStorage.setItem(`offline_${key}`, JSON.stringify(data));
    }

    async addPendingOperation(operation) {
        this.pendingOperations.push(operation);
        await AsyncStorage.setItem('pending_operations', JSON.stringify(this.pendingOperations));
    }

    async syncPendingOperations() {
        try {
            const pending = await AsyncStorage.getItem('pending_operations');
            if (pending) {
                const operations = JSON.parse(pending);
                
                for (const operation of operations) {
                    try {
                        await this.executeOperation(operation);
                    } catch (error) {
                        console.error('Sync operation failed:', error);
                    }
                }

                await AsyncStorage.removeItem('pending_operations');
                this.pendingOperations = [];
            }
        } catch (error) {
            console.error('Sync failed:', error);
        }
    }
}

export default new SyncService();
```

**Multi-Database Service:**
```php
// app/Services/DatabaseService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DatabaseService
{
    // User Operations (MySQL)
    public function getUserById(int $userId)
    {
        return DB::connection('mysql')
            ->table('users')
            ->where('id', $userId)
            ->first();
    }

    // Location Operations (MySQL with spatial)
    public function getNearbyAddresses(float $lat, float $lng, int $radius)
    {
        return DB::table('addresses')
            ->selectRaw("*, 
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
                sin(radians(latitude)))) AS distance", [$lat, $lng, $lat])
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->get();
    }
            ->table('addresses')
            ->selectRaw("
                id, name, address, 
                ST_Distance_Sphere(
                    ST_MakePoint(longitude, latitude),
                    ST_MakePoint(?, ?)
                ) as distance
            ", [$lng, $lat])
            ->whereRaw('ST_Distance_Sphere(...) < ?', [$radius])
            ->orderBy('distance')
            ->limit(50)
            ->get();
    }

    // AI Analytics Operations (External API)
    public function getSalesAnalytics(string $date)
    {
        $aiService = new AiService();
        return $aiService->getSalesAnalytics($date);
    }
    }

    // Cache Operations (Redis)
    public function cacheUserData(int $userId, array $data): void
    {
        DB::connection('redis')
            ->set("user:{$userId}", json_encode($data), 3600);
    }
}
```

**Performance Monitoring:**
```php
// app/Http/Middleware/PerformanceMonitor.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor
{
    public function handle($request, Closure $next)
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $duration = ($endTime - $startTime) * 1000;

        if ($duration > 1000) {
            Log::channel('performance')->warning('Slow API Request', [
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'duration_ms' => $duration,
                'user_id' => $request->user()?->id,
                'memory_usage' => memory_get_peak_usage(true),
            ]);
        }

        return $response;
    }
}
```

**Connection Pooling Configuration:**
```php
// Optimized settings untuk high traffic
'connections' => [
    'mysql' => [
        'pool' => [
            'min_connections' => 10,  // Minimum 10 connections
            'max_connections' => 50,  // Maximum 50 connections
            'connect_timeout' => 5,   // 5 seconds connect timeout
            'wait_timeout' => 30,     // 30 seconds wait timeout
            'idle_timeout' => 300,    // 5 minutes idle timeout
        ],
        'options' => [
            PDO::ATTR_PERSISTENT => true,  // Persistent connections
            PDO::ATTR_EMULATE_PREPARES => false,  // Native prepared statements
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,  // Buffered queries
        ],
    ],
],
```

### 4.2 Technology Stack Recommendation

**Backend:**
- **Framework:** PHP 8.1+ with Laravel 10.x
- **Database:** MySQL 8.0+ (Primary) + Redis (Cache)
- **AI Processing:** Python 3.9+ with FastAPI (separate server)
- **Queue:** Laravel Queue with Redis
- **File Storage:** Local storage with cloud backup

**Frontend:**
- **Framework:** Bootstrap 5.x + jQuery 3.x
- **UI Library:** Tailwind CSS + Alpine.js
- **Charts:** Chart.js / ApexCharts
- **Notifications:** SweetAlert2
- **Tables:** DataTables
- **Mobile:** Progressive Web App (PWA) support

**Infrastructure:**
- **Cloud:** AWS / Google Cloud
- **Container:** Docker
- **CI/CD:** GitHub Actions
- **Monitoring:** Prometheus + Grafana

---

## 📱 MODUL APLIKASI

### 5.1 Modul Core Business Operations

#### 5.1.1 Executive Dashboard
**Fitur:**
- Real-time business KPIs
- Sales overview (SPPG vs Regular)
- Financial performance metrics
- Inventory status alerts
- Employee productivity metrics
- Profit & loss summary
- Cash flow monitoring

**Key Metrics:**
- Total revenue (SPPG + Regular)
- Gross profit margin
- Operating expenses
- Net profit
- Sales growth rate
- Customer acquisition cost
- Employee productivity ratio
- Inventory turnover

#### 5.1.2 Customer Management (SPPG + Regular)
**Fitur:**
- Unified customer database
- Customer categorization (SPPG/Regular)
- Credit limit management
- Payment terms tracking
- Customer performance analysis
- Communication history
- Document management

**Sub-modul:**
- Customer Registration & Verification
- SPPG Customer Management
- Regular Customer Management
- Credit & Collections
- Customer Analytics

#### 5.1.3 Sales Order Management
**Fitur:**
- SPPG order processing
- Regular sales order processing
- Quotation management
- Order tracking
- Pricing management (per customer type)
- Commission calculation
- Order analytics

**Sub-modul:**
- Order Entry
- Quotation System
- Pricing Engine
- Order Tracking
- Commission Management

#### 5.1.4 Procurement & Supplier Management
**Fitur:**
- Supplier database & evaluation
- Purchase order management
- Price comparison & negotiation
- Supplier performance tracking
- Quality control records
- Payment scheduling
- Cost analysis

**Sub-modul:**
- Supplier Management
- Purchase Orders
- Price Management
- Quality Control
- Supplier Analytics

### 5.2 Modul Inventory & Warehouse

#### 5.2.1 Inventory Management
**Fitur:**
- Real-time stock monitoring
- Multi-warehouse support
- Batch/lot tracking
- Expiry management
- Stock valuation (FIFO/LIFO/Average)
- Low stock alerts
- Inventory analytics

**Sub-modul:**
- Stock Control
- Warehouse Management
- Valuation Methods
- Inventory Analytics
- Stock Movement Tracking

#### 5.2.2 Order Fulfillment
**Fitur:**
- Pick & pack optimization
- Order prioritization
- Batch allocation
- Quality check integration
- Packaging management
- Shipping integration

### 5.3 Modul Financial & Accounting

#### 5.3.1 Complete Accounting System
**Fitur:**
- General ledger management
- Accounts payable (supplier)
- Accounts receivable (customer)
- Cash & bank management
- Fixed assets tracking
- Multi-currency support (IDR primary)
- Tax management (PPN, PPh 21, PPh 23)

**Standar Indonesia:**
- **Mata Uang:** Rupiah (IDR) dengan format Rp 1.234.567,89
- **Tanggal:** Format DD/MM/YYYY (contoh: 16/01/2026)
- **Nomor:** Format Indonesia (contoh: INV/2026/01/001)
- **Pajak:** Sistem perpajakan Indonesia

**Sub-modul:**
- General Ledger
- Accounts Payable
- Accounts Receivable
- Cash Management
- Tax Management
- Fixed Assets

#### 5.3.2 Capital & Investment Management
**Fitur:**
- Modal awal tracking (equity)
- Penambahan modal (capital injection)
- Pembagian dividen
- Retained earnings management
- Investor tracking
- Equity structure management
- Modal kerja monitoring

**Format Indonesia:**
- **Laporan Keuangan:** Sesuai PSAK Indonesia
- **Neraca:** Format standar Bapepam
- **Laba Rugi:** Format Indonesia
- **Arus Kas:** Format standar Indonesia

**Sub-modul:**
- Equity Management
- Capital Tracking
- Dividend Management
- Investor Relations
- Working Capital Analysis

#### 5.3.3 Revenue & Cost Management
**Fitur:**
- Revenue recognition (accrual & cash basis)
- Cost of goods sold (COGS) calculation
- Operating expenses tracking
- Non-operating income/expense
- Cost center management
- Project costing
- Departmental budgeting

**Standar Akuntansi Indonesia:**
- **PSAK (Standar Akuntansi Keuangan Indonesia)**
- **Kode Akun:** Sesuai chart of accounts Indonesia
- **Laporan:** Format Bapepam/KEU
- **Pajak:** PPN 11%, PPh 21, PPh 23

**Sub-modul:**
- Revenue Recognition
- Cost Management
- Expense Tracking
- Budget Control
- Cost Analysis

#### 5.3.4 Profit & Loss Analysis
**Fitur:**
- Real-time P&L calculation
- Gross profit analysis
- Operating profit tracking
- Net profit calculation
- EBITDA analysis
- Profit margin analysis
- Break-even analysis
- Variance analysis (budget vs actual)

**Format Laporan Indonesia:**
```
LAPORAN LABA RUGI
PT. DISTRIBUTION MAKMURAN
Periode: 1 Januari - 31 Desember 2026

PENDAPATAN USAHA
  Penjualan Bersih                    Rp 1.234.567.890
  Pendapatan Lainnya                 Rp 12.345.678
  Total Pendapatan                   Rp 1.246.913.568

HARGA POKOK PENJUALAN
  Harga Pokok                        Rp 876.543.210
  Laba Kotor                        Rp 370.370.358

BEBAN USAHA
  Beban Penjualan                    Rp 45.678.901
  Beban Administrasi & Umum          Rp 67.890.123
  Beban Penyusutan                   Rp 34.567.890
  Total Beban                       Rp 148.136.914

LABA USAHA                        Rp 222.233.444
```

**Sub-modul:**
- P&L Statement
- Profit Analysis
- Margin Analysis
- Break-Even Analysis
- Variance Reporting

#### 5.3.5 Return & Investment Analysis
**Fitur:**
- ROI (Return on Investment) calculation
- ROE (Return on Equity) analysis
- Asset turnover analysis
- Investment performance tracking
- Project ROI analysis
- Payback period calculation
- NPV (Net Present Value) analysis
- IRR (Internal Rate of Return) calculation

**Format Indonesia:**
- **ROI:** Dalam persen dengan format 12,34%
- **Payback:** Dalam bulan/tahun
- **NPV:** Dalam Rupiah
- **IRR:** Dalam persen tahunan

**Sub-modul:**
- ROI Analysis
- Investment Tracking
- Performance Metrics
- Investment Appraisal

#### 5.3.6 Cash Flow Management
**Fitur:**
- Cash flow statement generation
- Operating cash flow tracking
- Investing cash flow monitoring
- Financing cash flow management
- Cash forecasting
- Working capital analysis
- Cash position monitoring
- Bank reconciliation

**Format Arus Kas Indonesia:**
```
LAPORAN ARUS KAS
Periode: 1 Januari - 31 Desember 2026

ARUS KAS DARI AKTIVITAS OPERASI
  Penerimaan dari Pelanggan     Rp 1.234.567.890
  Pembayaran kepada Supplier     (Rp 876.543.210)
  Pembayaran Gaji                (Rp 234.567.890)
  Arus Kas Operasi Bersih       Rp 123.456.790

ARUS KAS DARI AKTIVITAS INVESTASI
  Pembelian Aset Tetap          (Rp 67.890.123)
  Penjualan Aset                Rp 12.345.678
  Arus Kas Investasi Bersih     (Rp 55.544.445)

ARUS KAS DARI AKTIVITAS PENDANAAN
  Penambahan Modal              Rp 100.000.000
  Pembayaran Dividen            (Rp 50.000.000)
  Arus Kas Pendanaan Bersih    Rp 50.000.000

KENAIKAN (PENURUNAN) KAS      Rp 117.912.345
```

**Sub-modul:**
- Cash Flow Statement
- Cash Forecasting
- Working Capital Management
- Bank Reconciliation

#### 5.3.7 Financial Risk Management
**Fitur:**
- Credit risk analysis (customer)
- Liquidity risk monitoring
- Market risk analysis
- Operational risk tracking
- Financial ratio analysis
- Stress testing
- Scenario analysis
- Risk mitigation tracking

**Standar Risiko Indonesia:**
- **Rasio Likuiditas:** Current ratio, Quick ratio
- **Rasio Solvabilitas:** Debt to equity, Debt to assets
- **Rasio Efisiensi:** Inventory turnover, Asset turnover
- **Rasio Profitabilitas:** ROE, ROA, Net margin

**Sub-modul:**
- Risk Assessment
- Ratio Analysis
- Stress Testing
- Risk Monitoring

#### 5.3.8 Financial Reporting
**Fitur:**
- Financial statements (P&L, Balance Sheet, Cash Flow)
- Trial balance
- Custom financial reports
- Budget vs actual analysis
- Cost center reporting
- BGN format reports (for SPPG transactions)
- Management dashboards
- Investor reports

**Format Laporan Indonesia:**
- **Neraca:** Format standar Bapepam
- **Laba Rugi:** Format PSAK Indonesia
- **Arus Kas:** Format standar Indonesia
- **Perubahan Modal:** Format ekuitas
- **Catatan Laporan:** Disclosures PSAK

**Sub-modul:**
- Standard Reports
- Custom Reports
- Budget Management
- Cost Analysis
- BGN Reporting

### 5.5 Modul Human Resource Management

#### 5.5.1 Employee Management
**Fitur:**
- Employee database & profiles
- Department & position management
- Salary & grade structure
- Performance tracking (KPI)
- Training & certification
- Attendance management
- Leave management (cuti, sakit, izin)
- Employee documents management
- Contract management

**Data Karyawan:**
- **Personal Data:** Nama lengkap, NIK, KK, alamat, kontak
- **Employment Data:** Tanggal masuk, kontrak, posisi, departemen
- **Compensation:** Gaji pokok, tunjangan, komisi, bonus
- **Performance:** KPI, target, pencapaian, evaluasi
- **Training:** Sertifikat, pelatihan, kompetensi
- **Documents:** KTP, NPWP, BPJS, kontrak kerja

**Sub-modul:**
- Employee Database
- Performance Management
- Training Management
- Attendance System
- Document Management
- Contract Management

#### 5.5.2 Payroll & Compensation
**Fitur:**
- Payroll processing (bulanan/periodik)
- Commission & bonus calculation
- Overtime & allowance management
- Tax calculation (PPh 21, BPJS)
- BPJS & benefits management
- Pay slip generation
- Bank transfer integration
- THR & bonus management
- Loan & advance management

**Komponen Gaji Indonesia:**
- **Gaji Pokok:** Sesuai UMR/UMP
- **Tunjangan:** Transport, makan, jabatan, komunikasi
- **Lembur:** 1.5x jam kerja (sesuai UU Ketenagakerjaan)
- **THR:** 1x gaji (sesuai peraturan pemerintah)
- **BPJS:** Jaminan Kesehatan, Jaminan Ketenagakerjaan
- **PPh 21:** Tarif progresif sesuai peraturan
- **PPh 21 Final:** 1.5% untuk usaha kecil

**Sub-modul:**
- Payroll Processing
- Commission System
- Benefits Management
- Tax & Compliance
- Loan Management

#### 5.5.3 Team Productivity & Performance
**Fitur:**
- Sales team performance tracking
- Procurement efficiency metrics
- Warehouse productivity analysis
- Delivery performance monitoring
- Individual KPI tracking
- Team analytics
- Incentive calculation
- Performance dashboard
- Goal setting & tracking

**KPI per Department:**
- **Sales Team:** Target penjualan, conversion rate, customer acquisition
- **Procurement:** Cost savings, supplier performance, lead time
- **Warehouse:** Accuracy rate, turnover, safety, productivity
- **Delivery:** On-time delivery, customer satisfaction, cost per delivery
- **Admin:** Process efficiency, error rate, response time

**Sub-modul:**
- Performance Management
- KPI Tracking
- Incentive Management
- Team Analytics

### 5.6 Modul Inventory & Warehouse Management (Extended)

#### 5.6.1 Inventory Control
**Fitur:**
- Real-time stock monitoring
- Multi-warehouse support
- Batch/lot tracking
- Expiry management
- Stock valuation (FIFO/LIFO/Average)
- Low stock alerts
- Inventory analytics
- Stock adjustment tracking
- Physical stock counting

**Manajemen Stok:**
- **Stock Barang:** Real-time tracking per SKU
- **Stok Minimum:** Alert otomatis untuk reorder
- **Stok Maksimum:** Prevent overstocking
- **Barang Rusak:** Tracking dan reporting
- **Barang Kadaluarsa:** Expiry date management
- **Barang Lama:** Slow-moving inventory tracking
- **Penyusutan:** Depreciation calculation

**Sub-modul:**
- Stock Control
- Warehouse Management
- Valuation Methods
- Inventory Analytics
- Stock Movement Tracking

#### 5.6.2 Quality Control & Returns
**Fitur:**
- Goods receiving inspection
- Quality check procedures
- Defect tracking
- Return processing
- Claim management
- Supplier quality rating
- Quality reporting
- Corrective action tracking

**Manajemen Kualitas:**
- **Pemeriksaan Barang:** Quality control saat penerimaan
- **Barang Rusak:** Tracking dan analysis penyebab
- **Retur Pelanggan:** Process dan analysis retur
- **Klaim Supplier:** Claim untuk barang cacat
- **Quality Report:** Laporan kualitas produk
- **Supplier Rating:** Penilaian kualitas supplier

**Sub-modul:**
- Quality Control
- Return Management
- Claim Processing
- Quality Analytics

#### 5.6.3 Asset & Equipment Management
**Fitur:**
- Fixed asset tracking
- Equipment maintenance
- Asset depreciation
- Asset location tracking
- Maintenance scheduling
- Asset disposal
- Asset utilization reporting

**Manajemen Aset:**
- **Aset Tetap:** Gedung, kendaraan, peralatan
- **Penyusutan:** Depreciation methods (garis lurus, menurun)
- **Pemeliharaan:** Preventive & corrective maintenance
- **Utilisasi:** Asset usage tracking
- **Lokasi:** Real-time asset location

**Sub-modul:**
- Asset Management
- Maintenance Management
- Depreciation Tracking

### 5.7 Modul Sales & Customer Management (Extended)

#### 5.7.1 Customer Database Management
**Fitur:**
- Unified customer database (SPPG + Regular)
- Customer categorization
- Credit limit management
- Payment terms tracking
- Customer performance analysis
- Communication history
- Document management
- Customer segmentation

**Data Pelanggan:**
- **Data Umum:** Nama, alamat, telepon, email, NPWP
- **Alamat Lengkap:** Alamat kantor, alamat pengiriman, alamat billing
- **Kontak Person:** PIC, jabatan, kontak darurat
- **Kategori:** SPPG (institusi), Regular (bisnis), Government, Private
- **Credit Management:** Limit kredit, payment terms, collection history
- **Performance:** Purchase history, payment behavior, profitability

**Sub-modul:**
- Customer Registration & Verification
- Customer Segmentation
- Credit Management
- Communication Management
- Document Management

#### 5.7.2 Sales Process Management
**Fitur:**
- Lead management
- Quotation management
- Order processing (SPPG + Regular)
- Pipeline tracking
- Sales forecasting
- Commission calculation
- Sales analytics
- Territory management

**Proses Penjualan:**
- **Lead Generation:** Tracking prospek pelanggan
- **Quotation:** Pembuatan penawaran harga
- **Order Processing:** Konversi quotation ke sales order
- **Pipeline:** Tracking stages sales process
- **Forecasting:** Prediksi penjualan berdasarkan trend
- **Commission:** Otomatis perhitungan komisi sales

**Sub-modul:**
- Lead Management
- Quotation System
- Order Processing
- Sales Analytics
- Commission Management

#### 5.7.3 Procurement & Supplier Management
**Fitur:**
- Supplier database & evaluation
- Purchase order management
- Price comparison & negotiation
- Supplier performance tracking
- Quality control records
- Payment scheduling
- Cost analysis
- Supplier relationship management

**Manajemen Pembelian:**
- **Supplier Database:** Data lengkap pemasok
- **Purchase Order:** PO creation, approval, tracking
- **Price Comparison:** Multiple supplier quotes
- **Negotiation Tracking:** History negosiasi harga
- **Quality Control:** Inspection dan quality records
- **Performance Evaluation:** Supplier rating system
- **Payment Terms:** Management pembayaran ke supplier

**Sub-modul:**
- Supplier Management
- Purchase Orders
- Price Management
- Quality Control
- Supplier Analytics

### 5.8 Modul Document & Transaction Management

#### 5.8.1 Document Management System
**Fitur:**
- Digital document storage
- Document categorization
- Version control
- Access control
- Document workflow
- E-signature integration
- Document search
- Expiry tracking
- Compliance monitoring

**Manajemen Dokumen:**
- **Invoice/Faktur:** Digital storage & management
- **Purchase Order:** PO documents tracking
- **Delivery Order:** DO management
- **Receipt:** Bukti penerimaan barang
- **Payment Proof:** Bukti pembayaran
- **Contract:** Contract management
- **Legal Documents:** Legal document storage

**Sub-modul:**
- Document Storage
- Workflow Management
- E-signature Integration
- Document Analytics

#### 5.8.2 Transaction Processing
**Fitur:**
- Transaction recording
- Payment processing
- Receipt generation
- Transaction matching
- Bank reconciliation
- Tax calculation
- Multi-currency support
- Transaction approval workflow

**Proses Transaksi:**
- **Sales Transaction:** Recording penjualan
- **Purchase Transaction:** Recording pembelian
- **Payment Processing:** Multiple payment methods
- **Receipt Generation:** Automatic receipt creation
- **Bank Reconciliation:** Match dengan bank statement
- **Tax Calculation:** Automatic tax computation

**Sub-modul:**
- Transaction Processing
- Payment Management
- Bank Reconciliation
- Tax Processing

### 5.9 Modul Location & Facility Management

#### 5.9.1 Address & Location Management
**Fitur:**
- Multi-location support
- Address validation
- Location hierarchy
- Territory management
- Delivery route optimization
- Location-based pricing
- Facility management
- Asset location tracking

**Manajemen Alamat:**
- **Alamat Perusahaan:** Head office, branches, warehouses
- **Alamat Pelanggan:** Multiple addresses per customer
- **Alamat Supplier:** Supplier addresses
- **Alamat Pengiriman:** Delivery address management
- **Territory:** Sales territory mapping
- **Route Optimization:** Delivery route planning

**Sub-modul:**
- Location Management
- Address Validation
- Territory Management
- Route Optimization

#### 5.9.2 Facility & Branch Management
**Fitur:**
- Branch registration
- Facility capacity tracking
- Resource allocation
- Inter-branch transactions
- Branch performance reporting
- Consolidated reporting
- Asset allocation per branch
- Staff assignment per branch

**Manajemen Cabang:**
- **Data Cabang:** Branch information & setup
- **Kapasitas:** Storage & operational capacity
- **Resource Allocation:** Staff & asset assignment
- **Inter-branch:** Stock transfer & transactions
- **Performance:** Branch-level KPI tracking
- **Consolidation:** Group reporting

**Sub-modul:**
- Branch Management
- Resource Management
- Inter-branch Transactions
- Consolidated Reporting

### 5.10 Modul Time & Attendance Management

#### 5.10.1 Time Management
**Fitur:**
- Working hours configuration
- Shift scheduling
- Overtime tracking
- Holiday management
- Leave management
- Time tracking
- Attendance monitoring
- Timesheet approval
- Labor cost calculation

**Manajemen Waktu:**
- **Jam Kerja:** Standard working hours Indonesia
- **Shift:** Morning, afternoon, night shift
- **Lembur:** Overtime calculation & approval
- **Hari Libur:** Official holidays & company holidays
- **Cuti:** Annual leave, sick leave, personal leave
- **Absensi:** Attendance tracking & monitoring
- **Timesheet:** Daily time recording

**Sub-modul:**
- Time Configuration
- Shift Management
- Leave Management
- Attendance Tracking
- Timesheet Processing

#### 5.10.2 Calendar & Scheduling
**Fitur:**
- Company calendar
- Resource scheduling
- Meeting scheduling
- Task scheduling
- Reminder system
- Recurring events
- Resource availability
- Conflict detection

**Manajemen Kalender:**
- **Kalender Perusahaan:** Company events & holidays
- **Resource Booking:** Meeting room, equipment booking
- **Task Scheduling:** Task assignment & tracking
- **Reminder System:** Automated notifications
- **Availability:** Resource availability tracking
- **Conflict Detection:** Double booking prevention

**Sub-modul:**
- Calendar Management
- Resource Scheduling
- Task Management
- Notification System

### 5.5 Modul Logistics & Distribution

#### 5.5.1 Order Delivery
**Fitur:**
- Delivery scheduling
- Route planning & optimization
- Driver assignment
- Vehicle management
- Real-time tracking
- Proof of delivery (digital)
- Delivery cost analysis

**Sub-modul:**
- Delivery Planning
- Route Optimization
- Fleet Management
- Delivery Tracking

#### 5.5.2 Transportation Management
**Fitur:**
- Vehicle maintenance tracking
- Driver performance
- Fuel cost tracking
- Shipping cost analysis
- Third-party logistics integration
- Delivery analytics

### 5.6 Modul Business Intelligence

#### 5.6.1 Analytics & Reporting
**Fitur:**
- Sales analytics (by product, customer, region)
- Profitability analysis
- Cost analysis
- Trend analysis
- Forecasting
- Custom dashboards
- Data visualization

**Sub-modul:**
- Sales Analytics
- Financial Analytics
- Operational Analytics
- Custom Dashboards
- Forecasting Tools

#### 5.6.2 Business Intelligence
**Fitur:**
- KPI monitoring
- Performance dashboards
- Alert management
- Data mining
- Predictive analytics
- Executive reporting
- Mobile access

### 5.7 Modul System Management

#### 5.7.1 User & Access Control
**Fitur:**
- Multi-user support
- Role-based permissions
- User authentication
- Session management
- Password policies
- Audit logging

#### 5.7.2 System Configuration
**Fitur:**
- Company settings
- System parameters
- Integration settings
- Backup management
- System monitoring
- Performance optimization

---

## 🗄️ DATABASE DESIGN

### 6.1 Core Business Entities

#### 6.1.1 Users & Authentication
```sql
Users: id, name, email, password_hash, role, department, phone, 
      is_active, last_login, created_at, updated_at

Roles: id, name, permissions, created_at, updated_at

UserSessions: id, user_id, token, expires_at, created_at

Employees: id, user_id, employee_id, position, department, 
          hire_date, salary_grade, is_active, created_at, updated_at
```

#### 6.1.2 Customer Management (SPPG + Regular)
```sql
Customers: id, customer_code, name, type, address, phone, email, 
          contact_person, tax_id, credit_limit, payment_terms,
          is_active, created_at, updated_at

CustomerTypes: id, name, description (SPPG, Regular, Government, Private)

CustomerAddresses: id, customer_id, address_type, address, city, 
                province, postal_code, is_primary, created_at

CustomerDocuments: id, customer_id, document_type, filename, 
                 file_path, expiry_date, created_at, updated_at
```

#### 6.1.3 Sales Order Management
```sql
SalesOrders: id, order_number, customer_id, order_date, type, 
            status, subtotal, discount, tax_amount, total_amount,
            delivery_address, notes, created_by, created_at, updated_at

OrderTypes: id, name, description (SPPG, Regular)

SalesOrderItems: id, sales_order_id, product_id, quantity, unit, 
               unit_price, discount_amount, total_price, 
               delivered_quantity, created_at

OrderStatusHistory: id, sales_order_id, status, changed_by, 
                   notes, changed_at

Quotations: id, quotation_number, customer_id, valid_until, 
            subtotal, tax_amount, total_amount, status, 
            notes, created_by, created_at, updated_at

QuotationItems: id, quotation_id, product_id, quantity, unit, 
              unit_price, total_price, created_at
```

#### 6.1.4 Procurement & Supplier Management
```sql
Suppliers: id, supplier_code, name, address, phone, email, 
          contact_person, tax_id, payment_terms, is_active, 
          created_at, updated_at

PurchaseOrders: id, po_number, supplier_id, order_date, expected_date,
               status, subtotal, tax_amount, total_amount, notes,
               created_by, created_at, updated_at

PurchaseOrderItems: id, purchase_order_id, product_id, quantity, 
                  unit, unit_price, total_price, received_quantity,
                  created_at

GoodsReceipt: id, po_id, receipt_number, receipt_date, 
             supplier_id, status, notes, created_by, created_at

GoodsReceiptItems: id, goods_receipt_id, product_id, quantity_ordered,
                  quantity_received, unit_price, batch_number, 
                  expiry_date, notes, created_at
```

#### 6.1.5 Product & Inventory Management
```sql
Products: id, product_code, name, category, description, unit, 
          standard_cost, selling_price, is_active, created_at, updated_at

Categories: id, name, parent_id, description, created_at

Inventory: id, product_id, warehouse_id, current_stock, 
           minimum_stock, maximum_stock, unit_cost, 
           valuation_method, last_updated

InventoryTransactions: id, product_id, transaction_type, quantity, 
                      unit_cost, reference_type, reference_id, 
                      notes, created_by, created_at

Warehouses: id, code, name, address, manager_id, capacity, 
            is_active, created_at, updated_at

BatchNumbers: id, product_id, batch_number, manufacturing_date, 
              expiry_date, initial_quantity, current_quantity,
              location, created_at
```

#### 6.1.6 Financial & Accounting (Extended)
```sql
ChartOfAccounts: id, account_code, account_name, account_type, parent_id, 
                 normal_balance, is_active, created_at, updated_at

JournalEntries: id, entry_number, entry_date, description, 
               total_debit, total_credit, status, created_by, 
               created_at, updated_at

JournalEntryLines: id, journal_entry_id, account_id, debit, 
                  credit, description, reference_type, reference_id,
                  cost_center_id, project_id, created_at

Invoices: id, invoice_number, sales_order_id, customer_id, 
          invoice_date, due_date, subtotal, tax_amount, 
          total_amount, status, payment_terms, created_at, updated_at

InvoiceLines: id, invoice_id, product_id, description, quantity, 
             unit_price, discount_amount, total_price, tax_rate,
             tax_amount, created_at

Payments: id, payment_number, invoice_id, customer_id, payment_date,
          amount, payment_method, bank_account, reference_number,
          status, created_at, updated_at

Bills: id, bill_number, purchase_order_id, supplier_id, bill_date,
       due_date, subtotal, tax_amount, total_amount, status,
       payment_terms, created_at, updated_at

BillLines: id, bill_id, product_id, description, quantity, 
           unit_price, total_price, tax_rate, tax_amount, created_at

BillPayments: id, bill_id, payment_date, amount, payment_method,
             bank_account, reference_number, created_at, updated_at

-- Capital & Equity Management
Equity: id, investor_id, equity_type, amount, date, description,
        status, created_at, updated_at

CapitalTransactions: id, equity_id, transaction_type, amount, date,
                  description, reference_number, created_at

Dividends: id, declaration_date, payment_date, total_amount, 
           per_share, status, created_at, updated_at

-- Revenue & Cost Management
Revenue: id, revenue_date, customer_id, product_id, amount,
         tax_amount, net_amount, revenue_type, cost_center_id,
         project_id, created_at, updated_at

CostOfGoodsSold: id, transaction_date, product_id, quantity, 
                unit_cost, total_cost, sales_order_id, created_at

OperatingExpenses: id, expense_date, category, description, amount,
                  tax_amount, net_amount, cost_center_id,
                  approved_by, status, created_at, updated_at

-- Profit & Loss Tracking
ProfitLossPeriods: id, period_start, period_end, status, 
                   total_revenue, total_cogs, gross_profit,
                   operating_expenses, operating_profit,
                   net_profit, created_at, updated_at

ProfitLossItems: id, period_id, account_id, amount, type,
                 category, created_at

-- Investment & Return Analysis
Investments: id, investment_date, description, category, amount,
             expected_return, actual_return, status, project_id,
             created_at, updated_at

InvestmentReturns: id, investment_id, return_date, amount, 
                  return_type, description, created_at

ROI_Calculations: id, investment_id, calculation_date, roi_percentage,
                    payback_period_months, npv, irr, created_at

-- Cash Flow Management
CashFlow: id, flow_date, category, amount, flow_type,
           description, bank_account_id, reference_type,
           reference_id, created_at

BankAccounts: id, account_number, bank_name, account_type, 
              balance, currency, is_active, created_at, updated_at

CashFlowForecasts: id, forecast_date, category, expected_amount,
                    probability, notes, created_at, updated_at

-- Financial Risk Management
CreditRisks: id, customer_id, credit_limit, current_exposure,
              risk_score, last_review_date, status, created_at, updated_at

FinancialRatios: id, calculation_date, current_ratio, quick_ratio,
                 debt_to_equity, gross_margin, net_margin,
                 roi, roe, asset_turnover, created_at

RiskAssessments: id, assessment_date, risk_type, description,
                 impact_level, probability_level, mitigation_plan,
                 status, created_at, updated_at
```

#### 6.1.7 Budget & Forecasting
```sql
Budgets: id, budget_year, department_id, category, amount,
         description, status, approved_by, approved_date,
         created_at, updated_at

BudgetItems: id, budget_id, account_id, planned_amount, 
              actual_amount, variance, percentage_variance,
              created_at, updated_at

Forecasts: id, forecast_period, type, description, 
           created_by, created_at, updated_at

ForecastItems: id, forecast_id, product_id, customer_id,
              quantity, unit_price, total_amount, probability,
              created_at, updated_at
```

#### 6.1.8 Fixed Assets & Depreciation
```sql
FixedAssets: id, asset_code, description, category, purchase_date,
             purchase_cost, useful_life_years, salvage_value,
             depreciation_method, current_value, accumulated_depreciation,
             location, status, created_at, updated_at

Depreciation: id, asset_id, depreciation_date, amount,
               accumulated_depreciation, book_value, created_at
```

#### 6.1.7 Human Resource Management
```sql
Departments: id, name, parent_id, manager_id, description, 
            is_active, created_at, updated_at

Positions: id, name, department_id, grade_level, description, 
           is_active, created_at, updated_at

Salaries: id, employee_id, grade_level, base_salary, allowances,
          transport_allowance, meal_allowance, effective_date, 
          created_at, updated_at

Attendances: id, employee_id, attendance_date, check_in, check_out,
             work_hours, overtime_hours, status, created_at, updated_at

Payrolls: id, payroll_period, employee_id, base_salary, overtime,
          allowances, deductions, gross_salary, tax_amount, 
          net_salary, status, created_at, updated_at

Commissions: id, employee_id, sales_order_id, commission_rate,
            commission_amount, calculation_date, status, created_at
```

#### 6.1.9 Human Resource Management (Extended)
```sql
Employees: id, employee_id, user_id, full_name, nik, kk, npwp, 
           bpjs_tk, bpjs_kesehatan, place_of_birth, date_of_birth,
           gender, religion, marital_status, address, phone, email,
           department_id, position_id, grade_level, hire_date, 
           contract_type, contract_start, contract_end, salary_grade,
           bank_account, is_active, created_at, updated_at

Departments: id, name, parent_id, manager_id, description, 
            location, budget, is_active, created_at, updated_at

Positions: id, name, department_id, grade_level, description, 
          requirements, responsibilities, is_active, created_at, updated_at

Salaries: id, employee_id, grade_level, base_salary, transport_allowance,
         meal_allowance, position_allowance, other_allowances,
         effective_date, is_active, created_at, updated_at

Attendances: id, employee_id, attendance_date, check_in, check_out, 
           work_hours, overtime_hours, break_hours, status,
           location, supervisor_id, notes, created_at, updated_at

Leaves: id, employee_id, leave_type, start_date, end_date, days_count,
        reason, status, approver_id, approved_at, created_at, updated_at

LeaveTypes: id, name, description, max_days, requires_document,
           is_paid, is_active, created_at, updated_at

Payrolls: id, payroll_period, employee_id, base_salary, overtime,
          allowances, deductions, gross_salary, tax_amount,
          net_salary, bank_transfer_date, status, created_at, updated_at

PayrollDetails: id, payroll_id, component_type, amount, description,
               created_at

Commissions: id, employee_id, sales_order_id, commission_rate,
            commission_amount, calculation_date, status, paid_date,
            created_at, updated_at

Loans: id, employee_id, loan_type, amount, interest_rate, 
         term_months, monthly_installment, start_date, end_date,
         status, guarantor, created_at, updated_at

LoanPayments: id, loan_id, payment_date, amount, principal_amount,
              interest_amount, remaining_balance, created_at

PerformanceReviews: id, employee_id, review_period, reviewer_id,
                 kpi_score, strengths, weaknesses, goals,
                 overall_rating, notes, created_at, updated_at

TrainingRecords: id, employee_id, training_name, institution,
                start_date, end_date, certificate_name, cost,
                is_completed, created_at, updated_at

EmployeeDocuments: id, employee_id, document_type, filename, file_path,
                 expiry_date, is_verified, created_at, updated_at
```

#### 6.1.10 Asset & Facility Management
```sql
Assets: id, asset_code, name, category, description, purchase_date,
        purchase_cost, supplier_id, depreciation_method,
        useful_life_years, salvage_value, current_value,
        accumulated_depreciation, location_id, status,
        responsible_person_id, created_at, updated_at

AssetLocations: id, name, address, city, province, postal_code,
               phone, email, manager_id, created_at, updated_at

AssetDepreciations: id, asset_id, depreciation_date, amount,
                    accumulated_depreciation, book_value,
                    depreciation_method, created_at

MaintenanceRecords: id, asset_id, maintenance_type, description,
                   cost, vendor_id, start_date, end_date,
                   next_maintenance_date, status, created_at, updated_at

MaintenanceTypes: id, name, description, frequency, is_active,
                  created_at, updated_at

Facilities: id, name, type, location_id, capacity, manager_id,
            operating_hours, created_at, updated_at

FacilityBookings: id, facility_id, booked_by, booking_date,
                 start_time, end_time, purpose, status,
                 created_at, updated_at
```

#### 6.1.11 Location & Address Management
```sql
Addresses: id, entity_type, entity_id, address_type, street,
          city, province, postal_code, country, phone,
          email, is_primary, is_active, created_at, updated_at

Branches: id, branch_code, name, address_id, phone, email,
         manager_id, is_warehouse, capacity, operating_hours,
         is_active, created_at, updated_at

Territories: id, name, description, manager_id, coverage_area,
            created_at, updated_at

TerritoryAssignments: id, territory_id, employee_id, start_date,
                    end_date, is_active, created_at, updated_at

DeliveryRoutes: id, route_name, description, start_location,
               end_location, distance, estimated_time,
               vehicle_id, driver_id, created_at, updated_at

RouteStops: id, route_id, stop_order, customer_id, address_id,
            estimated_arrival, actual_arrival, status, notes,
            created_at, updated_at
```

#### 6.1.12 Document & Workflow Management
```sql
Documents: id, document_type, entity_type, entity_id, title,
          description, filename, file_path, file_size,
          mime_type, uploaded_by, uploaded_at, expiry_date,
          is_public, status, created_at, updated_at

DocumentTypes: id, name, description, category, required_fields,
               retention_period, is_active, created_at, updated_at

DocumentWorkflows: id, workflow_name, description, steps,
                   is_active, created_at, updated_at

WorkflowSteps: id, workflow_id, step_name, step_order, 
               approver_role, required_documents, 
               is_parallel, is_final, created_at, updated_at

DocumentApprovals: id, document_id, workflow_step_id, approver_id,
                 status, comments, approved_at, created_at

DocumentVersions: id, document_id, version_number, changes,
                 created_by, created_at, updated_at
```

#### 6.1.13 Time & Calendar Management
```sql
WorkingHours: id, day_of_week, start_time, end_time, break_duration,
               is_working_day, effective_date, created_at, updated_at

Shifts: id, shift_name, start_time, end_time, break_duration,
         working_hours, overtime_rate, created_at, updated_at

Holidays: id, name, date, type, is_recurring, recurrence_rule,
          description, is_active, created_at, updated_at

Calendars: id, calendar_name, description, timezone, working_days,
            created_at, updated_at

Events: id, calendar_id, title, description, start_datetime,
          end_datetime, location, attendees, organizer_id,
          event_type, status, created_at, updated_at

EventAttendees: id, event_id, attendee_id, response, response_date,
               created_at, updated_at
```

#### 6.1.14 Quality & Return Management
```sql
QualityInspections: id, inspection_date, inspector_id, purchase_order_id,
                   supplier_id, product_id, quantity_inspected,
                   quantity_passed, quantity_defective, defect_rate,
                   notes, status, created_at, updated_at

DefectRecords: id, inspection_id, product_id, defect_type,
               description, quantity, severity, action_taken,
               created_at, updated_at

CustomerReturns: id, return_date, customer_id, sales_order_id,
                product_id, quantity, return_reason, condition,
                refund_amount, status, created_at, updated_at

ReturnReasons: id, reason_code, description, category, is_active,
               created_at, updated_at

SupplierClaims: id, claim_date, supplier_id, purchase_order_id,
                claim_type, description, amount, status,
                resolution_date, resolution_notes, created_at, updated_at

ClaimTypes: id, name, description, category, required_documents,
             is_active, created_at, updated_at
```

### 6.2 System & Configuration Tables

#### 6.2.1 System Configuration
```sql
CompanyInfo: id, company_name, address, phone, email, tax_id,
            logo_url, created_at, updated_at

SystemSettings: id, setting_key, setting_value, description, 
               data_type, is_active, created_at, updated_at

DocumentTypes: id, name, description, required_for, created_at
```

#### 6.2.2 Audit Trail
```sql
AuditLogs: id, table_name, record_id, action, old_values, 
          new_values, user_id, ip_address, user_agent, 
          timestamp, session_id
```

### 6.3 Relationships & Constraints

**Key Relationships:**
- Users → Employees (1:1)
- Employees → Departments (many:1)
- Customers → SalesOrders (1:many)
- SalesOrders → SalesOrderItems (1:many)
- Products → Inventory (1:many)
- Products → InventoryTransactions (1:many)
- Suppliers → PurchaseOrders (1:many)
- PurchaseOrders → PurchaseOrderItems (1:many)
- SalesOrders → Invoices (1:1)
- Invoices → Payments (1:many)
- Employees → Payrolls (1:many)
- SalesOrders → Deliveries (1:many)

**Data Integrity:**
- Foreign key constraints with proper indexing
- Unique constraints (order numbers, invoice numbers, etc.)
- Check constraints (positive quantities, valid dates)
- Triggers for audit logging and automatic calculations
- Default values for status fields and timestamps

**Performance Optimization:**
- Composite indexes on frequently queried columns
- Partitioning for large tables (transactions, audit logs)
- Materialized views for complex reporting queries
- Proper data types for storage efficiency

---

## 👥 USER MANAGEMENT & ACCESS CONTROL

### 7.1 Role Definitions

#### 7.1.1 Super Admin / Owner
**Scope:** Seluruh sistem dan bisnis
**Permissions:**
- User management (semua role)
- System configuration & maintenance
- Financial reporting & approval
- Supplier & customer management
- Complete access ke semua modul
- Backup & restore system
- Audit log access

#### 7.1.2 Director / General Manager
**Scope:** Strategic business management
**Permissions:**
- Team management & performance review
- Budget approval & monitoring
- Strategic reports & analytics
- Supplier relationship management
- Major customer management
- Pricing strategy approval
- Business development decisions

#### 7.1.3 Finance Manager
**Scope:** Financial operations & reporting
**Permissions:**
- Complete accounting system access
- Invoice & payment processing
- Tax calculation & reporting
- Financial statement generation
- Budget management
- Cost analysis
- Bank reconciliation
- Financial audit preparation

#### 7.1.4 Sales Manager
**Scope:** Sales operations & team management
**Permissions:**
- Customer management (SPPG & Regular)
- Sales order approval
- Quotation management
- Pricing management
- Sales team performance tracking
- Commission calculation & approval
- Customer relationship management

#### 7.1.5 Procurement Manager
**Scope:** Supply chain & supplier management
**Permissions:**
- Supplier management & evaluation
- Purchase order creation & approval
- Price negotiation tracking
- Quality control oversight
- Supplier payment processing
- Cost analysis for procurement
- Inventory planning

#### 7.1.6 Warehouse Manager
**Scope:** Inventory & warehouse operations
**Permissions:**
- Inventory management
- Stock adjustment approval
- Warehouse operations
- Quality control execution
- Stock receiving & processing
- Warehouse staff management
- Inventory reporting

#### 7.1.7 Sales Officer (SPPG)
**Scope:** SPPG customer management
**Permissions:**
- SPPG customer data entry
- SPPG order processing
- Document verification
- Order status updates
- Basic reporting
- Customer communication

#### 7.1.8 Sales Officer (Regular)
**Scope:** Regular customer management
**Permissions:**
- Regular customer management
- Quotation creation
- Order processing
- Customer follow-up
- Sales reporting
- Commission tracking

#### 7.1.9 Warehouse Staff
**Scope:** Daily warehouse operations
**Permissions:**
- Stock data entry
- Pick & pack operations
- Inventory counting
- Basic reporting
- Quality check execution

#### 7.1.10 Delivery Coordinator
**Scope:** Logistics & delivery operations
**Permissions:**
- Delivery scheduling
- Driver assignment
- Route planning
- Delivery tracking
- Proof of delivery management
- Delivery reporting

#### 7.1.11 HR Admin
**Scope:** Human resource management
**Permissions:**
- Employee data management
- Payroll processing
- Attendance tracking
- Performance management
- Training records
- HR reporting

#### 7.1.12 Staff / Operator
**Scope:** Basic operational tasks
**Permissions:**
- Data entry (sesuai department)
- Basic reporting
- Document upload
- Status updates
- Communication tasks

### 7.2 Access Matrix

| Module | Owner | Director | Finance | Sales Mgr | Procurement | Warehouse | Sales Officer | Staff |
|---------|--------|----------|---------|-----------|-------------|------------|---------------|--------|
| **Executive Dashboard** | ✅ | ✅ | ✅ | ✅ | ⚠️ | ⚠️ | ⚠️ |
| **Customer Management** | ✅ | ✅ | ⚠️ | ✅ | ❌ | ⚠️ | ✅ | ⚠️ |
| **Sales Orders** | ✅ | ⚠️ | ⚠️ | ✅ | ❌ | ❌ | ✅ | ⚠️ |
| **Procurement** | ✅ | ⚠️ | ⚠️ | ❌ | ✅ | ⚠️ | ❌ | ❌ |
| **Inventory** | ✅ | ⚠️ | ❌ | ⚠️ | ⚠️ | ✅ | ⚠️ | ⚠️ |
| **Financial** | ✅ | ✅ | ✅ | ❌ | ⚠️ | ❌ | ❌ | ❌ |
| **HR Management** | ✅ | ✅ | ⚠️ | ⚠️ | ⚠️ | ⚠️ | ❌ | ❌ |
| **Logistics** | ✅ | ⚠️ | ❌ | ⚠️ | ❌ | ⚠️ | ✅ | ⚠️ |
| **Reports** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ⚠️ |
| **System Config** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

Legend: ✅ Full Access, ⚠️ Limited Access, ❌ No Access

### 7.3 Department-Based Access

#### 7.3.1 Sales Department
**Roles:** Sales Manager, Sales Officer (SPPG), Sales Officer (Regular)
**Primary Access:**
- Customer Management
- Sales Order Processing
- Quotations
- Sales Reporting
- Commission Tracking

#### 7.3.2 Finance Department
**Roles:** Finance Manager, Finance Staff
**Primary Access:**
- Complete Accounting System
- Financial Reporting
- Tax Management
- Budget Control
- Payment Processing

#### 7.3.3 Operations Department
**Roles:** Procurement Manager, Warehouse Manager, Warehouse Staff
**Primary Access:**
- Supplier Management
- Purchase Orders
- Inventory Management
- Warehouse Operations
- Quality Control

#### 7.3.4 Logistics Department
**Roles:** Delivery Coordinator, Drivers
**Primary Access:**
- Delivery Management
- Route Planning
- Fleet Management
- Proof of Delivery
- Delivery Analytics

#### 7.3.5 HR Department
**Roles:** HR Admin, HR Staff
**Primary Access:**
- Employee Management
- Payroll Processing
- Performance Management
- Training Records
- HR Reporting

### 7.4 Security & Authentication

#### 7.4.1 Authentication Methods
- **Username & Password** (Primary)
- **Two-Factor Authentication** (Optional for sensitive roles)
- **Session Management** (Auto logout after inactivity)
- **Password Policies** (Complexity, expiry, history)

#### 7.4.2 Access Control Features
- **IP Whitelist** (For office access)
- **Time-based Access** (Business hours only for staff)
- **Device Management** (Limit concurrent sessions)
- **Permission Inheritance** (Role-based with overrides)

#### 7.4.3 Audit & Monitoring
- **Login/Logout Tracking**

---

### 5.7.3 Procurement & Supplier Management (Extended)

#### 5.7.1 Supplier Database Management
**Fitur:**
- Supplier database & evaluation
- Purchase order management
- Price comparison & negotiation
- Supplier performance tracking
- Quality control records
- Payment scheduling
- Cost analysis
- Supplier relationship management
- Supplier categorization (SPPG, Regular, Government, Local)

**Manajemen Pembelian:**
- **Supplier Database:** Data lengkap pemasok
- **Purchase Order:** PO creation, approval, tracking
- **Price Comparison:** Multiple supplier quotes
- **Negotiation Tracking:** History negosiasi harga
- **Quality Control:** Inspection dan quality records
- **Performance Evaluation:** Supplier rating system
- **Payment Terms:** Management pembayaran ke supplier
- SPPG forms with proper signatures
- Institution verification documents
- Product certification (Halal, BPOM, SNI)
- Distribution reports
- Financial transparency documents

**Process Compliance:**
- Standard operating procedures (SOP)
- Quality control checkpoints
- Timeline adherence
- Reporting schedules
- Audit readiness

#### 8.1.2 Financial Compliance
**Tax Compliance:**
- PPN (VAT) calculation & reporting
- PPh 21/23 withholding tax
- Annual tax reporting
- E-filing integration
- Tax invoice requirements

**Accounting Standards:**
- PSAK compliance
- Double-entry bookkeeping
- Financial reporting standards
- Audit trail requirements
- Document retention policies

### 8.2 Audit Trail System

#### 8.2.1 Logging Requirements
**User Actions:**
- Login/logout activities
- Data creation/modification/deletion
- Status changes
- File uploads/downloads
- Permission changes

**System Events:**
- Configuration changes
- Error occurrences
- Performance metrics
- Security events
- Backup activities

#### 8.2.2 Audit Data Structure
```sql
AuditLogs: id, user_id, action, table_name, record_id,
          old_values, new_values, ip_address, user_agent,
          timestamp, session_id
```

#### 8.2.3 Reporting & Investigation
**Audit Reports:**
- User activity logs
- Data change history
- Compliance status reports
- Security incident reports
- Performance audit reports

**Investigation Tools:**
- Log search & filtering
- Timeline reconstruction
- Anomaly detection
- Pattern analysis
- Evidence collection

---

## 🎨 FRONTEND ARCHITECTURE & USER TYPES

### 12.1 Frontend Technology Recommendation

#### 12.1.2 Hybrid Frontend Architecture (Recommended)
**Complete Hybrid Stack:**
```
┌─────────────────────────────────────────────────────────────────┐
│                 WEB APP (PHP/Laravel)                     │
│  Admin Panel  │  Dashboard  │  Reports                      │
│  - Responsive Design  │  - Real-time Data  │  - Export/Import     │
│  - Role-based Access  │  - Analytics  │  - PDF Generation       │
└─────────────────────────────────┬───────────────────────────────┘
                              │ HTTPS/REST API
┌─────────────────────────────────┴───────────────────────────────┐
│              WEB ADMIN (Laravel + Blade)                   │
│  Dashboard  │  User Management  │  Financial Reports         │
│  - Analytics  │  - Permissions   │  - Exports              │
└─────────────────────────────────┬───────────────────────────────┘
                              │ API Gateway
┌─────────────────────────────────┴───────────────────────────────┐
│              LARAVEL API BACKEND                           │
│  Authentication  │  Rate Limiting  │  Multi-Database       │
└─────────────────────────────────────────────────────────────────────┘
```

**Technology Stack per Layer:**
- **Web Apps:** PHP 8.1+ + Laravel 10.x + MySQL 8.0+ + Redis
- **Frontend:** Bootstrap 5.x + jQuery 3.x + Tailwind CSS + Alpine.js
- **API Backend:** Laravel 10 + MySQL + Redis + External AI Server (Python)
- **Infrastructure:** Docker + Nginx + PHP-FPM

#### **Phase 1: Laravel Backend + Web Admin (Bulan 1-3)**
**Development Focus:**
- Laravel API backend dengan authentication
- Multi-database setup & configuration
- Web admin dashboard dengan Blade templates
- Core functionality implementation
- API endpoint development

**Web Admin Features:**
```php
// routes/web.php - Web admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/sppg', [SPPGController::class, 'index']);
    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::get('/financial', [FinancialController::class, 'index']);
    Route::get('/reports', [ReportController::class, 'index']);
});
```

**Blade Component Example:**
```php
<!-- resources/views/components/dashboard-card.blade.php -->
<div class="bg-white rounded-lg shadow-md p-6" x-data="{ isOpen: false }">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $value }}</p>
            <p class="text-sm text-gray-500">{{ $description }}</p>
        </div>
        <div class="text-gray-500" @click="isOpen = !isOpen">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
    </div>
    <div x-show="isOpen" x-transition class="mt-4 text-sm text-gray-600">
        {{ $details }}
    </div>
</div>
```

#### **Phase 2: AI Integration & Advanced Features (Bulan 4-7)**
**Development Focus:**
- Python FastAPI server setup untuk AI processing
- TensorFlow/PyTorch model integration
- Price prediction algorithms
- Menu optimization AI
- Real-time analytics dashboard

**React Native Project Structure:**
```
sppg-mobile/
├── src/
│   ├── components/
│   │   ├── common/
│   │   │   ├── Button.tsx
│   │   │   ├── Input.tsx
│   │   │   └── Card.tsx
│   │   ├── forms/
│   │   │   ├── OrderForm.tsx
│   │   │   ├── CustomerForm.tsx
│   │   │   └── InventoryForm.tsx
│   ├── screens/
│   │   ├── auth/
│   │   │   ├── LoginScreen.tsx
│   │   │   └── RegisterScreen.tsx
│   │   ├── sales/
│   │   │   ├── CustomerListScreen.tsx
│   │   │   ├── OrderCreateScreen.tsx
│   │   │   └── OrderDetailScreen.tsx
│   │   ├── delivery/
│   │   │   ├── RouteScreen.tsx
│   │   │   ├── DeliveryScreen.tsx
│   │   │   └── ProofScreen.tsx
│   │   └── warehouse/
│   │       ├── InventoryScreen.tsx
│   │       ├── ScanScreen.tsx
│   │       └── QualityScreen.tsx
│   ├── services/
│   │   ├── ApiService.ts
│   │   ├── SyncService.ts
│   │   ├── StorageService.ts
│   │   └── LocationService.ts
│   ├── store/
│   │   ├── slices/
│   │   │   ├── authSlice.ts
│   │   │   ├── orderSlice.ts
│   │   │   └── inventorySlice.ts
│   │   └── store.ts
│   └── utils/
│       ├── constants.ts
│       ├── helpers.ts
│       └── types.ts
├── android/
├── ios/
└── package.json
```

**Admin Panel Component Example:**
```php
// resources/views/dashboard.blade.php
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Total Sales</h5>
                    <h3>Rp {{ number_format($totalSales, 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
</div>

<script>
// Chart.js implementation
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chartLabels),
        datasets: [{
            label: 'Sales Trend',
            data: @json($chartData),
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    }
});
</script>
@endsection
```

interface OrderItem {
  product_id: string;
  quantity: number;
  unit_price: number;
}

const OrderCreateScreen: React.FC = ({ navigation }) => {
  const [customers, setCustomers] = useState([]);
  const [products, setProducts] = useState([]);
  const [selectedCustomer, setSelectedCustomer] = useState('');
  const [orderItems, setOrderItems] = useState<OrderItem[]>([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    loadCustomers();
    loadProducts();
  }, []);

  const loadCustomers = async () => {
    try {
      const data = await ApiService.getCustomers();
      setCustomers(data);
    } catch (error) {
      console.error('Load customers error:', error);
    }
  };

  const loadProducts = async () => {
    try {
      const data = await ApiService.getProducts();
      setProducts(data);
    } catch (error) {
      console.error('Load products error:', error);
    }
  };

  const addItem = () => {
    setOrderItems([...orderItems, {
      product_id: '',
      quantity: 1,
      unit_price: 0
    }]);
  };

  const createOrder = async () => {
    if (!selectedCustomer || orderItems.length === 0) {
      Alert.alert('Error', 'Please select customer and add items');
      return;
    }

    setLoading(true);
    try {
      const orderData = {
        customer_id: selectedCustomer,
        items: orderItems,
        total_amount: orderItems.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0)
      };

      const response = await ApiService.createOrder(orderData);
      
      if (response.success) {
        Alert.alert('Success', 'Order created successfully');
        navigation.goBack();
      } else {
        Alert.alert('Error', response.message);
      }
    } catch (error) {
      console.error('Create order error:', error);
      Alert.alert('Error', 'Failed to create order');
      
      // Save offline if network error
      if (error.message === 'Network Error') {
        await SyncService.addPendingOperation({
          type: 'create_order',
          data: orderData,
          timestamp: new Date().toISOString()
        });
        Alert.alert('Offline', 'Order saved locally, will sync when online');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={{ flex: 1, padding: 20 }}>
      <Text style={{ fontSize: 24, fontWeight: 'bold', marginBottom: 20 }}>
        Create Order
      </Text>
      
      <Text style={{ marginBottom: 10 }}>Customer:</Text>
      <Picker
        selectedValue={selectedCustomer}
        onValueChange={setSelectedCustomer}
        style={{ marginBottom: 20 }}
      >
        <Picker.Item label="Select Customer" value="" />
        {customers.map((customer: any) => (
          <Picker.Item 
            key={customer.id} 
            label={customer.name} 
            value={customer.id} 
          />
        ))}
      </Picker>

      <View style={{ marginBottom: 20 }}>
        <View style={{ flexDirection: 'row', justifyContent: 'space-between' }}>
          <Text style={{ fontSize: 18, fontWeight: 'bold' }}>Order Items</Text>
          <TouchableOpacity onPress={addItem}>
            <Text style={{ color: '#007AFF', fontSize: 16 }}>+ Add Item</Text>
          </TouchableOpacity>
        </View>
        
        {orderItems.map((item, index) => (
          <View key={index} style={{ marginBottom: 10 }}>
            <Picker
              selectedValue={item.product_id}
              onValueChange={(value) => {
                const newItems = [...orderItems];
                newItems[index].product_id = value;
                setOrderItems(newItems);
              }}
            >
              <Picker.Item label="Select Product" value="" />
              {products.map((product: any) => (
                <Picker.Item 
                  key={product.id} 
                  label={product.name} 
                  value={product.id} 
                />
              ))}
            </Picker>
            
            <TextInput
              style={{ borderWidth: 1, borderColor: '#ccc', padding: 10, marginTop: 5 }}
              placeholder="Quantity"
              value={item.quantity.toString()}
              onChangeText={(value) => {
                const newItems = [...orderItems];
                newItems[index].quantity = parseInt(value) || 0;
                setOrderItems(newItems);
              }}
              keyboardType="numeric"
            />
          </View>
        ))}
      </View>

      <TouchableOpacity
        style={{ 
          backgroundColor: loading ? '#ccc' : '#007AFF', 
          padding: 15, 
          borderRadius: 5, 
          alignItems: 'center' 
        }}
        onPress={createOrder}
        disabled={loading}
      >
        <Text style={{ color: 'white', fontSize: 18, fontWeight: 'bold' }}>
          {loading ? 'Creating...' : 'Create Order'}
        </Text>
      </TouchableOpacity>
    </View>
  );
};

export default OrderCreateScreen;
```

#### **Phase 3: Integration & Testing (Bulan 8-10)**
**Development Focus:**
- API integration testing
- Offline synchronization testing
- Performance optimization
- User acceptance testing
- Security testing

**Integration Testing:**
```php
// tests/Feature/ApiIntegrationTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;

class ApiIntegrationTest extends TestCase
{
    public function test_mobile_api_authentication()
    {
        // Test mobile API authentication
        $user = User::factory()->create();
        
        $response = $this->postJson('/api/mobile/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'token',
                        'user' => [
                            'id',
                            'name',
                            'email'
                        ]
                    ]
                ]);
    }

    public function test_order_creation_api()
    {
        $user = User::factory()->create();
        $token = $user->createToken('mobile')->accessToken;

        $orderData = [
            'customer_id' => 1,
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 10,
                    'unit_price' => 50000
                ]
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'order_number',
                        'total_amount',
                        'status'
                    ]
                ]);
    }

    public function test_offline_sync_api()
    {
        // Test offline synchronization
        $user = User::factory()->create();
        $token = $user->createToken('mobile')->accessToken;

        $pendingOperations = [
            [
                'type' => 'create_order',
                'data' => [
                    'customer_id' => 1,
                    'items' => []
                ],
                'timestamp' => now()->toISOString()
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->postJson('/api/sync/offline', [
            'operations' => $pendingOperations
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'synced_count' => 1,
                    'failed_count' => 0
                ]);
    }
}
```

#### **Phase 4: Deployment & Launch (Bulan 11-12)**
**Development Focus:**
- Production deployment setup
- Performance monitoring
- User training
- Marketing & launch
- Support system setup

**Deployment Configuration:**
```yaml
# docker-compose.yml
version: '3.8'

services:
  # Laravel API Backend
  api:
    build:
      context: ./api
      dockerfile: Dockerfile
    ports:
      - "8000:8000"
    environment:
      - DB_HOST=mysql
      - REDIS_HOST=redis
      - AI_SERVER_URL=http://ai-server:8000
    depends_on:
      - mysql
      - redis
    volumes:
      - ./api:/var/www/html
      - ./api/storage:/var/www/html/storage

  # MySQL Database
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: sppg_users
      MYSQL_USER: sppg_user
      MYSQL_PASSWORD: userpassword
    volumes:
      - mysql_data:/var/lib/mysql
    ports:
      - "3306:3306"

  # PostgreSQL Database
  pgsql:
    image: postgres:14
    environment:
      POSTGRES_DB: sppg_main
      POSTGRES_USER: sppg_user
      POSTGRES_PASSWORD: userpassword
    volumes:
      - pgsql_data:/var/lib/postgresql/data
    ports:
      - "5432:5432"

  # MongoDB Database
  mongodb:
    image: mongo:6.0
    environment:
      MONGO_INITDB_ROOT_USERNAME: admin
      MONGO_INITDB_ROOT_PASSWORD: adminpassword
      MONGO_INITDB_DATABASE: sppg_analytics
    volumes:
      - mongodb_data:/data/db
    ports:
      - "27017:27017"

  # Redis Cache
  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data

  # Nginx Reverse Proxy
  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx/nginx.conf:/etc/nginx/nginx.conf
      - ./nginx/ssl:/etc/nginx/ssl
    depends_on:
      - api

  # AI Server (Python)
  ai-server:
    build: ./ai-server
    environment:
      - DATABASE_URL=mysql://user:password@mysql:3306/sppg_ai
      - REDIS_URL=redis://redis:6379
    depends_on:
      - mysql
      - redis
    ports:
      - "8000:8000"
    volumes:
      - ./ai-server:/app

volumes:
  mysql_data:
  redis_data:
```

### 12.1.3 Team Composition & Development Strategy

#### **Development Team Structure:**
**Core Team (6-8 orang):**
- **Backend Developer (Anda):** Laravel API development
- **AI/ML Developer (1 orang):** Python FastAPI + ML models
- **Frontend Developer (1 orang):** Web admin dengan Laravel Blade
- **UI/UX Designer (1 orang):** Design untuk web interface
- **QA Engineer (1 orang):** Testing untuk web & API
- **DevOps Engineer (1 orang):** Deployment & infrastructure
- **Project Manager (1 orang):** Project coordination
- **Database Admin (1 orang):** MySQL optimization & maintenances (2 minggu per sprint):**
- **Sprint 1-2:** Laravel backend + web admin
- **Sprint 3-4:** AI server development + ML model integration
- **Sprint 5-6:** API integration + testing
- **Sprint 7-8:** Deployment + optimization

#### **Technology Stack Summary:**
```json
{
  "backend": {
    "framework": "Laravel 10",
    "language": "PHP 8.1+",
    "databases": ["MySQL 8.0+", "Redis"],
    "api": "RESTful dengan JWT authentication"
  },
  "frontend": {
    "framework": "Bootstrap 5.x + jQuery 3.x",
    "ui_library": "Tailwind CSS + Alpine.js",
    "charts": "Chart.js / ApexCharts",
    "target": "Admin & Management users"
  },
  "ai_server": {
    "framework": "FastAPI",
    "language": "Python 3.9+",
    "ml_libs": ["TensorFlow", "scikit-learn"],
    "purpose": "Price prediction & menu optimization"
      "target": "Field operations users"
    }
  },
  "infrastructure": {
    "containerization": "Docker",
    "web_server": "Nginx",
    "process_manager": "PHP-FPM",
    "deployment": "Docker Compose"
  }
}
```

### 12.1.4 Cost & Timeline Analysis

#### **Development Cost Comparison:**
**Hybrid Approach (Recommended):**
- **Backend Development:** Rp 300 juta (3 bulan)
- **Web Admin:** Rp 200 juta (2 bulan)
- **Mobile Apps:** Rp 800 juta (4 bulan)
- **Integration & Testing:** Rp 200 juta (2 bulan)
- **Deployment & Launch:** Rp 100 juta (1 bulan)
- **Total:** Rp 1.6 miliar (12 bulan)

**PHP-Only Approach (Alternative):**
- **Backend + Web:** Rp 400 juta (4 bulan)
- **PWA Development:** Rp 600 juta (4 bulan)
- **Integration & Testing:** Rp 200 juta (2 bulan)
- **Deployment & Launch:** Rp 100 juta (1 bulan)
- **Total:** Rp 1.3 miliar (11 bulan)

#### **Performance Comparison:**
**Hybrid Approach:**
- **Mobile Performance:** 90-95% native performance
- **User Experience:** Native UI/UX patterns
- **Offline Capability:** Excellent offline support
- **Device Integration:** Full native device access
- **App Store Distribution:** Proper app store presence

**PHP-Only Approach:**
- **Mobile Performance:** 70-80% native performance
- **User Experience:** Web-like experience
- **Offline Capability:** Good but limited
- **Device Integration:** Limited device access
- **Distribution:** Web-based, no app store

### 12.1.5 Final Recommendation

#### **🏆 HYBRID APPROACH - BEST CHOICE:**

**Why Hybrid is Best for You:**
1. **Maximizes PHP Skills:** Anda tetap pakai Laravel untuk backend
2. **Optimal Mobile Experience:** Native performance untuk field operations
3. **Cost-Effective:** Lebih murah daripada pure React development
4. **Faster Development:** Anda bisa maintain backend sendiri
5. **Future-Proof:** Scalable architecture untuk growth
6. **User Adoption:** Better user experience untuk mobile users

**Key Success Factors:**
- **Leverage Existing Skills:** PHP expertise untuk backend
- **Modern Mobile Experience:** React Native untuk field operations
- **Integrated Architecture:** Seamless communication antar platforms
- **Scalable Design:** Support untuk 1000+ concurrent users
- **Indonesian Compliance:** Sesuai regulasi lokal

**Expected Outcomes:**
- **Development Time:** 12 bulan untuk production-ready system
- **User Adoption:** 80%+ adoption dalam 3 bulan
- **Performance:** <200ms response time untuk API
- **Scalability:** Support untuk 500+ concurrent mobile users
- **ROI:** 200%+ dalam 3 tahun

### 12.2 Zero-Cost/Low-Cost Alternatives

#### 12.2.1 Free Technology Stack
**Backend (Laravel - FREE):**
- Laravel Framework (Open Source)
- MySQL Community Edition (FREE)
- PostgreSQL (FREE)
- Redis (FREE)
- Docker Community Edition (FREE)

**Frontend (FREE):**
- Laravel Blade (Built-in)
- Tailwind CSS (FREE)
- Alpine.js (FREE)
- PWA (FREE)
- GitHub Pages (FREE hosting)

**API (FREE):**
- RESTful API (Built-in Laravel)
- JWT Authentication (FREE packages)
- Rate Limiting (Built-in Laravel)
- File Storage (Local storage)

#### 12.2.2 Zero-Cost Architecture
```
FREE STACK:
├── Backend: Laravel + MySQL + Redis (FREE)
├── Frontend: Laravel Blade + Tailwind + Alpine.js (FREE)
├── Mobile: PWA (FREE)
├── Database: MySQL + PostgreSQL (FREE)
├── Hosting: VPS $5/bulan atau GitHub Pages (FREE)
└── Tools: Git + VS Code + Docker (FREE)
```

#### 12.2.3 Cost Comparison
**FREE Stack:**
- Development: Rp 300 juta (6 bulan)
- Hosting: $5/bulan (Rp 75ribu/bulan)
- Total: Rp 300 juta + Rp 900ribu/tahun

**Paid Stack:**
- Development: Rp 1.6 miliar (12 bulan)
- Hosting: $200/bulan (Rp 3 juta/bulan)
- Total: Rp 1.6 miliar + Rp 36 juta/tahun

**Savings: Rp 1.3 miliar + Rp 35.1 juta/tahun**

#### 12.2.4 Free Implementation Strategy
**Phase 1 (Bulan 1-3): Laravel Backend**
```php
// Install Laravel (FREE)
composer create-project laravel/laravel sppg-free

// Free packages
composer require laravel/ui
composer require laravel/sanctum
composer require intervention/image
```

**Phase 2 (Bulan 4-5): PWA Frontend**
```javascript
// PWA Manifest (FREE)
{
  "name": "SPPG Mobile",
  "short_name": "SPPG",
  "start_url": "/",
  "display": "standalone"
}
```

**Phase 3 (Bulan 6): Deployment**
```bash
# Free deployment options
1. GitHub Pages (FREE)
2. Netlify (FREE tier)
3. VPS $5/bulan (DigitalOcean)
4. Railway.app (FREE tier)
```

#### 12.2.5 Free Tools & Services
**Development Tools (FREE):**
- VS Code (FREE)
- Git/GitHub (FREE)
- Docker Community (FREE)
- Postman (FREE)
- Chrome DevTools (FREE)

**Hosting Options (FREE):**
- GitHub Pages (Static sites)
- Netlify (JAMstack)
- Vercel (Frontend)
- Railway.app (Backend)
- Supabase (Database)

**Monitoring (FREE):**
- Google Analytics
- Sentry (FREE tier)
- Uptime Robot (FREE)
- GitHub Actions (CI/CD)

#### 12.2.6 Limitations & Workarounds
**Limitations:**
- No custom domain (FREE tier)
- Limited storage (FREE tier)
- No dedicated support
- Limited bandwidth

**Workarounds:**
- Use subdomains
- Optimize images
- Community support
- CDN optimization

#### 12.2.7 Migration Path
**Start FREE → Scale PAID:**
1. **MVP:** FREE stack untuk launch
2. **Growth:** Add paid services saat revenue
3. **Scale:** Migrate ke enterprise stack
4. **Enterprise:** Full paid stack

### 12.3 Modular Architecture & Dynamic Feature Management

#### 12.3.1 Modular System Design
**Modular Architecture Pattern:**
```
MODULAR SYSTEM:
├── Core Module (Required)
│   ├── Authentication
│   ├── User Management
│   ├── Basic Dashboard
│   └── System Settings
├── Business Modules (Optional)
│   ├── SPPG Management
│   ├── Inventory Management
│   ├── Financial Management
│   ├── Procurement Management
│   ├── Logistics Management
│   └── Reporting Module
├── Extension Modules (Future)
│   ├── CRM Module
│   ├── HR Module
│   ├── Asset Management
│   ├── Project Management
│   └── Analytics Module
└── Integration Modules
    ├── BGN Integration
    ├── Payment Gateway
    ├── SMS Gateway
    └── Email Service
```

#### 12.3.2 Dynamic Feature Management
**Feature Flag System:**
```php
// app/Services/FeatureService.php
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Feature;
use Illuminate\Support\Facades\Cache;

class FeatureService
{
    public static function isEnabled(string $feature, ?User $user = null): bool
    {
        $cacheKey = "feature_{$feature}" . ($user ? "_{$user->id}" : "");
        
        return Cache::remember($cacheKey, 3600, function () use ($feature, $user) {
            $featureModel = Feature::where('code', $feature)->first();
            
            if (!$featureModel) {
                return false;
            }
            
            // Global feature status
            if (!$featureModel->is_active) {
                return false;
            }
            
            // User-specific feature access
            if ($user) {
                return $user->hasFeatureAccess($feature);
            }
            
            return $featureModel->is_default_enabled;
        });
    }
    
    public static function enableFeature(string $feature, User $user): void
    {
        $user->features()->attach($feature);
        Cache::forget("feature_{$feature}_{$user->id}");
    }
    
    public static function disableFeature(string $feature, User $user): void
    {
        $user->features()->detach($feature);
        Cache::forget("feature_{$feature}_{$user->id}");
    }
}
```

**Feature Management Database:**
```php
// database/migrations/create_features_table.php
Schema::create('features', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique();
    $table->string('name');
    $table->text('description');
    $table->string('module');
    $table->boolean('is_active')->default(true);
    $table->boolean('is_default_enabled')->default(false);
    $table->json('dependencies')->nullable();
    $table->timestamps();
});

// database/migrations/create_feature_user_table.php
Schema::create('feature_user', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('feature_id')->constrained()->onDelete('cascade');
    $table->boolean('is_enabled')->default(true);
    $table->timestamp('enabled_at')->nullable();
    $table->primary(['user_id', 'feature_id']);
});
```

#### 12.3.3 Module Registration System
**Module Provider Pattern:**
```php
// app/Providers/ModuleServiceProvider.php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    private $modules = [
        'sppg' => [
            'name' => 'SPPG Management',
            'provider' => \App\Modules\SPPG\SPPGServiceProvider::class,
            'routes' => 'modules/sppg/routes/web.php',
            'views' => 'modules/sppg/views',
            'migrations' => 'modules/sppG/database/migrations',
            'dependencies' => ['users', 'inventory'],
        ],
        'inventory' => [
            'name' => 'Inventory Management',
            'provider' => \App\Modules\Inventory\InventoryServiceProvider::class,
            'routes' => 'modules/inventory/routes/web.php',
            'views' => 'modules/inventory/views',
            'migrations' => 'modules/inventory/database/migrations',
            'dependencies' => ['users'],
        ],
        'financial' => [
            'name' => 'Financial Management',
            'provider' => \App\Modules\Financial\FinancialServiceProvider::class,
            'routes' => 'modules/financial/routes/web.php',
            'views' => 'modules/financial/views',
            'migrations' => 'modules/financial/database/migrations',
            'dependencies' => ['users', 'inventory'],
        ],
    ];
    
    public function boot()
    {
        $this->registerActiveModules();
    }
    
    private function registerActiveModules()
    {
        foreach ($this->modules as $moduleCode => $module) {
            if ($this->isModuleActive($moduleCode)) {
                $this->registerModule($moduleCode, $module);
            }
        }
    }
    
    private function isModuleActive(string $moduleCode): bool
    {
        return FeatureService::isEnabled("module_{$moduleCode}");
    }
    
    private function registerModule(string $moduleCode, array $module)
    {
        // Register module service provider
        if (class_exists($module['provider'])) {
            $this->app->register($module['provider']);
        }
        
        // Load module routes
        if (file_exists(base_path($module['routes']))) {
            Route::middleware(['web', 'auth'])->prefix($moduleCode)
                ->group(base_path($module['routes']));
        }
        
        // Register module views
        if (is_dir(base_path($module['views']))) {
            $this->loadViewsFrom(base_path($module['views']), $moduleCode);
        }
    }
}
```

#### 12.3.4 Dynamic Menu System
**Menu Management dengan Feature Flags:**
```php
// app/Services/MenuService.php
<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class MenuService
{
    public static function getUserMenu(User $user): Collection
    {
        $menuItems = [
            [
                'title' => 'Dashboard',
                'icon' => 'dashboard',
                'route' => 'dashboard',
                'feature' => 'dashboard',
                'children' => []
            ],
            [
                'title' => 'SPPG Management',
                'icon' => 'users',
                'route' => 'sppg.index',
                'feature' => 'module_sppg',
                'children' => [
                    [
                        'title' => 'Applications',
                        'route' => 'sppg.applications',
                        'feature' => 'sppg_applications'
                    ],
                    [
                        'title' => 'Institutions',
                        'route' => 'sppg.institutions',
                        'feature' => 'sppg_institutions'
                    ],
                    [
                        'title' => 'Distributions',
                        'route' => 'sppg.distributions',
                        'feature' => 'sppg_distributions'
                    ]
                ]
            ],
            [
                'title' => 'Inventory',
                'icon' => 'package',
                'route' => 'inventory.index',
                'feature' => 'module_inventory',
                'children' => [
                    [
                        'title' => 'Products',
                        'route' => 'inventory.products',
                        'feature' => 'inventory_products'
                    ],
                    [
                        'title' => 'Stock',
                        'route' => 'inventory.stock',
                        'feature' => 'inventory_stock'
                    ],
                    [
                        'title' => 'Movements',
                        'route' => 'inventory.movements',
                        'feature' => 'inventory_movements'
                    ]
                ]
            ],
            [
                'title' => 'Financial',
                'icon' => 'dollar-sign',
                'route' => 'financial.index',
                'feature' => 'module_financial',
                'children' => [
                    [
                        'title' => 'Transactions',
                        'route' => 'financial.transactions',
                        'feature' => 'financial_transactions'
                    ],
                    [
                        'title' => 'Reports',
                        'route' => 'financial.reports',
                        'feature' => 'financial_reports'
                    ],
                    [
                        'title' => 'Invoices',
                        'route' => 'financial.invoices',
                        'feature' => 'financial_invoices'
                    ]
                ]
            ],
            [
                'title' => 'Settings',
                'icon' => 'settings',
                'route' => 'settings.index',
                'feature' => 'settings',
                'children' => [
                    [
                        'title' => 'Users',
                        'route' => 'settings.users',
                        'feature' => 'user_management'
                    ],
                    [
                        'title' => 'Features',
                        'route' => 'settings.features',
                        'feature' => 'feature_management'
                    ],
                    [
                        'title' => 'Modules',
                        'route' => 'settings.modules',
                        'feature' => 'module_management'
                    ]
                ]
            ]
        ];
        
        return collect($menuItems)
            ->filter(function ($item) use ($user) {
                return FeatureService::isEnabled($item['feature'], $user);
            })
            ->map(function ($item) use ($user) {
                $item['children'] = collect($item['children'])
                    ->filter(function ($child) use ($user) {
                        return FeatureService::isEnabled($child['feature'], $user);
                    })
                    ->values();
                
                return $item;
            })
            ->values();
    }
}
```

#### 12.3.5 Module Installation System
**Module Installer:**
```php
// app/Console/Commands/InstallModule.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class InstallModule extends Command
{
    protected $signature = 'module:install {module}';
    protected $description = 'Install a new module';
    
    public function handle()
    {
        $module = $this->argument('module');
        
        $this->info("Installing module: {$module}");
        
        // 1. Create module directory
        $this->createModuleDirectory($module);
        
        // 2. Copy module files
        $this->copyModuleFiles($module);
        
        // 3. Run migrations
        $this->runMigrations($module);
        
        // 4. Register module
        $this->registerModule($module);
        
        // 5. Enable feature flags
        $this->enableFeatureFlags($module);
        
        $this->info("Module {$module} installed successfully!");
    }
    
    private function createModuleDirectory(string $module): void
    {
        $path = base_path("modules/{$module}");
        
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }
    
    private function copyModuleFiles(string $module): void
    {
        $source = base_path("stubs/module");
        $destination = base_path("modules/{$module}");
        
        File::copyDirectory($source, $destination);
    }
    
    private function runMigrations(string $module): void
    {
        $migrationPath = "modules/{$module}/database/migrations";
        
        if (File::exists(base_path($migrationPath))) {
            Artisan::call('migrate', [
                '--path' => $migrationPath,
                '--force' => true
            ]);
        }
    }
    
    private function registerModule(string $module): void
    {
        // Register module in configuration
        $config = config('modules', []);
        $config[$module] = [
            'name' => ucfirst($module),
            'enabled' => true,
            'version' => '1.0.0'
        ];
        
        // Save to config file
        file_put_contents(
            config_path('modules.php'),
            '<?php return ' . var_export($config, true) . ';'
        );
    }
    
    private function enableFeatureFlags(string $module): void
    {
        $features = [
            "module_{$module}" => [
                'name' => ucfirst($module) . ' Module',
                'description' => "Enable {$module} module functionality",
                'module' => $module,
                'is_active' => true,
                'is_default_enabled' => false
            ]
        ];
        
        foreach ($features as $code => $data) {
            \App\Models\Feature::updateOrCreate(
                ['code' => $code],
                $data
            );
        }
    }
}
```

#### 12.3.6 Feature Management UI
**Admin Panel untuk Feature Management:**
```php
// app/Http/Controllers/FeatureController.php
<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\User;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function index()
    {
        $features = Feature::with('users')->get();
        return view('features.index', compact('features'));
    }
    
    public function update(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'is_active' => 'boolean',
            'is_default_enabled' => 'boolean'
        ]);
        
        $feature->update($validated);
        
        // Clear cache
        Cache::forget("feature_{$feature->code}");
        
        return back()->with('success', 'Feature updated successfully');
    }
    
    public function toggleUserFeature(Request $request, Feature $feature, User $user)
    {
        $isEnabled = $request->boolean('enabled');
        
        if ($isEnabled) {
            $user->features()->attach($feature->id, [
                'is_enabled' => true,
                'enabled_at' => now()
            ]);
        } else {
            $user->features()->detach($feature->id);
        }
        
        // Clear cache
        Cache::forget("feature_{$feature->code}_{$user->id}");
        
        return back()->with('success', 'User feature updated successfully');
    }
}
```

**Feature Management Blade View:**
```php
<!-- resources/views/features/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1>Feature Management</h1>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Global Features</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Feature</th>
                                <th>Module</th>
                                <th>Global Status</th>
                                <th>Default</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($features as $feature)
                            <tr>
                                <td>{{ $feature->name }}</td>
                                <td>{{ $feature->module }}</td>
                                <td>
                                    <form action="{{ route('features.update', $feature) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="checkbox" name="is_active" 
                                            {{ $feature->is_active ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td>
                                    <form action="{{ route('features.update', $feature) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="checkbox" name="is_default_enabled" 
                                            {{ $feature->is_default_enabled ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" 
                                        onclick="showUserFeatures({{ $feature->id }})">
                                        User Access
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3>Module Status</h3>
                </div>
                <div class="card-body">
                    @foreach($features->groupBy('module') as $module => $moduleFeatures)
                    <div class="mb-3">
                        <h5>{{ ucfirst($module) }}</h5>
                        <div class="progress">
                            @php
                            $enabled = $moduleFeatures->where('is_active', true)->count();
                            $total = $moduleFeatures->count();
                            $percentage = $total > 0 ? ($enabled / $total) * 100 : 0;
                            @endphp
                            <div class="progress-bar" style="width: {{ $percentage }}%">
                                {{ $enabled }}/{{ $total }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Feature Modal -->
<div class="modal fade" id="userFeatureModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Feature Access</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="userFeatureContent">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showUserFeatures(featureId) {
    fetch(`/features/${featureId}/users`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('userFeatureContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('userFeatureModal')).show();
        });
}
</script>
@endsection
```

#### 12.3.7 Real-time Feature Updates
**WebSocket untuk Real-time Updates:**
```javascript
// resources/js/feature-updates.js
class FeatureUpdates {
    constructor() {
        this.socket = new WebSocket('ws://localhost:6001/app/feature-updates');
        this.setupEventListeners();
    }
    
    setupEventListeners() {
        this.socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            this.handleFeatureUpdate(data);
        };
    }
    
    handleFeatureUpdate(data) {
        switch(data.type) {
            case 'feature_enabled':
                this.enableFeature(data.feature);
                break;
            case 'feature_disabled':
                this.disableFeature(data.feature);
                break;
            case 'module_installed':
                this.installModule(data.module);
                break;
            case 'module_uninstalled':
                this.uninstallModule(data.module);
                break;
        }
    }
    
    enableFeature(featureCode) {
        // Show/hide menu items
        const menuItems = document.querySelectorAll(`[data-feature="${featureCode}"]`);
        menuItems.forEach(item => item.classList.remove('d-none'));
        
        // Enable buttons
        const buttons = document.querySelectorAll(`[data-feature-button="${featureCode}"]`);
        buttons.forEach(button => button.disabled = false);
        
        // Show notification
        this.showNotification(`Feature ${featureCode} enabled`, 'success');
    }
    
    disableFeature(featureCode) {
        // Hide menu items
        const menuItems = document.querySelectorAll(`[data-feature="${featureCode}"]`);
        menuItems.forEach(item => item.classList.add('d-none'));
        
        // Disable buttons
        const buttons = document.querySelectorAll(`[data-feature-button="${featureCode}"]`);
        buttons.forEach(button => button.disabled = true);
        
        // Show notification
        this.showNotification(`Feature ${featureCode} disabled`, 'warning');
    }
    
    showNotification(message, type) {
        // Show toast notification
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
}

// Initialize feature updates
new FeatureUpdates();
```

#### 12.3.8 API untuk Feature Management
**API Endpoints:**
```php
// routes/api.php
Route::middleware(['auth:api'])->group(function () {
    // Feature management
    Route::get('/features', [FeatureApiController::class, 'index']);
    Route::post('/features/{feature}/toggle', [FeatureApiController::class, 'toggle']);
    Route::get('/features/{feature}/users', [FeatureApiController::class, 'users']);
    
    // Module management
    Route::get('/modules', [ModuleApiController::class, 'index']);
    Route::post('/modules/{module}/install', [ModuleApiController::class, 'install']);
    Route::post('/modules/{module}/uninstall', [ModuleApiController::class, 'uninstall']);
});
```

**React Native Feature Detection:**
```typescript
// src/services/FeatureService.ts
export class FeatureService {
    static async getFeatures(): Promise<Feature[]> {
        const response = await ApiService.get('/features');
        return response.data;
    }
    
    static async isFeatureEnabled(featureCode: string): Promise<boolean> {
        const features = await this.getFeatures();
        return features.some(f => f.code === featureCode && f.is_enabled);
    }
    
    static async toggleFeature(featureCode: string): Promise<void> {
        await ApiService.post(`/features/${featureCode}/toggle`);
    }
    
    static async getFeatureUsers(featureCode: string): Promise<User[]> {
        const response = await ApiService.get(`/features/${featureCode}/users`);
        return response.data;
    }
}

// Usage in React Native component
const OrderScreen: React.FC = () => {
    const [hasAdvancedFeatures, setHasAdvancedFeatures] = useState(false);
    
    useEffect(() => {
        FeatureService.isFeatureEnabled('advanced_order_features')
            .then(setHasAdvancedFeatures);
    }, []);
    
    return (
        <View>
            {hasAdvancedFeatures && (
                <Button title="Advanced Order Options" />
            )}
            <Button title="Basic Order" />
        </View>
    );
};
```

### 12.3.9 Benefits of Modular Architecture

#### **Business Benefits:**
- **Scalable Growth:** Add modules as business grows
- **Cost Control:** Pay only for features needed
- **User Segmentation:** Different features for different users
- **Rapid Deployment:** Quick feature rollout
- **A/B Testing:** Test features with specific users

#### **Technical Benefits:**
- **Maintainability:** Independent module maintenance
- **Testing:** Isolated module testing
- **Performance:** Load only needed modules
- **Security:** Granular access control
- **Development:** Parallel module development

#### **Operational Benefits:**
- **Zero Downtime:** Hot module installation
- **Rollback:** Easy feature rollback
- **Monitoring:** Per-module performance tracking
- **Backup:** Module-specific backups
- **Updates:** Independent module updates

### 12.4 Additional Strategic Recommendations

#### 12.4.1 AI & Automation Integration
**AI-Powered Features:**
```
AI INTEGRATION:
├── Predictive Analytics
│   ├── Demand Forecasting
│   ├── Inventory Optimization
│   ├── Sales Trend Analysis
│   └── Customer Behavior Prediction
├── Automation
│   ├── Automated Order Processing
│   ├── Smart Inventory Management
│   ├── Automated Reporting
│   └── Chatbot Customer Service
├── Data Intelligence
│   ├── Pattern Recognition
│   ├── Anomaly Detection
│   ├── Fraud Detection
│   └── Performance Optimization
└── Machine Learning
    ├── Recommendation Engine
    ├── Price Optimization
    ├── Route Optimization
    └── Quality Prediction
```

**Implementation Strategy:**
- **Phase 1:** Basic analytics dashboard
- **Phase 2:** Predictive inventory management
- **Phase 3:** AI-powered recommendations
- **Phase 4:** Full automation system

#### 12.4.2 Blockchain & Supply Chain Transparency
**Blockchain Integration:**
```php
// Blockchain for Supply Chain Traceability
class SupplyChainService
{
    public function recordTransaction($transaction)
    {
        // Record on blockchain
        $blockchainData = [
            'transaction_id' => $transaction->id,
            'product_id' => $transaction->product_id,
            'supplier_id' => $transaction->supplier_id,
            'quantity' => $transaction->quantity,
            'timestamp' => now(),
            'location' => $transaction->location,
            'quality_score' => $transaction->quality_score
        ];
        
        // Store on blockchain (Ethereum/Hyperledger)
        $this->blockchainService->storeTransaction($blockchainData);
    }
    
    public function verifyProductAuthenticity($productId)
    {
        // Verify product authenticity via blockchain
        return $this->blockchainService->verifyProduct($productId);
    }
}
```

**Benefits:**
- **Transparency:** Complete supply chain visibility
- **Authenticity:** Product verification
- **Compliance:** Audit trail automation
- **Trust:** Customer confidence

#### 12.4.3 IoT & Smart Warehouse
**IoT Integration:**
```javascript
// IoT Device Management
class IoTService {
    // Smart shelf monitoring
    async monitorInventoryLevels() {
        const shelves = await this.getSmartShelves();
        
        shelves.forEach(shelf => {
            if (shelf.weight < shelf.min_threshold) {
                this.triggerReorder(shelf.product_id);
            }
            
            if (shelf.temperature > shelf.max_temp) {
                this.triggerTemperatureAlert(shelf.id);
            }
        });
    }
    
    // Smart quality control
    async performQualityCheck(productId) {
        const camera = await this.getQualityCamera();
        const image = await camera.captureImage();
        
        const quality = await this.aiService.analyzeQuality(image);
        
        if (quality.score < 0.8) {
            this.flagProductForInspection(productId);
        }
        
        return quality;
    }
}
```

**IoT Devices:**
- **Smart Shelves:** Real-time inventory monitoring
- **Quality Cameras:** Automated quality inspection
- **Temperature Sensors:** Cold chain monitoring
- **GPS Trackers:** Real-time location tracking
- **RFID Tags:** Automated inventory counting

#### 12.4.4 Advanced Analytics & Business Intelligence
**BI Dashboard Features:**
```php
// Advanced Analytics Service
class AnalyticsService
{
    public function getExecutiveDashboard()
    {
        return [
            'kpi_metrics' => $this->getKPIMetrics(),
            'trend_analysis' => $this->getTrendAnalysis(),
            'predictive_insights' => $this->getPredictiveInsights(),
            'benchmark_comparison' => $this->getBenchmarkComparison(),
            'risk_assessment' => $this->getRiskAssessment(),
            'opportunity_identification' => $this->getOpportunities()
        ];
    }
    
    public function getPredictiveInsights()
    {
        return [
            'demand_forecast' => $this->predictDemand(),
            'inventory_optimization' => $this->optimizeInventory(),
            'price_optimization' => $this->optimizePricing(),
            'customer_churn_prediction' => $this->predictChurn(),
            'supplier_risk_assessment' => $this->assessSupplierRisk()
        ];
    }
}
```

**Analytics Capabilities:**
- **Real-time Dashboards:** Live business metrics
- **Predictive Analytics:** Future trend prediction
- **Benchmarking:** Industry comparison
- **What-if Analysis:** Scenario planning
- **Automated Insights:** AI-powered recommendations

#### 12.4.5 Advanced Security & Compliance
**Security Framework:**
```php
// Advanced Security Service
class SecurityService
{
    public function implementZeroTrust()
    {
        // Zero Trust Architecture
        return [
            'multi_factor_authentication' => $this->enableMFA(),
            'role_based_access_control' => $this->implementRBAC(),
            'continuous_monitoring' => $this->monitorActivity(),
            'encryption_at_rest' => $this->encryptData(),
            'network_segmentation' => $this->segmentNetwork(),
            'threat_detection' => $this->detectThreats()
        ];
    }
    
    public function complianceAutomation()
    {
        // Automated compliance checking
        return [
            'bgn_compliance' => $this->checkBGNCompliance(),
            'tax_compliance' => $this->checkTaxCompliance(),
            'data_protection' => $this->checkDataProtection(),
            'audit_trail' => $this->generateAuditTrail(),
            'compliance_reporting' => $this->generateComplianceReports()
        ];
    }
}
```

**Security Features:**
- **Zero Trust Architecture:** Advanced security model
- **Multi-Factor Authentication:** Enhanced login security
- **Behavioral Analytics:** Anomaly detection
- **Automated Compliance:** Regulatory compliance automation
- **Data Encryption:** End-to-end encryption

#### 12.4.6 Cloud-Native & Microservices Architecture
**Microservices Design:**
```yaml
# docker-compose.microservices.yml
version: '3.8'
services:
  # API Gateway
  api-gateway:
    image: kong:latest
    ports:
      - "8000:8000"
    environment:
      - KONG_DATABASE=postgres
      - KONG_PG_HOST=postgres
    depends_on:
      - postgres

  # User Service
  user-service:
    build: ./services/user-service
    environment:
      - DATABASE_URL=postgresql://user:pass@postgres:5432/users
      - REDIS_URL=redis://redis:6379
    depends_on:
      - postgres
      - redis

  # Order Service
  order-service:
    build: ./services/order-service
    environment:
      - DATABASE_URL=postgresql://user:pass@postgres:5432/orders
      - KAFKA_BOOTSTRAP_SERVERS=kafka:9092
    depends_on:
      - postgres
      - kafka

  # Inventory Service
  inventory-service:
    build: ./services/inventory-service
    environment:
      - DATABASE_URL=postgresql://user:pass@postgres:5432/inventory
      - ELASTICSEARCH_URL=http://elasticsearch:9200
    depends_on:
      - postgres
      - elasticsearch

  # Analytics Service
  analytics-service:
    build: ./services/analytics-service
    environment:
      - MONGODB_URL=mongodb://mongo:27017/analytics
      - SPARK_MASTER_URL=spark://spark:7077
    depends_on:
      - mongo
      - spark

  # Message Queue
  kafka:
    image: confluentinc/cp-kafka:latest
    environment:
      KAFKA_ZOOKEEPER_CONNECT: zookeeper:2181
      KAFKA_ADVERTISED_LISTENERS: PLAINTEXT://kafka:9092
    depends_on:
      - zookeeper

  # Search Engine
  elasticsearch:
    image: docker.elastic.co/elasticsearch/elasticsearch:8.5.0
    environment:
      - discovery.type=single-node
      - "ES_JAVA_OPTS=-Xms512m -Xmx512m"

  # Big Data Processing
  spark:
    image: bitnami/spark:latest
    environment:
      - SPARK_MODE=master
```

**Benefits:**
- **Scalability:** Independent service scaling
- **Resilience:** Fault isolation
- **Flexibility:** Technology diversity
- **Performance:** Optimized per service
- **Maintenance:** Independent deployment

#### 12.4.7 Advanced Mobile Features
**Next-Gen Mobile Capabilities:**
```typescript
// Advanced Mobile Features
class AdvancedMobileService {
    // Augmented Reality for Product Visualization
    async enableARProductView(productId: string) {
        const product = await this.getProductDetails(productId);
        const arView = await this.initializeAR();
        
        return arView.displayProduct({
            model: product.model3D,
            dimensions: product.dimensions,
            features: product.features
        });
    }
    
    // Voice Commands for Field Operations
    async enableVoiceCommands() {
        const voiceService = new VoiceRecognitionService();
        
        voiceService.addCommand('create order', async (params) => {
            await this.createOrder(params);
        });
        
        voiceService.addCommand('check inventory', async (params) => {
            return await this.getInventory(params.product);
        });
        
        voiceService.addCommand('navigate to customer', async (params) => {
            await this.navigateToCustomer(params.customerId);
        });
        
        return voiceService.startListening();
    }
    
    // Offline AI Processing
    async enableOfflineAI() {
        const model = await this.loadMLModel('product_classifier');
        
        return {
            classifyProduct: (image) => model.classify(image),
            predictDemand: (data) => model.predict(data),
            detectQuality: (image) => model.analyze(image)
        };
    }
}
```

**Advanced Features:**
- **Augmented Reality:** Product visualization
- **Voice Commands:** Hands-free operation
- **Offline AI:** Local ML processing
- **Advanced Camera:** AI-powered scanning
- **Biometric Authentication:** Enhanced security

#### 12.4.8 Sustainability & Green Technology
**Sustainability Features:**
```php
// Sustainability Service
class SustainabilityService
{
    public function calculateCarbonFootprint($transaction)
    {
        $carbonFactors = [
            'transportation' => 0.12, // kg CO2 per km
            'packaging' => 0.05, // kg CO2 per kg
            'storage' => 0.02, // kg CO2 per day
            'processing' => 0.08 // kg CO2 per unit
        ];
        
        $footprint = 0;
        
        // Transportation footprint
        $footprint += $transaction->distance * $carbonFactors['transportation'];
        
        // Packaging footprint
        $footprint += $transaction->weight * $carbonFactors['packaging'];
        
        // Storage footprint
        $footprint += $transaction->storage_days * $carbonFactors['storage'];
        
        // Processing footprint
        $footprint += $transaction->quantity * $carbonFactors['processing'];
        
        return [
            'total_footprint' => $footprint,
            'breakdown' => [
                'transportation' => $transaction->distance * $carbonFactors['transportation'],
                'packaging' => $transaction->weight * $carbonFactors['packaging'],
                'storage' => $transaction->storage_days * $carbonFactors['storage'],
                'processing' => $transaction->quantity * $carbonFactors['processing']
            ],
            'offset_cost' => $footprint * 0.02, // $0.02 per kg CO2
            'sustainability_score' => $this->calculateSustainabilityScore($footprint)
        ];
    }
    
    public function optimizeGreenRoutes($deliveries)
    {
        // Optimize routes for minimal carbon footprint
        $optimizedRoutes = $this->routeOptimizer->optimizeForCarbon($deliveries);
        
        return [
            'routes' => $optimizedRoutes,
            'carbon_savings' => $this->calculateCarbonSavings($optimizedRoutes),
            'fuel_efficiency' => $this->calculateFuelEfficiency($optimizedRoutes),
            'green_score' => $this->calculateGreenScore($optimizedRoutes)
        ];
    }
}
```

**Green Features:**
- **Carbon Footprint Tracking:** Environmental impact measurement
- **Green Route Optimization:** Eco-friendly delivery routes
- **Sustainability Reporting:** ESG compliance
- **Energy Monitoring:** Resource usage tracking
- **Waste Reduction:** Optimization algorithms

#### 12.4.9 Global Expansion & Multi-Currency
**Global Readiness:**
```php
// Globalization Service
class GlobalizationService
{
    public function enableMultiCurrency()
    {
        return [
            'supported_currencies' => ['IDR', 'USD', 'EUR', 'SGD', 'MYR'],
            'real_time_exchange_rates' => $this->getExchangeRates(),
            'multi_currency_pricing' => $this->enableMultiCurrencyPricing(),
            'currency_conversion' => $this->enableCurrencyConversion(),
            'local_payment_methods' => $this->getLocalPaymentMethods()
        ];
    }
    
    public function enableMultiLanguage()
    {
        return [
            'supported_languages' => ['id', 'en', 'zh', 'ms', 'th'],
            'auto_translation' => $this->enableAutoTranslation(),
            'localization' => $this->enableLocalization(),
            'cultural_adaptation' => $this->enableCulturalAdaptation(),
            'regional_compliance' => $this->enableRegionalCompliance()
        ];
    }
    
    public function enableGlobalCompliance()
    {
        return [
            'tax_compliance' => $this->enableTaxCompliance(),
            'data_residency' => $this->enableDataResidency(),
            'local_regulations' => $this->enableLocalRegulations(),
            'international_standards' => $this->enableInternationalStandards(),
            'cross_border_payments' => $this->enableCrossBorderPayments()
        ];
    }
}
```

**Global Features:**
- **Multi-Currency:** Support untuk multiple currencies
- **Multi-Language:** International language support
- **Tax Compliance:** Multi-country tax handling
- **Data Residency:** Local data storage
- **Regional Adaptation:** Cultural customization

#### 12.4.10 Innovation Roadmap
**5-Year Technology Roadmap:**
```
YEAR 1-2: FOUNDATION
├── Core System Implementation
├── Basic Analytics
├── Mobile Apps
├── Cloud Infrastructure
└── Basic Automation

YEAR 2-3: INTELLIGENCE
├── AI Integration
├── Predictive Analytics
├── Advanced Automation
├── IoT Integration
└── Blockchain Basics

YEAR 3-4: OPTIMIZATION
├── Full AI Implementation
├── Advanced Analytics
├── Microservices Architecture
├── Global Expansion
└── Sustainability Features

YEAR 4-5: INNOVATION
├── Quantum Computing (Experimental)
├── Advanced AI/ML
├── Full Automation
├── Global Dominance
└── Industry Leadership
```

**Investment Priorities:**
- **Year 1:** Rp 300 juta (Foundation)
- **Year 2:** Rp 500 juta (Intelligence)
- **Year 3:** Rp 800 juta (Optimization)
- **Year 4:** Rp 1.2 miliar (Innovation)
- **Year 5:** Rp 2 miliar (Leadership)

### 12.4.11 Final Strategic Recommendations

#### **🏆 TOP 5 PRIORITY RECOMMENDATIONS:**

**1. Start with FREE Stack (Immediate)**
- **Investment:** Rp 300 juta
- **Timeline:** 6 bulan
- **ROI:** 300% dalam 2 tahun
- **Risk:** Minimal

**2. Implement Modular Architecture (Month 7)**
- **Flexibility:** Add/remove modules anytime
- **User Control:** Feature-based access control
- **Scalability:** Independent module scaling
- **Cost Control:** Pay only for needed features

**3. Add AI & Analytics (Year 2)**
- **Predictive Analytics:** Demand forecasting
- **Automation:** Reduce manual work
- **Insights:** Business intelligence
- **Competitive Edge:** AI-powered decisions

**4. Global Expansion (Year 3)**
- **Multi-Currency:** International payments
- **Multi-Language:** Regional adaptation
- **Compliance:** Global regulations
- **Market Access:** New customer segments

**5. Innovation Leadership (Year 4-5)**
- **Quantum Computing:** Future technology
- **Full Automation:** End-to-end automation
- **Industry Standards:** Set new benchmarks
- **Market Dominance:** Industry leadership

#### **🎯 SUCCESS METRICS:**
- **User Adoption:** 80%+ dalam 2 tahun
- **Revenue Growth:** 200%+ dalam 3 tahun
- **Market Share:** Top 3 dalam 5 tahun
- **Technology Leadership:** Industry recognition
- **Sustainability:** Carbon neutral operations

#### **💡 KEY SUCCESS FACTORS:**
- **Start Small:** Begin dengan MVP
- **Iterate Fast:** Continuous improvement
- **User-Centric:** Focus on user needs
- **Technology-Forward:** Embrace innovation
- **Sustainable:** Long-term vision

### 12.5 Master Data Management (Static Reference Data)

#### 12.5.1 Master Data Strategy
**Static vs Dynamic Data Classification:**
```
MASTER DATA CLASSIFICATION:
├── Static Master Data (Fixed)
│   ├── Geographic Data
│   ├── Product Categories
│   ├── Business Codes
│   ├── Regulatory References
│   └── System Constants
├── Semi-Static Data (Rarely Changes)
│   ├── Supplier Information
│   ├── Customer Types
│   ├── Price Lists
│   ├── Tax Rates
│   └── Exchange Rates
├── Dynamic Data (Frequently Changes)
│   ├── Transactions
│   ├── Inventory Levels
│   ├── User Activities
│   ├── System Logs
│   └── Temporary Data
└── Configuration Data
    ├── System Settings
    ├── User Preferences
    ├── Feature Flags
    └── Business Rules
```

#### 12.5.2 Static Master Tables Design
**Geographic Master Data:**
```sql
-- Provinces (Static - BPS Reference)
CREATE TABLE master_provinces (
    id INT PRIMARY KEY,
    code VARCHAR(2) UNIQUE NOT NULL,  -- BPS Code
    name VARCHAR(100) NOT NULL,
    name_en VARCHAR(100),
    island_group VARCHAR(50),
    capital VARCHAR(100),
    area_km2 DECIMAL(10,2),
    population INT,
    density_per_km2 DECIMAL(8,2),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_name (name),
    INDEX idx_island (island_group)
);

-- Regencies/Cities (Static - BPS Reference)
CREATE TABLE master_regencies (
    id INT PRIMARY KEY,
    province_id INT NOT NULL,
    code VARCHAR(4) UNIQUE NOT NULL,  -- BPS Code
    name VARCHAR(100) NOT NULL,
    name_en VARCHAR(100),
    type ENUM('Kabupaten', 'Kota') NOT NULL,
    capital VARCHAR(100),
    area_km2 DECIMAL(10,2),
    population INT,
    density_per_km2 DECIMAL(8,2),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (province_id) REFERENCES master_provinces(id),
    INDEX idx_province (province_id),
    INDEX idx_code (code),
    INDEX idx_name (name),
    INDEX idx_type (type)
);

-- Districts (Static - BPS Reference)
CREATE TABLE master_districts (
    id INT PRIMARY KEY,
    regency_id INT NOT NULL,
    code VARCHAR(6) UNIQUE NOT NULL,  -- BPS Code
    name VARCHAR(100) NOT NULL,
    name_en VARCHAR(100),
    area_km2 DECIMAL(10,2),
    population INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (regency_id) REFERENCES master_regencies(id),
    INDEX idx_regency (regency_id),
    INDEX idx_code (code),
    INDEX idx_name (name)
);

-- Villages (Static - BPS Reference)
CREATE TABLE master_villages (
    id INT PRIMARY KEY,
    district_id INT NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,  -- BPS Code
    name VARCHAR(100) NOT NULL,
    name_en VARCHAR(100),
    type ENUM('Desa', 'Kelurahan') NOT NULL,
    postal_code VARCHAR(5),
    area_km2 DECIMAL(10,2),
    population INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (district_id) REFERENCES master_districts(id),
    INDEX idx_district (district_id),
    INDEX idx_code (code),
    INDEX idx_name (name),
    INDEX idx_postal (postal_code)
);
```

**Product & Business Master Data:**
```sql
-- Product Categories (Static - BGN/MBG Reference)
CREATE TABLE master_product_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    name_en VARCHAR(100),
    description TEXT,
    parent_id INT NULL,
    level INT NOT NULL DEFAULT 1,
    bgn_code VARCHAR(20),  -- BGN Classification
    mbg_code VARCHAR(20),  -- MBG Classification
    hs_code VARCHAR(10),   -- Harmonized System
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES master_product_categories(id),
    INDEX idx_code (code),
    INDEX idx_parent (parent_id),
    INDEX idx_bgn (bgn_code),
    INDEX idx_mbg (mbg_code),
    INDEX idx_hs (hs_code)
);

-- Units of Measurement (Static - International Standard)
CREATE TABLE master_units (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(10) UNIQUE NOT NULL,
    name VARCHAR(50) NOT NULL,
    name_en VARCHAR(50),
    type ENUM('Weight', 'Volume', 'Length', 'Area', 'Count', 'Time') NOT NULL,
    base_unit VARCHAR(10),  -- Reference to base unit
    conversion_factor DECIMAL(10,6) DEFAULT 1.0,
    iso_code VARCHAR(5),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_type (type),
    INDEX idx_base (base_unit)
);

-- Business Categories (Static - KBLI Reference)
CREATE TABLE master_business_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kbli_code VARCHAR(5) UNIQUE NOT NULL,  -- KBLI 2020
    name VARCHAR(200) NOT NULL,
    name_en VARCHAR(200),
    description TEXT,
    parent_id INT NULL,
    level INT NOT NULL DEFAULT 1,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES master_business_categories(id),
    INDEX idx_kbli (kbli_code),
    INDEX idx_parent (parent_id),
    INDEX idx_level (level)
);

-- Tax Codes (Static - DJP Reference)
CREATE TABLE master_tax_codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(10) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    name_en VARCHAR(100),
    type ENUM('PPN', 'PPH_21', 'PPH_23', 'PPH_25', 'PPH_29', 'OTHER') NOT NULL,
    rate DECIMAL(5,2) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    effective_date DATE NOT NULL,
    expired_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_type (type),
    INDEX idx_effective (effective_date, expired_date)
);
```

**Regulatory & Compliance Master Data:**
```sql
-- BGN Compliance Codes (Static - BGN Reference)
CREATE TABLE master_bgn_compliance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(200) NOT NULL,
    name_en VARCHAR(200),
    category VARCHAR(50) NOT NULL,
    description TEXT,
    requirement_level ENUM('Mandatory', 'Recommended', 'Optional') NOT NULL,
    checklist_items JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_category (category),
    INDEX idx_level (requirement_level)
);

-- MBG Nutritional Standards (Static - BGN Reference)
CREATE TABLE master_mbg_standards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(200) NOT NULL,
    name_en VARCHAR(200),
    category VARCHAR(50) NOT NULL,
    age_group VARCHAR(50),
    nutritional_requirements JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_category (category),
    INDEX idx_age (age_group)
);

-- Document Types (Static - Business Reference)
CREATE TABLE master_document_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    name_en VARCHAR(100),
    category ENUM('Legal', 'Financial', 'Operational', 'Compliance', 'Other') NOT NULL,
    description TEXT,
    required_fields JSON,
    file_types JSON,  -- Allowed file extensions
    max_size_mb INT DEFAULT 10,
    expiry_required BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_category (category)
);
```

#### 12.5.3 Master Data Seeding Strategy
**Database Seeder Implementation:**
```php
// database/seeders/MasterDataSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MasterDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Seeding master data...');
        
        // Seed geographic data
        $this->seedGeographicData();
        
        // Seed product categories
        $this->seedProductCategories();
        
        // Seed units of measurement
        $this->seedUnits();
        
        // Seed business categories
        $this->seedBusinessCategories();
        
        // Seed tax codes
        $this->seedTaxCodes();
        
        // Seed BGN compliance
        $this->seedBGNCompliance();
        
        // Seed MBG standards
        $this->seedMBGStandards();
        
        // Seed document types
        $this->seedDocumentTypes();
        
        $this->command->info('Master data seeding completed!');
    }
    
    private function seedGeographicData()
    {
        // Load from JSON files or CSV files
        $provinces = json_decode(File::get(database_path('data/provinces.json')), true);
        
        foreach ($provinces as $province) {
            DB::table('master_provinces')->insert([
                'id' => $province['id'],
                'code' => $province['code'],
                'name' => $province['name'],
                'name_en' => $province['name_en'] ?? null,
                'island_group' => $province['island_group'],
                'capital' => $province['capital'],
                'area_km2' => $province['area_km2'],
                'population' => $province['population'],
                'density_per_km2' => $province['density_per_km2'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // Similar for regencies, districts, villages
    }
    
    private function seedProductCategories()
    {
        $categories = [
            [
                'code' => 'FOOD_GRAIN',
                'name' => 'Bahan Makanan Pokok',
                'name_en' => 'Staple Food',
                'bgn_code' => 'BMP001',
                'mbg_code' => 'MBG001',
                'hs_code' => '1001',
                'description' => 'Bahan makanan pokok sesuai BGN'
            ],
            [
                'code' => 'FOOD_SUPPLEMENT',
                'name' => 'Makanan Tambahan',
                'name_en' => 'Food Supplement',
                'bgn_code' => 'BMP002',
                'mbg_code' => 'MBG002',
                'hs_code' => '2106',
                'description' => 'Makanan tambahan bergizi'
            ],
            // Add more categories...
        ];
        
        foreach ($categories as $category) {
            DB::table('master_product_categories')->insert(array_merge([
                'created_at' => now(),
                'updated_at' => now()
            ], $category));
        }
    }
    
    private function seedUnits()
    {
        $units = [
            ['code' => 'KG', 'name' => 'Kilogram', 'type' => 'Weight', 'conversion_factor' => 1.0, 'iso_code' => 'KGM'],
            ['code' => 'GR', 'name' => 'Gram', 'type' => 'Weight', 'base_unit' => 'KG', 'conversion_factor' => 0.001, 'iso_code' => 'GRM'],
            ['code' => 'L', 'name' => 'Liter', 'type' => 'Volume', 'conversion_factor' => 1.0, 'iso_code' => 'LTR'],
            ['code' => 'ML', 'name' => 'Milliliter', 'type' => 'Volume', 'base_unit' => 'L', 'conversion_factor' => 0.001, 'iso_code' => 'MLT'],
            ['code' => 'PCS', 'name' => 'Pieces', 'type' => 'Count', 'conversion_factor' => 1.0],
            ['code' => 'M', 'name' => 'Meter', 'type' => 'Length', 'conversion_factor' => 1.0, 'iso_code' => 'MTR'],
            ['code' => 'CM', 'name' => 'Centimeter', 'type' => 'Length', 'base_unit' => 'M', 'conversion_factor' => 0.01, 'iso_code' => 'CMT'],
            // Add more units...
        ];
        
        foreach ($units as $unit) {
            DB::table('master_units')->insert(array_merge([
                'created_at' => now(),
                'updated_at' => now()
            ], $unit));
        }
    }
}
```

#### 12.5.4 Master Data Management Service
**Master Data Service Class:**
```php
// app/Services/MasterDataService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MasterDataService
{
    private static $cacheDuration = 86400; // 24 hours
    
    // Geographic Data Methods
    public static function getProvinces()
    {
        return Cache::remember('master_provinces', self::$cacheDuration, function () {
            return DB::table('master_provinces')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }
    
    public static function getRegenciesByProvince($provinceId)
    {
        $cacheKey = "master_regencies_province_{$provinceId}";
        
        return Cache::remember($cacheKey, self::$cacheDuration, function () use ($provinceId) {
            return DB::table('master_regencies')
                ->where('province_id', $provinceId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }
    
    public static function getFullAddress($villageId)
    {
        $cacheKey = "master_full_address_{$villageId}";
        
        return Cache::remember($cacheKey, self::$cacheDuration, function () use ($villageId) {
            return DB::table('master_villages as v')
                ->join('master_districts as d', 'v.district_id', '=', 'd.id')
                ->join('master_regencies as r', 'd.regency_id', '=', 'r.id')
                ->join('master_provinces as p', 'r.province_id', '=', 'p.id')
                ->where('v.id', $villageId)
                ->select(
                    'v.name as village',
                    'v.type as village_type',
                    'd.name as district',
                    'r.name as regency',
                    'r.type as regency_type',
                    'p.name as province',
                    'v.postal_code'
                )
                ->first();
        });
    }
    
    // Product Data Methods
    public static function getProductCategories($parentId = null)
    {
        $cacheKey = "master_product_categories_" . ($parentId ?? 'root');
        
        return Cache::remember($cacheKey, self::$cacheDuration, function () use ($parentId) {
            $query = DB::table('master_product_categories')
                ->where('is_active', true);
            
            if ($parentId) {
                $query->where('parent_id', $parentId);
            } else {
                $query->whereNull('parent_id');
            }
            
            return $query->orderBy('sort_order')->orderBy('name')->get();
        });
    }
    
    public static function getUnitsByType($type)
    {
        $cacheKey = "master_units_type_{$type}";
        
        return Cache::remember($cacheKey, self::$cacheDuration, function () use ($type) {
            return DB::table('master_units')
                ->where('type', $type)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }
    
    // Business Data Methods
    public static function getBusinessCategories($parentId = null)
    {
        $cacheKey = "master_business_categories_" . ($parentId ?? 'root');
        
        return Cache::remember($cacheKey, self::$cacheDuration, function () use ($parentId) {
            $query = DB::table('master_business_categories')
                ->where('is_active', true);
            
            if ($parentId) {
                $query->where('parent_id', $parentId);
            } else {
                $query->whereNull('parent_id');
            }
            
            return $query->orderBy('level')->orderBy('name')->get();
        });
    }
    
    // Tax Data Methods
    public static function getTaxCodes($type = null)
    {
        $cacheKey = "master_tax_codes_" . ($type ?? 'all');
        
        return Cache::remember($cacheKey, self::$cacheDuration, function () use ($type) {
            $query = DB::table('master_tax_codes')
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expired_date')
                      ->orWhere('expired_date', '>=', now());
                });
            
            if ($type) {
                $query->where('type', $type);
            }
            
            return $query->orderBy('code')->get();
        });
    }
    
    // Compliance Data Methods
    public static function getBGNCompliance($category = null)
    {
        $cacheKey = "master_bgn_compliance_" . ($category ?? 'all');
        
        return Cache::remember($cacheKey, self::$cacheDuration, function () use ($category) {
            $query = DB::table('master_bgn_compliance')
                ->where('is_active', true);
            
            if ($category) {
                $query->where('category', $category);
            }
            
            return $query->orderBy('category')->orderBy('name')->get();
        });
    }
    
    // Cache Management
    public static function clearCache($type = null)
    {
        if ($type) {
            Cache::forget("master_{$type}");
        } else {
            // Clear all master data cache
            $cacheKeys = [
                'master_provinces',
                'master_product_categories',
                'master_units',
                'master_business_categories',
                'master_tax_codes',
                'master_bgn_compliance',
                'master_mbg_standards',
                'master_document_types'
            ];
            
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }
        }
    }
    
    // Validation Methods
    public static function validateAddress($villageId, $districtId, $regencyId, $provinceId)
    {
        return DB::table('master_villages as v')
            ->join('master_districts as d', 'v.district_id', '=', 'd.id')
            ->join('master_regencies as r', 'd.regency_id', '=', 'r.id')
            ->join('master_provinces as p', 'r.province_id', '=', 'p.id')
            ->where('v.id', $villageId)
            ->where('d.id', $districtId)
            ->where('r.id', $regencyId)
            ->where('p.id', $provinceId)
            ->exists();
    }
    
    public static function convertUnit($fromUnit, $toUnit, $quantity)
    {
        $fromUnitData = DB::table('master_units')
            ->where('code', $fromUnit)
            ->where('is_active', true)
            ->first();
            
        $toUnitData = DB::table('master_units')
            ->where('code', $toUnit)
            ->where('is_active', true)
            ->first();
        
        if (!$fromUnitData || !$toUnitData) {
            throw new \Exception("Invalid unit conversion");
        }
        
        // Convert to base unit first
        $baseQuantity = $quantity * $fromUnitData->conversion_factor;
        
        // Convert from base unit to target unit
        return $baseQuantity / $toUnitData->conversion_factor;
    }
}
```

#### 12.5.5 Master Data API Endpoints
**API Controller for Master Data:**
```php
// app/Http/Controllers/API/MasterDataController.php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\MasterDataService;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    // Geographic endpoints
    public function getProvinces()
    {
        return response()->json([
            'success' => true,
            'data' => MasterDataService::getProvinces()
        ]);
    }
    
    public function getRegencies($provinceId)
    {
        return response()->json([
            'success' => true,
            'data' => MasterDataService::getRegenciesByProvince($provinceId)
        ]);
    }
    
    public function getFullAddress($villageId)
    {
        $address = MasterDataService::getFullAddress($villageId);
        
        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $address
        ]);
    }
    
    // Product endpoints
    public function getProductCategories(Request $request)
    {
        $parentId = $request->get('parent_id');
        
        return response()->json([
            'success' => true,
            'data' => MasterDataService::getProductCategories($parentId)
        ]);
    }
    
    public function getUnits(Request $request)
    {
        $type = $request->get('type');
        
        return response()->json([
            'success' => true,
            'data' => MasterDataService::getUnitsByType($type)
        ]);
    }
    
    // Business endpoints
    public function getBusinessCategories(Request $request)
    {
        $parentId = $request->get('parent_id');
        
        return response()->json([
            'success' => true,
            'data' => MasterDataService::getBusinessCategories($parentId)
        ]);
    }
    
    // Tax endpoints
    public function getTaxCodes(Request $request)
    {
        $type = $request->get('type');
        
        return response()->json([
            'success' => true,
            'data' => MasterDataService::getTaxCodes($type)
        ]);
    }
    
    // Compliance endpoints
    public function getBGNCompliance(Request $request)
    {
        $category = $request->get('category');
        
        return response()->json([
            'success' => true,
            'data' => MasterDataService::getBGNCompliance($category)
        ]);
    }
    
    // Utility endpoints
    public function convertUnit(Request $request)
    {
        $validated = $request->validate([
            'from_unit' => 'required|string',
            'to_unit' => 'required|string',
            'quantity' => 'required|numeric|min:0'
        ]);
        
        try {
            $result = MasterDataService::convertUnit(
                $validated['from_unit'],
                $validated['to_unit'],
                $validated['quantity']
            );
            
            return response()->json([
                'success' => true,
                'data' => [
                    'from_quantity' => $validated['quantity'],
                    'from_unit' => $validated['from_unit'],
                    'to_quantity' => $result,
                    'to_unit' => $validated['to_unit']
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    public function validateAddress(Request $request)
    {
        $validated = $request->validate([
            'village_id' => 'required|integer',
            'district_id' => 'required|integer',
            'regency_id' => 'required|integer',
            'province_id' => 'required|integer'
        ]);
        
        $isValid = MasterDataService::validateAddress(
            $validated['village_id'],
            $validated['district_id'],
            $validated['regency_id'],
            $validated['province_id']
        );
        
        return response()->json([
            'success' => true,
            'data' => [
                'is_valid' => $isValid,
                'message' => $isValid ? 'Address is valid' : 'Address is invalid'
            ]
        ]);
    }
}
```

#### 12.5.6 Master Data Maintenance
**Master Data Update Commands:**
```php
// app/Console/Commands/UpdateMasterData.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MasterDataService;
use Illuminate\Support\Facades\Http;

class UpdateMasterData extends Command
{
    protected $signature = 'master:update {type}';
    protected $description = 'Update master data from external sources';
    
    public function handle()
    {
        $type = $this->argument('type');
        
        switch ($type) {
            case 'geographic':
                $this->updateGeographicData();
                break;
            case 'tax':
                $this->updateTaxData();
                break;
            case 'bgn':
                $this->updateBGNData();
                break;
            default:
                $this->error("Unknown type: {$type}");
                return 1;
        }
        
        MasterDataService::clearCache();
        $this->info("Master data {$type} updated successfully!");
        
        return 0;
    }
    
    private function updateGeographicData()
    {
        // Fetch from BPS API
        $response = Http::get('https://api.bps.go.id/v1/geographic/provinces');
        
        if ($response->successful()) {
            $provinces = $response->json('data');
            
            foreach ($provinces as $province) {
                DB::table('master_provinces')
                    ->updateOrInsert(
                        ['code' => $province['code']],
                        [
                            'name' => $province['name'],
                            'updated_at' => now()
                        ]
                    );
            }
            
            $this->info("Updated " . count($provinces) . " provinces");
        }
    }
    
    private function updateTaxData()
    {
        // Fetch from DJP API
        $response = Http::get('https://api.djp.go.id/v1/tax-codes');
        
        if ($response->successful()) {
            $taxCodes = $response->json('data');
            
            foreach ($taxCodes as $taxCode) {
                DB::table('master_tax_codes')
                    ->updateOrInsert(
                        ['code' => $taxCode['code']],
                        [
                            'name' => $taxCode['name'],
                            'type' => $taxCode['type'],
                            'rate' => $taxCode['rate'],
                            'updated_at' => now()
                        ]
                    );
            }
            
            $this->info("Updated " . count($taxCodes) . " tax codes");
        }
    }
    
    private function updateBGNData()
    {
        // Fetch from BGN API
        $response = Http::get('https://api.bgn.go.id/v1/compliance-codes');
        
        if ($response->successful()) {
            $complianceCodes = $response->json('data');
            
            foreach ($complianceCodes as $code) {
                DB::table('master_bgn_compliance')
                    ->updateOrInsert(
                        ['code' => $code['code']],
                        [
                            'name' => $code['name'],
                            'category' => $code['category'],
                            'requirement_level' => $code['requirement_level'],
                            'updated_at' => now()
                        ]
                    );
            }
            
            $this->info("Updated " . count($complianceCodes) . " BGN compliance codes");
        }
    }
}
```

### 12.5.7 Benefits of Master Data Management

#### **Data Consistency:**
- **Single Source of Truth:** Centralized master data
- **Standardization:** Consistent data formats
- **Validation:** Built-in data validation
- **Integrity:** Referential integrity enforcement

#### **Performance Benefits:**
- **Caching:** Fast data retrieval
- **Indexing:** Optimized queries
- **Reduced Duplication:** Eliminate data redundancy
- **Efficient Joins:** Optimized table relationships

#### **Maintenance Benefits:**
- **Centralized Updates:** Single point of maintenance
- **Version Control:** Track data changes
- **Automated Updates:** Sync with external sources
- **Easy Backup:** Focused backup strategy

#### **Business Benefits:**
- **Regulatory Compliance:** Always up-to-date with regulations
- **Data Quality:** High-quality reference data
- **Decision Making:** Reliable data for decisions
- **Reporting:** Consistent reporting standards

### 12.6 Self-Healing System & Auto-Recovery

#### 12.6.1 Self-Healing Architecture
**Auto-Recovery System Design:**
```
SELF-HEALING ARCHITECTURE:
├── Health Monitoring Layer
│   ├── Application Health Checks
│   ├── Database Integrity Checks
│   ├── File System Monitoring
│   └── Network Connectivity Checks
├── Detection & Diagnosis Layer
│   ├── Anomaly Detection
│   ├── Error Pattern Recognition
│   ├── Performance Degradation Detection
│   └── Security Breach Detection
├── Auto-Recovery Layer
│   ├── Component Restart
│   ├── Data Repair
│   ├── Configuration Restore
│   └── Service Recovery
├── Backup & Restore Layer
│   ├── Real-time Backups
│   ├── Point-in-Time Recovery
│   ├── Incremental Backups
│   └── Disaster Recovery
└── Notification & Alerting Layer
    ├── System Alerts
    ├── Admin Notifications
    ├── User Notifications
    └── Escalation Protocols
```

#### 12.6.2 Health Monitoring System
**Comprehensive Health Checks:**
```php
// app/Services/HealthMonitoringService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HealthMonitoringService
{
    private static $healthChecks = [
        'database' => 'checkDatabaseHealth',
        'cache' => 'checkCacheHealth',
        'storage' => 'checkStorageHealth',
        'filesystem' => 'checkFilesystemHealth',
        'memory' => 'checkMemoryUsage',
        'disk_space' => 'checkDiskSpace',
        'services' => 'checkExternalServices',
        'integrity' => 'checkDataIntegrity'
    ];
    
    public static function runFullHealthCheck()
    {
        $results = [];
        $overallHealth = 'healthy';
        
        foreach (self::$healthChecks as $component => $method) {
            try {
                $results[$component] = self::$method();
                
                if ($results[$component]['status'] !== 'healthy') {
                    $overallHealth = 'degraded';
                }
            } catch (\Exception $e) {
                $results[$component] = [
                    'status' => 'critical',
                    'message' => $e->getMessage(),
                    'timestamp' => now()
                ];
                $overallHealth = 'critical';
            }
        }
        
        // Store health status
        Cache::put('system_health', [
            'overall_status' => $overallHealth,
            'components' => $results,
            'last_check' => now()
        ], 300); // 5 minutes
        
        // Trigger auto-recovery if needed
        if ($overallHealth !== 'healthy') {
            self::triggerAutoRecovery($results);
        }
        
        return [
            'overall_status' => $overallHealth,
            'components' => $results,
            'last_check' => now()
        ];
    }
    
    public static function checkDatabaseHealth()
    {
        try {
            // Test database connection
            $connection = DB::connection();
            $connection->getPdo();
            
            // Test basic query
            $result = DB::select('SELECT 1 as test');
            
            // Check table integrity
            $tables = DB::select('SHOW TABLES');
            $tableCount = count($tables);
            
            // Check for corruption
            $corruptedTables = [];
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                $checkResult = DB::select("CHECK TABLE {$tableName}");
                if ($checkResult[0]->Msg_type !== 'status') {
                    $corruptedTables[] = $tableName;
                }
            }
            
            return [
                'status' => empty($corruptedTables) ? 'healthy' : 'degraded',
                'connection' => 'connected',
                'table_count' => $tableCount,
                'corrupted_tables' => $corruptedTables,
                'timestamp' => now()
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'critical',
                'message' => $e->getMessage(),
                'timestamp' => now()
            ];
        }
    }
    
    public static function checkDataIntegrity()
    {
        $issues = [];
        
        // Check for orphaned records
        $orphanedRecords = DB::select("
            SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE REFERENCED_TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME NOT IN ('migrations', 'failed_jobs')
        ");
        
        foreach ($orphanedRecords as $record) {
            $table = $record->TABLE_NAME;
            $column = $record->COLUMN_NAME;
            $refTable = $record->REFERENCED_TABLE_NAME;
            
            $orphanCount = DB::select("
                SELECT COUNT(*) as count
                FROM {$table} t
                LEFT JOIN {$refTable} r ON t.{$column} = r.id
                WHERE t.{$column} IS NOT NULL AND r.id IS NULL
            ");
            
            if ($orphanCount[0]->count > 0) {
                $issues[] = [
                    'type' => 'orphaned_records',
                    'table' => $table,
                    'column' => $column,
                    'count' => $orphanCount[0]->count
                ];
            }
        }
        
        // Check for duplicate primary keys
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            
            try {
                $primaryKey = DB::select("SHOW KEYS FROM {$tableName} WHERE Key_name = 'PRIMARY'");
                if (!empty($primaryKey)) {
                    $primaryKeyColumn = $primaryKey[0]->Column_name;
                    
                    $duplicates = DB::select("
                        SELECT {$primaryKeyColumn}, COUNT(*) as count
                        FROM {$tableName}
                        GROUP BY {$primaryKeyColumn}
                        HAVING count > 1
                    ");
                    
                    if (!empty($duplicates)) {
                        $issues[] = [
                            'type' => 'duplicate_primary_keys',
                            'table' => $tableName,
                            'column' => $primaryKeyColumn,
                            'duplicates' => count($duplicates)
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Skip if table has issues
                continue;
            }
        }
        
        return [
            'status' => empty($issues) ? 'healthy' : 'degraded',
            'issues' => $issues,
            'timestamp' => now()
        ];
    }
    
    public static function checkFilesystemHealth()
    {
        $issues = [];
        
        // Check critical directories
        $criticalDirs = [
            storage_path('app/public'),
            storage_path('logs'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views')
        ];
        
        foreach ($criticalDirs as $dir) {
            if (!is_dir($dir)) {
                $issues[] = [
                    'type' => 'missing_directory',
                    'path' => $dir
                ];
            } elseif (!is_writable($dir)) {
                $issues[] = [
                    'type' => 'not_writable',
                    'path' => $dir
                ];
            }
        }
        
        // Check critical files
        $criticalFiles = [
            base_path('.env'),
            base_path('config/app.php'),
            base_path('config/database.php')
        ];
        
        foreach ($criticalFiles as $file) {
            if (!file_exists($file)) {
                $issues[] = [
                    'type' => 'missing_file',
                    'path' => $file
                ];
            }
        }
        
        return [
            'status' => empty($issues) ? 'healthy' : 'degraded',
            'issues' => $issues,
            'timestamp' => now()
        ];
    }
    
    public static function checkMemoryUsage()
    {
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');
        
        // Convert memory limit to bytes
        if (strpos($memoryLimit, 'G') !== false) {
            $memoryLimit = (int)$memoryLimit * 1024 * 1024 * 1024;
        } elseif (strpos($memoryLimit, 'M') !== false) {
            $memoryLimit = (int)$memoryLimit * 1024 * 1024;
        } elseif (strpos($memoryLimit, 'K') !== false) {
            $memoryLimit = (int)$memoryLimit * 1024;
        } else {
            $memoryLimit = (int)$memoryLimit;
        }
        
        $usagePercentage = ($memoryUsage / $memoryLimit) * 100;
        
        return [
            'status' => $usagePercentage < 80 ? 'healthy' : ($usagePercentage < 90 ? 'degraded' : 'critical'),
            'usage_bytes' => $memoryUsage,
            'limit_bytes' => $memoryLimit,
            'usage_percentage' => round($usagePercentage, 2),
            'timestamp' => now()
        ];
    }
    
    public static function checkDiskSpace()
    {
        $totalSpace = disk_total_space('/');
        $freeSpace = disk_free_space('/');
        $usedSpace = $totalSpace - $freeSpace;
        $usagePercentage = ($usedSpace / $totalSpace) * 100;
        
        return [
            'status' => $usagePercentage < 80 ? 'healthy' : ($usagePercentage < 90 ? 'degraded' : 'critical'),
            'total_bytes' => $totalSpace,
            'used_bytes' => $usedSpace,
            'free_bytes' => $freeSpace,
            'usage_percentage' => round($usagePercentage, 2),
            'timestamp' => now()
        ];
    }
}
```

#### 12.6.3 Auto-Recovery System
**Self-Healing Recovery Actions:**
```php
// app/Services/AutoRecoveryService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class AutoRecoveryService
{
    public static function triggerAutoRecovery($healthResults)
    {
        Log::warning('System health degraded, triggering auto-recovery', [
            'health_results' => $healthResults
        ]);
        
        foreach ($healthResults['components'] as $component => $result) {
            if ($result['status'] === 'critical') {
                self::handleCriticalIssue($component, $result);
            } elseif ($result['status'] === 'degraded') {
                self::handleDegradedIssue($component, $result);
            }
        }
    }
    
    private static function handleCriticalIssue($component, $result)
    {
        switch ($component) {
            case 'database':
                self::recoverDatabase();
                break;
            case 'cache':
                self::recoverCache();
                break;
            case 'storage':
                self::recoverStorage();
                break;
            case 'filesystem':
                self::recoverFilesystem();
                break;
            case 'integrity':
                self::recoverDataIntegrity($result['issues']);
                break;
        }
    }
    
    private static function handleDegradedIssue($component, $result)
    {
        switch ($component) {
            case 'database':
                self::optimizeDatabase($result);
                break;
            case 'integrity':
                self::repairDataIntegrity($result['issues']);
                break;
            case 'memory':
                self::optimizeMemory();
                break;
            case 'disk_space':
                self::cleanupDiskSpace();
                break;
        }
    }
    
    public static function recoverDatabase()
    {
        try {
            Log::info('Attempting database recovery');
            
            // 1. Reset database connection
            DB::purge();
            
            // 2. Test connection
            DB::connection()->getPdo();
            
            // 3. Check and repair tables
            $tables = DB::select('SHOW TABLES');
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                
                try {
                    DB::statement("REPAIR TABLE {$tableName}");
                    Log::info("Repaired table: {$tableName}");
                } catch (\Exception $e) {
                    Log::error("Failed to repair table {$tableName}: " . $e->getMessage());
                }
            }
            
            // 4. Optimize tables
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                
                try {
                    DB::statement("OPTIMIZE TABLE {$tableName}");
                    Log::info("Optimized table: {$tableName}");
                } catch (\Exception $e) {
                    Log::error("Failed to optimize table {$tableName}: " . $e->getMessage());
                }
            }
            
            // 5. Run migrations if needed
            try {
                Artisan::call('migrate', ['--force' => true]);
                Log::info('Database migrations completed');
            } catch (\Exception $e) {
                Log::error('Migration failed: ' . $e->getMessage());
            }
            
            Log::info('Database recovery completed successfully');
            
        } catch (\Exception $e) {
            Log::error('Database recovery failed: ' . $e->getMessage());
            
            // Try database restoration from backup
            self::restoreDatabaseFromBackup();
        }
    }
    
    public static function recoverDataIntegrity($issues)
    {
        Log::info('Starting data integrity recovery', ['issues' => $issues]);
        
        foreach ($issues as $issue) {
            switch ($issue['type']) {
                case 'orphaned_records':
                    self::fixOrphanedRecords($issue);
                    break;
                case 'duplicate_primary_keys':
                    self::fixDuplicatePrimaryKeys($issue);
                    break;
                case 'missing_foreign_keys':
                    self::fixMissingForeignKeys($issue);
                    break;
            }
        }
        
        Log::info('Data integrity recovery completed');
    }
    
    private static function fixOrphanedRecords($issue)
    {
        $table = $issue['table'];
        $column = $issue['column'];
        
        try {
            // Option 1: Delete orphaned records
            $deleted = DB::delete("
                DELETE t FROM {$table} t
                LEFT JOIN referenced_table r ON t.{$column} = r.id
                WHERE t.{$column} IS NOT NULL AND r.id IS NULL
            ");
            
            Log::info("Deleted {$deleted} orphaned records from {$table}");
            
        } catch (\Exception $e) {
            Log::error("Failed to fix orphaned records in {$table}: " . $e->getMessage());
        }
    }
    
    private static function fixDuplicatePrimaryKeys($issue)
    {
        $table = $issue['table'];
        $column = $issue['column'];
        
        try {
            // Find duplicates
            $duplicates = DB::select("
                SELECT {$column}, MIN(id) as keep_id, GROUP_CONCAT(id) as duplicate_ids
                FROM {$table}
                GROUP BY {$column}
                HAVING COUNT(*) > 1
            ");
            
            foreach ($duplicates as $duplicate) {
                $duplicateIds = explode(',', $duplicate->duplicate_ids);
                $keepId = $duplicate->keep_id;
                
                // Remove duplicates, keep the first one
                $idsToDelete = array_diff($duplicateIds, [$keepId]);
                
                if (!empty($idsToDelete)) {
                    $deleted = DB::table($table)
                        ->whereIn('id', $idsToDelete)
                        ->delete();
                    
                    Log::info("Deleted {$deleted} duplicate records from {$table}");
                }
            }
            
        } catch (\Exception $e) {
            Log::error("Failed to fix duplicate primary keys in {$table}: " . $e->getMessage());
        }
    }
    
    public static function recoverCache()
    {
        try {
            Log::info('Recovering cache system');
            
            // Clear all cache
            Artisan::call('cache:clear');
            
            // Restart cache service if using Redis
            if (config('cache.default') === 'redis') {
                // Restart Redis connection
                Cache::flush();
            }
            
            // Warm up critical cache
            self::warmUpCache();
            
            Log::info('Cache recovery completed');
            
        } catch (\Exception $e) {
            Log::error('Cache recovery failed: ' . $e->getMessage());
        }
    }
    
    private static function warmUpCache()
    {
        // Warm up master data cache
        \App\Services\MasterDataService::getProvinces();
        \App\Services\MasterDataService::getProductCategories();
        \App\Services\MasterDataService::getUnitsByType('Weight');
        
        // Warm up frequently accessed data
        Cache::remember('system_config', 3600, function () {
            return DB::table('system_settings')->pluck('value', 'key');
        });
    }
    
    public static function recoverFilesystem()
    {
        try {
            Log::info('Recovering filesystem');
            
            // Create missing directories
            $criticalDirs = [
                storage_path('app/public'),
                storage_path('logs'),
                storage_path('framework/cache'),
                storage_path('framework/sessions'),
                storage_path('framework/views')
            ];
            
            foreach ($criticalDirs as $dir) {
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                    Log::info("Created directory: {$dir}");
                }
            }
            
            // Restore missing files from backup
            $criticalFiles = [
                '.env' => 'backup/.env',
                'config/app.php' => 'backup/config/app.php',
                'config/database.php' => 'backup/config/database.php'
            ];
            
            foreach ($criticalFiles as $file => $backup) {
                if (!file_exists(base_path($file)) && file_exists(base_path($backup))) {
                    copy(base_path($backup), base_path($file));
                    Log::info("Restored file: {$file}");
                }
            }
            
            // Fix permissions
            foreach ($criticalDirs as $dir) {
                if (!is_writable($dir)) {
                    chmod($dir, 0755);
                    Log::info("Fixed permissions for: {$dir}");
                }
            }
            
            Log::info('Filesystem recovery completed');
            
        } catch (\Exception $e) {
            Log::error('Filesystem recovery failed: ' . $e->getMessage());
        }
    }
    
    public static function restoreDatabaseFromBackup()
    {
        try {
            Log::warning('Attempting database restoration from backup');
            
            // Get latest backup
            $backupPath = storage_path('backups/database');
            $backups = glob($backupPath . '/*.sql');
            
            if (empty($backups)) {
                Log::error('No database backups found');
                return false;
            }
            
            // Sort by date, get latest
            usort($backups, function ($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            
            $latestBackup = $backups[0];
            
            // Restore database
            $dbConfig = config('database.connections.mysql');
            $command = "mysql -h {$dbConfig['host']} -u {$dbConfig['username']} -p{$dbConfig['password']} {$dbConfig['database']} < {$latestBackup}";
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                Log::info('Database restored successfully from backup');
                return true;
            } else {
                Log::error('Database restoration failed', ['output' => $output]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('Database restoration failed: ' . $e->getMessage());
            return false;
        }
    }
    
    public static function cleanupDiskSpace()
    {
        try {
            Log::info('Starting disk space cleanup');
            
            // Clear old logs
            $logFiles = glob(storage_path('logs/*.log'));
            foreach ($logFiles as $file) {
                if (filemtime($file) < strtotime('-30 days')) {
                    unlink($file);
                    Log::info("Deleted old log file: {$file}");
                }
            }
            
            // Clear old cache files
            $cacheFiles = glob(storage_path('framework/cache/*'));
            foreach ($cacheFiles as $file) {
                if (is_file($file) && filemtime($file) < strtotime('-7 days')) {
                    unlink($file);
                }
            }
            
            // Clear old session files
            $sessionFiles = glob(storage_path('framework/sessions/*'));
            foreach ($sessionFiles as $file) {
                if (filemtime($file) < strtotime('-2 days')) {
                    unlink($file);
                }
            }
            
            // Clear old backups (keep last 10)
            $backupFiles = glob(storage_path('backups/*/*.sql'));
            if (count($backupFiles) > 10) {
                usort($backupFiles, function ($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                
                $filesToDelete = array_slice($backupFiles, 10);
                foreach ($filesToDelete as $file) {
                    unlink($file);
                    Log::info("Deleted old backup: {$file}");
                }
            }
            
            Log::info('Disk space cleanup completed');
            
        } catch (\Exception $e) {
            Log::error('Disk space cleanup failed: ' . $e->getMessage());
        }
    }
}
```

#### 12.6.4 Automated Backup System
**Real-time Backup Implementation:**
```php
// app/Services/BackupService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BackupService
{
    public static function createDatabaseBackup()
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "backup_{$timestamp}.sql";
            $backupPath = storage_path("backups/database/{$filename}");
            
            // Ensure backup directory exists
            if (!is_dir(dirname($backupPath))) {
                mkdir(dirname($backupPath), 0755, true);
            }
            
            $dbConfig = config('database.connections.mysql');
            $command = "mysqldump -h {$dbConfig['host']} -u {$dbConfig['username']} -p{$dbConfig['password']} --single-transaction --routines --triggers {$dbConfig['database']} > {$backupPath}";
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                // Compress backup
                $compressedPath = "{$backupPath}.gz";
                exec("gzip {$backupPath}");
                
                Log::info("Database backup created: {$compressedPath}");
                
                // Store backup info
                DB::table('backup_logs')->insert([
                    'type' => 'database',
                    'filename' => basename($compressedPath),
                    'size' => filesize($compressedPath),
                    'created_at' => now()
                ]);
                
                return $compressedPath;
            } else {
                Log::error('Database backup failed', ['output' => $output]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('Database backup failed: ' . $e->getMessage());
            return false;
        }
    }
    
    public static function createFilesBackup()
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "files_backup_{$timestamp}.tar.gz";
            $backupPath = storage_path("backups/files/{$filename}");
            
            // Ensure backup directory exists
            if (!is_dir(dirname($backupPath))) {
                mkdir(dirname($backupPath), 0755, true);
            }
            
            // Create backup of important files
            $filesToBackup = [
                '.env',
                'config/',
                'storage/app/public/',
                'resources/'
            ];
            
            $command = "tar -czf {$backupPath} -C " . base_path() . " " . implode(' ', $filesToBackup);
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                Log::info("Files backup created: {$backupPath}");
                
                // Store backup info
                DB::table('backup_logs')->insert([
                    'type' => 'files',
                    'filename' => basename($backupPath),
                    'size' => filesize($backupPath),
                    'created_at' => now()
                ]);
                
                return $backupPath;
            } else {
                Log::error('Files backup failed', ['output' => $output]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('Files backup failed: ' . $e->getMessage());
            return false;
        }
    }
    
    public static function restoreFromBackup($backupFile)
    {
        try {
            $backupPath = storage_path("backups/database/{$backupFile}");
            
            if (!file_exists($backupPath)) {
                Log::error("Backup file not found: {$backupFile}");
                return false;
            }
            
            // Create backup before restore
            self::createDatabaseBackup();
            
            // Restore database
            $dbConfig = config('database.connections.mysql');
            $command = "mysql -h {$dbConfig['host']} -u {$dbConfig['username']} -p{$dbConfig['password']} {$dbConfig['database']} < {$backupPath}";
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                Log::info("Database restored from: {$backupFile}");
                return true;
            } else {
                Log::error('Database restore failed', ['output' => $output]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('Database restore failed: ' . $e->getMessage());
            return false;
        }
    }
}
```

#### 12.6.5 Real-time Monitoring Dashboard
**Health Monitoring Dashboard:**
```php
// app/Http/Controllers/HealthController.php
<?php

namespace App\Http\Controllers;

use App\Services\HealthMonitoringService;
use App\Services\AutoRecoveryService;
use Illuminate\Http\Request;

class HealthController extends Controller
{
    public function dashboard()
    {
        $healthStatus = HealthMonitoringService::runFullHealthCheck();
        
        return view('health.dashboard', compact('healthStatus'));
    }
    
    public function check()
    {
        $healthStatus = HealthMonitoringService::runFullHealthCheck();
        
        return response()->json($healthStatus);
    }
    
    public function triggerRecovery(Request $request)
    {
        $component = $request->get('component');
        
        if ($component) {
            // Trigger recovery for specific component
            $healthResults = ['components' => [$component => ['status' => 'critical']]];
            AutoRecoveryService::triggerAutoRecovery($healthResults);
        } else {
            // Trigger full system recovery
            $healthResults = HealthMonitoringService::runFullHealthCheck();
            AutoRecoveryService::triggerAutoRecovery($healthResults);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Recovery process initiated'
        ]);
    }
    
    public function createBackup()
    {
        $databaseBackup = \App\Services\BackupService::createDatabaseBackup();
        $filesBackup = \App\Services\BackupService::createFilesBackup();
        
        return response()->json([
            'success' => true,
            'database_backup' => $databaseBackup ? basename($databaseBackup) : false,
            'files_backup' => $filesBackup ? basename($filesBackup) : false
        ]);
    }
}
```

#### 12.6.6 Automated Health Checks
**Scheduled Health Monitoring:**
```php
// app/Console/Commands/HealthCheck.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HealthMonitoringService;
use App\Services\AutoRecoveryService;
use App\Services\BackupService;

class HealthCheck extends Command
{
    protected $signature = 'health:check {--auto-recover}';
    protected $description = 'Run system health check and auto-recovery';
    
    public function handle()
    {
        $this->info('Running system health check...');
        
        $healthResults = HealthMonitoringService::runFullHealthCheck();
        
        $this->info('Overall Status: ' . $healthResults['overall_status']);
        
        foreach ($healthResults['components'] as $component => $result) {
            $status = $result['status'];
            $this->line("  {$component}: {$status}");
            
            if ($status !== 'healthy') {
                $this->line("    Message: " . ($result['message'] ?? 'No message'));
            }
        }
        
        // Trigger auto-recovery if requested
        if ($this->option('auto-recover') && $healthResults['overall_status'] !== 'healthy') {
            $this->info('Triggering auto-recovery...');
            AutoRecoveryService::triggerAutoRecovery($healthResults);
        }
        
        // Create backup if critical issues found
        if ($healthResults['overall_status'] === 'critical') {
            $this->info('Creating emergency backup...');
            BackupService::createDatabaseBackup();
            BackupService::createFilesBackup();
        }
        
        return $healthResults['overall_status'] === 'healthy' ? 0 : 1;
    }
}
```

**Scheduler Setup:**
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Health check every 5 minutes
    $schedule->command('health:check --auto-recover')->everyFiveMinutes();
    
    // Full backup every 6 hours
    $schedule->call(function () {
        BackupService::createDatabaseBackup();
    })->everySixHours();
    
    // Files backup daily
    $schedule->call(function () {
        BackupService::createFilesBackup();
    })->daily();
    
    // Cleanup old backups weekly
    $schedule->call(function () {
        AutoRecoveryService::cleanupDiskSpace();
    })->weekly();
}
```

### 12.6.7 Benefits of Self-Healing System

#### **System Reliability:**
- **Automatic Recovery:** No manual intervention needed
- **Zero Downtime:** Continuous service availability
- **Data Integrity:** Automatic corruption detection and repair
- **Performance Optimization:** Automatic performance tuning

#### **Operational Benefits:**
- **Reduced Maintenance:** Less manual monitoring required
- **Fast Recovery:** Immediate response to issues
- **Preventive Action:** Detect issues before they become critical
- **Comprehensive Monitoring:** All system components monitored

#### **Business Benefits:**
- **24/7 Availability:** System always available
- **Data Protection:** Automatic backups and recovery
- **Cost Savings:** Reduced emergency maintenance costs
- **Customer Satisfaction:** Reliable service delivery

### 12.7 Advanced Security & Cyber Defense System

#### 12.7.1 Multi-Layer Security Architecture
**Zero Trust Security Model:**
```
CYBER DEFENSE ARCHITECTURE:
├── Network Security Layer
│   ├── Firewall Configuration
│   ├── DDoS Protection
│   ├── Intrusion Detection System (IDS)
│   └── Network Segmentation
├── Application Security Layer
│   ├── Web Application Firewall (WAF)
│   ├── Input Validation & Sanitization
│   ├── SQL Injection Prevention
│   └── XSS Protection
├── Authentication & Authorization Layer
│   ├── Multi-Factor Authentication (MFA)
│   ├── Biometric Authentication
│   ├── Role-Based Access Control (RBAC)
│   └── Session Management
├── Data Protection Layer
│   ├── End-to-End Encryption
│   ├── Data Masking
│   ├── Database Encryption
│   └── Secure Backup Storage
├── Monitoring & Detection Layer
│   ├── Real-time Threat Detection
│   ├── Anomaly Detection
│   ├── Security Information & Event Management (SIEM)
│   └── Behavioral Analytics
└── Response & Recovery Layer
    ├── Incident Response Plan
    ├── Automatic Threat Neutralization
    ├── Forensic Analysis
    └── Disaster Recovery
```

#### 12.7.2 Advanced Authentication System
**Multi-Factor Authentication Implementation:**
```php
// app/Services/SecurityService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class SecurityService
{
    private static $failedLoginThreshold = 5;
    private static $lockoutDuration = 15; // minutes
    private static $sessionTimeout = 30; // minutes
    
    public static function enforceMultiFactorAuth($user)
    {
        // Check if MFA is required for this user
        if (self::shouldRequireMFA($user)) {
            return self::initiateMFA($user);
        }
        
        return true;
    }
    
    public static function initiateMFA($user)
    {
        $mfaMethods = [];
        
        // SMS OTP
        if ($user->phone_number) {
            $mfaMethods['sms'] = self::generateSMSOTP($user);
        }
        
        // Email OTP
        if ($user->email) {
            $mfaMethods['email'] = self::generateEmailOTP($user);
        }
        
        // TOTP (Google Authenticator)
        if ($user->google2fa_secret) {
            $mfaMethods['totp'] = self::generateTOTPChallenge($user);
        }
        
        // Biometric (if device supports)
        $mfaMethods['biometric'] = self::initiateBiometricAuth($user);
        
        return [
            'required' => true,
            'methods' => $mfaMethods,
            'expires_at' => now()->addMinutes(10)
        ];
    }
    
    public static function verifyMFA($user, $method, $code)
    {
        switch ($method) {
            case 'sms':
                return self::verifySMSOTP($user, $code);
            case 'email':
                return self::verifyEmailOTP($user, $code);
            case 'totp':
                return self::verifyTOTP($user, $code);
            case 'biometric':
                return self::verifyBiometric($user, $code);
            default:
                return false;
        }
    }
    
    public static function detectSuspiciousLogin($request, $user)
    {
        $suspiciousFactors = [];
        
        // Check IP address
        $currentIP = $request->ip();
        $lastIP = Cache::get("user_last_ip_{$user->id}");
        
        if ($lastIP && $lastIP !== $currentIP) {
            $suspiciousFactors[] = 'ip_change';
            
            // Check if IP is from different country
            if (self::isDifferentCountry($lastIP, $currentIP)) {
                $suspiciousFactors[] = 'country_change';
            }
        }
        
        // Check device fingerprint
        $currentFingerprint = self::generateDeviceFingerprint($request);
        $lastFingerprint = Cache::get("user_device_{$user->id}");
        
        if ($lastFingerprint && $lastFingerprint !== $currentFingerprint) {
            $suspiciousFactors[] = 'new_device';
        }
        
        // Check time-based patterns
        $currentHour = now()->hour;
        $usualHours = self::getUserUsualHours($user);
        
        if (!in_array($currentHour, $usualHours)) {
            $suspiciousFactors[] = 'unusual_time';
        }
        
        // Check login frequency
        $recentLogins = Cache::get("recent_logins_{$user->id}", []);
        if (count($recentLogins) > 10) {
            $suspiciousFactors[] = 'high_frequency';
        }
        
        if (!empty($suspiciousFactors)) {
            self::handleSuspiciousLogin($user, $request, $suspiciousFactors);
            return true;
        }
        
        return false;
    }
    
    private static function handleSuspiciousLogin($user, $request, $factors)
    {
        Log::warning('Suspicious login detected', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'factors' => $factors,
            'user_agent' => $request->userAgent()
        ]);
        
        // Require additional verification
        Cache::put("suspicious_login_{$user->id}", $factors, 3600);
        
        // Send security alert
        self::sendSecurityAlert($user, $request, $factors);
        
        // Block login if high risk
        if (in_array('country_change', $factors) || in_array('high_frequency', $factors)) {
            Cache::put("login_blocked_{$user->id}", true, 1800); // 30 minutes
            return false;
        }
        
        return true;
    }
    
    public static function enforceSessionSecurity($user, $session)
    {
        // Session timeout
        $session->put('last_activity', now());
        
        // Device binding
        $session->put('device_fingerprint', self::generateDeviceFingerprint(request()));
        
        // IP binding
        $session->put('ip_address', request()->ip());
        
        // CSRF protection
        $session->regenerateToken();
        
        return $session;
    }
    
    public static function validateSession($user, $session)
    {
        // Check session timeout
        $lastActivity = $session->get('last_activity');
        if ($lastActivity && now()->diffInMinutes($lastActivity) > self::$sessionTimeout) {
            return false;
        }
        
        // Check IP binding
        $sessionIP = $session->get('ip_address');
        if ($sessionIP && $sessionIP !== request()->ip()) {
            Log::warning('Session IP mismatch', [
                'user_id' => $user->id,
                'session_ip' => $sessionIP,
                'current_ip' => request()->ip()
            ]);
            return false;
        }
        
        // Check device binding
        $sessionFingerprint = $session->get('device_fingerprint');
        $currentFingerprint = self::generateDeviceFingerprint(request());
        
        if ($sessionFingerprint && $sessionFingerprint !== $currentFingerprint) {
            Log::warning('Session device mismatch', [
                'user_id' => $user->id,
                'session_device' => $sessionFingerprint,
                'current_device' => $currentFingerprint
            ]);
            return false;
        }
        
        // Update last activity
        $session->put('last_activity', now());
        
        return true;
    }
    
    private static function generateDeviceFingerprint($request)
    {
        $userAgent = $request->userAgent();
        $acceptLanguage = $request->header('Accept-Language');
        $acceptEncoding = $request->header('Accept-Encoding');
        
        return hash('sha256', $userAgent . $acceptLanguage . $acceptEncoding);
    }
}
```

#### 12.7.3 Real-time Threat Detection
**AI-Powered Security Monitoring:**
```php
// app/Services/ThreatDetectionService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ThreatDetectionService
{
    public static function analyzeRequest($request)
    {
        $threats = [];
        
        // SQL Injection Detection
        $sqlThreats = self::detectSQLInjection($request);
        if (!empty($sqlThreats)) {
            $threats[] = ['type' => 'sql_injection', 'details' => $sqlThreats];
        }
        
        // XSS Detection
        $xssThreats = self::detectXSS($request);
        if (!empty($xssThreats)) {
            $threats[] = ['type' => 'xss', 'details' => $xssThreats];
        }
        
        // CSRF Detection
        $csrfThreats = self::detectCSRF($request);
        if (!empty($csrfThreats)) {
            $threats[] = ['type' => 'csrf', 'details' => $csrfThreats];
        }
        
        // Rate Limiting
        $rateThreats = self::detectRateLimiting($request);
        if (!empty($rateThreats)) {
            $threats[] = ['type' => 'rate_limit', 'details' => $rateThreats];
        }
        
        // Bot Detection
        $botThreats = self::detectBot($request);
        if (!empty($botThreats)) {
            $threats[] = ['type' => 'bot', 'details' => $botThreats];
        }
        
        // Anomaly Detection
        $anomalyThreats = self::detectAnomalies($request);
        if (!empty($anomalyThreats)) {
            $threats[] = ['type' => 'anomaly', 'details' => $anomalyThreats];
        }
        
        if (!empty($threats)) {
            self::handleThreats($request, $threats);
        }
        
        return $threats;
    }
    
    private static function detectSQLInjection($request)
    {
        $suspiciousPatterns = [
            '/(\s|^)(union|select|insert|update|delete|drop|create|alter|exec|execute)\s/i',
            '/(\s|^)(or|and)\s+\d+\s*=\s*\d+/i',
            '/(\s|^)(or|and)\s+["\']?\w+["\']?\s*=\s*["\']?\w+["\']?/i',
            '/(\s|^)(--|#|\/\*|\*\/)/i',
            '/(\s|^)(waitfor|delay|benchmark)\s/i'
        ];
        
        $threats = [];
        
        // Check all input parameters
        foreach ($request->all() as $key => $value) {
            if (is_string($value)) {
                foreach ($suspiciousPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $threats[] = [
                            'parameter' => $key,
                            'value' => substr($value, 0, 100),
                            'pattern' => $pattern
                        ];
                    }
                }
            }
        }
        
        return $threats;
    }
    
    private static function detectXSS($request)
    {
        $suspiciousPatterns = [
            '/<script[^>]*>.*?<\/script>/i',
            '/<iframe[^>]*>.*?<\/iframe>/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<img[^>]*src\s*=\s*["\']?(javascript|data):/i'
        ];
        
        $threats = [];
        
        foreach ($request->all() as $key => $value) {
            if (is_string($value)) {
                foreach ($suspiciousPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $threats[] = [
                            'parameter' => $key,
                            'value' => substr($value, 0, 100),
                            'pattern' => $pattern
                        ];
                    }
                }
            }
        }
        
        return $threats;
    }
    
    private static function detectRateLimiting($request)
    {
        $ip = $request->ip();
        $endpoint = $request->path();
        
        // Check request rate per IP
        $key = "rate_limit_{$ip}_{$endpoint}";
        $requests = Cache::get($key, []);
        $now = now()->timestamp;
        
        // Remove old requests (older than 1 minute)
        $requests = array_filter($requests, function ($timestamp) use ($now) {
            return $now - $timestamp < 60;
        });
        
        $requests[] = $now;
        Cache::put($key, $requests, 60);
        
        // Check if rate limit exceeded
        $limits = [
            'login' => 5, // 5 requests per minute
            'api' => 100, // 100 requests per minute
            'default' => 60 // 60 requests per minute
        ];
        
        $limit = $limits[$endpoint] ?? $limits['default'];
        
        if (count($requests) > $limit) {
            return [
                'ip' => $ip,
                'endpoint' => $endpoint,
                'requests' => count($requests),
                'limit' => $limit
            ];
        }
        
        return [];
    }
    
    private static function handleThreats($request, $threats)
    {
        $ip = $request->ip();
        $user = $request->user();
        
        Log::critical('Security threats detected', [
            'ip' => $ip,
            'user_id' => $user ? $user->id : null,
            'threats' => $threats,
            'user_agent' => $request->userAgent()
        ]);
        
        // Block IP for severe threats
        $severeThreats = ['sql_injection', 'xss', 'rate_limit'];
        foreach ($threats as $threat) {
            if (in_array($threat['type'], $severeThreats)) {
                Cache::put("blocked_ip_{$ip}", true, 3600); // 1 hour
                self::sendSecurityAlert($user, $request, $threats);
                abort(403, 'Access blocked due to suspicious activity');
            }
        }
        
        // Log and monitor other threats
        foreach ($threats as $threat) {
            DB::table('security_logs')->insert([
                'ip_address' => $ip,
                'user_id' => $user ? $user->id : null,
                'threat_type' => $threat['type'],
                'threat_details' => json_encode($threat['details']),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);
        }
    }
}
```

#### 12.7.4 Data Protection & Encryption
**Advanced Encryption Implementation:**
```php
// app/Services/EncryptionService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class EncryptionService
{
    public static function encryptSensitiveData($data)
    {
        try {
            // Use AES-256 encryption
            return Crypt::encrypt($data);
        } catch (\Exception $e) {
            Log::error('Encryption failed: ' . $e->getMessage());
            return false;
        }
    }
    
    public static function decryptSensitiveData($encryptedData)
    {
        try {
            return Crypt::decrypt($encryptedData);
        } catch (\Exception $e) {
            Log::error('Decryption failed: ' . $e->getMessage());
            return false;
        }
    }
    
    public static function encryptField($model, $field)
    {
        if (isset($model->$field)) {
            $model->$field = self::encryptSensitiveData($model->$field);
        }
        
        return $model;
    }
    
    public static function decryptField($model, $field)
    {
        if (isset($model->$field)) {
            $decrypted = self::decryptSensitiveData($model->$field);
            if ($decrypted !== false) {
                $model->$field = $decrypted;
            }
        }
        
        return $model;
    }
    
    public static function hashPassword($password)
    {
        return Hash::make($password);
    }
    
    public static function verifyPassword($password, $hash)
    {
        return Hash::check($password, $hash);
    }
    
    public static function generateSecureToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }
    
    public static function maskSensitiveData($data, $maskChar = '*', $visibleChars = 4)
    {
        if (strlen($data) <= $visibleChars) {
            return str_repeat($maskChar, strlen($data));
        }
        
        $visible = substr($data, 0, $visibleChars);
        $masked = str_repeat($maskChar, strlen($data) - $visibleChars);
        
        return $visible . $masked;
    }
}

// Model trait for automatic encryption
// app/Traits/Encryptable.php
<?php

namespace App\Traits;

use App\Services\EncryptionService;

trait Encryptable
{
    protected $encryptable = [];
    
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        
        if (in_array($key, $this->encryptable) && $value !== null) {
            return EncryptionService::decryptSensitiveData($value);
        }
        
        return $value;
    }
    
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable) && $value !== null) {
            $value = EncryptionService::encryptSensitiveData($value);
        }
        
        return parent::setAttribute($key, $value);
    }
    
    public function toArray()
    {
        $array = parent::toArray();
        
        foreach ($this->encryptable as $key) {
            if (isset($array[$key])) {
                $array[$key] = EncryptionService::maskSensitiveData($array[$key]);
            }
        }
        
        return $array;
    }
}
```

#### 12.7.5 Security Middleware
**Custom Security Middleware:**
```php
// app/Http/Middleware/SecurityMiddleware.php
<?php

namespace App\Http\Middleware;

use App\Services\ThreatDetectionService;
use Closure;
use Illuminate\Http\Request;

class SecurityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if IP is blocked
        if ($this->isIPBlocked($request->ip())) {
            abort(403, 'Access blocked');
        }
        
        // Analyze request for threats
        $threats = ThreatDetectionService::analyzeRequest($request);
        
        if (!empty($threats)) {
            // Log security event
            Log::warning('Security threats detected', [
                'ip' => $request->ip(),
                'threats' => $threats
            ]);
        }
        
        // Add security headers
        $response = $next($request);
        
        $this->addSecurityHeaders($response);
        
        return $response;
    }
    
    private function isIPBlocked($ip)
    {
        return Cache::get("blocked_ip_{$ip}", false);
    }
    
    private function addSecurityHeaders($response)
    {
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'");
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        return $response;
    }
}
```

#### 12.7.6 Security Monitoring Dashboard
**Real-time Security Dashboard:**
```php
// app/Http/Controllers/SecurityController.php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SecurityController extends Controller
{
    public function dashboard()
    {
        $securityStats = $this->getSecurityStats();
        $recentThreats = $this->getRecentThreats();
        $blockedIPs = $this->getBlockedIPs();
        
        return view('security.dashboard', compact('securityStats', 'recentThreats', 'blockedIPs'));
    }
    
    public function getSecurityStats()
    {
        return [
            'total_threats' => DB::table('security_logs')->count(),
            'blocked_ips' => count(Cache::getKeys('blocked_ip_*')),
            'active_sessions' => DB::table('sessions')->count(),
            'failed_logins' => DB::table('security_logs')
                ->where('threat_type', 'failed_login')
                ->where('created_at', '>=', now()->subDay())
                ->count(),
            'suspicious_logins' => DB::table('security_logs')
                ->where('threat_type', 'suspicious_login')
                ->where('created_at', '>=', now()->subDay())
                ->count()
        ];
    }
    
    public function getRecentThreats()
    {
        return DB::table('security_logs')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }
    
    public function getBlockedIPs()
    {
        $blockedIPs = [];
        
        foreach (Cache::getKeys('blocked_ip_*') as $key) {
            $ip = str_replace('blocked_ip_', '', $key);
            $blockedIPs[] = [
                'ip' => $ip,
                'blocked_until' => Cache::get($key),
                'threat_count' => DB::table('security_logs')
                    ->where('ip_address', $ip)
                    ->where('created_at', '>=', now()->subDay())
                    ->count()
            ];
        }
        
        return $blockedIPs;
    }
    
    public function blockIP(Request $request)
    {
        $ip = $request->get('ip');
        $duration = $request->get('duration', 3600); // 1 hour default
        
        Cache::put("blocked_ip_{$ip}", true, $duration);
        
        return response()->json([
            'success' => true,
            'message' => "IP {$ip} blocked for {$duration} seconds"
        ]);
    }
    
    public function unblockIP(Request $request)
    {
        $ip = $request->get('ip');
        
        Cache::forget("blocked_ip_{$ip}");
        
        return response()->json([
            'success' => true,
            'message' => "IP {$ip} unblocked"
        ]);
    }
}
```

#### 12.7.7 Security Best Practices Configuration
**Security Configuration:**
```php
// config/security.php
<?php

return [
    'encryption' => [
        'cipher' => 'AES-256-CBC',
        'key_length' => 32,
        'algorithm' => 'sha256'
    ],
    
    'session' => [
        'lifetime' => 1800, // 30 minutes
        'expire_on_close' => true,
        'secure' => true,
        'http_only' => true,
        'same_site' => 'strict'
    ],
    
    'password' => [
        'min_length' => 12,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => true,
        'max_age_days' => 90
    ],
    
    'mfa' => [
        'required_for_admin' => true,
        'required_for_sensitive_ops' => true,
        'otp_length' => 6,
        'otp_expiry' => 600 // 10 minutes
    ],
    
    'rate_limiting' => [
        'login' => 5, // 5 attempts per minute
        'api' => 100, // 100 requests per minute
        'registration' => 3, // 3 attempts per minute
        'password_reset' => 3 // 3 attempts per minute
    ],
    
    'blocking' => [
        'ip_block_duration' => 3600, // 1 hour
        'user_block_duration' => 1800, // 30 minutes
        'max_failed_attempts' => 5
    ],
    
    'monitoring' => [
        'log_all_requests' => false,
        'log_failed_attempts' => true,
        'log_security_events' => true,
        'retention_days' => 90
    ]
];
```

### 12.7.8 Benefits of Advanced Security System

#### **Protection Benefits:**
- **Zero Trust Architecture:** Never trust, always verify
- **Multi-Layer Defense:** Multiple security layers
- **Real-time Detection:** Instant threat identification
- **Automated Response:** Immediate threat neutralization

#### **Business Benefits:**
- **Data Protection:** Comprehensive data security
- **Compliance:** Meet regulatory requirements
- **Customer Trust:** Enhanced security reputation
- **Risk Mitigation:** Reduced security risks

#### **Operational Benefits:**
- **24/7 Monitoring:** Continuous security monitoring
- **Automated Defense:** Minimal manual intervention
- **Scalable Security:** Grows with your business
- **Incident Response:** Fast incident handling

### 12.8 Business Continuity & Disaster Recovery (BCDR)

#### 12.8.1 Disaster Recovery Architecture
**Comprehensive BCDR Framework:**
```
BUSINESS CONTINUITY & DISASTER RECOVERY:
├── Risk Assessment & Planning
│   ├── Business Impact Analysis (BIA)
│   ├── Risk Assessment Matrix
│   ├── Recovery Time Objectives (RTO)
│   └── Recovery Point Objectives (RPO)
├── Prevention & Mitigation
│   ├── Redundant Infrastructure
│   ├── Geographic Distribution
│   ├── Data Redundancy
│   └── Security Measures
├── Response & Recovery
│   ├── Incident Response Team
│   ├── Emergency Procedures
│   ├── Communication Plan
│   └── Recovery Procedures
├── Backup & Recovery Systems
│   ├── Real-time Replication
│   ├── Automated Backups
│   ├── Point-in-Time Recovery
│   └── Cloud Disaster Recovery
├── Alternative Operations
│   ├── Remote Work Capability
│   ├── Mobile Operations
│   ├── Manual Workarounds
│   └── Temporary Facilities
└── Testing & Maintenance
    ├── Regular DR Drills
    ├── Plan Updates
    ├── Training Programs
    └── Continuous Improvement
```

#### 12.8.2 Business Impact Analysis (BIA)
**Critical Business Functions Assessment:**
```php
// app/Services/BIAService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BIAService
{
    public static function conductBIA()
    {
        return [
            'critical_functions' => self::identifyCriticalFunctions(),
            'impact_assessment' => self::assessBusinessImpact(),
            'recovery_priorities' => self::prioritizeRecovery(),
            'resource_requirements' => self::identifyResourceNeeds(),
            'communication_plan' => self::developCommunicationPlan()
        ];
    }
    
    private static function identifyCriticalFunctions()
    {
        return [
            [
                'function' => 'SPPG Distribution Management',
                'criticality' => 'Critical',
                'max_downtime' => '4 hours',
                'impact_level' => 'High',
                'dependencies' => ['Database', 'Network', 'Power'],
                'workaround_available' => true,
                'manual_process_duration' => '24 hours'
            ],
            [
                'function' => 'Inventory Management',
                'criticality' => 'Critical',
                'max_downtime' => '8 hours',
                'impact_level' => 'High',
                'dependencies' => ['Database', 'Barcode System'],
                'workaround_available' => true,
                'manual_process_duration' => '48 hours'
            ],
            [
                'function' => 'Financial Transactions',
                'criticality' => 'Critical',
                'max_downtime' => '2 hours',
                'impact_level' => 'Very High',
                'dependencies' => ['Database', 'Bank API', 'Network'],
                'workaround_available' => false,
                'manual_process_duration' => '12 hours'
            ],
            [
                'function' => 'Customer Service',
                'criticality' => 'High',
                'max_downtime' => '24 hours',
                'impact_level' => 'Medium',
                'dependencies' => ['CRM System', 'Phone System'],
                'workaround_available' => true,
                'manual_process_duration' => '72 hours'
            ],
            [
                'function' => 'Reporting & Analytics',
                'criticality' => 'Medium',
                'max_downtime' => '72 hours',
                'impact_level' => 'Low',
                'dependencies' => ['Database', 'BI Tools'],
                'workaround_available' => true,
                'manual_process_duration' => '168 hours'
            ]
        ];
    }
    
    private static function assessBusinessImpact()
    {
        return [
            'financial_impact' => [
                'daily_revenue_loss' => 'Rp 50-100 juta',
                'penalty_costs' => 'Rp 10-25 juta per day',
                'recovery_costs' => 'Rp 25-50 juta',
                'reputation_damage' => 'Medium to High'
            ],
            'operational_impact' => [
                'customer_satisfaction' => 'Decrease by 40-60%',
                'employee_productivity' => 'Decrease by 70-80%',
                'supply_chain_disruption' => 'High impact',
                'regulatory_compliance' => 'Risk of violations'
            ],
            'stakeholder_impact' => [
                'customers' => 'Service disruption',
                'employees' => 'Work disruption',
                'suppliers' => 'Payment delays',
                'regulators' => 'Compliance issues',
                'investors' => 'Financial impact'
            ]
        ];
    }
    
    public static function calculateRTO($function)
    {
        $rtoMatrix = [
            'Financial Transactions' => 2, // 2 hours
            'SPPG Distribution' => 4, // 4 hours
            'Inventory Management' => 8, // 8 hours
            'Customer Service' => 24, // 24 hours
            'Reporting & Analytics' => 72 // 72 hours
        ];
        
        return $rtoMatrix[$function] ?? 24;
    }
    
    public static function calculateRPO($function)
    {
        $rpoMatrix = [
            'Financial Transactions' => 15, // 15 minutes
            'SPPG Distribution' => 60, // 1 hour
            'Inventory Management' => 240, // 4 hours
            'Customer Service' => 1440, // 24 hours
            'Reporting & Analytics' => 4320 // 72 hours
        ];
        
        return $rpoMatrix[$function] ?? 1440;
    }
}
```

#### 12.8.3 Disaster Recovery Implementation
**Multi-Site Disaster Recovery:**
```php
// app/Services/DisasterRecoveryService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class DisasterRecoveryService
{
    public static function initiateDisasterRecovery($disasterType)
    {
        Log::critical('Disaster recovery initiated', [
            'disaster_type' => $disasterType,
            'timestamp' => now()
        ]);
        
        switch ($disasterType) {
            case 'data_center_failure':
                return self::handleDataCenterFailure();
            case 'network_outage':
                return self::handleNetworkOutage();
            case 'power_failure':
                return self::handlePowerFailure();
            case 'natural_disaster':
                return self::handleNaturalDisaster();
            case 'cyber_attack':
                return self::handleCyberAttack();
            case 'equipment_failure':
                return self::handleEquipmentFailure();
            default:
                return self::handleGeneralDisaster();
        }
    }
    
    private static function handleDataCenterFailure()
    {
        $recoverySteps = [];
        
        // 1. Activate DR site
        $recoverySteps[] = self::activateDRSite();
        
        // 2. Switch to backup systems
        $recoverySteps[] = self::switchToBackupSystems();
        
        // 3. Restore from latest backup
        $recoverySteps[] = self::restoreFromBackup();
        
        // 4. Validate system integrity
        $recoverySteps[] = self::validateSystemIntegrity();
        
        // 5. Notify stakeholders
        $recoverySteps[] = self::notifyStakeholders('data_center_failure');
        
        return [
            'status' => 'recovering',
            'estimated_recovery_time' => '4-6 hours',
            'steps_completed' => $recoverySteps,
            'manual_intervention_required' => false
        ];
    }
    
    private static function handleNetworkOutage()
    {
        $recoverySteps = [];
        
        // 1. Switch to backup ISP
        $recoverySteps[] = self::switchToBackupISP();
        
        // 2. Enable offline mode
        $recoverySteps[] = self::enableOfflineMode();
        
        // 3. Activate mobile hotspot
        $recoverySteps[] = self::activateMobileHotspot();
        
        // 4. Enable satellite communication
        $recoverySteps[] = self::enableSatelliteComm();
        
        return [
            'status' => 'partial_operation',
            'estimated_recovery_time' => '2-4 hours',
            'steps_completed' => $recoverySteps,
            'manual_intervention_required' => true
        ];
    }
    
    private static function handlePowerFailure()
    {
        $recoverySteps = [];
        
        // 1. Switch to UPS power
        $recoverySteps[] = self::switchToUPS();
        
        // 2. Activate backup generator
        $recoverySteps[] = self::activateBackupGenerator();
        
        // 3. Enable power saving mode
        $recoverySteps[] = self::enablePowerSavingMode();
        
        // 4. Switch to mobile operations
        $recoverySteps[] = self::switchToMobileOperations();
        
        return [
            'status' => 'limited_operation',
            'estimated_recovery_time' => '1-3 hours',
            'steps_completed' => $recoverySteps,
            'manual_intervention_required' => true
        ];
    }
    
    private static function handleNaturalDisaster()
    {
        $recoverySteps = [];
        
        // 1. Activate emergency response team
        $recoverySteps[] = self::activateEmergencyTeam();
        
        // 2. Switch to cloud DR site
        $recoverySteps[] = self::switchToCloudDR();
        
        // 3. Enable remote work mode
        $recoverySteps[] = self::enableRemoteWork();
        
        // 4. Activate manual processes
        $recoverySteps[] = self::activateManualProcesses();
        
        // 5. Contact emergency services
        $recoverySteps[] = self::contactEmergencyServices();
        
        return [
            'status' => 'emergency_mode',
            'estimated_recovery_time' => '24-72 hours',
            'steps_completed' => $recoverySteps,
            'manual_intervention_required' => true
        ];
    }
    
    public static function activateDRSite()
    {
        try {
            // Connect to DR site
            $drConnection = DB::connection('dr_site');
            $drConnection->getPdo();
            
            // Switch database connection
            config(['database.default' => 'dr_site']);
            
            // Update application state
            Cache::put('dr_mode_active', true, 86400); // 24 hours
            
            Log::info('DR site activated successfully');
            
            return [
                'success' => true,
                'message' => 'DR site activated',
                'timestamp' => now()
            ];
            
        } catch (\Exception $e) {
            Log::error('DR site activation failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'DR site activation failed',
                'error' => $e->getMessage()
            ];
        }
    }
    
    public static function enableOfflineMode()
    {
        try {
            // Enable offline mode
            Cache::put('offline_mode_active', true, 86400);
            
            // Sync critical data locally
            self::syncCriticalDataLocally();
            
            // Disable non-essential features
            self::disableNonEssentialFeatures();
            
            Log::info('Offline mode enabled');
            
            return [
                'success' => true,
                'message' => 'Offline mode enabled',
                'available_functions' => self::getOfflineFunctions()
            ];
            
        } catch (\Exception $e) {
            Log::error('Offline mode activation failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Offline mode activation failed',
                'error' => $e->getMessage()
            ];
        }
    }
    
    private static function getOfflineFunctions()
    {
        return [
            'customer_lookup' => true,
            'inventory_check' => true,
            'order_creation' => true,
            'basic_reporting' => true,
            'financial_transactions' => false,
            'advanced_analytics' => false,
            'real_time_sync' => false
        ];
    }
    
    public static function restoreFromBackup()
    {
        try {
            // Get latest backup
            $latestBackup = self::getLatestBackup();
            
            if (!$latestBackup) {
                throw new \Exception('No backup available');
            }
            
            // Restore database
            $restoreResult = self::restoreDatabase($latestBackup);
            
            if ($restoreResult['success']) {
                // Validate data integrity
                $validationResult = self::validateDataIntegrity();
                
                if ($validationResult['valid']) {
                    Log::info('Database restored successfully from backup');
                    
                    return [
                        'success' => true,
                        'message' => 'Database restored successfully',
                        'backup_used' => $latestBackup,
                        'restored_at' => now()
                    ];
                } else {
                    throw new \Exception('Data integrity validation failed');
                }
            } else {
                throw new \Exception('Database restoration failed');
            }
            
        } catch (\Exception $e) {
            Log::error('Backup restoration failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Backup restoration failed',
                'error' => $e->getMessage()
            ];
        }
    }
}
```

#### 12.8.4 Emergency Response System
**Incident Response Automation:**
```php
// app/Services/EmergencyResponseService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class EmergencyResponseService
{
    public static function handleEmergency($emergencyType, $severity)
    {
        Log::critical('Emergency response activated', [
            'type' => $emergencyType,
            'severity' => $severity,
            'timestamp' => now()
        ]);
        
        // 1. Activate emergency response team
        self::activateEmergencyTeam($emergencyType, $severity);
        
        // 2. Notify stakeholders
        self::notifyStakeholders($emergencyType, $severity);
        
        // 3. Initiate disaster recovery
        self::initiateDisasterRecovery($emergencyType);
        
        // 4. Enable emergency procedures
        self::enableEmergencyProcedures($emergencyType);
        
        // 5. Start communication protocol
        self::startCommunicationProtocol($emergencyType, $severity);
        
        return [
            'status' => 'emergency_active',
            'response_team_activated' => true,
            'stakeholders_notified' => true,
            'disaster_recovery_initiated' => true,
            'emergency_procedures_enabled' => true
        ];
    }
    
    private static function activateEmergencyTeam($emergencyType, $severity)
    {
        $teamMembers = self::getEmergencyTeamMembers($emergencyType);
        
        foreach ($teamMembers as $member) {
            // Send emergency notification
            self::sendEmergencyNotification($member, $emergencyType, $severity);
            
            // Log team activation
            Log::info('Emergency team member activated', [
                'member' => $member['name'],
                'role' => $member['role'],
                'contact' => $member['contact']
            ]);
        }
        
        Cache::put('emergency_team_active', true, 86400);
        
        return true;
    }
    
    private static function getEmergencyTeamMembers($emergencyType)
    {
        $teams = [
            'data_center_failure' => [
                ['name' => 'IT Director', 'role' => 'Incident Commander', 'contact' => '+62812345678'],
                ['name' => 'System Admin', 'role' => 'Technical Lead', 'contact' => '+62812345679'],
                ['name' => 'Network Engineer', 'role' => 'Network Specialist', 'contact' => '+62812345680'],
                ['name' => 'Database Admin', 'role' => 'Database Specialist', 'contact' => '+62812345681']
            ],
            'natural_disaster' => [
                ['name' => 'CEO', 'role' => 'Incident Commander', 'contact' => '+62812345682'],
                ['name' => 'Operations Director', 'role' => 'Operations Lead', 'contact' => '+62812345683'],
                ['name' => 'HR Director', 'role' => 'Personnel Coordinator', 'contact' => '+62812345684'],
                ['name' => 'Security Manager', 'role' => 'Security Lead', 'contact' => '+62812345685']
            ],
            'cyber_attack' => [
                ['name' => 'CISO', 'role' => 'Incident Commander', 'contact' => '+62812345686'],
                ['name' => 'Security Analyst', 'role' => 'Security Lead', 'contact' => '+62812345687'],
                ['name' => 'Forensic Specialist', 'role' => 'Forensic Lead', 'contact' => '+62812345688'],
                ['name' => 'Legal Counsel', 'role' => 'Legal Advisor', 'contact' => '+62812345689']
            ]
        ];
        
        return $teams[$emergencyType] ?? $teams['natural_disaster'];
    }
    
    public static function notifyStakeholders($emergencyType, $severity)
    {
        $stakeholders = self::getStakeholders($emergencyType);
        
        foreach ($stakeholders as $stakeholder) {
            $message = self::generateEmergencyMessage($emergencyType, $severity, $stakeholder);
            
            // Send notification via appropriate channel
            switch ($stakeholder['notification_method']) {
                case 'sms':
                    self::sendSMSNotification($stakeholder['contact'], $message);
                    break;
                case 'email':
                    self::sendEmailNotification($stakeholder['contact'], $message);
                    break;
                case 'whatsapp':
                    self::sendWhatsAppNotification($stakeholder['contact'], $message);
                    break;
                case 'phone':
                    self::makePhoneCall($stakeholder['contact'], $message);
                    break;
            }
        }
        
        return true;
    }
    
    private static function getStakeholders($emergencyType)
    {
        return [
            ['name' => 'Management Team', 'contact' => 'management@company.com', 'notification_method' => 'email'],
            ['name' => 'All Employees', 'contact' => 'employees@company.com', 'notification_method' => 'email'],
            ['name' => 'Key Customers', 'contact' => '+62812345678', 'notification_method' => 'whatsapp'],
            ['name' => 'Suppliers', 'contact' => 'suppliers@company.com', 'notification_method' => 'email'],
            ['name' => 'Regulatory Bodies', 'contact' => '+62812345679', 'notification_method' => 'phone'],
            ['name' => 'Emergency Services', 'contact' => '+62119', 'notification_method' => 'phone']
        ];
    }
    
    private static function generateEmergencyMessage($emergencyType, $severity, $stakeholder)
    {
        $messages = [
            'data_center_failure' => [
                'subject' => 'URGENT: Data Center Failure',
                'body' => 'We are experiencing a data center failure. Emergency procedures have been activated. Estimated recovery time: 4-6 hours.'
            ],
            'natural_disaster' => [
                'subject' => 'EMERGENCY: Natural Disaster Impact',
                'body' => 'Our operations have been affected by a natural disaster. Emergency response team has been activated. Please follow emergency procedures.'
            ],
            'cyber_attack' => [
                'subject' => 'CRITICAL: Cyber Attack Detected',
                'body' => 'We have detected a cyber attack. Security measures have been activated. Some services may be temporarily unavailable.'
            ]
        ];
        
        return $messages[$emergencyType] ?? $messages['natural_disaster'];
    }
    
    public static function enableEmergencyProcedures($emergencyType)
    {
        $procedures = self::getEmergencyProcedures($emergencyType);
        
        foreach ($procedures as $procedure) {
            self::executeProcedure($procedure);
        }
        
        Cache::put('emergency_procedures_active', true, 86400);
        
        return true;
    }
    
    private static function getEmergencyProcedures($emergencyType)
    {
        return [
            'data_center_failure' => [
                ['action' => 'switch_to_dr_site', 'description' => 'Switch to disaster recovery site'],
                ['action' => 'enable_offline_mode', 'description' => 'Enable offline operations'],
                ['action' => 'activate_backup_systems', 'description' => 'Activate backup systems'],
                ['action' => 'notify_customers', 'description' => 'Notify customers about service disruption']
            ],
            'natural_disaster' => [
                ['action' => 'ensure_employee_safety', 'description' => 'Ensure employee safety first'],
                ['action' => 'activate_remote_work', 'description' => 'Activate remote work capabilities'],
                ['action' => 'enable_mobile_operations', 'description' => 'Enable mobile operations'],
                ['action' => 'contact_emergency_services', 'description' => 'Contact emergency services']
            ],
            'cyber_attack' => [
                ['action' => 'isolate_affected_systems', 'description' => 'Isolate affected systems'],
                ['action' => 'activate_incident_response', 'description' => 'Activate incident response team'],
                ['action' => 'preserve_evidence', 'description' => 'Preserve forensic evidence'],
                ['action' => 'notify_authorities', 'description' => 'Notify relevant authorities']
            ]
        ];
    }
    
    private static function executeProcedure($procedure)
    {
        try {
            switch ($procedure['action']) {
                case 'switch_to_dr_site':
                    return DisasterRecoveryService::activateDRSite();
                case 'enable_offline_mode':
                    return DisasterRecoveryService::enableOfflineMode();
                case 'ensure_employee_safety':
                    return self::ensureEmployeeSafety();
                case 'activate_remote_work':
                    return self::activateRemoteWork();
                default:
                    Log::info('Procedure executed', ['procedure' => $procedure]);
                    return true;
            }
        } catch (\Exception $e) {
            Log::error('Procedure execution failed', [
                'procedure' => $procedure,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
```

#### 12.8.5 Business Continuity Planning
**Continuity Strategy Implementation:**
```php
// app/Services/BusinessContinuityService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BusinessContinuityService
{
    public static function developBCP()
    {
        return [
            'business_continuity_plan' => self::createBCPDocument(),
            'alternative_operations' => self::planAlternativeOperations(),
            'remote_work_capability' => self::establishRemoteWork(),
            'mobile_operations' => self::setupMobileOperations(),
            'manual_workarounds' => self::developManualWorkarounds(),
            'communication_strategy' => self::planCommunication(),
            'training_programs' => self::developTrainingPrograms()
        ];
    }
    
    private static function planAlternativeOperations()
    {
        return [
            'primary_alternatives' => [
                [
                    'scenario' => 'System Unavailable',
                    'alternative' => 'Manual Paper-Based System',
                    'implementation_time' => '2 hours',
                    'duration' => 'Up to 72 hours',
                    'resources_needed' => ['Paper forms', 'Manual calculators', 'Phone lines'],
                    'staff_required' => 4,
                    'training_required' => true
                ],
                [
                    'scenario' => 'Power Outage',
                    'alternative' => 'Mobile Operations',
                    'implementation_time' => '1 hour',
                    'duration' => 'Up to 24 hours',
                    'resources_needed' => ['Mobile phones', 'Portable power banks', 'Vehicle'],
                    'staff_required' => 2,
                    'training_required' => false
                ],
                [
                    'scenario' => 'Network Outage',
                    'alternative' => 'Offline Mode with Local Storage',
                    'implementation_time' => '30 minutes',
                    'duration' => 'Up to 48 hours',
                    'resources_needed' => ['Laptops with local DB', 'USB drives', 'Manual sync procedures'],
                    'staff_required' => 3,
                    'training_required' => true
                ]
            ],
            'secondary_alternatives' => [
                [
                    'scenario' => 'Extended Outage (>72 hours)',
                    'alternative' => 'Third-Party Facility',
                    'implementation_time' => '24 hours',
                    'duration' => 'Until recovery',
                    'resources_needed' => ['Rented office space', 'Temporary equipment', 'Transportation'],
                    'staff_required' => 6,
                    'training_required' => false
                ]
            ]
        ];
    }
    
    public static function establishRemoteWork()
    {
        return [
            'remote_work_capabilities' => [
                'cloud_access' => [
                    'available' => true,
                    'systems_accessible' => ['CRM', 'Email', 'Document Management'],
                    'security_measures' => ['VPN', 'MFA', 'Device encryption'],
                    'bandwidth_requirements' => '10 Mbps per user'
                ],
                'communication_tools' => [
                    'primary' => ['Microsoft Teams', 'Zoom', 'WhatsApp Business'],
                    'backup' => ['Phone calls', 'SMS', 'Email'],
                    'emergency' => ['Satellite phone', 'Radio communication']
                ],
                'equipment_requirements' => [
                    'laptop' => 'Business-grade with encryption',
                    'mobile_phone' => 'Company-issued with security apps',
                    'internet_backup' => '4G/5G mobile hotspot',
                    'power_backup' => 'Portable power bank (20,000mAh)'
                ]
            ],
            'remote_work_procedures' => [
                'daily_check_in' => 'Morning and evening check-ins required',
                'status_reporting' => 'Hourly status updates during emergency',
                'communication_protocol' => 'Use primary tools, fallback to backup',
                'security_protocol' => 'VPN required for all system access',
                'data_handling' => 'Local storage only when necessary, encrypt all data'
            ]
        ];
    }
    
    public static function setupMobileOperations()
    {
        return [
            'mobile_operations_kit' => [
                'hardware' => [
                    'rugged_laptop' => 'Panasonic Toughbook or equivalent',
                    'mobile_printer' => 'Portable thermal printer',
                    'barcode_scanner' => 'Handheld barcode scanner',
                    'mobile_phone' => 'Satellite-capable smartphone',
                    'power_bank' => '20,000mAh portable power bank',
                    'solar_charger' => 'Portable solar panel charger'
                ],
                'software' => [
                    'offline_application' => 'Mobile SPPG app with offline capability',
                    'local_database' => 'SQLite database for offline operations',
                    'sync_mechanism' => 'Automatic sync when connectivity restored',
                    'backup_system' => 'Local backup to USB drive'
                ],
                'supplies' => [
                    'paper_forms' => 'Pre-printed SPPG forms (100 copies)',
                    'pens' => 'Waterproof pens (20 units)',
                    'documentation' => 'Emergency procedures manual',
                    'contact_list' => 'Emergency contact list (laminated)'
                ]
            ],
            'mobile_operations_procedures' => [
                'deployment_time' => '30 minutes to be fully operational',
                'battery_life' => '8-12 hours with power banks',
                'data_capacity' => 'Up to 1,000 transactions offline',
                'sync_capability' => 'Automatic sync when connectivity available',
                'weather_resistance' => 'IP65 rating for all equipment'
            ]
        ];
    }
    
    public static function developManualWorkarounds()
    {
        return [
            'manual_processes' => [
                'order_processing' => [
                    'description' => 'Manual order processing using paper forms',
                    'steps' => [
                        '1. Fill out paper order form',
                        '2. Calculate totals manually',
                        '3. Get customer signature',
                        '4. Store form in secure location',
                        '5. Enter into system when available'
                    ],
                    'tools_required' => ['Order forms', 'Calculator', 'Pen', 'Storage box'],
                    'time_per_transaction' => '10-15 minutes',
                    'accuracy_rate' => '95% (with verification)'
                ],
                'inventory_management' => [
                    'description' => 'Manual inventory tracking using paper logs',
                    'steps' => [
                        '1. Check physical inventory',
                        '2. Update inventory log',
                        '3. Note discrepancies',
                        '4. Take photos if possible',
                        '5. Update system when available'
                    ],
                    'tools_required' => ['Inventory log sheets', 'Pen', 'Camera phone'],
                    'time_per_check' => '5-10 minutes',
                    'accuracy_rate' => '90% (with double-check)'
                ],
                'financial_transactions' => [
                    'description' => 'Manual financial recording using ledgers',
                    'steps' => [
                        '1. Record transaction in ledger',
                        '2. Calculate totals',
                        '3. Get receipt signature',
                        '4. Store receipt securely',
                        '5. Reconcile when system available'
                    ],
                    'tools_required' => ['Ledger book', 'Calculator', 'Receipt book'],
                    'time_per_transaction' => '8-12 minutes',
                    'accuracy_rate' => '98% (with verification)'
                ]
            ],
            'quality_control' => [
                'verification_procedures' => 'Double-check all entries',
                'error_correction' => 'Cross-reference with physical documents',
                'audit_trail' => 'Maintain paper trail for all transactions',
                'backup_procedures' => 'Photograph all documents'
            ]
        ];
    }
}
```

#### 12.8.6 Testing & Maintenance
**Regular BCDR Testing:**
```php
// app/Console/Commands/BCDRTest.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DisasterRecoveryService;
use App\Services\EmergencyResponseService;
use App\Services\BusinessContinuityService;

class BCDRTest extends Command
{
    protected $signature = 'bcdr:test {type}';
    protected $description = 'Run BCDR tests and drills';
    
    public function handle()
    {
        $type = $this->argument('type');
        
        switch ($type) {
            case 'dr_test':
                return $this->runDisasterRecoveryTest();
            case 'emergency_drill':
                return $this->runEmergencyDrill();
            case 'continuity_test':
                return $this->runContinuityTest();
            case 'full_simulation':
                return $this->runFullSimulation();
            default:
                $this->error('Invalid test type');
                return 1;
        }
    }
    
    private function runDisasterRecoveryTest()
    {
        $this->info('Running Disaster Recovery Test...');
        
        // Test DR site activation
        $this->line('Testing DR site activation...');
        $drResult = DisasterRecoveryService::activateDRSite();
        
        if ($drResult['success']) {
            $this->info('✓ DR site activation successful');
        } else {
            $this->error('✗ DR site activation failed');
        }
        
        // Test backup restoration
        $this->line('Testing backup restoration...');
        $backupResult = DisasterRecoveryService::restoreFromBackup();
        
        if ($backupResult['success']) {
            $this->info('✓ Backup restoration successful');
        } else {
            $this->error('✗ Backup restoration failed');
        }
        
        // Test offline mode
        $this->line('Testing offline mode...');
        $offlineResult = DisasterRecoveryService::enableOfflineMode();
        
        if ($offlineResult['success']) {
            $this->info('✓ Offline mode activation successful');
        } else {
            $this->error('✗ Offline mode activation failed');
        }
        
        // Generate test report
        $this->generateTestReport('disaster_recovery', [
            'dr_site' => $drResult,
            'backup_restoration' => $backupResult,
            'offline_mode' => $offlineResult
        ]);
        
        return 0;
    }
    
    private function runEmergencyDrill()
    {
        $this->info('Running Emergency Drill...');
        
        // Simulate emergency
        $this->line('Simulating data center failure...');
        $emergencyResult = EmergencyResponseService::handleEmergency('data_center_failure', 'high');
        
        if ($emergencyResult['status'] === 'emergency_active') {
            $this->info('✓ Emergency response activated');
        } else {
            $this->error('✗ Emergency response failed');
        }
        
        // Test notification system
        $this->line('Testing notification system...');
        $notificationResult = $this->testNotificationSystem();
        
        if ($notificationResult) {
            $this->info('✓ Notification system working');
        } else {
            $this->error('✗ Notification system failed');
        }
        
        // Test communication protocols
        $this->line('Testing communication protocols...');
        $communicationResult = $this->testCommunicationProtocols();
        
        if ($communicationResult) {
            $this->info('✓ Communication protocols working');
        } else {
            $this->error('✗ Communication protocols failed');
        }
        
        return 0;
    }
    
    private function runFullSimulation()
    {
        $this->info('Running Full BCDR Simulation...');
        
        // Simulate complete disaster scenario
        $scenarios = ['data_center_failure', 'network_outage', 'power_failure'];
        
        foreach ($scenarios as $scenario) {
            $this->line("Simulating {$scenario}...");
            
            $result = DisasterRecoveryService::initiateDisasterRecovery($scenario);
            
            if ($result['status'] !== 'failed') {
                $this->info("✓ {$scenario} simulation successful");
            } else {
                $this->error("✗ {$scenario} simulation failed");
            }
        }
        
        return 0;
    }
    
    private function generateTestReport($testType, $results)
    {
        $report = [
            'test_type' => $testType,
            'test_date' => now(),
            'results' => $results,
            'overall_status' => $this->calculateOverallStatus($results),
            'recommendations' => $this->generateRecommendations($results)
        ];
        
        // Save report
        file_put_contents(
            storage_path("reports/bcdr_{$testType}_" . now()->format('Y-m-d_H-i-s') . ".json"),
            json_encode($report, JSON_PRETTY_PRINT)
        );
        
        $this->info("Test report saved: {$report['overall_status']}");
        
        return $report;
    }
}
```

### 12.8.7 Benefits of BCDR System

#### **Business Protection Benefits:**
- **Continuous Operations:** Maintain business functions during disasters
- **Minimized Downtime:** Quick recovery with predefined procedures
- **Data Protection:** Comprehensive backup and recovery systems
- **Customer Service:** Maintain customer communication during crises

#### **Financial Benefits:**
- **Risk Mitigation:** Reduce financial impact of disasters
- **Insurance Benefits:** Lower premiums with proper BCDR planning
- **Revenue Protection:** Minimize revenue loss during outages
- **Cost Control:** Predictable disaster recovery costs

#### **Operational Benefits:**
- **Flexibility:** Multiple recovery options for different scenarios
- **Scalability:** Adaptable to business growth
- **Compliance:** Meet regulatory requirements for business continuity
- **Confidence:** Stakeholder confidence in business resilience

**Dengan BCDR system yang komprehensif ini, aplikasi SPPG Anda akan siap menghadapi berbagai skenario bencana dan kecelakaan, memastikan operasional bisnis tetap berjalan dengan minimal gangguan!**
**PHP Approach (Recommended untuk Anda):**
- **Framework:** Laravel 10+ dengan Blade templates
- **UI Framework:** Bootstrap 5 + Alpine.js
- **JavaScript:** Vanilla JS atau jQuery
- **CSS:** Tailwind CSS (Laravel Mix)
- **Build Tools:** Vite untuk fast development
- **Mobile:** Progressive Web App (PWA)
- API untuk submit laporan
- Status synchronization
- Document exchange
- Compliance validation
- Performance reporting

**Tax System Integration:**
- E-filing API (DJP Online)
- Tax calculation services
- Invoice validation
- NPWP verification
- Tax report generation

#### 9.1.2 Financial Systems
**Banking Integration:**
- Payment gateway
- Account verification
- Transaction status
- Reconciliation
- Cash management

**Accounting Software:**
- Export to accounting systems
- Chart of accounts sync
- Journal entry automation
- Financial report integration
- Tax preparation data

#### 9.1.3 Logistics & Tracking
**GPS Tracking:**
- Real-time vehicle tracking
- Route optimization
- Geofencing
- Delivery confirmation
- Performance analytics

**Communication Systems:**
- SMS gateway for notifications
- Email integration
- WhatsApp business API
- Push notifications
- Customer communication

### 9.2 Internal Integrations

#### 9.2.1 Third-party Services
**Cloud Storage:**
- Document backup
- File sharing
- CDN for assets
- Disaster recovery
- Data archival

**Analytics & BI:**
- Business intelligence tools
- Data visualization
- Predictive analytics
- Performance dashboards
- Custom reporting

---

## 🛣️ IMPLEMENTATION ROADMAP

### 10.1 Phase 1: Foundation (Month 1-2)

#### Sprint 1: Core Infrastructure
**Week 1-2:**
- [ ] Setup development environment
- [ ] Database design & setup
- [ ] Basic authentication system
- [ ] User management module
- [ ] Basic UI framework

**Week 3-4:**
- [ ] SPPG basic CRUD operations
- [ ] Institution management
- [ ] Document upload system
- [ ] Basic approval workflow
- [ ] Dashboard foundation

#### Sprint 2: Core Features
**Week 5-6:**
- [ ] Complete SPPG workflow
- [ ] Inventory basic management
- [ ] Product catalog
- [ ] Supplier management
- [ ] Basic reporting

**Week 7-8:**
- [ ] Quality control module
- [ ] Basic financial tracking
- [ ] Notification system
- [ ] Mobile responsiveness
- [ ] Testing & QA

### 10.2 Phase 2: Advanced Features (Month 3-4)

#### Sprint 3: Operations
**Week 9-10:**
- [ ] Advanced inventory management
- [ ] Warehouse operations
- [ ] Order fulfillment
- [ ] Basic logistics tracking
- [ ] Route planning

**Week 11-12:**
- [ ] Delivery management
- [ ] Proof of delivery
- [ ] Driver management
- [ ] Vehicle tracking
- [ ] Performance analytics

#### Sprint 4: Financial & Compliance
**Week 13-14:**
- [ ] Complete financial module
- [ ] Invoice generation
- [ ] Payment processing
- [ ] Tax calculation
- [ ] Budget management

**Week 15-16:**
- [ ] Compliance checking
- [ ] Audit trail system
- [ ] Regulatory reporting
- [ ] Document management
- [ ] Advanced security

### 10.3 Phase 3: Integration & Optimization (Month 5-6)

#### Sprint 5: External Integration
**Week 17-18:**
- [ ] BGN API integration
- [ ] Tax system integration
- [ ] Banking integration
- [ ] Email/SMS gateway
- [ ] Cloud storage setup

**Week 19-20:**
- [ ] GPS tracking integration
- [ ] Accounting software sync
- [ ] Advanced analytics
- [ ] Business intelligence
- [ ] Performance optimization

#### Sprint 6: Advanced Features
**Week 21-22:**
- [ ] Mobile app development
- [ ] Advanced reporting
- [ ] Predictive analytics
- [ ] AI-powered recommendations
- [ ] Automation features

**Week 23-24:**
- [ ] Load testing
- [ ] Security audit
- [ ] User training
- [ ] Documentation
- [ ] Production deployment

### 10.4 Phase 4: Maintenance & Enhancement (Ongoing)

#### Monthly Activities:
- [ ] Performance monitoring
- [ ] Security updates
- [ ] User feedback collection
- [ ] Bug fixes
- [ ] Feature enhancements

#### Quarterly Activities:
- [ ] System optimization
- [ ] Compliance updates
- [ ] User training
- [ ] Backup verification
- [ ] Disaster recovery testing

---

## 📊 FINANCIAL METRICS & KPIS

### 11.1 Capital Structure Metrics

#### 11.1.1 Modal & Equity Analysis
**Key Metrics:**
- **Total Modal Awal:** Jumlah investasi awal pemilik
- **Modal Kerja:** Current assets - Current liabilities
- **Debt to Equity Ratio:** Total liabilities / Total equity
- **Equity Ratio:** Total equity / Total assets
- **Retained Earnings:** Accumulated profits retained in business

**Formulas:**
```
Modal Kerja = Aktiva Lancar - Kewajiban Lancar
Debt to Equity = Total Utang / Total Ekuitas
Equity Ratio = Total Ekuitas / Total Aktiva
```

#### 11.1.2 Capital Performance
**Key Metrics:**
- **ROE (Return on Equity):** Net Income / Total Equity
- **ROI (Return on Investment):** Net Profit / Total Investment
- **ROA (Return on Assets):** Net Income / Total Assets
- **Capital Turnover:** Revenue / Total Capital

### 11.2 Profitability Analysis

#### 11.2.1 Revenue Metrics
**Key Metrics:**
- **Total Revenue:** SPPG + Regular sales
- **Revenue Growth:** (Current Revenue - Previous Revenue) / Previous Revenue
- **Revenue per Customer:** Total Revenue / Number of Customers
- **Revenue per Employee:** Total Revenue / Number of Employees
- **Revenue by Product:** Revenue breakdown per product category

#### 11.2.2 Profit Metrics
**Key Metrics:**
- **Gross Profit:** Revenue - COGS
- **Gross Margin:** Gross Profit / Revenue
- **Operating Profit:** Gross Profit - Operating Expenses
- **Operating Margin:** Operating Profit / Revenue
- **Net Profit:** Operating Profit - Taxes - Interest
- **Net Margin:** Net Profit / Revenue

**Formulas:**
```
Gross Margin = (Laba Kotor / Pendapatan) × 100%
Operating Margin = (Laba Operasional / Pendapatan) × 100%
Net Margin = (Laba Bersih / Pendapatan) × 100%
```

#### 11.2.3 Cost Analysis
**Key Metrics:**
- **COGS Ratio:** COGS / Revenue
- **Operating Expense Ratio:** Operating Expenses / Revenue
- **Cost per Unit:** Total Cost / Number of Units
- **Variable Cost per Unit:** Variable Costs / Number of Units
- **Fixed Costs:** Total costs that don't change with volume

### 11.3 Efficiency Metrics

#### 11.3.1 Asset Efficiency
**Key Metrics:**
- **Asset Turnover:** Revenue / Total Assets
- **Inventory Turnover:** COGS / Average Inventory
- **Receivable Turnover:** Revenue / Average Receivables
- **Payable Turnover:** COGS / Average Payables

#### 11.3.2 Operational Efficiency
**Key Metrics:**
- **Operating Cycle:** Inventory Days + Receivable Days
- **Cash Conversion Cycle:** Operating Cycle - Payable Days
- **Days Sales Outstanding (DSO):** (Receivables / Revenue) × 365
- **Days Inventory Outstanding (DIO):** (Inventory / COGS) × 365

### 11.4 Liquidity & Solvency

#### 11.4.1 Liquidity Metrics
**Key Metrics:**
- **Current Ratio:** Current Assets / Current Liabilities
- **Quick Ratio:** (Current Assets - Inventory) / Current Liabilities
- **Cash Ratio:** Cash & Equivalents / Current Liabilities
- **Working Capital:** Current Assets - Current Liabilities

#### 11.4.2 Solvency Metrics
**Key Metrics:**
- **Debt to Assets:** Total Debt / Total Assets
- **Interest Coverage Ratio:** EBIT / Interest Expense
- **Debt Service Coverage Ratio:** Operating Income / Debt Service
- **Solvency Ratio:** Net Worth / Total Assets

### 11.5 Cash Flow Metrics

#### 11.5.1 Cash Flow Analysis
**Key Metrics:**
- **Operating Cash Flow:** Cash from operations
- **Free Cash Flow:** Operating Cash Flow - Capital Expenditures
- **Cash Flow from Investing:** Cash used for investments
- **Cash Flow from Financing:** Cash from/repaid to investors

#### 11.5.2 Cash Quality Metrics
**Key Metrics:**
- **Cash Conversion Ratio:** Operating Cash Flow / Net Income
- **Cash Flow Adequacy:** Operating Cash Flow / Total Debt
- **Cash Burn Rate:** Monthly cash burn for startups
- **Cash Runway:** Current cash / Monthly burn rate

### 11.6 Investment & Return Metrics

#### 11.6.1 Investment Performance
**Key Metrics:**
- **ROI Percentage:** (Gain from Investment - Cost) / Cost
- **Payback Period:** Initial Investment / Annual Cash Flow
- **Net Present Value (NPV):** Present value of future cash flows
- **Internal Rate of Return (IRR):** Discount rate that makes NPV = 0

#### 11.6.2 Portfolio Analysis
**Key Metrics:**
- **Investment Diversity:** Revenue breakdown by investment type
- **Risk-Adjusted Return:** Return adjusted for risk level
- **Alpha & Beta:** Performance relative to market
- **Sharpe Ratio:** Risk-adjusted return

### 11.7 Budget & Variance Analysis

#### 11.7.1 Budget Performance
**Key Metrics:**
- **Budget Variance:** Actual - Budget
- **Variance Percentage:** (Actual - Budget) / Budget
- **Flexible Budget Variance:** Actual - Flexible Budget
- **Price Variance:** (Actual Price - Standard Price) × Actual Quantity
- **Quantity Variance:** (Actual Quantity - Standard Quantity) × Standard Price

#### 11.7.2 Forecast Accuracy
**Key Metrics:**
- **Forecast Error:** Actual - Forecast
- **Mean Absolute Percentage Error (MAPE):** Average of absolute percentage errors
- **Forecast Bias:** Tendency to over/under forecast
- **Tracking Signal:** Forecast accuracy measurement

### 11.8 Risk Metrics

#### 11.8.1 Credit Risk
**Key Metrics:**
- **Credit Utilization:** Current Exposure / Credit Limit
- **Days Past Due:** Average days invoices are past due
- **Bad Debt Ratio:** Bad Debts / Total Receivables
- **Customer Risk Score:** Composite risk rating per customer

#### 11.8.2 Financial Risk
**Key Metrics:**
- **Leverage Ratio:** Total Debt / Total Equity
- **Interest Coverage:** EBIT / Interest Expense
- **Operating Leverage:** % Change in EBIT / % Change in Sales
- **Financial Leverage:** % Change in EPS / % Change in EBIT

### 11.9 Industry Benchmarks

#### 11.9.1 Distribution Industry Standards
**Typical Ratios:**
- **Gross Margin:** 15-25%
- **Net Margin:** 3-8%
- **Inventory Turnover:** 8-12 times/year
- **Current Ratio:** 1.5-2.5
- **Debt to Equity:** 0.5-1.5

#### 11.9.2 Performance Comparison
**Key Metrics:**
- **Industry Ranking:** Performance vs industry average
- **Competitive Analysis:** Market position
- **Best Practices:** Gap analysis vs top performers
- **Improvement Targets:** Specific, measurable goals

### 11.10 Dashboard Integration

#### 11.10.1 Real-Time Financial Dashboard
**Components:**
- **Cash Position:** Current bank balances
- **Daily Revenue:** Today's sales performance
- **Profit Margin:** Real-time profitability
- **Expense Tracking:** Monthly vs budget
- **KPI Alerts:** Automatic notifications for thresholds

#### 11.10.2 Executive Summary
**Reports:**
- **Monthly Financial Summary:** Key metrics overview
- **Quarterly Business Review:** Detailed performance analysis
- **Annual Report:** Comprehensive financial statements
- **Investor Reports:** ROI and performance metrics

---

## �🇩 LOCALIZATION & INDONESIAN STANDARDS

### 12.1 Bahasa & Interface

#### 12.1.1 Bahasa Indonesia
**Interface Language:**
- **Primary:** Bahasa Indonesia (Bahasa Resmi)
- **Secondary:** English (Optional)
- **Date/Time:** Format Indonesia
- **Number:** Format Indonesia
- **Currency:** Rupiah (IDR)

**Terminology Indonesia:**
- **Customer:** Pelanggan
- **Supplier:** Pemasok/Pemasok
- **Invoice:** Faktur/Invoice
- **Purchase Order:** Pesanan Pembelian
- **Sales Order:** Pesanan Penjualan
- **Warehouse:** Gudang
- **Inventory:** Persediaan/Stok
- **Profit & Loss:** Laba Rugi
- **Balance Sheet:** Neraca
- **Cash Flow:** Arus Kas
- **Payroll:** Penggajian
- **Commission:** Komisi

#### 12.1.2 Format Tanggal & Waktu
**Standar Indonesia:**
- **Format Tanggal:** DD/MM/YYYY (16/01/2026)
- **Format Panjang:** 16 Januari 2026
- **Format Bulan-Tahun:** Januari 2026
- **Format Quarter:** Q1 2026
- **Format Time:** HH:MM (24 jam)
- **Format DateTime:** DD/MM/YYYY HH:MM

**Hari Libur Indonesia:**
- **Tahun Baru:** 1 Januari
- **Idul Fitri:** Variable (berdasarkan kalender hijriah)
- **Idul Adha:** Variable (berdasarkan kalender hijriah)
- **Kemerdekaan:** 17 Agustus
- **Natal:** 25 Desember
- **Cuti Bersama:** Sesuai SKB Menaker

### 12.2 Format Nominal & Mata Uang

#### 12.2.1 Format Rupiah
**Standar Indonesia:**
- **Symbol:** Rp (di depan)
- **Separator:** Titik untuk ribuan, koma untuk desimal
- **Format:** Rp 1.234.567,89
- **Tanpa Desimal:** Rp 1.234.568
- **Singkat:** Rp 1,2 jt (juta), Rp 1,2 m (miliar)

**Contoh Format:**
```
Rp 1.234.567,89
Rp 12.345.678
Rp 123.456.789
Rp 1.234.567.890
```

#### 12.2.2 Format Nomor Dokumen
**Standar Indonesia:**
- **Invoice:** INV/2026/01/001
- **Purchase Order:** PO/2026/01/001
- **Sales Order:** SO/2026/01/001
- **Delivery Order:** DO/2026/01/001
- **Receipt:** RCP/2026/01/001
- **Payment:** PAY/2026/01/001

**Struktur Nomor:**
```
[Kode]/[Tahun]/[Bulan]/[Urutan]
INV/2026/01/001 = Invoice Januari 2026 nomor 1
PO/2026/12/045 = Purchase Order Desember 2026 nomor 45
```

### 12.3 Standar Akuntansi Indonesia

#### 12.3.1 PSAK Indonesia
**Standar PSAK (IFRS):**
- **PSAK 1:** Penyajian Laporan Keuangan
- **PSAK 2:** Persediaan
- **PSAK 7:** Aset Tetap
- **PSAK 11:** Aset Tidak Berwujud
- **PSAK 14:** Investasi Associate
- **PSAK 15:** Investasi dalam Entitas Asosiasi
- **PSAK 23:** Pendapatan
- **PSAK 33:** Beban Pinjaman

#### 12.3.2 Chart of Accounts Indonesia
**Struktur Akun Standar:**
```
1 AKTIVA
  1.1 Aktiva Lancar
    1.1.1 Kas dan Bank
    1.1.2 Piutang Usaha
    1.1.3 Persediaan
    1.1.4 Biaya Dibayar di Muka
  1.2 Aktiva Tidak Lancar
    1.2.1 Piutang Jangka Panjang
    1.2.2 Investasi
    1.2.3 Aset Tetap
    1.2.4 Aset Tidak Berwujud
2 KEWAJIBAN
  2.1 Kewajiban Lancar
    2.1.1 Utang Usaha
    2.1.2 Utang Pajak
    2.1.3 Utang Jangka Panjang
  2.2 Kewajiban Jangka Panjang
3 EKUITAS
  3.1 Modal Saham
  3.2 Tambahan Modal Disetor
  3.3 Saldo Laba
  3.4 Cadangan
4 PENDAPATAN
  4.1 Pendapatan Usaha
  4.2 Pendapatan Luar Usaha
5 BEBAN USAHA
  5.1 Harga Pokok Penjualan
  5.2 Beban Usaha
  5.3 Beban Penjualan
  5.4 Beban Administrasi & Umum
  5.5 Beban Penyusutan
  5.6 Beban Pajak
```

### 12.4 Perpajakan Indonesia

#### 12.4.1 Jenis Pajak
**PPN (Pajak Pertambahan Nilai):**
- **Tarif:** 11% (umum)
- **PPN Terutang:** Output Pajak - Input Pajak
- **Faktur Pajak:** Standar format e-Faktur
- **Kode Pajak:** Sesuai KLU (Klasifikasi Lapangan Usaha)

**PPh 21 (Pajak Penghasilan Pasal 21):**
- **Tarif Progressif:** 5% - 35%
- **PTKP:** PPh Final Terutang (1.5% untuk usaha kecil)
- **Bukti Potong:** Bukti Pemotong PPh 21
- **SPT Masa:** Pelaporan bulanan

**PPh 23 (Pajak Penghasilan Pasal 23):**
- **Tarif:** 15% untuk dividen, bunga, royalti
- **Bukti Potong:** Bukti Pemotong PPh 23
- **Pelaporan:** SPT Masa PPh 23

#### 12.4.2 Kode Transaksi
**Kode Transaksi Bank Indonesia:**
- **Transfer:** 001 (Transfer Kredit)
- **Cash Withdrawal:** 002 (Penarikan Tunai)
- **Deposit:** 003 (Setoran Tunai)
- **Payment:** 004 (Pembayaran)
- **Collection:** 005 (Penerimaan)

### 12.5 Format Laporan Keuangan

#### 12.5.1 Format Standar Bapepam
**Neraca (Balance Sheet):**
```
PT. DISTRIBUTION MAKMURAN
NERACA PER 31 DESEMBER 2026
(Dalam Rupiah)

AKTIVA
Aktiva Lancar
  Kas dan Bank                          Rp 123.456.789
  Piutang Usaha                        Rp 234.567.890
  Persediaan                           Rp 345.678.901
  Jumlah Aktiva Lancar                Rp 703.703.580

Aktiva Tidak Lancar
  Aset Tetap                          Rp 456.789.012
  Akumulasi Penyusutan             (Rp 123.456.789)
  Aset Tetap Bersih                  Rp 333.332.223
  Jumlah Aktiva Tidak Lancar       Rp 333.332.223

TOTAL AKTIVA                              Rp 1.037.035.803

KEWAJIBAN
Kewajiban Lancar
  Utang Usaha                         Rp 234.567.890
  Utang Pajak                         Rp 45.678.901
  Jumlah Kewajiban Lancar          Rp 280.246.791

Kewajiban Jangka Panjang
  Utang Bank                           Rp 123.456.789
  Jumlah Kewajiban Jangka Panjang Rp 123.456.789

TOTAL KEWAJIBAN                         Rp 403.703.580

EKUITAS
Modal Saham                           Rp 500.000.000
Tambahan Modal Disetor              Rp 100.000.000
Saldo Laba                           Rp 33.332.223
TOTAL EKUITAS                         Rp 633.332.223

TOTAL KEWAJIBAN & EKUITAS           Rp 1.037.035.803
```

#### 12.5.2 Format Laporan Laba Rugi
```
PT. DISTRIBUTION MAKMURAN
LAPORAN LABA RUGI
Untuk Tahun yang Berakhir 31 Desember 2026
(Dalam Rupiah)

PENDAPATAN USAHA
  Penjualan Bersih                           Rp 2.345.678.901
  Pendapatan Jasa                           Rp 123.456.789
  Total Pendapatan                           Rp 2.469.135.690

HARGA POKOK PENJUALAN
  Persediaan Awal                           Rp 456.789.012
  Pembelian                                  Rp 1.234.567.890
  Persediaan Akhir                         (Rp 345.678.901)
  Harga Pokok Penjualan                    Rp 1.345.678.001
  Laba Kotor                               Rp 1.123.457.689

BEBAN USAHA
  Beban Penjualan                           Rp 234.567.890
  Beban Administrasi & Umum                Rp 345.678.901
  Beban Penyusutan                         Rp 123.456.789
  Total Beban USAHA                        Rp 703.703.580

LABA USAHA                               Rp 419.754.109

PENDAPATAN & BEBAN LUAR USAHA
  Pendapatan Bunga                         Rp 12.345.678
  Beban Bunga                              (Rp 23.456.789)
  Laba Sebelum Pajak                       Rp 408.643. - -

BEBAN PAJAK
  PPh Badan                                Rp 81.728.639
  LABA BERSIH TAHUN BERJALAN             Rp 326.914.441
```

### 12.6 Format Dokumen Elektronik

#### 12.6.1 e-Faktur Standar
**Format e-Faktur Pajak:**
- **Nomor:** Sesuai format e-Faktur
- **QR Code:** Untuk validasi DJP
- **Digital Signature:** Tanda tangan elektronik
- **Tax Invoice:** Format standar Direktorat Jenderal Pajak

#### 12.6.2 e-SPT Format
**Format Pelaporan Pajak:**
- **e-SPT PPh 21:** Pelaporan bulanan
- **e-SPT PPN:** Pelaporan bulanan
- **e-Filing:** Pelaporan online
- **Kode Billing:** Kode billing pembayaran pajak

### 12.7 Regional Settings

#### 12.7.1 Zona Waktu Indonesia
**Time Zones:**
- **WIB (Waktu Indonesia Barat):** GMT+7 (Jakarta, Bandung, Surabaya)
- **WITA (Waktu Indonesia Tengah):** GMT+8 (Bali, Nusa Tenggara)
- **WIT (Waktu Indonesia Timur):** GMT+9 (Papua, Maluku)

#### 12.7.2 Format Lokal
**Regional Preferences:**
- **Nama Perusahaan:** Format Indonesia
- **Alamat:** Format Indonesia
- **Nomor Telepon:** Format +62
- **Email:** Domain Indonesia (opsional)
- **Website:** Domain .id (opsional)

### 12.8 Compliance & Regulatory

#### 12.8.1 Standar Regulasi
**OJK (Otoritas Jasa Keuangan):**
- Laporan keuangan perusahaan
- Compliance reporting
- Anti-money laundering (AML)
- Consumer protection

**Bank Indonesia:**
- Laporan transaksi valas
- Kode transaksi bank
- Standar pembayaran digital

**Kementerian Keuangan:**
- Standar akuntansi pemerintah
- Peraturan perpajakan
- Laporan statistik

#### 12.8.2 Audit Requirements
**Standar Audit Indonesia:**
- **Audit Internal:** Bulanan/kuartalan
- **Audit Eksternal:** Tahunan
- **Audit Compliance:** Sesuai peraturan
- **Audit Report:** Format standar Indonesia

### 12.9 Implementation Guidelines

#### 12.9.1 Development Considerations
**Technical Requirements:**
- **Unicode Support:** UTF-8 untuk Bahasa Indonesia
- **Number Formatting:** Library format Indonesia
- **Date/Time Library:** Support kalender Indonesia
- **Currency Library:** Format Rupiah
- **Tax Engine:** Rules perpajakan Indonesia

#### 12.9.2 User Experience
**UX Considerations:**
- **Language Toggle:** Mudah switch Bahasa/English
- **Format Input:** Otomatis format Indonesia
- **Error Messages:** Dalam Bahasa Indonesia
- **Help Documentation:** Dalam Bahasa Indonesia
- **Training Materials:** Dalam Bahasa Indonesia

Dengan implementasi standar Indonesia ini, aplikasi akan:
✅ **Compliant** dengan regulasi Indonesia  
✅ **User-Friendly** untuk pengguna Indonesia  
✅ **Professional** dengan format laporan standar  
✅ **Complete** dengan fitur perpajakan Indonesia  
✅ **Scalable** untuk pertumbuhan bisnis lokal

Dokumen perencanaan ini menyediakan framework komprehensif untuk pengembangan aplikasi SPPG Distribution System yang:

1. **Compliant:** Memenuhi semua regulasi BGN dan standar makanan bergizi
2. **Comprehensive:** Menutup semua aspek operasional bisnis distribusi
3. **Scalable:** Dapat dikembangkan sesuai pertumbuhan bisnis
4. **Efficient:** Mengotomasi proses manual dan meningkatkan produktivitas
5. **Transparent:** Menyediakan audit trail dan pelaporan lengkap
6. **User-Friendly:** Interface yang intuitif untuk semua level pengguna

Implementasi sistem ini akan memastikan:
- ✅ Kelancaran operasional distribusi MBG
- ✅ Kepatuhan terhadap regulasi BGN
- ✅ Efisiensi biaya dan waktu
- ✅ Akuntansi yang terstruktur dan akurat
- ✅ Monitoring real-time untuk manajemen
- ✅ Kolaborasi tim yang efektif

**Next Steps:**
1. Review dan approve perencanaan ini
2. Setup development team
3. Mulai Phase 1 implementation
4. Regular progress reviews
5. Continuous improvement based on feedback

---

### 12.10 Advanced Future-Ready Features

#### 12.10.1 AI-Powered Intelligence System
**Smart Business Intelligence & Automation:**
```
AI INTELLIGENCE ARCHITECTURE:
├── Predictive Analytics Engine
│   ├── Demand Forecasting (ML models)
│   ├── Inventory Optimization
│   ├── Price Optimization
│   └── Customer Behavior Analysis
├── Intelligent Automation
│   ├── Smart Order Processing
│   ├── Automated Decision Making
│   ├── Intelligent Routing
│   └── Smart Resource Allocation
├── Natural Language Processing
│   ├── Voice Commands (Indonesian)
│   ├── Document Processing (OCR)
│   ├── Chatbot Assistant
│   └── Sentiment Analysis
├── Computer Vision
│   ├── Barcode/QR Recognition
│   ├── Document Verification
│   ├── Quality Control Imaging
│   └── Security Monitoring
└── Machine Learning Operations
    ├── Model Training Pipeline
    ├── Performance Monitoring
    ├── A/B Testing Framework
    └── Continuous Model Improvement
```

**AI Implementation Examples:**
```php
// app/Services/AIIntelligenceService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AIIntelligenceService
{
    public static function predictDemand($productId, $timeframe = 30)
    {
        // ML-based demand forecasting
        $historicalData = self::getHistoricalSalesData($productId, 90);
        $marketFactors = self::getMarketFactors();
        $seasonalPatterns = self::getSeasonalPatterns($productId);
        
        $prediction = self::runMLModel('demand_forecasting', [
            'historical_data' => $historicalData,
            'market_factors' => $marketFactors,
            'seasonal_patterns' => $seasonalPatterns,
            'timeframe' => $timeframe
        ]);
        
        return [
            'predicted_demand' => $prediction['demand'],
            'confidence_level' => $prediction['confidence'],
            'recommended_stock' => $prediction['recommended_stock'],
            'risk_factors' => $prediction['risk_factors'],
            'optimal_pricing' => $prediction['optimal_price']
        ];
    }
    
    public static function optimizeInventory($warehouseId)
    {
        // AI-powered inventory optimization
        $currentInventory = self::getCurrentInventory($warehouseId);
        $demandPredictions = self::getDemandPredictions($warehouseId);
        $supplierLeadTimes = self::getSupplierLeadTimes();
        $storageConstraints = self::getStorageConstraints($warehouseId);
        
        $optimization = self::runMLModel('inventory_optimization', [
            'current_inventory' => $currentInventory,
            'demand_predictions' => $demandPredictions,
            'supplier_lead_times' => $supplierLeadTimes,
            'storage_constraints' => $storageConstraints
        ]);
        
        return [
            'reorder_points' => $optimization['reorder_points'],
            'economic_order_quantities' => $optimization['eoq'],
            'safety_stock_levels' => $optimization['safety_stock'],
            'storage_optimization' => $optimization['storage_layout'],
            'cost_savings' => $optimization['projected_savings']
        ];
    }
    
    public static function processNaturalLanguage($input, $context = 'general')
    {
        // NLP for Indonesian language processing
        $nlpResponse = Http::post(config('ai.nlp_endpoint'), [
            'text' => $input,
            'language' => 'id',
            'context' => $context,
            'features' => ['intent_detection', 'entity_extraction', 'sentiment_analysis']
        ]);
        
        return [
            'intent' => $nlpResponse['intent'],
            'entities' => $nlpResponse['entities'],
            'sentiment' => $nlpResponse['sentiment'],
            'confidence' => $nlpResponse['confidence'],
            'suggested_action' => $nlpResponse['suggested_action']
        ];
    }
    
    public static function analyzeDocument($documentPath)
    {
        // Computer vision for document processing
        $analysis = Http::post(config('ai.vision_endpoint'), [
            'image_path' => $documentPath,
            'features' => [
                'ocr_extraction',
                'document_classification',
                'signature_detection',
                'data_validation'
            ]
        ]);
        
        return [
            'extracted_text' => $analysis['ocr_text'],
            'document_type' => $analysis['classification'],
            'signatures_detected' => $analysis['signatures'],
            'data_fields' => $analysis['extracted_fields'],
            'confidence_score' => $analysis['confidence'],
            'validation_results' => $analysis['validation']
        ];
    }
}
```

#### 12.10.2 Blockchain & Supply Chain Transparency
**Distributed Ledger Technology Integration:**
```
BLOCKCHAIN ARCHITECTURE:
├── Smart Contracts Layer
│   ├── Supply Chain Contracts
│   ├── Payment Processing
│   ├── Quality Assurance
│   └── Compliance Verification
├── Distributed Ledger
│   ├── Transaction Recording
│   ├── Product Provenance
│   ├── Audit Trail
│   └── Immutable Records
├── Tokenization System
│   ├── Asset Tokenization
│   ├── Loyalty Points
│   ├── Supply Chain Credits
│   └── Digital Certificates
├── Integration Layer
│   ├── ERP Integration
│   ├── IoT Device Integration
│   ├── Third-party APIs
│   └── Legacy System Bridge
└── Analytics & Reporting
    ├── Real-time Dashboards
    ├── Compliance Reports
    ├── Performance Analytics
    └── Predictive Insights
```

**Blockchain Implementation:**
```php
// app/Services/BlockchainService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class BlockchainService
{
    public static function createSupplyChainTransaction($transactionData)
    {
        // Create immutable supply chain record
        $smartContract = self::getSmartContract('supply_chain');
        
        $transaction = [
            'transaction_id' => uniqid('tx_', true),
            'timestamp' => now()->toISOString(),
            'product_id' => $transactionData['product_id'],
            'supplier_id' => $transactionData['supplier_id'],
            'quantity' => $transactionData['quantity'],
            'quality_score' => $transactionData['quality_score'],
            'certifications' => $transactionData['certifications'],
            'location' => $transactionData['location'],
            'handler' => $transactionData['handler']
        ];
        
        // Execute smart contract
        $result = $smartContract->execute('recordTransaction', $transaction);
        
        if ($result['success']) {
            // Update local database
            self::updateLocalRecord($transaction);
            
            // Notify stakeholders
            self::notifyStakeholders($transaction);
            
            return [
                'success' => true,
                'transaction_hash' => $result['hash'],
                'block_number' => $result['block'],
                'gas_used' => $result['gas'],
                'confirmation_time' => $result['confirmation_time']
            ];
        }
        
        return ['success' => false, 'error' => $result['error']];
    }
    
    public static function verifyProductProvenance($productId)
    {
        // Trace product journey through supply chain
        $provenance = self::queryBlockchain([
            'contract' => 'supply_chain',
            'method' => 'getProductHistory',
            'params' => [$productId]
        ]);
        
        return [
            'product_id' => $productId,
            'supply_chain_journey' => $provenance['journey'],
            'quality_metrics' => $provenance['quality_data'],
            'certifications' => $provenance['certifications'],
            'carbon_footprint' => $provenance['environmental_impact'],
            'verification_hash' => $provenance['verification_hash']
        ];
    }
    
    public static function executeSmartContract($contractName, $method, $params)
    {
        // Execute smart contract with gas optimization
        $contract = self::getSmartContract($contractName);
        
        // Estimate gas before execution
        $gasEstimate = $contract->estimateGas($method, $params);
        
        // Execute with optimal gas price
        $result = $contract->execute($method, $params, [
            'gas_limit' => $gasEstimate * 1.2, // 20% buffer
            'gas_price' => self::getOptimalGasPrice()
        ]);
        
        return $result;
    }
}
```

#### 12.10.3 IoT & Smart Warehouse Integration
**Internet of Things Ecosystem:**
```
IOT ARCHITECTURE:
├── Sensor Network
│   ├── Temperature/Humidity Sensors
│   ├── RFID/Barcode Readers
│   ├── Weight Sensors
│   └── Motion Detectors
├── Edge Computing
│   ├── Local Data Processing
│   ├── Real-time Analytics
│   ├── Predictive Maintenance
│   └── Automated Responses
├── Device Management
│   ├── Device Registration
│   ├── Firmware Updates
│   ├── Health Monitoring
│   └── Security Management
├── Data Integration
│   ├── Real-time Data Stream
│   ├── Historical Data Storage
│   ├── Analytics Pipeline
│   └── Alert System
└── Automation & Control
    ├── Automated Inventory
    ├── Smart Lighting
    ├── Climate Control
    └── Security Systems
```

**IoT Implementation:**
```php
// app/Services/IoTService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class IoTService
{
    public static function processSensorData($sensorData)
    {
        // Process real-time sensor data
        $processedData = [
            'sensor_id' => $sensorData['device_id'],
            'timestamp' => now(),
            'temperature' => $sensorData['temperature'],
            'humidity' => $sensorData['humidity'],
            'location' => $sensorData['location'],
            'alerts' => self::generateAlerts($sensorData)
        ];
        
        // Store in time-series database
        self::storeTimeSeriesData($processedData);
        
        // Trigger automated responses
        if (!empty($processedData['alerts'])) {
            self::triggerAutomatedResponse($processedData['alerts']);
        }
        
        // Update inventory conditions
        self::updateInventoryConditions($processedData);
        
        return $processedData;
    }
    
    private static function generateAlerts($sensorData)
    {
        $alerts = [];
        
        // Temperature alerts
        if ($sensorData['temperature'] > 25) {
            $alerts[] = [
                'type' => 'temperature_high',
                'severity' => 'warning',
                'message' => 'Temperature exceeds optimal range',
                'location' => $sensorData['location'],
                'action_required' => 'activate_cooling'
            ];
        }
        
        // Humidity alerts
        if ($sensorData['humidity'] > 70) {
            $alerts[] = [
                'type' => 'humidity_high',
                'severity' => 'critical',
                'message' => 'Humidity too high - risk of product damage',
                'location' => $sensorData['location'],
                'action_required' => 'activate_dehumidifier'
            ];
        }
        
        return $alerts;
    }
    
    public static function triggerAutomatedResponse($alerts)
    {
        foreach ($alerts as $alert) {
            switch ($alert['action_required']) {
                case 'activate_cooling':
                    self::activateCoolingSystem($alert['location']);
                    break;
                case 'activate_dehumidifier':
                    self::activateDehumidifier($alert['location']);
                    break;
                case 'security_alert':
                    self::triggerSecurityProtocol($alert);
                    break;
            }
            
            // Queue notification
            Queue::push(new \App\Jobs\SendIoTAlert($alert));
        }
    }
    
    public static function predictMaintenance($deviceId)
    {
        // Predictive maintenance using ML
        $deviceHistory = self::getDeviceHistory($deviceId, 90);
        $performanceMetrics = self::getPerformanceMetrics($deviceId);
        
        $prediction = self::runMLModel('predictive_maintenance', [
            'device_history' => $deviceHistory,
            'performance_metrics' => $performanceMetrics,
            'device_type' => self::getDeviceType($deviceId)
        ]);
        
        if ($prediction['maintenance_probability'] > 0.8) {
            // Schedule maintenance
            self::scheduleMaintenance($deviceId, $prediction['recommended_date']);
            
            // Notify maintenance team
            self::notifyMaintenanceTeam([
                'device_id' => $deviceId,
                'probability' => $prediction['maintenance_probability'],
                'recommended_date' => $prediction['recommended_date'],
                'predicted_issues' => $prediction['potential_issues']
            ]);
        }
        
        return $prediction;
    }
}
```

#### 12.10.4 Advanced Analytics & Big Data
**Real-time Analytics Platform:**
```php
// app/Services/BigDataAnalyticsService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class BigDataAnalyticsService
{
    public static function generateRealTimeInsights()
    {
        return [
            'sales_analytics' => self::getSalesAnalytics(),
            'inventory_analytics' => self::getInventoryAnalytics(),
            'customer_analytics' => self::getCustomerAnalytics(),
            'financial_analytics' => self::getFinancialAnalytics(),
            'operational_analytics' => self::getOperationalAnalytics(),
            'predictive_insights' => self::getPredictiveInsights()
        ];
    }
    
    public static function getSalesAnalytics()
    {
        // Real-time sales analytics
        $cacheKey = 'sales_analytics_' . now()->format('Y-m-d-H');
        
        return Cache::remember($cacheKey, 3600, function () {
            return [
                'total_sales_today' => DB::table('sales_orders')
                    ->whereDate('created_at', today())
                    ->sum('total_amount'),
                'sales_by_category' => DB::table('sales_orders')
                    ->join('products', 'sales_orders.product_id', '=', 'products.id')
                    ->selectRaw('products.category, SUM(sales_orders.total_amount) as total')
                    ->whereDate('sales_orders.created_at', today())
                    ->groupBy('products.category')
                    ->get(),
                'top_products' => DB::table('sales_orders')
                    ->selectRaw('product_id, SUM(quantity) as total_sold, SUM(total_amount) as revenue')
                    ->whereDate('created_at', today())
                    ->groupBy('product_id')
                    ->orderBy('total_sold', 'desc')
                    ->limit(10)
                    ->get(),
                'sales_trend' => self::getSalesTrend(),
                'conversion_rate' => self::getConversionRate(),
                'average_order_value' => self::getAverageOrderValue()
            ];
        });
    }
    
    public static function getCustomerAnalytics()
    {
        // Advanced customer analytics
        return [
            'customer_segments' => self::segmentCustomers(),
            'customer_lifetime_value' => self::calculateCustomerLifetimeValue(),
            'churn_prediction' => self::predictCustomerChurn(),
            'customer_satisfaction' => self::getCustomerSatisfaction(),
            'purchase_patterns' => self::analyzePurchasePatterns(),
            'recommendation_engine' => self::generateRecommendations()
        ];
    }
    
    private static function segmentCustomers()
    {
        // RFM Analysis (Recency, Frequency, Monetary)
        $customers = DB::table('customers')
            ->leftJoin('sales_orders', 'customers.id', '=', 'sales_orders.customer_id')
            ->selectRaw('
                customers.id,
                customers.name,
                MAX(sales_orders.created_at) as last_purchase_date,
                COUNT(sales_orders.id) as purchase_frequency,
                SUM(sales_orders.total_amount) as total_spent
            ')
            ->groupBy('customers.id', 'customers.name')
            ->get();
        
        $segments = [];
        
        foreach ($customers as $customer) {
            $recency = now()->diffInDays($customer->last_purchase_date);
            $frequency = $customer->purchase_frequency;
            $monetary = $customer->total_spent;
            
            // RFM Scoring
            $r_score = $recency <= 30 ? 5 : ($recency <= 90 ? 3 : 1);
            $f_score = $frequency >= 10 ? 5 : ($frequency >= 5 ? 3 : 1);
            $m_score = $monetary >= 10000000 ? 5 : ($monetary >= 5000000 ? 3 : 1);
            
            $rfm_score = $r_score + $f_score + $m_score;
            
            if ($rfm_score >= 13) {
                $segment = 'Champions';
            } elseif ($rfm_score >= 10) {
                $segment = 'Loyal Customers';
            } elseif ($rfm_score >= 7) {
                $segment = 'Potential Loyalists';
            } else {
                $segment = 'At Risk';
            }
            
            $segments[] = [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'segment' => $segment,
                'rfm_score' => $rfm_score,
                'recency_days' => $recency,
                'frequency' => $frequency,
                'monetary_value' => $monetary
            ];
        }
        
        return $segments;
    }
}
```

#### 12.10.5 Quantum-Ready Architecture
**Future-Proofing for Quantum Computing:**
```
QUANTUM-READY ARCHITECTURE:
├── Quantum-Resistant Cryptography
│   ├── Post-Quantum Algorithms
│   ├── Quantum Key Distribution
│   ├── Quantum-Safe Hash Functions
│   └── Hybrid Encryption
├── Quantum Computing Integration
│   ├── Quantum Algorithm APIs
│   ├── Quantum Simulators
│   ├── Quantum-Classical Hybrid
│   └── Quantum Optimization
├── Advanced Security Layer
│   ├── Quantum Random Number Generation
│   ├── Quantum Digital Signatures
│   ├── Quantum Secure Communication
│   └── Quantum-Resistant Authentication
└── Future Technology Bridge
    ├── Modular Architecture
    ├── API-First Design
    ├── Microservices Pattern
    └── Technology Abstraction
```

#### 12.10.6 Metaverse & AR/VR Integration
**Immersive Business Experience:**
```
METAVERSE ARCHITECTURE:
├── Virtual Showroom
│   ├── 3D Product Visualization
│   ├── Virtual Tours
│   ├── Interactive Demos
│   └── AR Product Overlays
├── Digital Twin
│   ├── Warehouse Simulation
│   ├── Supply Chain Visualization
│   ├── Process Optimization
│   └── Predictive Analytics
├── Virtual Collaboration
│   ├── VR Meeting Rooms
│   ├── AR Remote Assistance
│   ├── Virtual Training
│   └── Digital Whiteboards
└── Immersive Analytics
    ├── 3D Data Visualization
    ├── Interactive Dashboards
    ├── Spatial Analytics
    └── Gesture Controls
```

### 12.10.7 Implementation Roadmap
**Phased Technology Adoption:**

#### **Phase 1: Foundation (Months 1-6)**
- AI-powered analytics and automation
- Basic IoT sensor integration
- Advanced analytics dashboard
- Natural language processing

#### **Phase 2: Integration (Months 7-12)**
- Blockchain supply chain tracking
- Full IoT ecosystem
- Predictive maintenance
- Smart warehouse automation

#### **Phase 3: Advanced (Months 13-18)**
- Quantum-resistant security
- AR/VR integration
- Advanced AI models
- Digital twin implementation

#### **Phase 4: Future-Ready (Months 19-24)**
- Metaverse integration
- Quantum computing APIs
- Full automation
- Autonomous operations

### 12.10.8 Benefits of Future-Ready Features

#### **Competitive Advantages:**
- **Market Leadership:** First-mover advantage with advanced technologies
- **Operational Excellence:** Unmatched efficiency and automation
- **Customer Experience:** Immersive and personalized interactions
- **Innovation Culture:** Continuous technological advancement

#### **Business Impact:**
- **Revenue Growth:** New revenue streams from innovative services
- **Cost Reduction:** Significant operational cost savings
- **Risk Mitigation:** Future-proof against technological disruption
- **Scalability:** Unlimited growth potential

#### **Technical Excellence:**
- **Performance:** Sub-second response times
- **Reliability:** 99.999% uptime with quantum resilience
- **Security:** Quantum-resistant encryption
- **Innovation:** Continuous feature evolution

**Dengan future-ready features ini, aplikasi SPPG Anda tidak hanya menjadi solusi distribusi biasa, tetapi transformasi digital lengkap yang akan memimpin industri ke depan!**

---

## 📊 APPENDIX A: COMPREHENSIVE DATABASE DESIGN

### A.1 Database Architecture Overview
**Multi-Database Strategy:**
```
DATABASE ECOSYSTEM:
├── Primary Database (MySQL 8.0+)
│   ├── Transactional Data
│   ├── Business Operations
│   ├── User Management
│   └── Real-time Processing
├── Analytics Database (PostgreSQL 15+)
│   ├── Business Intelligence
│   ├── Complex Analytics
│   ├── Geospatial Data
│   └── Time-series Analysis
├── Cache Layer (Redis 7.0+)
│   ├── Session Management
│   ├── Real-time Caching
│   ├── Queue Management
│   └── Pub/Sub Messaging
└── Search Engine (Elasticsearch 8.0+)
    ├── Full-text Search
    ├── Log Aggregation
    ├── Analytics
    └── Real-time Monitoring
```

### A.2 Core Database Tables
**Complete Table Structure:**

#### **A.2.1 Users & Authentication (8 Tables)**
```sql
-- Core Tables:
- users (User management with Indonesian NIK/NIP)
- user_sessions (Session tracking with device fingerprinting)
- roles (Role-based access control)
- permissions (Granular permissions system)
- user_role_permissions (Many-to-many relationships)
- login_attempts (Security monitoring)
- password_resets (Password management)
- user_activities (Audit trail)
```

#### **A.2.2 Organization Structure (6 Tables)**
```sql
-- Organizational Tables:
- departments (Hierarchical department structure)
- positions (Job positions and grades)
- branches (Branch/warehouse locations)
- addresses (Indonesian address format)
- organizational_units (Business units)
- reporting_structure (Hierarchy management)
```

#### **A.2.3 Customer Management (12 Tables)**
```sql
-- Customer Tables:
- customers (SPPG + Regular customers)
- customer_contacts (Multiple contact persons)
- customer_categories (Customer categorization)
- customer_contracts (Contract management)
- customer_pricing (Dynamic pricing)
- customer_credit (Credit management)
- customer_loyalty (Loyalty program)
- customer_feedback (Feedback system)
- customer_segments (RFM segmentation)
- customer_communications (Communication log)
- customer_documents (Document management)
- customer_analytics (Behavioral analytics)
```

#### **A.2.4 Product & Inventory (15 Tables)**
```sql
-- Product Tables:
- products (Master product data)
- product_categories (Hierarchical categories)
- product_attributes (Flexible attributes)
- product_prices (Dynamic pricing)
- product_images (Image management)
- product_specifications (Technical specs)
- warehouses (Warehouse management)
- current_inventory (Real-time inventory)
- inventory_transactions (Transaction log)
- inventory_adjustments (Adjustment records)
- inventory_movements (Movement tracking)
- product_suppliers (Supplier relationships)
- product_barcodes (Multiple barcodes)
- product_batches (Batch tracking)
- product_expiry (Expiry management)
```

#### **A.2.5 Sales Management (18 Tables)**
```sql
-- Sales Tables:
- sales_orders (Order management)
- sales_order_items (Order details)
- sales_order_status (Status tracking)
- sales_invoices (Invoice management)
- invoice_items (Invoice details)
- payments (Payment processing)
- payment_methods (Payment methods)
- payment_terms (Terms management)
- sales_returns (Return processing)
- return_items (Return details)
- delivery_orders (Delivery management)
- delivery_items (Delivery details)
- shipping_methods (Shipping options)
- delivery_tracking (Tracking information)
- sales_commissions (Commission calculation)
- discount_management (Discount rules)
- promotion_management (Promotion campaigns)
- sales_analytics (Performance analytics)
```

#### **A.2.6 Procurement Management (14 Tables)**
```sql
-- Procurement Tables:
- suppliers (Supplier management)
- supplier_contacts (Contact persons)
- supplier_contracts (Contract management)
- supplier_performance (Performance tracking)
- purchase_orders (Purchase orders)
- purchase_order_items (Order details)
- purchase_requisitions (Requisition management)
- receiving_reports (Goods receipt)
- receiving_items (Receipt details)
- quality_inspections (Quality control)
- supplier_invoices (Supplier invoices)
- supplier_payments (Payment processing)
- procurement_analytics (Spend analytics)
- supplier_ratings (Rating system)
```

#### **A.2.7 Financial Management (22 Tables)**
```sql
-- Financial Tables:
- chart_of_accounts (COA structure)
- accounting_periods (Period management)
- journals (Journal entries)
- journal_entries (Entry details)
- trial_balances (Trial balance)
- financial_statements (Statement generation)
- accounts_receivable (AR management)
- accounts_payable (AP management)
- invoices (Invoice management)
- payments (Payment processing)
- bank_accounts (Bank management)
- bank_reconciliations (Reconciliation)
- tax_registrations (Tax compliance)
- tax_calculations (Tax computation)
- tax_reports (Tax reporting)
- expense_claims (Expense management)
- expense_items (Expense details)
- asset_management (Fixed assets)
- asset_depreciation (Depreciation)
- budget_management (Budget tracking)
- financial_analytics (Financial analytics)
```

#### **A.2.8 Human Resources (16 Tables)**
```sql
-- HR Tables:
- employees (Employee management)
- employee_contracts (Contract management)
- employee_attendance (Attendance tracking)
- leave_requests (Leave management)
- payroll_records (Payroll processing)
- salary_components (Salary structure)
- performance_reviews (Performance management)
- training_records (Training management)
- employee_documents (Document management)
- employee_benefits (Benefits management)
- loan_management (Employee loans)
- overtime_records (Overtime tracking)
- shift_schedules (Shift management)
- department_assignments (Department assignments)
- position_history (Position history)
- hr_analytics (HR analytics)
```

#### **A.2.9 System Management (12 Tables)**
```sql
-- System Tables:
- system_configurations (System settings)
- feature_flags (Feature management)
- audit_trail (Audit logging)
- error_logs (Error tracking)
- performance_logs (Performance monitoring)
- backup_logs (Backup tracking)
- maintenance_schedules (Maintenance planning)
- notifications (Notification system)
- user_preferences (User preferences)
- api_keys (API management)
- integration_logs (Integration tracking)
- system_health (Health monitoring)
```

### A.3 Database Optimization Strategy

#### **A.3.1 Indexing Strategy**
```sql
-- Primary Indexes
- All tables have proper primary keys
- Foreign key constraints with indexes
- Unique constraints for business keys

-- Composite Indexes
CREATE INDEX idx_sales_customer_status ON sales_orders(customer_id, status);
CREATE INDEX idx_inventory_product_warehouse ON current_inventory(product_id, warehouse_id);
CREATE INDEX idx_orders_date_status ON sales_orders(order_date, status);
CREATE INDEX idx_transactions_product_date ON inventory_transactions(product_id, created_at);

-- Full-text Search Indexes
CREATE FULLTEXT idx_products_search ON products(name, description, brand);
CREATE FULLTEXT idx_customers_search ON customers(name, legal_name, contact_person);

-- Partitioning Strategy
ALTER TABLE inventory_transactions PARTITION BY RANGE (YEAR(created_at));
ALTER TABLE audit_trail PARTITION BY RANGE (YEAR(created_at));
ALTER TABLE sales_orders PARTITION BY RANGE (YEAR(order_date));
```

#### **A.3.2 Performance Optimization**
```sql
-- Query Optimization
- Use covering indexes for frequent queries
- Implement read replicas for reporting
- Optimize JOIN operations
- Use appropriate data types

-- Caching Strategy
- Redis for session management
- Application-level caching
- Query result caching
- Static data caching

-- Connection Management
- Connection pooling
- Read-write separation
- Load balancing
- Failover configuration
```

### A.4 Data Security & Compliance

#### **A.4.1 Data Encryption**
```sql
-- Column-level Encryption
- Personal data (NIK, phone, email)
- Financial data (salary, bank accounts)
- Business data (pricing, margins)
- Document storage encryption

-- Encryption Implementation
AES-256 encryption for sensitive data
Key management with rotation
Secure key storage
Compliance with Indonesian regulations
```

#### **A.4.2 Audit & Compliance**
```sql
-- Audit Trail
CREATE TABLE audit_trail (
    id BIGINT PRIMARY KEY,
    table_name VARCHAR(100),
    record_id BIGINT,
    action ENUM('INSERT', 'UPDATE', 'DELETE'),
    old_values JSON,
    new_values JSON,
    user_id BIGINT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP
);

-- Compliance Features
- Data retention policies
- Privacy controls
- Access logging
- Change tracking
```

### A.5 Data Migration Strategy

#### **A.5.1 Migration Phases**
```sql
-- Phase 1: Master Data
- Users and roles
- Products and categories
- Customers and suppliers
- Chart of accounts

-- Phase 2: Transactional Data
- Current inventory
- Open orders
- Outstanding transactions
- Active contracts

-- Phase 3: Historical Data
- Historical transactions
- Archive data
- Reporting data
- Analytics data
```

#### **A.5.2 Data Validation**
```sql
-- Validation Rules
- Referential integrity
- Business rule validation
- Data format validation
- Duplicate detection

-- Validation Scripts
- Data quality checks
- Consistency validation
- Completeness verification
- Accuracy validation
```

### A.6 Backup & Recovery Strategy

#### **A.6.1 Backup Strategy**
```sql
-- Backup Types
- Full daily backups
- Incremental hourly backups
- Transaction log backups
- Cross-region replication

-- Backup Retention
- Daily backups: 30 days
- Weekly backups: 12 weeks
- Monthly backups: 12 months
- Yearly backups: 7 years
```

#### **A.6.2 Recovery Procedures**
```sql
-- Recovery Scenarios
- Point-in-time recovery
- Full database restore
- Partial table restore
- Disaster recovery

-- Recovery Testing
- Monthly recovery drills
- Automated testing
- Performance validation
- Data integrity checks
```

### A.7 Monitoring & Maintenance

#### **A.7.1 Performance Monitoring**
```sql
-- Monitoring Metrics
- Query performance
- Index usage
- Table size growth
- Connection pool usage

-- Alert Thresholds
- Slow queries > 1 second
- CPU usage > 80%
- Memory usage > 85%
- Disk usage > 90%
```

#### **A.7.2 Maintenance Procedures**
```sql
-- Regular Maintenance
- Index rebuilding
- Statistics updates
- Table optimization
- Log cleanup

-- Maintenance Schedule
- Daily: Index maintenance
- Weekly: Statistics update
- Monthly: Table optimization
- Quarterly: Performance review
```

### A.8 Scalability Planning

#### **A.8.1 Vertical Scaling**
```sql
-- Hardware Requirements
- CPU: 16+ cores for production
- RAM: 64GB+ for production
- Storage: SSD with RAID 10
- Network: 10Gbps connection

-- Performance Targets
- <100ms for 95% queries
- 1000+ concurrent users
- 10M+ records per table
- 99.9% uptime
```

#### **A.8.2 Horizontal Scaling**
```sql
-- Scaling Strategy
- Read replicas for reporting
- Sharding for large tables
- Microservices for modules
- Load balancing

-- Scaling Triggers
- CPU usage > 70%
- Memory usage > 80%
- Query response time > 200ms
- Concurrent users > 500
```

---

**File rancangan database lengkap tersedia di: `RANCANGAN_DATABASE.md`**

*Dokumen ini menyediakan blueprint database yang komprehensif untuk implementasi SPPG Distribution Management System.*
