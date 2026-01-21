#!/bin/bash

# =============================================================================
# Distributor Application Deployment Script
# Untuk deployment ke production server Linux
# =============================================================================

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
APP_NAME="distributor"
APP_DIR="/var/www/html/$APP_NAME"
BACKUP_DIR="/var/backups/$APP_NAME"
LOG_FILE="/var/log/deploy_$APP_NAME.log"

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header() {
    echo -e "${BLUE}=== $1 ===${NC}"
}

# Check if running as root
check_root() {
    if [[ $EUID -ne 0 ]]; then
        print_error "This script must be run as root"
        exit 1
    fi
}

# Create backup
create_backup() {
    print_header "Creating Backup"
    
    if [[ -d "$APP_DIR" ]]; then
        BACKUP_NAME="$APP_NAME_$(date +%Y%m%d_%H%M%S).tar.gz"
        mkdir -p "$BACKUP_DIR"
        
        print_status "Creating backup: $BACKUP_DIR/$BACKUP_NAME"
        tar -czf "$BACKUP_DIR/$BACKUP_NAME" -C "$(dirname "$APP_DIR")" "$(basename "$APP_DIR")"
        
        print_status "Backup created successfully"
    else
        print_warning "No existing application found, skipping backup"
    fi
}

# Update application files
update_files() {
    print_header "Updating Application Files"
    
    # Create directory if not exists
    mkdir -p "$APP_DIR"
    
    # Copy files (adjust source path as needed)
    if [[ -d "./app" ]]; then
        cp -r ./app/* "$APP_DIR/"
    else
        cp -r . "$APP_DIR/"
    fi
    
    # Set proper permissions
    chown -R www-data:www-data "$APP_DIR"
    chmod -R 755 "$APP_DIR"
    chmod -R 644 "$APP_DIR"/*.php
    chmod -R 777 "$APP_DIR/logs" "$APP_DIR/uploads" "$APP_DIR/temp" 2>/dev/null || true
    
    print_status "Files updated and permissions set"
}

# Setup database
setup_database() {
    print_header "Database Setup"
    
    # Check if MySQL is running
    if ! systemctl is-active --quiet mysql; then
        print_status "Starting MySQL service"
        systemctl start mysql
    fi
    
    # Create databases if not exist
    mysql -u root -p << EOF
CREATE DATABASE IF NOT EXISTS distributor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS alamat_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EOF
    
    # Import schema if provided
    if [[ -f "./database/schema_core.sql" ]]; then
        print_status "Importing main database schema"
        mysql -u root -p distributor < ./database/schema_core.sql
    fi
    
    if [[ -f "./database/schema_add_tipe_alamat.sql" ]]; then
        print_status "Importing alamat schema"
        mysql -u root -p distributor < ./database/schema_add_tipe_alamat.sql
    fi
    
    print_status "Database setup completed"
}

# Configure web server
configure_webserver() {
    print_header "Configuring Web Server"
    
    # Apache configuration
    cat > /etc/apache2/sites-available/$APP_NAME.conf << EOF
<VirtualHost *:80>
    ServerName $APP_NAME.example.com
    DocumentRoot $APP_DIR
    
    <Directory $APP_DIR>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/error_$APP_NAME.log
    CustomLog \${APACHE_LOG_DIR}/access_$APP_NAME.log combined
</VirtualHost>

<VirtualHost *:443>
    ServerName $APP_NAME.example.com
    DocumentRoot $APP_DIR
    
    <Directory $APP_DIR>
        AllowOverride All
        Require all granted
    </Directory>
    
    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/$APP_NAME.crt
    SSLCertificateKeyFile /etc/ssl/private/$APP_NAME.key
    
    ErrorLog \${APACHE_LOG_DIR}/error_$APP_NAME-ssl.log
    CustomLog \${APACHE_LOG_DIR}/access_$APP_NAME-ssl.log combined
</VirtualHost>
EOF
    
    # Enable site and modules
    a2ensite $APP_NAME.conf
    a2enmod rewrite
    a2enmod ssl
    
    # Test and reload Apache
    apache2ctl configtest
    systemctl reload apache2
    
    print_status "Apache configured and reloaded"
}

# Setup SSL (Let's Encrypt)
setup_ssl() {
    print_header "Setting up SSL Certificate"
    
    # Install Certbot if not present
    if ! command -v certbot &> /dev/null; then
        apt update
        apt install -y certbot python3-certbot-apache
    fi
    
    # Get SSL certificate
    certbot --apache -d $APP_NAME.example.com --non-interactive --agree-tos --email admin@example.com
    
    print_status "SSL certificate installed"
}

# Setup cron jobs
setup_cron() {
    print_header "Setting up Cron Jobs"
    
    # Backup cron job
    (crontab -l 2>/dev/null; echo "0 2 * * * /usr/local/bin/backup_$APP_NAME.sh") | crontab -
    
    # Log rotation
    (crontab -l 2>/dev/null; echo "0 0 * * 0 /usr/sbin/logrotate /etc/logrotate.d/$APP_NAME") | crontab -
    
    print_status "Cron jobs configured"
}

# Create backup script
create_backup_script() {
    cat > /usr/local/bin/backup_$APP_NAME.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/var/backups/$APP_NAME"
APP_DIR="/var/www/html/$APP_NAME"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p "$BACKUP_DIR"
tar -czf "$BACKUP_DIR/$APP_NAME_$DATE.tar.gz" -C "$(dirname "$APP_DIR")" "$(basename "$APP_DIR")"

# Keep only last 7 days of backups
find "$BACKUP_DIR" -name "*.tar.gz" -mtime +7 -delete

# Backup database
mysqldump -u root -p distributor > "$BACKUP_DIR/database_$DATE.sql"
mysqldump -u root -p alamat_db > "$BACKUP_DIR/alamat_db_$DATE.sql"

find "$BACKUP_DIR" -name "*.sql" -mtime +7 -delete
EOF
    
    chmod +x /usr/local/bin/backup_$APP_NAME.sh
    
    print_status "Backup script created"
}

# Health check
health_check() {
    print_header "Performing Health Check"
    
    # Check web server response
    if curl -f -s http://localhost/$APP_NAME > /dev/null; then
        print_status "Web server responding"
    else
        print_error "Web server not responding"
    fi
    
    # Check database connection
    if mysql -u root -p -e "USE distributor; SELECT 1;" &> /dev/null; then
        print_status "Database connection successful"
    else
        print_error "Database connection failed"
    fi
    
    # Check file permissions
    if [[ -r "$APP_DIR/index.php" ]]; then
        print_status "File permissions correct"
    else
        print_error "File permissions issue"
    fi
}

# Main deployment function
main() {
    print_header "Starting Deployment of $APP_NAME"
    
    # Log everything
    exec > >(tee -a "$LOG_FILE")
    
    check_root
    create_backup
    update_files
    setup_database
    configure_webserver
    setup_ssl
    setup_cron
    create_backup_script
    health_check
    
    print_header "Deployment Completed Successfully"
    print_status "Application URL: http://$APP_NAME.example.com"
    print_status "HTTPS URL: https://$APP_NAME.example.com"
    print_status "Log file: $LOG_FILE"
}

# Run main function
main "$@"

print_status "Deployment process completed. Check logs at $LOG_FILE"
