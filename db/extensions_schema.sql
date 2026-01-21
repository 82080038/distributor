-- =====================================================
-- DATABASE EXTENSIONS - Future-Proof Schema Extensions
-- =====================================================
-- Created: 19 Januari 2026
-- Purpose: Extensions untuk mendukung berbagai aplikasi masa depan
-- Principle: TIDAK mengubah struktur yang ada, hanya menambahkan extensions

CREATE DATABASE IF NOT EXISTS extensions CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE extensions;

-- =====================================================
-- 1. ENTITY_EXTENSIONS - Dynamic Entity Extensions
-- =====================================================
CREATE TABLE entity_extensions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entity_type VARCHAR(50) NOT NULL COMMENT 'Tipe entity (person, product, dll)',
    entity_id BIGINT UNSIGNED NOT NULL COMMENT 'ID entity',
    extension_key VARCHAR(100) NOT NULL COMMENT 'Key extension',
    extension_value TEXT NULL COMMENT 'Value extension',
    data_type ENUM('string', 'number', 'boolean', 'date', 'json', 'array') DEFAULT 'string',
    is_encrypted BOOLEAN DEFAULT FALSE COMMENT 'Data di-encrypt',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    UNIQUE KEY unique_entity_extension (entity_type, entity_id, extension_key),
    INDEX idx_entity_type (entity_type),
    INDEX idx_entity_id (entity_id),
    INDEX idx_extension_key (extension_key),
    INDEX idx_data_type (data_type),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB COMMENT='Dynamic extensions untuk semua entities';

-- =====================================================
-- 2. APPLICATION_REGISTRY - Registry Aplikasi
-- =====================================================
CREATE TABLE application_registry (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    app_code VARCHAR(50) UNIQUE NOT NULL COMMENT 'Kode aplikasi',
    app_name VARCHAR(100) NOT NULL COMMENT 'Nama aplikasi',
    app_category ENUM('government', 'business', 'education', 'health', 'finance', 'retail', 'manufacturing', 'service', 'other') NOT NULL,
    app_type ENUM('web', 'mobile', 'desktop', 'api', 'microservice', 'integration') NOT NULL,
    description TEXT NULL COMMENT 'Deskripsi aplikasi',
    version VARCHAR(20) DEFAULT '1.0.0' COMMENT 'Versi aplikasi',
    status ENUM('development', 'testing', 'staging', 'production', 'maintenance', 'deprecated') DEFAULT 'development',
    owner_name VARCHAR(100) NOT NULL COMMENT 'Nama pemilik/developer',
    owner_contact VARCHAR(255) NULL COMMENT 'Kontak pemilik',
    database_schemas JSON NULL COMMENT 'Schema yang digunakan',
    features JSON NULL COMMENT 'Fitur-fitur aplikasi',
    dependencies JSON NULL COMMENT 'Dependencies aplikasi',
    api_endpoints JSON NULL COMMENT 'API endpoints',
    webhooks JSON NULL COMMENT 'Webhook URLs',
    is_active BOOLEAN DEFAULT TRUE,
    is_public BOOLEAN DEFAULT FALSE COMMENT 'Aplikasi publik',
    launch_date DATE NULL COMMENT 'Tanggal launch',
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_app_code (app_code),
    INDEX idx_app_name (app_name),
    INDEX idx_app_category (app_category),
    INDEX idx_app_type (app_type),
    INDEX idx_status (status),
    INDEX idx_owner_name (owner_name),
    INDEX idx_is_active (is_active),
    INDEX idx_is_public (is_public),
    INDEX idx_launch_date (launch_date)
) ENGINE=InnoDB COMMENT='Registry semua aplikasi';

