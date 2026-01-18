<?php
require_once __DIR__ . '/auth.php';
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

if (isset($_GET['ajax']) && $_GET['ajax'] === 'search_products') {
    header('Content-Type: application/json; charset=utf-8');
    $user = current_user();
    $branch_id = $user['branch_id'] ?? null;
    $term = isset($_GET['q']) ? trim($_GET['q']) : '';
    $mode = isset($_GET['mode']) ? $_GET['mode'] : 'sale';
    $items = [];
    if ($term !== '') {
        if ($branch_id) {
            $sqlAjax = "SELECT p.id, p.code, p.name, p.unit, p.barcode, COALESCE(bpp.buy_price, p.buy_price) AS buy_price, COALESCE(bpp.sell_price, p.sell_price) AS sell_price,
(SELECT pc.plu_number FROM product_plu_mapping m JOIN plu_codes pc ON pc.id = m.plu_code_id WHERE m.product_id = p.id AND m.is_primary_plu = 1 LIMIT 1) AS plu_number
FROM products p
LEFT JOIN branch_product_prices bpp
  ON bpp.product_id = p.id
 AND bpp.branch_id = ?
 AND bpp.is_active = 1
WHERE p.is_active = 1
  AND (
    p.code LIKE ?
    OR p.name LIKE ?
    OR p.barcode LIKE ?
    OR EXISTS (
        SELECT 1
        FROM product_barcodes pb
        WHERE pb.product_id = p.id
          AND pb.is_active = 1
          AND pb.barcode_value LIKE ?
    )
  )
ORDER BY p.name
LIMIT 20";
            $stmtAjax = $conn->prepare($sqlAjax);
            if ($stmtAjax) {
                $like = '%' . $term . '%';
                $stmtAjax->bind_param('issss', $branch_id, $like, $like, $like, $like);
                $stmtAjax->execute();
                $resAjax = $stmtAjax->get_result();
                if ($resAjax) {
                    while ($row = $resAjax->fetch_assoc()) {
                        $price = $mode === 'purchase' ? (float)$row['buy_price'] : (float)$row['sell_price'];
                        $items[] = [
                            'id' => (int)$row['id'],
                            'code' => $row['code'],
                            'name' => $row['name'],
                            'unit' => $row['unit'],
                            'barcode' => $row['barcode'] ?? '',
                            'plu_number' => isset($row['plu_number']) ? $row['plu_number'] : null,
                            'price' => $price,
                        ];
                    }
                }
                $stmtAjax->close();
            }
        } else {
            $sqlAjax = "SELECT id, code, name, unit, barcode, buy_price, sell_price,
(SELECT pc.plu_number FROM product_plu_mapping m JOIN plu_codes pc ON pc.id = m.plu_code_id WHERE m.product_id = products.id AND m.is_primary_plu = 1 LIMIT 1) AS plu_number
FROM products
WHERE is_active = 1
  AND (
    code LIKE ?
    OR name LIKE ?
    OR barcode LIKE ?
    OR EXISTS (
        SELECT 1
        FROM product_barcodes pb
        WHERE pb.product_id = products.id
          AND pb.is_active = 1
          AND pb.barcode_value LIKE ?
    )
  )
ORDER BY name
LIMIT 20";
            $stmtAjax = $conn->prepare($sqlAjax);
            if ($stmtAjax) {
                $like = '%' . $term . '%';
                $stmtAjax->bind_param('ssss', $like, $like, $like, $like);
                $stmtAjax->execute();
                $resAjax = $stmtAjax->get_result();
                if ($resAjax) {
                    while ($row = $resAjax->fetch_assoc()) {
                        $price = $mode === 'purchase' ? (float)$row['buy_price'] : (float)$row['sell_price'];
                        $items[] = [
                            'id' => (int)$row['id'],
                            'code' => $row['code'],
                            'name' => $row['name'],
                            'unit' => $row['unit'],
                            'barcode' => $row['barcode'] ?? '',
                            'plu_number' => isset($row['plu_number']) ? $row['plu_number'] : null,
                            'price' => $price,
                        ];
                    }
                }
                $stmtAjax->close();
            }
        }
    }
    echo json_encode($items);
    exit;
}

