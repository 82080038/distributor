<?php
require_once __DIR__ . '/auth.php';
require_login();

$user = current_user();
$page_title = 'Dashboard';
$content_view = 'index_view.php';

include __DIR__ . '/template.php';

