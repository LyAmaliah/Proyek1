<?php
/**
 * includes/header.php — Header/Situs Utama
 * Bootstrap 5 + Bootstrap Icons (CDN) + Google Font Poppins
 * Palet: Hijau #27824B · Hijau Muda #E8F5E9 · Putih #FFFFFF · Oranye #F5A623 · Abu-abu #F5F5F5
 */
$judulHalaman = $pageTitle ?? '';
$aktif        = $activeNav ?? '';
$siapa        = Auth::user();
$adminSesi    = $siapa && $siapa['role'] === 'admin';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e(APP_TAGLINE) ?>">
    <title><?= $judulHalaman ? e($judulHalaman) . ' &middot; ' : '' ?><?= e(APP_NAME) ?></title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Style utama -->
    <link href="<?= BASE_URL ?>/assets/css/style.css?v=<?= APP_VERSION ?>" rel="stylesheet">
</head>
<body>

<!-- ======================= NAVBAR ======================= -->
<header class="site-header sticky-top">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-2">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= BASE_URL ?>/">
                <img src="<?= BASE_URL ?>/uploads/logo/logo_healthyhabit.png" alt="Logo HealthyHabit" width="40" height="40" class="d-inline-block align-text-top">
                <span class="brand-text">Healthy<span class="text-accent">Habit</span></span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navUtama" aria-controls="navUtama" aria-expanded="false"
                    aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navUtama">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link <?= $aktif === 'beranda' ? 'active fw-semibold' : '' ?>"
                           href="<?= BASE_URL ?>/"><i class="bi bi-house-door me-1"></i>Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $aktif === 'artikel' ? 'active fw-semibold' : '' ?>"
                           href="<?= BASE_URL ?>/artikel"><i class="bi bi-newspaper me-1"></i>Artikel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $aktif === 'gizi' ? 'active fw-semibold' : '' ?>"
                           href="<?= BASE_URL ?>/gizi"><i class="bi bi-egg-fried me-1"></i>Cek Gizi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $aktif === 'resep' ? 'active fw-semibold' : '' ?>"
                           href="<?= BASE_URL ?>/resep"><i class="bi bi-journal-richtext me-1"></i>Resep</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $aktif === 'tempat' ? 'active fw-semibold' : '' ?>"
                           href="<?= BASE_URL ?>/tempat"><i class="bi bi-shop me-1"></i>Tempat Sehat</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <?php if ($siapa): ?>
                        <!-- Menu pengguna login -->
                        <div class="dropdown">
                            <a class="btn btn-soft-green dropdown-toggle fw-semibold" href="#"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i><?= e($siapa['nama_lengkap']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/profil">
                                    <i class="bi bi-person me-2"></i>Profil Saya</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/bmi">
                                    <i class="bi bi-speedometer2 me-2"></i>Kalkulator BMI</a></li>
                                <?php if ($adminSesi): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin">
                                        <i class="bi bi-speedometer me-2"></i>Dashboard Admin</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/auth/logout">
                                    <i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a class="btn btn-outline-success fw-semibold" href="<?= BASE_URL ?>/auth/login">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
                        <a class="btn btn-green fw-semibold" href="<?= BASE_URL ?>/auth/register">
                            <i class="bi bi-person-plus me-1"></i>Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>

<!-- ======================= FLASH MESSAGE ======================= -->
<?php $flashList = get_flash(); ?>
<?php if ($flashList): ?>
    <div class="container mt-3">
        <?php foreach ($flashList as $f): ?>
            <div class="alert alert-<?= e($f['type']) ?? 'info' ?> alert-dismissible fade show shadow-sm border-0"
                 role="alert">
                <i class="bi bi-info-circle me-1"></i><?= $f['message'] /* sudah escape saat dibuat */ ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<main class="site-main">
