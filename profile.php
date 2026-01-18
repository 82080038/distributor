<?php
require_once __DIR__ . '/auth.php';
require_login();

$error = '';
$success = '';

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

$user = current_user();
$profile = null;

if ($user && isset($user['id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = clean($_POST['phone'] ?? '');
    $alamat = clean($_POST['alamat'] ?? '');
    $province_id = (int)($_POST['province_id'] ?? 0);
    $regency_id = (int)($_POST['regency_id'] ?? 0);
    $district_id = (int)($_POST['district_id'] ?? 0);
    $village_id = (int)($_POST['village_id'] ?? 0);
    $postal_code = clean($_POST['postal_code'] ?? '');

    if ($phone === '' || $alamat === '') {
        $error = 'Form alamat belum lengkap. Silakan isi Nomor HP dan Alamat Jalan.';
    } elseif (!preg_match('/^[0-9+\s-]{8,20}$/', $phone)) {
        $error = 'Nomor HP tidak valid. Gunakan hanya angka, spasi, tanda + atau -, minimal 8 karakter.';
    } elseif ($province_id === 0 || $regency_id === 0 || $district_id === 0) {
        $error = 'Alamat wilayah belum lengkap. Pilih Provinsi, Kabupaten/Kota, dan Kecamatan.';
    } elseif ($postal_code === '') {
        $error = 'Kode pos belum terisi. Pilih Kelurahan/Desa agar kode pos terisi otomatis.';
    } else {
        $userId = (int)$user['id'];
        $sqlUpdate = "UPDATE orang o
                      JOIN user u ON u.id_orang = o.id_orang
                      SET o.kontak = ?, o.alamat = ?, o.province_id = ?, o.regency_id = ?, o.district_id = ?, o.village_id = ?, o.postal_code = ?
                      WHERE u.id_user = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        if ($stmtUpdate) {
            $stmtUpdate->bind_param('ssiiiiis', $phone, $alamat, $province_id, $regency_id, $district_id, $village_id, $postal_code, $userId);
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
    $sql = "SELECT u.id_user, u.username, o.nama_lengkap, o.alamat, o.kontak, 
                   o.province_id, o.regency_id, o.district_id, o.village_id, o.postal_code,
                   r.name AS role_name,
                   p.nama_perusahaan AS perusahaan_nama,
                   p.alamat AS perusahaan_alamat,
                   p.kontak AS perusahaan_kontak
            FROM user u
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
            $profile = $row;
        }
        $stmt->close();
    }

    if ($profile) {
        $provinceName = '';
        $regencyName = '';
        $districtName = '';
        $villageName = '';

        if (!empty($profile['province_id'])) {
            $stmtProv = $conn_alamat->prepare("SELECT name FROM provinces WHERE id = ? LIMIT 1");
            if ($stmtProv) {
                $stmtProv->bind_param('i', $profile['province_id']);
                $stmtProv->execute();
                $resProv = $stmtProv->get_result();
                if ($p = $resProv->fetch_assoc()) {
                    $provinceName = $p['name'];
                }
                $stmtProv->close();
            }
        }

        if (!empty($profile['regency_id'])) {
            $stmtReg = $conn_alamat->prepare("SELECT name FROM regencies WHERE id = ? LIMIT 1");
            if ($stmtReg) {
                $stmtReg->bind_param('i', $profile['regency_id']);
                $stmtReg->execute();
                $resReg = $stmtReg->get_result();
                if ($r = $resReg->fetch_assoc()) {
                    $regencyName = $r['name'];
                }
                $stmtReg->close();
            }
        }

        if (!empty($profile['district_id'])) {
            $stmtDis = $conn_alamat->prepare("SELECT name FROM districts WHERE id = ? LIMIT 1");
            if ($stmtDis) {
                $stmtDis->bind_param('i', $profile['district_id']);
                $stmtDis->execute();
                $resDis = $stmtDis->get_result();
                if ($d = $resDis->fetch_assoc()) {
                    $districtName = $d['name'];
                }
                $stmtDis->close();
            }
        }

        if (!empty($profile['village_id'])) {
            $stmtVil = $conn_alamat->prepare("SELECT name FROM villages WHERE id = ? LIMIT 1");
            if ($stmtVil) {
                $stmtVil->bind_param('i', $profile['village_id']);
                $stmtVil->execute();
                $resVil = $stmtVil->get_result();
                if ($v = $resVil->fetch_assoc()) {
                    $villageName = $v['name'];
                }
                $stmtVil->close();
            }
        }

        $profile['province_name'] = $provinceName;
        $profile['regency_name'] = $regencyName;
        $profile['district_name'] = $districtName;
        $profile['village_name'] = $villageName;
    }
}

$page_title = 'Profil User';
$content_view = 'profile_view.php';

include __DIR__ . '/template.php';