-- =====================================================
-- 3. TENANT_MANAGEMENT - Multi-Tenant Support
-- =====================================================
CREATE TABLE tenant_management (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_code VARCHAR(50) UNIQUE NOT NULL COMMENT 'Kode tenant',
    tenant_name VARCHAR(100) NOT NULL COMMENT 'Nama tenant',
    tenant_type ENUM('organization', 'individual', 'government', 'enterprise', 'startup', 'non_profit') NOT NULL,
    tenant_category ENUM('polri', 'koperasi', 'puskesmas', 'desa', 'school', 'company', 'other') NOT NULL,
    parent_tenant_id BIGINT UNSIGNED NULL COMMENT 'Tenant induk',
    description TEXT NULL COMMENT 'Deskripsi tenant',
    domain VARCHAR(255) NULL COMMENT 'Domain tenant',
    subdomain VARCHAR(100) NULL COMMENT 'Subdomain',
    database_name VARCHAR(100) NULL COMMENT 'Database khusus tenant',
    storage_path VARCHAR(500) NULL COMMENT 'Path storage tenant',
    configuration JSON NULL COMMENT 'Konfigurasi khusus tenant',
    subscription_plan ENUM('free', 'basic', 'premium', 'enterprise', 'custom') DEFAULT 'basic',
    max_users INT DEFAULT 10 COMMENT 'Maksimum users',
    max_storage_gb INT DEFAULT 10 COMMENT 'Maksimum storage (GB)',
    features_allowed JSON NULL COMMENT 'Fitur yang diizinkan',
    api_rate_limit INT DEFAULT 1000 COMMENT 'API rate limit per hour',
    is_active BOOLEAN DEFAULT TRUE,
    is_verified BOOLEAN DEFAULT FALSE COMMENT 'Tenant terverifikasi',
    trial_end_date DATE NULL COMMENT 'Akhir trial',
    subscription_end_date DATE NULL COMMENT 'Akhir berlangganan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (parent_tenant_id) REFERENCES tenant_management(id) ON DELETE SET NULL,
    
    INDEX idx_tenant_code (tenant_code),
    INDEX idx_tenant_name (tenant_name),
    INDEX idx_tenant_type (tenant_type),
    INDEX idx_tenant_category (tenant_category),
    INDEX idx_parent_tenant_id (parent_tenant_id),
    INDEX idx_domain (domain),
    INDEX idx_subdomain (subdomain),
    INDEX idx_subscription_plan (subscription_plan),
    INDEX idx_is_active (is_active),
    INDEX idx_is_verified (is_verified),
    INDEX idx_trial_end_date (trial_end_date),
    INDEX idx_subscription_end_date (subscription_end_date)
) ENGINE=InnoDB COMMENT='Multi-tenant management';

-- =====================================================
-- 4. TENANT_USERS - Users per Tenant
-- =====================================================
CREATE TABLE tenant_users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke tenant_management',
    user_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke orang.users',
    role_in_tenant VARCHAR(50) NOT NULL COMMENT 'Role dalam tenant',
    permissions JSON NULL COMMENT 'Permissions khusus tenant',
    is_active BOOLEAN DEFAULT TRUE,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login_at TIMESTAMP NULL COMMENT 'Terakhir login ke tenant',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tenant_id) REFERENCES tenant_management(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES orang.users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_tenant_user (tenant_id, user_id),
    INDEX idx_tenant_id (tenant_id),
    INDEX idx_user_id (user_id),
    INDEX idx_role_in_tenant (role_in_tenant),
    INDEX idx_is_active (is_active),
    INDEX idx_joined_at (joined_at),
    INDEX idx_last_login_at (last_login_at)
) ENGINE=InnoDB COMMENT='Users dalam tenant';

