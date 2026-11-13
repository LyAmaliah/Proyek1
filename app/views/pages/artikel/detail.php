<?php /** @var array $artikel @var array $terkait */ ?>
<div class="container mt-4 mb-5">

    <div class="row g-4">
        <!-- Konten utama -->
        <div class="col-lg-8">
            <article class="panel-white">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="tag"><?= e($artikel['nama_kategori'] ?? 'Umum') ?></span>
                    <span class="tag orange"><i class="bi bi-calendar-event me-1"></i><?= tanggal_indo($artikel['tanggal_terbit']) ?></span>
                </div>

                <h1 class="h2 mb-3"><?= e($artikel['judul_artikel']) ?></h1>

                <div class="ratio ratio-article rounded-3 overflow-hidden mb-4">
                    <img src="<?= img_url($artikel['foto_artikel'], 'artikel') ?>"
                         alt="<?= e($artikel['judul_artikel']) ?>" class="img-cover">
                </div>

                <div class="article-body">
                    <?= nl2br(e($artikel['isi_artikel'])) ?>
                </div>

                <hr>
                <div class="d-flex justify-content-start align-items-center flex-wrap gap-2">
                    <a href="<?= BASE_URL ?>/artikel" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Artikel
                    </a>
                </div>
            </article>
        </div>

        <!-- Sidebar -->
        <aside class="col-lg-4">
            <?php if ($terkait): ?>
                <div class="panel-white mb-4">
                    <h5 class="section-title mb-3" style="font-size:1.05rem;">Artikel Terkait</h5>
                    <?php foreach ($terkait as $t): ?>
                        <a href="<?= BASE_URL ?>/artikel/<?= (int) $t['id_artikel'] ?>"
                           class="d-flex gap-3 mb-3 text-reset">
                            <div style="width:86px;height:64px;flex:0 0 86px;border-radius:10px;overflow:hidden;">
                                <img src="<?= img_url($t['foto_artikel'], 'artikel') ?>"
                                     alt="" class="img-cover">
                            </div>
                            <div class="lh-sm">
                                <small class="d-block fw-semibold"><?= e(excerpt($t['judul_artikel'], 55)) ?></small>
                                <small class="text-muted"><?= tanggal_indo($t['tanggal_terbit']) ?></small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="tip-box">
                <b><i class="bi bi-lightbulb-fill me-1"></i>Ingat ya!</b><br>
                Informasi di artikel bersifat edukatif. Untuk kondisi kesehatan tertentu,
                tetap konsultasikan dengan dokter atau ahli gizi.
            </div>
        </aside>
    </div>
</div>
