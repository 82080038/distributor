-- =====================================================
-- DATABASE LOGISTICS - Fleet & Route Management
-- =====================================================
-- Created: 19 Januari 2026
-- Purpose: Manajemen logistik lengkap, fleet, dan optimasi rute
-- Integration: Link ke aplikasi, barang, orang, alamat_db

CREATE DATABASE IF NOT EXISTS logistics CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE logistics;

-- =====================================================
-- 1. VEHICLES - Master Kendaraan
-- =====================================================
CREATE TABLE vehicles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Kode kendaraan',
    license_plate VARCHAR(20) UNIQUE NOT NULL COMMENT 'Nomor polisi',
    vehicle_type ENUM('truck', 'van', 'pickup', 'motorcycle', 'car', 'container', 'other') NOT NULL,
    brand VARCHAR(50) NOT NULL COMMENT 'Merek kendaraan',
    model VARCHAR(50) NOT NULL COMMENT 'Model kendaraan',
    year_manufacture SMALLINT NOT NULL COMMENT 'Tahun pembuatan',
    color VARCHAR(30) NULL COMMENT 'Warna',
    chassis_number VARCHAR(50) UNIQUE NULL COMMENT 'Nomor rangka',
    engine_number VARCHAR(50) UNIQUE NULL COMMENT 'Nomor mesin',
    vehicle_status ENUM('active', 'maintenance', 'repair', 'retired', 'accident') DEFAULT 'active',
    ownership_type ENUM('owned', 'rented', 'leased', 'contracted') DEFAULT 'owned',
    capacity_weight DECIMAL(8,2) NULL COMMENT 'Kapasitas muatan (ton)',
    capacity_volume DECIMAL(8,2) NULL COMMENT 'Kapasitas volume (m³)',
    fuel_type ENUM('diesel', 'gasoline', 'electric', 'hybrid', 'cng', 'lpg') DEFAULT 'diesel',
    fuel_capacity DECIMAL(8,2) NULL COMMENT 'Kapasitas tangki (liter)',
    average_consumption DECIMAL(6,2) NULL COMMENT 'Konsumsi rata-rata (km/l)',
    insurance_number VARCHAR(50) NULL COMMENT 'Nomor asuransi',
    insurance_expiry DATE NULL COMMENT 'Masa berlaku asuransi',
    road_tax_expiry DATE NULL COMMENT 'Masa berlaku pajak jalan',
    gps_device_id VARCHAR(50) NULL COMMENT 'ID GPS device',
    is_gps_enabled BOOLEAN DEFAULT FALSE,
    last_maintenance_date DATE NULL COMMENT 'Terakhir maintenance',
    next_maintenance_date DATE NULL COMMENT 'Maintenance berikutnya',
    notes TEXT NULL COMMENT 'Catatan kendaraan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_vehicle_code (vehicle_code),
    INDEX idx_license_plate (license_plate),
    INDEX idx_vehicle_type (vehicle_type),
    INDEX idx_brand (brand),
    INDEX idx_vehicle_status (vehicle_status),
    INDEX idx_ownership_type (ownership_type),
    INDEX idx_fuel_type (fuel_type),
    INDEX idx_is_gps_enabled (is_gps_enabled),
    INDEX idx_next_maintenance_date (next_maintenance_date)
) ENGINE=InnoDB COMMENT='Master data kendaraan';

