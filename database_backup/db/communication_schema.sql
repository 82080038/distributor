-- =====================================================
-- DATABASE COMMUNICATION - Communication System
-- =====================================================
-- Created: 19 Januari 2026
-- Purpose: Sistem komunikasi terintegrasi (email, SMS, WhatsApp)
-- Integration: Link ke orang, aplikasi, surat_laporan

CREATE DATABASE IF NOT EXISTS communication CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE communication;

-- =====================================================
-- 1. EMAIL_TEMPLATES - Template Email
-- =====================================================
CREATE TABLE email_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode template',
    template_name VARCHAR(100) NOT NULL COMMENT 'Nama template',
    template_category ENUM('transactional', 'marketing', 'notification', 'newsletter', 'alert', 'reminder') NOT NULL,
    template_purpose ENUM('welcome', 'order_confirmation', 'payment_confirmation', 'shipping_notification', 'delivery_confirmation', 'invoice', 'password_reset', 'account_verification', 'promotion', 'newsletter', 'custom') NOT NULL,
    subject_template VARCHAR(255) NOT NULL COMMENT 'Template subjek',
    html_template LONGTEXT NOT NULL COMMENT 'Template HTML',
    text_template TEXT NULL COMMENT 'Template text',
    css_style TEXT NULL COMMENT 'CSS styling',
    variables JSON NULL COMMENT 'Variable yang tersedia',
    default_sender_name VARCHAR(100) NULL COMMENT 'Nama pengirim default',
    default_sender_email VARCHAR(255) NULL COMMENT 'Email pengirim default',
    reply_to_email VARCHAR(255) NULL COMMENT 'Email reply-to',
    attachments JSON NULL COMMENT 'Default attachments',
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
    INDEX idx_template_purpose (template_purpose),
    INDEX idx_is_active (is_active),
    INDEX idx_is_default (is_default),
    INDEX idx_usage_count (usage_count),
    INDEX idx_last_used_at (last_used_at)
) ENGINE=InnoDB COMMENT='Template email';

-- =====================================================
-- 2. SMS_TEMPLATES - Template SMS
-- =====================================================
CREATE TABLE sms_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode template',
    template_name VARCHAR(100) NOT NULL COMMENT 'Nama template',
    template_category ENUM('transactional', 'marketing', 'notification', 'alert', 'reminder', 'verification') NOT NULL,
    template_purpose ENUM('otp', 'order_confirmation', 'payment_confirmation', 'shipping_update', 'delivery_notification', 'appointment_reminder', 'promotion', 'alert', 'custom') NOT NULL,
    message_template TEXT NOT NULL COMMENT 'Template pesan',
    max_length INT DEFAULT 160 COMMENT 'Maksimum karakter',
    variables JSON NULL COMMENT 'Variable yang tersedia',
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
    INDEX idx_template_purpose (template_purpose),
    INDEX idx_is_active (is_active),
    INDEX idx_is_default (is_default),
    INDEX idx_usage_count (usage_count),
    INDEX idx_last_used_at (last_used_at)
) ENGINE=InnoDB COMMENT='Template SMS';

-- =====================================================
-- 3. WHATSAPP_TEMPLATES - Template WhatsApp
-- =====================================================
CREATE TABLE whatsapp_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode template',
    template_name VARCHAR(100) NOT NULL COMMENT 'Nama template',
    template_category ENUM('transactional', 'marketing', 'notification', 'alert', 'reminder') NOT NULL,
    template_purpose ENUM('order_update', 'payment_reminder', 'shipping_notification', 'delivery_confirmation', 'appointment_reminder', 'promotion', 'support', 'custom') NOT NULL,
    header_type ENUM('none', 'text', 'image', 'video', 'document') DEFAULT 'none',
    header_content VARCHAR(500) NULL COMMENT 'Header content',
    body_template TEXT NOT NULL COMMENT 'Body template',
    footer_text VARCHAR(500) NULL COMMENT 'Footer text',
    buttons JSON NULL COMMENT 'Buttons',
    variables JSON NULL COMMENT 'Variable yang tersedia',
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
    INDEX idx_template_purpose (template_purpose),
    INDEX idx_header_type (header_type),
    INDEX idx_is_active (is_active),
    INDEX idx_is_default (is_default),
    INDEX idx_usage_count (usage_count),
    INDEX idx_last_used_at (last_used_at)
) ENGINE=InnoDB COMMENT='Template WhatsApp';

