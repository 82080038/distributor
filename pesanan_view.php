<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success !== ''): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<div class="row">
    <div class="col-md-4">
        <h1 class="h5 mb-3">Pesanan Baru</h1>
        <div class="card mb-3">
            <div class="card-body">
                <form method="post" autocomplete="off">
                    <input type="hidden" name="action" value="preview_pesanan">
                    <div class="mb-3">
                        <label class="form-label d-block">Pembeli</label>
                        <div class="mb-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="customer_type" id="order_customer_type_umum" value="umum" <?php echo $form_customer_type === 'umum' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="order_customer_type_umum">Pembeli Umum</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="customer_type" id="order_customer_type_pelanggan" value="pelanggan" <?php echo $form_customer_type !== 'umum' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="order_customer_type_pelanggan">Pelanggan</label>
                            </div>
                        </div>
                        <div id="order_customer_select_group">
                            <input
                                type="text"
                                class="form-control mb-1"
                                placeholder="Ketik untuk mencari..."
                                id="order_customer_search"
                                value="<?php echo htmlspecialchars($form_customer_name, ENT_QUOTES, 'UTF-8'); ?>"
                            >
                            <div class="d-flex gap-2 mb-1">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="order_customer_ajax_button">
                                    Cari ke server (AJAX)
                                </button>
                                <div class="form-text">
                                    Gunakan jika daftar pembeli sangat banyak.
                                </div>
                            </div>
                            <div class="input-group">
                                <select name="customer_id" id="order_customer_id" class="form-select">
                                    <option value="0">Pilih Pembeli</option>
                                    <?php foreach ($customers as $c): ?>
                                    <option value="<?php echo (int)$c['id_orang']; ?>" <?php echo $form_customer_id === (int)$c['id_orang'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['nama_lengkap'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#quickCustomerModal">
                                    Tambah
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="customer_name" id="order_customer_name" value="<?php echo htmlspecialchars($form_customer_name, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pesanan</label>
                        <div class="input-group">
                            <input
                                type="text"
                                name="order_date"
                                id="order_date"
                                class="form-control date-input"
                                required
                                inputmode="numeric"
                                pattern="\d{2}-\d{2}-\d{4}"
                                placeholder="dd-mm-yyyy"
                                value="<?php echo htmlspecialchars(format_date_id($form_order_date), ENT_QUOTES, 'UTF-8'); ?>"
                                aria-label="Tanggal pesanan"
                                data-calendar-button="#btn_order_date"
                            >
                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                id="btn_order_date"
                                aria-label="Pilih tanggal pesanan"
                            >
                                📅
                            </button>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Harus Dipenuhi</label>
                        <div class="input-group">
                            <input
                                type="text"
                                name="required_date"
                                id="required_date"
                                class="form-control date-input"
                                inputmode="numeric"
                                pattern="\d{2}-\d{2}-\d{4}"
                                placeholder="dd-mm-yyyy"
                                value="<?php echo htmlspecialchars(format_date_id($form_required_date), ENT_QUOTES, 'UTF-8'); ?>"
                                aria-label="Tanggal harus dipenuhi"
                                data-calendar-button="#btn_required_date"
                            >
                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                id="btn_required_date"
                                aria-label="Pilih tanggal harus dipenuhi"
                            >
                                📅
                            </button>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Paste pesanan dari Excel</label>
                        <textarea name="raw_pesanan" id="raw_pesanan" class="form-control" rows="6"><?php echo htmlspecialchars($preview_raw_pesanan ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" id="preview_pesanan_btn" class="btn btn-secondary">Preview dan Tambahkan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <h1 class="h5 mb-3">Daftar Pesanan Terakhir</h1>
        <div class="card mb-3">
            <div class="card-body">
                <?php if (empty($orders)): ?>
                    <p class="mb-0">Belum ada data pesanan.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal Pesanan</th>
                                <th>Pembeli</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(format_date_id($row['order_date']), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['customer_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php
                                    $status_text = (string)$row['status'];
                                    $status_lower = strtolower($status_text);
                                    $badge_class = 'bg-secondary';
                                    if ($status_lower === 'draft') {
                                        $badge_class = 'bg-secondary';
                                    } elseif ($status_lower === 'diproses') {
                                        $badge_class = 'bg-info text-dark';
                                    } elseif ($status_lower === 'selesai') {
                                        $badge_class = 'bg-success';
                                    } elseif ($status_lower === 'parsial') {
                                        $badge_class = 'bg-warning text-dark';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo htmlspecialchars($status_text, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(format_date_id($row['created_at']), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <a href="pesanan.php?order_id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-outline-primary">Pemenuhan</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if (!empty($preview_rows ?? [])): ?>
        <div class="card">
            <div class="card-body">
                <h2 class="h6 mb-3">Preview Pesanan</h2>
                <form id="savePesananForm" method="post" autocomplete="off">
                    <input type="hidden" name="action" id="pesanan_action" value="edit_pesanan">
                    <input type="hidden" name="customer_id" value="<?php echo (int)$form_customer_id; ?>">
                    <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($form_customer_name, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="customer_type" value="<?php echo htmlspecialchars($form_customer_type, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="order_date" value="<?php echo htmlspecialchars($form_order_date, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="required_date" value="<?php echo htmlspecialchars($form_required_date, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="raw_pesanan" value="<?php echo htmlspecialchars($preview_raw_pesanan ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="table-responsive mb-2">
                        <table class="table table-sm table-bordered align-middle" id="preview_rows_table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th>Uraian</th>
                                    <th style="width: 120px;">Qty</th>
                                    <th style="width: 120px;">Satuan</th>
                                    <th>Catatan</th>
                                    <th style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($preview_rows as $index => $r): ?>
                                <tr data-row-index="<?php echo (int)$index; ?>">
                                    <td>
                                        <input
                                            type="number"
                                            name="rows[<?php echo (int)$index; ?>][no]"
                                            class="form-control form-control-sm"
                                            value="<?php echo $r['no'] !== null ? (int)$r['no'] : ''; ?>"
                                            min="0"
                                            step="1"
                                        >
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input
                                                type="text"
                                                name="rows[<?php echo (int)$index; ?>][uraian]"
                                                class="form-control form-control-sm preview-row-uraian"
                                                value="<?php echo htmlspecialchars($r['uraian'], ENT_QUOTES, 'UTF-8'); ?>"
                                            >
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary btn-sm btn-pilih-produk"
                                                data-row-index="<?php echo (int)$index; ?>"
                                            >
                                                Pilih Produk
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            name="rows[<?php echo (int)$index; ?>][qty]"
                                            class="form-control form-control-sm text-end preview-row-qty"
                                            value="<?php echo htmlspecialchars((string)$r['qty'], ENT_QUOTES, 'UTF-8'); ?>"
                                        >
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            name="rows[<?php echo (int)$index; ?>][satuan]"
                                            class="form-control form-control-sm preview-row-satuan"
                                            value="<?php echo htmlspecialchars($r['satuan'], ENT_QUOTES, 'UTF-8'); ?>"
                                        >
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            name="rows[<?php echo (int)$index; ?>][catatan]"
                                            class="form-control form-control-sm preview-row-catatan"
                                            value="<?php echo htmlspecialchars($r['catatan'], ENT_QUOTES, 'UTF-8'); ?>"
                                        >
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-add-row">+</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">-</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('pesanan_action').value='edit_pesanan'">Simpan Perubahan</button>
                        <button type="submit" class="btn btn-success btn-sm" onclick="document.getElementById('pesanan_action').value='save_pesanan'">Simpan ke database</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($fulfillment_order)): ?>
        <div class="card mt-3">
            <div class="card-body">
                <h2 class="h6 mb-3">Pemenuhan Pesanan</h2>
                <p class="mb-2">
                    Tanggal: <?php echo htmlspecialchars(format_date_id($fulfillment_order['order_date']), ENT_QUOTES, 'UTF-8'); ?>,
                    Pembeli: <?php echo htmlspecialchars($fulfillment_order['customer_name'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <?php $canCreateSale = in_array($fulfillment_order['status'], ['diproses', 'parsial'], true); ?>
                <form method="post" autocomplete="off">
                    <input type="hidden" name="action" id="fulfillment_action" value="update_fulfillment">
                    <input type="hidden" name="order_id" value="<?php echo (int)$fulfillment_order['id']; ?>">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Status Pesanan</label>
                            <select name="order_status" class="form-select form-select-sm">
                                <option value="draft" <?php echo $fulfillment_order['status'] === 'draft' ? 'selected' : ''; ?>>draft</option>
                                <option value="diproses" <?php echo $fulfillment_order['status'] === 'diproses' ? 'selected' : ''; ?>>diproses</option>
                                <option value="selesai" <?php echo $fulfillment_order['status'] === 'selesai' ? 'selected' : ''; ?>>selesai</option>
                                <option value="parsial" <?php echo $fulfillment_order['status'] === 'parsial' ? 'selected' : ''; ?>>parsial</option>
                            </select>
                        </div>
                    </div>
                    <h3 class="h6 mt-2 mb-2">Mapping Item Pesanan ke Produk</h3>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th>Uraian</th>
                                    <th style="width: 140px;">Qty</th>
                                    <th style="width: 120px;">Satuan</th>
                                    <th style="width: 240px;">Produk</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fulfillment_items as $item): ?>
                                <tr>
                                    <td><?php echo (int)$item['seq_no']; ?></td>
                                    <td><?php echo htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-end"><?php echo htmlspecialchars((string)$item['qty'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($item['unit'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <select name="items[<?php echo (int)$item['id']; ?>][product_id]" class="form-select form-select-sm">
                                            <option value="">(Belum dipetakan)</option>
                                            <?php foreach ($fulfillment_products as $p): ?>
                                            <option value="<?php echo (int)$p['id']; ?>" <?php echo (int)$item['product_id'] === (int)$p['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($p['name'] . ' (' . $p['unit'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['notes'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <h3 class="h6 mt-2 mb-2">Ringkasan Stok vs Pesanan per Produk</h3>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Qty Pesanan</th>
                                    <th class="text-end">Stok Tersedia</th>
                                    <th class="text-end">Kekurangan</th>
                                    <th class="text-end">Sisa Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($fulfillment_summary)): ?>
                                <tr>
                                    <td colspan="5">Belum ada produk yang dipetakan pada pesanan ini.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($fulfillment_summary as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-end"><?php echo htmlspecialchars((string)$row['total_order_qty'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-end"><?php echo htmlspecialchars((string)$row['stock_qty'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-end"><?php echo htmlspecialchars((string)$row['shortage_qty'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td class="text-end"><?php echo htmlspecialchars((string)$row['leftover_qty'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary btn-sm" onclick="document.getElementById('fulfillment_action').value='update_fulfillment'">Simpan Pemenuhan</button>
                        <button type="submit" class="btn btn-success btn-sm" onclick="document.getElementById('fulfillment_action').value='create_sale_from_order'" <?php echo $canCreateSale ? '' : 'disabled'; ?>>Buat Penjualan dari Pesanan</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pembeli Cepat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" autocomplete="off">
                    <input type="hidden" name="action" value="quick_add_customer">
                    <div class="mb-3">
                        <label class="form-label">Nama Pembeli</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kontak</label>
                        <input type="text" name="kontak" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Pembeli</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="orderProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Produk untuk Uraian Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Cari Produk</label>
                    <input type="text" class="form-control mb-1" id="order_product_search" placeholder="Ketik untuk mencari...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Hasil</label>
                    <select id="order_product_id" class="form-select">
                        <option value="">Ketik untuk mencari...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-outline-primary" id="order_product_quick_add_button">Tambah Produk Cepat</button>
                <button type="button" class="btn btn-primary" id="order_product_use_button">Gunakan Produk</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="quickProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk Cepat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quick_product_form" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label">Kode Produk (opsional)</label>
                        <input type="text" name="code" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="unit" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Jual Standar</label>
                        <input type="number" step="0.01" min="0" name="sell_price" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="quick_product_is_active" name="is_active" checked>
                        <label class="form-check-label" for="quick_product_is_active">
                            Aktif
                        </label>
                    </div>
                    <div class="alert alert-danger d-none" id="quick_product_error"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="quick_product_save_button">Simpan Produk</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function () {
    var $customerSelect = $('#order_customer_id');
    var $customerSearch = $('#order_customer_search');
    var $customerNameHidden = $('#order_customer_name');
    var $customerTypeRadios = $('input[name="customer_type"]');
    var $customerGroup = $('#order_customer_select_group');

    function syncCustomerNameFromSelectAndInput() {
        if ($customerNameHidden.length === 0) {
            return;
        }
        var type = $customerTypeRadios.filter(':checked').val();
        if (type === 'umum') {
            $customerNameHidden.val('Pembeli Umum');
            return;
        }
        var selectedText = '';
        if ($customerSelect.length > 0) {
            var selectedOption = $customerSelect.find('option:selected');
            if (selectedOption.length > 0 && selectedOption.val() && selectedOption.val() !== '0') {
                selectedText = selectedOption.text();
            }
        }
        if (selectedText !== '') {
            $customerNameHidden.val(selectedText);
        } else if ($customerSearch.length > 0) {
            $customerNameHidden.val($customerSearch.val());
        }
    }

    if ($customerSelect.length > 0) {
        $customerSelect.on('change', function () {
            syncCustomerNameFromSelectAndInput();
        });
    }

    if ($customerSearch.length > 0) {
        $customerSearch.on('input', function () {
            if (!$customerSelect.length || !$customerSelect.val() || $customerSelect.val() === '0') {
                $customerNameHidden.val($customerSearch.val());
            }
            if ($customerSelect.length > 0) {
                var currentVal = $customerSelect.val();
                if (!currentVal || currentVal === '0') {
                    var firstValue = null;
                    $customerSelect.find('option').each(function (idx, el) {
                        if (idx === 0) {
                            return;
                        }
                        if (el.value && el.value !== '0') {
                            firstValue = el.value;
                            return false;
                        }
                    });
                    if (firstValue) {
                        $customerSelect.val(firstValue);
                    }
                }
            }
            syncCustomerNameFromSelectAndInput();
        });
    }

    function updateCustomerUI() {
        var type = $customerTypeRadios.filter(':checked').val();
        if (type === 'umum') {
            if ($customerGroup.length) {
                $customerGroup.hide();
            }
            if ($customerNameHidden.length) {
                $customerNameHidden.val('Pembeli Umum');
            }
        } else {
            if ($customerGroup.length) {
                $customerGroup.show();
            }
            syncCustomerNameFromSelectAndInput();
        }
    }

    if ($customerTypeRadios.length) {
        $customerTypeRadios.on('change', updateCustomerUI);
        updateCustomerUI();
    }

    if (typeof AppUtil !== 'undefined' && typeof AppUtil.setupSelectSearch === 'function') {
        AppUtil.setupSelectSearch('order_customer_search', 'order_customer_id', {
            onAfterRebuild: function () {
                syncCustomerNameFromSelectAndInput();
            }
        });
    }
    syncCustomerNameFromSelectAndInput();

    var $customerAjaxButton = $('#order_customer_ajax_button');

    if ($customerAjaxButton.length > 0 && $customerSearch.length > 0 && $customerSelect.length > 0) {
        $customerAjaxButton.on('click', function () {
            var term = $customerSearch.val();
            if (!term) {
                return;
            }
            $.getJSON('customers.php', {
                ajax: 'search_customers',
                q: term
            }).done(function (data) {
                $customerSelect.empty();
                $customerSelect.append('<option value="0">Pilih Pembeli</option>');
                if (Array.isArray(data)) {
                    $.each(data, function (index, item) {
                        var $opt = $('<option></option>');
                        $opt.val(item.id);
                        $opt.text(item.name);
                        $customerSelect.append($opt);
                    });
                    if (data.length > 0) {
                        $customerSelect.val(String(data[0].id));
                        $customerSelect.trigger('change');
                    }
                }
            });
        });
    }

    var $rawPesanan = $('#raw_pesanan');

    if ($rawPesanan.length > 0) {
        $rawPesanan.on('paste', function () {
            setTimeout(function () {
                var text = $.trim($rawPesanan.val());
                if (text === '') {
                    return;
                }
                var $previewTableEl = $('#preview_rows_table');
                var hasPreview = $previewTableEl.length > 0 && $previewTableEl.find('tbody tr').length > 0;
                if (!hasPreview) {
                    var $form = $rawPesanan.closest('form');
                    if ($form.length > 0) {
                        $form.trigger('submit');
                    }
                    return;
                }
                var isReset = window.confirm('Sudah ada data di Preview Pesanan.\n\nOK: Reset dan ganti dengan hasil paste Excel.\nCancel: Tambahkan hasil paste ke bawah Preview Pesanan.');
                if (isReset) {
                    var $formReset = $rawPesanan.closest('form');
                    if ($formReset.length > 0) {
                        $formReset.trigger('submit');
                    }
                } else {
                    var $previewForm = $previewTableEl.closest('form');
                    if ($previewForm.length > 0) {
                        $('#pesanan_action').val('append_from_excel');
                        var $appendInput = $previewForm.find('input[name="append_raw_pesanan"]');
                        if ($appendInput.length === 0) {
                            $appendInput = $('<input type="hidden" name="append_raw_pesanan">');
                            $previewForm.append($appendInput);
                        }
                        $appendInput.val(text);
                        $previewForm.trigger('submit');
                    }
                }
            }, 0);
        });
    }

    var $previewTable = $('#preview_rows_table');
    var currentProductRowIndex = null;

    function rebuildRowIndexes() {
        if (!$previewTable.length) {
            return;
        }
        var $rows = $previewTable.find('tbody tr');
        $rows.each(function (idx, row) {
            var $row = $(row);
            $row.attr('data-row-index', idx);
            $row.find('input[name^="rows["]').each(function (_, input) {
                var $input = $(input);
                var name = $input.attr('name');
                if (!name) {
                    return;
                }
                var newName = name.replace(/rows\[\d+\]/, 'rows[' + idx + ']');
                $input.attr('name', newName);
            });
            $row.find('.btn-pilih-produk').attr('data-row-index', idx);
        });
    }

    function addNewRow(afterRow) {
        if (!$previewTable.length) {
            return;
        }
        var $tbody = $previewTable.find('tbody');
        var index = $tbody.find('tr').length;
        var $newRow = $('<tr></tr>').attr('data-row-index', index);

        var noCell = $('<td></td>').append(
            $('<input>')
                .attr('type', 'number')
                .attr('name', 'rows[' + index + '][no]')
                .addClass('form-control form-control-sm')
                .attr('min', '0')
                .attr('step', '1')
        );

        var uraianInput = $('<input>')
            .attr('type', 'text')
            .attr('name', 'rows[' + index + '][uraian]')
            .addClass('form-control form-control-sm preview-row-uraian');
        var uraianCell = $('<td></td>').append(
            $('<div class="input-group input-group-sm"></div>')
                .append(uraianInput)
                .append(
                    $('<button type="button" class="btn btn-outline-secondary btn-sm btn-pilih-produk">Pilih Produk</button>')
                        .attr('data-row-index', index)
                )
        );

        var qtyCell = $('<td></td>').append(
            $('<input>')
                .attr('type', 'text')
                .attr('name', 'rows[' + index + '][qty]')
                .addClass('form-control form-control-sm text-end preview-row-qty')
        );

        var satuanCell = $('<td></td>').append(
            $('<input>')
                .attr('type', 'text')
                .attr('name', 'rows[' + index + '][satuan]')
                .addClass('form-control form-control-sm preview-row-satuan')
        );

        var catatanCell = $('<td></td>').append(
            $('<input>')
                .attr('type', 'text')
                .attr('name', 'rows[' + index + '][catatan]')
                .addClass('form-control form-control-sm preview-row-catatan')
        );

        var actionCell = $('<td class="text-center"></td>').append(
            $('<button type="button" class="btn btn-sm btn-outline-secondary btn-add-row">+</button>')
        ).append(' ').append(
            $('<button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">-</button>')
        );

        $newRow.append(noCell, uraianCell, qtyCell, satuanCell, catatanCell, actionCell);

        if (afterRow && afterRow.length) {
            afterRow.after($newRow);
        } else {
            $tbody.append($newRow);
        }
        rebuildRowIndexes();
    }

    if ($previewTable.length) {
        $previewTable.on('click', '.btn-add-row', function () {
            var $row = $(this).closest('tr');
            addNewRow($row);
        });

        $previewTable.on('click', '.btn-remove-row', function () {
            var $row = $(this).closest('tr');
            var $tbody = $previewTable.find('tbody');
            if ($tbody.find('tr').length > 1) {
                $row.remove();
                rebuildRowIndexes();
            } else {
                $row.find('input').val('');
            }
        });
    }

    var $productModal = $('#orderProductModal');
    var $productSearchInput = $('#order_product_search');
    var $productSelect = $('#order_product_id');
    var $quickProductModal = $('#quickProductModal');
    var $quickProductForm = $('#quick_product_form');
    var $quickProductError = $('#quick_product_error');
    var $quickProductSaveButton = $('#quick_product_save_button');
    var $quickProductOpenButton = $('#order_product_quick_add_button');

    if ($previewTable.length && $productModal.length && typeof AppUtil !== 'undefined' && typeof AppUtil.setupAjaxIncrementalSelect === 'function') {
        AppUtil.setupAjaxIncrementalSelect({
            inputSelector: '#order_product_search',
            selectSelector: '#order_product_id',
            ajaxUrl: 'products.php',
            ajaxParams: function (term) {
                return {
                    ajax: 'search_products',
                    q: term,
                    mode: 'sale'
                };
            },
            placeholderOptionText: 'Ketik untuk mencari...',
            maxResults: 10,
            delay: 300,
            optionBuilder: function (item) {
                var text = item.name + ' (' + item.unit + ')';
                if (item.plu_number) {
                    text += ' - PLU ' + item.plu_number;
                }
                var $opt = $('<option></option>');
                $opt.val(item.id);
                $opt.text(text);
                $opt.attr('data-name', item.name);
                $opt.attr('data-unit', item.unit);
                return $opt;
            },
            onAfterUpdate: function ($select, data) {
                if (Array.isArray(data) && data.length > 0) {
                    $select.val(String(data[0].id));
                }
            }
        });

        $previewTable.on('click', '.btn-pilih-produk', function () {
            var idx = $(this).attr('data-row-index');
            currentProductRowIndex = parseInt(idx, 10);
            if ($productSearchInput.length) {
                $productSearchInput.val('');
            }
            if ($productSelect.length) {
                $productSelect.empty();
                $productSelect.append('<option value=\"\">Ketik untuk mencari...</option>');
            }
            if (typeof bootstrap !== 'undefined') {
                var modal = new bootstrap.Modal($productModal[0]);
                modal.show();
            }
        });

        var uraianSuggestTimer = null;
        var $uraianSuggestBox = $('<div class="list-group position-absolute d-none" id="uraian_suggest_box"></div>');
        $('body').append($uraianSuggestBox);

        function hideUraianSuggestBox() {
            $uraianSuggestBox.addClass('d-none').empty();
        }

        function showUraianSuggestions($input, items, rowIndex) {
            if (!items || !items.length) {
                hideUraianSuggestBox();
                return;
            }
            var offset = $input.offset();
            var height = $input.outerHeight();
            $uraianSuggestBox.css({
                top: offset.top + height,
                left: offset.left,
                minWidth: $input.outerWidth()
            });
            $uraianSuggestBox.empty();
            $.each(items, function (idx, item) {
                var text = item.name + ' (' + item.unit + ')';
                if (item.plu_number) {
                    text += ' - PLU ' + item.plu_number;
                }
                var $a = $('<button type="button" class="list-group-item list-group-item-action"></button>');
                $a.text(text);
                $a.data('product', item);
                $a.data('row-index', rowIndex);
                $uraianSuggestBox.append($a);
            });
            $uraianSuggestBox.removeClass('d-none');
        }

        function fetchUraianSuggestions($input) {
            var term = $input.val();
            var $row = $input.closest('tr');
            var rowIndex = parseInt($row.attr('data-row-index'), 10);
            if (!term) {
                hideUraianSuggestBox();
                return;
            }
            $.getJSON('products.php', {
                ajax: 'search_products',
                q: term,
                mode: 'sale'
            }).done(function (data) {
                var list = Array.isArray(data) ? data.slice(0, 10) : [];
                showUraianSuggestions($input, list, rowIndex);
            });
        }

        $previewTable.on('input', '.preview-row-uraian', function () {
            var $input = $(this);
            if (uraianSuggestTimer) {
                clearTimeout(uraianSuggestTimer);
            }
            uraianSuggestTimer = setTimeout(function () {
                fetchUraianSuggestions($input);
            }, 300);
        });

        $previewTable.on('blur', '.preview-row-uraian', function () {
            setTimeout(function () {
                hideUraianSuggestBox();
            }, 200);
        });

        $uraianSuggestBox.on('mousedown', '.list-group-item', function (e) {
            e.preventDefault();
            var product = $(this).data('product');
            var rowIndex = $(this).data('row-index');
            var $rows = $previewTable.find('tbody tr');
            var $targetRow = $rows.eq(rowIndex);
            if ($targetRow.length) {
                if (product.name) {
                    $targetRow.find('.preview-row-uraian').val(product.name);
                }
                if (product.unit) {
                    $targetRow.find('.preview-row-satuan').val(product.unit);
                }
                var $qtyInput = $targetRow.find('.preview-row-qty');
                if ($qtyInput.val() === '') {
                    $qtyInput.val('1');
                }
                var currentIdx = $rows.index($targetRow);
                if (currentIdx !== -1) {
                    if (currentIdx === $rows.length - 1) {
                        addNewRow($targetRow);
                        $rows = $previewTable.find('tbody tr');
                        currentIdx = $rows.index($targetRow);
                    }
                    var $nextRow = $rows.eq(currentIdx + 1);
                    if ($nextRow.length) {
                        $nextRow.find('.preview-row-uraian').focus();
                    }
                }
            }
            hideUraianSuggestBox();
        });

        $('#order_product_use_button').on('click', function () {
            if (currentProductRowIndex === null) {
                return;
            }
            var $selected = $productSelect.find('option:selected');
            if ($selected.length === 0 || !$selected.val()) {
                return;
            }
            var name = $selected.attr('data-name') || $selected.text();
            var unit = $selected.attr('data-unit') || '';
            var $rows = $previewTable.find('tbody tr');
            var $targetRow = $rows.eq(currentProductRowIndex);
            if ($targetRow.length) {
                $targetRow.find('.preview-row-uraian').val(name);
                if (unit) {
                    $targetRow.find('.preview-row-satuan').val(unit);
                }
                var $qtyInput = $targetRow.find('.preview-row-qty');
                if ($qtyInput.val() === '') {
                    $qtyInput.val('1');
                }
                var currentIdx = $rows.index($targetRow);
                if (currentIdx !== -1) {
                    if (currentIdx === $rows.length - 1) {
                        addNewRow($targetRow);
                        $rows = $previewTable.find('tbody tr');
                        currentIdx = $rows.index($targetRow);
                    }
                    var $nextRow = $rows.eq(currentIdx + 1);
                    if ($nextRow.length) {
                        $nextRow.find('.preview-row-uraian').focus();
                    }
                }
            }
            if (typeof bootstrap !== 'undefined') {
                var modal = bootstrap.Modal.getInstance($productModal[0]);
                if (modal) {
                    modal.hide();
                }
            }
        });

        if ($quickProductOpenButton.length && $quickProductModal.length) {
            $quickProductOpenButton.on('click', function () {
                if (typeof bootstrap === 'undefined') {
                    return;
                }
                if ($quickProductForm.length) {
                    $quickProductForm[0].reset();
                }
                if ($quickProductError.length) {
                    $quickProductError.addClass('d-none').text('');
                }
                var productModalInstance = bootstrap.Modal.getInstance($productModal[0]);
                if (productModalInstance) {
                    productModalInstance.hide();
                }
                var quickModalInstance = new bootstrap.Modal($quickProductModal[0]);
                quickModalInstance.show();
            });
        }

        if ($quickProductSaveButton.length && $quickProductForm.length && $quickProductModal.length) {
            $quickProductSaveButton.on('click', function () {
                var formEl = $quickProductForm[0];
                if (!formEl.checkValidity()) {
                    formEl.reportValidity();
                    return;
                }
                var formArray = $quickProductForm.serializeArray();
                var payload = { action: 'quick_add_product' };
                $.each(formArray, function (_, item) {
                    payload[item.name] = item.value;
                });
                if (!Object.prototype.hasOwnProperty.call(payload, 'is_active')) {
                    payload.is_active = '0';
                }
                $.post('products.php', payload, function (data) {
                    if (!data || data.success !== true) {
                        if ($quickProductError.length) {
                            var msg = data && data.error ? data.error : 'Gagal menyimpan produk baru.';
                            $quickProductError.removeClass('d-none').text(msg);
                        }
                        return;
                    }
                    if ($quickProductError.length) {
                        $quickProductError.addClass('d-none').text('');
                    }
                    var product = data.product || null;
                    if (currentProductRowIndex !== null && product) {
                        var $targetRow = $previewTable.find('tbody tr').eq(currentProductRowIndex);
                        if ($targetRow.length) {
                            if (product.name) {
                                $targetRow.find('.preview-row-uraian').val(product.name);
                            }
                            if (product.unit) {
                                $targetRow.find('.preview-row-satuan').val(product.unit);
                            }
                            var $qtyInput = $targetRow.find('.preview-row-qty');
                            if ($qtyInput.val() === '') {
                                $qtyInput.val('1');
                            }
                        }
                    }
                    if (typeof bootstrap !== 'undefined') {
                        var quickModalInstance = bootstrap.Modal.getInstance($quickProductModal[0]);
                        if (quickModalInstance) {
                            quickModalInstance.hide();
                        }
                    }
                }, 'json').fail(function () {
                    if ($quickProductError.length) {
                        $quickProductError.removeClass('d-none').text('Terjadi kesalahan saat menyimpan produk baru.');
                    }
                });
            });
        });
    }

    var $savePesananForm = $('#savePesananForm');

    if (typeof AppUtil !== 'undefined' && typeof AppUtil.handleLargeForm === 'function') {
        if ($savePesananForm.length) {
            AppUtil.handleLargeForm({
                formSelector: '#savePesananForm',
                ajaxUrl: 'pesanan.php',
                parseJson: true,
                beforeSubmit: function ($form) {
                    if ($form.find('input[name="ajax"]').length === 0) {
                        $('<input type="hidden" name="ajax" value="1">').appendTo($form);
                    }
                    return true;
                },
                onSuccess: function (resp) {
                    if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                        AppUtil.showToast(resp.message || 'Pesanan berhasil disimpan ke database.', { type: 'success' });
                    }
                    window.location.href = 'pesanan.php';
                },
                onError: function (msg) {
                    if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                        AppUtil.showToast(msg || 'Terjadi kesalahan saat menyimpan pesanan.', { type: 'error' });
                    }
                }
            });
        }

        var $quickCustomerForm = $('#quickCustomerModal form');
        if ($quickCustomerForm.length) {
            AppUtil.handleLargeForm({
                formSelector: '#quickCustomerModal form',
                ajaxUrl: 'pesanan.php',
                parseJson: true,
                beforeSubmit: function ($form) {
                    if ($form.find('input[name="ajax"]').length === 0) {
                        $('<input type="hidden" name="ajax" value="1">').appendTo($form);
                    }
                    return true;
                },
                onSuccess: function (resp) {
                    if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                        AppUtil.showToast(resp.message || 'Pembeli baru berhasil ditambahkan.', { type: 'success' });
                    }
                    var modalEl = document.getElementById('quickCustomerModal');
                    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                    }
                    if (resp.customer && resp.customer.id && resp.customer.name && $customerSelect.length) {
                        var idStr = String(resp.customer.id);
                        var exists = false;
                        $customerSelect.find('option').each(function () {
                            if ($(this).val() === idStr) {
                                exists = true;
                                return false;
                            }
                        });
                        if (!exists) {
                            var $opt = $('<option></option>');
                            $opt.val(idStr);
                            $opt.text(resp.customer.name);
                            $customerSelect.append($opt);
                        }
                        $customerSelect.val(idStr);
                        if ($customerTypeRadios.length) {
                            $customerTypeRadios.filter('[value="pelanggan"]').prop('checked', true);
                            updateCustomerUI();
                        } else {
                            syncCustomerNameFromSelectAndInput();
                        }
                    }
                },
                onError: function (msg) {
                    if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                        AppUtil.showToast(msg || 'Terjadi kesalahan saat menambahkan pembeli.', { type: 'error' });
                    }
                }
            });
        }
    }
});
</script>
