<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Database Connection Test</h1>
        
        <div class="card">
            <div class="card-header">
                <h3>Connection Status</h3>
            </div>
            <div class="card-body">
                <?php if ($conn->connect_error): ?>
                    <div class="alert alert-danger">
                        <strong>Main Database Error:</strong> <?php echo $conn->connect_error; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success">
                        <strong>Main Database:</strong> Connected successfully to <?php echo DB_NAME; ?>
                    </div>
                <?php endif; ?>

                <?php if ($conn_alamat->connect_error): ?>
                    <div class="alert alert-warning">
                        <strong>Alamat Database Error:</strong> <?php echo $conn_alamat->connect_error; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success">
                        <strong>Alamat Database:</strong> Connected successfully to <?php echo DB_NAME_ALAMAT; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3>Configuration</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr><td><strong>Host:</strong></td><td><?php echo DB_HOST; ?></td></tr>
                    <tr><td><strong>Port:</strong></td><td><?php echo DB_PORT; ?></td></tr>
                    <tr><td><strong>User:</strong></td><td><?php echo DB_USER; ?></td></tr>
                    <tr><td><strong>Main Database:</strong></td><td><?php echo DB_NAME; ?></td></tr>
                    <tr><td><strong>Alamat Database:</strong></td><td><?php echo DB_NAME_ALAMAT; ?></td></tr>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3>Test Tables</h3>
            </div>
            <div class="card-body">
                <?php
                $tables = ['user', 'orang', 'perusahaan', 'branches', 'products'];
                foreach ($tables as $table) {
                    $result = $conn->query("SHOW TABLES LIKE '$table'");
                    if ($result && $result->num_rows > 0) {
                        echo "<div class='alert alert-success'>✓ Table '$table' exists</div>";
                    } else {
                        echo "<div class='alert alert-danger'>✗ Table '$table' missing</div>";
                    }
                }
                ?>
            </div>
        </div>

        <div class="mt-3">
            <a href="setup_database.php" class="btn btn-primary">Setup Database</a>
            <a href="login.php" class="btn btn-secondary">Go to Login</a>
        </div>
    </div>
</body>
</html>
