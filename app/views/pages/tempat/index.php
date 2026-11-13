<?php
/** @var array $tempat @var string $q */
if (!function_exists('bintang_tampil')) {
    function bintang_tampil($rating): string
    {
        $html = '';
        $r = (int) round((float) $rating);
        for ($i = 1; $i <= 5; $i++) {
            $html .= $i <= $r
                ? '<i class="bi bi-star-fill"></i>'
                : '<i class="bi bi-star muted"></i>';
        }
        return $html;
    }
}
?>
<div class="container mt-4 mb-5">
    <div class="text-center mb-4">
        <h1 class="section-title d-inline-block">Rekomendasi Tempat Sehat</h1>
        <p class="text-muted mt-2 mb-0">
            Temukan tempat makan dan kafe sehat terbaik dengan beragam menu bernutrisi di sekitar Anda.
        </p>
    </div>

    <div class="panel-white mb-4">
        <form action="<?= BASE_URL ?>/tempat" method="get" class="row g-2 align-items-center">
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" class="form-control" name="q"
                        placeholder="Cari nama tempat, alamat, atau deskripsi…" value="<?= e($q) ?>">
                </div>
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-green fw-semibold">
                    <i class="bi bi-search me-1"></i>Cari Tempat
                </button>
            </div>
        </form>
    </div>

    <div class="row g-4">
        <?php if (!$tempat): ?>
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm text-center py-4 mb-0">
                    <i class="bi bi-geo-alt fs-2 d-block mb-2"></i>
                    Tempat tidak ditemukan. Coba kata kunci lain.
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ($tempat as $t): ?>
            <div class="col-md-6 col-xl-4">
                <div class="card-soft h-100">
                    <div class="ratio ratio-article">
                        <img src="<?= img_url($t['foto_tempat'], 'tempat') ?>"
                            alt="<?= e($t['nama_tempat']) ?>" class="img-cover" loading="lazy">
                            <span class="position-absolute top-0 end-0 m-2 text-white fw-bold small" style="z-index: 10; text-shadow: 0px 1px 3px rgba(0, 0, 0, 0.8);">
                                <i class="bi bi-star-fill text-warning me-1"></i><?= $t['rating_rata'] !== null ? e($t['rating_rata']) : '-' ?>
                            </span>                    
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="mb-1">
                            <span class="stars"><?= bintang_tampil($t['rating_rata'] ?? 0) ?></span>
                            <small class="text-muted ms-1">(<?= (int) $t['jumlah_ulasan'] ?> ulasan)</small>
                        </div>
                        <h5 class="card-title"><?= e($t['nama_tempat']) ?></h5>
                        <p class="text-muted small mb-1">
                            <i class="bi bi-geo-alt me-1"></i><?= e(excerpt($t['alamat'], 70)) ?>
                        </p>
                        <p class="text-muted small flex-grow-1"><?= e(excerpt($t['deskripsi'], 80)) ?></p>
                        <a href="<?= BASE_URL ?>/tempat/<?= (int) $t['id_tempat'] ?>"
                        class="btn btn-sm btn-green align-self-start">
                            Lihat Detail &amp; Menu <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>