-- =====================================================
-- 2. DRIVERS - Master Driver
-- =====================================================
CREATE TABLE drivers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    driver_code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Kode driver',
    person_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke orang.persons',
    driver_license_number VARCHAR(30) UNIQUE NOT NULL COMMENT 'Nomor SIM',
    driver_license_type ENUM('A', 'B1', 'B2', 'C', 'D', 'SIM1', 'SIM2', 'International') NOT NULL,
    driver_license_expiry DATE NOT NULL COMMENT 'Masa berlaku SIM',
    driver_status ENUM('active', 'on_leave', 'suspended', 'terminated') DEFAULT 'active',
    employment_type ENUM('permanent', 'contract', 'daily', 'part_time') DEFAULT 'permanent',
    hire_date DATE NOT NULL COMMENT 'Tanggal mulai kerja',
    base_salary DECIMAL(10,2) DEFAULT 0 COMMENT 'Gaji pokok',
    transport_allowance DECIMAL(10,2) DEFAULT 0 COMMENT 'Tunjangan transport',
    meal_allowance DECIMAL(10,2) DEFAULT 0 COMMENT 'Tunjangan makan',
    overtime_rate DECIMAL(5,2) DEFAULT 1.50 COMMENT 'Lembur rate',
    max_working_hours DECIMAL(4,2) DEFAULT 8.00 COMMENT 'Maks jam kerja',
    emergency_contact_name VARCHAR(100) NULL COMMENT 'Kontak darurat',
    emergency_contact_phone VARCHAR(20) NULL COMMENT 'Telepon darurat',
    medical_checkup_expiry DATE NULL COMMENT 'Masa berlaku checkup medis',
    training_certificates JSON NULL COMMENT 'Sertifikat training',
    performance_rating ENUM('excellent', 'good', 'average', 'poor') DEFAULT 'average',
    total_deliveries INT DEFAULT 0 COMMENT 'Total pengiriman',
    successful_deliveries INT DEFAULT 0 COMMENT 'Pengiriman berhasil',
    success_rate DECIMAL(5,2) GENERATED ALWAYS AS (CASE WHEN total_deliveries > 0 THEN (successful_deliveries / total_deliveries * 100) ELSE 0 END) STORED,
    notes TEXT NULL COMMENT 'Catatan driver',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (person_id) REFERENCES orang.persons(id) ON DELETE RESTRICT,
    
    INDEX idx_driver_code (driver_code),
    INDEX idx_person_id (person_id),
    INDEX idx_driver_license_number (driver_license_number),
    INDEX idx_driver_status (driver_status),
    INDEX idx_employment_type (employment_type),
    INDEX idx_hire_date (hire_date),
    INDEX idx_performance_rating (performance_rating),
    INDEX idx_success_rate (success_rate)
) ENGINE=InnoDB COMMENT='Master data driver';

-- =====================================================
-- 3. ROUTES - Master Rute
-- =====================================================
CREATE TABLE routes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    route_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode rute',
    route_name VARCHAR(100) NOT NULL COMMENT 'Nama rute',
    route_type ENUM('regular', 'express', 'emergency', 'special') DEFAULT 'regular',
    origin_warehouse_id BIGINT UNSIGNED NOT NULL COMMENT 'Gudang asal',
    origin_address VARCHAR(255) NOT NULL COMMENT 'Alamat asal lengkap',
    destination_type ENUM('customer', 'warehouse', 'distribution_center', 'other') NOT NULL,
    destination_address VARCHAR(255) NOT NULL COMMENT 'Alamat tujuan',
    distance_km DECIMAL(8,2) NOT NULL COMMENT 'Jarak (km)',
    estimated_duration_minutes INT NOT NULL COMMENT 'Estimasi durasi (menit)',
    route_complexity ENUM('easy', 'medium', 'difficult', 'very_difficult') DEFAULT 'medium',
    traffic_condition ENUM('low', 'medium', 'high', 'very_high') DEFAULT 'medium',
    road_type ENUM('highway', 'city', 'rural', 'mountain', 'mixed') DEFAULT 'mixed',
    toll_cost DECIMAL(8,2) DEFAULT 0 COMMENT 'Biaya tol',
    fuel_cost_estimate DECIMAL(8,2) DEFAULT 0 COMMENT 'Estimasi biaya bahan bakar',
    other_cost_estimate DECIMAL(8,2) DEFAULT 0 COMMENT 'Estimasi biaya lain',
    total_cost_estimate DECIMAL(8,2) GENERATED ALWAYS AS (toll_cost + fuel_cost_estimate + other_cost_estimate) STORED,
    is_active BOOLEAN DEFAULT TRUE,
    seasonal_adjustment JSON NULL COMMENT 'Penyesuaian musiman',
    notes TEXT NULL COMMENT 'Catatan rute',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_route_code (route_code),
    INDEX idx_route_name (route_name),
    INDEX idx_route_type (route_type),
    INDEX idx_origin_warehouse_id (origin_warehouse_id),
    INDEX idx_destination_type (destination_type),
    INDEX idx_distance_km (distance_km),
    INDEX idx_estimated_duration_minutes (estimated_duration_minutes),
    INDEX idx_route_complexity (route_complexity),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Master data rute pengiriman';

