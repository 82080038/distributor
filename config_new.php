<?php
// Cross-Platform Configuration for Distributor Application
// Compatible with Windows (XAMPP) and Linux environments

// Environment Detection
function is_development() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    return in_array($host, ['localhost', '127.0.0.1', '::1', 'distributor.local']);
}

function is_windows() {
    return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}

function is_xampp() {
    return is_windows() && (
        file_exists('C:/xampp') || 
        file_exists('D:/xampp') || 
        file_exists('E:/xampp')
    );
}

// Environment-specific error reporting
if (is_development()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Ensure logs directory exists
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

// Secure session configuration
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true
]);

// Database Configuration with Cross-Platform Support
define('DB_HOST', 'localhost');
define('DB_NAME', 'distributor');
define('DB_NAME_ALAMAT', 'alamat_db');
define('DB_PORT', 3306);

// Cross-platform password detection
if (is_development()) {
    if (is_xampp()) {
        // XAMPP default password is usually empty
        define('DB_USER', 'root');
        define('DB_PASS', ''); // XAMPP default
    } else {
        // Linux development
        define('DB_USER', 'root');
        define('DB_PASS', '8208'); // Linux dev password
    }
} else {
    // Production - use environment variables or secure config
    define('DB_USER', $_ENV['DB_USER'] ?? 'root');
    define('DB_PASS', $_ENV['DB_PASS'] ?? 'secure_password_here');
}

// Database Connection with Universal Error Handling
try {
    // Main database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($conn->connect_error) {
        throw new Exception("Main database connection failed: " . $conn->connect_error);
    }
    $conn->set_charset('utf8mb4');
    
    // Alamat database connection (non-critical)
    $conn_alamat = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME_ALAMAT, DB_PORT);
    if ($conn_alamat->connect_error) {
        error_log("Alamat database connection failed: " . $conn_alamat->connect_error);
        $conn_alamat = null; // Set to null if failed, but don't die
    } else {
        $conn_alamat->set_charset('utf8mb4');
    }
    
} catch (Exception $e) {
    // Log error and show user-friendly message
    error_log("Database Error: " . $e->getMessage());
    
    if (is_development()) {
        die('Database Error: ' . $e->getMessage() . 
            '<br><br>Platform: ' . PHP_OS . 
            '<br>XAMPP Detected: ' . (is_xampp() ? 'Yes' : 'No') .
            '<br>DB_USER: ' . DB_USER .
            '<br>DB_HOST: ' . DB_HOST . ':' . DB_PORT);
    } else {
        die('Database connection failed. Please contact system administrator.');
    }
}

