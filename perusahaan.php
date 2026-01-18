<?php
require_once __DIR__ . '/auth.php';
require_login();
require_role(['owner']);

$error = '';
$success = '';
$perusahaan = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_perusahaan = clean($_POST['nama_perusahaan'] ?? '');
    $alamat = clean($_POST['alamat'] ?? '');
    $kontak = clean($_POST['kontak'] ?? '');

    if ($nama_perusahaan === '' || $alamat === '') {
        $error = 'Nama Perusahaan dan Alamat Perusahaan wajib diisi.';
    } else {
        $sql_get = "SELECT id_perusahaan FROM perusahaan ORDER BY id_perusahaan ASC LIMIT 1";
        $res_get = $conn->query($sql_get);
        if ($res_get && ($row_get = $res_get->fetch_assoc())) {
            $id_perusahaan = (int)$row_get['id_perusahaan'];
            $sql_update = "UPDATE perusahaan SET nama_perusahaan = ?, alamat = ?, kontak = ? WHERE id_perusahaan = ?";
            $stmt = $conn->prepare($sql_update);
            if ($stmt) {
                $stmt->bind_param('sssi', $nama_perusahaan, $alamat, $kontak, $id_perusahaan);
                if ($stmt->execute()) {
                    $success = 'Data perusahaan berhasil diperbarui.';
                } else {
                    $error = 'Gagal menyimpan perubahan data perusahaan.';
                }
                $stmt->close();
            } else {
                $error = 'Gagal menyiapkan query update perusahaan.';
            }
        } else {
            $sql_insert = "INSERT INTO perusahaan (nama_perusahaan, alamat, kontak) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql_insert);
            if ($stmt) {
                $stmt->bind_param('sss', $nama_perusahaan, $alamat, $kontak);
                if ($stmt->execute()) {
                    $success = 'Data perusahaan berhasil disimpan.';
                } else {
                    $error = 'Gagal menyimpan data perusahaan.';
                }
                $stmt->close();
            } else {
                $error = 'Gagal menyiapkan query insert perusahaan.';
            }
        }
    }
}

$sql_detail = "SELECT id_perusahaan, nama_perusahaan, alamat, kontak FROM perusahaan ORDER BY id_perusahaan ASC LIMIT 1";
$res_detail = $conn->query($sql_detail);
if ($res_detail && ($row_detail = $res_detail->fetch_assoc())) {
    $perusahaan = $row_detail;
}

$page_title = 'Data Perusahaan';
$content_view = 'perusahaan_view.php';

include __DIR__ . '/template.php';

