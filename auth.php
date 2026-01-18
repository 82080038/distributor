<?php
require_once __DIR__ . '/config.php';

function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function require_login()
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function current_user()
{
    if (!is_logged_in()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'name' => $_SESSION['name'] ?? '',
        'role' => $_SESSION['role'] ?? '',
        'branch_id' => $_SESSION['branch_id'] ?? null
    ];
}

function require_role(array $roles)
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
    $role = $_SESSION['role'] ?? '';
    if (!in_array($role, $roles, true)) {
        redirect('index.php');
    }
}