-- =====================================================
-- 5. CUSTOM_FIELDS - Custom Fields Management
-- =====================================================
CREATE TABLE custom_fields (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    field_code VARCHAR(50) UNIQUE NOT NULL COMMENT 'Kode field',
    field_name VARCHAR(100) NOT NULL COMMENT 'Nama field',
    field_type ENUM('text', 'number', 'decimal', 'date', 'datetime', 'boolean', 'select', 'multiselect', 'file', 'image', 'json', 'textarea', 'email', 'phone', 'url') NOT NULL,
    target_entity VARCHAR(50) NOT NULL COMMENT 'Target entity (person, product, dll)',
    target_application VARCHAR(50) NULL COMMENT 'Aplikasi target',
    field_category VARCHAR(50) NULL COMMENT 'Kategori field',
    description TEXT NULL COMMENT 'Deskripsi field',
    default_value TEXT NULL COMMENT 'Default value',
    validation_rules JSON NULL COMMENT 'Validation rules',
    options JSON NULL COMMENT 'Options untuk select/multiselect',
    is_required BOOLEAN DEFAULT FALSE COMMENT 'Wajib diisi',
    is_unique BOOLEAN DEFAULT FALSE COMMENT 'Harus unik',
    is_encrypted BOOLEAN DEFAULT FALSE COMMENT 'Data di-encrypt',
    is_searchable BOOLEAN DEFAULT TRUE COMMENT 'Bisa dicari',
    is_filterable BOOLEAN DEFAULT TRUE COMMENT 'Bisa difilter',
    display_order INT DEFAULT 0 COMMENT 'Urutan tampil',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_field_code (field_code),
    INDEX idx_field_name (field_name),
    INDEX idx_field_type (field_type),
    INDEX idx_target_entity (target_entity),
    INDEX idx_target_application (target_application),
    INDEX idx_field_category (field_category),
    INDEX idx_is_required (is_required),
    INDEX idx_is_unique (is_unique),
    INDEX idx_is_active (is_active),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB COMMENT='Custom fields untuk semua entities';

-- =====================================================
-- 6. CUSTOM_FIELD_VALUES - Values Custom Fields
-- =====================================================
CREATE TABLE custom_field_values (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    field_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke custom_fields',
    entity_type VARCHAR(50) NOT NULL COMMENT 'Tipe entity',
    entity_id BIGINT UNSIGNED NOT NULL COMMENT 'ID entity',
    field_value TEXT NULL COMMENT 'Value field',
    file_path VARCHAR(500) NULL COMMENT 'Path file (jika tipe file)',
    file_size BIGINT NULL COMMENT 'Ukuran file',
    mime_type VARCHAR(100) NULL COMMENT 'MIME type',
    is_encrypted BOOLEAN DEFAULT FALSE COMMENT 'Data di-encrypt',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (field_id) REFERENCES custom_fields(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_field_entity (field_id, entity_type, entity_id),
    INDEX idx_field_id (field_id),
    INDEX idx_entity_type (entity_type),
    INDEX idx_entity_id (entity_id),
    INDEX idx_is_encrypted (is_encrypted)
) ENGINE=InnoDB COMMENT='Values untuk custom fields';

-- =====================================================
-- 7. WORKFLOW_DEFINITIONS - Workflow Definitions
-- =====================================================
CREATE TABLE workflow_definitions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workflow_code VARCHAR(50) UNIQUE NOT NULL COMMENT 'Kode workflow',
    workflow_name VARCHAR(100) NOT NULL COMMENT 'Nama workflow',
    workflow_category VARCHAR(50) NOT NULL COMMENT 'Kategori workflow',
    target_entity VARCHAR(50) NOT NULL COMMENT 'Target entity',
    application_scope VARCHAR(50) NULL COMMENT 'Aplikasi scope',
    description TEXT NULL COMMENT 'Deskripsi workflow',
    trigger_events JSON NULL COMMENT 'Trigger events',
    steps JSON NOT NULL COMMENT 'Steps workflow',
    conditions JSON NULL COMMENT 'Conditions workflow',
    actions JSON NULL COMMENT 'Actions workflow',
    notifications JSON NULL COMMENT 'Notifications',
    timeouts JSON NULL COMMENT 'Timeout configurations',
    is_active BOOLEAN DEFAULT TRUE,
    version INT DEFAULT 1 COMMENT 'Versi workflow',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_workflow_code (workflow_code),
    INDEX idx_workflow_name (workflow_name),
    INDEX idx_workflow_category (workflow_category),
    INDEX idx_target_entity (target_entity),
    INDEX idx_application_scope (application_scope),
    INDEX idx_is_active (is_active),
    INDEX idx_version (version)
) ENGINE=InnoDB COMMENT='Workflow definitions untuk berbagai proses';

-- =====================================================
-- 8. WORKFLOW_INSTANCES - Workflow Instances
-- =====================================================
CREATE TABLE workflow_instances (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    workflow_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke workflow_definitions',
    instance_code VARCHAR(50) UNIQUE NOT NULL COMMENT 'Kode instance',
    entity_type VARCHAR(50) NOT NULL COMMENT 'Tipe entity',
    entity_id BIGINT UNSIGNED NOT NULL COMMENT 'ID entity',
    current_step VARCHAR(100) NULL COMMENT 'Step saat ini',
    status ENUM('pending', 'running', 'completed', 'failed', 'cancelled', 'suspended') DEFAULT 'pending',
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu mulai',
    completed_at TIMESTAMP NULL COMMENT 'Waktu selesai',
    duration_seconds INT NULL COMMENT 'Durasi (detik)',
    variables JSON NULL COMMENT 'Variables workflow',
    history JSON NULL COMMENT 'History perubahan',
    error_message TEXT NULL COMMENT 'Error message',
    retry_count INT DEFAULT 0 COMMENT 'Jumlah retry',
    max_retries INT DEFAULT 3 COMMENT 'Maksimum retry',
    next_retry_at TIMESTAMP NULL COMMENT 'Retry berikutnya',
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (workflow_id) REFERENCES workflow_definitions(id) ON DELETE CASCADE,
    
    INDEX idx_workflow_id (workflow_id),
    INDEX idx_instance_code (instance_code),
    INDEX idx_entity_type (entity_type),
    INDEX idx_entity_id (entity_id),
    INDEX idx_current_step (current_step),
    INDEX idx_status (status),
    INDEX idx_started_at (started_at),
    INDEX idx_completed_at (completed_at),
    INDEX idx_next_retry_at (next_retry_at)
) ENGINE=InnoDB COMMENT='Instances dari workflow yang berjalan';

-- =====================================================
-- 9. NOTIFICATION_TEMPLATES - Notification Templates
-- =====================================================
CREATE TABLE notification_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_code VARCHAR(50) UNIQUE NOT NULL COMMENT 'Kode template',
    template_name VARCHAR(100) NOT NULL COMMENT 'Nama template',
    template_category VARCHAR(50) NOT NULL COMMENT 'Kategori template',
    channel_type ENUM('email', 'sms', 'whatsapp', 'push', 'in_app', 'webhook', 'all') NOT NULL,
    target_application VARCHAR(50) NULL COMMENT 'Aplikasi target',
    subject_template VARCHAR(255) NULL COMMENT 'Template subjek',
    body_template LONGTEXT NOT NULL COMMENT 'Template body',
    variables JSON NULL COMMENT 'Variables yang tersedia',
    conditions JSON NULL COMMENT 'Conditions pengiriman',
    priority_level TINYINT DEFAULT 3 COMMENT 'Prioritas (1=highest)',
    is_active BOOLEAN DEFAULT TRUE,
    is_default BOOLEAN DEFAULT FALSE COMMENT 'Template default',
    usage_count INT DEFAULT 0 COMMENT 'Jumlah penggunaan',
    last_used_at TIMESTAMP NULL COMMENT 'Terakhir digunakan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_template_code (template_code),
    INDEX idx_template_name (template_name),
    INDEX idx_template_category (template_category),
    INDEX idx_channel_type (channel_type),
    INDEX idx_target_application (target_application),
    INDEX idx_priority_level (priority_level),
    INDEX idx_is_active (is_active),
    INDEX idx_is_default (is_default),
    INDEX idx_usage_count (usage_count),
    INDEX idx_last_used_at (last_used_at)
) ENGINE=InnoDB COMMENT='Template notifikasi universal';