-- =====================================================
-- 4. ROUTE_WAYPOINTS - Waypoints Rute
-- =====================================================
CREATE TABLE route_waypoints (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    route_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke routes',
    sequence_order INT NOT NULL COMMENT 'Urutan waypoint',
    waypoint_type ENUM('checkpoint', 'pickup', 'delivery', 'rest_stop', 'fuel_stop', 'custom') DEFAULT 'checkpoint',
    waypoint_name VARCHAR(100) NOT NULL COMMENT 'Nama waypoint',
    address VARCHAR(255) NOT NULL COMMENT 'Alamat waypoint',
    latitude DECIMAL(10,8) NOT NULL COMMENT 'Koordinat latitude',
    longitude DECIMAL(11,8) NOT NULL COMMENT 'Koordinat longitude',
    village_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.villages',
    district_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.districts',
    regency_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.regencies',
    province_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.provinces',
    postal_code VARCHAR(10) NULL COMMENT 'Kode pos',
    contact_person VARCHAR(100) NULL COMMENT 'Kontak person',
    contact_phone VARCHAR(20) NULL COMMENT 'Telepon kontak',
    estimated_arrival TIME NULL COMMENT 'Estimasi waktu tiba',
    service_duration_minutes INT DEFAULT 0 COMMENT 'Durasi layanan (menit)',
    distance_from_previous DECIMAL(8,2) DEFAULT 0 COMMENT 'Jarak dari waypoint sebelumnya',
    is_mandatory BOOLEAN DEFAULT TRUE COMMENT 'Wajib dikunjungi',
    is_active BOOLEAN DEFAULT TRUE,
    notes VARCHAR(255) NULL COMMENT 'Catatan waypoint',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE CASCADE,
    
    INDEX idx_route_id (route_id),
    INDEX idx_sequence_order (sequence_order),
    INDEX idx_waypoint_type (waypoint_type),
    INDEX idx_village_id (village_id),
    INDEX idx_district_id (district_id),
    INDEX idx_regency_id (regency_id),
    INDEX idx_province_id (province_id),
    INDEX idx_is_mandatory (is_mandatory),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Waypoints dalam rute';

-- =====================================================
-- 5. ROUTE_OPTIMIZATION - Optimasi Rute
-- =====================================================
CREATE TABLE route_optimization (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    optimization_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode optimasi',
    optimization_name VARCHAR(100) NOT NULL COMMENT 'Nama optimasi',
    optimization_date DATE NOT NULL COMMENT 'Tanggal optimasi',
    optimization_type ENUM('single_vehicle', 'multi_vehicle', 'time_window', 'capacity', 'cost') DEFAULT 'multi_vehicle',
    vehicle_type_filter JSON NULL COMMENT 'Filter tipe kendaraan',
    capacity_constraints JSON NULL COMMENT 'Constraint kapasitas',
    time_window_constraints JSON NULL COMMENT 'Constraint time window',
    cost_parameters JSON NULL COMMENT 'Parameter biaya',
    optimization_algorithm ENUM('nearest_neighbor', 'savings', 'genetic', 'simulated_annealing', 'custom') DEFAULT 'savings',
    total_distance_km DECIMAL(10,2) DEFAULT 0 COMMENT 'Total jarak hasil optimasi',
    total_duration_minutes INT DEFAULT 0 COMMENT 'Total durasi hasil optimasi',
    total_cost DECIMAL(12,2) DEFAULT 0 COMMENT 'Total biaya hasil optimasi',
    vehicles_used INT DEFAULT 0 COMMENT 'Jumlah kendaraan digunakan',
    deliveries_planned INT DEFAULT 0 COMMENT 'Jumlah pengiriman direncanakan',
    optimization_score DECIMAL(5,2) DEFAULT 0 COMMENT 'Skor optimasi (0-100)',
    status ENUM('planning', 'optimizing', 'completed', 'failed', 'cancelled') DEFAULT 'planning',
    optimization_time_seconds INT NULL COMMENT 'Waktu proses optimasi (detik)',
    notes TEXT NULL COMMENT 'Catatan optimasi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_optimization_code (optimization_code),
    INDEX idx_optimization_date (optimization_date),
    INDEX idx_optimization_type (optimization_type),
    INDEX idx_optimization_algorithm (optimization_algorithm),
    INDEX idx_total_distance_km (total_distance_km),
    INDEX idx_total_cost (total_cost),
    INDEX idx_optimization_score (optimization_score),
    INDEX idx_status (status)
) ENGINE=InnoDB COMMENT='Hasil optimasi rute';

-- =====================================================
-- 6. FUEL_MANAGEMENT - Manajemen Bahan Bakar
-- =====================================================
CREATE TABLE fuel_management (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode transaksi',
    vehicle_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke vehicles',
    driver_id BIGINT UNSIGNED NULL COMMENT 'Link ke drivers',
    transaction_type ENUM('fueling', 'fuel_adjustment', 'theft', 'leak', 'other') NOT NULL,
    transaction_date DATETIME NOT NULL COMMENT 'Tanggal transaksi',
    fuel_station VARCHAR(100) NULL COMMENT 'SPBU',
    fuel_type ENUM('diesel', 'gasoline', 'cng', 'lpg') NOT NULL,
    fuel_quantity DECIMAL(8,2) NOT NULL COMMENT 'Jumlah bahan bakar (liter)',
    fuel_price_per_liter DECIMAL(8,2) NOT NULL COMMENT 'Harga per liter',
    total_cost DECIMAL(10,2) GENERATED ALWAYS AS (fuel_quantity * fuel_price_per_liter) STORED,
    odometer_reading INT NULL COMMENT 'Odometer saat transaksi',
    fuel_level_before DECIMAL(5,2) NULL COMMENT 'Level bahan bakar sebelum (%)',
    fuel_level_after DECIMAL(5,2) NULL COMMENT 'Level bahan bakar sesudah (%)',
    consumption_rate DECIMAL(6,2) NULL COMMENT 'Konsumsi (km/l)',
    payment_method ENUM('cash', 'card', 'fleet_card', 'company_account', 'other') DEFAULT 'cash',
    receipt_number VARCHAR(50) NULL COMMENT 'Nomor struk',
    receipt_image_url VARCHAR(500) NULL COMMENT 'URL foto struk',
    is_fraud_suspicious BOOLEAN DEFAULT FALSE COMMENT 'Mencurigakan fraud',
    notes VARCHAR(255) NULL COMMENT 'Catatan transaksi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE RESTRICT,
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE SET NULL,
    
    INDEX idx_transaction_code (transaction_code),
    INDEX idx_vehicle_id (vehicle_id),
    INDEX idx_driver_id (driver_id),
    INDEX idx_transaction_type (transaction_type),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_fuel_type (fuel_type),
    INDEX idx_fuel_quantity (fuel_quantity),
    INDEX idx_total_cost (total_cost),
    INDEX idx_is_fraud_suspicious (is_fraud_suspicious)
) ENGINE=InnoDB COMMENT='Manajemen bahan bakar kendaraan';

-- =====================================================
-- 7. MAINTENANCE_SCHEDULE - Jadwal Maintenance
-- =====================================================
CREATE TABLE maintenance_schedule (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    schedule_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode jadwal',
    vehicle_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke vehicles',
    maintenance_type ENUM('routine', 'preventive', 'corrective', 'emergency', 'inspection') NOT NULL,
    maintenance_category ENUM('engine', 'transmission', 'brakes', 'tires', 'electrical', 'body', 'other') NOT NULL,
    description TEXT NOT NULL COMMENT 'Deskripsi maintenance',
    scheduled_date DATE NOT NULL COMMENT 'Tanggal rencana',
    scheduled_duration_hours DECIMAL(4,2) DEFAULT 2.00 COMMENT 'Estimasi durasi (jam)',
    priority_level TINYINT DEFAULT 3 COMMENT 'Prioritas (1=highest, 5=lowest)',
    odometer_trigger INT NULL COMMENT 'Trigger berdasarkan odometer',
    time_trigger_days INT NULL COMMENT 'Trigger berdasarkan hari',
    estimated_cost DECIMAL(10,2) DEFAULT 0 COMMENT 'Estimasi biaya',
    actual_cost DECIMAL(10,2) NULL COMMENT 'Biaya aktual',
    service_provider VARCHAR(100) NULL COMMENT 'Provider jasa',
    parts_needed JSON NULL COMMENT 'Spare parts yang dibutuhkan',
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled', 'postponed') DEFAULT 'scheduled',
    actual_start_date DATETIME NULL COMMENT 'Waktu mulai aktual',
    actual_end_date DATETIME NULL COMMENT 'Waktu selesai aktual',
    actual_duration_hours DECIMAL(4,2) NULL COMMENT 'Durasi aktual (jam)',
    next_maintenance_date DATE NULL COMMENT 'Maintenance berikutnya',
    maintenance_notes TEXT NULL COMMENT 'Catatan maintenance',
    quality_rating ENUM('excellent', 'good', 'average', 'poor') NULL COMMENT 'Rating kualitas',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    
    INDEX idx_schedule_code (schedule_code),
    INDEX idx_vehicle_id (vehicle_id),
    INDEX idx_maintenance_type (maintenance_type),
    INDEX idx_maintenance_category (maintenance_category),
    INDEX idx_scheduled_date (scheduled_date),
    INDEX idx_priority_level (priority_level),
    INDEX idx_status (status),
    INDEX idx_next_maintenance_date (next_maintenance_date)
) ENGINE=InnoDB COMMENT='Jadwal maintenance kendaraan';

-- =====================================================
-- 8. GPS_TRACKING - GPS Tracking
-- =====================================================
CREATE TABLE gps_tracking (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicle_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke vehicles',
    driver_id BIGINT UNSIGNED NULL COMMENT 'Link ke drivers',
    tracking_date DATETIME NOT NULL COMMENT 'Waktu tracking',
    latitude DECIMAL(10,8) NOT NULL COMMENT 'Koordinat latitude',
    longitude DECIMAL(11,8) NOT NULL COMMENT 'Koordinat longitude',
    altitude DECIMAL(8,2) NULL COMMENT 'Ketinggian (meter)',
    speed_kmh DECIMAL(6,2) NULL COMMENT 'Kecepatan (km/jam)',
    heading DECIMAL(5,2) NULL COMMENT 'Arah (derajat)',
    odometer_reading INT NULL COMMENT 'Odometer',
    engine_status ENUM('on', 'off', 'idle') DEFAULT 'off',
    vehicle_status ENUM('moving', 'stopped', 'parked', 'loading', 'unloading') DEFAULT 'parked',
    fuel_level DECIMAL(5,2) NULL COMMENT 'Level bahan bakar (%)',
    engine_temperature DECIMAL(5,2) NULL COMMENT 'Suhu mesin (°C)',
    gps_accuracy DECIMAL(6,2) NULL COMMENT 'Akurasi GPS (meter)',
    satellite_count INT NULL COMMENT 'Jumlah satelit',
    is_valid_location BOOLEAN DEFAULT TRUE COMMENT 'Lokasi valid',
    address_geocoded VARCHAR(255) NULL COMMENT 'Alamat dari geocoding',
    village_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.villages',
    district_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.districts',
    regency_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.regencies',
    province_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.provinces',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE SET NULL,
    
    INDEX idx_vehicle_id (vehicle_id),
    INDEX idx_driver_id (driver_id),
    INDEX idx_tracking_date (tracking_date),
    INDEX idx_latitude (latitude),
    INDEX idx_longitude (longitude),
    INDEX idx_speed_kmh (speed_kmh),
    INDEX idx_vehicle_status (vehicle_status),
    INDEX idx_engine_status (engine_status),
    INDEX idx_village_id (village_id),
    INDEX idx_district_id (district_id),
    INDEX idx_regency_id (regency_id),
    INDEX idx_province_id (province_id)
) ENGINE=InnoDB COMMENT='Data tracking GPS kendaraan';

-- =====================================================
-- 9. DELIVERY_PERFORMANCE - Performa Pengiriman
-- =====================================================
CREATE TABLE delivery_performance (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    delivery_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke aplikasi.deliveries',
    vehicle_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke vehicles',
    driver_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke drivers',
    route_id BIGINT UNSIGNED NULL COMMENT 'Link ke routes',
    planned_start_time DATETIME NOT NULL COMMENT 'Waktu mulai rencana',
    actual_start_time DATETIME NULL COMMENT 'Waktu mulai aktual',
    planned_completion_time DATETIME NOT NULL COMMENT 'Waktu selesai rencana',
    actual_completion_time DATETIME NULL COMMENT 'Waktu selesai aktual',
    planned_distance_km DECIMAL(8,2) NOT NULL COMMENT 'Jarak rencana',
    actual_distance_km DECIMAL(8,2) DEFAULT 0 COMMENT 'Jarak aktual',
    planned_duration_minutes INT NOT NULL COMMENT 'Durasi rencana',
    actual_duration_minutes INT DEFAULT 0 COMMENT 'Durasi aktual',
    on_time_performance ENUM('early', 'on_time', 'late', 'very_late') DEFAULT 'on_time',
    delivery_status ENUM('preparing', 'in_transit', 'delivered', 'failed', 'cancelled') DEFAULT 'preparing',
    total_deliveries INT DEFAULT 0 COMMENT 'Total pengiriman',
    successful_deliveries INT DEFAULT 0 COMMENT 'Pengiriman berhasil',
    failed_deliveries INT DEFAULT 0 COMMENT 'Pengiriman gagal',
    success_rate DECIMAL(5,2) GENERATED ALWAYS AS (CASE WHEN total_deliveries > 0 THEN (successful_deliveries / total_deliveries * 100) ELSE 0 END) STORED,
    fuel_consumed DECIMAL(8,2) DEFAULT 0 COMMENT 'Bahan bakar dikonsumsi (liter)',
    fuel_cost DECIMAL(10,2) DEFAULT 0 COMMENT 'Biaya bahan bakar',
    total_cost DECIMAL(10,2) DEFAULT 0 COMMENT 'Total biaya pengiriman',
    customer_rating DECIMAL(3,2) NULL COMMENT 'Rating customer (1-5)',
    issues_encountered JSON NULL COMMENT 'Masalah yang dihadapi',
    performance_notes TEXT NULL COMMENT 'Catatan performa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (delivery_id) REFERENCES aplikasi.deliveries(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE RESTRICT,
    FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE RESTRICT),
    FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE SET NULL,
    
    INDEX idx_delivery_id (delivery_id),
    INDEX idx_vehicle_id (vehicle_id),
    INDEX idx_driver_id (driver_id),
    INDEX idx_route_id (route_id),
    INDEX idx_planned_start_time (planned_start_time),
    INDEX idx_actual_start_time (actual_start_time),
    INDEX idx_on_time_performance (on_time_performance),
    INDEX idx_delivery_status (delivery_status),
    INDEX idx_success_rate (success_rate),
    INDEX idx_customer_rating (customer_rating)
) ENGINE=InnoDB COMMENT='Tracking performa pengiriman';

