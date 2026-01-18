# SPPG Planning & Design Repository

Repo ini berisi **dokumen perencanaan, desain, dan prototipe konsep** untuk:
- Aplikasi manajemen SPPG (Surat Permintaan Penyaluran Barang)
- Modul AI prediksi harga dan optimasi keuntungan (mirip aplikasi saham)

Saat ini **belum ada proyek aplikasi yang benar‑benar diinisialisasi** (belum ada folder `server.js`, `client/`, dsb). Semua isi repo ini adalah bahan rancangan dan contoh kode yang akan menjadi acuan ketika proyek SPPG nanti dibuat.

---

## 📌 Status Saat Ini

- Fokus repo: perencanaan arsitektur, desain bisnis, database, dan prototipe AI.
- Belum ada:
  - Proyek backend PHP/Laravel siap jalan
  - Proyek frontend Bootstrap/jQuery siap jalan
  - Struktur folder aplikasi seperti yang dicontohkan pada README lama
- Yang sudah ada:
  - Dokumen spesifikasi teknis dan bisnis yang cukup lengkap
  - Desain database dan master data
  - Dokumen MVP Phase 1 untuk modul AI prediksi harga beserta contoh kode (Python + FastAPI + Laravel Blade sederhana)

Dengan kata lain, repo ini adalah **“blueprint + prototipe konsep”**, bukan source code aplikasi produksi.

---

## 📂 Dokumen Utama di Repo Ini

Beberapa file penting yang menjadi dasar desain:

