<?php
require_once __DIR__ . '/auth.php';
require_login();

$ajaxMode = isset($_GET['ajax']) ? $_GET['ajax'] : '';
if ($ajaxMode === 'search_customers') {
    header('Content-Type: application/json; charset=utf-8');
    $term = isset($_GET['q']) ? trim($_GET['q']) : '';
    $items = [];
    if ($term !== '') {
        $sqlAjax = "SELECT id_orang, nama_lengkap FROM orang WHERE is_customer = 1 AND is_active = 1 AND nama_lengkap LIKE ? ORDER BY nama_lengkap LIMIT 20";
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
$edit_customer = null;
$customers = [];
$status = isset($_GET['status']) ? $_GET['status'] : 'active';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$role = isset($_GET['role']) ? $_GET['role'] : 'all';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
        $nama = clean($_POST['nama'] ?? '');
        $alamat = clean($_POST['alamat'] ?? '');
        $kontak = clean($_POST['kontak'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $also_supplier = isset($_POST['also_supplier']) ? 1 : 0;

        if ($nama === '') {
            $error = 'Nama pembeli wajib diisi.';
        } else {
            if ($id > 0) {
                $sql = "UPDATE orang SET nama_lengkap = ?, alamat = ?, kontak = ?, is_supplier = ?, is_customer = 1, is_active = ? WHERE id_orang = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('sssiii', $nama, $alamat, $kontak, $also_supplier, $is_active, $id);
                    if ($stmt->execute()) {
                        $success = 'Data pembeli berhasil diperbarui.';
                    } else {
                        $error = 'Gagal menyimpan perubahan data pembeli.';
                    }
                    $stmt->close();
                } else {
                    $error = 'Gagal menyiapkan query update pembeli.';
                }
            } else {
                $sql = "INSERT INTO orang (nama_lengkap, alamat, kontak, is_supplier, is_customer, is_active) VALUES (?, ?, ?, ?, 1, ?)";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('sssii', $nama, $alamat, $kontak, $also_supplier, $is_active);
                    if ($stmt->execute()) {
                        $success = 'Data pembeli baru berhasil disimpan.';
                    } else {
                        $error = 'Gagal menyimpan data pembeli.';
                    }
                    $stmt->close();
                } else {
                    $error = 'Gagal menyiapkan query insert pembeli.';
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
        $id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
        $newStatus = null;
        if ($id > 0) {
            $sqlGet = "SELECT is_active FROM orang WHERE id_orang = ? AND is_customer = 1 LIMIT 1";
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

                    $sqlToggle = "UPDATE orang SET is_active = ? WHERE id_orang = ? AND is_customer = 1";
                    $stmtToggle = $conn->prepare($sqlToggle);
                    if ($stmtToggle) {
                        $stmtToggle->bind_param('ii', $new, $id);
                        if ($stmtToggle->execute()) {
                            if ($new === 1) {
                                $success = 'Pembeli telah diaktifkan kembali.';
                            } else {
                                $success = 'Pembeli telah dinonaktifkan.';
                            }
                        } else {
                            $error = 'Gagal mengubah status pembeli.';
                        }
                        $stmtToggle->close();
                    } else {
                        $error = 'Gagal menyiapkan query perubahan status pembeli.';
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
    $sqlEdit = "SELECT id_orang, nama_lengkap, alamat, kontak, is_active, is_supplier FROM orang WHERE id_orang = ? AND is_customer = 1 LIMIT 1";
    $stmtEdit = $conn->prepare($sqlEdit);
    if ($stmtEdit) {
        $stmtEdit->bind_param('i', $edit_id);
        $stmtEdit->execute();
        $resEdit = $stmtEdit->get_result();
        if ($rowEdit = $resEdit->fetch_assoc()) {
            $edit_customer = $rowEdit;
        }
        $stmtEdit->close();
    }
}

$conditions = ['is_customer = 1'];
if ($status === 'active') {
    $conditions[] = 'is_active = 1';
} elseif ($status === 'inactive') {
    $conditions[] = 'is_active = 0';
}
$role = $role === 'dual' ? 'dual' : 'all';
if ($role === 'dual') {
    $conditions[] = 'is_supplier = 1';
}
$whereSql = implode(' AND ', $conditions);
$sqlList = "SELECT id_orang, nama_lengkap, alamat, kontak, is_active, is_supplier, created_at, updated_at FROM orang WHERE $whereSql";
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
                $customers[] = $row;
            }
        }
        $stmtList->close();
    }
} else {
    $resList = $conn->query($sqlList);
    if ($resList) {
        while ($row = $resList->fetch_assoc()) {
            $customers[] = $row;
        }
    }
}

$page_title = 'Pembeli';
$content_view = 'customers_view.php';

include __DIR__ . '/template.php';
