-- ========================================
-- Identity & People Management Schema
-- ========================================

-- Tabel Master Orang (Person Identity)
CREATE TABLE orang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_orang INT UNSIGNED NOT NULL, -- NIK/ID Number
    nama_lengkap VARCHAR(200) NOT NULL, -- Full Name
    nama_panggilan VARCHAR(100), -- Nickname/Preferred Name
    jenis_kelamin ENUM('L', 'P') DEFAULT NULL,
    tempat_lahir DATE,
    tanggal_lahir DATE,
    alamat TEXT, -- Alamat lengkap
    no_ktp VARCHAR(50) UNIQUE, -- Nomor KTP
    no_npwp VARCHAR(25) UNIQUE, -- Nomor NPWP
    no_passport VARCHAR(50), -- Nomor Paspor
    no_kk VARCHAR(50), -- Nomor Kartu Keluarga
    agama VARCHAR(50),
    status_pernikahan ENUM('BELUM_KAWIN', 'KAWIN', 'CERAI', 'MENINGGAL_DUNIA') DEFAULT NULL,
    pendidikan_terakhir VARCHAR(100),
    pekerjaan VARCHAR(100),
    email VARCHAR(150),
    phone VARCHAR(50),
    foto_url VARCHAR(255), -- URL photo
    is_supplier BOOLEAN DEFAULT FALSE,
    is_customer BOOLEAN DEFAULT FALSE,
    is_employee BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_id_orang (id_orang),
    INDEX idx_no_ktp (no_ktp),
    INDEX idx_no_npwp (no_npwp),
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_is_active (is_active),
    INDEX idx_is_supplier (is_supplier),
    INDEX idx_is_customer (is_customer),
    INDEX idx_is_employee (is_employee)
);

-- Tabel Alamat (Address Management)
CREATE TABLE alamat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_alamat VARCHAR(50) UNIQUE NOT NULL, -- Kode alamat
    alamat_lengkap TEXT NOT NULL, -- Alamat lengkap
    rt VARCHAR(10), -- RT
    rw VARCHAR(10), -- RW
    kode_pos VARCHAR(10), -- Kode Pos
    kelurahan VARCHAR(100), -- Kelurahan/Desa
    kecamatan VARCHAR(100), -- Kecamatan
    kabupaten_kota VARCHAR(100), -- Kabupaten/Kota
    provinsi VARCHAR(100), -- Provinsi
    kode_pos VARCHAR(20), -- Kode Pos Indonesia
    latitude DECIMAL(10,8), -- Koordinat GPS
    longitude DECIMAL(10,8), -- Koordinat GPS
    is_primary BOOLEAN DEFAULT FALSE, -- Alamat utama
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_id_alamat (id_alamat),
    INDEX idx_kelurahan (kelurahan),
    INDEX idx_kecamatan (kecamatan),
    INDEX idx_kabupaten (kabupaten_kota),
    INDEX idx_provinsi (provinsi),
    INDEX idx_kode_pos (kode_pos)
);

-- Tabel Hubungan Orang dengan Alamat
CREATE TABLE orang_alamat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_orang INT NOT NULL,
    id_alamat INT NOT NULL,
    jenis_alamat ENUM('DOMISILI', 'KANTOR', 'PABRIK', 'TOKO', 'GUDANG', 'LAINNYA') DEFAULT 'DOMISILI',
    status_alamat ENUM('AKTIF', 'TIDAK_AKTIF') DEFAULT 'AKTIF',
    tanggal_mulai DATE, -- Tanggal mulai tinggal
    tanggal_selesai DATE, -- Tanggal selesai tinggal
    keterangan TEXT, -- Keterangan tambahan
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_orang) REFERENCES orang(id_orang) ON DELETE CASCADE,
    FOREIGN KEY (id_alamat) REFERENCES alamat(id) ON DELETE CASCADE,
    INDEX idx_orang_alamat (id_orang, id_alamat),
    INDEX idx_jenis_alamat (jenis_alamat),
    INDEX idx_status_alamat (status_alamat)
);

