<?php
// Fungsi helper untuk alamat yang seragam di seluruh aplikasi

function render_address_fields($prefix = '', $values = [], $required = true, $show_tipe_alamat = true) {
    $req_attr = $required ? 'required' : '';
    $req_class = $required ? 'required' : '';
    
    $province_id = $values['province_id'] ?? 0;
    $regency_id = $values['regency_id'] ?? 0;
    $district_id = $values['district_id'] ?? 0;
    $village_id = $values['village_id'] ?? 0;
    $street_address = $values['street_address'] ?? '';
    $tipe_alamat = $values['tipe_alamat'] ?? '';
    $postal_code = $values['postal_code'] ?? '';
    
    echo '<div class="address-fields-container" data-prefix="' . htmlspecialchars($prefix) . '">';
    
    // Combo Propinsi - Kabupaten - Kecamatan - Desa (paling atas)
    echo '<div class="row mb-3">';
    echo '<div class="col-md-6 mb-3">';
    echo '<label class="form-label">Propinsi <span class="text-danger">*</span></label>';
    echo '<select name="' . $prefix . 'province_id" id="' . $prefix . 'province_id" class="form-select ' . $req_class . '" ' . $req_attr . '>';
    echo '<option value="">Pilih Propinsi</option>';
    global $conn_alamat;
    if ($conn_alamat) {
        $prov_sql = "SELECT id, name FROM provinces ORDER BY name";
        $prov_res = $conn_alamat->query($prov_sql);
        if ($prov_res) {
            while ($p = $prov_res->fetch_assoc()) {
                $selected = ($province_id == $p['id']) ? 'selected' : '';
                echo '<option value="' . (int)$p['id'] . '" ' . $selected . '>' . htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') . '</option>';
            }
        }
    }
    echo '</select>';
    echo '</div>';
    
    echo '<div class="col-md-6 mb-3">';
    echo '<label class="form-label">Kabupaten / Kota <span class="text-danger">*</span></label>';
    echo '<select name="' . $prefix . 'regency_id" id="' . $prefix . 'regency_id" class="form-select ' . $req_class . '" ' . $req_attr . '>';
    echo '<option value="">Pilih Propinsi terlebih dahulu</option>';
    if ($province_id > 0) {
        $stmt = $conn_alamat->prepare("SELECT id, name FROM regencies WHERE province_id = ? ORDER BY name");
        if ($stmt) {
            $stmt->bind_param('i', $province_id);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $selected = ($regency_id == $row['id']) ? 'selected' : '';
                echo '<option value="' . (int)$row['id'] . '" ' . $selected . '>' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</option>';
            }
            $stmt->close();
        }
    }
    echo '</select>';
    echo '</div>';
    echo '</div>';
    
    echo '<div class="row mb-3">';
    echo '<div class="col-md-6 mb-3">';
    echo '<label class="form-label">Kecamatan <span class="text-danger">*</span></label>';
    echo '<select name="' . $prefix . 'district_id" id="' . $prefix . 'district_id" class="form-select ' . $req_class . '" ' . $req_attr . '>';
    echo '<option value="">Pilih Kabupaten/Kota terlebih dahulu</option>';
    if ($regency_id > 0) {
        $stmt = $conn_alamat->prepare("SELECT id, name FROM districts WHERE regency_id = ? ORDER BY name");
        if ($stmt) {
            $stmt->bind_param('i', $regency_id);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $selected = ($district_id == $row['id']) ? 'selected' : '';
                echo '<option value="' . (int)$row['id'] . '" ' . $selected . '>' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</option>';
            }
            $stmt->close();
        }
    }
    echo '</select>';
    echo '</div>';
    
    echo '<div class="col-md-6 mb-3">';
    echo '<label class="form-label">Kelurahan / Desa <span class="text-danger">*</span></label>';
    echo '<div class="position-relative">';
    echo '<input type="text" name="' . $prefix . 'village_search" id="' . $prefix . 'village_search" ';
    echo 'class="form-control ' . $req_class . '" placeholder="Ketik untuk mencari desa..." ' . $req_attr . '>';
    echo '<input type="hidden" name="' . $prefix . 'village_id" id="' . $prefix . 'village_id" value="' . (int)$village_id . '">';
    echo '<div class="village-dropdown dropdown-menu" style="max-height: 200px; overflow-y: auto;"></div>';
    echo '</div>';
    echo '<small class="text-muted">Ketik nama desa untuk menampilkan pilihan yang sesuai</small>';
    echo '</div>';
    echo '</div>';
    
    // Tipe Alamat (di bawah combo wilayah)
    if ($show_tipe_alamat) {
        echo '<div class="row mb-3">';
        echo '<div class="col-md-6 mb-3">';
        echo '<label class="form-label">Tipe Alamat</label>';
        echo '<select name="' . $prefix . 'tipe_alamat" id="' . $prefix . 'tipe_alamat" class="form-select">';
        echo '<option value="">Pilih Tipe Alamat</option>';
        echo '<option value="rumah" ' . ($tipe_alamat == 'rumah' ? 'selected' : '') . '>Rumah</option>';
        echo '<option value="kantor" ' . ($tipe_alamat == 'kantor' ? 'selected' : '') . '>Kantor</option>';
        echo '<option value="gudang" ' . ($tipe_alamat == 'gudang' ? 'selected' : '') . '>Gudang</option>';
        echo '<option value="toko" ' . ($tipe_alamat == 'toko' ? 'selected' : '') . '>Toko</option>';
        echo '<option value="pabrik" ' . ($tipe_alamat == 'pabrik' ? 'selected' : '') . '>Pabrik</option>';
        echo '<option value="lainnya" ' . ($tipe_alamat == 'lainnya' ? 'selected' : '') . '>Lainnya</option>';
        echo '</select>';
        echo '</div>';
        
        echo '<div class="col-md-6 mb-3">';
        echo '<label class="form-label">Kode Pos</label>';
        echo '<input type="text" name="' . $prefix . 'postal_code" id="' . $prefix . 'postal_code" ';
        echo 'class="form-control" value="' . htmlspecialchars($postal_code, ENT_QUOTES, 'UTF-8') . '" readonly>';
        echo '</div>';
        echo '</div>';
    }
    
    // Alamat Jalan (paling bawah)
    echo '<div class="mb-3">';
    echo '<label class="form-label">Alamat Jalan Lengkap <span class="text-danger">*</span></label>';
    echo '<textarea name="' . $prefix . 'street_address" id="' . $prefix . 'street_address" ';
    echo 'class="form-control ' . $req_class . '" rows="3" placeholder="Contoh: Jl. Merdeka No. 123, RT 001/RW 002" ' . $req_attr . '>' . htmlspecialchars($street_address, ENT_QUOTES, 'UTF-8') . '</textarea>';
    echo '<small class="text-muted">Masukkan alamat jalan lengkap termasuk nomor rumah, RT/RW, dan patokan lainnya</small>';
    echo '</div>';
    
    echo '</div>';
}

