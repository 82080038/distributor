<?php
/**
 * Cross-Platform Setup Script
 * Untuk Windows (XAMPP) dan Linux Development
 * 
 * Usage:
 * - Windows: Buka via browser http://localhost/distributor/setup_cross_platform.php
 * - Linux: php setup_cross_platform.php
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Cross-Platform Setup - Distributor Application</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background: #f5f5f5; }
        .setup-container { max-width: 800px; margin: 50px auto; }
        .platform-info { background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .success { background: #e8f5e8; }
        .warning { background: #fff3cd; }
        .error { background: #f8d7da; }
        .code-block { background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; }
    </style>
</head>
<body>
    <div class='setup-container'>
        <h1 class='text-center mb-4'>🔧 Cross-Platform Setup</h1>
        
        <div class='platform-info'>
            <h3>🖥️ Platform Detection</h3>
            <table class='table table-bordered'>
                <tr><td><strong>PHP Version</strong></td><td>" . PHP_VERSION . "</td></tr>
                <tr><td><strong>Operating System</strong></td><td>" . PHP_OS . "</td></tr>
                <tr><td><strong>Server Software</strong></td><td>" . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</td></tr>
                <tr><td><strong>Document Root</strong></td><td>" . $_SERVER['DOCUMENT_ROOT'] . "</td></tr>
                <tr><td><strong>Current Directory</strong></td><td>" . __DIR__ . "</td></tr>
            </table>
        </div>";

// Environment Detection
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

function is_localhost() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    return in_array($host, ['localhost', '127.0.0.1', '::1']);
}

// Check requirements
echo "<div class='platform-info'>
    <h3>✅ Requirements Check</h3>
    <table class='table table-bordered'>
        <tr>
            <td><strong>PHP Version (8.0+)</strong></td>
            <td>" . (version_compare(PHP_VERSION, '8.0.0', '>=') ? '<span class=\"badge bg-success\">✓ PASS</span>' : '<span class=\"badge bg-danger\">✗ FAIL</span>') . "</td>
            <td>Current: " . PHP_VERSION . "</td>
        </tr>
        <tr>
            <td><strong>MySQL Extension</strong></td>
            <td>" . (extension_loaded('mysqli') ? '<span class=\"badge bg-success\">✓ PASS</span>' : '<span class=\"badge bg-danger\">✗ FAIL</span>') . "</td>
            <td>Required for database connection</td>
        </tr>
        <tr>
            <td><strong>Session Extension</strong></td>
            <td>" . (extension_loaded('session') ? '<span class=\"badge bg-success\">✓ PASS</span>' : '<span class=\"badge bg-danger\">✗ FAIL</span>') . "</td>
            <td>Required for user authentication</td>
        </tr>
        <tr>
            <td><strong>JSON Extension</strong></td>
            <td>" . (extension_loaded('json') ? '<span class=\"badge bg-success\">✓ PASS</span>' : '<span class=\"badge bg-danger\">✗ FAIL</span>') . "</td>
            <td>Required for AJAX responses</td>
        </tr>
        <tr>
            <td><strong>MBString Extension</strong></td>
            <td>" . (extension_loaded('mbstring') ? '<span class=\"badge bg-success\">✓ PASS</span>' : '<span class=\"badge bg-danger\">✗ FAIL</span>') . "</td>
            <td>Required for UTF-8 support</td>
        </tr>
    </table>
</div>";

// Platform-specific instructions
echo "<div class='platform-info'>
    <h3>🪟 Platform-Specific Setup</h3>";

if (is_windows()) {
    echo "<div class='alert alert-info'>
        <h4>🪟 Windows (XAMPP) Detected</h4>
        <p>Follow these steps for XAMPP setup:</p>
        <ol>
            <li><strong>Install XAMPP</strong> if not already installed</li>
            <li><strong>Start Apache & MySQL</strong> from XAMPP Control Panel</li>
            <li><strong>Configure Virtual Host</strong> (optional but recommended):</li>
        </ol>
        <div class='code-block'>
            # C:/xampp/apache/conf/extra/httpd-vhosts.conf<br>
            &lt;VirtualHost *:80&gt;<br>
            &nbsp;&nbsp;&nbsp;&nbsp;DocumentRoot \"C:/xampp/htdocs/distributor\"<br>
            &nbsp;&nbsp;&nbsp;&nbsp;ServerName distributor.local<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&lt;Directory \"C:/xampp/htdocs/distributor\"&gt;<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AllowOverride All<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Require all granted<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&lt;/Directory&gt;<br>
            &lt;/VirtualHost&gt;
        </div>
        <ol start='4'>
            <li><strong>Update Windows Hosts File</strong>:</li>
        </ol>
        <div class='code-block'>
            # C:/Windows/System32/drivers/etc/hosts<br>
            127.0.0.1 distributor.local
        </div>
        <ol start='5'>
            <li><strong>Database Setup</strong>:</li>
        </ol>
        <ul>
            <li>Open <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a></li>
            <li>Create database: <code>distributor</code></li>
            <li>Create database: <code>alamat_db</code></li>
            <li>Import schema files from <code>/database/</code> folder</li>
        </ul>
    </div>";
} else {
    echo "<div class='alert alert-warning'>
        <h4>🐧 Linux Detected</h4>
        <p>Follow these steps for Linux setup:</p>
        <ol>
            <li><strong>Install LAMP/LEMP Stack</strong>:</li>
        </ol>
        <div class='code-block'>
            # Ubuntu/Debian<br>
            sudo apt update<br>
            sudo apt install apache2 mysql-server php php-mysqli php-json php-mbstring<br><br>
            # CentOS/RHEL<br>
            sudo yum install httpd mariadb-server php php-mysqlnd php-json php-mbstring
        </div>
        <ol start='2'>
            <li><strong>Configure Virtual Host</strong>:</li>
        </ol>
        <div class='code-block'>
            # /etc/apache2/sites-available/distributor.conf<br>
            &lt;VirtualHost *:80&gt;<br>
            &nbsp;&nbsp;&nbsp;&nbsp;DocumentRoot \"/var/www/html/distributor\"<br>
            &nbsp;&nbsp;&nbsp;&nbsp;ServerName distributor.local<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&lt;Directory \"/var/www/html/distributor\"&gt;<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AllowOverride All<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Require all granted<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&lt;/Directory&gt;<br>
            &lt;/VirtualHost&gt;<br><br>
            sudo a2ensite distributor.conf<br>
            sudo systemctl reload apache2
        </div>
        <ol start='3'>
            <li><strong>Update Linux Hosts File</strong>:</li>
        </ol>
        <div class='code-block'>
            # /etc/hosts<br>
            127.0.0.1 distributor.local
        </div>
        <ol start='4'>
            <li><strong>Database Setup</strong>:</li>
        </ol>
        <ul>
            <li>sudo mysql -u root -p</li>
            <li>CREATE DATABASE distributor;</li>
            <li>CREATE DATABASE alamat_db;</li>
            <li>Import schema files</li>
        </ul>
    </div>";
}

echo "</div>";

// Test database connection
echo "<div class='platform-info'>
    <h3>🔗 Database Connection Test</h3>";

$test_configs = [
    'XAMPP Default' => ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'db' => 'distributor'],
    'Linux Dev' => ['host' => 'localhost', 'user' => 'root', 'pass' => '8208', 'db' => 'distributor'],
    'Custom' => ['host' => 'localhost', 'user' => 'root', 'pass' => '8208', 'db' => 'distributor']
];

foreach ($test_configs as $name => $config) {
    try {
        $test_conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
        if ($test_conn->connect_error) {
            echo "<div class='alert alert-danger'>
                <strong>$name:</strong> ✗ FAILED - " . $test_conn->connect_error . "
            </div>";
        } else {
            echo "<div class='alert alert-success'>
                <strong>$name:</strong> ✓ SUCCESS - Connection established
            </div>";
            $test_conn->close();
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>
            <strong>$name:</strong> ✗ FAILED - " . $e->getMessage() . "
        </div>";
    }
}

echo "</div>";

// File permissions check
echo "<div class='platform-info'>
    <h3>📁 File Permissions Check</h3>";

$dirs_to_check = ['logs', 'uploads', 'temp'];
foreach ($dirs_to_check as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "<div class='alert alert-warning'>
            <strong>$dir:</strong> 📁 Created directory
        </div>";
    }
    
    if (is_writable($dir)) {
        echo "<div class='alert alert-success'>
            <strong>$dir:</strong> ✓ Writable
        </div>";
    } else {
        echo "<div class='alert alert-danger'>
            <strong>$dir:</strong> ✗ Not writable - " . (is_windows() ? 'Run as Administrator' : 'sudo chown -R www-data:www-data ' . $dir . '"') . "
        </div>";
    }
}

echo "</div>";

// Configuration file generation
echo "<div class='platform-info'>
    <h3>⚙️ Configuration Files</h3>
    <p>Download the appropriate configuration file:</p>";

if (is_windows()) {
    echo "<a href='config.windows.php' class='btn btn-primary me-2'>🪟 Download Windows Config</a>";
} else {
    echo "<a href='config.linux.php' class='btn btn-primary me-2'>🐧 Download Linux Config</a>";
}

echo "<a href='config.universal.php' class='btn btn-secondary'>🔄 Download Universal Config</a>";

echo "</div>";

echo "<div class='platform-info'>
    <h3>🚀 Next Steps</h3>
    <ol>
        <li><strong>Download appropriate config file</strong> from above</li>
        <li><strong>Replace config.php</strong> with the downloaded version</li>
        <li><strong>Setup database</strong> using the instructions</li>
        <li><strong>Test the application</strong> by accessing login page</li>
        <li><strong>Default Login</strong>: admin / password</li>
    </ol>
    
    <div class='text-center mt-4'>
        <a href='index.php' class='btn btn-success btn-lg'>🎯 Go to Application</a>
    </div>
</div>";

echo "</div>
<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
