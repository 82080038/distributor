<div class="row">
    <div class="col-md-6">
        <h1 class="h3 mb-3">Data Perusahaan</h1>
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success !== ''): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Nama Perusahaan</label>
                <input type="text" name="nama_perusahaan" class="form-control" required value="<?php echo $perusahaan ? htmlspecialchars($perusahaan['nama_perusahaan'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat Perusahaan</label>
                <textarea name="alamat" class="form-control" rows="3" required><?php echo $perusahaan ? htmlspecialchars($perusahaan['alamat'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Kontak Perusahaan</label>
                <input type="text" name="kontak" class="form-control" value="<?php echo $perusahaan ? htmlspecialchars($perusahaan['kontak'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

