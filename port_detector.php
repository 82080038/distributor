<?php
/**
 * Port Detection Utility
 * Menampilkan informasi port MySQL yang tersedia
 */

echo "=== MySQL Port Detection Utility ===\n\n";

// Function to test if port is available for MySQL
function test_mysql_port($host, $port, $database = 'distributor', $user = 'root', $password = '') {
    echo "Testing port $port...\n";
    
    // Test basic connection
    $connection = @new mysqli($host, $user, $password, $database, $port);
    
    if ($connection && !$connection->connect_error) {
        echo "✅ Port $port: SUCCESS - Connected to MySQL\n";
        $connection->close();
        return true;
    } else {
        $error = $connection ? $connection->connect_error : 'Connection failed';
        echo "❌ Port $port: FAILED - $error\n";
        return false;
    }
}

// Function to find available port
function find_available_port($host, $ports_to_try) {
    echo "Scanning available ports on $host...\n";
    
    foreach ($ports_to_try as $port) {
        echo "  Trying port $port... ";
        
        // Quick socket test
        $socket = @fsockopen($host, $port, $errno, $errstr, 2);
        if ($socket) {
            fclose($socket);
            
            // Verify it's actually MySQL
            if (test_mysql_port($host, $port)) {
                echo "🎯 Found working MySQL port: $port\n";
                return $port;
            }
        } else {
            echo "Port $port not available\n";
        }
    }
    
    return null;
}

// Detect environment
$is_docker = file_exists('.dockerenv') || getenv('DOCKER_ENV') === 'true';
$is_windows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

echo "Environment: " . ($is_docker ? 'Docker' : ($is_windows ? 'Windows' : 'Linux')) . "\n";

if ($is_docker) {
    echo "Host: mysql (Docker container)\n";
    $default_ports = [3307, 3306, 3308, 3309];
    $available_port = find_available_port('127.0.0.1', $default_ports);
    
    if ($available_port) {
        echo "\n🐳 Docker Configuration:\n";
        echo "  Update docker-compose.yml ports:\n";
        echo "    ports:\n";
        echo "      - \"$available_port:3306\"\n";
        echo "\n  Then restart: docker-compose up -d\n";
    }
    
} elseif ($is_windows) {
    echo "Host: localhost (Windows XAMPP)\n";
    $default_ports = [3306, 3307, 3308];
    $available_port = find_available_port('127.0.0.1', $default_ports);
    
    if ($available_port) {
        echo "\n🪟 Windows Configuration:\n";
        echo "  Update config.php DB_PORT to: $available_port\n";
        echo "  Or change XAMPP MySQL port to: $available_port\n";
    }
    
} else {
    echo "Host: localhost (Linux Native)\n";
    $default_ports = [3306, 3307, 3308];
    $available_port = find_available_port('127.0.0.1', $default_ports);
    
    if ($available_port) {
        echo "\n🐧 Linux Configuration:\n";
        echo "  Update config.php DB_PORT to: $available_port\n";
        echo "  Or change MySQL service port to: $available_port\n";
        echo "  Then restart: sudo systemctl restart mysql\n";
    }
}

echo "\n=== Port Detection Complete ===\n";
?>