-- =====================================================
-- 10. AUDIT_TRAILS - Universal Audit Trails
-- =====================================================
CREATE TABLE audit_trails (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_code VARCHAR(50) NOT NULL COMMENT 'Kode aplikasi',
    table_name VARCHAR(100) NOT NULL COMMENT 'Nama tabel',
    record_id BIGINT UNSIGNED NOT NULL COMMENT 'ID record',
    action ENUM('insert', 'update', 'delete', 'view', 'login', 'logout', 'export', 'import') NOT NULL,
    old_values JSON NULL COMMENT 'Nilai lama',
    new_values JSON NULL COMMENT 'Nilai baru',
    changed_fields JSON NULL COMMENT 'Field yang berubah',
    user_id BIGINT UNSIGNED NULL COMMENT 'User yang melakukan',
    user_name VARCHAR(255) NULL COMMENT 'Nama user',
    ip_address VARCHAR(45) NULL COMMENT 'IP address',
    user_agent TEXT NULL COMMENT 'User agent',
    session_id VARCHAR(255) NULL COMMENT 'Session ID',
    tenant_id BIGINT UNSIGNED NULL COMMENT 'Tenant ID',
    reason VARCHAR(500) NULL COMMENT 'Alasan perubahan',
    risk_level ENUM('low', 'medium', 'high', 'critical') DEFAULT 'low',
    compliance_tags JSON NULL COMMENT 'Tags compliance',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_application_code (application_code),
    INDEX idx_table_name (table_name),
    INDEX idx_record_id (record_id),
    INDEX idx_action (action),
    INDEX idx_user_id (user_id),
    INDEX idx_user_name (user_name),
    INDEX idx_ip_address (ip_address),
    INDEX idx_tenant_id (tenant_id),
    INDEX idx_risk_level (risk_level),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB COMMENT='Universal audit trails untuk semua aplikasi';

-- =====================================================
-- 11. INTEGRATION_LOGS - Integration Logs
-- =====================================================
CREATE TABLE integration_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    integration_code VARCHAR(50) NOT NULL COMMENT 'Kode integrasi',
    source_application VARCHAR(50) NOT NULL COMMENT 'Aplikasi sumber',
    target_application VARCHAR(50) NOT NULL COMMENT 'Aplikasi target',
    integration_type ENUM('api_call', 'webhook', 'file_transfer', 'database_sync', 'message_queue') NOT NULL,
    direction ENUM('outbound', 'inbound') NOT NULL COMMENT 'Arah integrasi',
    endpoint_url VARCHAR(500) NULL COMMENT 'URL endpoint',
    method VARCHAR(10) NULL COMMENT 'HTTP method',
    request_headers JSON NULL COMMENT 'Request headers',
    request_body LONGTEXT NULL COMMENT 'Request body',
    response_headers JSON NULL COMMENT 'Response headers',
    response_body LONGTEXT NULL COMMENT 'Response body',
    status_code INT NULL COMMENT 'HTTP status code',
    status ENUM('pending', 'success', 'failed', 'timeout', 'retry') DEFAULT 'pending',
    error_message TEXT NULL COMMENT 'Error message',
    processing_time_ms INT NULL COMMENT 'Processing time (milliseconds)',
    retry_count INT DEFAULT 0 COMMENT 'Jumlah retry',
    tenant_id BIGINT UNSIGNED NULL COMMENT 'Tenant ID',
    correlation_id VARCHAR(100) NULL COMMENT 'ID untuk tracing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_integration_code (integration_code),
    INDEX idx_source_application (source_application),
    INDEX idx_target_application (target_application),
    INDEX idx_integration_type (integration_type),
    INDEX idx_direction (direction),
    INDEX idx_status_code (status_code),
    INDEX idx_status (status),
    INDEX idx_tenant_id (tenant_id),
    INDEX idx_correlation_id (correlation_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB COMMENT='Logs untuk semua integrasi';

-- =====================================================
-- 12. SYSTEM_METRICS - System Metrics
-- =====================================================
CREATE TABLE system_metrics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    metric_name VARCHAR(100) NOT NULL COMMENT 'Nama metric',
    metric_category VARCHAR(50) NOT NULL COMMENT 'Kategori metric',
    application_code VARCHAR(50) NULL COMMENT 'Aplikasi',
    metric_type ENUM('counter', 'gauge', 'histogram', 'timer') NOT NULL,
    value DECIMAL(15,4) NOT NULL COMMENT 'Nilai metric',
    unit VARCHAR(20) NULL COMMENT 'Unit (ms, bytes, count, dll)',
    tags JSON NULL COMMENT 'Tags metric',
    tenant_id BIGINT UNSIGNED NULL COMMENT 'Tenant ID',
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu recording',
    
    INDEX idx_metric_name (metric_name),
    INDEX idx_metric_category (metric_category),
    INDEX idx_application_code (application_code),
    INDEX idx_metric_type (metric_type),
    INDEX idx_value (value),
    INDEX idx_tenant_id (tenant_id),
    INDEX idx_recorded_at (recorded_at)
) ENGINE=InnoDB COMMENT='System metrics untuk monitoring';

-- =====================================================
-- 13. FEATURE_FLAGS - Feature Flags
-- =====================================================
CREATE TABLE feature_flags (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    flag_key VARCHAR(100) UNIQUE NOT NULL COMMENT 'Key feature flag',
    flag_name VARCHAR(100) NOT NULL COMMENT 'Nama feature',
    description TEXT NULL COMMENT 'Deskripsi feature',
    flag_type ENUM('boolean', 'percentage', 'whitelist', 'blacklist', 'gradual') DEFAULT 'boolean',
    target_application VARCHAR(50) NULL COMMENT 'Aplikasi target',
    target_users JSON NULL COMMENT 'Users yang ditarget',
    target_tenants JSON NULL COMMENT 'Tenants yang ditarget',
    percentage_value DECIMAL(5,2) NULL COMMENT 'Persentase (jika tipe percentage)',
    is_enabled BOOLEAN DEFAULT FALSE COMMENT 'Status enable',
    rollout_strategy ENUM('immediate', 'gradual', 'scheduled') DEFAULT 'immediate',
    rollout_start_date TIMESTAMP NULL COMMENT 'Mulai rollout',
    rollout_end_date TIMESTAMP NULL COMMENT 'Selesai rollout',
    conditions JSON NULL COMMENT 'Conditions tambahan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_flag_key (flag_key),
    INDEX idx_flag_name (flag_name),
    INDEX idx_flag_type (flag_type),
    INDEX idx_target_application (target_application),
    INDEX idx_is_enabled (is_enabled),
    INDEX idx_rollout_strategy (rollout_strategy),
    INDEX idx_rollout_start_date (rollout_start_date),
    INDEX idx_rollout_end_date (rollout_end_date)
) ENGINE=InnoDB COMMENT='Feature flags management';

-- =====================================================
-- 14. DATA_MAPPINGS - Data Mappings
-- =====================================================
CREATE TABLE data_mappings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mapping_code VARCHAR(50) UNIQUE NOT NULL COMMENT 'Kode mapping',
    mapping_name VARCHAR(100) NOT NULL COMMENT 'Nama mapping',
    source_system VARCHAR(50) NOT NULL COMMENT 'Sistem sumber',
    target_system VARCHAR(50) NOT NULL COMMENT 'Sistem target',
    entity_type VARCHAR(50) NOT NULL COMMENT 'Tipe entity',
    field_mappings JSON NOT NULL COMMENT 'Mapping fields',
    transformation_rules JSON NULL COMMENT 'Rules transformasi',
    validation_rules JSON NULL COMMENT 'Rules validasi',
    is_active BOOLEAN DEFAULT TRUE,
    last_sync_at TIMESTAMP NULL COMMENT 'Terakhir sync',
    sync_status ENUM('success', 'failed', 'in_progress') DEFAULT 'success',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_mapping_code (mapping_code),
    INDEX idx_mapping_name (mapping_name),
    INDEX idx_source_system (source_system),
    INDEX idx_target_system (target_system),
    INDEX idx_entity_type (entity_type),
    INDEX idx_is_active (is_active),
    INDEX idx_last_sync_at (last_sync_at),
    INDEX idx_sync_status (sync_status)
) ENGINE=InnoDB COMMENT='Data mappings antar sistem';

