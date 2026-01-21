# Setup Guide for Distributor Application with phpMyAdmin

## Prerequisites
- PHP 7.4 or higher
- MySQL/MariaDB server
- phpMyAdmin installed and configured
- Web server (Apache/Nginx)

## Database Setup

### 1. Create Database via phpMyAdmin
1. Open phpMyAdmin in your browser
2. Click on "New" to create a new database
3. Enter database name: `distributor`
4. Select collation: `utf8mb4_unicode_ci`
5. Click "Create"

6. Repeat for `alamat_db` database:
   - Database name: `alamat_db`
   - Collation: `utf8mb4_unicode_ci`

### 2. Import Database Schema
1. Select the `distributor` database in phpMyAdmin
2. Click on "Import" tab
3. Choose the following SQL files from your project:
   - `schema_core.sql`
   - `schema_migration_orang_user.sql`
   - `schema_migration_purchases.sql`
   - `db/distributor.sql` (latest complete schema)

4. For the `alamat_db` database, import:
   - `db/alamat_db.sql`

### 3. Configure Database Connection
Edit `config.php` file:

```php
// Update these values according to your phpMyAdmin setup
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Your MySQL username
define('DB_PASS', 'your_password'); // Your MySQL password
define('DB_NAME', 'distributor');
define('DB_NAME_ALAMAT', 'alamat_db');
define('DB_PORT', 3306);
```

### 4. Run Database Setup Script
Open your browser and navigate to:
```
http://localhost/distributor/setup_database.php
```

This will:
- Create necessary databases if they don't exist
- Import all schema files
- Create default user account

### 5. Default Login
After setup, you can login with:
- Username: `admin`
- Password: `admin123`

## File Structure
The application is now configured to run without Docker or Composer:

```
/distributor/
├── config.php              # Database configuration
├── setup_database.php       # Database setup script
├── login.php               # Login page
├── index.php               # Main application
├── *.php                   # Application files
├── db/                     # Database schemas
│   ├── distributor.sql     # Latest complete schema
│   └── alamat_db.sql       # Address database schema
└── schema_*.sql            # Core schema files
```

## Testing the Application
1. Make sure your web server is running
2. Access the application via: `http://localhost/distributor/`
3. Login with default credentials
4. Verify all modules are working correctly

## Troubleshooting

### Database Connection Issues
- Verify MySQL server is running
- Check username/password in `config.php`
- Ensure databases exist in phpMyAdmin
- Check MySQL port (default: 3306)

### Import Issues
- Ensure SQL files are readable
- Check file permissions
- Verify database charset/collation

### Application Errors
- Check PHP error logs: `logs/php_errors.log`
- Ensure all required PHP extensions are installed
- Verify web server configuration

## Security Notes
- Change default admin password after first login
- Update database credentials in production
- Ensure proper file permissions
- Use HTTPS in production environment