-- =====================================================
-- 4. COMMUNICATION_LOGS - Log Komunikasi
-- =====================================================
CREATE TABLE communication_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    communication_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode komunikasi',
    communication_type ENUM('email', 'sms', 'whatsapp', 'push_notification', 'in_app') NOT NULL,
    template_id BIGINT UNSIGNED NULL COMMENT 'Link ke template',
    recipient_type ENUM('user', 'customer', 'supplier', 'employee', 'group', 'custom') NOT NULL,
    recipient_id BIGINT UNSIGNED NULL COMMENT 'ID recipient',
    recipient_name VARCHAR(255) NOT NULL COMMENT 'Nama penerima',
    recipient_contact VARCHAR(255) NOT NULL COMMENT 'Kontak penerima (email/phone)',
    cc_recipients JSON NULL COMMENT 'CC recipients',
    bcc_recipients JSON NULL COMMENT 'BCC recipients',
    subject VARCHAR(500) NULL COMMENT 'Subjek (email)',
    message_content LONGTEXT NOT NULL COMMENT 'Isi pesan',
    attachments JSON NULL COMMENT 'Attachments',
    priority_level TINYINT DEFAULT 3 COMMENT 'Prioritas (1=highest, 5=lowest)',
    send_status ENUM('pending', 'sending', 'sent', 'delivered', 'failed', 'bounced', 'opened', 'clicked') DEFAULT 'pending',
    scheduled_at TIMESTAMP NULL COMMENT 'Dijadwalkan pada',
    sent_at TIMESTAMP NULL COMMENT 'Terkirim pada',
    delivered_at TIMESTAMP NULL COMMENT 'Terkirim pada (delivered)',
    opened_at TIMESTAMP NULL COMMENT 'Dibuka pada',
    clicked_at TIMESTAMP NULL COMMENT 'Diklik pada',
    failed_at TIMESTAMP NULL COMMENT 'Gagal pada',
    error_message TEXT NULL COMMENT 'Error message',
    external_id VARCHAR(100) NULL COMMENT 'ID dari provider',
    provider_response JSON NULL COMMENT 'Response dari provider',
    reference_type VARCHAR(50) NULL COMMENT 'Tipe referensi',
    reference_id BIGINT UNSIGNED NULL COMMENT 'ID referensi',
    campaign_id BIGINT UNSIGNED NULL COMMENT 'ID campaign',
    tags JSON NULL COMMENT 'Tags',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_communication_code (communication_code),
    INDEX idx_communication_type (communication_type),
    INDEX idx_template_id (template_id),
    INDEX idx_recipient_type (recipient_type),
    INDEX idx_recipient_id (recipient_id),
    INDEX idx_recipient_name (recipient_name),
    INDEX idx_recipient_contact (recipient_contact),
    INDEX idx_priority_level (priority_level),
    INDEX idx_send_status (send_status),
    INDEX idx_scheduled_at (scheduled_at),
    INDEX idx_sent_at (sent_at),
    INDEX idx_delivered_at (delivered_at),
    INDEX idx_opened_at (opened_at),
    INDEX idx_clicked_at (clicked_at),
    INDEX idx_reference_type (reference_type),
    INDEX idx_reference_id (reference_id),
    INDEX idx_campaign_id (campaign_id)
) ENGINE=InnoDB COMMENT='Log semua komunikasi';

