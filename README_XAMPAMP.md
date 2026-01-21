# Distributor Application - XAMPAMP Setup

## Prerequisites
- XAMPAMP installed (Apache + MySQL + PHP)
- PHP version 8.0 or higher
- MySQL version 8.0 or higher

## Installation Steps

### 1. Database Setup
1. Start XAMPAMP Control Panel
2. Start Apache and MySQL services
3. Open http://localhost/phpmyadmin in your browser
4. Create databases:
   - `distributor`
   - `alamat_db`
5. Import the SQL schema files from the `db/` folder:
   - `schema_core.sql`
   - `schema_migration_orang_user.sql`
   - `schema_migration_purchases.sql`

### 2. Configuration
The application is pre-configured for XAMPAMP:
- Database Host: localhost
- Database User: root
- Database Password: (empty)
- Database Port: 3306

### 3. Access the Application
- Main application: http://localhost/distributor/
- Login page: http://localhost/distributor/login.php

### 4. Default Credentials
Check the database for initial user accounts or register a new account at:
http://localhost/distributor/register.php

## File Structure
```
distributor/
├── config.php              # Database configuration
├── index.php               # Main entry point
├── login.php               # Login page
├── register.php            # Registration page
├── customers*.php          # Customer management
├── products*.php           # Product management
├── purchases*.php          # Purchase management
├── sales*.php              # Sales management
├── suppliers*.php          # Supplier management
├── pesanan*.php            # Order management
├── report_*.php            # Various reports
├── profile*.php            # User profile
├── db/                     # Database schemas
├── logs/                   # Application logs
└── catatan/                # Documentation and notes
```

## Troubleshooting

### Database Connection Issues
1. Ensure MySQL service is running in XAMPAMP
2. Check that databases exist in phpMyAdmin
3. Verify MySQL port is 3306 (default XAMPAMP setting)

### PHP Errors
1. Check error logs in `logs/php_errors.log`
2. Ensure PHP extensions are enabled in XAMPAMP:
   - mysqli
   - mbstring
   - json

### File Permissions
1. Ensure `logs/` folder is writable
2. Check that XAMPAMP has permission to access project files

## Features
- Customer Management
- Product Management
- Purchase Orders
- Sales Management
- Supplier Management
- Order Processing
- Reporting System
- User Authentication
- Address Management

## Support
For issues or questions, check the documentation in the `catatan/` folder or review the error logs.
