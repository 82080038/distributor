<?php
/**
 * Configuration untuk Windows (XAMPP)
 * Copy file ini ke config.php untuk development di Windows
 */

// Environment Detection
function is_development() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    return in_array($host, ['localhost', '127.0.0.1', '::1', 'distributor.local']);
}

// Error reporting untuk development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Ensure logs directory exists
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

// Session configuration
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

// XAMPP Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default password
define('DB_NAME', 'distributor');
define('DB_NAME_ALAMAT', 'alamat_db');
define('DB_PORT', 3306);

// Database Connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    $conn->set_charset('utf8mb4');
    
    $conn_alamat = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME_ALAMAT, DB_PORT);
    if ($conn_alamat->connect_error) {
        error_log("Alamat database connection failed: " . $conn_alamat->connect_error);
        $conn_alamat = null;
    } else {
        $conn_alamat->set_charset('utf8mb4');
    }
    
} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage());
    die('Database Error: ' . $e->getMessage() . 
        '<br><br>XAMPP Setup Instructions:' .
        '<br>1. Ensure XAMPP MySQL is running' .
        '<br>2. Check XAMPP Control Panel' .
        '<br>3. Try password: "" (empty) or "root"' .
        '<br>4. Access phpMyAdmin: http://localhost/phpmyadmin');
}

// Include all utility functions
require_once __DIR__ . '/config.functions.php';

?>
