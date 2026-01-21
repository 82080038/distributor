<div class="row">
    <div class="col-md-12">
        <h1 class="h3 mb-3">Laporan Pesanan</h1>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h6 mb-3">Ringkasan Pesanan per Status</h2>
                <form method="get" class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal dari</label>
                        <div class="input-group input-group-sm">
                            <input
                                type="text"
                                name="start_date"
                                id="report_start_date"
                                class="form-control form-control-sm date-input"
                                inputmode="numeric"
                                pattern="\d{2}-\d{2}-\d{4}"
                                placeholder="dd-mm-yyyy"
                                value="<?php echo htmlspecialchars(format_date_id($filter_start_date), ENT_QUOTES, 'UTF-8'); ?>"
                                aria-label="Tanggal mulai laporan pesanan"
                                data-calendar-button="#btn_report_start_date"
                            >
                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                id="btn_report_start_date"
                                aria-label="Pilih tanggal mulai laporan pesanan"
                            >
                                📅
                            </button>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal sampai</label>
                        <div class="input-group input-group-sm">
                            <input
                                type="text"
                                name="end_date"
                                id="report_end_date"
                                class="form-control form-control-sm date-input"
                                inputmode="numeric"
                                pattern="\d{2}-\d{2}-\d{4}"
                                placeholder="dd-mm-yyyy"
                                value="<?php echo htmlspecialchars(format_date_id($filter_end_date), ENT_QUOTES, 'UTF-8'); ?>"
                                aria-label="Tanggal selesai laporan pesanan"
                                data-calendar-button="#btn_report_end_date"
                            >
                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                id="btn_report_end_date"
                                aria-label="Pilih tanggal selesai laporan pesanan"
                            >
                                📅
                            </button>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <?php
                    $isOwner = isset($user) && isset($user['role']) && $user['role'] === 'owner';
                    ?>
                    <?php if (!empty($branches_for_filter) && $isOwner): ?>
                    <div class="col-md-2">
                        <label class="form-label">Cabang</label>
                        <select name="branch_id" class="form-select form-select-sm">
                            <?php foreach ($branches_for_filter as $b): ?>
                            <option value="<?php echo (int)$b['id']; ?>" <?php echo isset($active_branch_id) && (int)$active_branch_id === (int)$b['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($b['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <?php
                            $statusOptions = [
                                'all' => 'Semua',
                                'draft' => 'draft',
                                'diproses' => 'diproses',
                                'parsial' => 'parsial',
                                'selesai' => 'selesai',
                            ];
                            foreach ($statusOptions as $value => $label):
                            ?>
                            <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $status_filter === $value ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Terapkan Filter</button>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-4">
                        <h3 class="h6">Ringkasan Status</h3>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th class="text-end">Jumlah Pesanan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $allStatuses = ['draft', 'diproses', 'parsial', 'selesai'];
                                    foreach ($allStatuses as $st):
                                        $count = isset($summary_by_status[$st]) ? (int)$summary_by_status[$st] : 0;
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($st, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-end"><?php echo $count; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="h6 mb-0">Daftar Pesanan</h3>
                            <form method="get" class="d-inline">
                                <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($filter_start_date, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($filter_end_date, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php if (isset($active_branch_id) && $active_branch_id): ?>
                                <input type="hidden" name="branch_id" value="<?php echo (int)$active_branch_id; ?>">
                                <?php endif; ?>
                                <input type="hidden" name="export" value="orders_csv">
                                <button type="submit" class="btn btn-outline-secondary btn-sm">Export CSV Pesanan</button>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Tanggal Pesanan</th>
                                        <th>Pembeli</th>
                                        <th>Status</th>
                                        <th class="text-end">Total</th>
                                        <th>Dibuat</th>
                                        <th style="width: 140px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($detail_orders)): ?>
                                    <tr>
                                        <td colspan="6">Tidak ada pesanan pada periode dan filter ini.</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($detail_orders as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(format_date_id($row['order_date']), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row['customer_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-end">
                                            <?php
                                            $total = isset($row['total_amount']) ? (float)$row['total_amount'] : 0.0;
                                            echo htmlspecialchars('Rp ' . number_format($total, 2, ',', '.'), ENT_QUOTES, 'UTF-8');
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars(format_date_id($row['created_at']), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <a href="pesanan.php?order_id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-primary">Pemenuhan</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h2 class="h6 mb-3">Jejak Pelanggan: Pesanan ke Penjualan</h2>
                <form method="get" class="row g-2 mb-3">
                    <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($filter_start_date, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($filter_end_date, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if (isset($active_branch_id) && $active_branch_id): ?>
                    <input type="hidden" name="branch_id" value="<?php echo (int)$active_branch_id; ?>">
                    <?php endif; ?>
                    <div class="col-md-6">
                        <label class="form-label">Nama pelanggan</label>
                        <input
                            type="text"
                            name="trace_customer_name"
                            class="form-control form-control-sm"
                            value="<?php echo htmlspecialchars($trace_customer_name, ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="Ketik nama pelanggan"
                        >
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary btn-sm w-100">Lihat Jejak</button>
                    </div>
                </form>
                <?php if ($trace_customer_name !== ''): ?>
                <p class="mb-3">
                    Menampilkan jejak untuk pelanggan:
                    <strong><?php echo htmlspecialchars($trace_customer_name, ENT_QUOTES, 'UTF-8'); ?></strong>
                    pada periode
                    <strong><?php echo htmlspecialchars(format_date_id($filter_start_date), ENT_QUOTES, 'UTF-8'); ?></strong>
                    s.d.
                    <strong><?php echo htmlspecialchars(format_date_id($filter_end_date), ENT_QUOTES, 'UTF-8'); ?></strong>
                </p>
                <div class="mb-3">
                    <form method="get" class="d-inline">
                        <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($filter_start_date, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($filter_end_date, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php if (isset($active_branch_id) && $active_branch_id): ?>
                        <input type="hidden" name="branch_id" value="<?php echo (int)$active_branch_id; ?>">
                        <?php endif; ?>
                        <input type="hidden" name="trace_customer_name" value="<?php echo htmlspecialchars($trace_customer_name, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="export" value="trace_csv">
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Export CSV Jejak</button>
                    </form>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="h6">Pesanan</h3>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>ID Pesanan</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Penjualan terkait</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($customer_orders)): ?>
                                    <tr>
                                        <td colspan="4">Tidak ada pesanan untuk pelanggan ini pada periode tersebut.</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($customer_orders as $order): ?>
                                    <?php
                                    $orderId = (int)$order['id'];
                                    $salesForOrder = isset($customer_order_sales_map[$orderId]) ? $customer_order_sales_map[$orderId] : [];
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="pesanan.php?order_id=<?php echo $orderId; ?>">
                                                <?php echo $orderId; ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars(format_date_id($order['order_date']), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <?php if (empty($salesForOrder)): ?>
                                            Tidak ada
                                            <?php else: ?>
                                            <ul class="mb-0 ps-3">
                                                <?php foreach ($salesForOrder as $sale): ?>
                                                <li>
                                                    <a href="sales.php?sale_id=<?php echo (int)$sale['id']; ?>">
                                                        Penjualan #<?php echo (int)$sale['id']; ?>
                                                    </a>
                                                    pada
                                                    <?php echo htmlspecialchars(format_date_id($sale['sale_date']), ENT_QUOTES, 'UTF-8'); ?>
                                                </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h3 class="h6">Penjualan</h3>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>ID Penjualan</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($customer_sales)): ?>
                                    <tr>
                                        <td colspan="4">Tidak ada penjualan untuk pelanggan ini pada periode tersebut.</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($customer_sales as $sale): ?>
                                    <tr>
                                        <td>
                                            <a href="sales.php?sale_id=<?php echo (int)$sale['id']; ?>">
                                                <?php echo (int)$sale['id']; ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars(format_date_id($sale['sale_date']), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-end"><?php echo htmlspecialchars('Rp ' . number_format((float)$sale['total_amount'], 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($sale['notes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <p class="mb-0">Isi nama pelanggan lalu klik tombol "Lihat Jejak" untuk melihat hubungan pesanan dan penjualan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