-- =====================================================
-- 5. CAMPAIGNS - Campaign Marketing
-- =====================================================
CREATE TABLE campaigns (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campaign_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode campaign',
    campaign_name VARCHAR(100) NOT NULL COMMENT 'Nama campaign',
    campaign_type ENUM('email', 'sms', 'whatsapp', 'multi_channel', 'push', 'in_app') NOT NULL,
    campaign_category ENUM('marketing', 'transactional', 'notification', 'alert', 'newsletter', 'promotion') NOT NULL,
    campaign_purpose ENUM('product_launch', 'promotion', 'newsletter', 'announcement', 'survey', 'event', 'custom') NOT NULL,
    description TEXT NULL COMMENT 'Deskripsi campaign',
    target_audience JSON NOT NULL COMMENT 'Target audience',
    exclusion_criteria JSON NULL COMMENT 'Kriteria eksklusi',
    schedule_type ENUM('immediate', 'scheduled', 'recurring') DEFAULT 'immediate',
    scheduled_at TIMESTAMP NULL COMMENT 'Dijadwalkan pada',
    start_date TIMESTAMP NULL COMMENT 'Tanggal mulai',
    end_date TIMESTAMP NULL COMMENT 'Tanggal selesai',
    timezone VARCHAR(50) DEFAULT 'Asia/Jakarta' COMMENT 'Timezone',
    budget_limit DECIMAL(15,2) DEFAULT 0 COMMENT 'Limit budget',
    total_recipients INT DEFAULT 0 COMMENT 'Total penerima',
    sent_count INT DEFAULT 0 COMMENT 'Terkirim',
    delivered_count INT DEFAULT 0 COMMENT 'Terkirim (delivered)',
    opened_count INT DEFAULT 0 COMMENT 'Dibuka',
    clicked_count INT DEFAULT 0 COMMENT 'Diklik',
    bounced_count INT DEFAULT 0 COMMENT 'Bounce',
    unsubscribed_count INT DEFAULT 0 COMMENT 'Unsubscribe',
    conversion_count INT DEFAULT 0 COMMENT 'Konversi',
    conversion_value DECIMAL(15,2) DEFAULT 0 COMMENT 'Nilai konversi',
    campaign_status ENUM('draft', 'scheduled', 'running', 'paused', 'completed', 'cancelled', 'failed') DEFAULT 'draft',
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by BIGINT UNSIGNED NULL COMMENT 'Disetujui oleh',
    approved_at TIMESTAMP NULL COMMENT 'Waktu approval',
    performance_score DECIMAL(5,2) DEFAULT 0 COMMENT 'Skor performa (0-100)',
    notes TEXT NULL COMMENT 'Catatan campaign',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_campaign_code (campaign_code),
    INDEX idx_campaign_name (campaign_name),
    INDEX idx_campaign_type (campaign_type),
    INDEX idx_campaign_category (campaign_category),
    INDEX idx_campaign_purpose (campaign_purpose),
    INDEX idx_schedule_type (schedule_type),
    INDEX idx_scheduled_at (scheduled_at),
    INDEX idx_start_date (start_date),
    INDEX idx_end_date (end_date),
    INDEX idx_budget_limit (budget_limit),
    INDEX idx_sent_count (sent_count),
    INDEX idx_delivered_count (delivered_count),
    INDEX idx_opened_count (opened_count),
    INDEX idx_clicked_count (clicked_count),
    INDEX idx_conversion_count (conversion_count),
    INDEX idx_conversion_value (conversion_value),
    INDEX idx_campaign_status (campaign_status),
    INDEX idx_approval_status (approval_status),
    INDEX idx_performance_score (performance_score)
) ENGINE=InnoDB COMMENT='Campaign marketing';

