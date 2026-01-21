<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
require_login();

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

$ajaxMode = isset($_GET['ajax']) ? $_GET['ajax'] : '';
if ($ajaxMode === 'search_suppliers') {
    header('Content-Type: application/json; charset=utf-8');
    $term = isset($_GET['q']) ? trim($_GET['q']) : '';
    $items = [];
    if ($term !== '') {
        $sqlAjax = "SELECT id_orang, nama_lengkap FROM orang WHERE is_supplier = 1 AND is_active = 1 AND nama_lengkap LIKE ? ORDER BY nama_lengkap LIMIT 20";
        $stmtAjax = $conn->prepare($sqlAjax);
        if ($stmtAjax) {
            $like = '%' . $term . '%';
            $stmtAjax->bind_param('s', $like);
            $stmtAjax->execute();
            $resAjax = $stmtAjax->get_result();
            if ($resAjax) {
                while ($row = $resAjax->fetch_assoc()) {
                    $items[] = [
                        'id' => (int)$row['id_orang'],
                        'name' => $row['nama_lengkap'],
                    ];
                }
            }
            $stmtAjax->close();
        }
    }
    echo json_encode($items);
    exit;
}

$error = '';
$success = '';
$edit_supplier = null;
$suppliers = [];
$status = isset($_GET['status']) ? $_GET['status'] : 'active';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$role = isset($_GET['role']) ? $_GET['role'] : 'all';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'address_helper.php';
        $id = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
        $nama = clean($_POST['nama'] ?? '');
        $kontak = clean($_POST['kontak'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $also_customer = isset($_POST['also_customer']) ? 1 : 0;
        
        $address_validation = validate_address_fields('', true);
        $alamat = $address_validation['data']['street_address'];
        $province_id = $address_validation['data']['province_id'];
        $regency_id = $address_validation['data']['regency_id'];
        $district_id = $address_validation['data']['district_id'];
        $village_id = $address_validation['data']['village_id'];
        $postal_code = $address_validation['data']['postal_code'];

        if ($nama === '') {
            $error = 'Nama pemasok wajib diisi.';
        } elseif (!$address_validation['valid']) {
            $error = 'Alamat belum lengkap: ' . implode(', ', $address_validation['errors']);
        } else {
            if ($id > 0) {
                $sql = "UPDATE orang SET nama_lengkap = ?, alamat = ?, kontak = ?, is_supplier = 1, is_customer = ?, is_active = ?, province_id = ?, regency_id = ?, district_id = ?, village_id = ?, postal_code = ?, tipe_alamat = ? WHERE id_orang = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('ssssiiiiiii', $nama, $alamat, $kontak, $also_customer, $is_active, $province_id, $regency_id, $district_id, $village_id, $postal_code, $address_validation['data']['tipe_alamat'], $id);
                    if ($stmt->execute()) {
                        $success = 'Data pemasok berhasil diperbarui.';
                    } else {
                        $error = 'Gagal menyimpan perubahan data pemasok.';
                    }
                    $stmt->close();
                } else {
                    $error = 'Gagal menyiapkan query update pemasok.';
                }
            } else {
                $sql = "INSERT INTO orang (nama_lengkap, alamat, kontak, is_supplier, is_customer, is_active, province_id, regency_id, district_id, village_id, postal_code, tipe_alamat) VALUES (?, ?, ?, 1, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('sssiiiiiiii', $nama, $alamat, $kontak, $also_customer, $is_active, $province_id, $regency_id, $district_id, $village_id, $postal_code, $address_validation['data']['tipe_alamat']);
                    if ($stmt->execute()) {
                        $success = 'Data pemasok baru berhasil disimpan.';
                    } else {
                        $error = 'Gagal menyimpan data pemasok.';
                    }
                    $stmt->close();
                } else {
                    $error = 'Gagal menyiapkan query insert pemasok.';
                }
            }
        }

        if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
            header('Content-Type: application/json; charset=utf-8');
            $response = [
                'success' => $error === '',
            ];
            if ($error !== '') {
                $response['message'] = $error;
            } elseif ($success !== '') {
                $response['message'] = $success;
            }
            echo json_encode($response);
            exit;
        }
    } elseif ($action === 'toggle') {
        $id = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
        $newStatus = null;
        if ($id > 0) {
            $sqlGet = "SELECT is_active FROM orang WHERE id_orang = ? AND is_supplier = 1 LIMIT 1";
            $stmtGet = $conn->prepare($sqlGet);
            if ($stmtGet) {
                $stmtGet->bind_param('i', $id);
                $stmtGet->execute();
                $resGet = $stmtGet->get_result();
                if ($row = $resGet->fetch_assoc()) {
                    $current = (int)$row['is_active'];
                    $new = $current === 1 ? 0 : 1;
                    $newStatus = $new;
                    $stmtGet->close();

                    $sqlToggle = "UPDATE orang SET is_active = ? WHERE id_orang = ? AND is_supplier = 1";
                    $stmtToggle = $conn->prepare($sqlToggle);
                    if ($stmtToggle) {
                        $stmtToggle->bind_param('ii', $new, $id);
                        if ($stmtToggle->execute()) {
                            if ($new === 1) {
                                $success = 'Pemasok telah diaktifkan kembali.';
                            } else {
                                $success = 'Pemasok telah dinonaktifkan.';
                            }
                        } else {
                            $error = 'Gagal mengubah status pemasok.';
                        }
                        $stmtToggle->close();
                    } else {
                        $error = 'Gagal menyiapkan query perubahan status pemasok.';
                    }
                } else {
                    $stmtGet->close();
                }
            }
        }

        if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
            header('Content-Type: application/json; charset=utf-8');
            $response = [
                'success' => $error === '' && $newStatus !== null,
                'new_status' => $newStatus,
            ];
            if ($error !== '') {
                $response['message'] = $error;
            } elseif ($success !== '') {
                $response['message'] = $success;
            }
            echo json_encode($response);
            exit;
        }
    }
}

