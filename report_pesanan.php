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
$status_filter = $_GET['status'] ?? 'all';
$allowed_statuses = ['draft', 'diproses', 'parsial', 'selesai'];
if (!in_array($status_filter, $allowed_statuses, true)) {
    $status_filter = 'all';
}

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

$summary_by_status = [];
$detail_orders = [];
$trace_customer_name = isset($_GET['trace_customer_name']) ? trim($_GET['trace_customer_name']) : '';
$customer_orders = [];
$customer_sales = [];
$customer_order_sales_map = [];

if ($active_branch_id) {
    $stmtSummary = $conn->prepare("
        SELECT status, COUNT(*) AS cnt
        FROM orders
        WHERE branch_id = ?
          AND order_date BETWEEN ? AND ?
        GROUP BY status
    ");
    if ($stmtSummary) {
        $stmtSummary->bind_param('iss', $active_branch_id, $filter_start_date, $filter_end_date);
        $stmtSummary->execute();
        $resSummary = $stmtSummary->get_result();
        if ($resSummary) {
            while ($row = $resSummary->fetch_assoc()) {
                $st = $row['status'];
                $summary_by_status[$st] = (int)$row['cnt'];
            }
        }
        $stmtSummary->close();
    }

    $sqlDetail = "
        SELECT id, order_date, customer_name, status, total_amount, created_at
        FROM orders
        WHERE branch_id = ?
          AND order_date BETWEEN ? AND ?
    ";
    if ($status_filter !== 'all') {
        $sqlDetail .= " AND status = ?";
    }
    $sqlDetail .= " ORDER BY order_date DESC, id DESC";

    if ($status_filter !== 'all') {
        $stmtDetail = $conn->prepare($sqlDetail);
        if ($stmtDetail) {
            $stmtDetail->bind_param('isss', $active_branch_id, $filter_start_date, $filter_end_date, $status_filter);
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
                $detail_orders[] = $row;
            }
        }
        $stmtDetail->close();
    }

    if ($trace_customer_name !== '') {
        $stmtCustOrders = $conn->prepare("
            SELECT id, order_date, customer_name, status, total_amount, created_at
            FROM orders
            WHERE branch_id = ?
              AND customer_name LIKE ?
              AND order_date BETWEEN ? AND ?
            ORDER BY order_date ASC, id ASC
        ");
        if ($stmtCustOrders) {
            $like = '%' . $trace_customer_name . '%';
            $stmtCustOrders->bind_param('isss', $active_branch_id, $like, $filter_start_date, $filter_end_date);
            $stmtCustOrders->execute();
            $resCustOrders = $stmtCustOrders->get_result();
            if ($resCustOrders) {
                while ($row = $resCustOrders->fetch_assoc()) {
                    $customer_orders[] = $row;
                }
            }
            $stmtCustOrders->close();
        }

        $stmtCustSales = $conn->prepare("
            SELECT id, sale_date, customer_name, total_amount, notes, created_at
            FROM sales
            WHERE branch_id = ?
              AND customer_name LIKE ?
              AND sale_date BETWEEN ? AND ?
            ORDER BY sale_date ASC, id ASC
        ");
        if ($stmtCustSales) {
            $like = '%' . $trace_customer_name . '%';
            $stmtCustSales->bind_param('isss', $active_branch_id, $like, $filter_start_date, $filter_end_date);
            $stmtCustSales->execute();
            $resCustSales = $stmtCustSales->get_result();
            if ($resCustSales) {
                while ($row = $resCustSales->fetch_assoc()) {
                    $customer_sales[] = $row;
                }
            }
            $stmtCustSales->close();
        }

        if (!empty($customer_sales)) {
            foreach ($customer_sales as $sale) {
                $notes = $sale['notes'] ?? '';
                $orderIdFromNotes = null;
                if ($notes !== '') {
                    if (preg_match('/Penjualan dari pesanan ID\\s+(\\d+)/', $notes, $m)) {
                        $orderIdFromNotes = (int)$m[1];
                    }
                }
                if ($orderIdFromNotes) {
                    if (!isset($customer_order_sales_map[$orderIdFromNotes])) {
                        $customer_order_sales_map[$orderIdFromNotes] = [];
                    }
                    $customer_order_sales_map[$orderIdFromNotes][] = $sale;
                }
            }
        }
    }
}

$export = isset($_GET['export']) ? $_GET['export'] : '';
if ($export === 'orders_csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="laporan_pesanan_' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    if ($out) {
        fputcsv($out, ['ID Pesanan', 'Tanggal Pesanan', 'Pelanggan', 'Status', 'Total', 'Dibuat']);
        foreach ($detail_orders as $row) {
            $total = isset($row['total_amount']) ? (float)$row['total_amount'] : 0.0;
            fputcsv($out, [
                $row['id'],
                format_date_id($row['order_date']),
                $row['customer_name'],
                $row['status'],
                number_format($total, 2, '.', ''),
                format_date_id($row['created_at']),
            ]);
        }
        fclose($out);
    }
    exit;
} elseif ($export === 'trace_csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="laporan_jejak_pelanggan_' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    if ($out) {
        fputcsv($out, ['Jenis', 'ID', 'Tanggal', 'Pelanggan', 'Status', 'Total', 'Catatan', 'OrderID_Sumber']);
        foreach ($customer_orders as $order) {
            $totalOrder = isset($order['total_amount']) ? (float)$order['total_amount'] : 0.0;
            fputcsv($out, [
                'PESANAN',
                $order['id'],
                format_date_id($order['order_date']),
                $order['customer_name'],
                $order['status'],
                number_format($totalOrder, 2, '.', ''),
                '',
                '',
            ]);
        }
        foreach ($customer_sales as $sale) {
            $notes = $sale['notes'] ?? '';
            $orderIdFromNotes = '';
            if ($notes !== '') {
                if (preg_match('/Penjualan dari pesanan ID\\s+(\\d+)/', $notes, $m)) {
                    $orderIdFromNotes = $m[1];
                }
            }
            $totalSale = isset($sale['total_amount']) ? (float)$sale['total_amount'] : 0.0;
            fputcsv($out, [
                'PENJUALAN',
                $sale['id'],
                format_date_id($sale['sale_date']),
                $sale['customer_name'],
                '',
                number_format($totalSale, 2, '.', ''),
                $notes,
                $orderIdFromNotes,
            ]);
        }
        fclose($out);
    }
    exit;
}

$page_title = 'Laporan Pesanan';
$content_view = 'report_pesanan_view.php';

include __DIR__ . '/template.php';
