<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
require_login();

$user = current_user();
$page_title = isset($page_title) ? $page_title : 'Sistem Distribusi';
$current_page = basename($_SERVER['PHP_SELF']);
$is_transaksi_active = in_array($current_page, ['purchases.php', 'sales.php'], true);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <script>
    (function () {
        var theme = 'light';
        try {
            var stored = localStorage.getItem('app_theme');
            if (stored === 'dark') {
                theme = 'dark';
            }
        } catch (e) {
        }
        document.documentElement.setAttribute('data-bs-theme', theme);
    })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css" rel="stylesheet">
    <style>
    body {
        background-color: var(--bs-body-bg);
        color: var(--bs-body-color);
        transition: background-color 0.25s ease, color 0.25s ease;
    }
    .navbar .nav-link,
    .navbar .navbar-brand {
        border-bottom: 2px solid transparent;
    }
    .navbar .nav-link.active,
    .navbar .navbar-brand.active,
    .navbar .nav-link.show {
        border-bottom-color: currentColor;
    }
    .navbar,
    .card,
    .btn,
    .form-control,
    .form-select,
    .table,
    .badge,
    .alert {
        transition: background-color 0.25s ease, color 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    }
    
    .dropdown-menu {
        transition: background-color 0.25s ease, color 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        z-index: 1000;
        position: absolute;
    }
    
    /* Ensure dropdown works in all modes */
    .dropdown {
        position: relative;
    }
    
    .dropdown-menu.show {
        display: block;
        opacity: 1;
        visibility: visible;
    }
    
    .dropdown-menu:not(.show) {
        display: none;
        opacity: 0;
        visibility: hidden;
    }
    html[data-bs-theme="dark"] {
        --bs-body-bg: #020617;
        --bs-body-color: #e5e7eb;
        --bs-border-color: #1f2937;
        --bs-card-bg: #020617;
        --bs-card-border-color: #1f2937;
        --bs-navbar-bg: #020617;
        --bs-navbar-color: #e5e7eb;
        --bs-navbar-brand-color: #f9fafb;
        --bs-navbar-brand-hover-color: #ffffff;
        --bs-navbar-hover-color: #ffffff;
        --bs-dropdown-bg: #020617;
        --bs-dropdown-link-color: #e5e7eb;
        --bs-dropdown-link-hover-bg: #1f2937;
        --bs-table-bg: transparent;
        --bs-table-striped-bg: rgba(148,163,184,0.12);
        --bs-table-striped-color: inherit;
        --bs-table-hover-bg: rgba(148,163,184,0.18);
        --bs-primary: #3b82f6;
        --bs-primary-rgb: 59,130,246;
        --bs-secondary: #64748b;
        --bs-secondary-rgb: 100,116,139;
        --bs-success: #22c55e;
        --bs-success-rgb: 34,197,94;
        --bs-danger: #ef4444;
        --bs-danger-rgb: 239,68,68;
        --bs-warning: #eab308;
        --bs-warning-rgb: 234,179,8;
        --bs-info: #0ea5e9;
        --bs-info-rgb: 14,165,233;
    }
    html[data-bs-theme="dark"] .card {
        box-shadow: 0 0.25rem 0.75rem rgba(15,23,42,0.75);
    }
    </style>