-- Tabel Data Keluarga (Family Data)
CREATE TABLE keluarga_orang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_orang_kepala_keluarga INT NOT NULL, -- Kepala Keluarga
    id_orang_anggota_keluarga INT NOT NULL, -- Anggota Keluarga
    hubungan_keluarga ENUM('SUAMI', 'ISTRI', 'ANAK', 'ORANG_TUA', 'MERTUA', 'FAMILI_LAIN') NOT NULL,
    status_keluarga ENUM('AKTIF', 'MENINGGAL', 'TIDAK_AKTIF') DEFAULT 'AKTIF',
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_orang_kepala_keluarga) REFERENCES orang(id_orang) ON DELETE CASCADE,
    FOREIGN KEY (id_orang_anggota_keluarga) REFERENCES orang(id_orang) ON DELETE CASCADE,
    INDEX idx_keluarga_kepala (id_orang_kepala_keluarga),
    INDEX idx_keluarga_anggota (id_orang_anggota_keluarga),
    INDEX idx_hubungan_keluarga (hubungan_keluarga),
    INDEX idx_status_keluarga (status_keluarga)
);

-- Tabel Pendidikan (Education History)
CREATE TABLE pendidikan_orang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_orang INT NOT NULL,
    jenjang_pendidikan ENUM('TK', 'SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3', 'PROFESI', 'DOKTOR', 'MAGISTER', 'SARJANA', 'LAINNYA') NOT NULL,
    nama_institusi VARCHAR(200) NOT NULL, -- Nama Institusi
    jurusan VARCHAR(100), -- Jurusan/Program Studi
    tahun_masuk YEAR,
    tahun_lulus YEAR,
    ipk DECIMAL(3,1), -- Indeks Prestasi Kumulatif
    predikat_kelulusan VARCHAR(20), -- Predikat kelulusan
    nomor_ijazah VARCHAR(50), -- Nomor Ijazah
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_orang) REFERENCES orang(id_orang) ON DELETE CASCADE,
    INDEX idx_pendidikan_orang (id_orang, jenjang_pendidikan),
    INDEX idx_tahun_lulus (tahun_lulus),
    INDEX idx_nama_institusi (nama_institusi)
);

-- Tabel Pengalaman Kerja (Work Experience)
CREATE TABLE pengalaman_kerja_orang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_orang INT NOT NULL,
    nama_perusahaan VARCHAR(200) NOT NULL, -- Nama Perusahaan
    jabatan VARCHAR(100) NOT NULL, -- Posisi/Jabatan
    bidang_pekerjaan VARCHAR(100), -- Bidang Pekerjaan
    tanggal_masuk DATE NOT NULL, -- Tanggal Mulai Kerja
    tanggal_keluar DATE, -- Tanggal Selesai Kerja
    gaji_terakhir DECIMAL(15,2), -- Gaji Terakhir
    mata_uang ENUM('IDR', 'USD', 'EUR', 'LAINNYA') DEFAULT 'IDR',
    keterangan TEXT,
    alamat_kantor TEXT, -- Alamat Kantor
    nomor_telepon_kantor VARCHAR(50), -- Telepon Kantor
    email_kantor VARCHAR(150), -- Email Kantor
    is_pekerjaan_aktif BOOLEAN DEFAULT TRUE, -- Masih Bekerja?
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_orang) REFERENCES orang(id_orang) ON DELETE CASCADE,
    INDEX idx_pengalaman_orang (id_orang, is_pekerjaan_aktif),
    INDEX idx_tanggal_masuk (tanggal_masuk),
    INDEX idx_nama_perusahaan (nama_perusahaan)
);

-- Tabel Dokumen (Document Management)
CREATE TABLE dokumen_orang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_orang INT NOT NULL,
    jenis_dokumen ENUM('KTP', 'NPWP', 'PASSPORT', 'KK', 'IJAZAH', 'SIM', 'KARTU_KUNING', 'AKTE_KELAHIRAN', 'SKCK', 'SURAT_KEPUTUSAN', 'LAINNYA') NOT NULL,
    nomor_dokumen VARCHAR(100) NOT NULL, -- Nomor Dokumen
    file_dokumen VARCHAR(255), -- Path/URL file dokumen
    tanggal_terbit DATE, -- Tanggal Terbit
    tanggal_berlaku DATE, -- Tanggal Berlaku
    tanggal_kadaluarsa DATE, -- Tanggal Kadaluarsa
    penerbit VARCHAR(200), -- Penerbit
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_orang) REFERENCES orang(id_orang) ON DELETE CASCADE,
    INDEX idx_dokumen_orang (id_orang, jenis_dokumen),
    INDEX idx_nomor_dokumen (nomor_dokumen),
    INDEX idx_tanggal_berlaku (tanggal_berlaku)
);

