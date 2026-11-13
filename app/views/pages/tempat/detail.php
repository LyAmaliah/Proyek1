<?php
/**
 * Detail tempat + menu + ulasan & rating — FR-13.
 * @var array $tempat @var array $menu @var array $ulasan @var array $sebaran
 * @var float|null $rataRata @var int $totalUlasan @var bool $sudahUlas
 */
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
$user = Auth::user();
?>
<div class="container mt-4 mb-5">

    <!-- ===== Header tempat ===== -->
    <div class="panel-white overflow-hidden mb-4">
        <div class="row g-0">
            <div class="col-lg-5">
                <div class="ratio ratio-16x10 h-100">
                    <img src="<?= img_url($tempat['foto_tempat'], 'tempat') ?>"
                         alt="<?= e($tempat['nama_tempat']) ?>" class="img-cover">
                </div>
            </div>
            <div class="col-lg-7 p-4">
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <span class="tag"><i class="bi bi-star-fill text-warning me-1"></i>
                        <?= $rataRata !== null ? e($rataRata) : 'Belum dinilai' ?>
                    </span>
                    <span class="tag gray"><i class="bi bi-chat-dots me-1"></i><?= $totalUlasan ?> ulasan</span>
                    <span class="tag orange"><i class="bi bi-cup-straw me-1"></i><?= count($menu) ?> menu</span>
                </div>

                <h1 class="h2 mb-3"><?= e($tempat['nama_tempat']) ?></h1>
                <p class="text-muted mb-2"><i class="bi bi-geo-alt-fill text-success me-2"></i><?= e($tempat['alamat']) ?></p>
                <?php if ($tempat['kontak']): ?>
                    <p class="text-muted mb-2"><i class="bi bi-telephone-fill text-success me-2"></i><?= e($tempat['kontak']) ?></p>
                <?php endif; ?>
                <p class="mb-3"><?= nl2br(e($tempat['deskripsi'])) ?></p>

                <div class="d-flex gap-2 flex-wrap">
                    <?php if ($tempat['link_instagram']): ?>
                        <a href="<?= e($tempat['link_instagram']) ?>" target="_blank" rel="noopener"
                           class="btn btn-outline-success btn-sm">
                            <i class="bi bi-instagram me-1"></i>Instagram
                        </a>
                    <?php endif; ?>
                    <?php if ($tempat['link_grabfood']): ?>
                        <a href="<?= e($tempat['link_grabfood']) ?>" target="_blank" rel="noopener"
                           class="btn btn-success btn-sm">
                            <i class="bi bi-bag-fill me-1"></i>GrabFood
                        </a>
                    <?php endif; ?>
                    <?php if ($tempat['link_shopeefood']): ?>
                        <a href="<?= e($tempat['link_shopeefood']) ?>" target="_blank" rel="noopener"
                           class="btn btn-orange btn-sm">
                            <i class="bi bi-bag-fill me-1"></i>ShopeeFood
                        </a>
                    <?php endif; ?>
                    <?php if ($tempat['link_maps']): ?>
                        <a href="<?= e($tempat['link_maps']) ?>" target="_blank" rel="noopener"
                           class="btn btn-green fw-semibold btn-sm">
                            <i class="bi bi-geo-alt me-1"></i>Buka di Google Maps
                        </a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/tempat" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- ===== MENU ===== -->
        <div class="col-lg-7">
            <div class="panel-white mb-4">
                <h5 class="section-title mb-3" style="font-size:1.05rem;">
                    <i class="bi bi-bookmark-check me-2"></i>Menu Tersedia
                </h5>

                <?php if (!$menu): ?>
                    <p class="text-muted small mb-0">Belum ada menu terdaftar.</p>
                <?php else: ?>
                    <?php
                    // Kelompokkan menu berdasarkan kategori
                    $grouped = [];
                    foreach ($menu as $m) {
                        $kategori = $m['nama_kategori'] ?? 'Lainnya';
                        $grouped[$kategori][] = $m;
                    }
                    foreach ($grouped as $kategori => $items): ?>
                        <div class="mb-4">
                            <h6 class="mb-2 text-success">
                                <i class="bi bi-tag me-1"></i><?= e($kategori) ?>
                                <span class="small text-muted fw-normal">(<?= count($items) ?> menu)</span>
                            </h6>
                            <div class="row g-3">
                                <?php foreach ($items as $m): ?>
                                    <div class="col-md-6">
                                        <div class="card-soft h-100">
                                            <div class="ratio ratio-16x9">
                                                <img src="<?= img_url($m['foto_menu'], 'menu') ?>"
                                                     alt="<?= e($m['nama_menu']) ?>" class="img-cover" loading="lazy">
                                            </div>
                                            <div class="card-body py-2">
                                                <div class="d-flex justify-content-between align-items-start gap-2">
                                                    <h6 class="mb-1"><?= e($m['nama_menu']) ?></h6>
                                                    <span class="tag orange text-nowrap"><?= rupiah($m['harga']) ?></span>
                                                </div>
                                                <p class="text-muted small mb-2"><?= e(excerpt($m['deskripsi'], 70)) ?></p>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <?php if ((float) $m['kalori'] > 0): ?>
                                                        <span class="tag orange"><i class="bi bi-fire me-1"></i><?= number_format((float) $m['kalori'], 1) ?> kkal</span>
                                                    <?php endif; ?>
                                                    <span class="tag">P <?= number_format((float) $m['protein'], 1) ?> g</span>
                                                    <span class="tag">K <?= number_format((float) $m['karbohidrat'], 1) ?> g</span>
                                                    <span class="tag">L <?= number_format((float) $m['lemak'], 1) ?> g</span>
                                                    <?php if ($m['estimasi_waktu']): ?>
                                                        <span class="tag gray"><i class="bi bi-clock me-1"></i><?= (int) $m['estimasi_waktu'] ?> mnt</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- ===== FORM ULASAN (FR-13) ===== -->
            <div class="panel-white">
                <h5 class="section-title mb-3" style="font-size:1.05rem;">
                    <i class="bi bi-pencil-square me-2"></i>Berikan Ulasan &amp; Rating
                </h5>

                <?php if (!$user): ?>
                    <div class="alert alert-light border mb-0">
                        <i class="bi bi-lock me-1 text-success"></i>
                        <a href="<?= BASE_URL ?>/auth/login" class="fw-semibold text-success">Login</a>
                        terlebih dahulu untuk mengirim ulasan.
                    </div>
                <?php elseif ($sudahUlas): ?>
                    <div class="alert alert-light border mb-0">
                        <i class="bi bi-check-circle me-1 text-success"></i>
                        Anda sudah mengulas tempat ini. Terima kasih atas opininya!
                    </div>
                <?php else: ?>
                    <form action="<?= BASE_URL ?>/tempat/<?= (int) $tempat['id_tempat'] ?>/ulasan"
                          method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold d-block">Rating Anda</label>
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input class="rating-input" type="radio" name="rating"
                                       id="rating<?= $i ?>" value="<?= $i ?>" <?= $i === 5 ? 'checked' : '' ?>>
                            <?php endfor; ?>
                            <span class="d-inline-flex">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <label class="rating-label <?= $i === 1 ? 'active' : '' ?>"
                                           for="rating<?= $i ?>" data-value="<?= $i ?>" title="<?= $i ?> bintang">
                                        <i class="bi bi-star-fill"></i>
                                    </label>
                                <?php endfor; ?>
                            </span>
                        </div>

                        <div class="mb-3">
                            <label for="komentar" class="form-label small fw-semibold">Komentar</label>
                            <textarea class="form-control" id="komentar" name="komentar" rows="3"
                                      minlength="5" maxlength="500" required
                                      placeholder="Ceritakan pengalamanmu di sini…"></textarea>
                        </div>

                        <button type="submit" class="btn btn-green fw-semibold">
                            <i class="bi bi-send me-1"></i>Kirim Ulasan
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- ===== RATING & DAFTAR ULASAN ===== -->
        <div class="col-lg-5">
            <!-- Ringkasan rating -->
            <div class="panel-white mb-4 text-center">
                <div class="display-3 fw-bold text-success" style="font-size:2.6rem;">
                    <?= $rataRata !== null ? e($rataRata) : '—' ?>
                </div>
                <div class="stars mb-1" style="font-size:1.3rem;"><?= bintang_tampil($rataRata ?? 0) ?></div>
                <small class="text-muted d-block mb-3">Berdasarkan <?= $totalUlasan ?> ulasan</small>

                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <?php $pct = $totalUlasan > 0 ? round($sebaran[$i] / $totalUlasan * 100) : 0; ?>
                    <div class="d-flex align-items-center gap-2 mb-1 small">
                        <span style="width:38px;"><?= $i ?> <i class="bi bi-star-fill text-warning"></i></span>
                        <div class="progress flex-grow-1" style="height:8px;">
                            <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
                        </div>
                        <span class="text-muted" style="width:32px;text-align:right;"><?= $sebaran[$i] ?></span>
                    </div>
                <?php endfor; ?>
            </div>

            <!-- Daftar ulasan -->
            <div class="panel-white">
                <h5 class="mb-3"><i class="bi bi-chat-left-text me-2 text-success"></i>Ulasan Pengguna</h5>

                <?php if (!$ulasan): ?>
                    <p class="text-muted small mb-0">Belum ada ulasan. Jadilah yang pertama mengulas!</p>
                <?php endif; ?>

                <?php foreach ($ulasan as $u): ?>
                    <div class="border-bottom pb-3 mb-3 <?= $u === end($ulasan) ? 'border-0 mb-0 pb-0' : '' ?>">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="stat-icon" style="width:38px;height:38px;flex:0 0 38px;font-size:1rem;">
                                    <i class="bi bi-person"></i>
                                </span>
                                <div class="lh-sm">
                                    <small class="fw-semibold d-block"><?= e($u['nama_lengkap']) ?></small>
                                    <small class="text-muted"><?= tanggal_indo($u['tanggal_ulasan']) ?></small>
                                </div>
                            </div>
                            <span class="stars small"><?= bintang_tampil($u['rating']) ?></span>
                        </div>
                        <p class="small mt-2 mb-0 text-muted"><?= nl2br(e($u['komentar'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
