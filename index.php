<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
require_login();

$user = current_user();
$page_title = 'Dashboard';
$content_view = 'index_view.php';

include __DIR__ . DIRECTORY_SEPARATOR . 'template.php';

