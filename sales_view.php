<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success !== ''): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<div class="row">
    <div class="col-md-4">
        <h1 class="h5 mb-3">Transaksi Penjualan Baru</h1>
        <div class="card mb-3">
            <div class="card-body">
                <form id="saleForm" method="post" autocomplete="off">
                    <input type="hidden" name="action" value="save">
                    <div class="mb-3">
                        <label class="form-label d-block">Pembeli</label>
                        <div class="mb-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="customer_type" id="customer_type_umum" value="umum" <?php echo $form_customer_type === 'umum' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="customer_type_umum">Pembeli Umum</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="customer_type" id="customer_type_pelanggan" value="pelanggan" <?php echo $form_customer_type !== 'umum' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="customer_type_pelanggan">Pelanggan</label>
                            </div>
                        </div>
                        <div id="sale_customer_select_group">
                            <input type="text" class="form-control mb-1" placeholder="Ketik untuk mencari..." id="customer_search">
                            <div class="d-flex gap-2 mb-1">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="sale_customer_ajax_button">
                                    Cari ke server (AJAX)
                                </button>
                                <div class="form-text">
                                    Gunakan jika daftar pembeli sangat banyak.
                                </div>
                            </div>
                            <div class="input-group">
                                <select name="customer_id" id="customer_id" class="form-select" required>
                                    <option value="">Pilih Pembeli</option>
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
                            <div class="invalid-feedback d-block" id="customer_type_error"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Faktur</label>
                        <input type="text" name="invoice_no" class="form-control" value="<?php echo htmlspecialchars($form_invoice_no, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Penjualan</label>
                        <div class="input-group">
                            <input
                                type="text"
                                name="sale_date"
                                id="sale_date"
                                class="form-control date-input"
                                required
                                inputmode="numeric"
                                pattern="\d{2}-\d{2}-\d{4}"
                                placeholder="dd-mm-yyyy"
                                value="<?php echo htmlspecialchars(format_date_id($form_sale_date), ENT_QUOTES, 'UTF-8'); ?>"
                                aria-label="Tanggal penjualan"
                                data-calendar-button="#btn_sale_date"
                            >
                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                id="btn_sale_date"
                                aria-label="Pilih tanggal penjualan"
                            >
                                📅
                            </button>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Produk</label>
                        <input type="text" class="form-control mb-1" placeholder="Ketik untuk mencari..." id="sale_product_search">
                        <div class="d-flex gap-2 mb-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="sale_product_ajax_button">
                                Cari ke server (AJAX)
                            </button>
                            <div class="form-text">
                                Gunakan jika daftar produk sangat banyak.
                            </div>
                        </div>
                        <select name="product_id" id="sale_product_id" class="form-select" required>
                            <option value="">Pilih Produk</option>
                            <?php foreach ($products as $p): ?>
                            <option
                                value="<?php echo (int)$p['id']; ?>"
                                data-barcode="<?php echo htmlspecialchars($p['barcode'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                data-price="<?php echo htmlspecialchars($p['sell_price'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo $form_product_id === (int)$p['id'] ? 'selected' : ''; ?>
                            >
                                <?php
                                $text = $p['name'] . ' (' . $p['unit'] . ')';
                                if (isset($p['plu_number']) && $p['plu_number'] !== null && $p['plu_number'] !== '') {
                                    $text .= ' - PLU ' . $p['plu_number'];
                                }
                                echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
                                ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="qty" class="form-control" min="0" step="0.001" required value="<?php echo htmlspecialchars($form_qty, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Satuan</label>
                        <input
                            type="text"
                            id="sale_price"
                            class="form-control currency-input"
                            inputmode="decimal"
                            autocomplete="off"
                            required
                            data-currency-hidden="#sale_price_raw"
                        >
                        <input type="hidden" name="price" id="sale_price_raw" value="<?php echo htmlspecialchars($form_price, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2"><?php echo htmlspecialchars($form_notes, ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Simpan Penjualan</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="alert alert-info">
            Hanya pembeli dan produk dengan status aktif yang dapat dipilih.
        </div>
    </div>
    <div class="col-md-8">
        <h1 class="h5 mb-3">Daftar Penjualan Terakhir</h1>
        <?php if (!empty($selected_sale)): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h6 mb-3">Detail Penjualan Terpilih</h2>
                <p class="mb-1">
                    Tanggal: <?php echo htmlspecialchars(format_date_id($selected_sale['sale_date']), ENT_QUOTES, 'UTF-8'); ?>,
                    Pembeli: <?php echo htmlspecialchars($selected_sale['customer_name'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <p class="mb-2">
                    No. Faktur: <?php echo htmlspecialchars($selected_sale['invoice_no'], ENT_QUOTES, 'UTF-8'); ?><br>
                    Total: Rp <?php echo number_format((float)$selected_sale['total_amount'], 2, ',', '.'); ?><br>
                    <small>Terbilang: <?php echo htmlspecialchars(number_to_indonesian_words((float)$selected_sale['total_amount']), ENT_QUOTES, 'UTF-8'); ?></small>
                </p>
                <?php if (!empty($selected_sale['notes'])): ?>
                <p class="mb-2">
                    Catatan: <?php echo htmlspecialchars($selected_sale['notes'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($selected_sale_items)): ?>
                            <tr>
                                <td colspan="4">Belum ada item untuk penjualan ini.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($selected_sale_items as $item): ?>
                            <tr>
                                <td>
                                    <?php
                                    $name = $item['product_name'] ?? '';
                                    $unit = $item['unit'] ?? '';
                                    if ($unit !== '') {
                                        $name .= ' (' . $unit . ')';
                                    }
                                    echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
                                    ?>
                                </td>
                                <td class="text-end"><?php echo htmlspecialchars((string)$item['qty'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-end">Rp <?php echo number_format((float)$item['price'], 2, ',', '.'); ?></td>
                                <td class="text-end">Rp <?php echo number_format((float)$item['subtotal'], 2, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-body">
                <?php if (empty($sales_list)): ?>
                    <p class="mb-0">Belum ada data penjualan.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Faktur</th>
                                <th>Pembeli</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales_list as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(format_date_id($row['sale_date']), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['invoice_no'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['customer_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-end">Rp <?php echo number_format((float)$row['total_amount'], 2, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
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
<script>
$(function () {
    var $saleCustomerSelect = $('#customer_id');
    var $saleCustomerSearch = $('#customer_search');
    var $saleProductSelect = $('#sale_product_id');
    var $salePriceInput = $('#sale_price');
    var $salePriceRawInput = $('#sale_price_raw');

    function syncSalePriceFromProduct() {
        if ($saleProductSelect.length === 0 || $salePriceInput.length === 0) {
            return;
        }
        var $selectedOption = $saleProductSelect.find('option:selected');
        if ($selectedOption.length === 0) {
            return;
        }
        var price = $selectedOption.attr('data-price');
        var currentVal = $salePriceInput.val();
        if (price === undefined || price === '') {
            return;
        }
        var shouldUpdate = false;
        if (currentVal === '') {
            shouldUpdate = true;
        } else if (typeof AppUtil !== 'undefined' && typeof AppUtil.parseCurrencyIndo === 'function') {
            var currentNum = AppUtil.parseCurrencyIndo(currentVal);
            if (!isFinite(currentNum) || currentNum === 0) {
                shouldUpdate = true;
            }
        }
        if (!shouldUpdate) {
            return;
        }
        var numericPrice = parseFloat(String(price).replace(',', '.'));
        if (!isNaN(numericPrice) && isFinite(numericPrice)) {
            if (typeof AppUtil !== 'undefined' && typeof AppUtil.setCurrencyInputValue === 'function') {
                AppUtil.setCurrencyInputValue($salePriceInput[0], numericPrice);
            } else {
                $salePriceInput.val(String(numericPrice));
                if ($salePriceRawInput.length) {
                    $salePriceRawInput.val(String(numericPrice));
                }
            }
        }
    }

    if ($saleProductSelect.length > 0 && $salePriceInput.length > 0) {
        $saleProductSelect.on('change', syncSalePriceFromProduct);
        syncSalePriceFromProduct();
    }

    if (typeof AppUtil !== 'undefined' && typeof AppUtil.setupAjaxIncrementalSelect === 'function') {
        if ($saleCustomerSelect.length > 0 && $saleCustomerSearch.length > 0) {
            AppUtil.setupAjaxIncrementalSelect({
                inputSelector: '#customer_search',
                selectSelector: '#customer_id',
                ajaxUrl: 'customers.php',
                ajaxParams: function (term) {
                    return {
                        ajax: 'search_customers',
                        q: term
                    };
                },
                placeholderOptionText: 'Ketik untuk mencari...',
                maxResults: 10,
                delay: 300,
                optionBuilder: function (item) {
                    var $opt = $('<option></option>');
                    $opt.val(item.id);
                    $opt.text(item.name);
                    return $opt;
                },
                onAfterUpdate: function ($select, data) {
                    if (Array.isArray(data) && data.length > 0) {
                        $select.val(String(data[0].id));
                    }
                }
            });
        }

        if ($saleProductSelect.length > 0) {
            AppUtil.setupAjaxIncrementalSelect({
                inputSelector: '#sale_product_search',
                selectSelector: '#sale_product_id',
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
                    if (item.barcode) {
                        $opt.attr('data-barcode', item.barcode);
                    }
                    if (item.price !== undefined && item.price !== null) {
                        $opt.attr('data-price', item.price);
                    }
                    return $opt;
                },
                onAfterUpdate: function ($select, data) {
                    if (Array.isArray(data) && data.length === 1) {
                        $select.val(String(data[0].id));
                        $select.trigger('change');
                    }
                }
            });
        }
    }

    var $saleAjaxButton = $('#sale_product_ajax_button');
    var $saleSearchInput = $('#sale_product_search');
    var $saleCustomerAjaxButton = $('#sale_customer_ajax_button');

    if ($saleAjaxButton.length > 0 && $saleSearchInput.length > 0 && $saleProductSelect.length > 0) {
        $saleAjaxButton.on('click', function () {
            var term = $saleSearchInput.val();
            if (!term) {
                return;
            }
            $.getJSON('products.php', {
                ajax: 'search_products',
                q: term,
                mode: 'sale'
            }).done(function (data) {
                $saleProductSelect.empty();
                $saleProductSelect.append('<option value="">Pilih Produk</option>');
                if (Array.isArray(data)) {
                    var count = 0;
                    $.each(data, function (index, item) {
                        if (count >= 10) {
                            return false;
                        }
                        var text = item.name + ' (' + item.unit + ')';
                        if (item.plu_number) {
                            text += ' - PLU ' + item.plu_number;
                        }
                        var $opt = $('<option></option>');
                        $opt.val(item.id);
                        $opt.text(text);
                        if (item.barcode) {
                            $opt.attr('data-barcode', item.barcode);
                        }
                        if (item.price !== undefined && item.price !== null) {
                            $opt.attr('data-price', item.price);
                        }
                        $saleProductSelect.append($opt);
                        count++;
                    });
                    if (data.length === 1) {
                        $saleProductSelect.val(String(data[0].id));
                        $saleProductSelect.trigger('change');
                    }
                }
            });
        });
    }

    if ($saleCustomerAjaxButton.length > 0 && $saleCustomerSearch.length > 0 && $saleCustomerSelect.length > 0) {
        $saleCustomerAjaxButton.on('click', function () {
            var term = $saleCustomerSearch.val();
            if (!term) {
                return;
            }
            $.getJSON('customers.php', {
                ajax: 'search_customers',
                q: term
            }).done(function (data) {
                $saleCustomerSelect.empty();
                $saleCustomerSelect.append('<option value="">Pilih Pembeli</option>');
                if (Array.isArray(data)) {
                    var count = 0;
                    $.each(data, function (index, item) {
                        if (count >= 10) {
                            return false;
                        }
                        var $opt = $('<option></option>');
                        $opt.val(item.id);
                        $opt.text(item.name);
                        $saleCustomerSelect.append($opt);
                        count++;
                    });
                    if (data.length > 0) {
                        $saleCustomerSelect.val(String(data[0].id));
                    }
                }
            });
        });
    }

    var $saleForm = $('#saleForm');
    var $submitButton = $saleForm.length ? $saleForm.find('button[type="submit"]').first() : $();
    var $customerTypeRadios = $('input[name="customer_type"]');
    var $customerGroup = $('#sale_customer_select_group');
    var $customerError = $('#customer_type_error');

    function validateCustomerSelection() {
        var type = $customerTypeRadios.filter(':checked').val();
        var isValid = true;
        if (type === 'pelanggan') {
            if (!$saleCustomerSelect.val()) {
                isValid = false;
                if ($customerError.length) {
                    $customerError.text('Silakan pilih pelanggan terlebih dahulu.');
                }
            } else if ($customerError.length) {
                $customerError.text('');
            }
        } else if ($customerError.length) {
            $customerError.text('');
        }
        if ($submitButton.length) {
            $submitButton.prop('disabled', !isValid);
        }
    }

    function updateCustomerUI() {
        var type = $customerTypeRadios.filter(':checked').val();
        if (type === 'umum') {
            $customerGroup.hide();
            $saleCustomerSelect.prop('disabled', true);
        } else {
            $customerGroup.show();
            $saleCustomerSelect.prop('disabled', false);
        }
        validateCustomerSelection();
    }

    if ($customerTypeRadios.length && $customerGroup.length && $saleCustomerSelect.length) {
        $customerTypeRadios.on('change', updateCustomerUI);
        $saleCustomerSelect.on('change', validateCustomerSelection);
        $saleForm.on('submit', function (e) {
            var type = $customerTypeRadios.filter(':checked').val();
            if (type === 'pelanggan' && !$saleCustomerSelect.val()) {
                e.preventDefault();
                validateCustomerSelection();
                $saleCustomerSelect.focus();
            }
        });
        updateCustomerUI();
    }

    if (typeof AppUtil !== 'undefined' && typeof AppUtil.handleLargeForm === 'function') {
        if ($saleForm.length) {
            AppUtil.handleLargeForm({
                formSelector: '#saleForm',
                ajaxUrl: 'sales.php',
                parseJson: true,
                beforeSubmit: function ($form) {
                    var type = $customerTypeRadios.filter(':checked').val();
                    if (type === 'pelanggan' && !$saleCustomerSelect.val()) {
                        validateCustomerSelection();
                        $saleCustomerSelect.focus();
                        return false;
                    }
                    if ($form.find('input[name="ajax"]').length === 0) {
                        $('<input type="hidden" name="ajax" value="1">').appendTo($form);
                    }
                    return true;
                },
                onSuccess: function (resp) {
                    if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                        AppUtil.showToast(resp.message || 'Transaksi penjualan berhasil disimpan.', { type: 'success' });
                    }
                    window.location.href = 'sales.php';
                },
                onError: function (msg) {
                    if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                        AppUtil.showToast(msg || 'Terjadi kesalahan saat menyimpan transaksi penjualan.', { type: 'error' });
                    }
                }
            });
        }

        var $quickCustomerForm = $('#quickCustomerModal form');
        if ($quickCustomerForm.length) {
            AppUtil.handleLargeForm({
                formSelector: '#quickCustomerModal form',
                ajaxUrl: 'sales.php',
                parseJson: true,
                beforeSubmit: function ($form) {
                    if ($form.find('input[name="ajax"]').length === 0) {
                        $('<input type="hidden" name="ajax" value="1">').appendTo($form);
                    }
                    return true;
                },
                onSuccess: function (resp) {
                    if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                        AppUtil.showToast(resp.message || 'Pembeli berhasil ditambahkan.', { type: 'success' });
                    }
                    var modalEl = document.getElementById('quickCustomerModal');
                    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                    }
                    if ($saleCustomerSearch && $saleCustomerSearch.length) {
                        $saleCustomerSearch.trigger('input');
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