-- =====================================================
-- 6. SUBSCRIPTIONS - Subscriber Management
-- =====================================================
CREATE TABLE subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subscription_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode subscription',
    subscriber_type ENUM('customer', 'lead', 'prospect', 'employee', 'partner', 'other') NOT NULL,
    subscriber_id BIGINT UNSIGNED NULL COMMENT 'ID subscriber',
    subscriber_name VARCHAR(255) NOT NULL COMMENT 'Nama subscriber',
    subscriber_email VARCHAR(255) UNIQUE NULL COMMENT 'Email subscriber',
    subscriber_phone VARCHAR(20) UNIQUE NULL COMMENT 'Phone subscriber',
    whatsapp_number VARCHAR(20) UNIQUE NULL COMMENT 'WhatsApp number',
    subscription_channels JSON NOT NULL COMMENT 'Channel yang disubscribe',
    subscription_categories JSON NOT NULL COMMENT 'Kategori yang disubscribe',
    subscription_status ENUM('active', 'inactive', 'unsubscribed', 'bounced', 'complained') DEFAULT 'active',
    subscription_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Tanggal subscribe',
    unsubscribe_date TIMESTAMP NULL COMMENT 'Tanggal unsubscribe',
    unsubscribe_reason VARCHAR(255) NULL COMMENT 'Alasan unsubscribe',
    preferences JSON NULL COMMENT 'Preferensi subscriber',
    custom_attributes JSON NULL COMMENT 'Atribut custom',
    source VARCHAR(100) NULL COMMENT 'Sumber subscriber',
    medium VARCHAR(100) NULL COMMENT 'Medium subscriber',
    campaign VARCHAR(100) NULL COMMENT 'Campaign source',
    ip_address VARCHAR(45) NULL COMMENT 'IP address saat subscribe',
    user_agent TEXT NULL COMMENT 'User agent saat subscribe',
    verification_status ENUM('verified', 'unverified', 'pending') DEFAULT 'unverified',
    verification_token VARCHAR(255) NULL COMMENT 'Token verifikasi',
    verification_sent_at TIMESTAMP NULL COMMENT 'Verifikasi dikirim',
    verified_at TIMESTAMP NULL COMMENT 'Terverifikasi pada',
    last_activity_at TIMESTAMP NULL COMMENT 'Aktivitas terakhir',
    engagement_score DECIMAL(5,2) DEFAULT 0 COMMENT 'Skor engagement',
    total_emails_sent INT DEFAULT 0 COMMENT 'Total email terkirim',
    total_emails_opened INT DEFAULT 0 COMMENT 'Total email dibuka',
    total_emails_clicked INT DEFAULT 0 COMMENT 'Total email diklik',
    total_sms_sent INT DEFAULT 0 COMMENT 'Total SMS terkirim',
    total_whatsapp_sent INT DEFAULT 0 COMMENT 'Total WhatsApp terkirim',
    notes TEXT NULL COMMENT 'Catatan subscriber',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_subscription_code (subscription_code),
    INDEX idx_subscriber_type (subscriber_type),
    INDEX idx_subscriber_id (subscriber_id),
    INDEX idx_subscriber_name (subscriber_name),
    INDEX idx_subscriber_email (subscriber_email),
    INDEX idx_subscriber_phone (subscriber_phone),
    INDEX idx_whatsapp_number (whatsapp_number),
    INDEX idx_subscription_status (subscription_status),
    INDEX idx_subscription_date (subscription_date),
    INDEX idx_unsubscribe_date (unsubscribe_date),
    INDEX idx_verification_status (verification_status),
    INDEX idx_verified_at (verified_at),
    INDEX idx_last_activity_at (last_activity_at),
    INDEX idx_engagement_score (engagement_score)
) ENGINE=InnoDB COMMENT='Subscriber management';

-- =====================================================
-- 7. CHAT_CONVERSATIONS - Percakapan
-- =====================================================
CREATE TABLE chat_conversations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode percakapan',
    channel_type ENUM('website', 'whatsapp', 'telegram', 'facebook', 'instagram', 'email', 'sms', 'in_app') NOT NULL,
    channel_id VARCHAR(100) NOT NULL COMMENT 'ID channel',
    customer_id BIGINT UNSIGNED NULL COMMENT 'Link ke orang.persons',
    customer_name VARCHAR(255) NOT NULL COMMENT 'Nama customer',
    customer_identifier VARCHAR(255) NOT NULL COMMENT 'Identifier customer (email/phone/username)',
    agent_id BIGINT UNSIGNED NULL COMMENT 'Link ke orang.users',
    agent_name VARCHAR(255) NULL COMMENT 'Nama agent',
    department VARCHAR(100) NULL COMMENT 'Department',
    queue_name VARCHAR(100) NULL COMMENT 'Queue',
    priority_level TINYINT DEFAULT 3 COMMENT 'Prioritas (1=highest, 5=lowest)',
    conversation_status ENUM('new', 'open', 'assigned', 'pending', 'closed', 'archived') DEFAULT 'new',
    satisfaction_rating TINYINT NULL COMMENT 'Rating kepuasan (1-5)',
    satisfaction_comment TEXT NULL COMMENT 'Komentar kepuasan',
    first_message_at TIMESTAMP NULL COMMENT 'Pesan pertama',
    last_message_at TIMESTAMP NULL COMMENT 'Pesan terakhir',
    first_response_at TIMESTAMP NULL COMMENT 'Response pertama',
    resolution_at TIMESTAMP NULL COMMENT 'Resolusi',
    response_time_seconds INT NULL COMMENT 'Response time (detik)',
    resolution_time_seconds INT NULL COMMENT 'Resolution time (detik)',
    total_messages INT DEFAULT 0 COMMENT 'Total pesan',
    customer_messages INT DEFAULT 0 COMMENT 'Pesan customer',
    agent_messages INT DEFAULT 0 COMMENT 'Pesan agent',
    tags JSON NULL COMMENT 'Tags percakapan',
    custom_fields JSON NULL COMMENT 'Custom fields',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_conversation_code (conversation_code),
    INDEX idx_channel_type (channel_type),
    INDEX idx_channel_id (channel_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_customer_name (customer_name),
    INDEX idx_customer_identifier (customer_identifier),
    INDEX idx_agent_id (agent_id),
    INDEX idx_agent_name (agent_name),
    INDEX idx_department (department),
    INDEX idx_queue_name (queue_name),
    INDEX idx_priority_level (priority_level),
    INDEX idx_conversation_status (conversation_status),
    INDEX idx_satisfaction_rating (satisfaction_rating),
    INDEX idx_first_message_at (first_message_at),
    INDEX idx_last_message_at (last_message_at),
    INDEX idx_first_response_at (first_response_at),
    INDEX idx_resolution_at (resolution_at),
    INDEX idx_response_time_seconds (response_time_seconds),
    INDEX idx_resolution_time_seconds (resolution_time_seconds)
) ENGINE=InnoDB COMMENT='Percakapan customer service';

