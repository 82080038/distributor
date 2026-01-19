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
                <?php
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'address_helper.php';
                $address_values = [
                    'province_id' => $profile['province_id'],
                    'regency_id' => $profile['regency_id'],
                    'district_id' => $profile['district_id'],
                    'village_id' => $profile['village_id'],
                    'street_address' => $profile['alamat'],
                    'postal_code' => $profile['postal_code'],
                    'tipe_alamat' => $profile['tipe_alamat'] ?? ''
                ];
                render_address_fields('', $address_values, true, true);
                ?>
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
<?php render_address_script(''); ?>
</script>
<?php endif; ?>