-- =====================================================
-- 15. SCHEDULED_TASKS - Scheduled Tasks
-- =====================================================
CREATE TABLE scheduled_tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_code VARCHAR(50) UNIQUE NOT NULL COMMENT 'Kode task',
    task_name VARCHAR(100) NOT NULL COMMENT 'Nama task',
    task_category VARCHAR(50) NOT NULL COMMENT 'Kategori task',
    target_application VARCHAR(50) NULL COMMENT 'Aplikasi target',
    task_type ENUM('database_backup', 'data_sync', 'report_generation', 'email_send', 'cleanup', 'maintenance', 'custom') NOT NULL,
    schedule_expression VARCHAR(100) NOT NULL COMMENT 'Cron expression',
    task_parameters JSON NULL COMMENT 'Parameters task',
    timeout_minutes INT DEFAULT 60 COMMENT 'Timeout (menit)',
    retry_attempts INT DEFAULT 3 COMMENT 'Jumlah retry',
    is_active BOOLEAN DEFAULT TRUE,
    last_run_at TIMESTAMP NULL COMMENT 'Terakhir dijalankan',
    next_run_at TIMESTAMP NULL COMMENT 'Berikutnya dijalankan',
    last_run_status ENUM('success', 'failed', 'timeout', 'cancelled') NULL COMMENT 'Status terakhir',
    last_run_duration_seconds INT NULL COMMENT 'Durasi terakhir',
    last_run_message TEXT NULL COMMENT 'Pesan terakhir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_task_code (task_code),
    INDEX idx_task_name (task_name),
    INDEX idx_task_category (task_category),
    INDEX idx_target_application (target_application),
    INDEX idx_task_type (task_type),
    INDEX idx_is_active (is_active),
    INDEX idx_last_run_at (last_run_at),
    INDEX idx_next_run_at (next_run_at),
    INDEX idx_last_run_status (last_run_status)
) ENGINE=InnoDB COMMENT='Scheduled tasks management';