</head>
<body class="bg-body">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // Fallback jika jQuery gagal dimuat
    if (typeof jQuery === 'undefined') {
        document.write('<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"><\/script>');
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script src="app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Fallback jika Bootstrap gagal dimuat
    if (typeof bootstrap === 'undefined') {
        document.write('<script src="https://unpkg.com/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"><\/script>');
    }
    </script>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand<?php echo $current_page === 'index.php' ? ' active' : ''; ?>" href="index.php">Sistem Distribusi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle<?php echo $is_transaksi_active ? ' active' : ''; ?>" href="#" id="navbarDropdownTransaksi" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Transaksi
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownTransaksi">
                            <li><a class="dropdown-item<?php echo $current_page === 'purchases.php' ? ' active' : ''; ?>" href="purchases.php">Pembelian</a></li>
                            <li><a class="dropdown-item<?php echo $current_page === 'sales.php' ? ' active' : ''; ?>" href="sales.php">Penjualan</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $current_page === 'products.php' ? ' active' : ''; ?>" href="products.php">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $current_page === 'pesanan.php' ? ' active' : ''; ?>" href="pesanan.php">Pesanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $current_page === 'suppliers.php' ? ' active' : ''; ?>" href="suppliers.php">Pemasok</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $current_page === 'customers.php' ? ' active' : ''; ?>" href="customers.php">Pembeli</a>
                    </li>
                   
                    <li class="nav-item">
                        <a class="nav-link<?php echo $current_page === 'report_omzet.php' ? ' active' : ''; ?>" href="report_omzet.php">Laporan Omzet</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $current_page === 'report_purchases.php' ? ' active' : ''; ?>" href="report_purchases.php">Laporan Pembelian</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $current_page === 'report_pesanan.php' ? ' active' : ''; ?>" href="report_pesanan.php">Laporan Pesanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php echo $current_page === 'report_sppg.php' ? ' active' : ''; ?>" href="report_sppg.php">Laporan SPPG</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if ($user): ?>
                        <li class="nav-item">
                            <button type="button" class="btn btn-sm btn-outline-light me-2" data-theme-toggle>Tema</button>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $user['name']; ?> (<?php echo $user['role']; ?>)
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                                <li><a class="dropdown-item<?php echo $current_page === 'branches.php' ? ' active' : ''; ?>" href="branches.php">Cabang</a></li>
                                <?php if ($user['role'] === 'owner'): ?>
                                <li><a class="dropdown-item<?php echo $current_page === 'perusahaan.php' ? ' active' : ''; ?>" href="perusahaan.php">Perusahaan</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item<?php echo $current_page === 'profile.php' ? ' active' : ''; ?>" href="profile.php">Profil</a></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-3">
        <?php if (isset($error) && $error !== ''): ?>
            <div class="alert alert-danger mb-3"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($success) && $success !== ''): ?>
            <div class="alert alert-success mb-3"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($content_view)): ?>
            <?php include __DIR__ . DIRECTORY_SEPARATOR . $content_view; ?>
        <?php endif; ?>
    </div>
    <script>
    // Handle Chrome extension communication errors gracefully
    window.addEventListener('error', function(e) {
        // Suppress Chrome extension communication errors
        if (e.message && e.message.includes('Could not establish connection. Receiving end does not exist')) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);
    
    // Handle unhandled promise rejections from Chrome extensions
    window.addEventListener('unhandledrejection', function(e) {
        // Suppress Chrome extension communication errors
        if (e.reason && e.reason.message && e.reason.message.includes('Could not establish connection. Receiving end does not exist')) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        // Also check if the reason itself contains the error message
        if (e.reason && typeof e.reason === 'string' && e.reason.includes('Could not establish connection. Receiving end does not exist')) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);
    
    // Override console.error to suppress Chrome extension errors
    const originalConsoleError = console.error;
    console.error = function(...args) {
        const message = args.join(' ');
        if (message.includes('Could not establish connection. Receiving end does not exist')) {
            return; // Suppress this specific error
        }
        originalConsoleError.apply(console, args);
    };
    
    // Close dropdowns when clicking outside
    $(document).on('click', function (e) {
        var $navbar = $('nav');
        if ($navbar.length) {
            var $dropdowns = $navbar.find('.dropdown-menu.show');
            $dropdowns.each(function() {
                var $dropdown = $(this);
                var $dropdownToggle = $dropdown.siblings('[data-bs-toggle="dropdown"]');
                if ($dropdownToggle.length && !$dropdownToggle.is(e.target) && !$dropdown.has(e.target).length) {
                    // Close Bootstrap dropdown properly
                    var dropdown = new bootstrap.Dropdown($dropdownToggle[0]);
                    dropdown.hide();
                }
            });
        }
    });
    
    // Show alerts as toast notifications
    $(function() {
        if (typeof AppUtil !== 'undefined' && typeof AppUtil.showToast === 'function') {
            $('.alert.alert-danger, .alert.alert-success').each(function () {
                var $el = $(this);
                var text = $.trim($el.text());
                if (!text) {
                    return;
                }
                var isError = $el.hasClass('alert-danger');
                AppUtil.showToast(text, { type: isError ? 'error' : 'success' });
                $el.addClass('d-none');
            });
        }
    });
    </script>
</body>
</html>