-- Tabel Perizinan Usaha (Business Licenses)
CREATE TABLE perizinan_usaha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_orang INT NOT NULL,
    jenis_perizinan ENUM('SIUP', 'TDP', 'HO', 'IUMK', 'TDUP', 'SITU', 'IUT', 'API', 'HALAL', 'BPOM', 'PIRT', 'TR', 'MD', 'KBLI', 'SNI', 'LAINNYA') NOT NULL,
    nomor_perizinan VARCHAR(100) NOT NULL, -- Nomor Perizinan
    institusi_penerbit VARCHAR(200) NOT NULL, -- Institusi Penerbit
    tanggal_terbit DATE NOT NULL, -- Tanggal Terbit
    tanggal_berlaku DATE NOT NULL, -- Tanggal Berlaku
    tanggal_kadaluarsa DATE, -- Tanggal Kadaluarsa
    file_perizinan VARCHAR(255), -- Path/URL file perizinan
    status_perizinan ENUM('AKTIF', 'KADALUARSA', 'TIDAK_AKTIF', 'DIPERBARUI') DEFAULT 'AKTIF',
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_orang) REFERENCES orang(id_orang) ON DELETE CASCADE,
    INDEX idx_perizinan_usaha (id_orang, jenis_perizinan),
    INDEX idx_nomor_perizinan (nomor_perizinan),
    INDEX idx_tanggal_berlaku (tanggal_berlaku),
    INDEX idx_status_perizinan (status_perizinan)
);

-- Tabel Bank Accounts (Rekening Bank)
CREATE TABLE rekening_bank_orang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_orang INT NOT NULL,
    nama_bank VARCHAR(100) NOT NULL, -- Nama Bank
    nomor_rekening VARCHAR(50) NOT NULL, -- Nomor Rekening
    nama_pemilik VARCHAR(200) NOT NULL, -- Nama Pemilik Rekening
    jenis_rekening ENUM('TABUNGAN', 'GIRO', 'DEPOSITO', 'KARTU_KREDIT', 'E_WALLET') DEFAULT 'TABUNGAN',
    mata_uang ENUM('IDR', 'USD', 'EUR', 'LAINNYA') DEFAULT 'IDR',
    is_rekening_utama BOOLEAN DEFAULT FALSE, -- Rekening Utama?
    status_rekening ENUM('AKTIF', 'DITUTUP', 'DIBLOKIR', 'TIDAK_AKTIF') DEFAULT 'AKTIF',
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_orang) REFERENCES orang(id_orang) ON DELETE CASCADE,
    INDEX idx_rekening_orang (id_orang, is_rekening_utama),
    INDEX idx_nama_bank (nama_bank),
    INDEX idx_nomor_rekening (nomor_rekening),
    INDEX idx_status_rekening (status_rekening)
);

-- Tabel Tax Information (Perpajakan)
CREATE TABLE pajak_orang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_orang INT NOT NULL,
    jenis_pajak ENUM('PPH_21', 'PPH_22', 'PPH_23', 'PPH_4_2', 'PPH_15', 'PPH_FINAL', 'BPHTB', 'PPN', 'LAINNYA') NOT NULL,
    npwp_terdaftar VARCHAR(25) NOT NULL, -- NPWP Terdaftar
    status_pkp ENUM('PKP', 'NON_PKP', 'TIDAK_TERDAFTAR') DEFAULT 'NON_PKP', -- Status PKP
    persentase_pajak DECIMAL(5,2), -- Persentase Pajak
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_orang) REFERENCES orang(id_orang) ON DELETE CASCADE,
    INDEX idx_pajak_orang (id_orang, jenis_pajak),
    INDEX idx_npwp_terdaftar (npwp_terdaftar),
    INDEX idx_status_pkp (status_pkp)
);

