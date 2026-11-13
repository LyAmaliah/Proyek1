<?php
/** @var array $artikel @var array $kategori @var string $q @var int $idKategori */
?>
<div class="container mt-4 mb-5">
    <!-- Header -->
    <div class="text-center mb-4">
        <h1 class="section-title d-inline-block">Artikel Kesehatan</h1>
        <p class="text-muted mt-2 mb-0">Edukasi &amp; tips gaya hidup sehat pilihan redaksi</p>
    </div>

    <!-- Filter -->
    <div class="panel-white mb-4">
        <form action="<?= BASE_URL ?>/artikel" method="get" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label for="q" class="form-label small fw-semibold">Cari Artikel</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" class="form-control" id="q" name="q"
                           placeholder="Judul atau isi artikel…" value="<?= e($q) ?>">
                </div>
            </div>
            <div class="col-md-4">
                <label for="id_kategori" class="form-label small fw-semibold">Kategori</label>
                <select class="form-select" id="id_kategori" name="id_kategori">
                    <option value="">— Semua Kategori —</option>
                    <?php foreach ($kategori as $k): ?>
                        <option value="<?= (int) $k['id_kategori_artikel'] ?>"
                            <?= $idKategori === (int) $k['id_kategori_artikel'] ? 'selected' : '' ?>>
                            <?= e($k['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-green fw-semibold">
                    <i class="bi bi-funnel me-1"></i>Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Daftar artikel -->
    <div class="row g-4">
        <?php if (!$artikel): ?>
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm text-center py-4 mb-0">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    Artikel tidak ditemukan. Coba kata kunci lain.
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ($artikel as $a): ?>
            <div class="col-md-6 col-xl-4">
                <div class="card-soft h-100">
                    <div class="ratio ratio-article">
                        <img src="<?= img_url($a['foto_artikel'], 'artikel') ?>"
                             alt="<?= e($a['judul_artikel']) ?>" class="img-cover" loading="lazy">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2 d-flex flex-wrap gap-1">
                            <span class="tag"><?= e($a['nama_kategori'] ?? 'Umum') ?></span>
                            <span class="tag gray"><i class="bi bi-calendar3 me-1"></i><?= tanggal_indo($a['tanggal_terbit']) ?></span>
                        </div>
                        <h5 class="card-title"><?= e($a['judul_artikel']) ?></h5>
                        <p class="text-muted small flex-grow-1"><?= e(excerpt($a['isi_artikel'], 130)) ?></p>
                        <div class="d-flex justify-content-end align-items-center mt-2">
                            <a href="<?= BASE_URL ?>/artikel/<?= (int) $a['id_artikel'] ?>"
                               class="btn btn-sm btn-green">Baca <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
