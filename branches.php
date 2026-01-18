<?php
require_once __DIR__ . '/auth.php';
require_login();

$page_title = 'Cabang';
$content_view = 'branches_view.php';

include __DIR__ . '/template.php';

