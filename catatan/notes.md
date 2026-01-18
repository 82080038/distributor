kerjakan langkah berikutnya

kalau perlu, anda pisah-pisah dan kelompokkan; file dan folder di aplikasi ini, sesuai peruntukan dan bagiannya.
mana yang untuk general dan mana yang khusus.

==============




mulai mengerjakan next feature di menu lain yang sedang Anda buka lewat template.php (misalnya highlight menu aktif tertentu).

bagaimana dengan return pembelian atau return penjualan ?
apakah ada formnya ?
alasan return,
jumlah return,
sistem akuntasinya ?
dokumentasi ?

pelajari secara mendalam dari internet untuk urusan pembelian dan penjualan ini.

===
Buat navbarMain menjadi sticky pada bagian atas halaman dengan ketentuan berikut:
1. Implementasikan CSS position: sticky dengan top: 0
2. Pastikan navbar tetap terlihat saat melakukan scroll
3. Tambahkan efek shadow atau border bottom untuk membedakan navbar dengan konten utama
4. Uji di berbagai ukuran layar dan browser untuk memastikan kompatibilitas

Untuk form pembelian, siapkan informasi berikut dengan merujuk pada situs Badan Gizi Nasional dan sumber terpercaya lainnya:
1. Persyaratan bahan baku:
   - Spesifikasi teknis (kadar gizi, kemurnian, dll)
   - Sertifikasi yang dibutuhkan (halal, BPOM, dll)
   - Standar kualitas minimum
2. Data yang diperlukan dalam form:
   - Identitas pembeli (nama, instansi, kontak)
   - Detail pemesanan (jenis bahan, jumlah, satuan)
   - Alamat pengiriman
   - Metode pembayaran
   - Tanggal pengiriman yang diinginkan
3. Lampiran dokumen: foto barang 
update tabel yang dibutuhkan untuk hal diatas,
atau, kembangkan sesuai deep learning anda dari internet maupun dari situs Badan Gizi Nasional dan sumber terpercaya lainnya.
   

========
di tabel Daftar Produk, buat kolom selisih beli dan selisih jual dengan nama (selisih), yang otomatis dihitung oleh jquery, dan di "productColumnDropdownToggle" juga ada togle nya dengan status default tidak aktif.





kemudian refactor seluruh kode untuk penyesuaian;
buatkan pilihan tema aplikasi ini (tema gelap /terang), sesuaikan seluruh css untuk tema tersebut;

integrasikan seluruh aplikasi, dan periksa kebutuhan database;
anda berhak mengubah database dari terminal, ajax saya aktif dan tidak menggunakan password di phpmyadmin;

periksa dan perbaiki seluruh warning dan error.
lakukan semuanya secara otomatis, tanpa campur tangan user.

========================================

PETUNJUK UNTUK AI LAIN – LOGIKA APLIKASI DISTRIBUTOR

Tujuan bagian ini:
- Menjelaskan struktur dan alur logika aplikasi, supaya AI di komputer lain bisa langsung melanjutkan pekerjaan tanpa perlu membaca seluruh kode satu per satu.
- Menjadi acuan saat menambah fitur baru, refactor, atau mengintegrasikan modul SPPG dan modul AI harga di masa depan.

RINGKASAN ARSITEKTUR
- Bahasa dan stack:
  - Backend: PHP 8 + mysqli, gaya procedural, tanpa framework.
  - Frontend: Bootstrap 5, jQuery 3, Flatpickr untuk tanggal, sedikit utilitas di app.js.
  - Database utama: MySQL database "distributor".
  - Database tambahan: MySQL database "alamat_db" untuk data alamat.
