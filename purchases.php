<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
require_login();

function log_purchase_audit($conn, $purchase_id, $action, $total_before, $total_after, $user_id)
{
    $purchase_id = (int)$purchase_id;
    $user_id = (int)$user_id;
    if ($purchase_id <= 0 || $user_id <= 0) {
        return;
    }
    $resAuditCheck = $conn->query("SHOW TABLES LIKE 'purchase_audit_log'");
    if (!$resAuditCheck || $resAuditCheck->num_rows === 0) {
        if ($resAuditCheck) {
            $resAuditCheck->close();
        }
        return;
    }
    $resAuditCheck->close();
    $stmtAudit = $conn->prepare("INSERT INTO purchase_audit_log (purchase_id, action, total_before, total_after, performed_by) VALUES (?, ?, ?, ?, ?)");
    if ($stmtAudit) {
        $actionStr = (string)$action;
        $totalBefore = $total_before !== null ? (float)$total_before : null;
        $totalAfter = $total_after !== null ? (float)$total_after : null;
        $stmtAudit->bind_param('isddi', $purchase_id, $actionStr, $totalBefore, $totalAfter, $user_id);
        $stmtAudit->execute();
        $stmtAudit->close();
    }
}

$user = current_user();
$user_id = $user['id'] ?? null;
$branch_id = $user['branch_id'] ?? null;

$ajaxMode = isset($_GET['ajax']) ? $_GET['ajax'] : '';
if ($ajaxMode === 'purchase_detail') {
    header('Content-Type: application/json; charset=utf-8');
    $purchase_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $response = [
        'success' => false,
    ];
    if ($purchase_id <= 0) {
        $response['message'] = 'Pembelian tidak ditemukan.';
        echo json_encode($response);
        exit;
    }

    $selected_purchase = null;
    if ($branch_id) {
        $stmtPurchase = $conn->prepare("SELECT id, branch_id, supplier_id, supplier_name, invoice_no, purchase_date, total_amount, notes, created_at FROM purchases WHERE id = ? AND branch_id = ?");
        if ($stmtPurchase) {
            $stmtPurchase->bind_param('ii', $purchase_id, $branch_id);
            $stmtPurchase->execute();
            $resPurchase = $stmtPurchase->get_result();
            if ($resPurchase && ($rowPurchase = $resPurchase->fetch_assoc())) {
                $selected_purchase = $rowPurchase;
            }
            $stmtPurchase->close();
        }
    } else {
        $stmtPurchase = $conn->prepare("SELECT id, branch_id, supplier_id, supplier_name, invoice_no, purchase_date, total_amount, notes, created_at FROM purchases WHERE id = ?");
        if ($stmtPurchase) {
            $stmtPurchase->bind_param('i', $purchase_id);
            $stmtPurchase->execute();
            $resPurchase = $stmtPurchase->get_result();
            if ($resPurchase && ($rowPurchase = $resPurchase->fetch_assoc())) {
                $selected_purchase = $rowPurchase;
            }
            $stmtPurchase->close();
        }
    }

    if ($selected_purchase === null) {
        $response['message'] = 'Pembelian tidak ditemukan atau tidak dapat diakses.';
        echo json_encode($response);
        exit;
    }

    $purchase = [
        'id' => (int)$selected_purchase['id'],
        'supplier_id' => isset($selected_purchase['supplier_id']) ? (int)$selected_purchase['supplier_id'] : 0,
        'supplier_name' => $selected_purchase['supplier_name'],
        'invoice_no' => $selected_purchase['invoice_no'],
        'purchase_date' => $selected_purchase['purchase_date'],
        'formatted_date' => format_date_id($selected_purchase['purchase_date']),
        'total_amount' => (float)$selected_purchase['total_amount'],
        'formatted_total' => number_format((float)$selected_purchase['total_amount'], 2, ',', '.'),
        'notes' => $selected_purchase['notes'] ?? '',
    ];

    $items = [];
    $stmtItems = $conn->prepare("SELECT pi.product_id, p.name AS product_name, p.unit AS unit, pi.qty, pi.price, pi.subtotal FROM purchase_items pi LEFT JOIN products p ON p.id = pi.product_id WHERE pi.purchase_id = ? ORDER BY pi.id");
    if ($stmtItems) {
        $stmtItems->bind_param('i', $purchase_id);
        $stmtItems->execute();
        $resItems = $stmtItems->get_result();
        if ($resItems) {
            while ($rowItem = $resItems->fetch_assoc()) {
                $items[] = [
                    'product_id' => (int)$rowItem['product_id'],
                    'product_name' => $rowItem['product_name'] ?? '',
                    'unit' => $rowItem['unit'] ?? '',
                    'qty' => (float)$rowItem['qty'],
                    'price' => (float)$rowItem['price'],
                    'subtotal' => (float)$rowItem['subtotal'],
                    'formatted_price' => number_format((float)$rowItem['price'], 2, ',', '.'),
                    'formatted_subtotal' => number_format((float)$rowItem['subtotal'], 2, ',', '.'),
                ];
            }
        }
        $stmtItems->close();
    }

    $response['success'] = true;
    $response['purchase'] = $purchase;
    $response['items'] = $items;
    echo json_encode($response);
    exit;
}

// AJAX endpoints untuk data alamat
if ($ajaxMode === 'get_regencies') {
    header('Content-Type: application/json; charset=utf-8');
    $province_id = isset($_GET['province_id']) ? (int)$_GET['province_id'] : 0;
    $response = ['success' => false, 'data' => []];
    
    if ($province_id > 0 && isset($conn_alamat) && $conn_alamat->connect_error === null) {
        $sql = "SELECT id, name FROM regencies WHERE province_id = ? ORDER BY name";
        $stmt = $conn_alamat->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $province_id);
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
    echo json_encode($response);
    exit;
}

if ($ajaxMode === 'get_districts') {
    header('Content-Type: application/json; charset=utf-8');
    $regency_id = isset($_GET['regency_id']) ? (int)$_GET['regency_id'] : 0;
    $response = ['success' => false, 'data' => []];
    
    if ($regency_id > 0 && isset($conn_alamat) && $conn_alamat->connect_error === null) {
        $sql = "SELECT id, name FROM districts WHERE regency_id = ? ORDER BY name";
        $stmt = $conn_alamat->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $regency_id);
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
    echo json_encode($response);
    exit;
}

