<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success !== ''): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<div class="row">
    <div class="col-12">
        <h1 class="h5 mb-3">Daftar Produk</h1>
        <div class="card mb-3">
            <div class="card-body">
                <form class="row g-2" method="get" action="products.php">
                    <div class="col-md-4">
                        <input type="text" name="q" class="form-control" placeholder="Cari kode/nama/barcode/PLU" value="<?php echo htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="all" <?php echo ($status ?? 'all') === 'all' ? 'selected' : ''; ?>>Semua status</option>
                            <option value="active" <?php echo ($status ?? 'all') === 'active' ? 'selected' : ''; ?>>Hanya aktif</option>
                            <option value="inactive" <?php echo ($status ?? 'all') === 'inactive' ? 'selected' : ''; ?>>Hanya nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-secondary w-100">Terapkan Filter</button>
                    </div>
                    <div class="col-md-3">
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="products.php" class="btn btn-outline-danger">Reset</a>
                            <button type="button" class="btn btn-primary ms-md-2" data-bs-toggle="modal" data-bs-target="#productModal">+ Produk</button>
                            <div class="btn-group ms-md-2 dropdown">
                                <button type="button" id="productColumnDropdownToggle" class="btn btn-outline-secondary dropdown-toggle">
                                    Kolom
                                </button>
                                <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 220px;">
                                    <div class="form-check">
                                        <input class="form-check-input col-toggle" type="checkbox" id="col_category" data-col="category" checked onclick="toggleProductColumn(this);">
                                        <label class="form-check-label" for="col_category">Kategori</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input col-toggle" type="checkbox" id="col_buy_price" data-col="buy_price" onclick="toggleProductColumn(this);">
                                        <label class="form-check-label" for="col_buy_price">Harga Beli</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input col-toggle" type="checkbox" id="col_sell_price" data-col="sell_price" checked onclick="toggleProductColumn(this);">
                                        <label class="form-check-label" for="col_sell_price">Harga Jual</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input col-toggle" type="checkbox" id="col_profit" data-col="profit" onclick="toggleProductColumn(this);">
                                        <label class="form-check-label" for="col_profit">% Profit</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input col-toggle" type="checkbox" id="col_stock_qty" data-col="stock_qty" checked onclick="toggleProductColumn(this);">
                                        <label class="form-check-label" for="col_stock_qty">Stok</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input col-toggle" type="checkbox" id="col_min_stock_qty" data-col="min_stock_qty" onclick="toggleProductColumn(this);">
                                        <label class="form-check-label" for="col_min_stock_qty">Min Stok</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input col-toggle" type="checkbox" id="col_unit" data-col="unit" checked onclick="toggleProductColumn(this);">
                                        <label class="form-check-label" for="col_unit">Satuan</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input col-toggle" type="checkbox" id="col_status" data-col="status" checked onclick="toggleProductColumn(this);">
                                        <label class="form-check-label" for="col_status">Status</label>
                                    </div>
                                    <div class="mt-2 pt-2 border-top">
                                        <button type="button" id="productColumnResetButton" class="btn btn-sm btn-outline-secondary w-100">
                                            Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <?php if (empty($products)): ?>
                    <p class="mb-0">Belum ada data produk.</p>
                <?php else: ?>
                <?php
                $filteredCount = count($products);
                $filterLabel = 'Semua status';
                if (($status ?? 'all') === 'active') {
                    $filterLabel = 'Hanya aktif';
                } elseif (($status ?? 'all') === 'inactive') {
                    $filterLabel = 'Hanya nonaktif';
                }
                ?>
                <p class="mb-3">
                    Jumlah produk sesuai filter (<?php echo htmlspecialchars($filterLabel, ENT_QUOTES, 'UTF-8'); ?>):
                    <?php echo htmlspecialchars((string)$filteredCount, ENT_QUOTES, 'UTF-8'); ?>
                    <?php if (isset($product_counts)): ?>
                    — seluruh: <?php echo htmlspecialchars((string)($product_counts['total_all'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>,
                    aktif: <?php echo htmlspecialchars((string)($product_counts['total_active'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>,
                    tidak aktif: <?php echo htmlspecialchars((string)($product_counts['total_inactive'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>
                    <?php endif; ?>
                </p>
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Nama</th>
                                <th class="text-end" data-col="buy_price">Harga Beli</th>
                                <th class="text-end" data-col="profit">% Profit</th>
                                <th class="text-end" data-col="sell_price">Harga Jual</th>
                                <th class="text-end" data-col="stock_qty">Stok</th>
                                <th class="text-end" data-col="min_stock_qty">Min Stok</th>
                                <th style="width: 80px;" data-col="unit">Satuan</th>
                                <th data-col="category">Kategori</th>
                                <th class="text-center" style="width: 80px;" data-col="status">Status</th>
                                <th style="width: 160px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($products as $p): ?>
                            <tr>
                                <td class="text-center"><?php echo htmlspecialchars((string)$no, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-end col-buy_price">
                                    <?php
                                    $buyRaw = $p['buy_price'] ?? null;
                                    if ($buyRaw !== null) {
                                        $buy = (float)$buyRaw;
                                        if (floor($buy) == $buy) {
                                            echo 'Rp ' . number_format($buy, 0, ',', '.');
                                        } else {
                                            $formattedBuy = rtrim(rtrim(number_format($buy, 2, ',', '.'), '0'), ',');
                                            echo 'Rp ' . $formattedBuy;
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="text-end col-profit">
                                    <?php
                                    $profitRaw = isset($p['profit_percent']) ? $p['profit_percent'] : null;
                                    if ($profitRaw !== null) {
                                        $profit = (float)$profitRaw;
                                        echo number_format($profit, 2, ',', '.') . '%';
                                    }
                                    ?>
                                </td>
                                <td class="text-end col-sell_price">
                                    <?php
                                    $sellRaw = $p['sell_price'] ?? null;
                                    if ($sellRaw !== null) {
                                        $sell = (float)$sellRaw;
                                        if (floor($sell) == $sell) {
                                            echo 'Rp ' . number_format($sell, 0, ',', '.');
                                        } else {
                                            $formattedSell = rtrim(rtrim(number_format($sell, 2, ',', '.'), '0'), ',');
                                            echo 'Rp ' . $formattedSell;
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="text-end col-stock_qty">
                                    <?php
                                    $qtyRaw = $p['stock_qty'] ?? null;
                                    if ($qtyRaw !== null) {
                                        $qty = (float)$qtyRaw;
                                        if (floor($qty) == $qty) {
                                            echo number_format($qty, 0, ',', '.');
                                        } else {
                                            $formattedQty = rtrim(rtrim(number_format($qty, 3, ',', '.'), '0'), ',');
                                            echo $formattedQty;
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="text-end col-min_stock_qty">
                                    <?php
                                    $minQtyRaw = $p['min_stock_qty'] ?? null;
                                    if ($minQtyRaw !== null) {
                                        $minQty = (float)$minQtyRaw;
                                        if (floor($minQty) == $minQty) {
                                            echo number_format($minQty, 0, ',', '.');
                                        } else {
                                            $formattedMinQty = rtrim(rtrim(number_format($minQty, 3, ',', '.'), '0'), ',');
                                            echo $formattedMinQty;
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="col-unit"><?php echo htmlspecialchars($p['unit'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="col-category"><?php echo htmlspecialchars($p['category_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-center col-status">
                                    <?php if ((int)$p['is_active'] === 1): ?>
                                        <span class="fw-bold status-label" data-status="1">A</span>
                                    <?php else: ?>
                                        <span class="fw-bold text-danger status-label" data-status="0">N</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="products.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                            ✎
                                        </a>
                                        <?php
                                        $isActive = (int)$p['is_active'] === 1;
                                        $btnClass = $isActive ? 'btn-outline-warning' : 'btn-outline-success';
                                        $btnTitle = $isActive ? 'Nonaktifkan' : 'Aktifkan';
                                        $btnText = $isActive ? '✖' : '✔';
                                        ?>
                                        <button
                                            type="button"
                                            class="btn btn-sm product-status-toggle <?php echo $btnClass; ?>"
                                            data-product-id="<?php echo (int)$p['id']; ?>"
                                            data-current-status="<?php echo $isActive ? '1' : '0'; ?>"
                                            title="<?php echo $btnTitle; ?>"
                                        >
                                            <?php echo $btnText; ?>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php $no++; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" autocomplete="off">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="product_id" value="0">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="unit" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-select">
                            <option value="">Pilih kategori</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo (int)$cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Beli</label>
                        <input
                            type="text"
                            id="product_buy_price"
                            class="form-control currency-input"
                            inputmode="decimal"
                            autocomplete="off"
                            required
                            data-currency-hidden="#product_buy_price_raw"
                        >
                        <input type="hidden" name="buy_price" id="product_buy_price_raw" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Jual</label>
                        <input
                            type="text"
                            id="product_sell_price"
                            class="form-control currency-input"
                            inputmode="decimal"
                            autocomplete="off"
                            required
                            data-currency-hidden="#product_sell_price_raw"
                        >
                        <input type="hidden" name="sell_price" id="product_sell_price_raw" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" step="0.01" min="0" name="stock_qty" class="form-control" value="0">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="modal_is_active" name="is_active" checked>
                        <label class="form-check-label" for="modal_is_active">
                            Aktif
                        </label>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php if ($edit_product): ?>
<div class="modal fade" id="productEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" autocomplete="off">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="product_id" value="<?php echo (int)$edit_product['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Kode Produk</label>
                        <input type="text" name="code" class="form-control" required value="<?php echo htmlspecialchars($edit_product['code'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($edit_product['name'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Barcode</label>
                        <input type="text" name="barcode" class="form-control" value="<?php echo isset($edit_product['barcode']) ? htmlspecialchars($edit_product['barcode'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PLU (opsional)</label>
                        <input type="text" name="plu_number" class="form-control" value="<?php echo isset($edit_product['plu_number']) && $edit_product['plu_number'] !== null ? htmlspecialchars($edit_product['plu_number'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <input type="text" id="category_search" class="form-control mb-1" placeholder="Cari kategori...">
                        <select name="category_id" id="category_id" class="form-select">
                            <option value="">Pilih kategori</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo (int)$cat['id']; ?>" <?php echo isset($edit_product['category_id']) && (int)$edit_product['category_id'] === (int)$cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="unit" class="form-control" required value="<?php echo htmlspecialchars($edit_product['unit'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Beli</label>
                        <input
                            type="text"
                            id="product_edit_buy_price"
                            class="form-control currency-input"
                            inputmode="decimal"
                            autocomplete="off"
                            required
                            data-currency-hidden="#product_edit_buy_price_raw"
                        >
                        <input
                            type="hidden"
                            name="buy_price"
                            id="product_edit_buy_price_raw"
                            value="<?php echo htmlspecialchars($edit_product['buy_price'], ENT_QUOTES, 'UTF-8'); ?>"
                        >
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Jual</label>
                        <input
                            type="text"
                            id="product_edit_sell_price"
                            class="form-control currency-input"
                            inputmode="decimal"
                            autocomplete="off"
                            required
                            data-currency-hidden="#product_edit_sell_price_raw"
                        >
                        <input
                            type="hidden"
                            name="sell_price"
                            id="product_edit_sell_price_raw"
                            value="<?php echo htmlspecialchars($edit_product['sell_price'], ENT_QUOTES, 'UTF-8'); ?>"
                        >
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" step="0.01" min="0" name="stock_qty" class="form-control" value="<?php echo isset($edit_product['stock_qty']) ? htmlspecialchars($edit_product['stock_qty'], ENT_QUOTES, 'UTF-8') : '0'; ?>">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" <?php echo (int)$edit_product['is_active'] === 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="edit_is_active">
                            Aktif
                        </label>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="products.php" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<script>
function toggleProductColumn(checkbox) {
    var $cb = $(checkbox);
    var col = $cb.data('col');
    var visible = $cb.is(':checked');
    $('th[data-col="' + col + '"], td.col-' + col).toggle(visible);
}
$(function () {
    if (typeof AppUtil !== 'undefined' && typeof AppUtil.setupSelectSearch === 'function') {
        AppUtil.setupSelectSearch('category_search', 'category_id');
    }
    var $checkboxes = $('.col-toggle');
    $checkboxes.each(function () {
        var $cb = $(this);
        $cb.attr('data-default-checked', $cb.is(':checked') ? '1' : '0');
        toggleProductColumn(this);
    });
    var $toggle = $('#productColumnDropdownToggle');
    if ($toggle.length) {
        var $parent = $toggle.closest('.dropdown');
        if ($parent.length) {
            var $menu = $parent.find('.dropdown-menu').first();
            if ($menu.length) {
                $toggle.on('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var isShown = $menu.hasClass('show');
                    $('.dropdown-menu.show').not($menu).removeClass('show');
                    if (!isShown) {
                        $menu.addClass('show');
                    }
                });
                $(document).on('click', function (e) {
                    if (!$parent.has(e.target).length) {
                        $menu.removeClass('show');
                    }
                });
            }
        }
    }
    var $resetButton = $('#productColumnResetButton');
    if ($resetButton.length) {
        $resetButton.on('click', function () {
            var $btn = $(this);
            $btn.prop('disabled', true);
            $('.col-toggle').each(function () {
                var $cb = $(this);
                var def = $cb.attr('data-default-checked') === '1';
                $cb.prop('checked', def);
                toggleProductColumn(this);
            });
            setTimeout(function () {
                $btn.prop('disabled', false);
            }, 150);
        });
    }

    if (typeof AppUtil !== 'undefined' && typeof AppUtil.handleStatusToggle === 'function') {
        AppUtil.handleStatusToggle({
            buttonSelector: '.product-status-toggle',
            confirmMessage: 'Apakah Anda yakin ingin mengubah status produk ini?',
            url: 'products.php',
            idDataAttribute: 'product-id',
            idParamName: 'product_id',
            defaultErrorMessage: 'Gagal mengubah status produk.',
            updateUI: function ($btn, newStatus) {
                $btn.removeClass('btn-outline-warning btn-outline-success');
                if (newStatus === 1) {
                    $btn.addClass('btn-outline-warning');
                    $btn.attr('title', 'Nonaktifkan');
                    $btn.text('✖');
                } else {
                    $btn.addClass('btn-outline-success');
                    $btn.attr('title', 'Aktifkan');
                    $btn.text('✔');
                }
                var $row = $btn.closest('tr');
                var $statusCell = $row.find('.col-status');
                if ($statusCell.length) {
                    if (newStatus === 1) {
                        $statusCell.html('<span class="fw-bold status-label" data-status="1">A</span>');
                    } else {
                        $statusCell.html('<span class="fw-bold text-danger status-label" data-status="0">N</span>');
                    }
                }
            }
        });
    }

    <?php if ($edit_product): ?>
    var $modalElement = $('#productEditModal');
    if ($modalElement.length && typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function') {
        var modal = new bootstrap.Modal($modalElement[0]);
        modal.show();
    }
    <?php endif; ?>

    if (typeof AppUtil !== 'undefined' && typeof AppUtil.handleLargeForm === 'function') {
        AppUtil.handleLargeForm({
            formSelector: '#productModal form',
            ajaxUrl: 'products.php',
            parseJson: true,
            beforeSubmit: function ($form) {
                if ($form.find('input[name="ajax"]').length === 0) {
                    $('<input type="hidden" name="ajax" value="1">').appendTo($form);
                }
                return true;
            },
            onSuccess: function (resp) {
                if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                    AppUtil.showToast(resp.message || 'Produk berhasil disimpan.', { type: 'success' });
                }
                var modalEl = document.getElementById('productModal');
                if (modalEl && typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function') {
                    var instance = bootstrap.Modal.getInstance(modalEl);
                    if (!instance) {
                        instance = new bootstrap.Modal(modalEl);
                    }
                    instance.hide();
                }
                window.location.reload();
            },
            onError: function (msg) {
                if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                    AppUtil.showToast(msg || 'Terjadi kesalahan saat menyimpan produk.', { type: 'error' });
                } else {
                    alert(msg);
                }
            }
        });

        AppUtil.handleLargeForm({
            formSelector: '#productEditModal form',
            ajaxUrl: 'products.php',
            parseJson: true,
            beforeSubmit: function ($form) {
                if ($form.find('input[name="ajax"]').length === 0) {
                    $('<input type="hidden" name="ajax" value="1">').appendTo($form);
                }
                return true;
            },
            onSuccess: function (resp) {
                if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                    AppUtil.showToast(resp.message || 'Produk berhasil disimpan.', { type: 'success' });
                }
                var modalEl = document.getElementById('productEditModal');
                if (modalEl && typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function') {
                    var instance = bootstrap.Modal.getInstance(modalEl);
                    if (!instance) {
                        instance = new bootstrap.Modal(modalEl);
                    }
                    instance.hide();
                }
                window.location.href = 'products.php';
            },
            onError: function (msg) {
                if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                    AppUtil.showToast(msg || 'Terjadi kesalahan saat menyimpan produk.', { type: 'error' });
                } else {
                    alert(msg);
                }
            }
        });
    }
});
</script>