$error = '';
$success = '';
$edit_product = null;
$products = [];
$categories = [];
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $code = clean($_POST['code'] ?? '');
        $name = sppg_to_title_case(clean($_POST['name'] ?? ''));
        $unit = clean($_POST['unit'] ?? '');
        $barcode = clean($_POST['barcode'] ?? '');
        $plu_number = clean($_POST['plu_number'] ?? '');
        $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
        $buy_price = isset($_POST['buy_price']) ? (float)str_replace(',', '', $_POST['buy_price']) : 0.0;
        $sell_price = isset($_POST['sell_price']) ? (float)str_replace(',', '', $_POST['sell_price']) : 0.0;
        $stock_qty = isset($_POST['stock_qty']) ? (float)str_replace(',', '', $_POST['stock_qty']) : 0.0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($name === '' || $unit === '') {
            $error = 'Nama dan Satuan produk wajib diisi.';
        } elseif ($buy_price < 0 || $sell_price < 0 || $stock_qty < 0) {
            $error = 'Harga beli, harga jual, dan stok tidak boleh bernilai negatif.';
        } elseif ($id > 0 && $code === '') {
            $error = 'Kode produk wajib diisi untuk produk yang sudah ada.';
        } else {
            if ($code !== '') {
                $sqlCheck = "SELECT id FROM products WHERE code = ? AND id <> ? LIMIT 1";
                $stmtCheck = $conn->prepare($sqlCheck);
                if ($stmtCheck) {
                    $stmtCheck->bind_param('si', $code, $id);
                    $stmtCheck->execute();
                    $resCheck = $stmtCheck->get_result();
                    if ($resCheck->fetch_assoc()) {
                        $error = 'Kode produk sudah digunakan. Silakan gunakan kode lain.';
                    }
                    $stmtCheck->close();
                }
            }
        }

        if ($error === '') {
            if ($id === 0 && $code === '') {
                $generatedCode = null;
                for ($i = 0; $i < 5; $i++) {
                    $candidate = 'P' . date('ymd') . '-' . mt_rand(100, 999);
                    $sqlCheckCandidate = "SELECT id FROM products WHERE code = ? LIMIT 1";
                    $stmtCheckCandidate = $conn->prepare($sqlCheckCandidate);
                    if ($stmtCheckCandidate) {
                        $stmtCheckCandidate->bind_param('s', $candidate);
                        $stmtCheckCandidate->execute();
                        $resCheckCandidate = $stmtCheckCandidate->get_result();
                        if ($resCheckCandidate && !$resCheckCandidate->fetch_assoc()) {
                            $generatedCode = $candidate;
                            $stmtCheckCandidate->close();
                            break;
                        }
                        $stmtCheckCandidate->close();
                    }
                }
                if ($generatedCode === null) {
                    $error = 'Gagal menghasilkan kode produk otomatis.';
                } else {
                    $code = $generatedCode;
                }
            }
        }

        if ($error === '') {
            if ($id > 0) {
                $sql = "UPDATE products SET code = ?, name = ?, unit = ?, barcode = ?, category_id = ?, buy_price = ?, sell_price = ?, stock_qty = ?, is_active = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('ssssidddii', $code, $name, $unit, $barcode, $category_id, $buy_price, $sell_price, $stock_qty, $is_active, $id);
                    if ($stmt->execute()) {
                        $success = 'Data produk berhasil diperbarui.';
                        $product_id_for_plu = $id;
                    } else {
                        $error = 'Gagal memperbarui data produk.';
                    }
                    $stmt->close();
                } else {
                    $error = 'Gagal menyiapkan query update produk.';
                }
            } else {
                $sql = "INSERT INTO products (code, name, unit, barcode, category_id, buy_price, sell_price, stock_qty, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('ssssidddi', $code, $name, $unit, $barcode, $category_id, $buy_price, $sell_price, $stock_qty, $is_active);
                    if ($stmt->execute()) {
                        $success = 'Produk baru berhasil ditambahkan.';
                        $product_id_for_plu = (int)$conn->insert_id;
                    } else {
                        $error = 'Gagal menambahkan produk baru.';
                    }
                    $stmt->close();
                } else {
                    $error = 'Gagal menyiapkan query insert produk.';
                }
            }
            if ($error === '' && isset($product_id_for_plu) && $plu_number !== '') {
                $sqlPlu = "SELECT id FROM plu_codes WHERE plu_number = ? AND is_active = 1 LIMIT 1";
                $stmtPlu = $conn->prepare($sqlPlu);
                if ($stmtPlu) {
                    $stmtPlu->bind_param('s', $plu_number);
                    $stmtPlu->execute();
                    $resPlu = $stmtPlu->get_result();
                    if ($rowPlu = $resPlu->fetch_assoc()) {
                        $plu_id = (int)$rowPlu['id'];
                        $stmtPlu->close();
                        $sqlCheckMap = "SELECT id FROM product_plu_mapping WHERE product_id = ? AND plu_code_id = ? LIMIT 1";
                        $stmtCheckMap = $conn->prepare($sqlCheckMap);
                        if ($stmtCheckMap) {
                            $stmtCheckMap->bind_param('ii', $product_id_for_plu, $plu_id);
                            $stmtCheckMap->execute();
                            $resCheckMap = $stmtCheckMap->get_result();
                            if (!$resCheckMap->fetch_assoc()) {
                                $stmtCheckMap->close();
                                $sqlInsertMap = "INSERT INTO product_plu_mapping (product_id, plu_code_id, province_id, local_name, is_primary_plu, effective_date) VALUES (?, ?, NULL, ?, 1, CURRENT_DATE)";
                                $stmtInsertMap = $conn->prepare($sqlInsertMap);
                                if ($stmtInsertMap) {
                                    $local_name = $name;
                                    $stmtInsertMap->bind_param('iis', $product_id_for_plu, $plu_id, $local_name);
                                    $stmtInsertMap->execute();
                                    $stmtInsertMap->close();
                                }
                            } else {
                                $stmtCheckMap->close();
                            }
                        }
                        $sqlCheckBarcode = "SELECT id, product_id FROM product_barcodes WHERE barcode_value = ? LIMIT 1";
                        $stmtCheckBarcode = $conn->prepare($sqlCheckBarcode);
                        if ($stmtCheckBarcode) {
                            $stmtCheckBarcode->bind_param('s', $plu_number);
                            $stmtCheckBarcode->execute();
                            $resCheckBarcode = $stmtCheckBarcode->get_result();
                            if (!$rowBarcode = $resCheckBarcode->fetch_assoc()) {
                                $stmtCheckBarcode->close();
                                $sqlInsertBarcode = "INSERT INTO product_barcodes (product_id, plu_code_id, barcode_type, barcode_value, is_primary, description, is_active) VALUES (?, ?, 'PLU', ?, 1, ?, 1)";
                                $stmtInsertBarcode = $conn->prepare($sqlInsertBarcode);
                                if ($stmtInsertBarcode) {
                                    $desc = 'PLU ' . $plu_number . ' untuk ' . $name;
                                    $stmtInsertBarcode->bind_param('iiss', $product_id_for_plu, $plu_id, $plu_number, $desc);
                                    $stmtInsertBarcode->execute();
                                    $stmtInsertBarcode->close();
                                }
                            } else {
                                $stmtCheckBarcode->close();
                            }
                        }
                    } else {
                        $stmtPlu->close();
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
    } elseif ($action === 'quick_add_product') {
        header('Content-Type: application/json; charset=utf-8');
        $response = [
            'success' => false,
            'error' => '',
            'product' => null,
        ];

        $code = clean($_POST['code'] ?? '');
        $name = sppg_to_title_case(clean($_POST['name'] ?? ''));
        $unit = clean($_POST['unit'] ?? '');
        $sell_price_raw = $_POST['sell_price'] ?? '';
        $sell_price = 0;
        if ($sell_price_raw !== '') {
            $sell_price = (float)str_replace(',', '', $sell_price_raw);
        }
        $is_active = isset($_POST['is_active']) && $_POST['is_active'] ? 1 : 0;

        if ($name === '' || $unit === '') {
            $response['error'] = 'Nama dan satuan produk wajib diisi.';
            echo json_encode($response);
            exit;
        }
        if ($sell_price < 0) {
            $response['error'] = 'Harga jual tidak boleh bernilai negatif.';
            echo json_encode($response);
            exit;
        }

        if ($code !== '') {
            $sqlCheckQuick = "SELECT id FROM products WHERE code = ? LIMIT 1";
            $stmtCheckQuick = $conn->prepare($sqlCheckQuick);
            if ($stmtCheckQuick) {
                $stmtCheckQuick->bind_param('s', $code);
                $stmtCheckQuick->execute();
                $resCheckQuick = $stmtCheckQuick->get_result();
                if ($resCheckQuick && $resCheckQuick->fetch_assoc()) {
                    $response['error'] = 'Kode produk sudah digunakan. Kosongkan kode atau gunakan kode lain.';
                    $stmtCheckQuick->close();
                    echo json_encode($response);
                    exit;
                }
                $stmtCheckQuick->close();
            }
        } else {
            $generatedCode = null;
            for ($i = 0; $i < 5; $i++) {
                $candidate = 'P' . date('ymd') . '-' . mt_rand(100, 999);
                $sqlCheckCandidate = "SELECT id FROM products WHERE code = ? LIMIT 1";
                $stmtCheckCandidate = $conn->prepare($sqlCheckCandidate);
                if ($stmtCheckCandidate) {
                    $stmtCheckCandidate->bind_param('s', $candidate);
                    $stmtCheckCandidate->execute();
                    $resCheckCandidate = $stmtCheckCandidate->get_result();
                    if ($resCheckCandidate && !$resCheckCandidate->fetch_assoc()) {
                        $generatedCode = $candidate;
                        $stmtCheckCandidate->close();
                        break;
                    }
                    $stmtCheckCandidate->close();
                }
            }
            if ($generatedCode === null) {
                $response['error'] = 'Gagal menghasilkan kode produk otomatis.';
                echo json_encode($response);
                exit;
            }
            $code = $generatedCode;
        }

        $category_id = 0;
        $buy_price = 0;

        $sqlQuickInsert = "INSERT INTO products (code, name, unit, barcode, category_id, buy_price, sell_price, is_active) VALUES (?, ?, ?, '', ?, ?, ?, ?)";
        $stmtQuickInsert = $conn->prepare($sqlQuickInsert);
        if ($stmtQuickInsert) {
            $stmtQuickInsert->bind_param('sssiddi', $code, $name, $unit, $category_id, $buy_price, $sell_price, $is_active);
            if ($stmtQuickInsert->execute()) {
                $newId = (int)$conn->insert_id;
                $response['success'] = true;
                $response['product'] = [
                    'id' => $newId,
                    'code' => $code,
                    'name' => $name,
                    'unit' => $unit,
                    'sell_price' => $sell_price,
                    'is_active' => $is_active,
                ];
            } else {
                $response['error'] = 'Gagal menambahkan produk baru.';
            }
            $stmtQuickInsert->close();
        } else {
            $response['error'] = 'Gagal menyiapkan query produk baru.';
        }

        echo json_encode($response);
        exit;
    } elseif ($action === 'toggle') {
        $id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $newStatus = null;
        if ($id > 0) {
            $sqlGet = "SELECT is_active FROM products WHERE id = ? LIMIT 1";
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
                    $sqlToggle = "UPDATE products SET is_active = ? WHERE id = ?";
                    $stmtToggle = $conn->prepare($sqlToggle);
                    if ($stmtToggle) {
                        $stmtToggle->bind_param('ii', $new, $id);
                        if ($stmtToggle->execute()) {
                            if ($new === 1) {
                                $success = 'Produk telah diaktifkan kembali.';
                            } else {
                                $success = 'Produk telah dinonaktifkan. Produk yang dinonaktifkan tidak bisa dipilih di transaksi baru, tetapi tetap tercatat pada transaksi pembelian dan penjualan yang sudah ada.';
                            }
                        } else {
                            $error = 'Gagal mengubah status produk.';
                        }
                        $stmtToggle->close();
                    } else {
                        $error = 'Gagal menyiapkan query perubahan status produk.';
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

$sqlCategories = "SELECT id, name FROM product_categories ORDER BY name";
$resCategories = $conn->query($sqlCategories);
if ($resCategories) {
    while ($rowCat = $resCategories->fetch_assoc()) {
        $categories[] = $rowCat;
    }
}

$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($edit_id > 0) {
    $sqlEdit = "SELECT p.id, p.code, p.name, p.unit, p.barcode, p.category_id, p.buy_price, p.sell_price, p.stock_qty, p.is_active,
(SELECT pc.plu_number FROM product_plu_mapping m JOIN plu_codes pc ON pc.id = m.plu_code_id WHERE m.product_id = p.id AND m.is_primary_plu = 1 LIMIT 1) AS plu_number
FROM products p WHERE p.id = ? LIMIT 1";
    $stmtEdit = $conn->prepare($sqlEdit);
    if ($stmtEdit) {
        $stmtEdit->bind_param('i', $edit_id);
        $stmtEdit->execute();
        $resEdit = $stmtEdit->get_result();
        if ($rowEdit = $resEdit->fetch_assoc()) {
            $edit_product = $rowEdit;
        }
        $stmtEdit->close();
    }
}

$conditions = ['1=1'];
if ($status === 'active') {
    $conditions[] = 'is_active = 1';
} elseif ($status === 'inactive') {
    $conditions[] = 'is_active = 0';
}
$whereSql = implode(' AND ', $conditions);
$sqlList = "SELECT p.id, p.code, p.name, p.unit, p.barcode, p.category_id, c.name AS category_name, p.buy_price, p.sell_price, p.profit_percent, p.internet_price, p.internet_date, p.stock_qty, p.min_stock_qty, p.is_active, p.created_at, p.updated_at,
(SELECT pc.plu_number FROM product_plu_mapping m JOIN plu_codes pc ON pc.id = m.plu_code_id WHERE m.product_id = p.id AND m.is_primary_plu = 1 LIMIT 1) AS plu_number
FROM products p LEFT JOIN product_categories c ON p.category_id = c.id WHERE $whereSql";
$params = [];
$types = '';
if ($q !== '') {
    $sqlList .= " AND (p.code LIKE ? OR p.name LIKE ? OR p.barcode LIKE ? OR EXISTS (SELECT 1 FROM product_barcodes pb WHERE pb.product_id = p.id AND pb.is_active = 1 AND pb.barcode_value LIKE ?))";
    $like = '%' . $q . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'ssss';
}
$sqlList .= " ORDER BY name";
if (!empty($params)) {
    $stmtList = $conn->prepare($sqlList);
    if ($stmtList) {
        $stmtList->bind_param($types, ...$params);
        $stmtList->execute();
        $resList = $stmtList->get_result();
        if ($resList) {
            while ($row = $resList->fetch_assoc()) {
                $products[] = $row;
            }
        }
        $stmtList->close();
    }
} else {
    $resList = $conn->query($sqlList);
    if ($resList) {
        while ($row = $resList->fetch_assoc()) {
            $products[] = $row;
        }
    }
}

$product_counts = [
    'total_all' => 0,
    'total_active' => 0,
    'total_inactive' => 0,
];
$sqlCount = "SELECT
    COUNT(*) AS total_all,
    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS total_active,
    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) AS total_inactive
FROM products";
$resCount = $conn->query($sqlCount);
if ($resCount && $rowCount = $resCount->fetch_assoc()) {
    $product_counts['total_all'] = (int)$rowCount['total_all'];
    $product_counts['total_active'] = (int)$rowCount['total_active'];
    $product_counts['total_inactive'] = (int)$rowCount['total_inactive'];
}
if ($resCount) {
    $resCount->close();
}

$page_title = 'Produk';
$content_view = 'products_view.php';

include __DIR__ . '/template.php';
