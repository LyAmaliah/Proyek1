<?php
/** @var array $resep */
$bahan  = preg_split('/\r\n|\r|\n/', trim((string) $resep['bahan_bahan']));
$langkah = preg_split('/\r\n|\r|\n/', trim((string) $resep['langkah_pembuatan']));
$bahan  = array_values(array_filter(array_map('trim', $bahan), fn($b) => $b !== ''));
$langkah = array_values(array_filter(array_map('trim', $langkah), fn($b) => $b !== ''));
?>
<div class="container mt-4 mb-5">

    <div class="panel-white overflow-hidden mb-4">
        <div class="row g-0">
            <div class="col-lg-5">
                <div class="ratio ratio-4x3 h-100">
                    <img src="<?= img_url($resep['foto_resep'], 'resep') ?>"
                         alt="<?= e($resep['judul_resep']) ?>" class="img-cover">
                </div>
            </div>
            <div class="col-lg-7 p-4">
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <span class="tag orange"><i class="bi bi-fire me-1"></i><?= (int) $resep['total_kalori'] ?> kkal / porsi</span>
                    <span class="tag"><i class="bi bi-clock me-1"></i><?= (int) $resep['waktu_masak'] ?> menit</span>
                    <span class="tag gray"><i class="bi bi-person me-1"></i><?= e($resep['penulis'] ?? 'Redaksi') ?></span>
                </div>
                <h1 class="h2 mb-3"><?= e($resep['judul_resep']) ?></h1>
                <p class="text-muted"><?= e($resep['deskripsi']) ?></p>

                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?= BASE_URL ?>/resep" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Semua Resep
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Bahan -->
        <div class="col-lg-5">
            <div class="panel-white h-100">
                <h5 class="section-title mb-3" style="font-size:1.05rem;">
                    <i class="bi bi-basket me-2"></i>Bahan-Bahan
                </h5>
                <?php foreach ($bahan as $i => $b): ?>
                    <div class="ingredient-item">
                        <span><?= e($b) ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (!$bahan): ?>
                    <p class="text-muted small mb-0">Belum ada data bahan.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Langkah -->
        <div class="col-lg-7">
            <div class="panel-white h-100">
                <h5 class="section-title mb-3" style="font-size:1.05rem;">
                    <i class="bi bi-list-check me-2"></i>Langkah Pembuatan
                </h5>
                <?php foreach ($langkah as $i => $l): ?>
                    <div class="step-item">
                        <span class="step-num"><?= $i + 1 ?></span>
                        <span><?= e($l) ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (!$langkah): ?>
                    <p class="text-muted small mb-0">Belum ada data langkah.</p>
                <?php endif; ?>

                <div class="tip-box mt-3">
                    <b><i class="bi bi-heart-pulse me-1"></i>Tips sehat:</b>
                    Untuk hasil maksimal, kombinasikan menu ini dengan aktivitas fisik
                    minimal 30 menit setiap hari dan cukup minum air putih.
                </div>
            </div>
        </div>
    </div>
</div>