-- =====================================================
-- 10. SHIPPING_PARTNERS - Partner Pengiriman
-- =====================================================
CREATE TABLE shipping_partners (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    partner_code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Kode partner',
    partner_name VARCHAR(100) NOT NULL COMMENT 'Nama partner',
    partner_type ENUM('courier', 'freight_forwarder', 'logistics_company', 'individual', 'other') NOT NULL,
    service_type ENUM('same_day', 'next_day', 'regular', 'express', 'economy', 'international') DEFAULT 'regular',
    contact_person VARCHAR(100) NULL COMMENT 'Nama kontak',
    phone VARCHAR(20) NULL COMMENT 'Telepon',
    email VARCHAR(255) NULL COMMENT 'Email',
    website VARCHAR(255) NULL COMMENT 'Website',
    village_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.villages',
    district_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.districts',
    regency_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.regencies',
    province_id INT UNSIGNED NULL COMMENT 'Link ke alamat_db.provinces',
    address VARCHAR(255) NULL COMMENT 'Alamat lengkap',
    postal_code VARCHAR(10) NULL COMMENT 'Kode pos',
    services_offered JSON NULL COMMENT 'Layanan yang ditawarkan',
    coverage_areas JSON NULL COMMENT 'Area coverage',
    pricing_model ENUM('per_kg', 'per_km', 'per_package', 'flat_rate', 'custom') DEFAULT 'per_kg',
    base_rate DECIMAL(8,2) DEFAULT 0 COMMENT 'Tarif dasar',
    rate_per_km DECIMAL(6,2) DEFAULT 0 COMMENT 'Tarif per km',
    rate_per_kg DECIMAL(6,2) DEFAULT 0 COMMENT 'Tarif per kg',
    max_weight_kg DECIMAL(8,2) DEFAULT 0 COMMENT 'Maksimum berat (kg)',
    max_volume_m3 DECIMAL(8,2) DEFAULT 0 COMMENT 'Maksimum volume (m³)',
    delivery_time_estimate VARCHAR(100) NULL COMMENT 'Estimasi waktu pengiriman',
    tracking_available BOOLEAN DEFAULT FALSE COMMENT 'Tracking tersedia',
    insurance_available BOOLEAN DEFAULT FALSE COMMENT 'Asuransi tersedia',
    quality_rating ENUM('excellent', 'good', 'average', 'poor') DEFAULT 'average',
    contract_start_date DATE NULL COMMENT 'Mulai kontrak',
    contract_end_date DATE NULL COMMENT 'Akhir kontrak',
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT NULL COMMENT 'Catatan partner',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_partner_code (partner_code),
    INDEX idx_partner_name (partner_name),
    INDEX idx_partner_type (partner_type),
    INDEX idx_service_type (service_type),
    INDEX idx_village_id (village_id),
    INDEX idx_district_id (district_id),
    INDEX idx_regency_id (regency_id),
    INDEX idx_province_id (province_id),
    INDEX idx_pricing_model (pricing_model),
    INDEX idx_quality_rating (quality_rating),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Data partner pengiriman eksternal';

-- =====================================================
-- INSERT DEFAULT DATA
-- =====================================================

-- Default vehicle types
INSERT INTO vehicles (vehicle_code, license_plate, vehicle_type, brand, model, year_manufacture, capacity_weight, fuel_type, vehicle_status) VALUES
('VH001', 'B1234ABC', 'truck', 'Isuzu', 'Elf', 2022, 3.00, 'diesel', 'active'),
('VH002', 'B5678DEF', 'van', 'Suzuki', 'Carry', 2023, 1.00, 'gasoline', 'active'),
('VH003', 'B9012GHI', 'pickup', 'Toyota', 'Hilux', 2021, 1.50, 'diesel', 'active'),
('VH004', 'B3456JKL', 'motorcycle', 'Honda', 'Beat', 2023, 0.05, 'gasoline', 'active');

-- Default routes
INSERT INTO routes (route_code, route_name, route_type, origin_warehouse_id, origin_address, destination_type, destination_address, distance_km, estimated_duration_minutes) VALUES
('RT001', 'Jakarta - Tangerang', 'regular', 1, 'Jakarta Pusat', 'customer', 'Tangerang Selatan', 35.5, 90),
('RT002', 'Jakarta - Bekasi', 'regular', 1, 'Jakarta Pusat', 'customer', 'Bekasi', 42.3, 120),
('RT003', 'Jakarta - Depok', 'express', 1, 'Jakarta Pusat', 'customer', 'Depok', 28.7, 75);

-- =====================================================
-- VIEWS untuk logistics analytics
-- =====================================================

-- View untuk vehicle utilization
CREATE VIEW v_vehicle_utilization AS
SELECT 
    v.*,
    COUNT(DISTINCT DATE(gt.tracking_date)) as active_days,
    COUNT(DISTINCT gt.tracking_date) as total_tracking_days,
    SUM(CASE WHEN gt.vehicle_status = 'moving' THEN 1 ELSE 0 END) as moving_periods,
    SUM(CASE WHEN gt.vehicle_status = 'stopped' THEN 1 ELSE 0 END) as stopped_periods,
    AVG(gt.speed_kmh) as avg_speed,
    MAX(gt.speed_kmh) as max_speed,
    SUM(gt.distance_km) as total_distance,
    COUNT(DISTINCT dp.delivery_id) as total_deliveries,
    AVG(dp.success_rate) as avg_success_rate
FROM vehicles v
LEFT JOIN gps_tracking gt ON v.id = gt.vehicle_id 
    AND gt.tracking_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
LEFT JOIN delivery_performance dp ON v.id = dp.vehicle_id
    AND dp.actual_start_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY v.id;

-- View untuk driver performance
CREATE VIEW v_driver_performance AS
SELECT 
    d.*,
    p.full_name,
    COUNT(DISTINCT dp.delivery_id) as total_deliveries,
    SUM(dp.successful_deliveries) as successful_deliveries,
    AVG(dp.success_rate) as avg_success_rate,
    AVG(dp.actual_duration_minutes) as avg_delivery_time,
    COUNT(DISTINCT fm.transaction_date) as fuel_transactions,
    SUM(fm.fuel_quantity) as total_fuel_consumed,
    AVG(fm.consumption_rate) as avg_fuel_consumption,
    AVG(dp.customer_rating) as avg_customer_rating,
    COUNT(DISTINCT ms.id) as maintenance_count
FROM drivers d
JOIN orang.persons p ON d.person_id = p.id
LEFT JOIN delivery_performance dp ON d.id = dp.driver_id
LEFT JOIN fuel_management fm ON d.id = fm.driver_id
LEFT JOIN maintenance_schedule ms ON d.driver_code = ms.created_by
GROUP BY d.id;