-- =====================================================
-- INSERT DEFAULT DATA
-- =====================================================

-- Default Applications
INSERT INTO application_registry (app_code, app_name, app_category, app_type, description, status, owner_name) VALUES
('POLRI_KINERJA', 'Manajemen Kinerja Personil POLRI', 'government', 'web', 'Aplikasi manajemen kinerja personil POLRI', 'development', 'POLRI'),
('KOPERASI_ID', 'Aplikasi Koperasi Indonesia', 'business', 'web', 'Aplikasi manajemen koperasi simpan pinjam', 'development', 'Koperasi Indonesia'),
('SENTRA_GIZI', 'Sentra Pelayanan Gizi', 'health', 'web', 'Aplikasi sentra pelayanan gizi masyarakat', 'development', 'Kementerian Kesehatan'),
('B2B_DESA', 'B2B Antar Desa', 'business', 'web', 'Platform B2B perdagangan antar desa', 'development', 'Kementerian Desa'),
('DISTRIBUTOR_CORE', 'Core Distributor System', 'business', 'web', 'Sistem distributor core business', 'production', 'Distributor Company');

-- Default Tenants
INSERT INTO tenant_management (tenant_code, tenant_name, tenant_type, tenant_category, description, subscription_plan) VALUES
('POLRI_PUSAT', 'POLRI Pusat', 'government', 'polri', 'Markas Besar POLRI', 'enterprise'),
('KOP_SUMBERMAKmur', 'Koperasi Sumber Makmur', 'organization', 'koperasi', 'Koperasi simpan pinjam Sumber Makmur', 'premium'),
('PUSKESMAS_SEHAT', 'Puskesmas Sehat', 'government', 'puskesmas', 'Puskesmas Sehat Bersama', 'basic'),
('DESA_MAKMUR', 'Desa Makmur', 'government', 'desa', 'Desa Makmur Jaya', 'basic');

