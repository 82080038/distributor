<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Form login belum lengkap. Silakan isi Username dan Password.';
    } else {
        $sql = "SELECT u.id_user, u.username, o.nama_lengkap, o.perusahaan_id, u.password_hash, u.branch_id, r.name AS role_name 
                FROM user u 
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
                    $branch_id = isset($row['branch_id']) ? (int)$row['branch_id'] : 0;
                    $perusahaan_id = isset($row['perusahaan_id']) ? (int)$row['perusahaan_id'] : 0;

                    if ($branch_id <= 0) {
                        if ($perusahaan_id > 0) {
                            $stmtBranch = $conn->prepare("SELECT id FROM branches WHERE perusahaan_id = ? ORDER BY id ASC LIMIT 1");
                            if ($stmtBranch) {
                                $stmtBranch->bind_param('i', $perusahaan_id);
                                $stmtBranch->execute();
                                $resBranch = $stmtBranch->get_result();
                                if ($resBranch && ($rowBranch = $resBranch->fetch_assoc())) {
                                    $branch_id = (int)$rowBranch['id'];
                                }
                                $stmtBranch->close();
                            }

                            if ($branch_id <= 0) {
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

                                $branch_code = 'CBG' . $perusahaan_id . '001';
                                $branch_name = $nama_perusahaan !== '' ? $nama_perusahaan : 'Cabang Utama';

                                $stmtInsertBranch = $conn->prepare("INSERT INTO branches (perusahaan_id, code, name, phone, street_address, postal_code, owner_id) VALUES (?, ?, ?, ?, ?, '', ?)");
                                if ($stmtInsertBranch) {
                                    $stmtInsertBranch->bind_param('issssi', $perusahaan_id, $branch_code, $branch_name, $kontak_perusahaan, $alamat_perusahaan, $user_id);
                                    if ($stmtInsertBranch->execute()) {
                                        $branch_id = (int)$conn->insert_id;
                                    }
                                    $stmtInsertBranch->close();
                                }
                            }

                            if ($branch_id > 0) {
                                $updateBranchSql = "UPDATE user SET branch_id = ? WHERE id_user = ?";
                                $stmtUpdateBranch = $conn->prepare($updateBranchSql);
                                if ($stmtUpdateBranch) {
                                    $stmtUpdateBranch->bind_param('ii', $branch_id, $user_id);
                                    $stmtUpdateBranch->execute();
                                    $stmtUpdateBranch->close();
                                }
                            }
                        }
                    }

                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['name'] = $row['nama_lengkap'];
                    $_SESSION['role'] = $row['role_name'];
                    $_SESSION['branch_id'] = $branch_id > 0 ? $branch_id : null;

                    $update_sql = "UPDATE user SET last_login_at = NOW() WHERE id_user = ?";
                    $stmt_update = $conn->prepare($update_sql);
                    if ($stmt_update) {
                        $stmt_update->bind_param('i', $_SESSION['user_id']);
                        $stmt_update->execute();
                        $stmt_update->close();
                    }
                    redirect('index.php');
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
    <script>
    (function () {
        var theme = 'light';
        try {
            var stored = localStorage.getItem('app_theme');
            if (stored === 'dark') {
                theme = 'dark';
            }
        } catch (e) {
        }
        document.documentElement.setAttribute('data-bs-theme', theme);
    })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: var(--bs-body-bg);
        color: var(--bs-body-color);
        transition: background-color 0.25s ease, color 0.25s ease;
    }
    .card,
    .btn,
    .form-control,
    .form-select,
    .alert {
        transition: background-color 0.25s ease, color 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    }
    html[data-bs-theme="dark"] {
        --bs-body-bg: #020617;
        --bs-body-color: #e5e7eb;
        --bs-border-color: #1f2937;
        --bs-card-bg: #020617;
        --bs-card-border-color: #1f2937;
        --bs-dropdown-bg: #020617;
        --bs-dropdown-link-color: #e5e7eb;
        --bs-dropdown-link-hover-bg: #1f2937;
        --bs-table-bg: transparent;
        --bs-table-striped-bg: rgba(148,163,184,0.12);
        --bs-table-striped-color: inherit;
        --bs-table-hover-bg: rgba(148,163,184,0.18);
        --bs-primary: #3b82f6;
        --bs-primary-rgb: 59,130,246;
        --bs-secondary: #64748b;
        --bs-secondary-rgb: 100,116,139;
        --bs-success: #22c55e;
        --bs-success-rgb: 34,197,94;
        --bs-danger: #ef4444;
        --bs-danger-rgb: 239,68,68;
        --bs-warning: #eab308;
        --bs-warning-rgb: 234,179,8;
        --bs-info: #0ea5e9;
        --bs-info-rgb: 14,165,233;
    }
    html[data-bs-theme="dark"] .card {
        box-shadow: 0 0.25rem 0.75rem rgba(15,23,42,0.75);
    }
    </style>
</head>
<body class="bg-body-tertiary">
    <div class="container position-relative">
        <div class="position-absolute top-0 end-0 p-3">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-theme-toggle>Tema</button>
        </div>
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3 text-center">Login Sistem Distribusi</h5>
                        <?php if ($error !== ''): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="post" id="loginForm" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Login</button>
                                <a href="register.php" class="btn btn-outline-secondary">Daftar Akun Baru</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(function () {
        var $form = $('#loginForm');
        if ($form.length === 0) {
            return;
        }
        AppUtil.setupFocusNavigation($form);
    });
    </script>
</body>
</html>