function render_address_script($prefix = '') {
    ?>
    <script>
    (function() {
        var prefix = '<?php echo $prefix; ?>';
        var $province = $('#' + prefix + 'province_id');
        var $regency = $('#' + prefix + 'regency_id');
        var $district = $('#' + prefix + 'district_id');
        var $villageSearch = $('#' + prefix + 'village_search');
        var $villageId = $('#' + prefix + 'village_id');
        var $villageDropdown = $villageSearch.next('.village-dropdown');
        var $postalCode = $('#' + prefix + 'postal_code');
        
        var villageCache = {};
        var currentDistrictId = 0;
        
        // Load regencies
        $province.on('change', function() {
            var provinceId = $(this).val();
            AppUtil.resetSelect($regency, 'Pilih Kabupaten/Kota');
            AppUtil.resetSelect($district, 'Pilih Kecamatan');
            resetVillage();
            
            if (provinceId) {
                AppUtil.loadOptions({
                    url: window.location.pathname + '?ajax=get_regencies&province_id=' + encodeURIComponent(provinceId),
                    $select: $regency,
                    placeholder: 'Pilih Kabupaten/Kota'
                });
            }
        });
        
        // Load districts
        $regency.on('change', function() {
            var regencyId = $(this).val();
            AppUtil.resetSelect($district, 'Pilih Kecamatan');
            resetVillage();
            
            if (regencyId) {
                AppUtil.loadOptions({
                    url: window.location.pathname + '?ajax=get_districts&regency_id=' + encodeURIComponent(regencyId),
                    $select: $district,
                    placeholder: 'Pilih Kecamatan'
                });
            }
        });
        
        // Load villages when district changes
        $district.on('change', function() {
            currentDistrictId = $(this).val() || 0;
            resetVillage();
            
            if (currentDistrictId > 0) {
                loadVillages(currentDistrictId);
            }
        });
        
        // Village search with autocomplete
        $villageSearch.on('input', function() {
            var searchTerm = $(this).val().toLowerCase();
            var districtId = $district.val();
            
            if (searchTerm.length < 2 || !districtId) {
                $villageDropdown.hide();
                return;
            }
            
            var villages = villageCache[districtId] || [];
            var filtered = villages.filter(function(village) {
                return village.name.toLowerCase().includes(searchTerm);
            });
            
            displayVillageDropdown(filtered);
        });
        
        // Village selection
        $villageDropdown.on('click', 'a', function(e) {
            e.preventDefault();
            var $item = $(this);
            var villageId = $item.data('id');
            var villageName = $item.data('name');
            var postalCode = $item.data('postal');
            
            $villageSearch.val(villageName);
            $villageId.val(villageId);
            $postalCode.val(postalCode || '');
            $villageDropdown.hide();
        });
        
        // Hide dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.position-relative').length) {
                $villageDropdown.hide();
            }
        });
        
        function loadVillages(districtId) {
            if (villageCache[districtId]) {
                return;
            }
            
            $.getJSON(window.location.pathname + '?ajax=get_villages&district_id=' + encodeURIComponent(districtId), function(data) {
                if (data.success && data.data) {
                    villageCache[districtId] = data.data;
                }
            });
        }
        
        function displayVillageDropdown(villages) {
            $villageDropdown.empty();
            
            if (villages.length === 0) {
                $villageDropdown.html('<div class="dropdown-item disabled">Tidak ada desa yang cocok</div>');
            } else {
                villages.forEach(function(village) {
                    var $item = $('<a class="dropdown-item" href="#"></a>');
                    $item.text(village.name + (village.postal_code ? ' (' + village.postal_code + ')' : ''));
                    $item.data('id', village.id);
                    $item.data('name', village.name);
                    $item.data('postal', village.postal_code);
                    $villageDropdown.append($item);
                });
            }
            
            $villageDropdown.show();
        }
        
        function resetVillage() {
            $villageSearch.val('');
            $villageId.val(0);
            $postalCode.val('');
            $villageDropdown.hide();
        }
        
        // Initialize village display if editing
        var initialVillageId = $villageId.val();
        var initialDistrictId = $district.val();
        if (initialVillageId && initialDistrictId) {
            loadVillages(initialDistrictId);
            // Set initial display after villages are loaded
            setTimeout(function() {
                var villages = villageCache[initialDistrictId] || [];
                var village = villages.find(function(v) { return v.id == initialVillageId; });
                if (village) {
                    $villageSearch.val(village.name);
                    $postalCode.val(village.postal_code || '');
                }
            }, 500);
        }
    })();
    </script>
    <?php
}