-- View untuk Master Orang Lengkap
CREATE VIEW master_orang_view AS
SELECT 
    o.id_orang,
    o.nama_lengkap,
    o.nama_panggilan,
    o.jenis_kelamin,
    o.tempat_lahir,
    o.tanggal_lahir,
    o.email,
    o.phone,
    o.is_supplier,
    o.is_customer,
    o.is_employee,
    o.is_active,
    o.created_at,
    o.updated_at,
    
    -- Alamat Utama
    alamat_utama.alamat_lengkap,
    alamat_utama.rt,
    alamat_utama.rw,
    alamat_utama.kelurahan,
    alamat_utama.kecamatan,
    alamat_utama.kabupaten_kota,
    alamat_utama.provinsi,
    alamat_utama.kode_pos,
    
    -- Data Keluarga
    COUNT(DISTINCT CASE WHEN ko.hubungan_keluarga = 'SUAMI' THEN 1 ELSE 0 END) as jumlah_istri,
    COUNT(DISTINCT CASE WHEN ko.hubungan_keluarga = 'ANAK' THEN 1 ELSE 0 END) as jumlah_anak,
    COUNT(DISTINCT CASE WHEN ko.hubungan_keluarga IN ('ORANG_TUA', 'MERTUA', 'FAMILI_LAIN') THEN 1 ELSE 0 END) as jumlah_keluarga_lain,
    
    -- Pendidikan Tertinggi
    MAX(po.tahun_lulus) as pendidikan_tertinggi,
    GROUP_CONCAT(DISTINCT po.jenjang_pendidikan, ', ' ORDER BY po.jenjang_pendidikan) as daftar_pendidikan,
    
    -- Pengalaman Kerja
    COUNT(DISTINCT CASE WHEN pk.is_pekerjaan_aktif = 1 THEN 1 ELSE 0 END) as pekerjaan_aktif,
    MAX(pk.gaji_terakhir) as gaji_terakhir,
    GROUP_CONCAT(DISTINCT pk.nama_perusahaan, ', ' ORDER BY pk.tanggal_keluar DESC LIMIT 3) as daftar_perusahaan,
    
    -- Dokumen
    COUNT(DISTINCT CASE WHEN do.tanggal_berlaku > CURDATE() THEN 1 ELSE 0 END) as dokumen_berlaku,
    GROUP_CONCAT(DISTINCT do.jenis_dokumen, ', ' ORDER BY do.jenis_dokumen) as daftar_dokumen,
    
    -- Perizinan
    COUNT(DISTINCT CASE WHEN pu.status_perizinan = 'AKTIF' THEN 1 ELSE 0 END) as perizinan_aktif,
    GROUP_CONCAT(DISTINCT pu.jenis_perizinan, ', ' ORDER BY pu.jenis_perizinan) as daftar_perizinan,
    
    -- Rekening Bank
    COUNT(DISTINCT CASE WHEN rb.status_rekening = 'AKTIF' THEN 1 ELSE 0 END) as rekening_aktif,
    GROUP_CONCAT(DISTINCT rb.nama_bank, ', ' ORDER BY rb.is_rekening_utama DESC, rb.nama_bank) as daftar_bank,
    
    -- Pajak
    COUNT(DISTINCT CASE WHEN pa.status_pkp = 'PKP' THEN 1 ELSE 0 END) as status_pkp,
    GROUP_CONCAT(DISTINCT pa.jenis_pajak, ', ' ORDER BY pa.jenis_pajak) as daftar_pajak
    
FROM orang o
LEFT JOIN orang_alamat oa ON o.id_orang = oa.id_orang AND oa.status_alamat = 'AKTIF' AND oa.jenis_alamat = 'DOMISILI'
LEFT JOIN alamat alamat_utama ON oa.id_alamat = alamat_utama.id
LEFT JOIN keluarga_orang ko ON o.id_orang = ko.id_orang_kepala_keluarga
LEFT JOIN keluarga_orang ka ON o.id_orang = ko.id_orang_anggota_keluarga
LEFT JOIN pendidikan_orang po ON o.id_orang = po.id_orang
LEFT JOIN pengalaman_kerja_orang pk ON o.id_orang = pk.id_orang
LEFT JOIN dokumen_orang do ON o.id_orang = do.id_orang
LEFT JOIN perizinan_usaha pu ON o.id_orang = pu.id_orang
LEFT JOIN rekening_bank_orang rb ON o.id_orang = rb.id_orang
LEFT JOIN pajak_orang pa ON o.id_orang = pa.id_orang
WHERE o.is_active = TRUE
GROUP BY o.id_orang;