$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($edit_id > 0) {
    $sqlEdit = "SELECT id_orang, nama_lengkap, alamat, kontak, is_active, is_customer, province_id, regency_id, district_id, village_id, postal_code, tipe_alamat FROM orang WHERE id_orang = ? AND is_supplier = 1 LIMIT 1";
    $stmtEdit = $conn->prepare($sqlEdit);
    if ($stmtEdit) {
        $stmtEdit->bind_param('i', $edit_id);
        $stmtEdit->execute();
        $resEdit = $stmtEdit->get_result();
        if ($rowEdit = $resEdit->fetch_assoc()) {
            $edit_supplier = $rowEdit;
        }
        $stmtEdit->close();
    }
}

$conditions = ['is_supplier = 1'];
if ($status === 'active') {
    $conditions[] = 'is_active = 1';
} elseif ($status === 'inactive') {
    $conditions[] = 'is_active = 0';
}
$role = $role === 'dual' ? 'dual' : 'all';
if ($role === 'dual') {
    $conditions[] = 'is_customer = 1';
}
$whereSql = implode(' AND ', $conditions);
$sqlList = "SELECT id_orang, nama_lengkap, alamat, kontak, is_active, is_customer, created_at, updated_at FROM orang WHERE $whereSql";
$params = [];
$types = '';
if ($q !== '') {
    $sqlList .= " AND nama_lengkap LIKE ?";
    $params[] = '%' . $q . '%';
    $types .= 's';
}
$sqlList .= " ORDER BY nama_lengkap";
if (!empty($params)) {
    $stmtList = $conn->prepare($sqlList);
    if ($stmtList) {
        $stmtList->bind_param($types, ...$params);
        $stmtList->execute();
        $resList = $stmtList->get_result();
        if ($resList) {
            while ($row = $resList->fetch_assoc()) {
                $suppliers[] = $row;
            }
        }
        $stmtList->close();
    }
} else {
    $resList = $conn->query($sqlList);
    if ($resList) {
        while ($row = $resList->fetch_assoc()) {
            $suppliers[] = $row;
        }
    }
}

$page_title = 'Pemasok';
$content_view = 'suppliers_view.php';

include __DIR__ . DIRECTORY_SEPARATOR . 'template.php';