-- =====================================================
-- 8. CHAT_MESSAGES - Pesan Percakapan
-- =====================================================
CREATE TABLE chat_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED NOT NULL COMMENT 'Link ke chat_conversations',
    message_sequence INT NOT NULL COMMENT 'Urutan pesan',
    sender_type ENUM('customer', 'agent', 'system', 'bot') NOT NULL,
    sender_id BIGINT UNSIGNED NULL COMMENT 'ID sender',
    sender_name VARCHAR(255) NOT NULL COMMENT 'Nama sender',
    message_type ENUM('text', 'image', 'file', 'audio', 'video', 'location', 'contact', 'system') DEFAULT 'text',
    message_content TEXT NOT NULL COMMENT 'Isi pesan',
    attachments JSON NULL COMMENT 'Attachments',
    metadata JSON NULL COMMENT 'Metadata pesan',
    is_internal BOOLEAN DEFAULT FALSE COMMENT 'Pesan internal',
    is_read BOOLEAN DEFAULT FALSE COMMENT 'Sudah dibaca',
    read_at TIMESTAMP NULL COMMENT 'Dibaca pada',
    message_status ENUM('sending', 'sent', 'delivered', 'read', 'failed') DEFAULT 'sent',
    external_id VARCHAR(100) NULL COMMENT 'ID dari platform',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE,
    
    INDEX idx_conversation_id (conversation_id),
    INDEX idx_message_sequence (message_sequence),
    INDEX idx_sender_type (sender_type),
    INDEX idx_sender_id (sender_id),
    INDEX idx_sender_name (sender_name),
    INDEX idx_message_type (message_type),
    INDEX idx_is_internal (is_internal),
    INDEX idx_is_read (is_read),
    INDEX idx_read_at (read_at),
    INDEX idx_message_status (message_status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB COMMENT='Pesan-pesan dalam percakapan';

-- =====================================================
-- 9. SUPPORT_TICKETS - Ticket Support
-- =====================================================
CREATE TABLE support_tickets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode ticket',
    customer_id BIGINT UNSIGNED NULL COMMENT 'Link ke orang.persons',
    customer_name VARCHAR(255) NOT NULL COMMENT 'Nama customer',
    customer_email VARCHAR(255) NULL COMMENT 'Email customer',
    customer_phone VARCHAR(20) NULL COMMENT 'Phone customer',
    ticket_category ENUM('technical', 'billing', 'product', 'shipping', 'account', 'complaint', 'suggestion', 'other') NOT NULL,
    ticket_priority ENUM('low', 'medium', 'high', 'urgent', 'critical') DEFAULT 'medium',
    ticket_source ENUM('email', 'phone', 'chat', 'website', 'mobile_app', 'social_media', 'walk_in') NOT NULL,
    subject VARCHAR(255) NOT NULL COMMENT 'Subjek ticket',
    description TEXT NOT NULL COMMENT 'Deskripsi masalah',
    attachments JSON NULL COMMENT 'Attachments',
    assigned_agent_id BIGINT UNSIGNED NULL COMMENT 'Agent yang ditugaskan',
    assigned_team VARCHAR(100) NULL COMMENT 'Team yang ditugaskan',
    ticket_status ENUM('new', 'open', 'in_progress', 'pending_customer', 'resolved', 'closed', 'reopened') DEFAULT 'new',
    resolution TEXT NULL COMMENT 'Resolusi masalah',
    resolution_category ENUM('solved', 'not_solved', 'duplicate', 'invalid', 'cancelled') NULL,
    satisfaction_rating TINYINT NULL COMMENT 'Rating kepuasan (1-5)',
    satisfaction_comment TEXT NULL COMMENT 'Komentar kepuasan',
    sla_response_minutes INT DEFAULT 60 COMMENT 'SLA response (menit)',
    sla_resolution_minutes INT DEFAULT 480 COMMENT 'SLA resolution (menit)',
    actual_response_minutes INT NULL COMMENT 'Response aktual (menit)',
    actual_resolution_minutes INT NULL COMMENT 'Resolution aktual (menit)',
    is_sla_met BOOLEAN NULL COMMENT 'SLA terpenuhi',
    escalation_level TINYINT DEFAULT 0 COMMENT 'Level eskalasi',
    parent_ticket_id BIGINT UNSIGNED NULL COMMENT 'Ticket induk',
    related_tickets JSON NULL COMMENT 'Ticket terkait',
    tags JSON NULL COMMENT 'Tags ticket',
    internal_notes TEXT NULL COMMENT 'Catatan internal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    FOREIGN KEY (customer_id) REFERENCES orang.persons(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_agent_id) REFERENCES orang.users(id) ON DELETE SET NULL),
    FOREIGN KEY (parent_ticket_id) REFERENCES support_tickets(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES orang.users(id) ON DELETE SET NULL,
    
    INDEX idx_ticket_code (ticket_code),
    INDEX idx_customer_id (customer_id),
    INDEX idx_customer_name (customer_name),
    INDEX idx_customer_email (customer_email),
    INDEX idx_ticket_category (ticket_category),
    INDEX idx_ticket_priority (ticket_priority),
    INDEX idx_ticket_source (ticket_source),
    INDEX idx_assigned_agent_id (assigned_agent_id),
    INDEX idx_assigned_team (assigned_team),
    INDEX idx_ticket_status (ticket_status),
    INDEX idx_satisfaction_rating (satisfaction_rating),
    INDEX idx_actual_response_minutes (actual_response_minutes),
    INDEX idx_actual_resolution_minutes (actual_resolution_minutes),
    INDEX idx_is_sla_met (is_sla_met),
    INDEX idx_escalation_level (escalation_level),
    INDEX idx_parent_ticket_id (parent_ticket_id)
) ENGINE=InnoDB COMMENT='Ticket support customer';

-- =====================================================
-- 10. FEEDBACK_MANAGEMENT - Manajemen Feedback
-- =====================================================
CREATE TABLE feedback_management (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    feedback_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode feedback',
    feedback_type ENUM('survey', 'review', 'complaint', 'suggestion', 'compliment', 'bug_report', 'feature_request') NOT NULL,
    feedback_category ENUM('product', 'service', 'website', 'mobile_app', 'customer_service', 'delivery', 'pricing', 'other') NOT NULL,
    respondent_type ENUM('customer', 'employee', 'partner', 'anonymous') NOT NULL,
    respondent_id BIGINT UNSIGNED NULL COMMENT 'ID respondent',
    respondent_name VARCHAR(255) NULL COMMENT 'Nama respondent',
    respondent_email VARCHAR(255) NULL COMMENT 'Email respondent',
    respondent_phone VARCHAR(20) NULL COMMENT 'Phone respondent',
    feedback_source VARCHAR(100) NULL COMMENT 'Sumber feedback',
    reference_type VARCHAR(50) NULL COMMENT 'Tipe referensi',
    reference_id BIGINT UNSIGNED NULL COMMENT 'ID referensi',
    rating TINYINT NULL COMMENT 'Rating (1-5)',
    nps_score TINYINT NULL COMMENT 'NPS score (0-10)',
    sentiment ENUM('positive', 'neutral', 'negative') NULL COMMENT 'Sentimen',
    feedback_title VARCHAR(255) NULL COMMENT 'Judul feedback',
    feedback_content TEXT NOT NULL COMMENT 'Isi feedback',
    attachments JSON NULL COMMENT 'Attachments',
    feedback_status ENUM('new', 'reviewed', 'acknowledged', 'in_progress', 'resolved', 'closed', 'ignored') DEFAULT 'new',
    priority_level TINYINT DEFAULT 3 COMMENT 'Prioritas (1=highest, 5=lowest)',
    assigned_to BIGINT UNSIGNED NULL COMMENT 'Ditugaskan ke',
    action_taken TEXT NULL COMMENT 'Tindakan yang diambil',
    resolution TEXT NULL COMMENT 'Resolusi',
    follow_up_required BOOLEAN DEFAULT FALSE COMMENT 'Perlu follow-up',
    follow_up_date DATE NULL COMMENT 'Tanggal follow-up',
    is_public BOOLEAN DEFAULT FALSE COMMENT 'Bisa dipublikasikan',
    tags JSON NULL COMMENT 'Tags feedback',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    
    INDEX idx_feedback_code (feedback_code),
    INDEX idx_feedback_type (feedback_type),
    INDEX idx_feedback_category (feedback_category),
    INDEX idx_respondent_type (respondent_type),
    INDEX idx_respondent_id (respondent_id),
    INDEX idx_respondent_name (respondent_name),
    INDEX idx_respondent_email (respondent_email),
    INDEX idx_feedback_source (feedback_source),
    INDEX idx_reference_type (reference_type),
    INDEX idx_reference_id (reference_id),
    INDEX idx_rating (rating),
    INDEX idx_nps_score (nps_score),
    INDEX idx_sentiment (sentiment),
    INDEX idx_feedback_status (feedback_status),
    INDEX idx_priority_level (priority_level),
    INDEX idx_assigned_to (assigned_to),
    INDEX idx_follow_up_date (follow_up_date),
    INDEX idx_is_public (is_public)
) ENGINE=InnoDB COMMENT='Manajemen feedback customer';

-- =====================================================
-- INSERT DEFAULT TEMPLATES
-- =====================================================

-- Default Email Templates
INSERT INTO email_templates (template_code, template_name, template_category, template_purpose, subject_template, html_template) VALUES
('WELCOME', 'Welcome Email', 'transactional', 'welcome', 'Welcome to {{company_name}}!', 
 '<h1>Welcome {{customer_name}}!</h1><p>Thank you for joining {{company_name}}.</p><p>Your account has been successfully created.</p>'),

('ORDER_CONFIRM', 'Order Confirmation', 'transactional', 'order_confirmation', 'Order #{{order_number}} Confirmed',
 '<h2>Order Confirmation</h2><p>Dear {{customer_name}},</p><p>Your order #{{order_number}} has been confirmed.</p><p>Total: {{order_total}}</p>'),

('PAYMENT_CONFIRM', 'Payment Confirmation', 'transactional', 'payment_confirmation', 'Payment Received for Order #{{order_number}}',
 '<h2>Payment Confirmation</h2><p>Dear {{customer_name}},</p><p>We have received your payment for order #{{order_number}}.</p><p>Amount: {{payment_amount}}</p>'),

('SHIPPING_NOTIFY', 'Shipping Notification', 'transactional', 'shipping_notification', 'Your Order #{{order_number}} Has Been Shipped',
 '<h2>Shipping Notification</h2><p>Dear {{customer_name}},</p><p>Your order #{{order_number}} has been shipped.</p><p>Tracking: {{tracking_number}}</p>'),

('INVOICE', 'Invoice', 'transactional', 'invoice', 'Invoice #{{invoice_number}}',
 '<h2>Invoice #{{invoice_number}}</h2><p>Dear {{customer_name}},</p><p>Please find attached your invoice.</p><p>Due Date: {{due_date}}</p><p>Amount: {{invoice_amount}}</p>');

-- Default SMS Templates
INSERT INTO sms_templates (template_code, template_name, template_category, template_purpose, message_template) VALUES
('OTP', 'OTP Verification', 'verification', 'otp', 'Your OTP code is {{otp_code}}. Valid for {{otp_expiry}} minutes.'),
('ORDER_CONFIRM_SMS', 'Order Confirmation SMS', 'transactional', 'order_confirmation', 'Your order #{{order_number}} has been confirmed. Total: {{order_total}}. Thank you!'),
('DELIVERY_SMS', 'Delivery Notification SMS', 'transactional', 'delivery_notification', 'Your order #{{order_number}} is out for delivery. Tracking: {{tracking_number}}'),
('PAYMENT_SMS', 'Payment Reminder SMS', 'reminder', 'payment_confirmation', 'Payment reminder for order #{{order_number}}. Amount: {{payment_amount}}. Due: {{due_date}}');

-- Default WhatsApp Templates
INSERT INTO whatsapp_templates (template_code, template_name, template_category, template_purpose, body_template) VALUES
('ORDER_UPDATE_WA', 'Order Update WhatsApp', 'transactional', 'order_update', 'Hello {{customer_name}}, your order #{{order_number}} status is now: {{order_status}}. Thank you for shopping with us!'),
('DELIVERY_WA', 'Delivery WhatsApp', 'transactional', 'delivery_confirmation', 'Great news! Your order #{{order_number}} has been delivered. Tracking: {{tracking_number}}. Enjoy your purchase!'),
('PROMO_WA', 'Promotion WhatsApp', 'marketing', 'promotion', 'Hi {{customer_name}}! Special offer just for you: {{promo_details}}. Click here: {{promo_link}}. Valid until {{promo_expiry}}!');

-- =====================================================
-- VIEWS untuk communication analytics
-- =====================================================

-- View untuk campaign performance
CREATE VIEW v_campaign_performance AS
SELECT 
    c.campaign_code,
    c.campaign_name,
    c.campaign_type,
    c.campaign_category,
    c.campaign_purpose,
    c.total_recipients,
    c.sent_count,
    c.delivered_count,
    c.opened_count,
    c.clicked_count,
    c.bounced_count,
    c.conversion_count,
    c.conversion_value,
    CASE 
        WHEN c.sent_count > 0 THEN (c.delivered_count * 100.0 / c.sent_count) 
        ELSE 0 
    END as delivery_rate,
    CASE 
        WHEN c.delivered_count > 0 THEN (c.opened_count * 100.0 / c.delivered_count) 
        ELSE 0 
    END as open_rate,
    CASE 
        WHEN c.opened_count > 0 THEN (c.clicked_count * 100.0 / c.opened_count) 
        ELSE 0 
    END as click_rate,
    CASE 
        WHEN c.clicked_count > 0 THEN (c.conversion_count * 100.0 / c.clicked_count) 
        ELSE 0 
    END as conversion_rate,
    c.performance_score,
    c.campaign_status,
    c.start_date,
    c.end_date
FROM campaigns c
WHERE c.campaign_status IN ('completed', 'running')
ORDER BY c.performance_score DESC;

-- View untuk communication summary
CREATE VIEW v_communication_summary AS
SELECT 
    cl.communication_type,
    DATE(cl.created_at) as communication_date,
    COUNT(*) as total_sent,
    SUM(CASE WHEN cl.send_status = 'delivered' THEN 1 ELSE 0 END) as total_delivered,
    SUM(CASE WHEN cl.send_status = 'opened' THEN 1 ELSE 0 END) as total_opened,
    SUM(CASE WHEN cl.send_status = 'clicked' THEN 1 ELSE 0 END) as total_clicked,
    SUM(CASE WHEN cl.send_status = 'failed' THEN 1 ELSE 0 END) as total_failed,
    CASE 
        WHEN COUNT(*) > 0 THEN (SUM(CASE WHEN cl.send_status = 'delivered' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) 
        ELSE 0 
    END as delivery_rate,
    CASE 
        WHEN SUM(CASE WHEN cl.send_status = 'delivered' THEN 1 ELSE 0 END) > 0 THEN (SUM(CASE WHEN cl.send_status = 'opened' THEN 1 ELSE 0 END) * 100.0 / SUM(CASE WHEN cl.send_status = 'delivered' THEN 1 ELSE 0 END)) 
        ELSE 0 
    END as open_rate
FROM communication_logs cl
WHERE cl.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY cl.communication_type, DATE(cl.created_at)
ORDER BY communication_date DESC;
