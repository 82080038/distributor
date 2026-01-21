<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success !== ''): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0">Pembeli</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customerModal">
        Tambah Pembeli
    </button>
</div>
<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2" method="get" action="customers_new.php">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Cari nama pembeli" value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Hanya aktif</option>
                    <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Hanya nonaktif</option>
                    <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>Semua status</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="all" <?php echo $role === 'all' ? 'selected' : ''; ?>>Semua peran</option>
                    <option value="dual" <?php echo $role === 'dual' ? 'selected' : ''; ?>>Juga pemasok</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">Terapkan Filter</button>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php if (empty($customers)): ?>
            <p class="mb-0">Belum ada data pembeli.</p>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm table-striped align-middle">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kontak</th>
                        <th>Alamat</th>
                        <th>Peran</th>
                        <th>Status</th>
                        <th style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $c): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($c['nama_lengkap'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($c['kontak'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($c['alamat'], ENT_QUOTES, 'UTF-8')); ?></td>
                        <td>
                            <span class="badge bg-primary">Pembeli</span>
                            <?php if (isset($c['is_supplier']) && (int)$c['is_supplier'] === 1): ?>
                                <span class="badge bg-info text-dark ms-1">Juga pemasok</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ((int)$c['is_active'] === 1): ?>
                                <span class="badge bg-success customer-status-badge" data-status="1">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary customer-status-badge" data-status="0">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="customers_new.php?id=<?php echo (int)$c['id_orang']; ?>&status=<?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>&q=<?php echo urlencode($q); ?>" class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>
                                <?php
                                $isActive = (int)$c['is_active'] === 1;
                                $btnClass = $isActive ? 'btn-outline-warning' : 'btn-outline-success';
                                $btnText = $isActive ? 'Nonaktifkan' : 'Aktifkan';
                                ?>
                                <button
                                    type="button"
                                    class="btn btn-sm customer-status-toggle <?php echo $btnClass; ?>"
                                    data-customer-id="<?php echo (int)$c['id_orang']; ?>"
                                    data-current-status="<?php echo $isActive ? '1' : '0'; ?>"
                                >
                                    <?php echo $btnText; ?>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <div class="mt-3 alert alert-info">
            Pembeli yang dinonaktifkan tidak dapat dipilih pada transaksi penjualan baru, tetapi tetap tercatat pada transaksi yang sudah ada.
        </div>
    </div>
</div>
<div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $edit_customer ? 'Edit Pembeli' : 'Tambah Pembeli'; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" autocomplete="off">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="customer_id" value="<?php echo $edit_customer ? (int)$edit_customer['id_orang'] : 0; ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Pembeli (Orang/Usaha/Yayasan/Institusi)</label>
                        <input type="text" name="nama" class="form-control" required value="<?php echo $edit_customer ? htmlspecialchars($edit_customer['nama_lengkap'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kontak</label>
                        <input type="text" name="kontak" class="form-control" value="<?php echo $edit_customer ? htmlspecialchars($edit_customer['kontak'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                    </div>
                    <?php
                    require_once __DIR__ . DIRECTORY_SEPARATOR . 'alamat_manager.php';
                    $address_values = [
                        'province_id' => $edit_customer['province_id'] ?? 0,
                        'regency_id' => $edit_customer['regency_id'] ?? 0,
                        'district_id' => $edit_customer['district_id'] ?? 0,
                        'village_id' => $edit_customer['village_id'] ?? 0,
                        'street_address' => $edit_customer['alamat'] ?? '',
                        'postal_code' => $edit_customer['postal_code'] ?? '',
                        'tipe_alamat' => $edit_customer['tipe_alamat'] ?? ''
                    ];
                    render_alamat_form('', $address_values, true, true, true);
                    ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="also_supplier" name="also_supplier" <?php echo $edit_customer && isset($edit_customer['is_supplier']) && (int)$edit_customer['is_supplier'] === 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="also_supplier">
                            Juga sebagai pemasok
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active_cust" name="is_active" <?php echo !$edit_customer || (int)$edit_customer['is_active'] === 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active_cust">
                            Aktif
                        </label>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary"><?php echo $edit_customer ? 'Simpan Perubahan' : 'Tambah Pembeli'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
$(function () {
    if (typeof AppUtil !== 'undefined' && typeof AppUtil.handleStatusToggle === 'function') {
        AppUtil.handleStatusToggle({
            buttonSelector: '.customer-status-toggle',
            confirmMessage: 'Apakah Anda yakin ingin mengubah status pembeli ini?',
            url: 'customers_new.php',
            idDataAttribute: 'customer-id',
            idParamName: 'customer_id',
            defaultErrorMessage: 'Gagal mengubah status pembeli.',
            updateUI: function ($btn, newStatus) {
                $btn.removeClass('btn-outline-warning btn-outline-success');
                if (newStatus === 1) {
                    $btn.addClass('btn-outline-warning');
                    $btn.text('Nonaktifkan');
                } else {
                    $btn.addClass('btn-outline-success');
                    $btn.text('Aktifkan');
                }
                var $row = $btn.closest('tr');
                var $badge = $row.find('.customer-status-badge');
                if ($badge.length) {
                    if (newStatus === 1) {
                        $badge.removeClass('bg-secondary').addClass('bg-success');
                        $badge.text('Aktif');
                        $badge.attr('data-status', '1');
                    } else {
                        $badge.removeClass('bg-success').addClass('bg-secondary');
                        $badge.text('Nonaktif');
                        $badge.attr('data-status', '0');
                    }
                }
            }
        });
    }

    if (typeof AppUtil !== 'undefined' && typeof AppUtil.handleLargeForm === 'function') {
        AppUtil.handleLargeForm({
            formSelector: '#customerModal form',
            ajaxUrl: 'customers_new.php',
            parseJson: true,
            beforeSubmit: function ($form) {
                if ($form.find('input[name="ajax"]').length === 0) {
                    $('<input type="hidden" name="ajax" value="1">').appendTo($form);
                }
                return true;
            },
            onSuccess: function (resp) {
                if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                    AppUtil.showToast(resp.message || 'Data pembeli berhasil disimpan.', { type: 'success' });
                }
                var modalEl = document.getElementById('customerModal');
                if (modalEl && typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function') {
                    var instance = bootstrap.Modal.getInstance(modalEl);
                    if (!instance) {
                        instance = new bootstrap.Modal(modalEl);
                    }
                    instance.hide();
                }
                window.location.href = 'customers_new.php';
            },
            onError: function (msg) {
                if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                    AppUtil.showToast(msg || 'Terjadi kesalahan saat menyimpan data pembeli.', { type: 'error' });
                } else {
                    alert(msg);
                }
            }
        });
    }
});
</script>
<?php render_alamat_script(''); ?>
<?php if ($edit_customer): ?>
<script>
$(function () {
    var $modalElement = $('#customerModal');
    if ($modalElement.length > 0 && typeof bootstrap !== 'undefined') {
        var modal = new bootstrap.Modal($modalElement[0]);
        modal.show();
    }
});
</script>
<?php endif; ?>