- Pola umum:
  - Setiap halaman utama adalah satu file PHP (misalnya purchases.php, sales.php, products.php, pesanan.php, customers.php, suppliers.php, branches.php, perusahaan.php, profile.php, report_*.php).
  - File tersebut:
    - Memproses request (GET/POST, termasuk mode AJAX).
    - Menyiapkan variabel (data, error, success).
    - Menentukan $page_title dan $content_view.
    - Meng-include template.php untuk merender layout dan navbar.
  - File *_view.php hanya berisi HTML + sedikit PHP untuk loop data dan form.

KONFIGURASI DAN UTILITAS DASAR
- File: config.php
  - Membuka koneksi mysqli ke:
    - DB_HOST, DB_USER, DB_PASS, DB_NAME ("distributor").
    - DB_NAME_ALAMAT ("alamat_db") untuk data alamat.
  - Fungsi penting:
    - clean($value): trimming + htmlspecialchars, dipakai untuk semua input teks sebelum disimpan/ditampilkan.
    - redirect($url): header Location + exit, dipakai di auth dan beberapa alur lain.
    - format_date_id($date): mengubah tanggal database (Y-m-d) menjadi tampilan Indonesia (d-m-Y).
    - parse_date_id_to_db($value): menerima variasi input tanggal/tanggal+jam dan mengubah ke format database (Y-m-d atau Y-m-d H:i:s).
    - number_to_indonesian_words($value) dan angka_to_kata_id($x): konversi angka ke tulisan Rupiah (untuk kwitansi/nota bila nanti dibutuhkan).

