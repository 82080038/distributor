<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
require_login();

$page_title = 'Laporan Omzet';
$content_view = 'report_omzet_view.php';

include __DIR__ . DIRECTORY_SEPARATOR . 'template.php';

