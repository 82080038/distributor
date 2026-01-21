<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">📍 Manajemen Alamat</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#alamatModal">
        <i class="fas fa-plus me-2"></i>Tambah Alamat
    </button>
</div>

<!-- Alamat List -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="alamatTable">
                <thead>
                    <tr>
                        <th>Tipe</th>
                        <th>Alamat Lengkap</th>
                        <th>Wilayah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan di-load via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Alamat -->
<div class="modal fade" id="alamatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alamatModalTitle">Tambah Alamat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="alamatForm" autocomplete="off">
                    <input type="hidden" name="alamat_id" id="alamat_id" value="0">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label class="form-label">Tipe Alamat</label>
                        <select name="address_type" id="address_type" class="form-select">
                            <option value="personal">🏠 Pribadi</option>
                            <option value="office">🏢 Kantor</option>
                            <option value="warehouse">📦 Gudang</option>
                            <option value="pickup">🚚 Titik Pickup</option>
                            <option value="delivery">📤 Titik Delivery</option>
                            <option value="other">📍 Lainnya</option>
                        </select>
                    </div>
                    
                    <?php
                    require_once __DIR__ . DIRECTORY_SEPARATOR . 'alamat_manager.php';
                    render_alamat_form('', [], true, true, true);
                    ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Hubungkan dengan Entity</label>
                        <select name="entity_type" id="entity_type" class="form-select mb-2">
                            <option value="">Pilih Entity</option>
                            <option value="user">User</option>
                            <option value="customer">Customer</option>
                            <option value="supplier">Supplier</option>
                        </select>
                        <input type="number" name="entity_id" id="entity_id" class="form-control" placeholder="ID Entity (opsional)">
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary">
                        <label class="form-check-label" for="is_primary">
                            Jadikan alamat utama
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="alamatForm" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan Alamat
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus alamat ini?</p>
                <p class="text-muted">Alamat akan dinonaktifkan dan tidak dapat digunakan lagi.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash me-2"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    let deleteAlamatId = 0;
    
    // Load alamat list
    loadAlamatList();
    
    // Form submit
    $('#alamatForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const action = formData.get('action');
        
        // Build data object for AJAX
        const data = {
            action: action,
            alamat_id: formData.get('alamat_id'),
            address_type: formData.get('address_type'),
            street_address: formData.get('street_address'),
            province_id: formData.get('province_id'),
            regency_id: formData.get('regency_id'),
            district_id: formData.get('district_id'),
            village_id: formData.get('village_id'),
            postal_code: formData.get('postal_code'),
            is_primary: formData.get('is_primary') ? 1 : 0
        };
        
        if (formData.get('entity_type') && formData.get('entity_id')) {
            data.entity_type = formData.get('entity_type');
            data.entity_id = formData.get('entity_id');
        }
        
        $.ajax({
            url: window.location.pathname + '?alamat_crud=1',
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast(response.message || 'Alamat berhasil disimpan', 'success');
                    $('#alamatModal').modal('hide');
                    resetAlamatForm();
                    loadAlamatList();
                } else {
                    showToast(response.message || 'Gagal menyimpan alamat', 'error');
                }
            },
            error: function() {
                showToast('Terjadi kesalahan saat menyimpan alamat', 'error');
            }
        });
    });
    
    // Edit alamat
    $(document).on('click', '.edit-alamat', function() {
        const alamatId = $(this).data('id');
        
        $.ajax({
            url: window.location.pathname + '?alamat_crud=1&action=get&alamat_id=' + alamatId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    $('#alamat_id').val(data.id);
                    $('#address_type').val(data.address_type);
                    $('#street_address').val(data.street_address);
                    $('#province_id').val(data.province_id);
                    $('#regency_id').val(data.regency_id);
                    $('#district_id').val(data.district_id);
                    $('#village_id').val(data.village_id);
                    $('#postal_code').val(data.postal_code);
                    $('#is_primary').prop('checked', data.is_primary == 1);
                    
                    $('#alamatForm input[name="action"]').val('update');
                    $('#alamatModalTitle').text('Edit Alamat');
                    $('#alamatModal').modal('show');
                    
                    // Load regencies, districts, villages
                    loadAlamatDependencies(data.province_id, data.regency_id, data.district_id);
                }
            },
            error: function() {
                showToast('Gagal memuat data alamat', 'error');
            }
        });
    });
    
    // Delete alamat
    $(document).on('click', '.delete-alamat', function() {
        deleteAlamatId = $(this).data('id');
        $('#deleteModal').modal('show');
    });
    
    $('#confirmDelete').on('click', function() {
        if (deleteAlamatId > 0) {
            $.ajax({
                url: window.location.pathname + '?alamat_crud=1',
                method: 'POST',
                data: {
                    action: 'delete',
                    alamat_id: deleteAlamatId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showToast(response.message || 'Alamat berhasil dihapus', 'success');
                        $('#deleteModal').modal('hide');
                        loadAlamatList();
                    } else {
                        showToast(response.message || 'Gagal menghapus alamat', 'error');
                    }
                },
                error: function() {
                    showToast('Terjadi kesalahan saat menghapus alamat', 'error');
                }
            });
        }
    });
    
    // Set primary alamat
    $(document).on('click', '.set-primary', function() {
        const alamatId = $(this).data('id');
        const entityType = $(this).data('entity-type');
        const entityId = $(this).data('entity-id');
        
        $.ajax({
            url: window.location.pathname + '?alamat_crud=1',
            method: 'POST',
            data: {
                action: 'set_primary',
                alamat_id: alamatId,
                entity_type: entityType,
                entity_id: entityId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showToast(response.message || 'Alamat utama berhasil diperbarui', 'success');
                    loadAlamatList();
                } else {
                    showToast(response.message || 'Gagal memperbarui alamat utama', 'error');
                }
            },
            error: function() {
                showToast('Terjadi kesalahan saat memperbarui alamat utama', 'error');
            }
        });
    });
    
    // Reset form when modal is hidden
    $('#alamatModal').on('hidden.bs.modal', function() {
        resetAlamatForm();
    });
    
    // Helper functions
    function loadAlamatList() {
        $.ajax({
            url: window.location.pathname + '?alamat_crud=1&action=list&entity_type=all',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderAlamatTable(response.data);
                }
            },
            error: function() {
                showToast('Gagal memuat daftar alamat', 'error');
            }
        });
    }
    
    function renderAlamatTable(alamats) {
        const tbody = $('#alamatTable tbody');
        tbody.empty();
        
        if (alamats.length === 0) {
            tbody.html('<tr><td colspan="5" class="text-center">Belum ada data alamat</td></tr>');
            return;
        }
        
        alamats.forEach(function(alamat) {
            const typeIcon = getTypeIcon(alamat.address_type);
            const statusBadge = alamat.is_active ? 
                '<span class="badge bg-success">Aktif</span>' : 
                '<span class="badge bg-secondary">Nonaktif</span>';
            const primaryBadge = alamat.is_primary ? 
                '<span class="badge bg-primary ms-1">Utama</span>' : '';
            
            const fullAddress = formatAlamatDisplay(alamat);
            
            const row = `
                <tr>
                    <td>${typeIcon} ${getAddressTypeLabel(alamat.address_type)}</td>
                    <td>${fullAddress}</td>
                    <td>${alamat.province_name || '-'}</td>
                    <td>${statusBadge}${primaryBadge}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary edit-alamat" data-id="${alamat.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-warning set-primary" 
                                    data-id="${alamat.id}" 
                                    data-entity-type="${alamat.address_type}" 
                                    data-entity-id="${alamat.orang_id || 0}"
                                    ${alamat.is_primary ? 'disabled' : ''}>
                                <i class="fas fa-star"></i>
                            </button>
                            <button class="btn btn-outline-danger delete-alamat" data-id="${alamat.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    
    function getTypeIcon(type) {
        const icons = {
            'personal': '🏠',
            'office': '🏢',
            'warehouse': '📦',
            'pickup': '🚚',
            'delivery': '📤',
            'other': '📍'
        };
        return icons[type] || '📍';
    }
    
    function getAddressTypeLabel(type) {
        const labels = {
            'personal': 'Pribadi',
            'office': 'Kantor',
            'warehouse': 'Gudang',
            'pickup': 'Pickup',
            'delivery': 'Delivery',
            'other': 'Lainnya'
        };
        return labels[type] || 'Lainnya';
    }
    
    function formatAlamatDisplay(alamat) {
        const parts = [];
        if (alamat.street_address) parts.push(alamat.street_address);
        if (alamat.village_name) parts.push('Desa/Kel. ' + alamat.village_name);
        if (alamat.district_name) parts.push('Kec. ' + alamat.district_name);
        if (alamat.regency_name) parts.push(alamat.regency_name);
        if (alamat.province_name) parts.push(alamat.province_name);
        if (alamat.postal_code) parts.push(alamat.postal_code);
        return parts.join(', ');
    }
    
    function resetAlamatForm() {
        $('#alamatForm')[0].reset();
        $('#alamat_id').val(0);
        $('#alamatForm input[name="action"]').val('create');
        $('#alamatModalTitle').text('Tambah Alamat');
        $('#is_primary').prop('checked', false);
    }
    
    function loadAlamatDependencies(provinceId, regencyId, districtId) {
        // Load regencies if province is selected
        if (provinceId) {
            $.ajax({
                url: window.location.pathname + '?ajax=get_regencies&province_id=' + provinceId,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        populateSelect('#regency_id', response.data);
                        if (regencyId) {
                            $('#regency_id').val(regencyId);
                            // Load districts
                            if (regencyId) {
                                $.ajax({
                                    url: window.location.pathname + '?ajax=get_districts&regency_id=' + regencyId,
                                    method: 'GET',
                                    dataType: 'json',
                                    success: function(response) {
                                        if (response.success) {
                                            populateSelect('#district_id', response.data);
                                            if (districtId) {
                                                $('#district_id').val(districtId);
                                                // Load villages
                                                if (districtId) {
                                                    $.ajax({
                                                        url: window.location.pathname + '?ajax=get_villages&district_id=' + districtId,
                                                        method: 'GET',
                                                        dataType: 'json',
                                                        success: function(response) {
                                                            if (response.success) {
                                                                // Set village search and id
                                                                const village = response.data.find(v => v.id == $('#village_id').val());
                                                                if (village) {
                                                                    $('#village_search').val(village.name);
                                                                    $('#village_id').val(village.id);
                                                                }
                                                            }
                                                        }
                                                    });
                                                }
                                            }
                                        }
                                    }
                                });
                            }
                        }
                    }
                }
            });
        }
    }
    
    function populateSelect(selector, data) {
        const $select = $(selector);
        $select.empty();
        $select.append('<option value="">Pilih...</option>');
        data.forEach(function(item) {
            $select.append(`<option value="${item.id}">${item.name}</option>`);
        });
    }
    
    function showToast(message, type) {
        // Implementasi toast notification
        if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
            AppUtil.showToast(message, { type: type });
        } else {
            alert(message);
        }
    }
});

<?php render_alamat_script(''); ?>
</script>
