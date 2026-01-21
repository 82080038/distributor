# Windows Compatibility Guide

## Overview
This distributor management system has been adapted for Windows compatibility while maintaining full functionality on Unix/Linux systems.

## Changes Made

### 1. File Path Separators
- Updated all `include`, `require`, and file path operations to use `DIRECTORY_SEPARATOR`
- Replaced hardcoded `/` with `DIRECTORY_SEPARATOR` for cross-platform compatibility
- Files affected: All PHP files in the main directory and `catatan/` subdirectory

### 2. Database Configuration
- Added Windows-specific database connection settings
- Windows uses TCP/IP connections (no socket file)
- Unix/Linux maintains socket file support
- Enhanced error reporting for database connections

### 3. Case Sensitivity
- All file includes now use proper case-sensitive paths
- Windows is case-insensitive but this ensures compatibility with case-sensitive systems

## Windows Setup Instructions

### Prerequisites
1. PHP 7.4+ with MySQLi extension
2. MySQL/MariaDB server
3. Web server (Apache/IIS/Nginx)

### Database Setup
1. Create database: `distributor`
2. Create database: `alamat_db`
3. Import schema files:
   - `schema_core.sql`
   - `schema_migration_orang_user.sql`
   - `schema_migration_purchases.sql`

### Configuration
The system automatically detects Windows vs Unix environments:
- Windows: Uses TCP/IP connection to localhost
- Unix/Linux: Uses socket file when available

### File Permissions
Windows doesn't use Unix-style permissions. Ensure:
- Web server can read all PHP files
- Upload directories (if any) are writable by the web server

### Testing
1. Access `index.php` in your browser
2. Login with default credentials (create via `register.php`)
3. Verify all modules are functioning

## Notes
- Excel parsing functionality works on both platforms
- No Unix-specific dependencies remain
- All file operations are cross-platform compatible
