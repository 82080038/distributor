USE distributor;

CREATE TABLE IF NOT EXISTS perusahaan (
    id_perusahaan INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_perusahaan VARCHAR(150) NOT NULL,
    alamat TEXT,
    kontak VARCHAR(50),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orang (
    id_orang INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    perusahaan_id INT UNSIGNED NULL,
    nama_lengkap VARCHAR(150) NOT NULL,
    alamat TEXT,
    kontak VARCHAR(50),
    province_id INT UNSIGNED NULL,
    regency_id INT UNSIGNED NULL,
    district_id INT UNSIGNED NULL,
    village_id INT UNSIGNED NULL,
    postal_code VARCHAR(10),
    is_supplier TINYINT(1) NOT NULL DEFAULT 0,
    is_customer TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orang_perusahaan FOREIGN KEY (perusahaan_id) REFERENCES perusahaan(id_perusahaan)
);

CREATE TABLE IF NOT EXISTS user (
    id_user INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_orang INT UNSIGNED NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    branch_id INT UNSIGNED NULL,
    status_aktif TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_user_orang (id_orang),
    CONSTRAINT fk_user_orang FOREIGN KEY (id_orang) REFERENCES orang(id_orang),
    CONSTRAINT fk_user_role FOREIGN KEY (role_id) REFERENCES roles(id),
    CONSTRAINT fk_user_branch FOREIGN KEY (branch_id) REFERENCES branches(id)
);

INSERT INTO perusahaan (nama_perusahaan)
SELECT 'Perusahaan Utama'
WHERE NOT EXISTS (SELECT 1 FROM perusahaan);

ALTER TABLE branches
    ADD COLUMN perusahaan_id INT UNSIGNED NULL AFTER id;

UPDATE branches
SET perusahaan_id = (SELECT id_perusahaan FROM perusahaan LIMIT 1)
WHERE perusahaan_id IS NULL;

ALTER TABLE branches
    MODIFY COLUMN perusahaan_id INT UNSIGNED NOT NULL;

ALTER TABLE branches
    ADD CONSTRAINT fk_branches_perusahaan FOREIGN KEY (perusahaan_id) REFERENCES perusahaan(id_perusahaan);

INSERT INTO orang (id_orang, nama_lengkap, alamat, kontak, province_id, regency_id, district_id, village_id, postal_code, created_at, updated_at)
SELECT id, name, street_address, phone, province_id, regency_id, district_id, village_id, postal_code, created_at, updated_at
FROM users
WHERE id NOT IN (SELECT id_orang FROM orang);

INSERT INTO user (id_user, id_orang, username, email, password_hash, role_id, branch_id, status_aktif, created_at, updated_at)
SELECT id, id, username, email, password_hash, role_id, branch_id, is_active, created_at, updated_at
FROM users
WHERE id NOT IN (SELECT id_user FROM user);

ALTER TABLE purchases
    DROP FOREIGN KEY fk_purchases_user;

ALTER TABLE sales
    DROP FOREIGN KEY fk_sales_user;

ALTER TABLE purchases
    ADD CONSTRAINT fk_purchases_user FOREIGN KEY (created_by) REFERENCES user(id_user);

ALTER TABLE sales
    ADD CONSTRAINT fk_sales_user FOREIGN KEY (created_by) REFERENCES user(id_user);
