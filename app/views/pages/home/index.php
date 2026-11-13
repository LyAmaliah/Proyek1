<?php
/** @var array $statistik @var array $artikel @var array $resep */
?>

<!-- ======================= HERO ======================= -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="hero-badge mb-3">
                    <?php if (Auth::check()): ?>
                        ✨ Selamat datang kembali, <?= e(Auth::user()['nama_lengkap']) ?>! Yuk, lanjutkan kebiasaan sehatmu.
                    <?php else: ?>
                        <i class="bi bi-flower1"></i> Kebiasaan Sehat Dimulai dari Hari Ini
                    <?php endif; ?>
                </span>
                <h1 class="mb-3">
                    Wujudkan Hidup Sehat<br>
                    Bersama <span class="text-accent">HealthyHabit</span>
                </h1>
                <p class="lead mb-4">
                    Pantau kesehatan Anda dengan mudah! Hitung BMI, cek kalori makanan,
                    coba resep sehat lezat, dan temukan tempat kuliner bernutrisi di sekitar Anda.
                </p>
                <?php if (!Auth::check()): ?>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= BASE_URL.'/auth/register' ?>" class="btn btn-green fw-semibold px-4">
                            <i class="bi bi-rocket-takeoff me-1"></i>Mulai Sekarang
                        </a>
                    </div>

                    <div class="row g-3 mt-3">
                        <?php foreach ([
                            ['icon' => 'bi-people', 'num' => $statistik['pengguna'], 'label' => 'Pengguna Terdaftar'],
                            ['icon' => 'bi-newspaper', 'num' => $statistik['artikel'], 'label' => 'Artikel Kesehatan'],
                            ['icon' => 'bi-journal-richtext', 'num' => $statistik['resep'], 'label' => 'Resep Sehat'],
                            ['icon' => 'bi-shop', 'num' => $statistik['tempat'], 'label' => 'Tempat Sehat'],
                        ] as $s): ?>
                            <div class="col-6 col-lg-3">
                                <div class="stat-card">
                                    <span class="stat-icon"><i class="bi <?= $s['icon'] ?>"></i></span>
                                    <div>
                                        <div class="stat-num"><?= (int) $s['num'] ?>+</div>
                                        <div class="stat-label"><?= $s['label'] ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block">
                <div class="hero-art"><i class="bi bi-emoji-smile"></i></div>
            </div>
        </div>
    </div>
</section>

<!-- ======================= CTA KALKULATOR BMI (di atas artikel) ======================= -->
<section class="container mt-4 mb-4">
    <div class="calc-panel d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
        <div>
            <h3 class="mb-1 text-white"><i class="bi bi-speedometer2 me-2"></i>Kalkulator BMI &amp; Estimasi Kalori</h3>
            <p class="mb-0 opacity-75">Tahu berat badan idealmu? Hitung BMI dan kebutuhan kalori harian secara otomatis.</p>
        </div>
        <a href="<?= BASE_URL ?>/bmi" class="btn btn-orange fw-semibold px-4 flex-shrink-0">
            Hitung BMI Saya <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</section>

<!-- ======================= ARTIKEL TERBARU (FR-10) ======================= -->
<section class="container mt-5">
    <div class="d-flex justify-content-between align-items-end mb-3 flex-wrap gap-2">
        <div>
            <h2 class="section-title mb-0">Artikel Kesehatan Terbaru</h2>
            <small class="text-muted">Edukasi gaya hidup sehat dari para ahli</small>
        </div>
        <a href="<?= BASE_URL ?>/artikel" class="btn btn-soft-green fw-semibold">
            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="row g-4">
        <?php if (!$artikel): ?>
            <div class="col-12"><div class="alert alert-info border-0">Belum ada artikel.</div></div>
        <?php endif; ?>
        <?php foreach ($artikel as $a): ?>
            <div class="col-md-4">
                <div class="card-soft h-100">
                    <div class="ratio ratio-article">
                        <img src="<?= img_url($a['foto_artikel'], 'artikel') ?>"
                             alt="<?= e($a['judul_artikel']) ?>" class="img-cover">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <span class="tag"><?= e($a['nama_kategori'] ?? 'Umum') ?></span>
                            <span class="tag gray ms-1"><i class="bi bi-calendar3 me-1"></i><?= tanggal_indo($a['tanggal_terbit']) ?></span>
                        </div>
                        <h5 class="card-title"><?= e($a['judul_artikel']) ?></h5>
                        <p class="text-muted small flex-grow-1"><?= e(excerpt($a['isi_artikel'], 110)) ?></p>
                        <a href="<?= BASE_URL ?>/artikel/<?= (int) $a['id_artikel'] ?>"
                           class="btn btn-sm btn-green align-self-start">
                            Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ======================= RESEP SEHAT (FR-12) ======================= -->
<section class="container mt-5 mb-4">
    <div class="d-flex justify-content-between align-items-end mb-3 flex-wrap gap-2">
        <div>
            <h2 class="section-title mb-0">Resep Sehat Pilihan</h2>
            <small class="text-muted">Menu rumahan bergizi & rendah kalori</small>
        </div>
        <a href="<?= BASE_URL ?>/resep" class="btn btn-soft-green fw-semibold">
            Semua Resep <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="row g-4">
        <?php if (!$resep): ?>
            <div class="col-12"><div class="alert alert-info border-0">Belum ada resep dipublikasikan.</div></div>
        <?php endif; ?>
        <?php foreach ($resep as $r): ?>
            <div class="col-md-4">
                <div class="card-soft h-100">
                    <div class="ratio ratio-thumb">
                        <img src="<?= img_url($r['foto_resep'], 'resep') ?>"
                             alt="<?= e($r['judul_resep']) ?>" class="img-cover">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="tag orange"><i class="bi bi-fire me-1"></i><?= (int) $r['total_kalori'] ?> kkal</span>
                            <span class="tag gray"><i class="bi bi-clock me-1"></i><?= (int) $r['waktu_masak'] ?> menit</span>
                        </div>
                        <h5 class="card-title"><?= e($r['judul_resep']) ?></h5>
                        <p class="text-muted small flex-grow-1"><?= e(excerpt($r['deskripsi'], 90)) ?></p>
                        <a href="<?= BASE_URL ?>/resep/<?= (int) $r['id_resep'] ?>"
                           class="btn btn-sm btn-green align-self-start">
                            Lihat Resep <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
