<?php
/**
 * Layout dashboard admin (sidebar + topbar).
 * @var string $content
 */
Auth::requireAdmin();
$aktif = $activeNav ?? '';
$menu = [
    'dashboard' => ['label' => 'Dashboard',   'icon' => 'bi-speedometer2', 'url' => '/admin'],
    'artikel'   => ['label' => 'Artikel',     'icon' => 'bi-newspaper',    'url' => '/admin/artikel'],
    'gizi'      => ['label' => 'Gizi & Kalori', 'icon' => 'bi-egg-fried',  'url' => '/admin/gizi'],
    'resep'     => ['label' => 'Resep',       'icon' => 'bi-journal-richtext', 'url' => '/admin/resep'],
    'tempat'    => ['label' => 'Tempat & Menu', 'icon' => 'bi-shop',       'url' => '/admin/tempat'],
    'pengguna'  => ['label' => 'Pengguna',    'icon' => 'bi-people',       'url' => '/admin/pengguna'],
];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' · ' : '' ?>Admin <?= e(APP_NAME) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css?v=<?= APP_VERSION ?>" rel="stylesheet">
</head>
<body class="admin-body">

<div class="d-flex" id="wrapper">

    <!-- ======================= SIDEBAR ======================= -->
    <aside class="admin-sidebar" id="sidebarMenu">
        <div class="sidebar-brand px-3 py-3 d-flex align-items-center gap-2">
            <img src="<?= BASE_URL ?>/uploads/logo/logo_healthyhabit.png" alt="Logo HealthyHabit" width="40" height="40" class="d-inline-block align-text-top">
            <span class="fw-bold text-white">Healthy<span class="text-accent">Habit</span></span>
            <button class="btn btn-sm btn-link text-white ms-auto d-md-none" id="closeSidebar"
                    aria-label="Tutup menu"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="sidebar-user px-3 py-2 mb-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-badge fs-4"></i>
                <div class="lh-sm">
                    <small class="d-block text-white-50">Masuk sebagai</small>
                    <small class="fw-semibold text-white"><?= e(Auth::user()['username'] ?? 'Admin') ?></small>
                </div>
            </div>
        </div>

        <ul class="nav flex-column sidebar-nav">
            <?php foreach ($menu as $key => $item): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $aktif === $key ? 'active' : '' ?>"
                       href="<?= BASE_URL . $item['url'] ?>">
                        <i class="bi <?= $item['icon'] ?> me-2"></i><?= $item['label'] ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <li class="nav-item mt-3">
                <a class="nav-link text-danger-soft" href="<?= BASE_URL ?>/auth/logout">
                    <i class="bi bi-box-arrow-right me-2"></i>Keluar
                </a>
            </li>
        </ul>
    </aside>

    <!-- ======================= KONTEN ======================= -->
    <div class="admin-main flex-grow-1">
        <nav class="admin-topbar navbar navbar-expand navbar-light bg-white border-bottom px-3 py-2">
            <button class="btn btn-link d-md-none me-2 text-dark" id="openSidebar"
                    aria-label="Buka menu"><i class="bi bi-list fs-4"></i></button>
            <span class="navbar-text fw-semibold text-truncate">
                <?= isset($pageTitle) ? e($pageTitle) : 'Dashboard' ?>
            </span>
            <div class="ms-auto d-flex align-items-center gap-2">
                <span class="badge text-bg-success"><i class="bi bi-shield-check me-1"></i>Admin</span>
            </div>
        </nav>

        <main class="admin-content p-3 p-lg-4">
            <?php $flashList = get_flash(); ?>
            <?php foreach ($flashList ?? [] as $f): ?>
                <div class="alert alert-<?= e($f['type']) ?> alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-info-circle me-1"></i><?= $f['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endforeach; ?>

            <?= $content ?>
        </main>
    </div>
</div>

<div class="sidebar-backdrop d-md-none" id="sidebarBackdrop"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/main.js?v=<?= APP_VERSION ?>"></script>
</body>
</html>
