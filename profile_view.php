<?php if (isset($error) && $error !== ''): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<?php if (isset($success) && $success !== ''): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<div class="row">
    <div class="col-md-6 mb-3">
        <h1 class="h3 mb-3">Profil</h1>
        <ul class="list-group">
            <li class="list-group-item">
                <strong>Nama:</strong> <?php echo $profile ? $profile['nama_lengkap'] : $user['name']; ?>
            </li>
            <li class="list-group-item">
                <strong>Username:</strong> <?php echo $profile ? $profile['username'] : $user['username']; ?>
            </li>
            <li class="list-group-item">
                <strong>Role:</strong> <?php echo $profile ? $profile['role_name'] : $user['role']; ?>
            </li>
        </ul>
    </div>
    <?php if ($profile): ?>
    <div class="col-md-6 mb-3">
        <h2 class="h5 mb-2">Alamat Perusahaan</h2>
        <ul class="list-group">
            <?php if (!empty($profile['perusahaan_nama'])): ?>
            <li class="list-group-item">
                <strong>Nama Perusahaan:</strong> <?php echo $profile['perusahaan_nama']; ?>
            </li>
            <li class="list-group-item">
                <strong>Alamat Perusahaan:</strong> <?php echo $profile['perusahaan_alamat']; ?>
            </li>
            <li class="list-group-item">
                <strong>Kontak Perusahaan:</strong> <?php echo $profile['perusahaan_kontak']; ?>
            </li>
            <?php else: ?>
            <li class="list-group-item">
                <em>Data perusahaan belum tersedia.</em>
            </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="col-md-6 mb-3">
        <h2 class="h5 mb-2">Alamat Pribadi</h2>
        <form method="post" id="profileAddressForm" autocomplete="off">
            <ul class="list-group mb-3">
                <li class="list-group-item">
                    <label class="form-label mb-1"><strong>Nomor HP</strong></label>
                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($profile['kontak'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </li>
                <li class="list-group-item">
                    <label class="form-label mb-1"><strong>Provinsi</strong></label>
                    <select name="province_id" id="province_id" class="form-select" required>
                        <option value="">Pilih Provinsi</option>
                        <?php
                        $prov_sql = "SELECT id, name FROM provinces ORDER BY name";
                        $prov_res = $conn_alamat->query($prov_sql);
                        if ($prov_res) {
                            while ($p = $prov_res->fetch_assoc()) {
                                $pid = (int)$p['id'];
                                $selected = ($profile['province_id'] == $pid) ? ' selected' : '';
                                echo '<option value="' . $pid . '"' . $selected . '>' . htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') . '</option>';
                            }
                        }
                        ?>
                    </select>
                </li>
                <li class="list-group-item">
                    <label class="form-label mb-1"><strong>Kabupaten / Kota</strong></label>
                    <select name="regency_id" id="regency_id" class="form-select" required>
                        <option value="">Pilih Provinsi terlebih dahulu</option>
                    </select>
                </li>
                <li class="list-group-item">
                    <label class="form-label mb-1"><strong>Kecamatan</strong></label>
                    <select name="district_id" id="district_id" class="form-select" required>
                        <option value="">Pilih Kabupaten/Kota terlebih dahulu</option>
                    </select>
                </li>
                <li class="list-group-item">
                    <label class="form-label mb-1"><strong>Kelurahan / Desa</strong></label>
                    <select name="village_id" id="village_id" class="form-select">
                        <option value="">Pilih Kecamatan terlebih dahulu</option>
                    </select>
                </li>
                <li class="list-group-item">
                    <label class="form-label mb-1"><strong>Alamat Jalan</strong></label>
                    <input type="text" name="alamat" class="form-control" value="<?php echo htmlspecialchars($profile['alamat'], ENT_QUOTES, 'UTF-8'); ?>" required>
                </li>
                <li class="list-group-item">
                    <label class="form-label mb-1"><strong>Kode Pos</strong></label>
                    <input type="text" name="postal_code" id="postal_code" class="form-control" value="<?php echo htmlspecialchars($profile['postal_code'], ENT_QUOTES, 'UTF-8'); ?>" readonly required>
                </li>
            </ul>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Simpan Alamat Pribadi</button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>
<?php if ($profile): ?>
<script>
$(function () {
    var $form = $('#profileAddressForm');
    if ($form.length === 0) {
        return;
    }
    var $provinceSelect = $('#province_id');
    var $regencySelect = $('#regency_id');
    var $districtSelect = $('#district_id');
    var $villageSelect = $('#village_id');
    var $postalInput = $('#postal_code');

    var currentProvinceId = <?php echo (int)$profile['province_id']; ?>;
    var currentRegencyId = <?php echo (int)$profile['regency_id']; ?>;
    var currentDistrictId = <?php echo (int)$profile['district_id']; ?>;
    var currentVillageId = <?php echo (int)$profile['village_id']; ?>;

    if (currentProvinceId) {
        $provinceSelect.val(String(currentProvinceId));
        AppUtil.loadOptions({
            url: 'profile.php?alamat_action=kabupaten&province_id=' + encodeURIComponent(currentProvinceId),
            $select: $regencySelect,
            placeholder: 'Pilih Kabupaten/Kota',
            selectedId: currentRegencyId,
            nextLoader: function () {
                if (currentRegencyId) {
                    AppUtil.loadOptions({
                        url: 'profile.php?alamat_action=kecamatan&regency_id=' + encodeURIComponent(currentRegencyId),
                        $select: $districtSelect,
                        placeholder: 'Pilih Kecamatan',
                        selectedId: currentDistrictId,
                        nextLoader: function () {
                            if (currentDistrictId) {
                                AppUtil.loadOptions({
                                    url: 'profile.php?alamat_action=desa&district_id=' + encodeURIComponent(currentDistrictId),
                                    $select: $villageSelect,
                                    placeholder: 'Pilih Kelurahan/Desa',
                                    selectedId: currentVillageId
                                });
                            }
                        }
                    });
                }
            }
        });
    }

    $provinceSelect.on('change', function () {
        var provId = $(this).val();
        AppUtil.resetSelect($regencySelect, 'Pilih Kabupaten/Kota');
        AppUtil.resetSelect($districtSelect, 'Pilih Kecamatan');
        AppUtil.resetSelect($villageSelect, 'Pilih Kelurahan/Desa');
        $postalInput.val('');
        if (provId) {
            AppUtil.loadOptions({
                url: 'profile.php?alamat_action=kabupaten&province_id=' + encodeURIComponent(provId),
                $select: $regencySelect,
                placeholder: 'Pilih Kabupaten/Kota'
            });
        }
    });

    $regencySelect.on('change', function () {
        var regId = $(this).val();
        AppUtil.resetSelect($districtSelect, 'Pilih Kecamatan');
        AppUtil.resetSelect($villageSelect, 'Pilih Kelurahan/Desa');
        $postalInput.val('');
        if (regId) {
            AppUtil.loadOptions({
                url: 'profile.php?alamat_action=kecamatan&regency_id=' + encodeURIComponent(regId),
                $select: $districtSelect,
                placeholder: 'Pilih Kecamatan'
            });
        }
    });

    $districtSelect.on('change', function () {
        var distId = $(this).val();
        AppUtil.resetSelect($villageSelect, 'Pilih Kelurahan/Desa');
        $postalInput.val('');
        if (distId) {
            AppUtil.loadOptions({
                url: 'profile.php?alamat_action=desa&district_id=' + encodeURIComponent(distId),
                $select: $villageSelect,
                placeholder: 'Pilih Kelurahan/Desa'
            });
        }
    });

    $villageSelect.on('change', function () {
        var villId = $(this).val();
        $postalInput.val('');
        if (villId) {
            $.getJSON('profile.php?alamat_action=kodepos&village_id=' + encodeURIComponent(villId), function (data) {
                if (data.length > 0) {
                    $postalInput.val(data[0].name);
                }
            });
        }
    });

    AppUtil.setupFocusNavigation($form);
});
</script>
<?php endif; ?>
