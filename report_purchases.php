<?php
require_once __DIR__ . '/auth.php';
require_login();

$user = current_user();
$user_branch_id = $user['branch_id'] ?? null;
$user_role = $user['role'] ?? '';
$branches_for_filter = [];
$active_branch_id = null;

$filter_start_date = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? parse_date_id_to_db($_GET['start_date']) : date('Y-m-01');
$filter_end_date = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? parse_date_id_to_db($_GET['end_date']) : date('Y-m-d');
$supplier_filter_id = isset($_GET['supplier_id']) ? (int)$_GET['supplier_id'] : 0;
if ($supplier_filter_id < 0) {
    $supplier_filter_id = 0;
}
$category_filter_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
if ($category_filter_id < 0) {
    $category_filter_id = 0;
}

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date';
$dir = isset($_GET['dir']) ? $_GET['dir'] : 'desc';
$allowedSort = ['date', 'supplier', 'invoice', 'total'];
if (!in_array($sort, $allowedSort, true)) {
    $sort = 'date';
}
$dir = strtolower($dir) === 'asc' ? 'asc' : 'desc';

if ($user_role === 'owner') {
    $stmtBranches = $conn->prepare("SELECT id, name FROM branches ORDER BY name");
    if ($stmtBranches) {
        $stmtBranches->execute();
        $resBranches = $stmtBranches->get_result();
        if ($resBranches) {
            while ($rowB = $resBranches->fetch_assoc()) {
                $branches_for_filter[] = $rowB;
            }
        }
        $stmtBranches->close();
    }
    if (!empty($branches_for_filter)) {
        $branch_id_raw = isset($_GET['branch_id']) ? $_GET['branch_id'] : '';
        if ($branch_id_raw !== '') {
            $active_branch_id = (int)$branch_id_raw;
            if ($active_branch_id <= 0) {
                $active_branch_id = null;
            }
        } else {
            $first = $branches_for_filter[0];
            $active_branch_id = isset($first['id']) ? (int)$first['id'] : null;
        }
    }
} else {
    if ($user_branch_id) {
        $active_branch_id = (int)$user_branch_id;
    }
}

$suppliers_for_filter = [];
$categories_for_filter = [];

$sqlSup = "SELECT id_orang, nama_lengkap FROM orang WHERE is_supplier = 1 AND is_active = 1 ORDER BY nama_lengkap";
$resSup = $conn->query($sqlSup);
if ($resSup) {
    while ($row = $resSup->fetch_assoc()) {
        $suppliers_for_filter[] = $row;
    }
}

$sqlCat = "SELECT id, name FROM product_categories ORDER BY name";
$resCat = $conn->query($sqlCat);
if ($resCat) {
    while ($row = $resCat->fetch_assoc()) {
        $categories_for_filter[] = $row;
    }
}

$summary_by_supplier = [];
$summary_by_category = [];
$detail_purchases = [];

