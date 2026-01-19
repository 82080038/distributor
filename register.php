<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

if (isset($_GET['alamat_action'])) {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_GET['alamat_action'];
    $data = [];

    if ($action === 'kabupaten' && isset($_GET['province_id'])) {
        $provinceId = (int)$_GET['province_id'];
        $stmt = $conn_alamat->prepare("SELECT id, name FROM regencies WHERE province_id = ? ORDER BY name");
        if ($stmt) {
            $stmt->bind_param('i', $provinceId);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->close();
        }
    } elseif ($action === 'kecamatan' && isset($_GET['regency_id'])) {
        $regencyId = (int)$_GET['regency_id'];
        $stmt = $conn_alamat->prepare("SELECT id, name FROM districts WHERE regency_id = ? ORDER BY name");
        if ($stmt) {
            $stmt->bind_param('i', $regencyId);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->close();
        }
    } elseif ($action === 'desa' && isset($_GET['district_id'])) {
        $districtId = (int)$_GET['district_id'];
        $stmt = $conn_alamat->prepare("SELECT id, name FROM villages WHERE district_id = ? ORDER BY name");
        if ($stmt) {
            $stmt->bind_param('i', $districtId);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->close();
        }
    } elseif ($action === 'kodepos' && isset($_GET['village_id'])) {
        $villageId = (int)$_GET['village_id'];
        $stmt = $conn_alamat->prepare("SELECT postal_code FROM villages WHERE id = ? AND postal_code IS NOT NULL AND postal_code <> '' LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $villageId);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $data[] = ['id' => $row['postal_code'], 'name' => $row['postal_code']];
            }
            $stmt->close();
        }
    }

    echo json_encode($data);
    exit;
}

$error = '';
$success = '';

$users_exist = false;
$check_sql = "SELECT COUNT(*) AS cnt FROM information_schema.tables 
              WHERE table_schema = ? AND table_name = 'user'";
$stmt_check = $conn->prepare($check_sql);
if ($stmt_check) {
    $dbName = DB_NAME;
    $stmt_check->bind_param('s', $dbName);
    $stmt_check->execute();
    $res = $stmt_check->get_result();
    if ($row = $res->fetch_assoc()) {
        $users_exist = false;
        $count_users_sql = "SELECT COUNT(*) AS total FROM user";
        $stmt_users = $conn->prepare($count_users_sql);
        if ($stmt_users) {
            $stmt_users->execute();
            $res_users = $stmt_users->get_result();
            if ($u = $res_users->fetch_assoc()) {
                $users_exist = ((int)$u['total'] > 0);
            }
            $stmt_users->close();
        }
    }
    $stmt_check->close();
}

