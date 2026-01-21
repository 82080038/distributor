<?php
// Start output buffering to prevent any accidental output
ob_start();

// Set headers to prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');

session_start();
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
require_login();

$user = current_user();
$page_title = 'Dashboard';
$content_view = 'index_view.php';

include __DIR__ . DIRECTORY_SEPARATOR . 'template.php';

