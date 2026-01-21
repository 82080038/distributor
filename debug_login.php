<?php
session_start();
require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Form login belum lengkap.';
    } else {
        $sql = "SELECT u.id_user, u.username, o.nama_lengkap, o.perusahaan_id, u.password_hash, u.branch_id, r.name AS role_name 
                FROM user_accounts u 
                JOIN orang o ON u.id_orang = o.id_orang 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.username = ? AND u.status_aktif = 1
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if (password_verify($password, $row['password_hash'])) {
                    $user_id = (int)$row['id_user'];
                    $perusahaan_id = isset($row['perusahaan_id']) ? (int)$row['perusahaan_id'] : 0;

                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['name'] = $row['nama_lengkap'];
                    $_SESSION['role'] = $row['role_name'];
                    $_SESSION['branch_id'] = null;

                    // Update last login
                    $update_sql = "UPDATE user_accounts SET last_login_at = NOW() WHERE id_user = ?";
                    $stmt_update = $conn->prepare($update_sql);
                    if ($stmt_update) {
                        $stmt_update->bind_param('i', $_SESSION['user_id']);
                        $stmt_update->execute();
                        $stmt_update->close();
                    }
                    
                    $success = "Login successful! Redirecting...";
                    echo "<script>setTimeout(() => window.location.href='index.php', 2000);</script>";
                } else {
                    $error = 'Username atau password salah.';
                }
            } else {
                $error = 'Username atau password salah.';
            }
            $stmt->close();
        } else {
            $error = 'Database error.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>🔍 Debug Login Page</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Login Form</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" value="admin" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" value="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Session Debug Info</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
                        <p><strong>Session Status:</strong> <?php echo session_status() === PHP_SESSION_ACTIVE ? 'ACTIVE' : 'INACTIVE'; ?></p>
                        <p><strong>Is Logged In:</strong> <?php echo is_logged_in() ? 'YES' : 'NO'; ?></p>
                        <p><strong>User ID:</strong> <?php echo $_SESSION['user_id'] ?? 'NOT SET'; ?></p>
                        <p><strong>Username:</strong> <?php echo $_SESSION['username'] ?? 'NOT SET'; ?></p>
                        <p><strong>Name:</strong> <?php echo $_SESSION['name'] ?? 'NOT SET'; ?></p>
                        <p><strong>Role:</strong> <?php echo $_SESSION['role'] ?? 'NOT SET'; ?></p>
                        <p><strong>Branch ID:</strong> <?php echo $_SESSION['branch_id'] ?? 'NOT SET'; ?></p>
                        
                        <hr>
                        <h6>Current User Function:</h6>
                        <pre><?php print_r(current_user()); ?></pre>
                        
                        <hr>
                        <h6>All Session Data:</h6>
                        <pre><?php print_r($_SESSION); ?></pre>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-success">Go to Index</a>
            <a href="login.php" class="btn btn-secondary">Original Login</a>
        </div>
    </div>
</body>
</html>