if ($users_exist) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
    require_login();
    require_role(['owner']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $name = clean($_POST['name'] ?? '');
    $role_name = clean($_POST['role'] ?? 'owner');
    $phone = clean($_POST['phone'] ?? '');
    $alamat = clean($_POST['alamat'] ?? '');
    $province_id = (int)($_POST['province_id'] ?? 0);
    $regency_id = (int)($_POST['regency_id'] ?? 0);
    $district_id = (int)($_POST['district_id'] ?? 0);
    $village_id = (int)($_POST['village_id'] ?? 0);
    $postal_code = clean($_POST['postal_code'] ?? '');
    $nama_perusahaan = clean($_POST['nama_perusahaan'] ?? '');
    $is_first_user = !$users_exist;
    if ($is_first_user) {
        $role_name = 'owner';
    }

    if ($is_first_user && $nama_perusahaan === '') {
        $error = 'Untuk pendaftaran pertama, Nama Perusahaan wajib diisi.';
    } elseif ($username === '' || $email === '' || $password === '' || $confirm_password === '' || $name === '' || $phone === '' || $alamat === '') {
        $error = 'Form belum lengkap. Silakan isi semua kolom wajib, termasuk Nomor HP dan Alamat Jalan.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid. Contoh: nama@domain.com.';
    } elseif (!preg_match('/^[0-9+\s-]{8,20}$/', $phone)) {
        $error = 'Nomor HP tidak valid. Gunakan hanya angka, spasi, tanda + atau -, minimal 8 karakter.';
    } elseif (strlen($password) < 8) {
        $error = 'Password terlalu pendek. Minimal 8 karakter.';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak sama dengan password.';
    } elseif ($province_id === 0 || $regency_id === 0 || $district_id === 0) {
        $error = 'Alamat wilayah belum lengkap. Pilih Provinsi, Kabupaten/Kota, dan Kecamatan.';
    } elseif ($postal_code === '') {
        $error = 'Kode pos belum terisi. Pilih Kelurahan/Desa agar kode pos terisi otomatis.';
    } else {
        $check_user_sql = "SELECT id_user FROM user WHERE username = ? OR email = ? LIMIT 1";
        $stmt = $conn->prepare($check_user_sql);
        if ($stmt) {
            $stmt->bind_param('ss', $username, $email);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->fetch_assoc()) {
                $error = 'Username atau email sudah digunakan';
            }
            $stmt->close();
        }

        if ($error === '') {
            $role_sql = "SELECT id FROM roles WHERE name = ? LIMIT 1";
            $stmt_role = $conn->prepare($role_sql);
            if ($stmt_role) {
                $stmt_role->bind_param('s', $role_name);
                $stmt_role->execute();
                $res_role = $stmt_role->get_result();
                $role_row = $res_role->fetch_assoc();
                $stmt_role->close();
            } else {
                $role_row = null;
            }

            if (!$role_row) {
                $error = 'Role tidak ditemukan di database';
            } else {
                $role_id = (int)$role_row['id'];
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $conn->begin_transaction();
                try {
                    $perusahaan_id = null;
                    if ($is_first_user) {
                        $sql_perusahaan = "INSERT INTO perusahaan (nama_perusahaan, alamat, kontak) VALUES (?, ?, ?)";
                        $stmt_perusahaan = $conn->prepare($sql_perusahaan);
                        if (!$stmt_perusahaan) {
                            throw new Exception('Gagal menyiapkan query perusahaan');
                        }
                        $kontak_perusahaan = $phone;
                        $stmt_perusahaan->bind_param('sss', $nama_perusahaan, $alamat, $kontak_perusahaan);
                        if (!$stmt_perusahaan->execute()) {
                            throw new Exception('Gagal menyimpan data perusahaan');
                        }
                        $perusahaan_id = $conn->insert_id;
                        $stmt_perusahaan->close();
                    } else {
                        $sql_get_perusahaan = "SELECT id_perusahaan FROM perusahaan ORDER BY id_perusahaan ASC LIMIT 1";
                        $res_perusahaan = $conn->query($sql_get_perusahaan);
                        if ($res_perusahaan && ($row_perusahaan = $res_perusahaan->fetch_assoc())) {
                            $perusahaan_id = (int)$row_perusahaan['id_perusahaan'];
                        } else {
                            throw new Exception('Data perusahaan belum tersedia. Silakan hubungi administrator.');
                        }
                    }

                    $sql_orang = "INSERT INTO orang (perusahaan_id, nama_lengkap, alamat, kontak, province_id, regency_id, district_id, village_id, postal_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt_orang = $conn->prepare($sql_orang);
                    if (!$stmt_orang) {
                        throw new Exception('Gagal menyiapkan query orang');
                    }
                    $stmt_orang->bind_param('isssiiiis', $perusahaan_id, $name, $alamat, $phone, $province_id, $regency_id, $district_id, $village_id, $postal_code);
                    if (!$stmt_orang->execute()) {
                        throw new Exception('Gagal menyimpan data orang');
                    }
                    $id_orang = $conn->insert_id;
                    $stmt_orang->close();

                    $insert_sql = "INSERT INTO user (id_orang, username, email, password_hash, role_id, status_aktif)
                                   VALUES (?, ?, ?, ?, ?, 1)";
                    $stmt_ins = $conn->prepare($insert_sql);
                    if (!$stmt_ins) {
                        throw new Exception('Gagal menyiapkan query user');
                    }
                    $stmt_ins->bind_param('isssi', $id_orang, $username, $email, $password_hash, $role_id);
                    if (!$stmt_ins->execute()) {
                        throw new Exception('Gagal mendaftarkan user');
                    }
                    $stmt_ins->close();

                    $conn->commit();
                    $success = 'User berhasil didaftarkan. Anda dapat login menggunakan username dan password yang baru dibuat.';
                } catch (Exception $e) {
                    $conn->rollback();
                    $error = $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User - Sistem Distribusi</title>
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
    .alert,
    .badge {
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
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3 text-center">Register User Sistem Distribusi</h5>
                        <?php if ($error !== ''): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if ($success !== ''): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                            <script>
                            setTimeout(function () {
                                window.location.href = 'login.php';
                            }, 3000);
                            </script>
                        <?php endif; ?>
                        <form method="post" id="registerForm" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nomor HP</label>
                                <input type="tel" name="phone" class="form-control" required>
                            </div>
                            <?php if (!$users_exist): ?>
                            <div class="mb-3">
                                <label class="form-label">Nama Perusahaan</label>
                                <input type="text" name="nama_perusahaan" class="form-control" required>
                            </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label class="form-label">Provinsi</label>
                                <select name="province_id" id="province_id" class="form-select" required>
                                    <option value="">Pilih Provinsi</option>
                                    <?php
                                    $prov_sql = "SELECT id, name FROM provinces ORDER BY name";
                                    $prov_res = $conn_alamat->query($prov_sql);
                                    if ($prov_res) {
                                        while ($p = $prov_res->fetch_assoc()) {
                                            echo '<option value="' . (int)$p['id'] . '">' . htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kabupaten / Kota</label>
                                <select name="regency_id" id="regency_id" class="form-select" required>
                                    <option value="">Pilih Provinsi terlebih dahulu</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kecamatan</label>
                                <select name="district_id" id="district_id" class="form-select" required>
                                    <option value="">Pilih Kabupaten/Kota terlebih dahulu</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kelurahan / Desa</label>
                                <select name="village_id" id="village_id" class="form-select">
                                    <option value="">Pilih Kecamatan terlebih dahulu</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat Jalan</label>
                                <input type="text" name="alamat" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" name="postal_code" id="postal_code" class="form-control" readonly required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" minlength="8" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="confirm_password" class="form-control" minlength="8" required>
                            </div>
                            <?php if ($users_exist): ?>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select">
                                    <option value="owner">Owner</option>
                                    <option value="manager">Manager</option>
                                    <option value="staff">Staff</option>
                                </select>
                            </div>
                            <?php else: ?>
                                <input type="hidden" name="role" value="owner">
                            <?php endif; ?>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Register</button>
                                <a href="login.php" class="btn btn-outline-secondary">Kembali ke Login</a>
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
        var $form = $('#registerForm');
        var $provinceSelect = $('#province_id');
        var $regencySelect = $('#regency_id');
        var $districtSelect = $('#district_id');
        var $villageSelect = $('#village_id');
        var $postalInput = $('#postal_code');

        $provinceSelect.on('change', function () {
            var provId = $(this).val();
            AppUtil.resetSelect($regencySelect, 'Pilih Kabupaten/Kota');
            AppUtil.resetSelect($districtSelect, 'Pilih Kecamatan');
            AppUtil.resetSelect($villageSelect, 'Pilih Kelurahan/Desa');
            $postalInput.val('');
            if (provId) {
                AppUtil.loadOptions({
                    url: 'register.php?alamat_action=kabupaten&province_id=' + encodeURIComponent(provId),
                    $select: $regencySelect,
                    placeholder: 'Pilih Kabupaten/Kota'
                });
            }
        });

        $regencySelect.on('change', function () {
            var regId = $(this).val();
            AppUtil.resetSelect($districtSelect, 'Pilih Kecamatan');
            AppUtil.resetSelect($villageSelect, 'Pilih Kelurahan/Desa');
            $postalInput.val('');
            if (regId) {
                AppUtil.loadOptions({
                    url: 'register.php?alamat_action=kecamatan&regency_id=' + encodeURIComponent(regId),
                    $select: $districtSelect,
                    placeholder: 'Pilih Kecamatan'
                });
            }
        });

        $districtSelect.on('change', function () {
            var distId = $(this).val();
            AppUtil.resetSelect($villageSelect, 'Pilih Kelurahan/Desa');
            $postalInput.val('');
            if (distId) {
                AppUtil.loadOptions({
                    url: 'register.php?alamat_action=desa&district_id=' + encodeURIComponent(distId),
                    $select: $villageSelect,
                    placeholder: 'Pilih Kelurahan/Desa'
                });
            }
        });

        $villageSelect.on('change', function () {
            var villId = $(this).val();
            $postalInput.val('');
            if (villId) {
                $.getJSON('register.php?alamat_action=kodepos&village_id=' + encodeURIComponent(villId), function (data) {
                    if (data.length > 0) {
                        $postalInput.val(data[0].name);
                    }
                });
            }
        });

        AppUtil.setupFocusNavigation($form);
    });
    </script>
</body>
</html>
