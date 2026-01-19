<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
require_login();
require_once __DIR__ . DIRECTORY_SEPARATOR . 'alamat_manager.php';

// Setup AJAX endpoints untuk alamat
setup_alamat_ajax_endpoints();

$page_title = 'Manajemen Alamat';
$content_view = 'alamat_crud_view.php';

include __DIR__ . DIRECTORY_SEPARATOR . 'template.php';
?>
