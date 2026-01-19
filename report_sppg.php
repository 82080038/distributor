<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
require_login();

$page_title = 'Laporan SPPG';
$content_view = 'report_sppg_view.php';

include __DIR__ . DIRECTORY_SEPARATOR . 'template.php';

