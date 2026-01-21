<div class="row">
    <div class="col-md-12">
        <h1 class="h3 mb-3">Laporan Pembelian</h1>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h6 mb-3">Filter Laporan</h2>
                <form method="get" class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal dari</label>
                        <div class="input-group input-group-sm">
                            <input
                                type="text"
                                name="start_date"
                                id="report_purchase_start_date"
                                class="form-control form-control-sm date-input"
                                inputmode="numeric"
                                pattern="\d{2}-\d{2}-\d{4}"
                                placeholder="dd-mm-yyyy"
                                value="<?php echo htmlspecialchars(format_date_id($filter_start_date), ENT_QUOTES, 'UTF-8'); ?>"
                                aria-label="Tanggal mulai laporan pembelian"
                                data-calendar-button="#btn_report_purchase_start_date"
                            >
                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                id="btn_report_purchase_start_date"
                                aria-label="Pilih tanggal mulai laporan pembelian"
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
                                id="report_purchase_end_date"
                                class="form-control form-control-sm date-input"
                                inputmode="numeric"
                                pattern="\d{2}-\d{2}-\d{4}"
                                placeholder="dd-mm-yyyy"
                                value="<?php echo htmlspecialchars(format_date_id($filter_end_date), ENT_QUOTES, 'UTF-8'); ?>"
                                aria-label="Tanggal selesai laporan pembelian"
                                data-calendar-button="#btn_report_purchase_end_date"
                            >
                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                id="btn_report_purchase_end_date"
                                aria-label="Pilih tanggal selesai laporan pembelian"
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
                        <label class="form-label">Pemasok</label>
                        <select name="supplier_id" class="form-select form-select-sm">
                            <option value="0">Semua pemasok</option>
                            <?php foreach ($suppliers_for_filter as $s): ?>
                            <option value="<?php echo (int)$s['id_orang']; ?>" <?php echo $supplier_filter_id === (int)$s['id_orang'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['nama_lengkap'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kategori produk</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="0">Semua kategori</option>
                            <?php foreach ($categories_for_filter as $c): ?>
                            <option value="<?php echo (int)$c['id']; ?>" <?php echo $category_filter_id === (int)$c['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Terapkan Filter</button>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="h6 mb-0">Ringkasan per pemasok</h3>
                            <form method="get" class="d-inline">
                                <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($filter_start_date, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($filter_end_date, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php if (isset($active_branch_id) && $active_branch_id): ?>
                                <input type="hidden" name="branch_id" value="<?php echo (int)$active_branch_id; ?>">
                                <?php endif; ?>
                                <input type="hidden" name="supplier_id" value="<?php echo (int)$supplier_filter_id; ?>">
                                <input type="hidden" name="category_id" value="<?php echo (int)$category_filter_id; ?>">
                                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="dir" value="<?php echo htmlspecialchars($dir, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="export" value="purchases_supplier_summary_csv">
                                <button type="submit" class="btn btn-outline-secondary btn-sm">Export</button>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Pemasok</th>
                                        <th class="text-end">Total Pembelian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $grandTotalSupplier = 0.0;
                                    ?>
                                    <?php if (empty($summary_by_supplier)): ?>
                                    <tr>
                                        <td colspan="2">Tidak ada data pembelian untuk filter ini.</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($summary_by_supplier as $row): ?>
                                    <?php
                                    $total = isset($row['total']) ? (float)$row['total'] : 0.0;
                                    $grandTotalSupplier += $total;
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['supplier_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-end">Rp <?php echo number_format($total, 2, ',', '.'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-end">Rp <?php echo number_format($grandTotalSupplier, 2, ',', '.'); ?></th>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="h6 mb-0">Ringkasan per kategori</h3>
                            <form method="get" class="d-inline">
                                <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($filter_start_date, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($filter_end_date, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php if (isset($active_branch_id) && $active_branch_id): ?>
                                <input type="hidden" name="branch_id" value="<?php echo (int)$active_branch_id; ?>">
                                <?php endif; ?>
                                <input type="hidden" name="supplier_id" value="<?php echo (int)$supplier_filter_id; ?>">
                                <input type="hidden" name="category_id" value="<?php echo (int)$category_filter_id; ?>">
                                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="dir" value="<?php echo htmlspecialchars($dir, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="export" value="purchases_category_summary_csv">
                                <button type="submit" class="btn btn-outline-secondary btn-sm">Export</button>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th class="text-end">Total Pembelian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $grandTotalCategory = 0.0;
                                    ?>
                                    <?php if (empty($summary_by_category)): ?>
                                    <tr>
                                        <td colspan="2">Tidak ada data pembelian per kategori untuk filter ini.</td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($summary_by_category as $row): ?>
                                    <?php
                                    $total = isset($row['total']) ? (float)$row['total'] : 0.0;
                                    $grandTotalCategory += $total;
                                    $name = $row['category_name'] !== null ? $row['category_name'] : 'Tanpa kategori';
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-end">Rp <?php echo number_format($total, 2, ',', '.'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-end">Rp <?php echo number_format($grandTotalCategory, 2, ',', '.'); ?></th>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <hr class="my-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 mb-0">Daftar pembelian</h2>
                    <form method="get" class="d-inline">
                        <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($filter_start_date, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($filter_end_date, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php if (isset($active_branch_id) && $active_branch_id): ?>
                        <input type="hidden" name="branch_id" value="<?php echo (int)$active_branch_id; ?>">
                        <?php endif; ?>
                        <input type="hidden" name="supplier_id" value="<?php echo (int)$supplier_filter_id; ?>">
                        <input type="hidden" name="category_id" value="<?php echo (int)$category_filter_id; ?>">
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="dir" value="<?php echo htmlspecialchars($dir, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="export" value="purchases_csv">
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Export CSV Pembelian</button>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <?php
                                $nextDirDate = ($sort === 'date' && $dir === 'asc') ? 'desc' : 'asc';
                                $nextDirSupplier = ($sort === 'supplier' && $dir === 'asc') ? 'desc' : 'asc';
                                $nextDirInvoice = ($sort === 'invoice' && $dir === 'asc') ? 'desc' : 'asc';
                                $nextDirTotal = ($sort === 'total' && $dir === 'asc') ? 'desc' : 'asc';
                                ?>
                                <th>
                                    <a href="?start_date=<?php echo htmlspecialchars($filter_start_date, ENT_QUOTES, 'UTF-8'); ?>&amp;end_date=<?php echo htmlspecialchars($filter_end_date, ENT_QUOTES, 'UTF-8'); ?><?php if (isset($active_branch_id) && $active_branch_id): ?>&amp;branch_id=<?php echo (int)$active_branch_id; ?><?php endif; ?>&amp;supplier_id=<?php echo (int)$supplier_filter_id; ?>&amp;category_id=<?php echo (int)$category_filter_id; ?>&amp;sort=date&amp;dir=<?php echo $nextDirDate; ?>">
                                        Tanggal
                                    </a>
                                </th>
                                <th>
                                    <a href="?start_date=<?php echo htmlspecialchars($filter_start_date, ENT_QUOTES, 'UTF-8'); ?>&amp;end_date=<?php echo htmlspecialchars($filter_end_date, ENT_QUOTES, 'UTF-8'); ?><?php if (isset($active_branch_id) && $active_branch_id): ?>&amp;branch_id=<?php echo (int)$active_branch_id; ?><?php endif; ?>&amp;supplier_id=<?php echo (int)$supplier_filter_id; ?>&amp;category_id=<?php echo (int)$category_filter_id; ?>&amp;sort=supplier&amp;dir=<?php echo $nextDirSupplier; ?>">
                                        Pemasok
                                    </a>
                                </th>
                                <th>
                                    <a href="?start_date=<?php echo htmlspecialchars($filter_start_date, ENT_QUOTES, 'UTF-8'); ?>&amp;end_date=<?php echo htmlspecialchars($filter_end_date, ENT_QUOTES, 'UTF-8'); ?><?php if (isset($active_branch_id) && $active_branch_id): ?>&amp;branch_id=<?php echo (int)$active_branch_id; ?><?php endif; ?>&amp;supplier_id=<?php echo (int)$supplier_filter_id; ?>&amp;category_id=<?php echo (int)$category_filter_id; ?>&amp;sort=invoice&amp;dir=<?php echo $nextDirInvoice; ?>">
                                        No. Faktur
                                    </a>
                                </th>
                                <th class="text-end">
                                    <a href="?start_date=<?php echo htmlspecialchars($filter_start_date, ENT_QUOTES, 'UTF-8'); ?>&amp;end_date=<?php echo htmlspecialchars($filter_end_date, ENT_QUOTES, 'UTF-8'); ?><?php if (isset($active_branch_id) && $active_branch_id): ?>&amp;branch_id=<?php echo (int)$active_branch_id; ?><?php endif; ?>&amp;supplier_id=<?php echo (int)$supplier_filter_id; ?>&amp;category_id=<?php echo (int)$category_filter_id; ?>&amp;sort=total&amp;dir=<?php echo $nextDirTotal; ?>">
                                        Total
                                    </a>
                                </th>
                                <th>Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($detail_purchases)): ?>
                            <tr>
                                <td colspan="5">Tidak ada pembelian pada periode dan filter ini.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($detail_purchases as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(format_date_id($row['purchase_date']), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['supplier_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['invoice_no'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-end">
                                    <?php
                                    $total = isset($row['total_amount']) ? (float)$row['total_amount'] : 0.0;
                                    echo htmlspecialchars('Rp ' . number_format($total, 2, ',', '.'), ENT_QUOTES, 'UTF-8');
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars(format_date_id($row['created_at']), ENT_QUOTES, 'UTF-8'); ?></td>
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
