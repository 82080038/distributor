sql_statement
INSERT INTO roles (id, name) VALUES (1, 'owner');
INSERT INTO roles (id, name) VALUES (2, 'manager');
INSERT INTO roles (id, name) VALUES (3, 'staff');


sql_statement
INSERT INTO user_accounts (id_user, id_orang, username, email, password_hash, role_id, status_aktif, created_at) VALUES (1, 1, 'admin', 'admin@distributor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, '2026-01-21 20:37:01');
INSERT INTO user_accounts (id_user, id_orang, username, email, password_hash, role_id, status_aktif, created_at) VALUES (2, 2, 'manager', 'manager@distributor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 1, '2026-01-21 20:37:01');
INSERT INTO user_accounts (id_user, id_orang, username, email, password_hash, role_id, status_aktif, created_at) VALUES (3, 3, 'staff', 'staff@distributor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 1, '2026-01-21 20:37:01');


sql_statement
INSERT INTO orang (id_orang, perusahaan_id, nama_lengkap, alamat, kontak, tipe_alamat, is_customer, is_supplier, is_active, created_at) VALUES (1, 1, 'Admin User', 'Jakarta Selatan', '08123456789', 'rumah', 0, 0, 1, '2026-01-21 20:36:54');
INSERT INTO orang (id_orang, perusahaan_id, nama_lengkap, alamat, kontak, tipe_alamat, is_customer, is_supplier, is_active, created_at) VALUES (2, 1, 'Test Customer', 'Jakarta Utara', '08123456788', 'kantor', 1, 0, 1, '2026-01-21 20:36:54');
INSERT INTO orang (id_orang, perusahaan_id, nama_lengkap, alamat, kontak, tipe_alamat, is_customer, is_supplier, is_active, created_at) VALUES (3, 1, 'Test Supplier', 'Jakarta Barat', '08123456787', 'gudang', 0, 1, 1, '2026-01-21 20:36:54');


sql_statement
INSERT INTO perusahaan (id_perusahaan, nama_perusahaan, alamat, kontak, created_at) VALUES (1, 'PT Distributor Utama', 'Jakarta Pusat', '021-12345678', '2026-01-21 20:36:48');
