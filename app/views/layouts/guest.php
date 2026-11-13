<?php
/**
 * Layout minimal (guest) — untuk halaman login & halaman cetak.
 * @var string $content
 */
$halamanCetak = ($pageTitle ?? '') === 'Cetak Laporan Statistik';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' · ' : '' ?><?= e(APP_NAME) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css?v=<?= APP_VERSION ?>" rel="stylesheet">
</head>
<body class="guest-body <?= $halamanCetak ? 'cetak-body' : '' ?>">

<nav class="navbar navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= BASE_URL ?>/">
            <span class="brand-icon"><i class="bi bi-heart-pulse-fill"></i></span>
            <span>Healthy<span class="text-accent">Habit</span></span>
        </a>
        <a href="<?= BASE_URL ?>/" class="btn btn-sm btn-outline-success">
            <i class="bi bi-house me-1"></i>Beranda
        </a>
    </div>
</nav>

<?php $flashList = get_flash(); ?>
<?php if ($flashList): ?>
    <div class="container mt-3">
        <?php foreach ($flashList as $f): ?>
            <div class="alert alert-<?= e($f['type']) ?> alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="bi bi-info-circle me-1"></i><?= $f['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $content ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/main.js?v=<?= APP_VERSION ?>"></script>
</body>
</html>