-- Default Custom Fields untuk POLRI
INSERT INTO custom_fields (field_code, field_name, field_type, target_entity, target_application, field_category, description, is_required) VALUES
('POLRI_NRP', 'NRP', 'text', 'person', 'POLRI_KINERJA', 'identity', 'Nomor Register Personil POLRI', TRUE),
('POLRI_PANGKAT', 'Pangkat', 'select', 'person', 'POLRI_KINERJA', 'identity', 'Pangkat personil POLRI', TRUE),
('POLRI_KESATUAN', 'Kesatuan', 'text', 'person', 'POLRI_KINERJA', 'identity', 'Kesatuan personil POLRI', TRUE),
('POLRI_JABATAN', 'Jabatan', 'text', 'person', 'POLRI_KINERJA', 'position', 'Jabatan personil POLRI', TRUE);

-- Default Custom Fields untuk Koperasi
INSERT INTO custom_fields (field_code, field_name, field_type, target_entity, target_application, field_category, description, is_required) VALUES
('KOP_NO_ANGGOTA', 'No. Anggota', 'text', 'person', 'KOPERASI_ID', 'identity', 'Nomor anggota koperasi', TRUE),
('KOP_JENIS_SIMPANAN', 'Jenis Simpanan', 'select', 'person', 'KOPERASI_ID', 'financial', 'Jenis simpanan anggota', TRUE),
('KOP_LIMIT_PINJAMAN', 'Limit Pinjaman', 'decimal', 'person', 'KOPERASI_ID', 'financial', 'Limit pinjaman anggota', FALSE);

-- Default Custom Fields untuk Sentra Gizi
INSERT INTO custom_fields (field_code, field_name, field_type, target_entity, target_application, field_category, description, is_required) VALUES
('GIZI_NIK_BALITA', 'NIK Balita', 'text', 'person', 'SENTRA_GIZI', 'identity', 'NIK balita', TRUE),
('GIZI_BERAT_BADAN', 'Berat Badan', 'decimal', 'person', 'SENTRA_GIZI', 'health', 'Berat badan balita (kg)', TRUE),
('GIZI_TINGGI_BADAN', 'Tinggi Badan', 'decimal', 'person', 'SENTRA_GIZI', 'health', 'Tinggi badan balita (cm)', TRUE),
('GIZI_STATUS_GIZI', 'Status Gizi', 'select', 'person', 'SENTRA_GIZI', 'health', 'Status gizi balita', TRUE);

-- Default Custom Fields untuk B2B Desa
INSERT INTO custom_fields (field_code, field_name, field_type, target_entity, target_application, field_category, description, is_required) VALUES
('B2B_NAMA_PEDAGANG', 'Nama Pedagang', 'text', 'person', 'B2B_DESA', 'business', 'Nama pedagang desa', TRUE),
('B2B_JENIS_USAHA', 'Jenis Usaha', 'select', 'person', 'B2B_DESA', 'business', 'Jenis usaha pedagang', TRUE),
('B2B_LOKASI_DAGANG', 'Lokasi Dagang', 'text', 'person', 'B2B_DESA', 'business', 'Lokasi usaha pedagang', TRUE);

