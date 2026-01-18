<?php
require_once __DIR__ . '/auth.php';
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
        $alamat = clean($_POST['alamat'] ?? '');
        if ($nama === '') {
            $error = 'Nama pemasok wajib diisi.';
        } else {
            $sql = "INSERT INTO orang (nama_lengkap, alamat, kontak, is_supplier, is_customer, is_active) VALUES (?, ?, ?, 1, 0, 1)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('sss', $nama, $alamat, $kontak);
                if ($stmt->execute()) {
                    $success = 'Pemasok baru berhasil ditambahkan.';
                    $form_supplier_id = (int)$stmt->insert_id;
                } else {
                    $error = 'Gagal menambahkan pemasok baru.';
                }
                $stmt->close();
            } else {
                $error = 'Gagal menyiapkan query tambah pemasok.';
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

include __DIR__ . '/template.php';