function validate_address_fields($prefix = '', $required = true) {
    $province_id = (int)($_POST[$prefix . 'province_id'] ?? 0);
    $regency_id = (int)($_POST[$prefix . 'regency_id'] ?? 0);
    $district_id = (int)($_POST[$prefix . 'district_id'] ?? 0);
    $village_id = (int)($_POST[$prefix . 'village_id'] ?? 0);
    $street_address = clean($_POST[$prefix . 'street_address'] ?? '');
    $tipe_alamat = clean($_POST[$prefix . 'tipe_alamat'] ?? '');
    $postal_code = clean($_POST[$prefix . 'postal_code'] ?? '');
    
    $errors = [];
    
    if ($required) {
        if ($province_id === 0) $errors[] = 'Propinsi wajib dipilih';
        if ($regency_id === 0) $errors[] = 'Kabupaten/Kota wajib dipilih';
        if ($district_id === 0) $errors[] = 'Kecamatan wajib dipilih';
        if ($village_id === 0) $errors[] = 'Kelurahan/Desa wajib dipilih';
        if ($street_address === '') $errors[] = 'Alamat jalan wajib diisi';
    }
    
    // Validate tipe_alamat if provided
    if ($tipe_alamat !== '') {
        $valid_types = ['rumah', 'kantor', 'gudang', 'toko', 'pabrik', 'lainnya'];
        if (!in_array($tipe_alamat, $valid_types)) {
            $errors[] = 'Tipe alamat tidak valid';
        }
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors,
        'data' => [
            'province_id' => $province_id,
            'regency_id' => $regency_id,
            'district_id' => $district_id,
            'village_id' => $village_id,
            'street_address' => $street_address,
            'tipe_alamat' => $tipe_alamat,
            'postal_code' => $postal_code
        ]
    ];
}

function get_postal_code_by_village($village_id) {
    global $conn_alamat;
    if (!$village_id || !$conn_alamat) return '';
    
    $stmt = $conn_alamat->prepare("SELECT postal_code FROM villages WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $village_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $stmt->close();
            return $row['postal_code'] ?? '';
        }
        $stmt->close();
    }
    return '';
}
?>
