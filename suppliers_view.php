<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success !== ''): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0">Pemasok</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#supplierModal">
        Tambah Pemasok
    </button>
</div>
<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2" method="get" action="suppliers.php">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Cari nama pemasok" value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>">
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
                    <option value="dual" <?php echo $role === 'dual' ? 'selected' : ''; ?>>Juga pembeli</option>
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
        <?php if (empty($suppliers)): ?>
            <p class="mb-0">Belum ada data pemasok.</p>
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
                    <?php foreach ($suppliers as $s): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($s['nama_lengkap'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($s['kontak'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($s['alamat'], ENT_QUOTES, 'UTF-8')); ?></td>
                        <td>
                            <span class="badge bg-primary">Pemasok</span>
                            <?php if (isset($s['is_customer']) && (int)$s['is_customer'] === 1): ?>
                                <span class="badge bg-info text-dark ms-1">Juga pembeli</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ((int)$s['is_active'] === 1): ?>
                                <span class="badge bg-success supplier-status-badge" data-status="1">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary supplier-status-badge" data-status="0">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="suppliers.php?id=<?php echo (int)$s['id_orang']; ?>&status=<?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>&q=<?php echo urlencode($q); ?>" class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>
                                <?php
                                $isActive = (int)$s['is_active'] === 1;
                                $btnClass = $isActive ? 'btn-outline-warning' : 'btn-outline-success';
                                $btnText = $isActive ? 'Nonaktifkan' : 'Aktifkan';
                                ?>
                                <button
                                    type="button"
                                    class="btn btn-sm supplier-status-toggle <?php echo $btnClass; ?>"
                                    data-supplier-id="<?php echo (int)$s['id_orang']; ?>"
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
            Pemasok yang dinonaktifkan tidak dapat dipilih pada transaksi pembelian baru, tetapi tetap tercatat pada transaksi yang sudah ada.
        </div>
    </div>
</div>
<div class="modal fade" id="supplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $edit_supplier ? 'Edit Pemasok' : 'Tambah Pemasok'; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" autocomplete="off">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="supplier_id" value="<?php echo $edit_supplier ? (int)$edit_supplier['id_orang'] : 0; ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Pemasok (Orang/Usaha/Yayasan/Institusi)</label>
                        <input type="text" name="nama" class="form-control" required value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['nama_lengkap'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kontak</label>
                        <input type="text" name="kontak" class="form-control" value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['kontak'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"><?php echo $edit_supplier ? htmlspecialchars($edit_supplier['alamat'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="also_customer" name="also_customer" <?php echo $edit_supplier && isset($edit_supplier['is_customer']) && (int)$edit_supplier['is_customer'] === 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="also_customer">
                            Juga sebagai pembeli
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo !$edit_supplier || (int)$edit_supplier['is_active'] === 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">
                            Aktif
                        </label>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary"><?php echo $edit_supplier ? 'Simpan Perubahan' : 'Tambah Pemasok'; ?></button>
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
            buttonSelector: '.supplier-status-toggle',
            confirmMessage: 'Apakah Anda yakin ingin mengubah status pemasok ini?',
            url: 'suppliers.php',
            idDataAttribute: 'supplier-id',
            idParamName: 'supplier_id',
            defaultErrorMessage: 'Gagal mengubah status pemasok.',
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
                var $badge = $row.find('.supplier-status-badge');
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

    var $modalElement = $('#supplierModal');
    if ($modalElement.length > 0 && typeof bootstrap !== 'undefined' && <?php echo $edit_supplier ? 'true' : 'false'; ?>) {
        var modal = new bootstrap.Modal($modalElement[0]);
        modal.show();
    }

    if (typeof AppUtil !== 'undefined' && typeof AppUtil.handleLargeForm === 'function') {
        AppUtil.handleLargeForm({
            formSelector: '#supplierModal form',
            ajaxUrl: 'suppliers.php',
            parseJson: true,
            beforeSubmit: function ($form) {
                if ($form.find('input[name="ajax"]').length === 0) {
                    $('<input type="hidden" name="ajax" value="1">').appendTo($form);
                }
                return true;
            },
            onSuccess: function (resp) {
                if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                    AppUtil.showToast(resp.message || 'Data pemasok berhasil disimpan.', { type: 'success' });
                }
                var modalEl = document.getElementById('supplierModal');
                if (modalEl && typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function') {
                    var instance = bootstrap.Modal.getInstance(modalEl);
                    if (!instance) {
                        instance = new bootstrap.Modal(modalEl);
                    }
                    instance.hide();
                }
                window.location.href = 'suppliers.php';
            },
            onError: function (msg) {
                if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
                    AppUtil.showToast(msg || 'Terjadi kesalahan saat menyimpan data pemasok.', { type: 'error' });
                } else {
                    alert(msg);
                }
            }
        });
    }
});
</script>
