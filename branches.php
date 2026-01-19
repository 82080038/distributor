<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
require_login();

$page_title = 'Cabang';
$content_view = 'branches_view.php';

include __DIR__ . DIRECTORY_SEPARATOR . 'template.php';

