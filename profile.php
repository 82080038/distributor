<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
require_login();

$error = '';
$success = '';

if (isset($_GET['ajax'])) {
    header('Content-Type: application/json; charset=utf-8');
    $ajaxMode = $_GET['ajax'];
    $response = ['success' => false, 'data' => []];

    if ($ajaxMode === 'get_regencies' && isset($_GET['province_id'])) {
        $provinceId = (int)$_GET['province_id'];
        if ($provinceId > 0 && isset($conn_alamat) && $conn_alamat->connect_error === null) {
            $sql = "SELECT id, name FROM regencies WHERE province_id = ? ORDER BY name";
            $stmt = $conn_alamat->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('i', $provinceId);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($res) {
                    $regencies = [];
                    while ($row = $res->fetch_assoc()) {
                        $regencies[] = $row;
                    }
                    $response['success'] = true;
                    $response['data'] = $regencies;
                }
                $stmt->close();
            }
        }
    } elseif ($ajaxMode === 'get_districts' && isset($_GET['regency_id'])) {
        $regencyId = (int)$_GET['regency_id'];
        if ($regencyId > 0 && isset($conn_alamat) && $conn_alamat->connect_error === null) {
            $sql = "SELECT id, name FROM districts WHERE regency_id = ? ORDER BY name";
            $stmt = $conn_alamat->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('i', $regencyId);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($res) {
                    $districts = [];
                    while ($row = $res->fetch_assoc()) {
                        $districts[] = $row;
                    }
                    $response['success'] = true;
                    $response['data'] = $districts;
                }
                $stmt->close();
            }
        }
    } elseif ($ajaxMode === 'get_villages' && isset($_GET['district_id'])) {
        $districtId = (int)$_GET['district_id'];
        if ($districtId > 0 && isset($conn_alamat) && $conn_alamat->connect_error === null) {
            $sql = "SELECT id, name, postal_code FROM villages WHERE district_id = ? ORDER BY name";
            $stmt = $conn_alamat->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('i', $districtId);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($res) {
                    $villages = [];
                    while ($row = $res->fetch_assoc()) {
                        $villages[] = $row;
                    }
                    $response['success'] = true;
                    $response['data'] = $villages;
                }
                $stmt->close();
            }
        }
    }

    echo json_encode($response);
    exit;
}

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
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'address_helper.php';
    $phone = clean($_POST['phone'] ?? '');
    
    $address_validation = validate_address_fields('', true);
    
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
    $sql = "SELECT u.id_user, u.username, o.nama_lengkap, o.alamat, o.kontak, 
                   o.province_id, o.regency_id, o.district_id, o.village_id, o.postal_code, o.tipe_alamat,
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

include __DIR__ . DIRECTORY_SEPARATOR . 'template.php';
