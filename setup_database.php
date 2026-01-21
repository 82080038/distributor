<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

echo "<h2>Database Setup for Distributor Application</h2>";

// Create databases if they don't exist
echo "<h3>Creating databases...</h3>";

// Create distributor database
$conn->query("CREATE DATABASE IF NOT EXISTS distributor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
echo "✓ Database 'distributor' created or already exists<br>";

// Create alamat_db database  
$conn->query("CREATE DATABASE IF NOT EXISTS alamat_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
echo "✓ Database 'alamat_db' created or already exists<br>";

// Switch to distributor database
$conn->select_db('distributor');

// Import schema files
echo "<h3>Importing schema files...</h3>";

$schema_files = [
    'schema_core.sql',
    'schema_migration_orang_user.sql', 
    'schema_migration_purchases.sql',
    'db/distributor.sql'
];

foreach ($schema_files as $file) {
    $filepath = __DIR__ . DIRECTORY_SEPARATOR . $file;
    if (file_exists($filepath)) {
        $sql = file_get_contents($filepath);
        if ($conn->multi_query($sql)) {
            echo "✓ Imported $file successfully<br>";
            // Clear remaining results
            while ($conn->more_results() && $conn->next_result()) {
                $conn->store_result();
            }
        } else {
            echo "✗ Error importing $file: " . $conn->error . "<br>";
        }
    } else {
        echo "⚠ File $file not found<br>";
    }
}

// Create default user if none exists
echo "<h3>Creating default user...</h3>";

$check_user = $conn->query("SELECT COUNT(*) as count FROM user");
$user_count = $check_user->fetch_assoc()['count'];

if ($user_count == 0) {
    // Create default company
    $conn->query("INSERT INTO perusahaan (nama_perusahaan, alamat, kontak) VALUES ('Default Company', 'Default Address', 'Default Contact')");
    $perusahaan_id = $conn->insert_id;
    
    // Create default branch
    $conn->query("INSERT INTO branches (perusahaan_id, code, name) VALUES ($perusahaan_id, 'MAIN', 'Main Branch')");
    $branch_id = $conn->insert_id;
    
    // Create default person
    $conn->query("INSERT INTO orang (perusahaan_id, nama_lengkap) VALUES ($perusahaan_id, 'Administrator')");
    $orang_id = $conn->insert_id;
    
    // Create default user
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->query("INSERT INTO user (id_orang, username, email, password_hash, role_id, branch_id) VALUES ($orang_id, 'admin', 'admin@example.com', '$password_hash', 1, $branch_id)");
    
    echo "✓ Default user created: Username: admin, Password: admin123<br>";
} else {
    echo "✓ Users already exist in database<br>";
}

echo "<h3>Setup Complete!</h3>";
echo "<p>You can now <a href='login.php'>login to the application</a>.</p>";
echo "<p><strong>Default Login:</strong><br>";
echo "Username: admin<br>";
echo "Password: admin123</p>";

$conn->close();
?>
