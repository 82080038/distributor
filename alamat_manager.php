<?php
/**
 * File Khusus Manajemen Alamat
 * Berisi fungsi-fungsi dan helper untuk alamat yang bisa di-include ke berbagai file
 * 
 * @version 1.0.0
 * @author Distribusi System
 */

// Cek apakah file sudah di-include sebelumnya
if (!defined('ALAMAT_HELPER_LOADED')) {
    define('ALAMAT_HELPER_LOADED', true);
    
    /**
     * Render form alamat lengkap dengan layout yang seragam
     * 
     * @param string $prefix Prefix untuk nama field (kosong jika tidak ada)
     * @param array $values Nilai default untuk field
     * @param bool $required Apakah field wajib diisi
     * @param bool $show_tipe_alamat Apakah menampilkan field tipe alamat
     * @param bool $show_kode_pos Apakah menampilkan field kode pos
     */
    function render_alamat_form($prefix = '', $values = [], $required = true, $show_tipe_alamat = true, $show_kode_pos = true) {
        $req_attr = $required ? 'required' : '';
        $req_class = $required ? 'required' : '';
        
        $province_id = $values['province_id'] ?? 0;
        $regency_id = $values['regency_id'] ?? 0;
        $district_id = $values['district_id'] ?? 0;
        $village_id = $values['village_id'] ?? 0;
        $street_address = $values['street_address'] ?? '';
        $tipe_alamat = $values['tipe_alamat'] ?? '';
        $postal_code = $values['postal_code'] ?? '';
        
        echo '<div class="alamat-form-container" data-prefix="' . htmlspecialchars($prefix) . '">';
        echo '<div class="card border-0 shadow-sm">';
        echo '<div class="card-header bg-light">';
        echo '<h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Informasi Alamat</h6>';
        echo '</div>';
        echo '<div class="card-body">';
        
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
        echo '<div class="village-dropdown dropdown-menu" style="max-height: 200px; overflow-y: auto; width: 100%;"></div>';
        echo '</div>';
        echo '<small class="text-muted">Ketik nama desa untuk menampilkan pilihan yang sesuai</small>';
        echo '</div>';
        echo '</div>';
        
        // Tipe Alamat dan Kode Pos (baris kedua)
        if ($show_tipe_alamat || $show_kode_pos) {
            echo '<div class="row mb-3">';
            
            if ($show_tipe_alamat) {
                echo '<div class="col-md-6 mb-3">';
                echo '<label class="form-label">Tipe Alamat</label>';
                echo '<select name="' . $prefix . 'tipe_alamat" id="' . $prefix . 'tipe_alamat" class="form-select">';
                echo '<option value="">Pilih Tipe Alamat</option>';
                echo '<option value="rumah" ' . ($tipe_alamat == 'rumah' ? 'selected' : '') . '>🏠 Rumah</option>';
                echo '<option value="kantor" ' . ($tipe_alamat == 'kantor' ? 'selected' : '') . '>🏢 Kantor</option>';
                echo '<option value="gudang" ' . ($tipe_alamat == 'gudang' ? 'selected' : '') . '>📦 Gudang</option>';
                echo '<option value="toko" ' . ($tipe_alamat == 'toko' ? 'selected' : '') . '>🏪 Toko</option>';
                echo '<option value="pabrik" ' . ($tipe_alamat == 'pabrik' ? 'selected' : '') . '>🏭 Pabrik</option>';
                echo '<option value="lainnya" ' . ($tipe_alamat == 'lainnya' ? 'selected' : '') . '>📍 Lainnya</option>';
                echo '</select>';
                echo '</div>';
            }
            
            if ($show_kode_pos) {
                $col_class = $show_tipe_alamat ? 'col-md-6' : 'col-md-12';
                echo '<div class="' . $col_class . ' mb-3">';
                echo '<label class="form-label">Kode Pos</label>';
                echo '<div class="input-group">';
                echo '<span class="input-group-text"><i class="fas fa-envelope"></i></span>';
                echo '<input type="text" name="' . $prefix . 'postal_code" id="' . $prefix . 'postal_code" ';
                echo 'class="form-control" value="' . htmlspecialchars($postal_code, ENT_QUOTES, 'UTF-8') . '" readonly placeholder="Akan terisi otomatis">';
                echo '</div>';
                echo '</div>';
            }
            
            echo '</div>';
        }
        
        // Alamat Jalan (paling bawah)
        echo '<div class="mb-3">';
        echo '<label class="form-label">Alamat Jalan Lengkap <span class="text-danger">*</span></label>';
        echo '<textarea name="' . $prefix . 'street_address" id="' . $prefix . 'street_address" ';
        echo 'class="form-control ' . $req_class . '" rows="3" placeholder="Contoh: Jl. Merdeka No. 123, RT 001/RW 002, Dekat Masjid" ' . $req_attr . '>' . htmlspecialchars($street_address, ENT_QUOTES, 'UTF-8') . '</textarea>';
        echo '<small class="text-muted">Masukkan alamat jalan lengkap termasuk nomor rumah, RT/RW, dan patokan lainnya</small>';
        echo '</div>';
        
        echo '</div>'; // card-body
        echo '</div>'; // card
        echo '</div>'; // alamat-form-container
    }
    
    /**
     * Render JavaScript untuk autocomplete alamat
     * 
     * @param string $prefix Prefix untuk field ID
     */
    function render_alamat_script($prefix = '') {
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
                resetSelect($regency, 'Pilih Kabupaten/Kota');
                resetSelect($district, 'Pilih Kecamatan');
                resetVillage();
                
                if (provinceId) {
                    loadAlamatData('get_regencies', {province_id: provinceId}, function(data) {
                        populateSelect($regency, data, 'Pilih Kabupaten/Kota');
                    });
                }
            });
            
            // Load districts
            $regency.on('change', function() {
                var regencyId = $(this).val();
                resetSelect($district, 'Pilih Kecamatan');
                resetVillage();
                
                if (regencyId) {
                    loadAlamatData('get_districts', {regency_id: regencyId}, function(data) {
                        populateSelect($district, data, 'Pilih Kecamatan');
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
                
                // Trigger change event for other listeners
                $villageId.trigger('change');
            });
            
            // Hide dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.position-relative').length) {
                    $villageDropdown.hide();
                }
            });
            
            // Helper functions
            function loadAlamatData(action, params, callback) {
                $.ajax({
                    url: window.location.pathname,
                    method: 'GET',
                    data: {ajax: action, ...params},
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data) {
                            callback(response.data);
                        }
                    },
                    error: function() {
                        console.error('Failed to load ' + action);
                    }
                });
            }
            
            function loadVillages(districtId) {
                if (villageCache[districtId]) {
                    return;
                }
                
                loadAlamatData('get_villages', {district_id: districtId}, function(data) {
                    villageCache[districtId] = data;
                });
            }
            
            function displayVillageDropdown(villages) {
                $villageDropdown.empty();
                
                if (villages.length === 0) {
                    $villageDropdown.html('<div class="dropdown-item disabled">Tidak ada desa yang cocok</div>');
                } else {
                    villages.forEach(function(village) {
                        var $item = $('<a class="dropdown-item" href="#"></a>');
                        var displayText = village.name;
                        if (village.postal_code) {
                            displayText += ' (' + village.postal_code + ')';
                        }
                        $item.text(displayText);
                        $item.data('id', village.id);
                        $item.data('name', village.name);
                        $item.data('postal', village.postal_code);
                        $villageDropdown.append($item);
                    });
                }
                
                $villageDropdown.show();
            }
            
            function populateSelect($select, data, placeholder) {
                $select.empty();
                $select.append('<option value="">' + placeholder + '</option>');
                data.forEach(function(item) {
                    var $option = $('<option></option>');
                    $option.val(item.id);
                    $option.text(item.name);
                    $select.append($option);
                });
            }
            
            function resetSelect($select, placeholder) {
                $select.empty();
                $select.append('<option value="">' + placeholder + '</option>');
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
    
    /**
     * Validasi data alamat dari POST
     * 
     * @param string $prefix Prefix untuk nama field
     * @param bool $required Apakah field wajib diisi
     * @return array Hasil validasi
     */
    function validate_alamat_data($prefix = '', $required = true) {
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
    
    /**
     * Ambil kode pos berdasarkan ID desa
     * 
     * @param int $village_id ID desa
     * @return string Kode pos
     */
    function get_kode_pos_by_desa($village_id) {
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
    
    /**
     * Format alamat lengkap untuk ditampilkan
     * 
     * @param array $alamat_data Data alamat
     * @return string Alamat terformat
     */
    function format_alamat_lengkap($alamat_data) {
        $parts = [];
        
        if (!empty($alamat_data['street_address'])) {
            $parts[] = $alamat_data['street_address'];
        }
        
        if (!empty($alamat_data['village_name'])) {
            $parts[] = 'Desa/Kel. ' . $alamat_data['village_name'];
        }
        
        if (!empty($alamat_data['district_name'])) {
            $parts[] = 'Kec. ' . $alamat_data['district_name'];
        }
        
        if (!empty($alamat_data['regency_name'])) {
            $parts[] = $alamat_data['regency_name'];
        }
        
        if (!empty($alamat_data['province_name'])) {
            $parts[] = $alamat_data['province_name'];
        }
        
        if (!empty($alamat_data['postal_code'])) {
            $parts[] = $alamat_data['postal_code'];
        }
        
        return implode(', ', $parts);
    }
    
    /**
     * Setup AJAX endpoints untuk alamat (include di file yang membutuhkan)
     */
    function setup_alamat_ajax_endpoints() {
        global $conn_alamat;
        
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json; charset=utf-8');
            $ajaxMode = $_GET['ajax'];
            $response = ['success' => false, 'data' => []];
    
            if ($ajaxMode === 'get_regencies' && isset($_GET['province_id'])) {
                $provinceId = (int)$_GET['province_id'];
                if ($provinceId > 0 && isset($conn_alamat) && $conn_alamat->connect_error === null) {
                    $sql = "SELECT id, name FROM regencies WHERE province_id = ? ORDER BY name";
                    $stmt = $conn_alamat->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param('i', $provinceId);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        if ($res) {
                            $regencies = [];
                            while ($row = $res->fetch_assoc()) {
                                $regencies[] = $row;
                            }
                            $response['success'] = true;
                            $response['data'] = $regencies;
                        }
                        $stmt->close();
                    }
                }
            } elseif ($ajaxMode === 'get_districts' && isset($_GET['regency_id'])) {
                $regencyId = (int)$_GET['regency_id'];
                if ($regencyId > 0 && isset($conn_alamat) && $conn_alamat->connect_error === null) {
                    $sql = "SELECT id, name FROM districts WHERE regency_id = ? ORDER BY name";
                    $stmt = $conn_alamat->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param('i', $regencyId);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        if ($res) {
                            $districts = [];
                            while ($row = $res->fetch_assoc()) {
                                $districts[] = $row;
                            }
                            $response['success'] = true;
                            $response['data'] = $districts;
                        }
                        $stmt->close();
                    }
                }
            } elseif ($ajaxMode === 'get_villages' && isset($_GET['district_id'])) {
                $districtId = (int)$_GET['district_id'];
                if ($districtId > 0 && isset($conn_alamat) && $conn_alamat->connect_error === null) {
                    $sql = "SELECT id, name, postal_code FROM villages WHERE district_id = ? ORDER BY name";
                    $stmt = $conn_alamat->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param('i', $districtId);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        if ($res) {
                            $villages = [];
                            while ($row = $res->fetch_assoc()) {
                                $villages[] = $row;
                            }
                            $response['success'] = true;
                            $response['data'] = $villages;
                        }
                        $stmt->close();
                    }
                }
            }
    
            echo json_encode($response);
            exit;
        }
        
        // Handle CRUD AJAX requests
        if (isset($_GET['alamat_crud']) && isset($_GET['action'])) {
            global $conn;
            handle_alamat_crud_ajax($_GET['action'], $conn);
        }
    }
    
    /**
     * Load data alamat lengkap berdasarkan ID
     * 
     * @param int $entity_id ID entitas (user, customer, supplier)
     * @param string $entity_type Tipe entitas
     * @param mysqli $conn Koneksi database
     * @return array Data alamat
     */
    function load_alamat_by_entity($entity_id, $entity_type, $conn) {
        $sql = "";
        $params = [];
        $types = "";
        
        switch ($entity_type) {
            case 'user':
                $sql = "SELECT o.province_id, o.regency_id, o.district_id, o.village_id, 
                               o.alamat as street_address, o.tipe_alamat, o.postal_code,
                               p.name as province_name, r.name as regency_name, 
                               d.name as district_name, v.name as village_name
                        FROM user u
                        JOIN orang o ON u.id_orang = o.id_orang
                        LEFT JOIN alamat_db.provinces p ON o.province_id = p.id
                        LEFT JOIN alamat_db.regencies r ON o.regency_id = r.id
                        LEFT JOIN alamat_db.districts d ON o.district_id = d.id
                        LEFT JOIN alamat_db.villages v ON o.village_id = v.id
                        WHERE u.id_user = ?";
                $params = [$entity_id];
                $types = "i";
                break;
                
            case 'customer':
            case 'supplier':
                $sql = "SELECT province_id, regency_id, district_id, village_id, 
                               alamat as street_address, tipe_alamat, postal_code,
                               p.name as province_name, r.name as regency_name, 
                               d.name as district_name, v.name as village_name
                        FROM orang
                        LEFT JOIN alamat_db.provinces p ON province_id = p.id
                        LEFT JOIN alamat_db.regencies r ON regency_id = r.id
                        LEFT JOIN alamat_db.districts d ON district_id = d.id
                        LEFT JOIN alamat_db.villages v ON village_id = v.id
                        WHERE id_orang = ?";
                $params = [$entity_id];
                $types = "i";
                break;
        }
        
        if ($sql) {
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    $stmt->close();
                    return $row;
                }
                $stmt->close();
            }
        }
        
        return [];
    }
    
    /**
     * CRUD: Create alamat baru
     * 
     * @param array $data Data alamat
     * @param mysqli $conn Koneksi database
     * @return array Result dengan ID alamat yang dibuat
     */
    function create_alamat($data, $conn) {
        $sql = "INSERT INTO addresses (street_address, province_id, regency_id, district_id, village_id, postal_code, address_type, is_primary, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'other', 1, NOW())";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('siiiiiss', 
                $data['street_address'], 
                $data['province_id'], 
                $data['regency_id'], 
                $data['district_id'], 
                $data['village_id'], 
                $data['postal_code'],
                $data['address_type'] ?? 'other'
            );
            if ($stmt->execute()) {
                $alamat_id = $stmt->insert_id;
                $stmt->close();
                return ['success' => true, 'alamat_id' => $alamat_id, 'message' => 'Alamat berhasil dibuat'];
            }
            $stmt->close();
        }
        return ['success' => false, 'message' => 'Gagal membuat alamat'];
    }
    
    /**
     * CRUD: Update alamat
     * 
     * @param int $alamat_id ID alamat
     * @param array $data Data alamat baru
     * @param mysqli $conn Koneksi database
     * @return array Result update
     */
    function update_alamat($alamat_id, $data, $conn) {
        $sql = "UPDATE addresses SET street_address = ?, province_id = ?, regency_id = ?, district_id = ?, 
                    village_id = ?, postal_code = ?, address_type = ?, updated_at = NOW() 
                    WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('siiiiissi', 
                $data['street_address'], 
                $data['province_id'], 
                $data['regency_id'], 
                $data['district_id'], 
                $data['village_id'], 
                $data['postal_code'],
                $data['address_type'] ?? 'other',
                $alamat_id
            );
            if ($stmt->execute()) {
                $stmt->close();
                return ['success' => true, 'message' => 'Alamat berhasil diperbarui'];
            }
            $stmt->close();
        }
        return ['success' => false, 'message' => 'Gagal memperbarui alamat'];
    }
    
    /**
     * CRUD: Delete alamat
     * 
     * @param int $alamat_id ID alamat
     * @param mysqli $conn Koneksi database
     * @return array Result delete
     */
    function delete_alamat($alamat_id, $conn) {
        // Soft delete dengan menghubungkan ke orang_addresses
        $sql = "UPDATE orang_addresses SET is_active = 0 WHERE address_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $alamat_id);
            if ($stmt->execute()) {
                $stmt->close();
                return ['success' => true, 'message' => 'Alamat berhasil dihapus'];
            }
            $stmt->close();
        }
        return ['success' => false, 'message' => 'Gagal menghapus alamat'];
    }
    
    /**
     * CRUD: Hubungkan alamat dengan entity
     * 
     * @param int $entity_id ID entity (user, customer, supplier)
     * @param int $alamat_id ID alamat
     * @param string $entity_type Tipe entity
     * @param string $address_type Tipe alamat untuk entity
     * @param mysqli $conn Koneksi database
     * @return array Result
     */
    function link_alamat_to_entity($entity_id, $alamat_id, $entity_type, $address_type, $conn) {
        // Nonaktifkan alamat lama untuk entity ini
        $sql_deactivate = "UPDATE orang_addresses SET is_active = 0 WHERE orang_id = ? AND address_type = ?";
        $stmt_deactivate = $conn->prepare($sql_deactivate);
        if ($stmt_deactivate) {
            $stmt_deactivate->bind_param('is', $entity_id, $address_type);
            $stmt_deactivate->execute();
            $stmt_deactivate->close();
        }
        
        // Hubungkan alamat baru
        $sql = "INSERT INTO orang_addresses (orang_id, address_id, address_type, is_active, created_at) 
                VALUES (?, ?, ?, 1, NOW())
                ON DUPLICATE KEY UPDATE is_active = 1, created_at = NOW()";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('iis', $entity_id, $alamat_id, $address_type);
            if ($stmt->execute()) {
                $stmt->close();
                
                // Update id_alamat_orang di tabel orang
                $sql_update = "UPDATE orang SET id_alamat_orang = ? WHERE id_orang = ?";
                $stmt_update = $conn->prepare($sql_update);
                if ($stmt_update) {
                    $stmt_update->bind_param('ii', $alamat_id, $entity_id);
                    $stmt_update->execute();
                    $stmt_update->close();
                }
                
                return ['success' => true, 'message' => 'Alamat berhasil dihubungkan'];
            }
            $stmt->close();
        }
        return ['success' => false, 'message' => 'Gagal menghubungkan alamat'];
    }
    
    /**
     * CRUD: Get alamat by ID
     * 
     * @param int $alamat_id ID alamat
     * @param mysqli $conn Koneksi database
     * @return array Data alamat
     */
    function get_alamat_by_id($alamat_id, $conn) {
        $sql = "SELECT a.*, 
                       p.name as province_name, r.name as regency_name, 
                       d.name as district_name, v.name as village_name
                FROM addresses a
                LEFT JOIN alamat_db.provinces p ON a.province_id = p.id
                LEFT JOIN alamat_db.regencies r ON a.regency_id = r.id
                LEFT JOIN alamat_db.districts d ON a.district_id = d.id
                LEFT JOIN alamat_db.villages v ON a.village_id = v.id
                WHERE a.id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $alamat_id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $stmt->close();
                return $row;
            }
            $stmt->close();
        }
        return [];
    }
    
    /**
     * CRUD: Get semua alamat untuk entity
     * 
     * @param int $entity_id ID entity
     * @param string $entity_type Tipe entity
     * @param mysqli $conn Koneksi database
     * @return array List alamat
     */
    function get_alamats_by_entity($entity_id, $entity_type, $conn) {
        $sql = "SELECT a.*, oa.address_type, oa.is_active,
                       p.name as province_name, r.name as regency_name, 
                       d.name as district_name, v.name as village_name
                FROM orang_addresses oa
                JOIN addresses a ON oa.address_id = a.id
                LEFT JOIN alamat_db.provinces p ON a.province_id = p.id
                LEFT JOIN alamat_db.regencies r ON a.regency_id = r.id
                LEFT JOIN alamat_db.districts d ON a.district_id = d.id
                LEFT JOIN alamat_db.villages v ON a.village_id = v.id
                WHERE oa.orang_id = ? AND oa.address_type = ?
                ORDER BY oa.is_active DESC, a.created_at DESC";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('is', $entity_id, $entity_type);
            $stmt->execute();
            $res = $stmt->get_result();
            $alamats = [];
            while ($row = $res->fetch_assoc()) {
                $alamats[] = $row;
            }
            $stmt->close();
            return $alamats;
        }
        return [];
    }
    
    /**
     * CRUD: Update alamat utama untuk entity
     * 
     * @param int $entity_id ID entity
     * @param int $alamat_id ID alamat yang akan dijadikan utama
     * @param string $entity_type Tipe entity
     * @param mysqli $conn Koneksi database
     * @return array Result
     */
    function set_primary_alamat($entity_id, $alamat_id, $entity_type, $conn) {
        // Reset semua alamat menjadi non-utama
        $sql_reset = "UPDATE addresses a
                      JOIN orang_addresses oa ON a.id = oa.address_id
                      SET a.is_primary = 0
                      WHERE oa.orang_id = ? AND oa.address_type = ?";
        $stmt_reset = $conn->prepare($sql_reset);
        if ($stmt_reset) {
            $stmt_reset->bind_param('is', $entity_id, $entity_type);
            $stmt_reset->execute();
            $stmt_reset->close();
        }
        
        // Set alamat yang dipilih menjadi utama
        $sql_set = "UPDATE addresses a
                    JOIN orang_addresses oa ON a.id = oa.address_id
                    SET a.is_primary = 1
                    WHERE oa.orang_id = ? AND oa.address_id = ? AND oa.address_type = ?";
        $stmt_set = $conn->prepare($sql_set);
        if ($stmt_set) {
            $stmt_set->bind_param('iis', $entity_id, $alamat_id, $entity_type);
            if ($stmt_set->execute()) {
                $stmt_set->close();
                return ['success' => true, 'message' => 'Alamat utama berhasil diperbarui'];
            }
            $stmt_set->close();
        }
        return ['success' => false, 'message' => 'Gagal memperbarui alamat utama'];
    }
    
    /**
     * CRUD: Get alamat utama untuk entity
     * 
     * @param int $entity_id ID entity
     * @param string $entity_type Tipe entity
     * @param mysqli $conn Koneksi database
     * @return array Data alamat utama
     */
    function get_primary_alamat($entity_id, $entity_type, $conn) {
        $sql = "SELECT a.*, oa.address_type,
                       p.name as province_name, r.name as regency_name, 
                       d.name as district_name, v.name as village_name
                FROM addresses a
                JOIN orang_addresses oa ON a.id = oa.address_id
                LEFT JOIN alamat_db.provinces p ON a.province_id = p.id
                LEFT JOIN alamat_db.regencies r ON a.regency_id = r.id
                LEFT JOIN alamat_db.districts d ON a.district_id = d.id
                LEFT JOIN alamat_db.villages v ON a.village_id = v.id
                WHERE oa.orang_id = ? AND oa.address_type = ? AND oa.is_active = 1 AND a.is_primary = 1
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('is', $entity_id, $entity_type);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $stmt->close();
                return $row;
            }
            $stmt->close();
        }
        return [];
    }
    
    /**
     * AJAX Handler untuk CRUD alamat
     * 
     * @param string $action Action yang akan dilakukan
     * @param mysqli $conn Koneksi database
     * @return array Response
     */
    function handle_alamat_crud_ajax($action, $conn) {
        header('Content-Type: application/json; charset=utf-8');
        
        $response = ['success' => false, 'message' => 'Action tidak valid'];
        
        switch ($action) {
            case 'create':
                $data = [
                    'street_address' => clean($_POST['street_address'] ?? ''),
                    'province_id' => (int)($_POST['province_id'] ?? 0),
                    'regency_id' => (int)($_POST['regency_id'] ?? 0),
                    'district_id' => (int)($_POST['district_id'] ?? 0),
                    'village_id' => (int)($_POST['village_id'] ?? 0),
                    'postal_code' => clean($_POST['postal_code'] ?? ''),
                    'address_type' => clean($_POST['address_type'] ?? 'other')
                ];
                $response = create_alamat($data, $conn);
                break;
                
            case 'update':
                $alamat_id = (int)($_POST['alamat_id'] ?? 0);
                $data = [
                    'street_address' => clean($_POST['street_address'] ?? ''),
                    'province_id' => (int)($_POST['province_id'] ?? 0),
                    'regency_id' => (int)($_POST['regency_id'] ?? 0),
                    'district_id' => (int)($_POST['district_id'] ?? 0),
                    'village_id' => (int)($_POST['village_id'] ?? 0),
                    'postal_code' => clean($_POST['postal_code'] ?? ''),
                    'address_type' => clean($_POST['address_type'] ?? 'other')
                ];
                $response = update_alamat($alamat_id, $data, $conn);
                break;
                
            case 'delete':
                $alamat_id = (int)($_POST['alamat_id'] ?? 0);
                $response = delete_alamat($alamat_id, $conn);
                break;
                
            case 'link':
                $entity_id = (int)($_POST['entity_id'] ?? 0);
                $alamat_id = (int)($_POST['alamat_id'] ?? 0);
                $entity_type = clean($_POST['entity_type'] ?? '');
                $address_type = clean($_POST['address_type'] ?? 'other');
                $response = link_alamat_to_entity($entity_id, $alamat_id, $entity_type, $address_type, $conn);
                break;
                
            case 'set_primary':
                $entity_id = (int)($_POST['entity_id'] ?? 0);
                $alamat_id = (int)($_POST['alamat_id'] ?? 0);
                $entity_type = clean($_POST['entity_type'] ?? '');
                $response = set_primary_alamat($entity_id, $alamat_id, $entity_type, $conn);
                break;
                
            case 'get':
                $alamat_id = (int)($_GET['alamat_id'] ?? 0);
                $alamat_data = get_alamat_by_id($alamat_id, $conn);
                $response = ['success' => true, 'data' => $alamat_data];
                break;
                
            case 'list':
                $entity_id = (int)($_GET['entity_id'] ?? 0);
                $entity_type = clean($_GET['entity_type'] ?? '');
                $alamats = get_alamats_by_entity($entity_id, $entity_type, $conn);
                $response = ['success' => true, 'data' => $alamats];
                break;
        }
        
        echo json_encode($response);
        exit;
    }
    
} // End check ALAMAT_HELPER_LOADED
?>
