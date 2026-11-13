<?php
/** @var array $resep @var string $q @var int $maksWaktu */
?>
<div class="container mt-4 mb-5">
    <div class="text-center mb-4">
        <h1 class="section-title d-inline-block">Resep Sehat</h1>
        <p class="text-muted mt-2 mb-0">Kumpulan resep hidangan lezat dan bernutrisi, dilengkapi estimasi kalori serta waktu memasak untuk mendukung gaya hidup sehat Anda.</p>
    </div>

    <!-- Filter -->
    <div class="panel-white mb-4">
        <form action="<?= BASE_URL ?>/resep" method="get" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label for="q" class="form-label small fw-semibold">Cari Resep</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" class="form-control" id="q" name="q"
                        placeholder="Judul, bahan, atau deskripsi…" value="<?= e($q) ?>">
                </div>
            </div>
            <div class="col-md-4">
                <label for="waktu" class="form-label small fw-semibold">Waktu Masak Maksimal</label>
                <select class="form-select" id="waktu" name="waktu">
                    <option value="">— Semua —</option>
                    <option value="15" <?= $maksWaktu === 15 ? 'selected' : '' ?>>≤ 15 menit</option>
                    <option value="30" <?= $maksWaktu === 30 ? 'selected' : '' ?>>≤ 30 menit</option>
                    <option value="60" <?= $maksWaktu === 60 ? 'selected' : '' ?>>≤ 60 menit</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-green fw-semibold">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Grid resep -->
    <div class="row g-4">
        <?php if (!$resep): ?>
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm text-center py-4 mb-0">
                    <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
                    Resep tidak ditemukan. Coba kata kunci atau filter lain.
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ($resep as $r): ?>
            <div class="col-md-6 col-xl-3">
                <div class="card-soft h-100">
                    <div class="ratio ratio-4x3">
                        <img src="<?= img_url($r['foto_resep'], 'resep') ?>"
                            alt="<?= e($r['judul_resep']) ?>" class="img-cover" loading="lazy">
                            <span class="position-absolute top-0 start-0 m-2 text-white fw-bold small" style="z-index: 10; text-shadow: 0px 1px 3px rgba(0, 0, 0, 0.8);">
                                <i class="bi bi-fire me-1 text-warning"></i><?= (int) $r['total_kalori'] ?> kkal
                            </span>                    
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2 d-flex flex-wrap gap-1">
                            <span class="tag orange"><i class="bi bi-clock me-1"></i><?= (int) $r['waktu_masak'] ?> menit</span>
                            <?php if (!empty($r['penulis'])): ?>
                                <span class="tag gray"><i class="bi bi-person me-1"></i><?= e($r['penulis']) ?></span>
                            <?php endif; ?>
                        </div>
                        <h5 class="card-title"><?= e($r['judul_resep']) ?></h5>
                        <p class="text-muted small flex-grow-1"><?= e(excerpt($r['deskripsi'], 95)) ?></p>
                        <a href="<?= BASE_URL ?>/resep/<?= (int) $r['id_resep'] ?>"
                        class="btn btn-sm btn-green align-self-start">
                            Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>