if ($ajaxMode === 'get_villages') {
    header('Content-Type: application/json; charset=utf-8');
    $district_id = isset($_GET['district_id']) ? (int)$_GET['district_id'] : 0;
    $response = ['success' => false, 'data' => []];
    
    if ($district_id > 0 && isset($conn_alamat) && $conn_alamat->connect_error === null) {
        $sql = "SELECT id, name, postal_code FROM villages WHERE district_id = ? ORDER BY name";
        $stmt = $conn_alamat->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $district_id);
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
    echo json_encode($response);
    exit;
}

// AJAX endpoint untuk mengambil data streets berdasarkan village
if ($ajaxMode === 'get_streets') {
    header('Content-Type: application/json; charset=utf-8');
    $village_id = isset($_GET['village_id']) ? (int)$_GET['village_id'] : 0;
    $response = ['success' => false, 'data' => []];
    
    if ($village_id > 0) {
        $sql = "SELECT id, name, type, rt, rw, postal_code FROM alamat_db.streets WHERE village_id = ? AND is_active = 1 ORDER BY name";
        $stmt = $conn_alamat->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $village_id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res) {
                $streets = [];
                while ($row = $res->fetch_assoc()) {
                    $streets[] = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'type' => $row['type'],
                        'rt' => $row['rt'],
                        'rw' => $row['rw'],
                        'postal_code' => $row['postal_code'],
                        'display_name' => $row['name'] . 
                            ($row['rt'] ? ' RT ' . $row['rt'] : '') . 
                            ($row['rw'] ? ' RW ' . $row['rw'] : '') .
                            ($row['postal_code'] ? ' (' . $row['postal_code'] . ')' : '')
                    ];
                }
                $response['success'] = true;
                $response['data'] = $streets;
            }
            $stmt->close();
        }
    }
    echo json_encode($response);
    exit;
}

// AJAX endpoint untuk mengambil data alamat supplier
if ($ajaxMode === 'get_supplier_address') {
    header('Content-Type: application/json; charset=utf-8');
    $supplier_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;
    $response = ['success' => false, 'data' => null];
    
    if ($supplier_id > 0) {
        // Prioritaskan id_alamat_orang dari tabel orang
        $sql = "SELECT a.*, oa.address_type 
                FROM addresses a 
                LEFT JOIN orang_addresses oa ON a.id = oa.address_id AND oa.orang_id = ? AND oa.address_type = 'supplier' AND oa.is_active = 1
                WHERE a.id = (SELECT id_alamat_orang FROM orang WHERE id_orang = ? AND id_alamat_orang IS NOT NULL)
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ii', $supplier_id, $supplier_id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && ($row = $res->fetch_assoc())) {
                $response['success'] = true;
                $response['data'] = [
                    'street_address' => $row['street_address'],
                    'street_id' => $row['street_id'],
                    'nomor_rumah' => $row['nomor_rumah'],
                    'nomor_bangunan' => $row['nomor_bangunan'],
                    'blok' => $row['blok'],
                    'lantai' => $row['lantai'],
                    'nomor_unit' => $row['nomor_unit'],
                    'patokan_lokasi' => $row['patokan_lokasi'],
                    'input_type' => $row['input_type'],
                    'province_id' => $row['province_id'],
                    'regency_id' => $row['regency_id'],
                    'district_id' => $row['district_id'],
                    'village_id' => $row['village_id'],
                    'postal_code' => $row['postal_code']
                ];
            }
            $stmt->close();
        }
        
        // Jika tidak ada id_alamat_orang, coba dari orang_addresses
        if (!$response['success']) {
            $sql = "SELECT a.*, oa.address_type 
                    FROM addresses a 
                    JOIN orang_addresses oa ON a.id = oa.address_id 
                    WHERE oa.orang_id = ? AND oa.address_type = 'supplier' AND oa.is_active = 1 
                    LIMIT 1";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('i', $supplier_id);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($res && ($row = $res->fetch_assoc())) {
                    $response['success'] = true;
                    $response['data'] = [
                        'street_address' => $row['street_address'],
                        'street_id' => $row['street_id'],
                        'nomor_rumah' => $row['nomor_rumah'],
                        'nomor_bangunan' => $row['nomor_bangunan'],
                        'blok' => $row['blok'],
                        'lantai' => $row['lantai'],
                        'nomor_unit' => $row['nomor_unit'],
                        'patokan_lokasi' => $row['patokan_lokasi'],
                        'input_type' => $row['input_type'],
                        'province_id' => $row['province_id'],
                        'regency_id' => $row['regency_id'],
                        'district_id' => $row['district_id'],
                        'village_id' => $row['village_id'],
                        'postal_code' => $row['postal_code']
                    ];
                }
                $stmt->close();
            }
        }
    }
    echo json_encode($response);
    exit;
}