-- Default Workflow Definitions
INSERT INTO workflow_definitions (workflow_code, workflow_name, workflow_category, target_entity, description, steps) VALUES
('POLRI_KINERJA_EVALUASI', 'Evaluasi Kinerja POLRI', 'performance', 'person', 'Workflow evaluasi kinerja personil POLRI', 
 '[{"step": "input_kinerja", "name": "Input Kinerja", "type": "form"}, {"step": "validasi", "name": "Validasi", "type": "approval"}, {"step": "evaluasi", "name": "Evaluasi", "type": "assessment"}, {"step": "approval", "name": "Approval", "type": "approval"}]'),

('KOP_PINJAMAN_PROSES', 'Proses Pinjaman Koperasi', 'financial', 'person', 'Workflow proses pinjaman koperasi',
 '[{"step": "pengajuan", "name": "Pengajuan Pinjaman", "type": "form"}, {"step": "verifikasi", "name": "Verifikasi", "type": "validation"}, {"step": "approval", "name": "Approval", "type": "approval"}, {"step": "pencairan", "name": "Pencairan", "type": "process"}]'),

('GIZI_PEMERIKSAAN', 'Pemeriksaan Gizi', 'health', 'person', 'Workflow pemeriksaan gizi balita',
 '[{"step": "pendaftaran", "name": "Pendaftaran", "type": "form"}, {"step": "pengukuran", "name": "Pengukuran", "type": "measurement"}, {"step": "analisis", "name": "Analisis Gizi", "type": "analysis"}, {"step": "konsultasi", "name": "Konsultasi", "type": "consultation"}]');

-- Default Notification Templates
INSERT INTO notification_templates (template_code, template_name, template_category, channel_type, target_application, subject_template, body_template) VALUES
('POLRI_KINERJA_REMINDER', 'Reminder Input Kinerja', 'reminder', 'email', 'POLRI_KINERJA', 'Reminder Input Kinerja Bulanan', 'Halo {{person_name}}, jangan lupa menginput kinerja bulan {{bulan}} {{tahun}}.'),
('KOP_PINJAMAN_APPROVED', 'Pinjaman Disetujui', 'notification', 'sms', 'KOPERASI_ID', 'Pinjaman Disetujui', 'Selamat! Pengajuan pinjaman Anda sebesar {{jumlah}} telah disetujui.'),
('GIZI_JADWAL_IMUNISASI', 'Jadwal Imunisasi', 'reminder', 'whatsapp', 'SENTRA_GIZI', 'Jadwal Imunisasi', 'Halo {{ortu_name}}, jadwal imunisasi untuk {{anak_name}} adalah {{tanggal}}.'),
('B2B_ORDER_CONFIRMATION', 'Konfirmasi Order', 'notification', 'email', 'B2B_DESA', 'Order Dikonfirmasi', 'Order Anda #{{order_id}} telah dikonfirmasi. Total: {{total}}.');

-- Default Feature Flags
INSERT INTO feature_flags (flag_key, flag_name, description, target_application, is_enabled) VALUES
('polri_kinerja_mobile', 'Mobile App Kinerja POLRI', 'Enable mobile app untuk kinerja POLRI', 'POLRI_KINERJA', FALSE),
('koperasi_digital_payment', 'Digital Payment Koperasi', 'Enable pembayaran digital untuk koperasi', 'KOPERASI_ID', FALSE),
('gizi_ai_prediction', 'AI Prediction Gizi', 'Enable AI prediction untuk status gizi', 'SENTRA_GIZI', FALSE),
('b2b_real_time_sync', 'Real-time Sync B2B', 'Enable real-time sync untuk B2B antar desa', 'B2B_DESA', FALSE);

-- Default Scheduled Tasks
INSERT INTO scheduled_tasks (task_code, task_name, task_category, task_type, schedule_expression, task_parameters) VALUES
('DAILY_BACKUP', 'Daily Database Backup', 'maintenance', 'database_backup', '0 2 * * *', '{"backup_type": "full", "compression": true}'),
('WEEKLY_REPORT', 'Weekly Report Generation', 'reporting', 'report_generation', '0 8 * * 1', '{"report_types": ["sales", "inventory", "performance"]}'),
('MONTHLY_CLEANUP', 'Monthly Data Cleanup', 'maintenance', 'cleanup', '0 3 1 * *', '{"retention_days": 90, "tables": ["logs", "temp"]}');
