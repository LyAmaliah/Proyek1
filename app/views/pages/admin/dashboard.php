<?php
/**
 * Dashboard admin + Laporan Statistik gabungan (FR-07).
 * @var array $rekap @var array $riwayat @var array $perKategori
 * @var array $artikelBaru @var int $totalUlasan @var int $totalGizi
 */
$kartu = [
    ['label' => 'Total Pengguna',      'num' => $rekap['total_pengguna'], 'icon' => 'bi-people',           'color' => ''],
    ['label' => 'Total Artikel',       'num' => $rekap['total_artikel'],  'icon' => 'bi-newspaper',        'color' => ''],
    ['label' => 'Total Resep',         'num' => $rekap['total_resep'],    'icon' => 'bi-journal-richtext', 'color' => 'orange'],
    ['label' => 'Total Tempat/Mitra',  'num' => $rekap['total_tempat'],   'icon' => 'bi-shop',             'color' => 'orange'],
    ['label' => 'Total Menu',          'num' => $rekap['total_menu'],     'icon' => 'bi-bookmark-check',   'color' => ''],
    ['label' => 'Total Ulasan',        'num' => $totalUlasan,             'icon' => 'bi-chat-dots',        'color' => 'orange'],
    ['label' => 'Total Data Gizi',     'num' => $totalGizi,               'icon' => 'bi-egg-fried',        'color' => ''],
];
$maxKategori = max(array_map(fn($r) => (int) $r['jumlah'], $perKategori ?: [['jumlah' => 1]]));
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h4 mb-1">Dashboard &amp; Laporan Statistik</h1>
        <small class="text-muted">Ringkasan sistem <?= e(APP_NAME) ?> — terakhir diperbarui <?= date('d M Y, H:i') ?></small>
    </div>
    <div class="d-flex gap-2 no-print">
        <button type="button" class="btn btn-outline-success fw-semibold btn-sm" data-print>
            <i class="bi bi-printer me-1"></i>Cetak
        </button>
        <a href="<?= BASE_URL ?>/admin/cetak" class="btn btn-orange fw-semibold btn-sm" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i>Ekspor PDF
        </a>
    </div>
</div>

<!-- ===== 8 Kartu statistik ===== -->
<div class="row g-3 mb-4">
    <?php foreach ($kartu as $k): ?>
        <div class="col-6 col-md-4 col-xl-3">
            <div class="stat-card h-100">
                <span class="stat-icon <?= $k['color'] ?>"><i class="bi <?= $k['icon'] ?>"></i></span>
                <div>
                    <div class="stat-num"><?= $k['num'] ?></div>
                    <div class="stat-label"><?= $k['label'] ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <!-- ===== Kolom Kiri: Sebaran Artikel per Kategori ===== -->
    <div class="col-lg-5">
        <div class="panel-white h-100">
            <h6 class="mb-3"><i class="bi bi-bar-chart-line me-2 text-success"></i>Sebaran Artikel per Kategori</h6>
            <?php if (!$perKategori): ?>
                <p class="small text-muted mb-0">Belum ada data kategori.</p>
            <?php endif; ?>
            <?php foreach ($perKategori as $row): ?>
                <?php $pct = $maxKategori > 0 ? round($row['jumlah'] / $maxKategori * 100) : 0; ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span><?= e($row['nama_kategori']) ?></span>
                        <span class="fw-semibold"><?= (int) $row['jumlah'] ?></span>
                    </div>
                    <div class="progress" style="height:9px;">
                        <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ===== Kolom Kanan: Artikel Terbaru + Riwayat ===== -->
    <div class="col-lg-7">
        <div class="panel-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="bi bi-clock-history me-2 text-success"></i>Artikel Terbaru</h6>
                <a href="<?= BASE_URL ?>/admin/artikel" class="small fw-semibold text-success">Lihat semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead>
                        <tr><th>Judul</th><th>Kategori</th><th>Terbit</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!$artikelBaru): ?>
                            <tr><td colspan="3" class="text-muted small text-center py-3">Belum ada artikel.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($artikelBaru as $a): ?>
                            <tr>
                                <td class="text-truncate" style="max-width:260px;">
                                    <?= e($a['judul_artikel']) ?>
                                </td>
                                <td><span class="tag"><?= e($a['nama_kategori'] ?? '-') ?></span></td>
                                <td class="text-nowrap small"><?= tanggal_indo($a['tanggal_terbit']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel-white">
            <h6 class="mb-3"><i class="bi bi-printer me-2 text-success"></i>Riwayat Pencetakan Laporan</h6>
            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th><th>Admin</th><th>Tanggal Cetak</th>
                            <th class="text-end">Pengguna</th>
                            <th class="text-end">Artikel</th>
                            <th class="text-end">Resep</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$riwayat): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">
                                Belum pernah mencetak laporan.
                            </td></tr>
                        <?php endif; ?>
                        <?php foreach ($riwayat as $r): ?>
                            <tr>
                                <td><?= (int) $r['id_laporan'] ?></td>
                                <td><?= e($r['username'] ?? '-') ?></td>
                                <td class="small text-nowrap"><?= tanggal_indo($r['tanggal_cetak'], true) ?></td>
                                <td class="text-end"><?= (int) $r['total_pengguna'] ?></td>
                                <td class="text-end"><?= (int) $r['total_artikel'] ?></td>
                                <td class="text-end"><?= (int) $r['total_resep'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-light border small mt-4 mb-0 no-print">
    <i class="bi bi-lightbulb text-success me-1"></i>
    <b>Cara ekspor PDF:</b> klik tombol <b>Ekspor PDF</b>, lalu pada dialog cetak browser pilih
    destination <b>"Save as PDF"</b> (Chrome/Edge). Hasil otomatis tersimpan sebagai file PDF.
</div>