if ($ajaxMode === 'update_supplier_address') {
    header('Content-Type: application/json; charset=utf-8');
    $supplier_id = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
    $alamat_detail = clean($_POST['alamat_detail'] ?? '');
    $street_id = isset($_POST['street_id']) ? (int)$_POST['street_id'] : 0;
    $province_id = isset($_POST['province_id']) ? (int)$_POST['province_id'] : 0;
    $regency_id = isset($_POST['regency_id']) ? (int)$_POST['regency_id'] : 0;
    $district_id = isset($_POST['district_id']) ? (int)$_POST['district_id'] : 0;
    $village_id = isset($_POST['village_id']) ? (int)$_POST['village_id'] : 0;
    
    // New fields for manual input
    $input_type = clean($_POST['input_type'] ?? 'manual_full');
    $street_address = clean($_POST['street_address'] ?? '');
    $nomor_rumah = clean($_POST['nomor_rumah'] ?? '');
    $nomor_bangunan = clean($_POST['nomor_bangunan'] ?? '');
    $blok = clean($_POST['blok'] ?? '');
    $lantai = clean($_POST['lantai'] ?? '');
    $nomor_unit = clean($_POST['nomor_unit'] ?? '');
    $patokan_lokasi = clean($_POST['patokan_lokasi'] ?? '');
    
    // Jika street_id dipilih, gunakan data dari street untuk alamat_detail
    if ($input_type === 'street_dropdown' && $street_id > 0 && isset($conn_alamat) && $conn_alamat->connect_error === null) {
        $sql_street = "SELECT name, type, rt, rw, postal_code FROM alamat_db.streets WHERE id = ?";
        $stmt_street = $conn_alamat->prepare($sql_street);
        if ($stmt_street) {
            $stmt_street->bind_param('i', $street_id);
            $stmt_street->execute();
            $res_street = $stmt_street->get_result();
            if ($res_street && ($row_street = $res_street->fetch_assoc())) {
                $prefix = '';
                switch($row_street['type']) {
                    case 'jalan': $prefix = 'Jl. '; break;
                    case 'gang': $prefix = 'Gg. '; break;
                    case 'lorong': $prefix = 'Lr. '; break;
                    case 'komplek': $prefix = 'Komplek '; break;
                    case 'perumahan': $prefix = 'Perum. '; break;
                    case 'jalan_raya': $prefix = 'Jl. Raya '; break;
                    case 'jalan_utama': $prefix = 'Jl. Utama '; break;
                    case 'jalan_tol': $prefix = 'Jl. Tol '; break;
                }
                
                $alamat_detail = $prefix . $row_street['name'];
                if ($row_street['rt']) $alamat_detail .= ' RT ' . $row_street['rt'];
                if ($row_street['rw']) $alamat_detail .= ' RW ' . $row_street['rw'];
                
                // Override province_id, regency_id, district_id, village_id dari street
                $sql_village = "SELECT village_id, district_id, regency_id, province_id FROM alamat_db.streets WHERE id = ?";
                $stmt_village = $conn_alamat->prepare($sql_village);
                if ($stmt_village) {
                    $stmt_village->bind_param('i', $street_id);
                    $stmt_village->execute();
                    $res_village = $stmt_village->get_result();
                    if ($res_village && ($row_village = $res_village->fetch_assoc())) {
                        $village_id = $row_village['village_id'];
                        $district_id = $row_village['district_id'];
                        $regency_id = $row_village['regency_id'];
                        $province_id = $row_village['province_id'];
                    }
                    $stmt_village->close();
                }
            }
            $stmt_street->close();
        }
    } elseif ($input_type === 'manual_full') {
        // Build alamat_detail from manual fields
        $alamat_detail = $street_address;
        if ($nomor_rumah) $alamat_detail .= ' No. ' . $nomor_rumah;
        if ($nomor_bangunan) $alamat_detail .= ' ' . $nomor_bangunan;
        if ($blok) $alamat_detail .= ' Blok ' . $blok;
        if ($lantai) $alamat_detail .= ' Lantai ' . $lantai;
        if ($nomor_unit) $alamat_detail .= ' Unit ' . $nomor_unit;
        if ($patokan_lokasi) $alamat_detail .= ' (' . $patokan_lokasi . ')';
    } elseif ($input_type === 'manual_partial') {
        // Use alamat_detail as is
        $alamat_detail = $alamat_detail;
    }
    
    $response = ['success' => false, 'message' => ''];
    
    if ($supplier_id <= 0) {
        $response['message'] = 'Supplier ID tidak valid.';
    } else {
        $conn->begin_transaction();
        $ok = true;
        
        // Cek apakah supplier sudah punya alamat
        $sql_check = "SELECT a.id FROM addresses a JOIN orang_addresses oa ON a.id = oa.address_id WHERE oa.orang_id = ? AND oa.address_type = 'supplier' AND oa.is_active = 1 LIMIT 1";
        $stmt_check = $conn->prepare($sql_check);
        $existing_address_id = null;
        
        if ($stmt_check) {
            $stmt_check->bind_param('i', $supplier_id);
            $stmt_check->execute();
            $res_check = $stmt_check->get_result();
            if ($res_check && ($row = $res_check->fetch_assoc())) {
                $existing_address_id = $row['id'];
            }
            $stmt_check->close();
        }
        
        // Get postal code
        $postal_code = '';
        if ($village_id > 0 && isset($conn_alamat) && $conn_alamat->connect_error === null) {
            $sql_postal = "SELECT postal_code FROM villages WHERE id = ?";
            $stmt_postal = $conn_alamat->prepare($sql_postal);
            if ($stmt_postal) {
                $stmt_postal->bind_param('i', $village_id);
                $stmt_postal->execute();
                $res_postal = $stmt_postal->get_result();
                if ($res_postal && ($row = $res_postal->fetch_assoc())) {
                    $postal_code = $row['postal_code'] ?? '';
                }
                $stmt_postal->close();
            }
        }
        
        if ($existing_address_id) {
            // Update existing address
            $sql_update = "UPDATE addresses SET street_id = ?, street_address = ?, nomor_rumah = ?, nomor_bangunan = ?, blok = ?, lantai = ?, nomor_unit = ?, patokan_lokasi = ?, input_type = ?, province_id = ?, regency_id = ?, district_id = ?, village_id = ?, postal_code = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            if ($stmt_update) {
                $stmt_update->bind_param('isssssssisiiissi', $street_id, $street_address, $nomor_rumah, $nomor_bangunan, $blok, $lantai, $nomor_unit, $patokan_lokasi, $input_type, $province_id, $regency_id, $district_id, $village_id, $postal_code, $existing_address_id);
                if (!$stmt_update->execute()) {
                    $ok = false;
                    $response['message'] = 'Gagal update alamat.';
                }
                $stmt_update->close();
            } else {
                $ok = false;
                $response['message'] = 'Gagal menyiapkan query update alamat.';
            }
            
            // Update id_alamat_orang di tabel orang (jika belum ada)
            if ($ok) {
                $sql_check_orang = "SELECT id_alamat_orang FROM orang WHERE id_orang = ?";
                $stmt_check_orang = $conn->prepare($sql_check_orang);
                if ($stmt_check_orang) {
                    $stmt_check_orang->bind_param('i', $supplier_id);
                    $stmt_check_orang->execute();
                    $res_check_orang = $stmt_check_orang->get_result();
                    $current_address_id = null;
                    if ($res_check_orang && ($row = $res_check_orang->fetch_assoc())) {
                        $current_address_id = $row['id_alamat_orang'];
                    }
                    $stmt_check_orang->close();
                    
                    if ($current_address_id != $existing_address_id) {
                        $sql_update_orang = "UPDATE orang SET id_alamat_orang = ? WHERE id_orang = ?";
                        $stmt_update_orang = $conn->prepare($sql_update_orang);
                        if ($stmt_update_orang) {
                            $stmt_update_orang->bind_param('ii', $existing_address_id, $supplier_id);
                            $stmt_update_orang->execute();
                            $stmt_update_orang->close();
                        }
                    }
                }
            }
        } else {
            // Insert new address
            $sql_address = "INSERT INTO addresses (street_id, street_address, nomor_rumah, nomor_bangunan, blok, lantai, nomor_unit, patokan_lokasi, input_type, province_id, regency_id, district_id, village_id, postal_code, address_type, is_primary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'supplier', 1)";
            $stmt_address = $conn->prepare($sql_address);
            if ($stmt_address) {
                $stmt_address->bind_param('isssssssisiiiss', $street_id, $street_address, $nomor_rumah, $nomor_bangunan, $blok, $lantai, $nomor_unit, $patokan_lokasi, $input_type, $province_id, $regency_id, $district_id, $village_id, $postal_code);
                if (!$stmt_address->execute()) {
                    $ok = false;
                    $response['message'] = 'Gagal menyimpan alamat baru.';
                }
                $address_id = $stmt_address->insert_id;
                $stmt_address->close();
            } else {
                $ok = false;
                $response['message'] = 'Gagal menyiapkan query alamat baru.';
            }
            
            // Hubungkan dengan supplier
            if ($ok && isset($address_id)) {
                $sql_orang_address = "INSERT INTO orang_addresses (orang_id, address_id, address_type) VALUES (?, ?, 'supplier')";
                $stmt_orang_address = $conn->prepare($sql_orang_address);
                if ($stmt_orang_address) {
                    $stmt_orang_address->bind_param('ii', $supplier_id, $address_id);
                    if (!$stmt_orang_address->execute()) {
                        $ok = false;
                        $response['message'] = 'Gagal menghubungkan alamat dengan supplier.';
                    }
                    $stmt_orang_address->close();
                } else {
                    $ok = false;
                    $response['message'] = 'Gagal menyiapkan query hubungan alamat.';
                }
                
                // Update id_alamat_orang di tabel orang
                if ($ok) {
                    $sql_update_orang = "UPDATE orang SET id_alamat_orang = ? WHERE id_orang = ?";
                    $stmt_update_orang = $conn->prepare($sql_update_orang);
                    if ($stmt_update_orang) {
                        $stmt_update_orang->bind_param('ii', $address_id, $supplier_id);
                        if (!$stmt_update_orang->execute()) {
                            // Trigger akan menghandle ini, tapi kita log error untuk debugging
                            error_log("Warning: Failed to update id_alamat_orang for supplier_id: $supplier_id");
                        }
                        $stmt_update_orang->close();
                    }
                }
            }
        }
        
        if ($ok) {
            $conn->commit();
            $response['success'] = true;
            $response['message'] = 'Alamat supplier berhasil diperbarui.';
        } else {
            $conn->rollback();
        }
    }
    
    echo json_encode($response);
    exit;
}

