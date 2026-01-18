<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success !== ''): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<div class="row">
    <div class="col-12 col-md-12 d-none" id="purchaseFormCol">
        <h1 class="h5 mb-3">
            Transaksi Pembelian
            <span class="badge bg-secondary ms-2" id="purchase_mode_badge">Baru</span>
        </h1>
        <div class="card mb-3">
            <div class="card-body">
                <form id="purchaseForm" method="post" autocomplete="off">
                    <input type="hidden" name="action" id="purchase_action" value="save">
                    <input type="hidden" name="purchase_id" id="purchase_id" value="">
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label">Pemasok</label>
                            <div class="input-group">
                                <select name="supplier_id" id="supplier_id" class="form-select" required>
                                    <option value="">Pilih Pemasok</option>
                                    <?php foreach ($suppliers as $s): ?>
                                    <option value="<?php echo (int)$s['id_orang']; ?>" <?php echo $form_supplier_id === (int)$s['id_orang'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($s['nama_lengkap'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#quickSupplierModal">
                                    Tambah
                                </button>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label">No. Faktur Supplier</label>
                            <input type="text" name="supplier_invoice_no" class="form-control" value="<?php echo htmlspecialchars($form_supplier_invoice_no ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label class="form-label">Tanggal Pembelian</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            name="purchase_date"
                                            id="purchase_date"
                                            class="form-control date-input"
                                            required
                                            inputmode="numeric"
                                            pattern="\d{2}-\d{2}-\d{4}"
                                            placeholder="dd-mm-yyyy"
                                            value="<?php echo htmlspecialchars(format_date_id($form_purchase_date), ENT_QUOTES, 'UTF-8'); ?>"
                                            aria-label="Tanggal pembelian"
                                            data-calendar-button="#btn_purchase_date"
                                        >
                                        <button
                                            class="btn btn-outline-secondary"
                                            type="button"
                                            id="btn_purchase_date"
                                            aria-label="Pilih tanggal pembelian"
                                        >
                                            📅
                                        </button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text">Jam</span>
                                        <input
                                            type="time"
                                            name="purchase_time"
                                            id="purchase_time"
                                            class="form-control"
                                            step="60"
                                            value="<?php echo htmlspecialchars($form_purchase_time ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                            aria-label="Jam pembelian (HH:mm)"
                                        >
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label">Produk</label>
                            <div class="input-group">
                                <select name="product_id" id="purchase_product_id" class="form-select">
                                    <option value="">Pilih Produk</option>
                                    <?php foreach ($products as $p): ?>
                                    <option
                                        value="<?php echo (int)$p['id']; ?>"
                                        data-unit="<?php echo htmlspecialchars($p['unit'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        data-barcode="<?php echo htmlspecialchars($p['barcode'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        data-price="<?php echo htmlspecialchars($p['buy_price'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
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
                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#quickProductModal"
                                    id="purchase_quick_product_button"
                                >
                                    Tambah
                                </button>
                            </div>
                            <div class="invalid-feedback" id="purchase_product_invalid_feedback"></div>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label">Harga Satuan</label>
                            <input
                                type="text"
                                id="purchase_price"
                                class="form-control currency-input"
                                inputmode="decimal"
                                autocomplete="off"
                                data-currency-hidden="#purchase_price_raw"
                            >
                            <input type="hidden" name="price" id="purchase_price_raw" value="<?php echo htmlspecialchars($form_price, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="qty" id="purchase_qty" class="form-control" min="0" step="1" value="<?php echo htmlspecialchars($form_qty, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            Daftar Item Pembelian
                            <span id="purchaseItemsCount" class="text-muted">(0 item.)</span>
                        </label>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle" id="purchaseItemsTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 40px;">#</th>
                                        <th>Produk</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Harga</th>
                                        <th class="text-end">Subtotal</th>
                                        <th style="width: 80px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="purchaseItemsBody">
                                    <tr class="purchase-items-empty-row">
                                        <td colspan="6">Belum ada item. Tambahkan item menggunakan form di atas.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2 text-end">
                            <span class="fw-bold">Grand Total: </span>
                            <span id="purchaseGrandTotal">Rp 0,00</span>
                        </div>
                        <div class="text-end">
                            <small id="purchaseGrandTotalWords" class="text-muted">Terbilang: Nol Rupiah</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2"><?php echo htmlspecialchars($form_notes, ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="purchase_submit_button">Simpan Pembelian</button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="purchase_cancel_edit_button">Batal Edit</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="alert alert-info">
            Hanya pemasok dan produk dengan status aktif yang dapat dipilih.
        </div>
    </div>
    <div class="col-12 d-none" id="purchaseFormSeparator">
        <hr class="my-4">
    </div>
    <div class="col-12" id="purchaseListCol">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h5 mb-0">Daftar Pembelian Terakhir</h1>
            <button type="button" class="btn btn-primary btn-sm" id="togglePurchaseFormButton">Tambah Pembelian Baru</button>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <?php if (empty($purchases)): ?>
                    <p class="mb-0">Belum ada data pembelian.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Faktur</th>
                                <th>Pemasok</th>
                                <th class="text-end">Total</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($purchases as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(format_date_id($row['purchase_date']), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($row['invoice_no'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php if (!empty($row['supplier_invoice_no'])): ?>
                                        <br><small class="text-muted">Supplier: <?php echo htmlspecialchars($row['supplier_invoice_no'], ENT_QUOTES, 'UTF-8'); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['supplier_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-end">Rp <?php echo number_format((float)$row['total_amount'], 2, ',', '.'); ?></td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary btn-purchase-detail"
                                        data-purchase-id="<?php echo (int)$row['id']; ?>"
                                    >
                                        Detail
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary ms-1 btn-purchase-edit"
                                        data-purchase-id="<?php echo (int)$row['id']; ?>"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger ms-1 btn-purchase-delete"
                                        data-purchase-id="<?php echo (int)$row['id']; ?>"
                                    >
                                        Hapus
                                    </button>
                                </td>
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
<div class="modal fade" id="quickSupplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pemasok Cepat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" autocomplete="off">
                    <input type="hidden" name="action" value="quick_add_supplier">
                    <div class="mb-3">
                        <label class="form-label">Nama Pemasok</label>
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
                        <button type="submit" class="btn btn-primary">Simpan Pemasok</button>
                    </div>
                </form>
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
                <form id="purchase_quick_product_form" autocomplete="off">
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
                        <input
                            type="text"
                            name="sell_price_display"
                            class="form-control currency-input"
                            inputmode="decimal"
                            autocomplete="off"
                            required
                            data-currency-hidden="#purchase_quick_product_sell_price_raw"
                        >
                        <input type="hidden" name="sell_price" id="purchase_quick_product_sell_price_raw" value="0">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="purchase_quick_product_is_active" name="is_active" checked>
                        <label class="form-check-label" for="purchase_quick_product_is_active">
                            Aktif
                        </label>
                    </div>
                    <div class="alert alert-danger d-none" id="purchase_quick_product_error"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="purchase_quick_product_save_button">Simpan Produk</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="purchaseDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <p class="mb-1">
                        <span id="purchaseDetailDate"></span>,
                        <span id="purchaseDetailSupplier"></span>
                    </p>
                    <p class="mb-1">
                        No. Faktur: <span id="purchaseDetailInvoice"></span><br>
                        Total: <span id="purchaseDetailTotal"></span><br>
                        <small id="purchaseDetailTotalWords">Terbilang (dibulatkan ke rupiah terdekat):</small>
                    </p>
                    <p class="mb-2 d-none" id="purchaseDetailNotesWrapper">
                        Catatan: <span id="purchaseDetailNotes"></span>
                    </p>
                </div>
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
                        <tbody id="purchaseDetailItemsBody">
                            <tr>
                                <td colspan="4">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(function () {
    var $supplierSelect = $('#supplier_id');
    var $supplierSearch = $('#supplier_search');
    var $purchaseFormCol = $('#purchaseFormCol');
    var $purchaseListCol = $('#purchaseListCol');
    var $purchaseFormSeparator = $('#purchaseFormSeparator');
    var $togglePurchaseFormButton = $('#togglePurchaseFormButton');
    var $purchaseProductSelect = $('#purchase_product_id');
    var $purchaseQtyInput = $('#purchase_qty');
    var $purchasePriceInput = $('#purchase_price');
    var $purchasePriceRawInput = $('#purchase_price_raw');
    var $purchaseItemsBody = $('#purchaseItemsBody');
    var $purchaseDetailModal = $('#purchaseDetailModal');
    var $purchaseDetailItemsBody = $('#purchaseDetailItemsBody');
    var $purchaseDetailDate = $('#purchaseDetailDate');
    var $purchaseDetailSupplier = $('#purchaseDetailSupplier');
    var $purchaseDetailInvoice = $('#purchaseDetailInvoice');
    var $purchaseDetailTotal = $('#purchaseDetailTotal');
    var $purchaseDetailTotalWords = $('#purchaseDetailTotalWords');
    var $purchaseDetailNotes = $('#purchaseDetailNotes');
    var $purchaseDetailNotesWrapper = $('#purchaseDetailNotesWrapper');
    var $purchaseForm = $('#purchaseForm');
    var $purchaseAction = $('#purchase_action');
    var $purchaseIdInput = $('#purchase_id');
    var $purchaseSubmitButton = $('#purchase_submit_button');
    var $purchaseCancelEditButton = $('#purchase_cancel_edit_button');
    var $purchaseModeBadge = $('#purchase_mode_badge');
    var $quickProductModal = $('#quickProductModal');
    var $quickProductForm = $('#purchase_quick_product_form');
    var $quickProductError = $('#purchase_quick_product_error');
    var $quickProductSaveButton = $('#purchase_quick_product_save_button');
    var $purchaseGrandTotalWords = $('#purchaseGrandTotalWords');
    var $purchaseTimeInput = $('#purchase_time');
    var $purchaseProductInvalidFeedback = $('#purchase_product_invalid_feedback');
    var $purchaseItemsCount = $('#purchaseItemsCount');

    function setDefaultPurchaseTimeIfEmpty() {
        if (!$purchaseTimeInput.length) {
            return;
        }
        var currentVal = String($purchaseTimeInput.val() || '');
        if (currentVal !== '') {
            return;
        }
        var now = new Date();
        var hours = now.getHours();
        var minutes = now.getMinutes();
        var hh = hours < 10 ? '0' + hours : String(hours);
        var mm = minutes < 10 ? '0' + minutes : String(minutes);
        $purchaseTimeInput.val(hh + ':' + mm);
    }
    function numberToIndonesianWords(num) {
        var n = Math.floor(Number(num) || 0);
        if (!isFinite(n) || n < 0) {
            return '';
        }
        if (n === 0) {
            return 'Nol Rupiah';
        }
        var units = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        function toWords(x) {
            var str;
            if (x < 12) {
                return units[x];
            }
            if (x < 20) {
                return units[x - 10] + ' Belas';
            }
            if (x < 100) {
                var tens = Math.floor(x / 10);
                var rest = x % 10;
                str = units[tens] + ' Puluh';
                if (rest > 0) {
                    str += ' ' + toWords(rest);
                }
                return str;
            }
            if (x < 200) {
                return 'Seratus' + (x > 100 ? ' ' + toWords(x - 100) : '');
            }
            if (x < 1000) {
                var hundreds = Math.floor(x / 100);
                var rest100 = x % 100;
                str = units[hundreds] + ' Ratus';
                if (rest100 > 0) {
                    str += ' ' + toWords(rest100);
                }
                return str;
            }
            if (x < 2000) {
                return 'Seribu' + (x > 1000 ? ' ' + toWords(x - 1000) : '');
            }
            if (x < 1000000) {
                var thousands = Math.floor(x / 1000);
                var rest1000 = x % 1000;
                str = toWords(thousands) + ' Ribu';
                if (rest1000 > 0) {
                    str += ' ' + toWords(rest1000);
                }
                return str;
            }
            if (x < 1000000000) {
                var millions = Math.floor(x / 1000000);
                var restMillion = x % 1000000;
                str = toWords(millions) + ' Juta';
                if (restMillion > 0) {
                    str += ' ' + toWords(restMillion);
                }
                return str;
            }
            if (x < 1000000000000) {
                var billions = Math.floor(x / 1000000000);
                var restBillion = x % 1000000000;
                str = toWords(billions) + ' Miliar';
                if (restBillion > 0) {
                    str += ' ' + toWords(restBillion);
                }
                return str;
            }
            var trillions = Math.floor(x / 1000000000000);
            var restTrillion = x % 1000000000000;
            str = toWords(trillions) + ' Triliun';
            if (restTrillion > 0) {
                str += ' ' + toWords(restTrillion);
            }
            return str;
        }
        var words = toWords(n);
        return words + ' Rupiah';
    }

    setDefaultPurchaseTimeIfEmpty();

    function togglePurchaseFormVisibility(show) {
        if (!$purchaseFormCol.length) {
            return;
        }
        if (typeof show === 'boolean') {
            if (show) {
                $purchaseFormCol.removeClass('d-none');
                if ($purchaseFormSeparator.length) {
                    $purchaseFormSeparator.removeClass('d-none');
                }
            } else {
                $purchaseFormCol.addClass('d-none');
                if ($purchaseFormSeparator.length) {
                    $purchaseFormSeparator.addClass('d-none');
                }
            }
        } else {
            $purchaseFormCol.toggleClass('d-none');
            if ($purchaseFormSeparator.length) {
                if ($purchaseFormCol.hasClass('d-none')) {
                    $purchaseFormSeparator.addClass('d-none');
                } else {
                    $purchaseFormSeparator.removeClass('d-none');
                }
            }
        }
        if ($togglePurchaseFormButton.length) {
            if ($purchaseFormCol.hasClass('d-none')) {
                $togglePurchaseFormButton.text('Tambah Pembelian Baru');
            } else {
                $togglePurchaseFormButton.text('Tutup Form Pembelian');
            }
        }
        if (!$purchaseFormCol.hasClass('d-none')) {
            $('html, body').animate({
                scrollTop: $purchaseFormCol.offset().top - 20
            }, 200);
        }
    }

    if ($supplierSelect.length && $purchaseForm.length) {
        $supplierSelect.on('change', function () {
            var val = $(this).val();
            if (!val) {
                return;
            }
            var invoiceInput = $purchaseForm.find('input[name="supplier_invoice_no"]');
            if (invoiceInput.length) {
                invoiceInput.focus();
            }
        });
    }

    function syncPurchasePriceFromProduct() {
        if ($purchaseProductSelect.length === 0 || $purchasePriceInput.length === 0) {
            return;
        }
        var $selectedOption = $purchaseProductSelect.find('option:selected');
        if ($selectedOption.length === 0) {
            return;
        }
        var price = $selectedOption.attr('data-price');
        if (price === undefined || price === '') {
            return;
        }
        var currentVal = $purchasePriceInput.val();
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
                AppUtil.setCurrencyInputValue($purchasePriceInput[0], numericPrice);
            } else {
                $purchasePriceInput.val(String(numericPrice));
                if ($purchasePriceRawInput.length) {
                    $purchasePriceRawInput.val(String(numericPrice));
                }
            }
        }
        if ($purchaseQtyInput.length) {
            var qtyVal = $purchaseQtyInput.val();
            if (qtyVal === '' || qtyVal === '0' || qtyVal === '0.00') {
                $purchaseQtyInput.val('1');
            }
        }
    }

    function updatePurchaseItemsEmptyRow() {
        if (!$purchaseItemsBody.length) {
            return;
        }
        var realRows = [];
        $purchaseItemsBody.find('tr').each(function () {
            var $tr = $(this);
            if (!$tr.hasClass('purchase-items-empty-row')) {
                realRows.push($tr);
            }
        });
        if (realRows.length > 0) {
            $purchaseItemsBody.find('.purchase-items-empty-row').remove();
        } else if ($purchaseItemsBody.find('.purchase-items-empty-row').length === 0) {
            var $emptyRow = $('<tr></tr>').addClass('purchase-items-empty-row');
            var $td = $('<td></td>').attr('colspan', 6);
            $td.text('Belum ada item. Tambahkan item menggunakan form di atas.');
            $emptyRow.append($td);
            $purchaseItemsBody.append($emptyRow);
        }
        if ($purchaseItemsCount && $purchaseItemsCount.length) {
            var count = realRows.length;
            $purchaseItemsCount.text('(' + count + ' item.)');
        }
        if (realRows.length > 0) {
            for (var i = 0; i < realRows.length; i++) {
                var $row = realRows[i];
                var $firstTd = $row.children('td').first();
                $firstTd.text(String(i + 1));
            }
        }
        if ($purchaseProductSelect.length) {
            var used = {};
            $purchaseItemsBody.find('input[name$="[product_id]"]').each(function () {
                var v = $(this).val();
                if (v) {
                    used[String(v)] = true;
                }
            });
            var currentVal = String($purchaseProductSelect.val() || '');
            $purchaseProductSelect.find('option').each(function () {
                var $opt = $(this);
                var val = $opt.val();
                if (!val) {
                    return;
                }
                if (Object.prototype.hasOwnProperty.call(used, String(val))) {
                    $opt.prop('disabled', true).addClass('d-none');
                    if (currentVal === String(val)) {
                        currentVal = '';
                    }
                } else {
                    $opt.prop('disabled', false).removeClass('d-none');
                }
            });
            if (currentVal === '') {
                $purchaseProductSelect.val('');
            }
        }
    }

    var purchaseItemIndex = 0;

    function clearPurchaseFormItems() {
        if (!$purchaseItemsBody.length) {
            return;
        }
        $purchaseItemsBody.empty();
        purchaseItemIndex = 0;
        updatePurchaseItemsEmptyRow();
        recalculateGrandTotal();
    }

    function appendPurchaseItemRowFromData(item) {
        if (!$purchaseItemsBody.length) {
            return;
        }
        var productId = item.product_id;
        var qty = item.qty;
        var price = item.price;
        var name = item.product_name || '';
        var unit = item.unit || '';
        if (unit) {
            name += ' (' + unit + ')';
        }
        var index = purchaseItemIndex++;
        var subtotal = qty * price;
        var qtyText = String(qty);

        var $row = $('<tr></tr>');
        var $indexTd = $('<td></td>').addClass('text-center');
        var $nameTd = $('<td></td>').text(name);

        var $qtyTd = $('<td></td>').addClass('text-end');
        var $qtyInput = $('<input>').attr({
            type: 'number',
            min: '0',
            step: '1',
            class: 'form-control form-control-sm text-end',
            name: 'items[' + index + '][qty]'
        }).val(qtyText);
        $qtyTd.append($qtyInput);

        var $priceTd = $('<td></td>').addClass('text-end');
        var formattedPrice = price;
        if (typeof AppUtil !== 'undefined' && typeof AppUtil.formatCurrencyIndo === 'function') {
            formattedPrice = AppUtil.formatCurrencyIndo(price);
        } else if (typeof price === 'number' && isFinite(price)) {
            formattedPrice = price.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        } else {
            formattedPrice = String(price);
        }
        var $priceInput = $('<input>').attr({
            type: 'text',
            class: 'form-control form-control-sm text-end',
            inputmode: 'decimal',
            autocomplete: 'off'
        }).val(formattedPrice);
        $priceTd.append($priceInput);
        var $hiddenPrice = $('<input>').attr({
            type: 'hidden',
            name: 'items[' + index + '][price]'
        }).val(price.toFixed(2));
        $priceTd.append($hiddenPrice);

        var $subtotalTd = $('<td></td>').addClass('text-end');
        $subtotalTd.text('Rp ' + subtotal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        var $actionTd = $('<td></td>');
        var $removeBtn = $('<button type="button" class="btn btn-sm btn-outline-danger" aria-label="Hapus" title="Hapus">🗑️</button>');
        $removeBtn.on('click', function () {
            $row.remove();
            updatePurchaseItemsEmptyRow();
            recalculateGrandTotal();
        });
        $actionTd.append($removeBtn);

        var $hiddenProductId = $('<input>').attr({
            type: 'hidden',
            name: 'items[' + index + '][product_id]'
        }).val(productId);

        $row.append($indexTd, $nameTd, $qtyTd, $priceTd, $subtotalTd, $actionTd, $hiddenProductId);
        $purchaseItemsBody.append($row);

        var updateRow = function () {
            var qtyVal = parseFloat(String($qtyInput.val() || '0').replace(',', ''));
            if (!isFinite(qtyVal) || qtyVal < 0) {
                qtyVal = 0;
            }
            var rawPriceStr = String($priceInput.val() || '0');
            var priceVal;
            if (typeof AppUtil !== 'undefined' && typeof AppUtil.parseCurrencyIndo === 'function') {
                priceVal = AppUtil.parseCurrencyIndo(rawPriceStr);
            } else {
                priceVal = parseFloat(rawPriceStr.replace(/\./g, '').replace(',', '.'));
            }
            if (!isFinite(priceVal) || priceVal < 0) {
                priceVal = 0;
            }
            var subtotalVal = qtyVal * priceVal;
            $hiddenPrice.val(priceVal.toFixed(2));
            $subtotalTd.text('Rp ' + subtotalVal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            recalculateGrandTotal();
        };

        var formatPriceInput = function () {
            var rawPriceStr = String($priceInput.val() || '');
            if (!rawPriceStr) {
                return;
            }
            var priceVal;
            if (typeof AppUtil !== 'undefined' && typeof AppUtil.parseCurrencyIndo === 'function') {
                priceVal = AppUtil.parseCurrencyIndo(rawPriceStr);
            } else {
                priceVal = parseFloat(rawPriceStr.replace(/\./g, '').replace(',', '.'));
            }
            if (!isFinite(priceVal) || priceVal < 0) {
                return;
            }
            var formatted = priceVal;
            if (typeof AppUtil !== 'undefined' && typeof AppUtil.formatCurrencyIndo === 'function') {
                formatted = AppUtil.formatCurrencyIndo(priceVal);
            } else {
                formatted = priceVal.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
            $priceInput.val(formatted);
        };

        $qtyInput.on('input change', updateRow);
        $priceInput.on('input change', updateRow);
        $priceInput.on('blur', formatPriceInput);
    }

    function addCurrentPurchaseItemRow() {
        if (!$purchaseProductSelect.length || !$purchaseQtyInput.length || !$purchasePriceInput.length || !$purchaseItemsBody.length) {
            return;
        }
        var $selectedOption = $purchaseProductSelect.find('option:selected');
        var productId = $selectedOption.val();
        var productText = $selectedOption.text();
        var unit = $selectedOption.attr('data-unit') || '';
        var qtyRaw = $purchaseQtyInput.val();
        var priceRawDisplay = $purchasePriceInput.val();
        var qty = parseFloat(qtyRaw);
        var price = typeof AppUtil !== 'undefined' && typeof AppUtil.parseCurrencyIndo === 'function'
            ? AppUtil.parseCurrencyIndo(priceRawDisplay)
            : parseFloat(String(priceRawDisplay || '0').replace(',', ''));

        $purchaseProductSelect.removeClass('is-invalid');
        $purchaseQtyInput.removeClass('is-invalid');
        $purchasePriceInput.removeClass('is-invalid');

        if (!productId) {
            var msg = 'Silakan pilih produk terlebih dahulu. Jika produk belum ada di daftar, gunakan tombol Tambah di samping untuk menambahkan produk baru.';
            if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                AppUtil.showToast(msg, { type: 'error' });
            } else {
                alert(msg);
            }
            $purchaseProductSelect.addClass('is-invalid');
            if ($purchaseProductInvalidFeedback && $purchaseProductInvalidFeedback.length) {
                $purchaseProductInvalidFeedback.text(msg);
            }
            return;
        }
        if (!qtyRaw || isNaN(qty) || qty <= 0) {
            if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                AppUtil.showToast('Jumlah pembelian harus lebih besar dari nol.', { type: 'error' });
            } else {
                alert('Jumlah pembelian harus lebih besar dari nol.');
            }
            $purchaseQtyInput.addClass('is-invalid');
            return;
        }
        if (!priceRawDisplay || isNaN(price) || price < 0) {
            if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                AppUtil.showToast('Harga tidak boleh bernilai negatif.', { type: 'error' });
            } else {
                alert('Harga tidak boleh bernilai negatif.');
            }
            $purchasePriceInput.addClass('is-invalid');
            return;
        }

        var subtotal = qty * price;
        var nameText = productText || '';
        if (unit && nameText.indexOf('(') === -1) {
            nameText += ' (' + unit + ')';
        }

        var $row = $('<tr></tr>');

        var $indexTd = $('<td></td>').addClass('text-center');

        var $nameTd = $('<td></td>');
        $nameTd.text(nameText);

        var $qtyTd = $('<td></td>').addClass('text-end');
        var $qtyInput = $('<input>').attr({
            type: 'number',
            min: '0',
            step: '1',
            class: 'form-control form-control-sm text-end',
            name: 'items[' + purchaseItemIndex + '][qty]'
        }).val(qtyRaw);
        $qtyTd.append($qtyInput);

        var $priceTd = $('<td></td>').addClass('text-end');
        var formattedPrice = price;
        if (typeof AppUtil !== 'undefined' && typeof AppUtil.formatCurrencyIndo === 'function') {
            formattedPrice = AppUtil.formatCurrencyIndo(price);
        } else if (typeof price === 'number' && isFinite(price)) {
            formattedPrice = price.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        } else {
            formattedPrice = String(price);
        }
        var $priceInput = $('<input>').attr({
            type: 'text',
            class: 'form-control form-control-sm text-end',
            inputmode: 'decimal',
            autocomplete: 'off'
        }).val(formattedPrice);
        $priceTd.append($priceInput);
        var $subtotalTd = $('<td></td>').addClass('text-end');
        $subtotalTd.text('Rp ' + subtotal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        var $actionTd = $('<td></td>');
        var $removeBtn = $('<button type="button" class="btn btn-sm btn-outline-danger" aria-label="Hapus" title="Hapus">🗑️</button>');
        $removeBtn.on('click', function () {
            $row.remove();
            updatePurchaseItemsEmptyRow();
            recalculateGrandTotal();
        });
        $actionTd.append($removeBtn);

        var index = purchaseItemIndex++;
        var $hiddenProductId = $('<input>').attr({
            type: 'hidden',
            name: 'items[' + index + '][product_id]'
        }).val(productId);
        var $hiddenPrice = $('<input>').attr({
            type: 'hidden',
            name: 'items[' + index + '][price]'
        }).val(price.toFixed(2));

        $row.append($indexTd, $nameTd, $qtyTd, $priceTd, $subtotalTd, $actionTd, $hiddenProductId, $hiddenPrice);
        $purchaseItemsBody.append($row);
        updatePurchaseItemsEmptyRow();
        recalculateGrandTotal();

        var updateRow = function () {
            var qtyVal = parseFloat(String($qtyInput.val() || '0').replace(',', ''));
            if (!isFinite(qtyVal) || qtyVal < 0) {
                qtyVal = 0;
            }
            var rawPriceStr = String($priceInput.val() || '0');
            var priceVal;
            if (typeof AppUtil !== 'undefined' && typeof AppUtil.parseCurrencyIndo === 'function') {
                priceVal = AppUtil.parseCurrencyIndo(rawPriceStr);
            } else {
                priceVal = parseFloat(rawPriceStr.replace(/\./g, '').replace(',', '.'));
            }
            if (!isFinite(priceVal) || priceVal < 0) {
                priceVal = 0;
            }
            var subtotalVal = qtyVal * priceVal;
            $hiddenPrice.val(priceVal.toFixed(2));
            $subtotalTd.text('Rp ' + subtotalVal.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            recalculateGrandTotal();
        };

        var formatPriceInput = function () {
            var rawPriceStr = String($priceInput.val() || '');
            if (!rawPriceStr) {
                return;
            }
            var priceVal;
            if (typeof AppUtil !== 'undefined' && typeof AppUtil.parseCurrencyIndo === 'function') {
                priceVal = AppUtil.parseCurrencyIndo(rawPriceStr);
            } else {
                priceVal = parseFloat(rawPriceStr.replace(/\./g, '').replace(',', '.'));
            }
            if (!isFinite(priceVal) || priceVal < 0) {
                return;
            }
            var formatted = priceVal;
            if (typeof AppUtil !== 'undefined' && typeof AppUtil.formatCurrencyIndo === 'function') {
                formatted = AppUtil.formatCurrencyIndo(priceVal);
            } else {
                formatted = priceVal.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
            $priceInput.val(formatted);
        };

        $qtyInput.on('input change', updateRow);
        $priceInput.on('input change', updateRow);
        $priceInput.on('blur', formatPriceInput);

        $purchaseProductSelect.val('');
        $purchaseProductSelect.removeClass('is-invalid');
        if ($purchaseProductInvalidFeedback && $purchaseProductInvalidFeedback.length) {
            $purchaseProductInvalidFeedback.text('');
        }
        $purchaseQtyInput.val('');
        $purchaseQtyInput.removeClass('is-invalid');
        $purchasePriceInput.val('');
        $purchasePriceInput.removeClass('is-invalid');
        if ($purchasePriceRawInput.length) {
            $purchasePriceRawInput.val('');
        }
        if ($purchaseProductSelect.length) {
            $purchaseProductSelect.focus();
        }
    }

    function recalculateGrandTotal() {
        var total = 0;
        if ($purchaseItemsBody.length) {
            $purchaseItemsBody.find('input[name$="[qty]"]').each(function () {
                var $qtyInput = $(this);
                var nameAttr = $qtyInput.attr('name');
                var priceName = nameAttr.replace('[qty]', '[price]');
                var qtyVal = parseFloat(String($qtyInput.val() || '0').replace(',', ''));
                var priceVal = 0;
                var $priceInput = $purchaseItemsBody.find('input[name="' + priceName + '"]');
                if ($priceInput.length) {
                    priceVal = parseFloat(String($priceInput.val() || '0').replace(',', ''));
                }
                if (!isNaN(qtyVal) && !isNaN(priceVal)) {
                    total += qtyVal * priceVal;
                }
            });
        }
        var $grandTotal = $('#purchaseGrandTotal');
        if ($grandTotal.length) {
            $grandTotal.text('Rp ' + total.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }
        if ($purchaseGrandTotalWords && $purchaseGrandTotalWords.length) {
            if (total > 0) {
                $purchaseGrandTotalWords.text('Terbilang: ' + numberToIndonesianWords(total));
            } else {
                $purchaseGrandTotalWords.text('Terbilang: Nol Rupiah');
            }
        }
    }

    function tryAutoAddPurchaseItemFromQty() {
        if (!$purchaseProductSelect.length || !$purchaseQtyInput.length || !$purchasePriceInput.length) {
            return;
        }
        var productId = $purchaseProductSelect.val();
        var qtyRaw = $purchaseQtyInput.val();
        if (!productId || !qtyRaw) {
            return;
        }
        var qty = parseFloat(qtyRaw);
        if (!isFinite(qty) || qty <= 0) {
            return;
        }
        var priceRawDisplay = $purchasePriceInput.val();
        if (!priceRawDisplay) {
            return;
        }
        addCurrentPurchaseItemRow();
    }

    if ($purchaseQtyInput.length) {
        $purchaseQtyInput.on('input', function () {
            if ($purchaseQtyInput.hasClass('is-invalid')) {
                var val = parseFloat(String($purchaseQtyInput.val() || '0'));
                if (!isNaN(val) && val > 0) {
                    $purchaseQtyInput.removeClass('is-invalid');
                }
            }
        });
        $purchaseQtyInput.on('blur', function () {
            tryAutoAddPurchaseItemFromQty();
        });
    }

    if ($purchasePriceInput.length) {
        $purchasePriceInput.on('input', function () {
            if ($purchasePriceInput.hasClass('is-invalid')) {
                var val = typeof AppUtil !== 'undefined' && typeof AppUtil.parseCurrencyIndo === 'function'
                    ? AppUtil.parseCurrencyIndo($purchasePriceInput.val())
                    : parseFloat(String($purchasePriceInput.val() || '0').replace(',', ''));
                if (!isNaN(val) && val >= 0) {
                    $purchasePriceInput.removeClass('is-invalid');
                }
            }
        });
    }

    if ($purchaseTimeInput.length) {
        $purchaseTimeInput.on('input change', function () {
            if ($purchaseTimeInput.hasClass('is-invalid')) {
                var val = String($purchaseTimeInput.val() || '');
                var match = val.match(/^(\d{2}):(\d{2})$/);
                if (match) {
                    var hours = parseInt(match[1], 10);
                    var minutes = parseInt(match[2], 10);
                    if (hours >= 0 && hours < 24 && minutes >= 0 && minutes < 60) {
                        $purchaseTimeInput.removeClass('is-invalid');
                        var group = $purchaseTimeInput.closest('.input-group');
                        if (group.length) {
                            var feedback = group.next('.invalid-feedback');
                            if (feedback.length) {
                                feedback.text('');
                            }
                        }
                    }
                }
            }
        });
    }

    if ($purchaseQtyInput.length) {
        $purchaseQtyInput.on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addCurrentPurchaseItemRow();
            }
        });
    }

    if ($purchasePriceInput.length) {
        $purchasePriceInput.on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addCurrentPurchaseItemRow();
            }
        });
    }

    if ($purchaseProductSelect.length > 0 && $purchasePriceInput.length > 0) {
        $purchaseProductSelect.on('change', function () {
            syncPurchasePriceFromProduct();
            $purchaseProductSelect.removeClass('is-invalid');
            if ($purchaseProductInvalidFeedback && $purchaseProductInvalidFeedback.length) {
                $purchaseProductInvalidFeedback.text('');
            }
            if ($purchaseQtyInput.length) {
                $purchaseQtyInput.focus();
                $purchaseQtyInput.select();
            }
        });
        syncPurchasePriceFromProduct();
    }

    if (typeof AppUtil !== 'undefined' && typeof AppUtil.setupAjaxIncrementalSelect === 'function') {
        if ($supplierSelect.length > 0 && $supplierSearch.length > 0) {
            AppUtil.setupAjaxIncrementalSelect({
                inputSelector: '#supplier_search',
                selectSelector: '#supplier_id',
                ajaxUrl: 'suppliers.php',
                ajaxParams: function (term) {
                    return {
                        ajax: 'search_suppliers',
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
    }

    if (typeof AppUtil !== 'undefined' && typeof AppUtil.handleLargeForm === 'function') {
        if ($purchaseForm.length) {
            AppUtil.handleLargeForm({
                formSelector: '#purchaseForm',
                ajaxUrl: 'purchases.php',
                parseJson: true,
                beforeSubmit: function ($form) {
                    if ($form.find('input[name^="items["]').length === 0) {
                        if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                            AppUtil.showToast('Minimal satu item pembelian harus ditambahkan.', { type: 'error' });
                        } else {
                            alert('Minimal satu item pembelian harus ditambahkan.');
                        }
                        return false;
                    }
                    if ($purchaseTimeInput && $purchaseTimeInput.length) {
                        $purchaseTimeInput.removeClass('is-invalid');
                        var timeVal = String($purchaseTimeInput.val() || '');
                        var timeFeedback = null;
                        var timeGroup = $purchaseTimeInput.closest('.input-group');
                        if (timeGroup.length) {
                            timeFeedback = timeGroup.next('.invalid-feedback');
                        }
                        if (timeFeedback && timeFeedback.length) {
                            timeFeedback.text('');
                        }
                        if (timeVal !== '') {
                            var match = timeVal.match(/^(\d{2}):(\d{2})$/);
                            var validTime = false;
                            if (match) {
                                var hours = parseInt(match[1], 10);
                                var minutes = parseInt(match[2], 10);
                                if (hours >= 0 && hours < 24 && minutes >= 0 && minutes < 60) {
                                    validTime = true;
                                }
                            }
                            if (!validTime) {
                                $purchaseTimeInput.addClass('is-invalid');
                                if (timeFeedback && timeFeedback.length) {
                                    timeFeedback.text('Jam tidak valid, gunakan format HH:mm.');
                                }
                                if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                                    AppUtil.showToast('Jam pembelian tidak valid.', { type: 'error' });
                                }
                                return false;
                            }
                        }
                    }
                    if ($form.find('input[name="ajax"]').length === 0) {
                        $('<input type="hidden" name="ajax" value="1">').appendTo($form);
                    }
                    return true;
                },
                onSuccess: function (resp) {
                    if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                        AppUtil.showToast(resp.message || 'Transaksi pembelian berhasil disimpan.', { type: 'success' });
                    }
                    window.location.href = 'purchases.php';
                },
                onError: function (msg) {
                    if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                        AppUtil.showToast(msg || 'Terjadi kesalahan saat menyimpan transaksi pembelian.', { type: 'error' });
                    }
                }
            });
        }

        var $quickSupplierForm = $('#quickSupplierModal form');
        if ($quickSupplierForm.length) {
            AppUtil.handleLargeForm({
                formSelector: '#quickSupplierModal form',
                ajaxUrl: 'purchases.php',
                parseJson: true,
                beforeSubmit: function ($form) {
                    if ($form.find('input[name="ajax"]').length === 0) {
                        $('<input type="hidden" name="ajax" value="1">').appendTo($form);
                    }
                    return true;
                },
                onSuccess: function (resp) {
                    if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                        AppUtil.showToast(resp.message || 'Pemasok baru berhasil ditambahkan.', { type: 'success' });
                    }
                    var modalEl = document.getElementById('quickSupplierModal');
                    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                    }
                    if (resp.supplier && resp.supplier.id && resp.supplier.name && $supplierSelect.length) {
                        var idStr = String(resp.supplier.id);
                        var exists = false;
                        $supplierSelect.find('option').each(function () {
                            if ($(this).val() === idStr) {
                                exists = true;
                                return false;
                            }
                        });
                        if (!exists) {
                            var $opt = $('<option></option>');
                            $opt.val(idStr);
                            $opt.text(resp.supplier.name);
                            $supplierSelect.append($opt);
                        }
                        $supplierSelect.val(idStr);
                    }
                },
                onError: function (msg) {
                    if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                        AppUtil.showToast(msg || 'Terjadi kesalahan saat menambahkan pemasok.', { type: 'error' });
                    }
                }
            });
        }

        if ($quickProductForm.length && $quickProductModal.length && $quickProductSaveButton.length && $purchaseProductSelect.length) {
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
                    if (product && product.id) {
                        var idStr = String(product.id);
                        var exists = false;
                        $purchaseProductSelect.find('option').each(function () {
                            if ($(this).val() === idStr) {
                                exists = true;
                                return false;
                            }
                        });
                        if (!exists) {
                            var text = product.name;
                            if (product.unit) {
                                text += ' (' + product.unit + ')';
                            }
                            var $opt = $('<option></option>');
                            $opt.val(idStr);
                            $opt.text(text);
                            $purchaseProductSelect.append($opt);
                        }
                        $purchaseProductSelect.val(idStr);
                        $purchaseProductSelect.trigger('change');
                    }
                    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Modal !== 'undefined') {
                        var modalInstance = bootstrap.Modal.getInstance($quickProductModal[0]);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    }
                }, 'json').fail(function () {
                    if ($quickProductError.length) {
                        $quickProductError.removeClass('d-none').text('Terjadi kesalahan saat menyimpan produk baru.');
                    }
                });
            });
        }
    }

    if (typeof AppUtil !== 'undefined' && typeof AppUtil.handleDeleteAction === 'function') {
        AppUtil.handleDeleteAction({
            buttonSelector: '.btn-purchase-delete',
            url: 'purchases.php',
            idDataAttribute: 'purchase-id',
            idParamName: 'purchase_id',
            actionName: 'delete_purchase',
            defaultErrorMessage: 'Gagal menghapus pembelian.',
            successMessage: 'Pembelian berhasil dihapus.',
            onSuccess: function ($btn) {
                var $row = $btn.closest('tr');
                if ($row.length) {
                    $row.remove();
                }
            }
        });
    }

    if ($togglePurchaseFormButton.length && $purchaseFormCol.length) {
        $togglePurchaseFormButton.on('click', function () {
            togglePurchaseFormVisibility();
        });
    }

    var $purchaseEditButtons = $('.btn-purchase-edit');
    if ($purchaseEditButtons.length && $purchaseForm.length && $purchaseAction.length && $purchaseIdInput.length) {
        $purchaseEditButtons.on('click', function () {
            var id = $(this).data('purchase-id');
            if (!id) {
                return;
            }
            togglePurchaseFormVisibility(true);
            $.getJSON('purchases.php', {
                ajax: 'purchase_detail',
                id: id
            }).done(function (resp) {
                if (!resp || resp.success !== true || !resp.purchase) {
                    var msg = resp && resp.message ? resp.message : 'Gagal memuat data pembelian.';
                    if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                        AppUtil.showToast(msg, { type: 'error' });
                    } else {
                        alert(msg);
                    }
                    return;
                }
                var p = resp.purchase;
                var items = Array.isArray(resp.items) ? resp.items : [];
                $purchaseAction.val('update_purchase');
                $purchaseIdInput.val(p.id || '');
                if ($supplierSelect.length && p.supplier_id) {
                    var supplierIdStr = String(p.supplier_id);
                    var hasOption = false;
                    $supplierSelect.find('option').each(function () {
                        if ($(this).val() === supplierIdStr) {
                            hasOption = true;
                            return false;
                        }
                    });
                    if (!hasOption) {
                        var $opt = $('<option></option>');
                        $opt.val(supplierIdStr);
                        $opt.text(p.supplier_name || '');
                        $supplierSelect.append($opt);
                    }
                    $supplierSelect.val(supplierIdStr);
                }
                var supplierInvoiceInput = $purchaseForm.find('input[name="supplier_invoice_no"]');
                if (supplierInvoiceInput.length) {
                    supplierInvoiceInput.val(p.supplier_invoice_no || '');
                }
                var dateInput = $purchaseForm.find('input[name="purchase_date"]');
                if (dateInput.length) {
                    if (p.formatted_date) {
                        dateInput.val(p.formatted_date);
                    } else if (p.purchase_date) {
                        dateInput.val(p.purchase_date);
                    }
                }
                if ($purchaseTimeInput.length) {
                    var timeValue = '';
                    if (p.purchase_date) {
                        var purchaseDateStr = String(p.purchase_date);
                        var spaceIndex = purchaseDateStr.indexOf(' ');
                        if (spaceIndex !== -1) {
                            var timePart = purchaseDateStr.substr(spaceIndex + 1);
                            var timePieces = timePart.split(':');
                            if (timePieces.length >= 2) {
                                timeValue = timePieces[0] + ':' + timePieces[1];
                            }
                        }
                    }
                    $purchaseTimeInput.val(timeValue);
                    $purchaseTimeInput.removeClass('is-invalid');
                    var timeGroup = $purchaseTimeInput.closest('.input-group');
                    if (timeGroup.length) {
                        var timeFeedback = timeGroup.next('.invalid-feedback');
                        if (timeFeedback.length) {
                            timeFeedback.text('');
                        }
                    }
                }
                var notesInput = $purchaseForm.find('textarea[name="notes"]');
                if (notesInput.length) {
                    notesInput.val(p.notes || '');
                }
                clearPurchaseFormItems();
                if (items.length) {
                    $.each(items, function (index, item) {
                        appendPurchaseItemRowFromData(item);
                    });
                    updatePurchaseItemsEmptyRow();
                    recalculateGrandTotal();
                }
                if ($purchaseSubmitButton.length) {
                    $purchaseSubmitButton.text('Simpan Perubahan');
                }
                if ($purchaseCancelEditButton.length) {
                    $purchaseCancelEditButton.removeClass('d-none');
                }
                if ($purchaseModeBadge.length) {
                    $purchaseModeBadge
                        .removeClass('bg-secondary')
                        .addClass('bg-warning text-dark')
                        .text('Mode Edit');
                }
                $('html, body').animate({
                    scrollTop: $purchaseForm.offset().top - 20
                }, 200);
            }).fail(function () {
                var msg = 'Gagal terhubung ke server.';
                if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                    AppUtil.showToast(msg, { type: 'error' });
                } else {
                    alert(msg);
                }
            });
        });
    }

    if ($purchaseCancelEditButton.length && $purchaseForm.length && $purchaseAction.length && $purchaseIdInput.length) {
        $purchaseCancelEditButton.on('click', function () {
            $purchaseAction.val('save');
            $purchaseIdInput.val('');
            if ($supplierSelect.length) {
                $supplierSelect.val('');
            }
            var invoiceInput = $purchaseForm.find('input[name="invoice_no"]');
            if (invoiceInput.length) {
                invoiceInput.val('');
            }
            var dateInput = $purchaseForm.find('input[name="purchase_date"]');
            if (dateInput.length) {
                dateInput.val('');
            }
            if ($purchaseTimeInput.length) {
                $purchaseTimeInput.val('');
                $purchaseTimeInput.removeClass('is-invalid');
                var timeGroup = $purchaseTimeInput.closest('.input-group');
                if (timeGroup.length) {
                    var timeFeedback = timeGroup.next('.invalid-feedback');
                    if (timeFeedback.length) {
                        timeFeedback.text('');
                    }
                }
                setDefaultPurchaseTimeIfEmpty();
            }
            var notesInput = $purchaseForm.find('textarea[name="notes"]');
            if (notesInput.length) {
                notesInput.val('');
            }
            if ($purchaseProductSelect.length) {
                $purchaseProductSelect.val('');
                $purchaseProductSelect.trigger('change');
            }
            if ($purchaseQtyInput.length) {
                $purchaseQtyInput.val('');
                $purchaseQtyInput.removeClass('is-invalid');
            }
            if ($purchasePriceInput.length) {
                $purchasePriceInput.val('');
                $purchasePriceInput.removeClass('is-invalid');
            }
            clearPurchaseFormItems();
            if ($purchaseSubmitButton.length) {
                $purchaseSubmitButton.text('Simpan Pembelian');
            }
            if ($purchaseModeBadge.length) {
                $purchaseModeBadge
                    .removeClass('bg-warning text-dark')
                    .addClass('bg-secondary')
                    .text('Baru');
            }
            $purchaseCancelEditButton.addClass('d-none');
            $('html, body').animate({
                scrollTop: $purchaseForm.offset().top - 20
            }, 200);
        });
    }

    var $purchaseDetailButtons = $('.btn-purchase-detail');
    if ($purchaseDetailButtons.length && $purchaseDetailModal.length) {
        $purchaseDetailButtons.on('click', function () {
            var id = $(this).data('purchase-id');
            if (!id) {
                return;
            }
            if ($purchaseDetailItemsBody.length) {
                $purchaseDetailItemsBody.html('<tr><td colspan="4">Memuat data...</td></tr>');
            }
            $.getJSON('purchases.php', {
                ajax: 'purchase_detail',
                id: id
            }).done(function (resp) {
                if (!resp || resp.success !== true || !resp.purchase) {
                    var msg = resp && resp.message ? resp.message : 'Gagal memuat detail pembelian.';
                    if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                        AppUtil.showToast(msg, { type: 'error' });
                    } else {
                        alert(msg);
                    }
                    return;
                }
                var p = resp.purchase;
                var items = Array.isArray(resp.items) ? resp.items : [];
                if ($purchaseDetailDate.length) {
                    $purchaseDetailDate.text(p.formatted_date || '');
                }
                if ($purchaseDetailSupplier.length) {
                    $purchaseDetailSupplier.text(p.supplier_name || '');
                }
                if ($purchaseDetailInvoice.length) {
                    $purchaseDetailInvoice.text(p.invoice_no || '');
                }
                if ($purchaseDetailTotal.length) {
                    var totalText = p.formatted_total || '';
                    if (totalText !== '') {
                        $purchaseDetailTotal.text('Rp ' + totalText);
                    } else {
                        $purchaseDetailTotal.text('');
                    }
                }
                if ($purchaseDetailTotalWords && $purchaseDetailTotalWords.length) {
                    var totalAmount = typeof p.total_amount === 'number' ? p.total_amount : parseFloat(String(p.total_amount || '0').replace(',', '.'));
                    if (!isNaN(totalAmount) && totalAmount > 0) {
                        $purchaseDetailTotalWords.text('Terbilang (dibulatkan ke rupiah terdekat): ' + numberToIndonesianWords(totalAmount));
                    } else {
                        $purchaseDetailTotalWords.text('Terbilang (dibulatkan ke rupiah terdekat): Nol Rupiah');
                    }
                }
                if ($purchaseDetailNotesWrapper.length && $purchaseDetailNotes.length) {
                    if (p.notes && p.notes !== '') {
                        $purchaseDetailNotes.text(p.notes);
                        $purchaseDetailNotesWrapper.removeClass('d-none');
                    } else {
                        $purchaseDetailNotes.text('');
                        $purchaseDetailNotesWrapper.addClass('d-none');
                    }
                }
                if ($purchaseDetailItemsBody.length) {
                    if (!items.length) {
                        $purchaseDetailItemsBody.html('<tr><td colspan="4">Belum ada item untuk pembelian ini.</td></tr>');
                    } else {
                        var rowsHtml = '';
                        $.each(items, function (index, item) {
                            var name = item.product_name || '';
                            var unit = item.unit || '';
                            if (unit) {
                                name += ' (' + unit + ')';
                            }
                            var $nameEsc = $('<div></div>').text(name);
                            var $qtyEsc = $('<div></div>').text(String(item.qty));
                            var priceText = item.formatted_price || '';
                            var subtotalText = item.formatted_subtotal || '';
                            var $priceEsc = $('<div></div>').text(priceText !== '' ? 'Rp ' + priceText : '');
                            var $subtotalEsc = $('<div></div>').text(subtotalText !== '' ? 'Rp ' + subtotalText : '');
                            rowsHtml += '<tr>' +
                                '<td>' + $nameEsc.html() + '</td>' +
                                '<td class="text-end">' + $qtyEsc.html() + '</td>' +
                                '<td class="text-end">' + $priceEsc.html() + '</td>' +
                                '<td class="text-end">' + $subtotalEsc.html() + '</td>' +
                                '</tr>';
                        });
                        $purchaseDetailItemsBody.html(rowsHtml);
                    }
                }
                var modalEl = $purchaseDetailModal[0];
                if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (!modal) {
                        modal = new bootstrap.Modal(modalEl);
                    }
                    modal.show();
                }
            }).fail(function () {
                var msg = 'Gagal terhubung ke server.';
                if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                    AppUtil.showToast(msg, { type: 'error' });
                } else {
                    alert(msg);
                }
            });
        });
    }
});
</script>