if ($active_branch_id) {
    $orderSql = "p.purchase_date DESC, p.id DESC";
    if ($sort === 'date') {
        $orderSql = "p.purchase_date " . strtoupper($dir) . ", p.id " . strtoupper($dir);
    } elseif ($sort === 'supplier') {
        $orderSql = "p.supplier_name " . strtoupper($dir) . ", p.purchase_date DESC, p.id DESC";
    } elseif ($sort === 'invoice') {
        $orderSql = "p.invoice_no " . strtoupper($dir) . ", p.purchase_date DESC, p.id DESC";
    } elseif ($sort === 'total') {
        $orderSql = "p.total_amount " . strtoupper($dir) . ", p.purchase_date DESC, p.id DESC";
    }

    $sqlSummarySupplier = "
        SELECT supplier_id, supplier_name, SUM(total_amount) AS total
        FROM purchases
        WHERE branch_id = ?
          AND purchase_date BETWEEN ? AND ?
    ";
    if ($supplier_filter_id > 0) {
        $sqlSummarySupplier .= " AND supplier_id = ?";
    }
    $sqlSummarySupplier .= " GROUP BY supplier_id, supplier_name ORDER BY supplier_name ASC";

    if ($supplier_filter_id > 0) {
        $stmtSumSup = $conn->prepare($sqlSummarySupplier);
        if ($stmtSumSup) {
            $stmtSumSup->bind_param('issi', $active_branch_id, $filter_start_date, $filter_end_date, $supplier_filter_id);
        }
    } else {
        $stmtSumSup = $conn->prepare($sqlSummarySupplier);
        if ($stmtSumSup) {
            $stmtSumSup->bind_param('iss', $active_branch_id, $filter_start_date, $filter_end_date);
        }
    }
    if (isset($stmtSumSup) && $stmtSumSup) {
        $stmtSumSup->execute();
        $resSumSup = $stmtSumSup->get_result();
        if ($resSumSup) {
            while ($row = $resSumSup->fetch_assoc()) {
                $summary_by_supplier[] = $row;
            }
        }
        $stmtSumSup->close();
    }

    $sqlSummaryCategory = "
        SELECT c.id AS category_id, c.name AS category_name, SUM(pi.subtotal) AS total
        FROM purchases p
        JOIN purchase_items pi ON pi.purchase_id = p.id
        JOIN products pr ON pr.id = pi.product_id
        LEFT JOIN product_categories c ON c.id = pr.category_id
        WHERE p.branch_id = ?
          AND p.purchase_date BETWEEN ? AND ?
    ";
    if ($supplier_filter_id > 0) {
        $sqlSummaryCategory .= " AND p.supplier_id = ?";
    }
    if ($category_filter_id > 0) {
        $sqlSummaryCategory .= " AND c.id = ?";
    }
    $sqlSummaryCategory .= " GROUP BY c.id, c.name ORDER BY c.name ASC";

    if ($supplier_filter_id > 0 && $category_filter_id > 0) {
        $stmtSumCat = $conn->prepare($sqlSummaryCategory);
        if ($stmtSumCat) {
            $stmtSumCat->bind_param('issii', $active_branch_id, $filter_start_date, $filter_end_date, $supplier_filter_id, $category_filter_id);
        }
    } elseif ($supplier_filter_id > 0) {
        $stmtSumCat = $conn->prepare($sqlSummaryCategory);
        if ($stmtSumCat) {
            $stmtSumCat->bind_param('issi', $active_branch_id, $filter_start_date, $filter_end_date, $supplier_filter_id);
        }
    } elseif ($category_filter_id > 0) {
        $stmtSumCat = $conn->prepare($sqlSummaryCategory);
        if ($stmtSumCat) {
            $stmtSumCat->bind_param('issi', $active_branch_id, $filter_start_date, $filter_end_date, $category_filter_id);
        }
    } else {
        $stmtSumCat = $conn->prepare($sqlSummaryCategory);
        if ($stmtSumCat) {
            $stmtSumCat->bind_param('iss', $active_branch_id, $filter_start_date, $filter_end_date);
        }
    }
    if (isset($stmtSumCat) && $stmtSumCat) {
        $stmtSumCat->execute();
        $resSumCat = $stmtSumCat->get_result();
        if ($resSumCat) {
            while ($row = $resSumCat->fetch_assoc()) {
                $summary_by_category[] = $row;
            }
        }
        $stmtSumCat->close();
    }

    $sqlDetail = "
        SELECT p.id, p.purchase_date, p.invoice_no, p.supplier_invoice_no, p.supplier_name, p.total_amount, p.created_at
        FROM purchases p
        WHERE p.branch_id = ?
          AND p.purchase_date BETWEEN ? AND ?
    ";
    if ($supplier_filter_id > 0) {
        $sqlDetail .= " AND p.supplier_id = ?";
    }
    if ($category_filter_id > 0) {
        $sqlDetail .= " AND EXISTS (
            SELECT 1
            FROM purchase_items pi
            JOIN products pr ON pr.id = pi.product_id
            WHERE pi.purchase_id = p.id
              AND pr.category_id = ?
        )";
    }
    $sqlDetail .= " ORDER BY " . $orderSql;

    if ($supplier_filter_id > 0 && $category_filter_id > 0) {
        $stmtDetail = $conn->prepare($sqlDetail);
        if ($stmtDetail) {
            $stmtDetail->bind_param('issii', $active_branch_id, $filter_start_date, $filter_end_date, $supplier_filter_id, $category_filter_id);
        }
    } elseif ($supplier_filter_id > 0) {
        $stmtDetail = $conn->prepare($sqlDetail);
        if ($stmtDetail) {
            $stmtDetail->bind_param('issi', $active_branch_id, $filter_start_date, $filter_end_date, $supplier_filter_id);
        }
    } elseif ($category_filter_id > 0) {
        $stmtDetail = $conn->prepare($sqlDetail);
        if ($stmtDetail) {
            $stmtDetail->bind_param('issi', $active_branch_id, $filter_start_date, $filter_end_date, $category_filter_id);
        }
    } else {
        $stmtDetail = $conn->prepare($sqlDetail);
        if ($stmtDetail) {
            $stmtDetail->bind_param('iss', $active_branch_id, $filter_start_date, $filter_end_date);
        }
    }
    if (isset($stmtDetail) && $stmtDetail) {
        $stmtDetail->execute();
        $resDetail = $stmtDetail->get_result();
        if ($resDetail) {
            while ($row = $resDetail->fetch_assoc()) {
                $detail_purchases[] = $row;
            }
        }
        $stmtDetail->close();
    }
}

$export = isset($_GET['export']) ? $_GET['export'] : '';
if ($export === 'purchases_csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="laporan_pembelian_' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    if ($out) {
        fputcsv($out, ['ID Pembelian', 'Tanggal Pembelian', 'Pemasok', 'No Faktur Internal', 'No Faktur Supplier', 'Total', 'Dibuat']);
        foreach ($detail_purchases as $row) {
            $total = isset($row['total_amount']) ? (float)$row['total_amount'] : 0.0;
            fputcsv($out, [
                $row['id'],
                format_date_id($row['purchase_date']),
                $row['supplier_name'],
                $row['invoice_no'],
                $row['supplier_invoice_no'],
                number_format($total, 2, '.', ''),
                format_date_id($row['created_at']),
            ]);
        }
        fclose($out);
    }
    exit;
} elseif ($export === 'purchases_supplier_summary_csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="ringkasan_pembelian_pemasok_' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    if ($out) {
        fputcsv($out, ['Pemasok', 'Total Pembelian']);
        foreach ($summary_by_supplier as $row) {
            $total = isset($row['total']) ? (float)$row['total'] : 0.0;
            fputcsv($out, [
                $row['supplier_name'],
                number_format($total, 2, '.', ''),
            ]);
        }
        fclose($out);
    }
    exit;
} elseif ($export === 'purchases_category_summary_csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="ringkasan_pembelian_kategori_' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    if ($out) {
        fputcsv($out, ['Kategori', 'Total Pembelian']);
        foreach ($summary_by_category as $row) {
            $total = isset($row['total']) ? (float)$row['total'] : 0.0;
            $name = $row['category_name'] !== null ? $row['category_name'] : 'Tanpa kategori';
            fputcsv($out, [
                $name,
                number_format($total, 2, '.', ''),
            ]);
        }
        fclose($out);
    }
    exit;
}

$page_title = 'Laporan Pembelian';
$content_view = 'report_purchases_view.php';

include __DIR__ . '/template.php';
