<?php
// Start output buffering to prevent any accidental output
ob_start();

// Set headers to prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');

session_start();
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
$login_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Form login belum lengkap. Silakan isi Username dan Password.';
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

                    // Get company info directly without branches
                    if ($perusahaan_id > 0) {
                        $stmtPerusahaan = $conn->prepare("SELECT nama_perusahaan, alamat, kontak FROM perusahaan WHERE id_perusahaan = ? LIMIT 1");
                        if ($stmtPerusahaan) {
                            $stmtPerusahaan->bind_param('i', $perusahaan_id);
                            $stmtPerusahaan->execute();
                            $resPerusahaan = $stmtPerusahaan->get_result();
                            if ($resPerusahaan && ($rowPerusahaan = $resPerusahaan->fetch_assoc())) {
                                $nama_perusahaan = $rowPerusahaan['nama_perusahaan'];
                                $alamat_perusahaan = $rowPerusahaan['alamat'];
                                $kontak_perusahaan = $rowPerusahaan['kontak'];
                            } else {
                                $nama_perusahaan = '';
                                $alamat_perusahaan = '';
                                $kontak_perusahaan = '';
                            }
                            $stmtPerusahaan->close();
                        } else {
                            $nama_perusahaan = '';
                            $alamat_perusahaan = '';
                            $kontak_perusahaan = '';
                        }
                    } else {
                        $nama_perusahaan = '';
                        $alamat_perusahaan = '';
                        $kontak_perusahaan = '';
                    }

                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['name'] = $row['nama_lengkap'];
                    $_SESSION['role'] = $row['role_name'];
                    $_SESSION['branch_id'] = null; // No branches table available

                    $update_sql = "UPDATE user_accounts SET last_login_at = NOW() WHERE id_user = ?";
                    $stmt_update = $conn->prepare($update_sql);
                    if ($stmt_update) {
                        $stmt_update->bind_param('i', $_SESSION['user_id']);
                        $stmt_update->execute();
                        $stmt_update->close();
                    }
                    
                    $login_success = true;
                    
                    // Don't redirect immediately, let JavaScript handle it
                    // This prevents browser caching issues
                } else {
                    $error = 'Username atau password salah. Silakan periksa kembali dan coba lagi.';
                }
            } else {
                $error = 'Username atau password salah. Silakan periksa kembali dan coba lagi.';
            }
            $stmt->close();
        } else {
            $error = 'Terjadi kesalahan saat memproses login. Silakan coba lagi beberapa saat lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Distribusi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            padding: 40px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .login-header p {
            color: #666;
            margin: 0;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .loading-spinner {
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Sistem Distribusi</h1>
            <p>Silakan login untuk melanjutkan</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger mb-3">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($login_success): ?>
            <div class="alert alert-success mb-3">
                <i class="fas fa-check-circle me-2"></i>
                Login berhasil! Mengalihkan ke dashboard...
            </div>
        <?php endif; ?>
        
        <form method="POST" id="loginForm">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" 
                       value="<?php echo htmlspecialchars($username ?? 'admin'); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" 
                       value="<?php echo htmlspecialchars($password ?? 'password'); ?>" required>
            </div>
            <button type="submit" class="btn btn-login" id="loginBtn">
                <span class="btn-text">Login</span>
                <span class="loading-spinner">
                    <i class="fas fa-spinner fa-spin me-2"></i>Mengalihkan...
                </span>
            </button>
        </form>
        
        <div class="text-center mt-3">
            <small class="text-muted">
                Default: admin / password
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-key.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const btnText = loginBtn.querySelector('.btn-text');
            const loadingSpinner = loginBtn.querySelector('.loading-spinner');
            
            <?php if ($login_success): ?>
            // If login was successful, redirect using JavaScript
            setTimeout(function() {
                window.location.href = 'index.php';
            }, 1000);
            <?php endif; ?>
            
            loginForm.addEventListener('submit', function(e) {
                // Show loading state
                btnText.style.display = 'none';
                loadingSpinner.style.display = 'inline';
                loginBtn.disabled = true;
                
                // Form will submit normally
            });
            
            // Auto-focus username field
            document.getElementById('username').focus();
        });
    </script>
</body>
</html>
<?php
// End output buffering and send content
ob_end_flush();
?>
