<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
require_login();

function sppg_to_title_case($text)
{
    $text = trim((string)$text);
    if ($text === '') {
        return '';
    }
    if (function_exists('mb_convert_case') && function_exists('mb_strtolower')) {
        return mb_convert_case(mb_strtolower($text, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }
    return ucwords(strtolower($text));
}

function sppg_parse_pesanan_text($text)
{
    $lines = preg_split("/\\r\\n|\\n|\\r/", $text);
    $rows = [];
    foreach ($lines as $line) {
        $trim = trim($line);
        if ($trim === '') {
            continue;
        }
        $noSpace = str_replace(' ', '', $trim);
        if ($noSpace !== '' && preg_match('/^[=\\-]+$/', $noSpace)) {
            continue;
        }
        $partsTab = preg_split("/\\t+/", $line);
        if (count($partsTab) >= 3) {
            $partsTab = array_map('trim', $partsTab);
            $first = $partsTab[0] ?? '';
            if (preg_match('/^\\d+$/', $first)) {
                $no = (int)$first;
                $uraian = $partsTab[1] ?? '';
                $uraian = sppg_to_title_case($uraian);
                $qtyRaw = $partsTab[2] ?? '';
                $satuan = $partsTab[3] ?? '';
                $extra = [];
                if (count($partsTab) > 4) {
                    $extra = array_slice($partsTab, 4);
                }
                $catatan = trim(implode(' ', $extra));
                $qty = 0.0;
                $qtyClean = preg_replace('/[^0-9,\\.]/', '', $qtyRaw);
                if ($qtyClean !== '') {
                    $qty = (float)str_replace(',', '.', $qtyClean);
                }
                if ($qty <= 0) {
                    continue;
                }
                $rows[] = [
                    'no' => $no,
                    'uraian' => $uraian,
                    'qty' => $qty,
                    'satuan' => $satuan,
                    'catatan' => $catatan,
                ];
                continue;
            }
        }
        if (preg_match('/^(?<uraian>.+?)\\s+(?<qty>[\\d.,]+)\\s+(?<satuan>\\S+)(?:\\s+(?<catatan>.+))?$/u', $trim, $m)) {
            $uraian = $m['uraian'];
            $uraian = sppg_to_title_case($uraian);
            $qtyRaw = $m['qty'];
            $satuan = $m['satuan'];
            $catatan = isset($m['catatan']) ? $m['catatan'] : '';
            $qtyClean = preg_replace('/[^0-9,\\.]/', '', $qtyRaw);
            $qty = 0.0;
            if ($qtyClean !== '') {
                $qty = (float)str_replace(',', '.', $qtyClean);
            }
            if ($qty <= 0) {
                continue;
            }
            $rows[] = [
                'no' => null,
                'uraian' => $uraian,
                'qty' => $qty,
                'satuan' => $satuan,
                'catatan' => $catatan,
            ];
        }
    }
    return $rows;
}

function normalize_pesanan_rows_from_post($rows)
{
    $result = [];
    if (!is_array($rows)) {
        return $result;
    }
    foreach ($rows as $row) {
        $noRaw = $row['no'] ?? '';
        $no = null;
        if ($noRaw !== '' && $noRaw !== null) {
            $no = (int)$noRaw;
        }
        $uraian = trim($row['uraian'] ?? '');
        $qtyRaw = trim((string)($row['qty'] ?? ''));
        $satuan = trim($row['satuan'] ?? '');
        $catatan = trim($row['catatan'] ?? '');
        if ($uraian === '' && $qtyRaw === '' && $satuan === '' && $catatan === '') {
            continue;
        }
        $qtyClean = preg_replace('/[^0-9,\\.]/', '', $qtyRaw);
        $qty = 0.0;
        if ($qtyClean !== '') {
            $qty = (float)str_replace(',', '.', $qtyClean);
        }
        $result[] = [
            'no' => $no,
            'uraian' => $uraian,
            'qty' => $qty,
            'satuan' => $satuan,
            'catatan' => $catatan,
        ];
    }
    return $result;
}

function sppg_merge_duplicate_rows($rows)
{
    $merged = [];
    $indexByKey = [];
    if (!is_array($rows)) {
        return $merged;
    }
    foreach ($rows as $row) {
        $uraian = isset($row['uraian']) ? trim($row['uraian']) : '';
        $satuan = isset($row['satuan']) ? trim($row['satuan']) : '';
        if ($uraian === '') {
            $merged[] = $row;
            continue;
        }
        $key = strtolower($uraian) . '|' . strtolower($satuan);
        if (isset($indexByKey[$key])) {
            $idx = $indexByKey[$key];
            $existingQty = isset($merged[$idx]['qty']) ? (float)$merged[$idx]['qty'] : 0.0;
            $newQty = isset($row['qty']) ? (float)$row['qty'] : 0.0;
            $merged[$idx]['qty'] = $existingQty + $newQty;
            if ((!isset($merged[$idx]['no']) || $merged[$idx]['no'] === null) && isset($row['no']) && $row['no'] !== null) {
                $merged[$idx]['no'] = (int)$row['no'];
            }
            continue;
        }
        $merged[] = $row;
        $indexByKey[$key] = count($merged) - 1;
    }
    return $merged;
}

$error = '';
$success = '';
$orders = [];
$preview_raw_pesanan = '';
$preview_rows = [];
$form_customer_name = '';
$form_customer_id = 0;
$form_customer_type = 'pelanggan';
$form_order_date = date('Y-m-d');
$form_required_date = '';
$fulfillment_order = null;
$fulfillment_items = [];
$fulfillment_products = [];
$fulfillment_stock = [];
$fulfillment_summary = [];
$customers = [];

$user = current_user();
$user_id = $user['id'] ?? null;
$branch_id = $user['branch_id'] ?? null;

$selected_order_id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $selected_order_id = (int)$_POST['order_id'];
} elseif (isset($_GET['order_id'])) {
    $selected_order_id = (int)$_GET['order_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'preview_pesanan') {
        $form_customer_type = $_POST['customer_type'] ?? $form_customer_type;
        $form_customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
        $form_customer_name = trim($_POST['customer_name'] ?? '');
        $form_order_date = parse_date_id_to_db($_POST['order_date'] ?? date('Y-m-d'));
        $form_required_date = parse_date_id_to_db($_POST['required_date'] ?? '');
        $preview_raw_pesanan = $_POST['raw_pesanan'] ?? '';
        $preview_rows = sppg_parse_pesanan_text($preview_raw_pesanan);
        $preview_rows = sppg_merge_duplicate_rows($preview_rows);
        if (empty($preview_rows)) {
            $preview_rows = [
                [
                    'no' => null,
                    'uraian' => '',
                    'qty' => '',
                    'satuan' => '',
                    'catatan' => '',
                ],
            ];
        }
    } elseif ($action === 'edit_pesanan') {
        $form_customer_type = $_POST['customer_type'] ?? $form_customer_type;
        $form_customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
        $form_customer_name = trim($_POST['customer_name'] ?? '');
        $form_order_date = parse_date_id_to_db($_POST['order_date'] ?? date('Y-m-d'));
        $form_required_date = parse_date_id_to_db($_POST['required_date'] ?? '');
        $preview_raw_pesanan = $_POST['raw_pesanan'] ?? '';
        $preview_rows = normalize_pesanan_rows_from_post($_POST['rows'] ?? []);
    } elseif ($action === 'append_from_excel') {
        $form_customer_type = $_POST['customer_type'] ?? $form_customer_type;
        $form_customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
        $form_customer_name = trim($_POST['customer_name'] ?? '');
        $form_order_date = parse_date_id_to_db($_POST['order_date'] ?? date('Y-m-d'));
        $form_required_date = parse_date_id_to_db($_POST['required_date'] ?? '');
        $existing_rows = normalize_pesanan_rows_from_post($_POST['rows'] ?? []);
        $existing_raw = $_POST['raw_pesanan'] ?? '';
        $append_raw = $_POST['append_raw_pesanan'] ?? '';
        $preview_raw_pesanan = $existing_raw;
        if ($append_raw !== '') {
            if ($preview_raw_pesanan !== '') {
                $preview_raw_pesanan .= "\n" . $append_raw;
            } else {
                $preview_raw_pesanan = $append_raw;
            }
        }
        $append_rows = sppg_parse_pesanan_text($append_raw);
        $preview_rows = array_values(array_merge($existing_rows, $append_rows));
        $preview_rows = sppg_merge_duplicate_rows($preview_rows);
        if (empty($preview_rows)) {
            $preview_rows = [
                [
                    'no' => null,
                    'uraian' => '',
                    'qty' => '',
                    'satuan' => '',
                    'catatan' => '',
                ],
            ];
        }
    } elseif ($action === 'save_pesanan') {
        $form_customer_type = $_POST['customer_type'] ?? $form_customer_type;
        $form_customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
        $form_customer_name = trim($_POST['customer_name'] ?? '');
        $form_order_date = parse_date_id_to_db($_POST['order_date'] ?? date('Y-m-d'));
        $form_required_date = parse_date_id_to_db($_POST['required_date'] ?? '');
        $preview_raw_pesanan = $_POST['raw_pesanan'] ?? '';
        $preview_rows = normalize_pesanan_rows_from_post($_POST['rows'] ?? []);

        $is_general_customer = ($form_customer_type === 'umum');

        if ($is_general_customer) {
            $form_customer_name = 'Pembeli Umum';
            $form_customer_id = 0;
        } elseif ($form_customer_id > 0) {
            $customer_name_db = '';
            $stmtCust = $conn->prepare("SELECT nama_lengkap FROM orang WHERE id_orang = ? AND is_customer = 1 AND is_active = 1 LIMIT 1");
            if ($stmtCust) {
                $stmtCust->bind_param('i', $form_customer_id);
                $stmtCust->execute();
                $resCust = $stmtCust->get_result();
                if ($rowCust = $resCust->fetch_assoc()) {
                    $customer_name_db = $rowCust['nama_lengkap'];
                }
                $stmtCust->close();
            }
            $form_customer_name = trim($customer_name_db);
        }

        if (!$user_id || !$branch_id) {
            $error = 'User atau cabang belum lengkap. Silakan pastikan user terhubung ke cabang.';
        } elseif ($form_customer_name === '') {
            $error = 'Nama pembeli wajib diisi.';
        } elseif ($form_order_date === '') {
            $error = 'Tanggal pesanan wajib diisi.';
        } elseif (empty($preview_rows)) {
            $error = 'Tidak ada baris pesanan yang dapat disimpan.';
        } else {
            $conn->begin_transaction();
            $ok = true;

            $sqlHeader = "INSERT INTO orders (branch_id, customer_name, order_date, required_date, raw_text, status, created_by) VALUES (?, ?, ?, ?, ?, 'draft', ?)";
            $stmtHeader = $conn->prepare($sqlHeader);
            if ($stmtHeader) {
                $stmtHeader->bind_param('issssi', $branch_id, $form_customer_name, $form_order_date, $form_required_date, $preview_raw_pesanan, $user_id);
                if (!$stmtHeader->execute()) {
                    $ok = false;
                    $error = 'Gagal menyimpan header pesanan.';
                }
                $order_id = $stmtHeader->insert_id;
                $stmtHeader->close();
            } else {
                $ok = false;
                $error = 'Gagal menyiapkan query header pesanan.';
            }

            if ($ok) {
                $sqlItem = "INSERT INTO order_items (order_id, seq_no, description, qty, unit, notes) VALUES (?, ?, ?, ?, ?, ?)";
                $stmtItem = $conn->prepare($sqlItem);
                if ($stmtItem) {
                    foreach ($preview_rows as $row) {
                        $seqNo = $row['no'] !== null ? (int)$row['no'] : 0;
                        $desc = $row['uraian'];
                        $qty = (float)$row['qty'];
                        $unit = $row['satuan'];
                        $notes = $row['catatan'];
                        $stmtItem->bind_param('iisdss', $order_id, $seqNo, $desc, $qty, $unit, $notes);
                        if (!$stmtItem->execute()) {
                            $ok = false;
                            $error = 'Gagal menyimpan item pesanan.';
                            break;
                        }
                    }
                    $stmtItem->close();
                } else {
                    $ok = false;
                    $error = 'Gagal menyiapkan query item pesanan.';
                }
            }

            if ($ok) {
                $conn->commit();
                $success = 'Pesanan berhasil disimpan ke database.';
                $preview_rows = [];
                $preview_raw_pesanan = '';
                $form_customer_name = '';
                $form_customer_id = 0;
                $form_customer_type = 'pelanggan';
                $form_order_date = date('Y-m-d');
                $form_required_date = '';
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
            echo json_encode($response);
            exit;
        }
    } elseif ($action === 'quick_add_customer') {
        $nama = clean($_POST['nama'] ?? '');
        $kontak = clean($_POST['kontak'] ?? '');
        $alamat = clean($_POST['alamat'] ?? '');
        if ($nama === '') {
            $error = 'Nama pembeli wajib diisi.';
        } else {
            $sql = "INSERT INTO orang (nama_lengkap, alamat, kontak, is_supplier, is_customer, is_active) VALUES (?, ?, ?, 0, 1, 1)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('sss', $nama, $alamat, $kontak);
                if ($stmt->execute()) {
                    $success = 'Pembeli baru berhasil ditambahkan.';
                    $form_customer_id = (int)$stmt->insert_id;
                    $form_customer_name = $nama;
                } else {
                    $error = 'Gagal menambahkan pembeli baru.';
                }
                $stmt->close();
            } else {
                $error = 'Gagal menyiapkan query tambah pembeli.';
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
            if ($error === '' && isset($form_customer_id) && $form_customer_id > 0) {
                $response['customer'] = [
                    'id' => (int)$form_customer_id,
                    'name' => $form_customer_name !== '' ? $form_customer_name : $nama,
                ];
            }
            echo json_encode($response);
            exit;
        }
    }
    elseif ($action === 'update_fulfillment') {
        $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        $selected_order_id = $order_id;
        $newStatus = $_POST['order_status'] ?? '';
        $validStatuses = ['draft', 'diproses', 'selesai', 'parsial'];

        if ($order_id > 0 && in_array($newStatus, $validStatuses, true)) {
            $stmtStatus = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            if ($stmtStatus) {
                $stmtStatus->bind_param('si', $newStatus, $order_id);
                if (!$stmtStatus->execute()) {
                    $error = 'Gagal memperbarui status pesanan.';
                }
                $stmtStatus->close();
            }
        }

        $items = $_POST['items'] ?? [];
        if ($order_id > 0 && is_array($items)) {
            $stmtItem = $conn->prepare("UPDATE order_items SET product_id = ? WHERE id = ? AND order_id = ?");
            if ($stmtItem) {
                foreach ($items as $itemId => $itemData) {
                    $pidRaw = $itemData['product_id'] ?? '';
                    if ($pidRaw === '') {
                        $productId = null;
                    } else {
                        $productId = (int)$pidRaw;
                    }
                    $itemIdInt = (int)$itemId;
                    if ($productId === null) {
                        $null = null;
                        $stmtItem->bind_param('iii', $null, $itemIdInt, $order_id);
                    } else {
                        $stmtItem->bind_param('iii', $productId, $itemIdInt, $order_id);
                    }
                    if (!$stmtItem->execute()) {
                        $error = 'Gagal memperbarui mapping produk pada item pesanan.';
                        break;
                    }
                }
                $stmtItem->close();
            }
        }

        if ($error === '') {
            $success = 'Pemenuhan pesanan berhasil diperbarui.';
        }
    } elseif ($action === 'create_sale_from_order') {
        $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        $selected_order_id = $order_id;

        if (!$user_id || !$branch_id) {
            $error = 'User atau cabang belum lengkap. Silakan pastikan user terhubung ke cabang.';
        } elseif ($order_id <= 0) {
            $error = 'Pesanan tidak ditemukan.';
        } else {
            $stmtOrder = $conn->prepare("SELECT id, branch_id, customer_name, order_date, status FROM orders WHERE id = ? AND branch_id = ?");
            if ($stmtOrder) {
                $stmtOrder->bind_param('ii', $order_id, $branch_id);
                $stmtOrder->execute();
                $resOrder = $stmtOrder->get_result();
                if ($resOrder && ($rowOrder = $resOrder->fetch_assoc())) {
                    $orderData = $rowOrder;
                } else {
                    $orderData = null;
                }
                $stmtOrder->close();
            } else {
                $orderData = null;
            }

            if (!isset($orderData) || $orderData === null) {
                $error = 'Pesanan tidak valid untuk cabang ini.';
            } else {
                $orderStatus = $orderData['status'] ?? '';
                if ($orderStatus === 'draft') {
                    $error = 'Silakan ubah status pesanan ke \"diproses\" sebelum membuat penjualan.';
                } elseif ($orderStatus === 'selesai') {
                    $error = 'Pesanan sudah berstatus selesai dan tidak dapat dibuat penjualan baru.';
                } else {
                    $items = [];
                    $stmtItems = $conn->prepare("SELECT id, product_id, description, qty, unit FROM order_items WHERE order_id = ? AND qty > 0");
                    if ($stmtItems) {
                        $stmtItems->bind_param('i', $order_id);
                        $stmtItems->execute();
                        $resItems = $stmtItems->get_result();
                        if ($resItems) {
                            while ($rowItem = $resItems->fetch_assoc()) {
                                $items[] = $rowItem;
                            }
                        }
                        $stmtItems->close();
                    }

                    $productIds = [];
                    foreach ($items as $it) {
                        if ($it['product_id'] !== null) {
                            $pid = (int)$it['product_id'];
                            if ($pid > 0) {
                                $productIds[$pid] = true;
                            }
                        }
                    }

                    if (empty($productIds)) {
                        $error = 'Belum ada item pesanan yang dipetakan ke produk untuk dibuat penjualan.';
                    } else {
                        $idList = implode(',', array_map('intval', array_keys($productIds)));
                        $prices = [];
                        $sqlPrice = null;
                        if ($branch_id) {
                            $sqlPrice = "SELECT p.id, COALESCE(bpp.sell_price, p.sell_price) AS sell_price
                                         FROM products p
                                         LEFT JOIN branch_product_prices bpp
                                           ON bpp.product_id = p.id
                                          AND bpp.branch_id = " . (int)$branch_id . "
                                          AND bpp.is_active = 1
                                         WHERE p.id IN (" . $idList . ")";
                        } else {
                            $sqlPrice = "SELECT id, sell_price FROM products WHERE id IN (" . $idList . ")";
                        }
                        if ($sqlPrice !== null) {
                            $resPrice = $conn->query($sqlPrice);
                            if ($resPrice) {
                                while ($rowP = $resPrice->fetch_assoc()) {
                                    $prices[(int)$rowP['id']] = (float)$rowP['sell_price'];
                                }
                            }
                        }

                        $stockByProduct = [];
                        $stmtStock = $conn->prepare("
                            SELECT
                                base.product_id,
                                COALESCE(pur.total_purchase, 0) - COALESCE(sel.total_sale, 0) AS stock_qty
                            FROM (
                                SELECT DISTINCT product_id
                                FROM order_items
                                WHERE order_id = ? AND product_id IS NOT NULL
                            ) base
                            LEFT JOIN (
                                SELECT pi.product_id, SUM(pi.qty) AS total_purchase
                                FROM purchase_items pi
                                JOIN purchases pu ON pu.id = pi.purchase_id
                                WHERE pu.branch_id = ?
                                GROUP BY pi.product_id
                            ) pur ON pur.product_id = base.product_id
                            LEFT JOIN (
                                SELECT si.product_id, SUM(si.qty) AS total_sale
                                FROM sale_items si
                                JOIN sales s ON s.id = si.sale_id
                                WHERE s.branch_id = ?
                                GROUP BY si.product_id
                            ) sel ON sel.product_id = base.product_id
                        ");
                        if ($stmtStock) {
                            $stmtStock->bind_param('iii', $order_id, $branch_id, $branch_id);
                            $stmtStock->execute();
                            $resStock = $stmtStock->get_result();
                            if ($resStock) {
                                while ($rowStock = $resStock->fetch_assoc()) {
                                    $pid = (int)$rowStock['product_id'];
                                    $stockByProduct[$pid] = (float)$rowStock['stock_qty'];
                                }
                            }
                            $stmtStock->close();
                        }

                        $saleItems = [];
                        $totalAmount = 0.0;
                        $allFulfilled = true;

                        foreach ($items as $it) {
                            $pid = $it['product_id'] !== null ? (int)$it['product_id'] : 0;
                            if ($pid <= 0) {
                                $allFulfilled = false;
                                continue;
                            }
                            $orderedQty = (float)$it['qty'];
                            if ($orderedQty <= 0) {
                                continue;
                            }
                            $stockQty = $stockByProduct[$pid] ?? 0.0;
                            if ($stockQty <= 0) {
                                $allFulfilled = false;
                                continue;
                            }
                            $price = $prices[$pid] ?? 0.0;
                            $fulfillQty = $orderedQty;
                            if ($stockQty < $orderedQty) {
                                $fulfillQty = $stockQty;
                                $allFulfilled = false;
                            }
                            if ($fulfillQty <= 0) {
                                $allFulfilled = false;
                                continue;
                            }
                            $subtotal = $fulfillQty * $price;
                            $totalAmount += $subtotal;
                            $saleItems[] = [
                                'product_id' => $pid,
                                'qty' => $fulfillQty,
                                'price' => $price,
                                'subtotal' => $subtotal,
                            ];
                        }

                        if (empty($saleItems)) {
                            $error = 'Tidak ada item yang dapat dibuat penjualan (stok habis atau belum dipetakan).';
                        } else {
                            $conn->begin_transaction();
                            $ok = true;

                            $saleDate = date('Y-m-d');
                            $invoiceNo = null;
                            $notes = 'Penjualan dari pesanan ID ' . $order_id;

                            $sqlSale = "INSERT INTO sales (branch_id, customer_id, customer_name, invoice_no, sale_date, total_amount, notes, created_by)
                                        VALUES (?, NULL, ?, ?, ?, ?, ?, ?)";
                            $stmtSale = $conn->prepare($sqlSale);
                            if ($stmtSale) {
                                $stmtSale->bind_param('issdsis', $branch_id, $orderData['customer_name'], $invoiceNo, $saleDate, $totalAmount, $notes, $user_id);
                                if (!$stmtSale->execute()) {
                                    $ok = false;
                                    $error = 'Gagal menyimpan data penjualan.';
                                }
                                $sale_id = $stmtSale->insert_id;
                                $stmtSale->close();
                            } else {
                                $ok = false;
                                $error = 'Gagal menyiapkan query header penjualan.';
                            }

                            if ($ok) {
                                $sqlSaleItem = "INSERT INTO sale_items (sale_id, product_id, qty, price, subtotal) VALUES (?, ?, ?, ?, ?)";
                                $stmtSaleItem = $conn->prepare($sqlSaleItem);
                                if ($stmtSaleItem) {
                                    foreach ($saleItems as $si) {
                                        $pid = $si['product_id'];
                                        $qty = $si['qty'];
                                        $price = $si['price'];
                                        $subtotal = $si['subtotal'];
                                        $stmtSaleItem->bind_param('iiddd', $sale_id, $pid, $qty, $price, $subtotal);
                                        if (!$stmtSaleItem->execute()) {
                                            $ok = false;
                                            $error = 'Gagal menyimpan item penjualan.';
                                            break;
                                        }
                                    }
                                    $stmtSaleItem->close();
                                } else {
                                    $ok = false;
                                    $error = 'Gagal menyiapkan query item penjualan.';
                                }
                            }

                            if ($ok) {
                                $newStatus = $allFulfilled ? 'selesai' : 'parsial';
                                $stmtStatus = $conn->prepare("UPDATE orders SET status = ?, total_amount = total_amount + ? WHERE id = ?");
                                if ($stmtStatus) {
                                    $stmtStatus->bind_param('sdi', $newStatus, $totalAmount, $order_id);
                                    if (!$stmtStatus->execute()) {
                                        $ok = false;
                                        $error = 'Gagal memperbarui status pesanan setelah penjualan.';
                                    }
                                    $stmtStatus->close();
                                }
                            }

                            if ($ok) {
                                $conn->commit();
                                $success = 'Penjualan dari pesanan berhasil dibuat. <a href=\"sales.php?sale_id=' . (int)$sale_id . '\">Lihat penjualan</a>';
                            } else {
                                $conn->rollback();
                            }
                        }
                    }
                }
            }
        }
    }
}

$sqlOrders = "SELECT id, order_date, customer_name, status, created_at FROM orders ORDER BY order_date DESC, id DESC LIMIT 50";
$resOrders = $conn->query($sqlOrders);
if ($resOrders) {
    while ($row = $resOrders->fetch_assoc()) {
        $orders[] = $row;
    }
}

$sqlCustomers = "SELECT id_orang, nama_lengkap FROM orang WHERE is_customer = 1 AND is_active = 1 ORDER BY nama_lengkap";
$resCustomers = $conn->query($sqlCustomers);
if ($resCustomers) {
    while ($row = $resCustomers->fetch_assoc()) {
        $customers[] = $row;
    }
}

if ($selected_order_id > 0 && $branch_id) {
    $stmtOrder = $conn->prepare("SELECT id, branch_id, customer_name, order_date, status, created_at FROM orders WHERE id = ? AND branch_id = ?");
    if ($stmtOrder) {
        $stmtOrder->bind_param('ii', $selected_order_id, $branch_id);
        $stmtOrder->execute();
        $resOrder = $stmtOrder->get_result();
        if ($resOrder && ($rowOrder = $resOrder->fetch_assoc())) {
            $fulfillment_order = $rowOrder;
        }
        $stmtOrder->close();
    }

    if ($fulfillment_order !== null) {
        $stmtItems = $conn->prepare("SELECT oi.id, oi.seq_no, oi.product_id, oi.description, oi.qty, oi.unit, oi.notes, p.name AS product_name FROM order_items oi LEFT JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ? ORDER BY oi.seq_no, oi.id");
        if ($stmtItems) {
            $stmtItems->bind_param('i', $selected_order_id);
            $stmtItems->execute();
            $resItems = $stmtItems->get_result();
            if ($resItems) {
                while ($rowItem = $resItems->fetch_assoc()) {
                    $fulfillment_items[] = $rowItem;
                }
            }
            $stmtItems->close();
        }

        $products = [];
        if ($branch_id) {
            $sqlProducts = "SELECT p.id, p.name, p.unit FROM products p WHERE p.is_active = 1 ORDER BY p.name";
            $resProducts = $conn->query($sqlProducts);
            if ($resProducts) {
                while ($rowP = $resProducts->fetch_assoc()) {
                    $fulfillment_products[] = $rowP;
                }
            }
        }

        $stockByProduct = [];
        $stmtStock = $conn->prepare("
            SELECT
                base.product_id,
                COALESCE(pur.total_purchase, 0) - COALESCE(sel.total_sale, 0) AS stock_qty
            FROM (
                SELECT DISTINCT product_id
                FROM order_items
                WHERE order_id = ? AND product_id IS NOT NULL
            ) base
            LEFT JOIN (
                SELECT pi.product_id, SUM(pi.qty) AS total_purchase
                FROM purchase_items pi
                JOIN purchases pu ON pu.id = pi.purchase_id
                WHERE pu.branch_id = ?
                GROUP BY pi.product_id
            ) pur ON pur.product_id = base.product_id
            LEFT JOIN (
                SELECT si.product_id, SUM(si.qty) AS total_sale
                FROM sale_items si
                JOIN sales s ON s.id = si.sale_id
                WHERE s.branch_id = ?
                GROUP BY si.product_id
            ) sel ON sel.product_id = base.product_id
        ");
        if ($stmtStock) {
            $stmtStock->bind_param('iii', $selected_order_id, $branch_id, $branch_id);
            $stmtStock->execute();
            $resStock = $stmtStock->get_result();
            if ($resStock) {
                while ($rowStock = $resStock->fetch_assoc()) {
                    $pid = (int)$rowStock['product_id'];
                    $stockByProduct[$pid] = (float)$rowStock['stock_qty'];
                }
            }
            $stmtStock->close();
        }
        $fulfillment_stock = $stockByProduct;

        $summary = [];
        foreach ($fulfillment_items as $item) {
            $pid = $item['product_id'] !== null ? (int)$item['product_id'] : 0;
            if ($pid <= 0) {
                continue;
            }
            if (!isset($summary[$pid])) {
                $summary[$pid] = [
                    'product_id' => $pid,
                    'product_name' => $item['product_name'] ?? '',
                    'total_order_qty' => 0.0,
                    'stock_qty' => $stockByProduct[$pid] ?? 0.0,
                ];
            }
            $summary[$pid]['total_order_qty'] += (float)$item['qty'];
        }
        foreach ($summary as $pid => $row) {
            $orderQty = $row['total_order_qty'];
            $stockQty = $row['stock_qty'];
            $shortage = 0.0;
            $leftover = $stockQty - $orderQty;
            if ($leftover < 0) {
                $shortage = -$leftover;
                $leftover = 0.0;
            }
            $summary[$pid]['shortage_qty'] = $shortage;
            $summary[$pid]['leftover_qty'] = $leftover;
        }
        $fulfillment_summary = $summary;
    }
}

$page_title = 'Pesanan';
$content_view = 'pesanan_view.php';

include __DIR__ . DIRECTORY_SEPARATOR . 'template.php';