AUTENTIKASI DAN SESSION
- File: auth.php
  - Selalu di-include oleh semua halaman utama (via require_once).
  - Fungsi:
    - is_logged_in(): cek apakah $_SESSION['user_id] ada.
    - require_login(): jika belum login -> redirect ke login.php.
    - current_user(): mengembalikan array user saat ini (id, username, name, role, branch_id) dari $_SESSION.
    - require_role(array $roles): membatasi akses berdasarkan role, redirect ke index.php jika tidak cocok.
  - Asumsi:
    - Proses login/register ada di login.php dan register.php (mengisi $_SESSION); logout.php menghapus session.

LAYOUT GLOBAL DAN NAVBAR
- File: template.php
  - Selalu menjadi wrapper tampilan.
  - Alur:
    - require_login() -> memastikan user sudah login.
    - Mengambil current_user() ke $user.
    - Menentukan:
      - $page_title: diisi dari halaman pemanggil.
      - $current_page: basename($_SERVER['PHP_SELF']) untuk menentukan menu aktif.
      - $is_transaksi_active: true jika halaman sedang di purchases.php atau sales.php.
    - HTML:
      - Tag <html lang="id"> dengan Bootstrap 5 dan tema light/dark menggunakan atribut data-bs-theme pada <html>.
      - Navbar utama:
        - Menu Transaksi (dropdown): Pembelian (purchases.php), Penjualan (sales.php).
        - Menu Produk, Pesanan, Pemasok, Pembeli.
        - Menu laporan: Laporan Omzet, Laporan Pembelian, Laporan Pesanan, Laporan SPPG.
        - Menu user di kanan:
          - Tombol tema (data-theme-toggle) yang berinteraksi dengan app.js.
          - Dropdown user:
            - Cabang (branches.php) untuk memilih / mengelola cabang.
            - Perusahaan (perusahaan.php) hanya jika role = owner.
            - Profil (profile.php).
            - Logout.
      - Konten utama:
        - Menampilkan $error dan $success jika ada.
        - include $content_view (misalnya purchases_view.php).
    - JavaScript:
      - jQuery, Flatpickr (plus locale id), app.js, Bootstrap bundle.
      - Kode jQuery untuk:
        - Mengelola dropdown manual agar lebih stabil.
        - Mengubah alert menjadi toast jika AppUtil.showToast tersedia di app.js.

MODUL PRODUK (MASTER DATA PRODUK)
- File utama: products.php
- Fungsi utama:
  - CRUD produk:
    - Menyimpan produk baru dan update produk lama.
    - Field penting: code, name, unit, barcode, category_id, buy_price, sell_price, stock_qty, is_active.
    - Validasi:
      - Nama dan satuan wajib.
      - Harga dan stok tidak boleh negatif.
      - Untuk produk yang sudah ada (id > 0), code wajib.
  - Generasi kode otomatis:
    - Jika insert baru dan code kosong:
      - Mencoba kode PyyMMdd-XYZ dengan angka random 3 digit.
      - Mengecek keunikan di tabel products; jika bentrok, mencoba beberapa kali.
  - Integrasi PLU:
    - Bila user mengisi plu_number (nomor PLU internasional/lokal):
      - Mencari di tabel plu_codes (harus is_active = 1).
      - Jika mapping product_plu_mapping belum ada:
        - Menambahkan baris mapping (product_id, plu_code_id, province_id NULL, local_name, is_primary_plu = 1, effective_date = CURRENT_DATE).
      - Jika barcode dengan nilai plu_number belum ada di product_barcodes:
        - Menambahkan baris barcode bertipe 'PLU' dan is_primary = 1.
  - AJAX search_products:
    - Endpoint: products.php?ajax=search_products&q=...&mode=sale|purchase.
    - Mengembalikan JSON daftar produk (id, code, name, unit, barcode, plu_number, price).
    - price dipilih berdasarkan mode:
      - purchase -> buy_price (harga beli).
      - sale -> sell_price (harga jual).
    - Harga dapat dikustom per cabang via tabel branch_product_prices:
      - Jika ada baris aktif untuk product_id dan branch_id, maka COALESCE(bpp.buy_price/sell_price, p.buy_price/sell_price).
  - Toggle aktif/nonaktif produk:
    - Action: POST action=toggle.
    - Mengambil is_active, membalik nilai (1 -> 0, 0 -> 1), update ke tabel products.
    - Mengembalikan pesan sukses dan status baru, mendukung mode AJAX.
  - Listing dan filtering:
    - Mengambil daftar produk dengan filter status (all/active/inactive) dan q (pencarian nama/kode/barcode).
    - Menggabungkan kategori (product_categories) untuk menampilkan category_name.
    - Menghitung statistik jumlah total_active dan total_inactive.
- File tampilan: products_view.php
  - Menampilkan form tambah/edit produk, daftar produk, dan kemungkinan kolom tambahan seperti selisih harga jika nanti ditambahkan.

MODUL PEMBELIAN (TRANSAKSI PENGADAAN)
- File utama: purchases.php
- Fungsi logika:
  - log_purchase_audit():
    - Menulis jejak perubahan ke tabel purchase_audit_log (jika tabel tersebut ada).
    - Mencatat purchase_id, action (insert/update/delete), total_before, total_after, performed_by (user_id).
  - AJAX detail pembelian:
    - Endpoint: purchases.php?ajax=purchase_detail&id=...
    - Mengambil header dan items pembelian, memfilter berdasarkan branch_id user jika di-set.
    - Mengembalikan JSON dengan struktur:
      - purchase: id, supplier_id, supplier_name, invoice_no, purchase_date (+ formatted_date), total_amount (+ formatted_total), notes.
      - items: daftar item dengan product_id, product_name, unit, qty, price, subtotal (+ format).
  - Form dan default:
    - Mengisi daftar pemasok (orang.is_supplier = 1, is_active = 1).
    - Mengisi daftar produk aktif dengan harga beli, termasuk harga khusus per cabang (branch_product_prices).
    - Daftar 50 pembelian terbaru untuk ringkasan.
  - Penyimpanan pembelian baru (action=save):
    - Mengambil supplier_id, supplier_invoice_no, purchase_date (+ purchase_time optional), notes.
    - Items:
      - Bisa dikirim dalam bentuk array items[] atau single product_id+qty+price.
      - Membersihkan nilai qty/price dari karakter koma sebelum casting ke float.
      - Validasi setiap item:
        - product_id > 0, qty > 0, price >= 0.
    - Validasi header:
      - user_id dan branch_id harus ada (user harus terhubung ke cabang).
      - supplier_id harus valid dan aktif di tabel orang.
      - tanggal pembelian wajib dan dapat di-parse.
    - Generate invoice internal:
      - generate_internal_purchase_invoice_no():
        - Prefix: "PB".
        - Format: PB-BBB-YYYYMMDD-#### (BBB = branch_id 3 digit, #### = sequence harian).
        - Mencari invoice_no terbesar pada tanggal dan cabang yang sama, lalu increment.
    - Transaksi database:
      - BEGIN TRANSACTION.
      - Insert ke purchases (header).
      - Insert multiple rows ke purchase_items (detail).
      - Commit jika semua sukses, rollback jika ada error.
      - Memanggil log_purchase_audit dengan action "insert".
    - Mendukung respon JSON jika POST['ajax'] = '1'.
  - Update pembelian (action=update_purchase):
    - Mengambil pembelian berdasarkan id dan branch_id (jika ada).
    - Validasi supplier dan items seperti pada insert.
    - Menyimpan total_before dari purchases.total_amount.
    - Transaksi:
      - UPDATE header (supplier, tanggal, total_amount, notes).
      - DELETE semua purchase_items lama.
      - INSERT purchase_items baru.
      - Commit/rollback.
      - Menulis audit log dengan action "update".
    - Mendukung respon JSON jika ajax=1.
  - Hapus pembelian (action=delete_purchase):
    - Mencari pembelian dengan branch_id user (jika ada).
    - Menghapus purchase_items lalu purchases dalam transaksi.
    - Menulis audit log dengan action "delete".
    - Mendukung mode AJAX.
  - Quick add supplier (action=quick_add_supplier):
    - Menyimpan entitas orang baru sebagai pemasok (is_supplier=1, is_customer=0, is_active=1).
    - Mengembalikan JSON berisi supplier baru untuk di-select langsung di form.

MODUL PENJUALAN (TRANSAKSI KELUAR)
- File utama: sales.php
- Fungsi logika:
  - Form dan default:
    - Mengisi daftar pembeli aktif (orang.is_customer=1).
    - Mengisi daftar produk dengan harga jual, termasuk harga per cabang (branch_product_prices).
    - Mengambil 50 penjualan terbaru untuk ringkasan.
  - Simpan penjualan (action=save):
    - Input:
      - customer_type: "pelanggan" atau "umum".
      - customer_id: jika pelanggan khusus.
      - invoice_no: kode faktur manual/opsional.
      - sale_date: tanggal penjualan.
      - product_id, qty, price, notes.
    - Validasi:
      - user_id dan branch_id harus ada.
      - Jika bukan "umum", customer_id wajib dan aktif di tabel orang (is_customer=1, is_active=1).
      - product_id wajib dan aktif di tabel products (is_active=1).
      - tanggal wajib.
      - qty > 0 dan price >= 0.
    - Penentuan nama pembeli:
      - Jika umum -> "Pembeli Umum" dengan customer_id=0.
      - Jika pelanggan -> nama diambil dari orang.nama_lengkap.
    - Menyimpan header dan 1 item:
      - Header: sales (branch_id, customer_id, customer_name, invoice_no, sale_date, total_amount, notes, created_by).
      - Item: sale_items (sale_id, product_id, qty, price, subtotal).
    - Transaksi database:
      - BEGIN TRANSACTION, commit/rollback seperti pembelian.
    - Mendukung mode AJAX (response JSON).
  - Quick add customer (action=quick_add_customer):
    - Menambahkan orang baru (is_customer=1).
    - Mengembalikan JSON untuk langsung mengisi field pembeli di form.
  - Detail penjualan:
    - Ketika GET sale_id dan branch_id valid, mengambil detail penjualan dan item terkait untuk ditampilkan di sales_view.php.

MODUL PEMBELI (MASTER CUSTOMER)
- File utama: customers.php
- Fungsi logika:
  - AJAX search_customers:
    - Endpoint: customers.php?ajax=search_customers&q=...
    - Mengembalikan JSON (id, name) untuk fitur auto-complete di form penjualan/pesanan.
  - CRUD pembeli (action=save):
    - Menyimpan data pembeli di tabel orang.
    - Field: nama_lengkap, alamat, kontak, is_supplier, is_customer=1, is_active.
    - Bisa menandai pembeli juga sebagai pemasok (also_supplier) -> is_supplier=1.
    - Mendukung mode AJAX.
  - Toggle aktif/nonaktif pembeli (action=toggle):
    - Mengambil pembeli berdasarkan id_orang dan is_customer=1.
    - Membalik is_active lalu menyimpan.
    - Mendukung mode AJAX.
  - Listing dan filter:
    - Filter status: active/inactive/all.
    - Filter role: all atau dual (pembeli yang juga supplier).
    - Pencarian nama (q).
  - File tampilan: customers_view.php untuk form dan tabel pembeli.

MODUL PESANAN (ORDER MANAGEMENT + INTEGRASI KE PENJUALAN)
- File utama: pesanan.php
- Konsep:
  - Modul ini mengelola pesanan (orders) yang bisa berasal dari teks copy-paste (misalnya dari Excel/WhatsApp/email), lalu dinormalisasi menjadi baris barang, disimpan sebagai "draft", dan kemudian dipenuhi stoknya dengan mengonversi menjadi penjualan (sales).
- Fungsi parsing dan normalisasi:
  - sppg_to_title_case(): sama seperti di products.php, merapikan penulisan uraian.
  - sppg_parse_pesanan_text($text):
    - Memecah teks multiline menjadi baris.
    - Mengabaikan baris kosong dan baris yang hanya berisi karakter '=' atau '-'.
    - Mencoba dua format:
      - Format tabel dengan tab:
        - Kolom: nomor, uraian, qty, satuan, catatan.
      - Format 1 baris:
        - "uraian qty satuan [catatan]".
    - Membersihkan qty dari karakter non-angka, mengubah koma menjadi titik.
    - Hanya menyimpan baris dengan qty > 0.
  - normalize_pesanan_rows_from_post($rows):
    - Dinormalisasi dari input form HTML (array rows[]), memastikan qty sudah dalam float dan baris kosong di-skip.
  - sppg_merge_duplicate_rows($rows):
    - Menggabungkan baris dengan uraian dan satuan yang sama (case-insensitive), menjumlah qty, dan memilih nomor (no) jika tersedia.
- Alur utama:
  - preview_pesanan:
    - Mengambil raw_pesanan (teks copy-paste) dari form.
    - Mem-parse dan merge duplicates.
    - Menampilkan preview_rows di form untuk diperiksa / diedit.
  - edit_pesanan:
    - Mengambil rows[] dari form (hasil editing manual) dan menyiapkan kembali preview_rows.
  - append_from_excel:
    - Menggabungkan raw_pesanan lama dengan append_raw_pesanan.
    - Mem-parse baris tambahan, merge dengan existing_rows, dan menampilkan hasil gabungan.
  - save_pesanan:
    - Menentukan nama pembeli:
      - Jika customer_type=umum -> "Pembeli Umum".
      - Kalau id pembeli diisi -> di-cek di tabel orang (is_customer=1, is_active=1) untuk memastikan valid.
    - Validasi:
      - user_id dan branch_id wajib.
      - Nama pembeli wajib.
      - Tanggal pesanan wajib.
      - Harus ada minimal 1 baris preview_rows.
    - Menyimpan:
      - Header ke tabel orders (branch_id, customer_name, order_date, required_date, raw_text, status='draft', created_by).
      - Detail ke order_items (order_id, seq_no, description, qty, unit, notes).
    - Mendukung mode AJAX.
  - quick_add_customer:
    - Menambah pembeli baru di tabel orang seperti di sales.php.
    - Mengembalikan JSON dengan data pembeli baru.
  - update_fulfillment:
    - Mengubah status order (draft/diproses/selesai/parsial).
    - Meng-update mapping produk untuk setiap order_items (product_id) berdasarkan input form.
  - create_sale_from_order:
    - Validasi:
      - user dan branch harus valid.
      - Order harus milik branch tersebut dan status tidak lagi "draft" atau "selesai".
    - Mengambil item order yang qty > 0 dan sudah dipetakan ke product_id.
    - Mengambil harga jual per produk (sell_price atau branch_product_prices).
    - Menghitung stok per produk berdasarkan total pembelian - total penjualan (per branch).
    - Menentukan qty yang bisa dipenuhi:
      - Jika stok < qty order -> fulfillQty=stok, status order menjadi "parsial".
      - Jika stok cukup untuk semua item -> status order menjadi "selesai".
    - Membuat transaksi penjualan baru:
      - Header di sales (branch_id, customer_name, sale_date=hari ini, total_amount, notes "Penjualan dari pesanan ID X").
      - Items di sale_items.
    - Mengupdate orders.total_amount (menambahkan totalAmount penjualan) dan status.
  - Data pendukung:
    - Daftar orders terbaru untuk tabel ringkasan.
    - Daftar pembeli untuk dropdown.
    - Data pemetaan fulfilment (fulfillment_order, fulfillment_items, fulfillment_products, fulfillment_stock, fulfillment_summary) untuk tampilan pemenuhan stok.

MODUL SPPG (PERENCANAAN KEBUTUHAN BAHAN)
- File utama tampilan laporan: report_sppg.php + report_sppg_view.php.
- Database terkait:
  - sppg_materials: master bahan SPPG.
  - sppg_daily_material_demand: kebutuhan bahan per hari per sppg_id.
- Alur report_sppg_view.php:
  - Input:
    - sppg_a (rencana), sppg_b (aktual) sebagai kode SPPG.
    - start_date dan end_date opsional.
  - Mengambil daftar sppg_id dari tabel sppg_daily_material_demand untuk opsi dropdown.
  - Menjalankan query agregasi:
    - Jika sppg_a dan sppg_b terisi dan berbeda:
      - Menghitung qty_a dan qty_b per kombinasi (tanggal, material_code) dalam kg (total_quantity_grams / 1000).
      - Mendukung filter tanggal (between, >=, <=, atau tanpa filter).
    - Jika hanya sppg_a terisi:
      - Menghitung qty_a saja dan mengisi qty_b = null.
  - Menampilkan tabel:
    - Kolom: Tanggal, Kode Bahan, Nama Bahan, Qty SPPG A, Qty SPPG B, Selisih B-A.
    - Qty diformat 3 decimal dan selisih dihitung di PHP sebelum render.
- File pendukung di folder catatan:
  - parse_sppg_excel.php, sppg_bahan_parser.php, sppg_bahan_week_parser.php, sppg_sync_products.php:
    - Dipakai untuk parsing file Excel SPPG, konversi ke format tabel harian, sinkronisasi ke tabel products, dsb.
    - Semua file ini adalah skrip utilitas pengolahan data SPPG (manual/one-off) dan bukan bagian dari flow user harian.

MODUL CABANG, PERUSAHAAN, PROFIL
- File: branches.php, perusahaan.php, profile.php (+ *_view.php masing-masing).
- Tujuan:
  - branches.php:
    - Mengelola cabang (branches): nama cabang, alamat, dsb.
    - Menghubungkan user ke branch_id tertentu (disimpan di session saat login).
    - Banyak query transaksi mem-filter berdasarkan branch_id user.
  - perusahaan.php:
    - Mengelola identitas perusahaan (hanya role owner).
  - profile.php:
    - Mengelola profil user: nama, password, cabang default, dll.

LAPORAN KEUANGAN DAN TRANSAKSI
- File: report_omzet.php, report_purchases.php, report_pesanan.php (+ *_view.php).
- Tujuan umum:
  - report_omzet:
    - Menghitung omzet penjualan per periode, per cabang, atau per kategori.
  - report_purchases:
    - Menampilkan daftar pembelian per periode, bisa difilter oleh pemasok dan cabang.
  - report_pesanan:
    - Menyajikan ringkasan orders (draft/diproses/selesai/parsial) per tanggal dan pelanggan.
  - Pola umum:
    - Mengambil parameter tanggal dari GET.
    - Menggunakan format_date_id dan parse_date_id_to_db untuk konversi.
    - Menjalankan query agregasi dan menampilkan hasil di *_view.php.

PATTERN UMUM UNTUK ENDPOINT AJAX
- Parameter:
  - Biasanya menggunakan:
    - $_GET['ajax'] untuk menentukan jenis endpoint (misalnya search_products, purchase_detail, search_customers).
    - $_POST['action'] untuk action form (save, delete, toggle, quick_add_*).
  - Mode AJAX dibedakan oleh:
    - GET['ajax'] untuk endpoint GET.
    - POST['ajax'] === '1' untuk endpoint POST yang mengembalikan JSON.
- Response:
  - Selalu mengirim header('Content-Type: application/json; charset=utf-8').
  - Struktur minimal:
    - success: true/false.
    - message: pesan error/sukses (jika ada).
    - data/entitas tambahan (misalnya product, supplier, customer).

CATATAN UNTUK AI YANG MELANJUTKAN PEKERJAAN
- Saat menambah fitur baru:
  - Ikuti pola:
    - Proses input di file utama .php (bukan di *_view.php).
    - Gunakan clean() untuk semua input teks.
    - Gunakan prepared statements (->prepare, ->bind_param) untuk query dengan input user.
    - Gunakan transaksi (begin_transaction/commit/rollback) untuk operasi yang menyentuh beberapa tabel.
  - Pastikan semua alur yang butuh login selalu memakai require_login() dari auth.php.
  - Untuk modul yang bergantung pada cabang:
    - Selalu baca current_user() dan ambil branch_id.
    - Filter query berdasarkan branch_id jika relevan, agar data tidak tercampur antar cabang.
- Integrasi dengan UI:
  - Tambahkan entri menu baru di navbar di template.php, sesuaikan $current_page agar highlight aktif.
  - Jika butuh mode AJAX:
    - Gunakan pola $_GET['ajax'] atau $_POST['ajax'] seperti file lain.
    - Pastikan response JSON selalu valid.
- Integrasi dengan SPPG dan modul AI:
  - Semua desain SPPG dan AI dijelaskan di file-file .md di folder catatan.
  - Database SPPG sudah memiliki skema hampir final (lihat schema_sppg_core.sql di dokumen).
  - Modul distributor ini sudah punya basis data produk dan transaksi; integrasi ke modul AI dapat dilakukan dengan:
    - Menambahkan job/skrip eksport data harga historis dari tabel purchases/sales/products.
    - Menghubungkan ke API FastAPI (modul AI) sebagaimana dijelaskan di SPPG_MVP_PHASE1.md.

URUTAN KERJA OTOMATIS YANG DISARANKAN UNTUK AI
- Baca ringkasan logika di atas untuk memahami arsitektur.
- Gunakan catatan di file ini sebagai referensi tugas lanjutan:
  - Sticky navbar, kolom selisih di daftar produk, tema gelap/terang, refactor, integrasi database, perbaikan warning/error, dll.
- Saat melanjutkan:
  - Utamakan implementasi yang tidak merusak alur pembelian/penjualan/pesanan yang sudah ada.
  - Selalu uji:
    - Input valid dan tidak valid.
    - Mode AJAX dan non-AJAX.
    - Multi-cabang jika branch_id dipakai di query.