$error = '';
$success = '';
$suppliers = [];
$products = [];
$purchases = [];
$form_supplier_id = 0;
$form_invoice_no = '';
$form_supplier_invoice_no = '';
$form_purchase_date = date('Y-m-d');
$form_purchase_time = '';
$form_product_id = 0;
$form_qty = '';
$form_price = '';
$form_notes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if (!function_exists('generate_internal_purchase_invoice_no')) {
        function generate_internal_purchase_invoice_no(mysqli $conn, int $branchId, string $purchaseDate): string
        {
            $datePart = date('Ymd', strtotime($purchaseDate));
            $prefix = 'PB';
            $branchPart = $branchId > 0 ? str_pad((string)$branchId, 3, '0', STR_PAD_LEFT) : '000';
            $likePattern = $prefix . '-' . $branchPart . '-' . $datePart . '-%';
            $sql = "SELECT invoice_no FROM purchases WHERE branch_id = ? AND purchase_date = ? AND invoice_no LIKE ? ORDER BY invoice_no DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $nextNumber = 1;
            if ($stmt) {
                $stmt->bind_param('iss', $branchId, $purchaseDate, $likePattern);
                if ($stmt->execute()) {
                    $res = $stmt->get_result();
                    if ($res && ($row = $res->fetch_assoc())) {
                        $last = $row['invoice_no'];
                        $parts = explode('-', $last);
                        $lastSeq = (int)end($parts);
                        if ($lastSeq > 0) {
                            $nextNumber = $lastSeq + 1;
                        }
                    }
                }
                $stmt->close();
            }
            $seqPart = str_pad((string)$nextNumber, 4, '0', STR_PAD_LEFT);
            return $prefix . '-' . $branchPart . '-' . $datePart . '-' . $seqPart;
        }
    }
    if ($action === 'save') {
        $form_supplier_id = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
        $form_invoice_no = '';
        $form_supplier_invoice_no = clean($_POST['supplier_invoice_no'] ?? '');
        $purchase_date_raw = $_POST['purchase_date'] ?? date('Y-m-d');
        $purchase_time_raw = $_POST['purchase_time'] ?? '';
        $purchase_date_raw = trim((string)$purchase_date_raw);
        $purchase_time_raw = trim((string)$purchase_time_raw);
        $form_purchase_time = $purchase_time_raw;
        $purchaseDateForParse = $purchase_date_raw;
        if ($purchase_time_raw !== '') {
            $purchaseDateForParse .= ' ' . $purchase_time_raw;
        }
        $form_purchase_date = parse_date_id_to_db($purchaseDateForParse);
        $form_notes = clean($_POST['notes'] ?? '');

        $items_input = [];
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            $items_input = $_POST['items'];
        }
        if (empty($items_input)) {
            $form_product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $form_qty = $_POST['qty'] ?? '';
            $form_price = $_POST['price'] ?? '';
            if ($form_product_id > 0 && $form_qty !== '') {
                $items_input[] = [
                    'product_id' => $form_product_id,
                    'qty' => $form_qty,
                    'price' => $form_price,
                ];
            }
        }

        if (!$user_id || !$branch_id) {
            $error = 'User atau cabang belum lengkap. Silakan pastikan user terhubung ke cabang.';
        } elseif ($form_supplier_id <= 0) {
            $error = 'Pemasok wajib dipilih.';
        } elseif ($form_purchase_date === '') {
            $error = 'Tanggal pembelian wajib diisi.';
        } else {
            $supplier_name = '';
            $stmtSup = $conn->prepare("SELECT nama_lengkap FROM orang WHERE id_orang = ? AND is_supplier = 1 AND is_active = 1 LIMIT 1");
            if ($stmtSup) {
                $stmtSup->bind_param('i', $form_supplier_id);
                $stmtSup->execute();
                $resSup = $stmtSup->get_result();
                if ($rowSup = $resSup->fetch_assoc()) {
                    $supplier_name = $rowSup['nama_lengkap'];
                }
                $stmtSup->close();
            }

            if ($supplier_name === '') {
                $error = 'Pemasok tidak valid atau sudah tidak aktif.';
            } else {
                $items = [];
                $total_amount = 0.0;

                foreach ($items_input as $row) {
                    $pid = isset($row['product_id']) ? (int)$row['product_id'] : 0;
                    $qtyRaw = isset($row['qty']) ? (string)$row['qty'] : '';
                    $priceRaw = isset($row['price']) ? (string)$row['price'] : '';
                    $qty = (float)str_replace(',', '', $qtyRaw);
                    $price = (float)str_replace(',', '', $priceRaw);
                    if ($pid <= 0) {
                        continue;
                    }
                    if ($qty <= 0) {
                        $error = 'Jumlah pembelian harus lebih besar dari nol.';
                        break;
                    }
                    if ($price < 0) {
                        $error = 'Harga tidak boleh bernilai negatif.';
                        break;
                    }
                    $subtotal = $qty * $price;
                    $items[] = [
                        'product_id' => $pid,
                        'qty' => $qty,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ];
                    $total_amount += $subtotal;
                }

                if ($error === '' && empty($items)) {
                    $error = 'Minimal harus ada satu item pembelian yang valid.';
                }
            }

            if ($error === '') {
                $conn->begin_transaction();
                $ok = true;

                $generated_invoice_no = generate_internal_purchase_invoice_no($conn, $branch_id, $form_purchase_date);
                $sqlHeader = "INSERT INTO purchases (branch_id, supplier_id, supplier_name, invoice_no, supplier_invoice_no, purchase_date, total_amount, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtHeader = $conn->prepare($sqlHeader);
                if ($stmtHeader) {
                    $stmtHeader->bind_param('isssssdis', $branch_id, $form_supplier_id, $supplier_name, $generated_invoice_no, $form_supplier_invoice_no, $form_purchase_date, $total_amount, $form_notes, $user_id);
                    if (!$stmtHeader->execute()) {
                        $ok = false;
                        $error = 'Gagal menyimpan data pembelian.';
                    }
                    $purchase_id = $stmtHeader->insert_id;
                    $stmtHeader->close();
                } else {
                    $ok = false;
                    $error = 'Gagal menyiapkan query header pembelian.';
                }

                if ($ok) {
                    $sqlItem = "INSERT INTO purchase_items (purchase_id, product_id, qty, price, subtotal) VALUES (?, ?, ?, ?, ?)";
                    $stmtItem = $conn->prepare($sqlItem);
                    if ($stmtItem) {
                        foreach ($items as $it) {
                            $pid = $it['product_id'];
                            $qty = $it['qty'];
                            $price = $it['price'];
                            $subtotal = $it['subtotal'];
                            $stmtItem->bind_param('iiddd', $purchase_id, $pid, $qty, $price, $subtotal);
                            if (!$stmtItem->execute()) {
                                $ok = false;
                                $error = 'Gagal menyimpan item pembelian.';
                                break;
                            }
                        }
                        $stmtItem->close();
                    } else {
                        $ok = false;
                        $error = 'Gagal menyiapkan query item pembelian.';
                    }
                }

                if ($ok) {
                    $conn->commit();
                    $success = 'Transaksi pembelian berhasil disimpan. No. internal: ' . $generated_invoice_no;
                    $form_supplier_id = 0;
                    $form_invoice_no = '';
                    $form_supplier_invoice_no = '';
                    $form_purchase_date = date('Y-m-d');
                    $form_product_id = 0;
                    $form_qty = '';
                    $form_price = '';
                    $form_notes = '';
                    if ($user_id) {
                        log_purchase_audit($conn, $purchase_id, 'insert', null, $total_amount, $user_id);
                    }
                } else {
                    $conn->rollback();
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
    } elseif ($action === 'update_purchase') {
        $purchase_id = isset($_POST['purchase_id']) ? (int)$_POST['purchase_id'] : 0;
        $form_supplier_id = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
        $form_invoice_no = '';
        $form_supplier_invoice_no = clean($_POST['supplier_invoice_no'] ?? '');
        $purchase_date_raw = $_POST['purchase_date'] ?? date('Y-m-d');
        $purchase_time_raw = $_POST['purchase_time'] ?? '';
        $purchase_date_raw = trim((string)$purchase_date_raw);
        $purchase_time_raw = trim((string)$purchase_time_raw);
        $form_purchase_time = $purchase_time_raw;
        $purchaseDateForParse = $purchase_date_raw;
        if ($purchase_time_raw !== '') {
            $purchaseDateForParse .= ' ' . $purchase_time_raw;
        }
        $form_purchase_date = parse_date_id_to_db($purchaseDateForParse);
        $form_notes = clean($_POST['notes'] ?? '');

        $items_input = [];
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            $items_input = $_POST['items'];
        }

        if (!$user_id || !$branch_id) {
            $error = 'User atau cabang belum lengkap. Silakan pastikan user terhubung ke cabang.';
        } elseif ($purchase_id <= 0) {
            $error = 'Pembelian tidak ditemukan.';
        } elseif ($form_supplier_id <= 0) {
            $error = 'Pemasok wajib dipilih.';
        } elseif ($form_purchase_date === '') {
            $error = 'Tanggal pembelian wajib diisi.';
        } else {
            $selected_purchase = null;
            if ($branch_id) {
                    $stmtPurchase = $conn->prepare("SELECT id, branch_id, total_amount, supplier_invoice_no FROM purchases WHERE id = ? AND branch_id = ?");
                if ($stmtPurchase) {
                    $stmtPurchase->bind_param('ii', $purchase_id, $branch_id);
                    $stmtPurchase->execute();
                    $resPurchase = $stmtPurchase->get_result();
                    if ($resPurchase && ($rowPurchase = $resPurchase->fetch_assoc())) {
                        $selected_purchase = $rowPurchase;
                    }
                    $stmtPurchase->close();
                }
            } else {
                $stmtPurchase = $conn->prepare("SELECT id, branch_id, total_amount, supplier_invoice_no FROM purchases WHERE id = ?");
                if ($stmtPurchase) {
                    $stmtPurchase->bind_param('i', $purchase_id);
                    $stmtPurchase->execute();
                    $resPurchase = $stmtPurchase->get_result();
                    if ($resPurchase && ($rowPurchase = $resPurchase->fetch_assoc())) {
                        $selected_purchase = $rowPurchase;
                    }
                    $stmtPurchase->close();
                }
            }
            if ($selected_purchase === null) {
                $error = 'Pembelian tidak ditemukan atau tidak dapat diakses.';
            } else {
                $supplier_name = '';
                $stmtSup = $conn->prepare("SELECT nama_lengkap FROM orang WHERE id_orang = ? AND is_supplier = 1 AND is_active = 1 LIMIT 1");
                if ($stmtSup) {
                    $stmtSup->bind_param('i', $form_supplier_id);
                    $stmtSup->execute();
                    $resSup = $stmtSup->get_result();
                    if ($rowSup = $resSup->fetch_assoc()) {
                        $supplier_name = $rowSup['nama_lengkap'];
                    }
                    $stmtSup->close();
                }
                if ($supplier_name === '') {
                    $error = 'Pemasok tidak valid atau sudah tidak aktif.';
                } else {
                    $items = [];
                    $total_amount = 0.0;
                    foreach ($items_input as $row) {
                        $pid = isset($row['product_id']) ? (int)$row['product_id'] : 0;
                        $qtyRaw = isset($row['qty']) ? (string)$row['qty'] : '';
                        $priceRaw = isset($row['price']) ? (string)$row['price'] : '';
                        $qty = (float)str_replace(',', '', $qtyRaw);
                        $price = (float)str_replace(',', '', $priceRaw);
                        if ($pid <= 0) {
                            continue;
                        }
                        if ($qty <= 0) {
                            $error = 'Jumlah pembelian harus lebih besar dari nol.';
                            break;
                        }
                        if ($price < 0) {
                            $error = 'Harga tidak boleh bernilai negatif.';
                            break;
                        }
                        $subtotal = $qty * $price;
                        $items[] = [
                            'product_id' => $pid,
                            'qty' => $qty,
                            'price' => $price,
                            'subtotal' => $subtotal,
                        ];
                        $total_amount += $subtotal;
                    }
                    if ($error === '' && empty($items)) {
                        $error = 'Minimal harus ada satu item pembelian yang valid.';
                    }
                }
                if ($error === '') {
                    $total_before = isset($selected_purchase['total_amount']) ? (float)$selected_purchase['total_amount'] : 0.0;
                    $conn->begin_transaction();
                    $okUpdate = true;
                    $stmtUpdate = $conn->prepare("UPDATE purchases SET supplier_id = ?, supplier_name = ?, supplier_invoice_no = ?, purchase_date = ?, total_amount = ?, notes = ? WHERE id = ?");
                    if ($stmtUpdate) {
                        $stmtUpdate->bind_param('isssdsi', $form_supplier_id, $supplier_name, $form_supplier_invoice_no, $form_purchase_date, $total_amount, $form_notes, $purchase_id);
                        if (!$stmtUpdate->execute()) {
                            $okUpdate = false;
                            $error = 'Gagal menyimpan perubahan pembelian.';
                        }
                        $stmtUpdate->close();
                    } else {
                        $okUpdate = false;
                        $error = 'Gagal menyiapkan query update pembelian.';
                    }
                    if ($okUpdate) {
                        $stmtDelItems = $conn->prepare("DELETE FROM purchase_items WHERE purchase_id = ?");
                        if ($stmtDelItems) {
                            $stmtDelItems->bind_param('i', $purchase_id);
                            if (!$stmtDelItems->execute()) {
                                $okUpdate = false;
                                $error = 'Gagal menghapus item pembelian lama.';
                            }
                            $stmtDelItems->close();
                        } else {
                            $okUpdate = false;
                            $error = 'Gagal menyiapkan query hapus item pembelian lama.';
                        }
                    }
                    if ($okUpdate) {
                        $sqlItem = "INSERT INTO purchase_items (purchase_id, product_id, qty, price, subtotal) VALUES (?, ?, ?, ?, ?)";
                        $stmtItem = $conn->prepare($sqlItem);
                        if ($stmtItem) {
                            foreach ($items as $it) {
                                $pid = $it['product_id'];
                                $qty = $it['qty'];
                                $price = $it['price'];
                                $subtotal = $it['subtotal'];
                                $stmtItem->bind_param('iiddd', $purchase_id, $pid, $qty, $price, $subtotal);
                                if (!$stmtItem->execute()) {
                                    $okUpdate = false;
                                    $error = 'Gagal menyimpan item pembelian.';
                                    break;
                                }
                            }
                            $stmtItem->close();
                        } else {
                            $okUpdate = false;
                            $error = 'Gagal menyiapkan query item pembelian.';
                        }
                    }
                    if ($okUpdate) {
                        $conn->commit();
                        $success = 'Perubahan transaksi pembelian berhasil disimpan.';
                        if ($user_id) {
                            log_purchase_audit($conn, $purchase_id, 'update', $total_before, $total_amount, $user_id);
                        }
                    } else {
                        $conn->rollback();
                    }
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
    } elseif ($action === 'delete_purchase') {
        $purchase_id = isset($_POST['purchase_id']) ? (int)$_POST['purchase_id'] : 0;
        if (!$user_id) {
            $error = 'User tidak valid.';
        } elseif ($purchase_id <= 0) {
            $error = 'Pembelian tidak ditemukan.';
        } else {
            $selected_purchase = null;
            if ($branch_id) {
                $stmtPurchase = $conn->prepare("SELECT id, branch_id, total_amount FROM purchases WHERE id = ? AND branch_id = ?");
                if ($stmtPurchase) {
                    $stmtPurchase->bind_param('ii', $purchase_id, $branch_id);
                    $stmtPurchase->execute();
                    $resPurchase = $stmtPurchase->get_result();
                    if ($resPurchase && ($rowPurchase = $resPurchase->fetch_assoc())) {
                        $selected_purchase = $rowPurchase;
                    }
                    $stmtPurchase->close();
                }
            } else {
                $stmtPurchase = $conn->prepare("SELECT id, branch_id, total_amount FROM purchases WHERE id = ?");
                if ($stmtPurchase) {
                    $stmtPurchase->bind_param('i', $purchase_id);
                    $stmtPurchase->execute();
                    $resPurchase = $stmtPurchase->get_result();
                    if ($resPurchase && ($rowPurchase = $resPurchase->fetch_assoc())) {
                        $selected_purchase = $rowPurchase;
                    }
                    $stmtPurchase->close();
                }
            }
            if ($selected_purchase === null) {
                $error = 'Pembelian tidak ditemukan atau tidak dapat diakses.';
            } else {
                $total_before = isset($selected_purchase['total_amount']) ? (float)$selected_purchase['total_amount'] : 0.0;
                $conn->begin_transaction();
                $okDelete = true;
                $stmtDelItems = $conn->prepare("DELETE FROM purchase_items WHERE purchase_id = ?");
                if ($stmtDelItems) {
                    $stmtDelItems->bind_param('i', $purchase_id);
                    if (!$stmtDelItems->execute()) {
                        $okDelete = false;
                        $error = 'Gagal menghapus item pembelian.';
                    }
                    $stmtDelItems->close();
                } else {
                    $okDelete = false;
                    $error = 'Gagal menyiapkan query hapus item pembelian.';
                }
                if ($okDelete) {
                    $stmtDelHeader = $conn->prepare("DELETE FROM purchases WHERE id = ?");
                    if ($stmtDelHeader) {
                        $stmtDelHeader->bind_param('i', $purchase_id);
                        if (!$stmtDelHeader->execute()) {
                            $okDelete = false;
                            $error = 'Gagal menghapus data pembelian.';
                        }
                        $stmtDelHeader->close();
                    } else {
                        $okDelete = false;
                        $error = 'Gagal menyiapkan query hapus pembelian.';
                    }
                }
                if ($okDelete) {
                    $conn->commit();
                    $success = 'Pembelian berhasil dihapus.';
                    if ($user_id) {
                        log_purchase_audit($conn, $purchase_id, 'delete', $total_before, 0.0, $user_id);
                    }
                } else {
                    $conn->rollback();
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
    } elseif ($action === 'quick_add_supplier') {
        $nama = clean($_POST['nama'] ?? '');
        $kontak = clean($_POST['kontak'] ?? '');
        $alamat_detail = clean($_POST['alamat_detail'] ?? '');
        $street_id = isset($_POST['street_id']) ? (int)$_POST['street_id'] : 0;
        $province_id = isset($_POST['province_id']) ? (int)$_POST['province_id'] : 0;
        $regency_id = isset($_POST['regency_id']) ? (int)$_POST['regency_id'] : 0;
        $district_id = isset($_POST['district_id']) ? (int)$_POST['district_id'] : 0;
        $village_id = isset($_POST['village_id']) ? (int)$_POST['village_id'] : 0;
        
        // New fields for manual input
        $input_type = clean($_POST['input_type'] ?? 'manual_full');
        $street_address = clean($_POST['street_address'] ?? '');
        $nomor_rumah = clean($_POST['nomor_rumah'] ?? '');
        $nomor_bangunan = clean($_POST['nomor_bangunan'] ?? '');
        $blok = clean($_POST['blok'] ?? '');
        $lantai = clean($_POST['lantai'] ?? '');
        $nomor_unit = clean($_POST['nomor_unit'] ?? '');
        $patokan_lokasi = clean($_POST['patokan_lokasi'] ?? '');
        
        // Jika street_id dipilih, gunakan data dari street untuk alamat_detail
        if ($input_type === 'street_dropdown' && $street_id > 0 && isset($conn_alamat) && $conn_alamat->connect_error === null) {
            $sql_street = "SELECT name, type, rt, rw, postal_code FROM alamat_db.streets WHERE id = ?";
            $stmt_street = $conn_alamat->prepare($sql_street);
            if ($stmt_street) {
                $stmt_street->bind_param('i', $street_id);
                $stmt_street->execute();
                $res_street = $stmt_street->get_result();
                if ($res_street && ($row_street = $res_street->fetch_assoc())) {
                    $prefix = '';
                    switch($row_street['type']) {
                        case 'jalan': $prefix = 'Jl. '; break;
                        case 'gang': $prefix = 'Gg. '; break;
                        case 'lorong': $prefix = 'Lr. '; break;
                        case 'komplek': $prefix = 'Komplek '; break;
                        case 'perumahan': $prefix = 'Perum. '; break;
                        case 'jalan_raya': $prefix = 'Jl. Raya '; break;
                        case 'jalan_utama': $prefix = 'Jl. Utama '; break;
                        case 'jalan_tol': $prefix = 'Jl. Tol '; break;
                    }
                    
                    $alamat_detail = $prefix . $row_street['name'];
                    if ($row_street['rt']) $alamat_detail .= ' RT ' . $row_street['rt'];
                    if ($row_street['rw']) $alamat_detail .= ' RW ' . $row_street['rw'];
                    
                    // Override province_id, regency_id, district_id, village_id dari street
                    $sql_village = "SELECT village_id, district_id, regency_id, province_id FROM alamat_db.streets WHERE id = ?";
                    $stmt_village = $conn_alamat->prepare($sql_village);
                    if ($stmt_village) {
                        $stmt_village->bind_param('i', $street_id);
                        $stmt_village->execute();
                        $res_village = $stmt_village->get_result();
                        if ($res_village && ($row_village = $res_village->fetch_assoc())) {
                            $village_id = $row_village['village_id'];
                            $district_id = $row_village['district_id'];
                            $regency_id = $row_village['regency_id'];
                            $province_id = $row_village['province_id'];
                        }
                        $stmt_village->close();
                    }
                }
                $stmt_street->close();
            }
        } elseif ($input_type === 'manual_full') {
            // Build alamat_detail from manual fields
            $alamat_detail = $street_address;
            if ($nomor_rumah) $alamat_detail .= ' No. ' . $nomor_rumah;
            if ($nomor_bangunan) $alamat_detail .= ' ' . $nomor_bangunan;
            if ($blok) $alamat_detail .= ' Blok ' . $blok;
            if ($lantai) $alamat_detail .= ' Lantai ' . $lantai;
            if ($nomor_unit) $alamat_detail .= ' Unit ' . $nomor_unit;
            if ($patokan_lokasi) $alamat_detail .= ' (' . $patokan_lokasi . ')';
        } elseif ($input_type === 'manual_partial') {
            // Use alamat_detail as is
            $alamat_detail = $alamat_detail;
        }
        
        if ($nama === '') {
            $error = 'Nama pemasok wajib diisi.';
        } else {
            $conn->begin_transaction();
            $ok = true;
            
            // Insert data orang terlebih dahulu
            $sql_orang = "INSERT INTO orang (nama_lengkap, kontak, is_supplier, is_customer, is_active) VALUES (?, ?, 1, 0, 1)";
            $stmt_orang = $conn->prepare($sql_orang);
            if ($stmt_orang) {
                $stmt_orang->bind_param('ss', $nama, $kontak);
                if (!$stmt_orang->execute()) {
                    $ok = false;
                    $error = 'Gagal menambahkan data pemasok.';
                }
                $orang_id = $stmt_orang->insert_id;
                $stmt_orang->close();
            } else {
                $ok = false;
                $error = 'Gagal menyiapkan query data pemasok.';
            }
            
            // Jika ada data alamat, simpan ke tabel addresses
            if ($ok && ($province_id > 0 || $regency_id > 0 || $district_id > 0 || $village_id > 0 || $alamat_detail !== '')) {
                $postal_code = '';
                
                // Get postal code dari village jika ada
                if ($village_id > 0 && isset($conn_alamat) && $conn_alamat->connect_error === null) {
                    $sql_postal = "SELECT postal_code FROM villages WHERE id = ?";
                    $stmt_postal = $conn_alamat->prepare($sql_postal);
                    if ($stmt_postal) {
                        $stmt_postal->bind_param('i', $village_id);
                        $stmt_postal->execute();
                        $res_postal = $stmt_postal->get_result();
                        if ($res_postal && ($row = $res_postal->fetch_assoc())) {
                            $postal_code = $row['postal_code'] ?? '';
                        }
                        $stmt_postal->close();
                    }
                }
                
                $sql_address = "INSERT INTO addresses (street_id, street_address, nomor_rumah, nomor_bangunan, blok, lantai, nomor_unit, patokan_lokasi, input_type, province_id, regency_id, district_id, village_id, postal_code, address_type, is_primary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'supplier', 1)";
                $stmt_address = $conn->prepare($sql_address);
                if ($stmt_address) {
                    $stmt_address->bind_param('isssssssisiiiss', $street_id, $street_address, $nomor_rumah, $nomor_bangunan, $blok, $lantai, $nomor_unit, $patokan_lokasi, $input_type, $province_id, $regency_id, $district_id, $village_id, $postal_code);
                    if (!$stmt_address->execute()) {
                        $ok = false;
                        $error = 'Gagal menyimpan data alamat.';
                    }
                    $address_id = $stmt_address->insert_id;
                    $stmt_address->close();
                } else {
                    $ok = false;
                    $error = 'Gagal menyiapkan query alamat.';
                }
                
                // Hubungkan orang dengan alamat
                if ($ok && isset($address_id)) {
                    $sql_orang_address = "INSERT INTO orang_addresses (orang_id, address_id, address_type) VALUES (?, ?, 'supplier')";
                    $stmt_orang_address = $conn->prepare($sql_orang_address);
                    if ($stmt_orang_address) {
                        $stmt_orang_address->bind_param('ii', $orang_id, $address_id);
                        if (!$stmt_orang_address->execute()) {
                            $ok = false;
                            $error = 'Gagal menghubungkan alamat dengan pemasok.';
                        }
                        $stmt_orang_address->close();
                    } else {
                        $ok = false;
                        $error = 'Gagal menyiapkan query hubungan alamat.';
                    }
                    
                    // Update id_alamat_orang di tabel orang
                    if ($ok) {
                        $sql_update_orang = "UPDATE orang SET id_alamat_orang = ? WHERE id_orang = ?";
                        $stmt_update_orang = $conn->prepare($sql_update_orang);
                        if ($stmt_update_orang) {
                            $stmt_update_orang->bind_param('ii', $address_id, $orang_id);
                            if (!$stmt_update_orang->execute()) {
                                // Trigger akan menghandle ini, tapi kita log error untuk debugging
                                error_log("Warning: Failed to update id_alamat_orang for orang_id: $orang_id");
                            }
                            $stmt_update_orang->close();
                        }
                    }
                }
            }
            
            if ($ok) {
                $conn->commit();
                $success = 'Pemasok baru berhasil ditambahkan.';
                $form_supplier_id = (int)$orang_id;
            } else {
                $conn->rollback();
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
            if ($error === '' && isset($form_supplier_id) && $form_supplier_id > 0) {
                $response['supplier'] = [
                    'id' => (int)$form_supplier_id,
                    'name' => $nama,
                ];
            }
            echo json_encode($response);
            exit;
        }
    }
}

$sqlSuppliers = "SELECT id_orang, nama_lengkap FROM orang WHERE is_supplier = 1 AND is_active = 1 ORDER BY nama_lengkap";
$resSuppliers = $conn->query($sqlSuppliers);
if ($resSuppliers) {
    while ($row = $resSuppliers->fetch_assoc()) {
        $suppliers[] = $row;
    }
}

// Ambil data provinsi untuk dropdown alamat
$provinces = [];
if (isset($conn_alamat) && $conn_alamat->connect_error === null) {
    $sqlProvinces = "SELECT id, name FROM provinces ORDER BY name";
    $resProvinces = $conn_alamat->query($sqlProvinces);
    if ($resProvinces) {
        while ($row = $resProvinces->fetch_assoc()) {
            $provinces[] = $row;
        }
    }
}

if ($branch_id) {
    $sqlProducts = "SELECT p.id, p.name, p.unit, p.barcode, COALESCE(bpp.buy_price, p.buy_price) AS buy_price,
(SELECT pc.plu_number FROM product_plu_mapping m JOIN plu_codes pc ON pc.id = m.plu_code_id WHERE m.product_id = p.id AND m.is_primary_plu = 1 LIMIT 1) AS plu_number
FROM products p
LEFT JOIN branch_product_prices bpp
  ON bpp.product_id = p.id
 AND bpp.branch_id = ?
 AND bpp.is_active = 1
WHERE p.is_active = 1
ORDER BY p.name";
    $stmtProducts = $conn->prepare($sqlProducts);
    if ($stmtProducts) {
        $stmtProducts->bind_param('i', $branch_id);
        $stmtProducts->execute();
        $resProducts = $stmtProducts->get_result();
        if ($resProducts) {
            while ($row = $resProducts->fetch_assoc()) {
                $products[] = $row;
            }
        }
        $stmtProducts->close();
    }
} else {
    $sqlProducts = "SELECT id, name, unit, barcode, buy_price,
(SELECT pc.plu_number FROM product_plu_mapping m JOIN plu_codes pc ON pc.id = m.plu_code_id WHERE m.product_id = products.id AND m.is_primary_plu = 1 LIMIT 1) AS plu_number
FROM products
WHERE is_active = 1
ORDER BY name";
    $resProducts = $conn->query($sqlProducts);
    if ($resProducts) {
        while ($row = $resProducts->fetch_assoc()) {
            $products[] = $row;
        }
    }
}

$sqlPurchases = "SELECT id, purchase_date, invoice_no, supplier_invoice_no, supplier_name, total_amount, created_at FROM purchases ORDER BY purchase_date DESC, id DESC LIMIT 50";
$resPurchases = $conn->query($sqlPurchases);
if ($resPurchases) {
    while ($row = $resPurchases->fetch_assoc()) {
        $purchases[] = $row;
    }
}

$page_title = 'Pembelian';
$content_view = 'purchases_view.php';

include __DIR__ . DIRECTORY_SEPARATOR . 'template.php';
