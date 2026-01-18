<?php
require_once __DIR__ . '/auth.php';
require_login();

$error = '';
$success = '';
$customers = [];
$products = [];
$sales_list = [];
$selected_sale = null;
$selected_sale_items = [];
$form_customer_type = 'pelanggan';
$form_customer_id = 0;
$form_invoice_no = '';
$form_sale_date = date('Y-m-d');
$form_product_id = 0;
$form_qty = '';
$form_price = '';
$form_notes = '';

$user = current_user();
$user_id = $user['id'] ?? null;
$branch_id = $user['branch_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $form_customer_type = $_POST['customer_type'] ?? 'pelanggan';
        $form_customer_id = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
        $form_invoice_no = clean($_POST['invoice_no'] ?? '');
        $form_sale_date = parse_date_id_to_db($_POST['sale_date'] ?? date('Y-m-d'));
        $form_product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $form_qty = $_POST['qty'] ?? '';
        $form_price = $_POST['price'] ?? '';
        $form_notes = clean($_POST['notes'] ?? '');

        $qty = (float)str_replace(',', '', $form_qty);
        $price = (float)str_replace(',', '', $form_price);

        $is_general_customer = ($form_customer_type === 'umum');

        if (!$user_id || !$branch_id) {
            $error = 'User atau cabang belum lengkap. Silakan pastikan user terhubung ke cabang.';
        } elseif (!$is_general_customer && $form_customer_id <= 0) {
            $error = 'Pembeli wajib dipilih.';
        } elseif ($form_product_id <= 0) {
            $error = 'Produk wajib dipilih.';
        } elseif ($form_sale_date === '') {
            $error = 'Tanggal penjualan wajib diisi.';
        } elseif ($qty <= 0) {
            $error = 'Jumlah penjualan harus lebih besar dari nol.';
        } elseif ($price < 0) {
            $error = 'Harga tidak boleh bernilai negatif.';
        } else {
            $customer_name = '';
            if ($is_general_customer) {
                $customer_name = 'Pembeli Umum';
                $form_customer_id = 0;
            } else {
                $stmtCust = $conn->prepare("SELECT nama_lengkap FROM orang WHERE id_orang = ? AND is_customer = 1 AND is_active = 1 LIMIT 1");
                if ($stmtCust) {
                    $stmtCust->bind_param('i', $form_customer_id);
                    $stmtCust->execute();
                    $resCust = $stmtCust->get_result();
                    if ($rowCust = $resCust->fetch_assoc()) {
                        $customer_name = $rowCust['nama_lengkap'];
                    }
                    $stmtCust->close();
                }
            }

            $product_name = '';
            $stmtProd = $conn->prepare("SELECT name FROM products WHERE id = ? AND is_active = 1 LIMIT 1");
            if ($stmtProd) {
                $stmtProd->bind_param('i', $form_product_id);
                $stmtProd->execute();
                $resProd = $stmtProd->get_result();
                if ($rowProd = $resProd->fetch_assoc()) {
                    $product_name = $rowProd['name'];
                }
                $stmtProd->close();
            }

            if ($customer_name === '') {
                $error = 'Pembeli tidak valid atau sudah tidak aktif.';
            } elseif ($product_name === '') {
                $error = 'Produk tidak valid atau sudah tidak aktif.';
            } else {
                $subtotal = $qty * $price;
                $conn->begin_transaction();
                $ok = true;

                $sqlHeader = "INSERT INTO sales (branch_id, customer_id, customer_name, invoice_no, sale_date, total_amount, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtHeader = $conn->prepare($sqlHeader);
                if ($stmtHeader) {
                    $stmtHeader->bind_param('issssdis', $branch_id, $form_customer_id, $customer_name, $form_invoice_no, $form_sale_date, $subtotal, $form_notes, $user_id);
                    if (!$stmtHeader->execute()) {
                        $ok = false;
                        $error = 'Gagal menyimpan data penjualan.';
                    }
                    $sale_id = $stmtHeader->insert_id;
                    $stmtHeader->close();
                } else {
                    $ok = false;
                    $error = 'Gagal menyiapkan query header penjualan.';
                }

                if ($ok) {
                    $sqlItem = "INSERT INTO sale_items (sale_id, product_id, qty, price, subtotal) VALUES (?, ?, ?, ?, ?)";
                    $stmtItem = $conn->prepare($sqlItem);
                    if ($stmtItem) {
                        $stmtItem->bind_param('iiddd', $sale_id, $form_product_id, $qty, $price, $subtotal);
                    }
                }

                if ($ok && isset($stmtItem)) {
                    if (!$stmtItem->execute()) {
                        $ok = false;
                        $error = 'Gagal menyimpan item penjualan.';
                    }
                    $stmtItem->close();
                }

                if ($ok) {
                    $conn->commit();
                    $success = 'Transaksi penjualan berhasil disimpan.';
                    $form_customer_type = 'pelanggan';
                    $form_customer_id = 0;
                    $form_invoice_no = '';
                    $form_sale_date = date('Y-m-d');
                    $form_product_id = 0;
                    $form_qty = '';
                    $form_price = '';
                    $form_notes = '';
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
            echo json_encode($response);
            exit;
        }
    }
}

$sqlCustomers = "SELECT id_orang, nama_lengkap FROM orang WHERE is_customer = 1 AND is_active = 1 ORDER BY nama_lengkap";
$resCustomers = $conn->query($sqlCustomers);
if ($resCustomers) {
    while ($row = $resCustomers->fetch_assoc()) {
        $customers[] = $row;
    }
}

if ($branch_id) {
    $sqlProducts = "SELECT p.id, p.name, p.unit, p.barcode, COALESCE(bpp.sell_price, p.sell_price) AS sell_price,
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
    $sqlProducts = "SELECT id, name, unit, barcode, sell_price,
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

$sqlSales = "SELECT id, sale_date, invoice_no, customer_name, total_amount, created_at FROM sales ORDER BY sale_date DESC, id DESC LIMIT 50";
$resSales = $conn->query($sqlSales);
if ($resSales) {
    while ($row = $resSales->fetch_assoc()) {
        $sales_list[] = $row;
    }
}

$selected_sale_id = isset($_GET['sale_id']) ? (int)$_GET['sale_id'] : 0;
if ($selected_sale_id > 0 && $branch_id) {
    $stmtSale = $conn->prepare("SELECT id, branch_id, customer_name, invoice_no, sale_date, total_amount, notes, created_at FROM sales WHERE id = ? AND branch_id = ?");
    if ($stmtSale) {
        $stmtSale->bind_param('ii', $selected_sale_id, $branch_id);
        $stmtSale->execute();
        $resSale = $stmtSale->get_result();
        if ($resSale && ($rowSale = $resSale->fetch_assoc())) {
            $selected_sale = $rowSale;
        }
        $stmtSale->close();
    }

    if ($selected_sale !== null) {
        $stmtItems = $conn->prepare("SELECT si.product_id, p.name AS product_name, p.unit AS unit, si.qty, si.price, si.subtotal FROM sale_items si LEFT JOIN products p ON p.id = si.product_id WHERE si.sale_id = ? ORDER BY si.id");
        if ($stmtItems) {
            $stmtItems->bind_param('i', $selected_sale_id);
            $stmtItems->execute();
            $resItems = $stmtItems->get_result();
            if ($resItems) {
                while ($rowItem = $resItems->fetch_assoc()) {
                    $selected_sale_items[] = $rowItem;
                }
            }
            $stmtItems->close();
        }
    }
}

$page_title = 'Penjualan';
$content_view = 'sales_view.php';

include __DIR__ . '/template.php';
