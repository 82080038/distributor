<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
require_login();
require_once __DIR__ . DIRECTORY_SEPARATOR . 'alamat_manager.php';

// Setup AJAX endpoints untuk alamat
setup_alamat_ajax_endpoints();

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

if ($user && isset($user['id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'alamat_manager.php';
    $phone = clean($_POST['phone'] ?? '');
    
    $address_validation = validate_alamat_data('', true);
    
    if ($phone === '') {
        $error = 'Nomor HP wajib diisi.';
    } elseif (!preg_match('/^[0-9+\s-]{8,20}$/', $phone)) {
        $error = 'Nomor HP tidak valid. Gunakan hanya angka, spasi, tanda + atau -, minimal 8 karakter.';
    } elseif (!$address_validation['valid']) {
        $error = 'Alamat belum lengkap: ' . implode(', ', $address_validation['errors']);
    } else {
        $userId = (int)$user['id'];
        $address_data = $address_validation['data'];
        
        $sqlUpdate = "UPDATE orang o
                      JOIN user u ON u.id_orang = o.id_orang
                      SET o.kontak = ?, o.alamat = ?, o.province_id = ?, o.regency_id = ?, o.district_id = ?, o.village_id = ?, o.postal_code = ?, o.tipe_alamat = ?
                      WHERE u.id_user = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        if ($stmtUpdate) {
            $stmtUpdate->bind_param('ssiiiiiss', $phone, $address_data['street_address'], $address_data['province_id'], $address_data['regency_id'], $address_data['district_id'], $address_data['village_id'], $address_data['postal_code'], $address_data['tipe_alamat'], $userId);
            if ($stmtUpdate->execute()) {
                $success = 'Alamat pribadi berhasil diperbarui.';
            } else {
                $error = 'Gagal menyimpan perubahan alamat pribadi.';
            }
            $stmtUpdate->close();
        } else {
            $error = 'Gagal menyiapkan query update alamat pribadi.';
        }
    }
}

if ($user && isset($user['id'])) {
    $userId = (int)$user['id'];
    $profile = load_alamat_by_entity($userId, 'user', $conn);
    
    if ($profile) {
        $sql = "SELECT u.id_user, u.username, o.nama_lengkap, o.kontak, 
                       r.name AS role_name,
                       p.nama_perusahaan AS perusahaan_nama,
                       p.alamat AS perusahaan_alamat,
                       p.kontak AS perusahaan_kontak
                FROM user_accounts u
                JOIN orang o ON u.id_orang = o.id_orang
                JOIN roles r ON u.role_id = r.id
                LEFT JOIN perusahaan p ON o.perusahaan_id = p.id_perusahaan
                WHERE u.id_user = ?
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $profile = array_merge($profile, $row);
            }
            $stmt->close();
        }
    }
}

$page_title = 'Profil User';
$content_view = 'profile_view_new.php';

include __DIR__ . DIRECTORY_SEPARATOR . 'template.php';
?>