- [SPPG_TECHNICAL_SPECIFICATIONS.md](file:///e:/xampp/htdocs/plan/plan%20sppg/SPPG_TECHNICAL_SPECIFICATIONS.md)  
  Spesifikasi teknis lengkap: arsitektur sistem, modul aplikasi, teknologi yang direkomendasikan, termasuk bagian **AI-Powered Financial Optimization**.

- [REKOMENDASI_APLIKASI.md](file:///e:/xampp/htdocs/plan/plan%20sppg/REKOMENDASI_APLIKASI.md)  
  Analisis bisnis, rekomendasi teknologi, dan strategi produk untuk aplikasi SPPG di pasar Indonesia.

- [PERENCANAAN_APLIKASI.md](file:///e:/xampp/htdocs/plan/plan%20sppg/PERENCANAAN_APLIKASI.md)  
  Perencanaan fitur dan modul aplikasi SPPG dari sisi fungsional.

- [RANCANGAN_DATABASE.md](file:///e:/xampp/htdocs/plan/plan%20sppg/RANCANGAN_DATABASE.md)  
  Desain database dan struktur tabel untuk SPPG.

- [PLU_DATABASE.md](file:///e:/xampp/htdocs/plan/plan%20sppg/PLU_DATABASE.md) dan [SPPG_MATERIALS_DATABASE.md](file:///e:/xampp/htdocs/plan/plan%20sppg/SPPG_MATERIALS_DATABASE.md)  
  Master data bahan, kode PLU, dan klasifikasi barang yang akan dipakai dalam sistem SPPG.

- [schema_sppg_core.sql](file:///e:/xampp/htdocs/plan/plan%20sppg/schema_sppg_core.sql)  
  Skema inti MySQL untuk tabel `sppg_materials`, `sppg_daily_material_demand`, `sppg_menus`, dan view kebutuhan bahan, sudah disinkronkan dengan RANCANGAN_DATABASE.md.

- [plu_import.sql](file:///e:/xampp/htdocs/plan/plan%20sppg/plu_import.sql)  
  Script import data PLU internasional dan lokal, termasuk nutrisi dasar serta mapping provinsi dan barcode.

- [SPPG_MVP_PHASE1.md](file:///e:/xampp/htdocs/plan/plan%20sppg/SPPG_MVP_PHASE1.md)  
  Dokumen **MVP Phase 1** untuk modul AI prediksi harga:
  - Skema tabel MySQL untuk harga historis dan produk
  - Contoh kode Python:
    - `DataCollector` (akses data harga ke MySQL)
    - `LSTMPredictor` dan `ARIMAPredictor` untuk prediksi harga
    - API FastAPI `prediction_api.py`
    - `ProfitCalculator` untuk hitung profit dan metrik risiko
  - Contoh komponen Laravel Blade `TradingInterface` sebagai antarmuka manual trading
  - Contoh unit test sederhana untuk model LSTM/ARIMA

- [SPPG_WORK_SUMMARY.md](file:///e:/xampp/htdocs/plan/plan%20sppg/SPPG_WORK_SUMMARY.md)  
  Ringkasan pekerjaan, daftar tugas, error yang pernah terjadi, dan roadmap sesi pengembangan selanjutnya.

---

## 🎯 Tujuan Repo

Repo ini dimaksudkan untuk:

- Menjadi **satu sumber kebenaran** (single source of truth) untuk:
  - Ide bisnis aplikasi SPPG
  - Desain teknis dan arsitektur
  - Desain AI untuk prediksi harga & optimasi keuntungan
- Menjamin **tidak ada knowledge yang hilang** ketika pindah komputer atau pindah tim:
  - Semua keputusan dan asumsi penting terdokumentasi
  - Contoh kode kunci sudah disimpan dalam bentuk snippet di file `.md`
- Menjadi dasar ketika nanti:
  - Proyek backend/ frontend diinisialisasi
  - Tim development mulai mengimplementasikan MVP sesungguhnya

---

## 🧠 Ringkasan Modul AI (MVP Phase 1)

Modul AI di [SPPG_MVP_PHASE1.md](file:///e:/xampp/htdocs/plan/plan%20sppg/SPPG_MVP_PHASE1.md) dirancang untuk:

- Mengumpulkan dan menyimpan **harga historis** per produk.
- Melatih model:
  - **LSTM** (deep learning) untuk prediksi harga berurutan
  - **ARIMA** (statistical) untuk prediksi time series klasik
- Menyediakan **API prediksi** berbasis FastAPI:
  - Input: `product_id`, horizon hari (`days_ahead`), pilihan model
  - Output: prediksi harga, confidence, dan interval kepercayaan
- Menghitung:
  - Profit transaksi tunggal
  - Profit dan risiko portofolio sederhana (volatilitas, max drawdown, Sharpe ratio, dsb.)
- Memberikan contoh antarmuka frontend (Laravel Blade) untuk:
  - Memilih produk
  - Menampilkan riwayat harga
  - Menampilkan hasil prediksi LSTM/ARIMA
  - Mensimulasikan transaksi buy/sell manual

Ini semua masih berupa **contoh kode dalam dokumen**, belum dipisah menjadi struktur folder Python/Laravel yang siap dijalankan langsung.

---

## 🗺️ Roadmap Implementasi (Saran)

Berikut urutan langkah yang disarankan ketika nanti proyek benar‑benar akan dibuat berdasarkan dokumen di repo ini:

1. **Inisialisasi proyek backend & frontend**
   - Pilih stack resmi (misalnya: backend PHP/Laravel untuk SPPG, dengan FastAPI untuk modul AI).
   - Buat struktur folder proyek nyata (bukan lagi cuma di README).

2. **Implementasi MVP SPPG non‑AI**
   - Fitur dasar:
     - Pengajuan SPPG
     - Approval workflow sederhana
     - Manajemen inventaris dan stok minimum
   - Gunakan desain database dan PLU dari dokumen yang sudah ada.

3. **Implementasi modul AI Phase 1 sebagai service terpisah**
   - Ekstrak kode dari `SPPG_MVP_PHASE1.md` menjadi file Python/Laravel sesungguhnya.
   - Jalankan service FastAPI untuk prediksi harga.

4. **Integrasi SPPG dengan modul AI**
   - Backend SPPG memanggil API AI untuk:
     - Menampilkan prediksi harga di form SPPG
     - Membantu keputusan stok dan margin.

5. **Lanjut ke Phase 2 (fitur lanjutan)**
   - Ensemble model, real‑time data, backtesting, trading bot, dsb. sesuai rencana di `SPPG_WORK_SUMMARY.md` dan bagian lanjutan di `SPPG_TECHNICAL_SPECIFICATIONS.md`.

---

## ℹ️ Cara Menggunakan Repo Ini Sekarang

- Jika Anda **product owner / business stakeholder**:
  - Baca `REKOMENDASI_APLIKASI.md` dan `PERENCANAAN_APLIKASI.md` untuk memahami value dan positioning produk.
  - Baca `SPPG_TECHNICAL_SPECIFICATIONS.md` bagian overview dan modul utama untuk memahami kemampuan sistem.

- Jika Anda **engineer yang akan mengimplementasikan proyek**:
  - Mulai dari:
    - `SPPG_TECHNICAL_SPECIFICATIONS.md` untuk gambaran arsitektur
    - `RANCANGAN_DATABASE.md` + `PLU_DATABASE.md` + `SPPG_MATERIALS_DATABASE.md` untuk desain data
    - `schema_sppg_core.sql` + `plu_import.sql` jika ingin langsung membuat database MySQL berdasarkan desain dan mengisi master PLU awal
    - `SPPG_MVP_PHASE1.md` untuk modul AI prediksi harga
  - Gunakan README ini sebagai petunjuk bahwa:
    - Belum ada kode PHP/Laravel siap pakai
    - Anda bebas menginisialisasi proyek baru dengan mengikuti desain yang ada di dokumen

---

## Catatan Penting

- Semua instruksi `npm run dev`, struktur `sppg/server.js`, dan konfigurasi MongoDB yang sebelumnya ada di README lama **tidak lagi mencerminkan isi repo saat ini**, karena proyek tersebut belum dibuat.
- README ini sengaja disederhanakan agar:
  - Tidak menyesatkan (seakan‑akan proyek sudah bisa dijalankan)
  - Lebih jujur bahwa repo ini adalah **“plan/saran + desain + contoh kode”**.