// Security and Utility Functions
function clean($value) {
    if ($value === null) {
        return '';
    }
    // Additional sanitization for security
    $value = trim($value);
    $value = stripslashes($value);
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function validate_input($data, $type = 'string') {
    if ($data === null) {
        return null;
    }
    
    switch ($type) {
        case 'int':
            return filter_var($data, FILTER_VALIDATE_INT);
        case 'email':
            return filter_var($data, FILTER_VALIDATE_EMAIL);
        case 'float':
            return filter_var($data, FILTER_VALIDATE_FLOAT);
        default:
            return clean($data);
    }
}

// CSRF Protection
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function redirect($url) {
    header('Location: ' . $url);
    exit();
}

// Date Functions (Indonesian Format)
function format_date_id($date) {
    if ($date === null || $date === '') {
        return '';
    }
    $dateString = substr((string)$date, 0, 10);
    $dt = DateTime::createFromFormat('Y-m-d', $dateString);
    if ($dt === false) {
        return $dateString;
    }
    return $dt->format('d-m-Y');
}

function parse_date_id_to_db($value) {
    if ($value === null) {
        return '';
    }
    $value = trim((string)$value);
    if ($value === '') {
        return '';
    }
    $formats = [
        ['d-m-Y H:i', 'Y-m-d H:i:s'],
        ['Y-m-d H:i', 'Y-m-d H:i:s'],
        ['Y-m-d H:i:s', 'Y-m-d H:i:s'],
        ['d-m-Y', 'Y-m-d'],
        ['Y-m-d', 'Y-m-d'],
    ];
    foreach ($formats as $pair) {
        $dt = DateTime::createFromFormat($pair[0], $value);
        if ($dt instanceof DateTime) {
            return $dt->format($pair[1]);
        }
    }
    return $value;
}

// Currency Functions (Indonesian Rupiah)
function number_to_indonesian_words($value) {
    $num = (float)$value;
    if (!is_finite($num)) {
        return '';
    }
    $n = (int)floor($num);
    if ($n <= 0) {
        return 'Nol Rupiah';
    }
    $words = angka_to_kata_id($n);
    return trim($words) . ' Rupiah';
}

function angka_to_kata_id($x) {
    $units = [
        '',
        'Satu',
        'Dua',
        'Tiga',
        'Empat',
        'Lima',
        'Enam',
        'Tujuh',
        'Delapan',
        'Sembilan',
        'Sepuluh',
        'Sebelas',
    ];
    if ($x < 12) {
        return $units[$x];
    }
    if ($x < 20) {
        return $units[$x - 10] . ' Belas';
    }
    if ($x < 100) {
        $tens = (int)floor($x / 10);
        $rest = $x % 10;
        $str = $units[$tens] . ' Puluh';
        if ($rest > 0) {
            $str .= ' ' . angka_to_kata_id($rest);
        }
        return $str;
    }
    if ($x < 200) {
        return 'Seratus' . ($x > 100 ? ' ' . angka_to_kata_id($x - 100) : '');
    }
    if ($x < 1000) {
        $hundreds = (int)floor($x / 100);
        $rest100 = $x % 100;
        $str = $units[$hundreds] . ' Ratus';
        if ($rest100 > 0) {
            $str .= ' ' . angka_to_kata_id($rest100);
        }
        return $str;
    }
    if ($x < 2000) {
        return 'Seribu' . ($x > 1000 ? ' ' . angka_to_kata_id($x - 1000) : '');
    }
    if ($x < 1000000) {
        $thousands = (int)floor($x / 1000);
        $rest1000 = $x % 1000;
        $str = angka_to_kata_id($thousands) . ' Ribu';
        if ($rest1000 > 0) {
            $str .= ' ' . angka_to_kata_id($rest1000);
        }
        return $str;
    }
    if ($x < 1000000000) {
        $millions = (int)floor($x / 1000000);
        $restMillion = $x % 1000000;
        $str = angka_to_kata_id($millions) . ' Juta';
        if ($restMillion > 0) {
            $str .= ' ' . angka_to_kata_id($restMillion);
        }
        return $str;
    }
    if ($x < 1000000000000) {
        $billions = (int)floor($x / 1000000000);
        $restBillion = $x % 1000000;
        $str = angka_to_kata_id($billions) . ' Miliar';
        if ($restBillion > 0) {
            $str .= ' ' . angka_to_kata_id($restBillion);
        }
        return $str;
    }
    $trillions = (int)floor($x / 1000000000000);
    $restTrillion = $x % 1000000000000;
    $str = angka_to_kata_id($trillions) . ' Triliun';
    if ($restTrillion > 0) {
        $str .= ' ' . angka_to_kata_id($restTrillion);
    }
    return $str;
}

// Debug Information (Development Only)
if (is_development()) {
    error_log("=== DEBUG INFO ===");
    error_log("PHP OS: " . PHP_OS);
    error_log("Windows: " . (is_windows() ? 'Yes' : 'No'));
    error_log("XAMPP: " . (is_xampp() ? 'Yes' : 'No'));
    error_log("Environment: " . (is_development() ? 'Development' : 'Production'));
    error_log("DB_HOST: " . DB_HOST);
    error_log("DB_USER: " . DB_USER);
    error_log("DB_NAME: " . DB_NAME);
    error_log("Main DB Connection: " . ($conn->ping() ? 'OK' : 'FAILED'));
    error_log("Alamat DB Connection: " . ($conn_alamat ? ($conn_alamat->ping() ? 'OK' : 'FAILED') : 'NOT CONNECTED'));
    error_log("================");
}

?>
