<?php
/**
 * Halaman laporan siap cetak / ekspor PDF — FR-07.
 * @var array $rekap @var array $perKategori @var array $admin
 * @var string $tanggal @var int $totalUlasan @var int $totalGizi
 */
?>
<div class="cetak-toolbar sticky-top no-print">
    <div class="container d-flex justify-content-between align-items-center py-2">
        <span class="small text-muted">
            <i class="bi bi-info-circle me-1"></i>Gunakan <b>Ctrl + P</b> → <b>Save as PDF</b> untuk ekspor PDF
        </span>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-green btn-sm fw-semibold" data-print>
                <i class="bi bi-printer me-1"></i>Cetak / Simpan PDF
            </button>
            <a href="<?= BASE_URL ?>/admin" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="cetak-sheet">
    <!-- Kop laporan -->
    <div class="cetak-header d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="brand-icon"><i class="bi bi-heart-pulse-fill"></i></span>
                <div>
                    <div class="fw-bold fs-5" style="color:var(--hijau-dark);">HealthyHabit</div>
                    <small class="text-muted"><?= e(APP_TAGLINE) ?></small>
                </div>
            </div>
        </div>
        <div class="text-end small">
            <div class="fw-semibold">LAPORAN STATISTIK SISTEM</div>
            <div class="text-muted">Tanggal cetak: <?= tanggal_indo($tanggal, true) ?></div>
            <div class="text-muted">Admin: <?= e($admin['username'] ?? '-') ?></div>
        </div>
    </div>

    <!-- Rekapitulasi -->
    <h6 class="fw-bold mb-2">1. Rekapitulasi Data</h6>
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
            <thead class="table-success">
                <tr>
                    <th>No.</th><th>Indikator</th><th class="text-end">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php $baris = [
                    'Total Pengguna Terdaftar' => $rekap['total_pengguna'],
                    'Total Artikel Kesehatan'  => $rekap['total_artikel'],
                    'Total Resep Sehat'        => $rekap['total_resep'],
                    'Total Tempat Sehat'       => $rekap['total_tempat'],
                    'Total Menu'               => $rekap['total_menu'],
                    'Total Data Kandungan Gizi' => $totalGizi,
                    'Total Ulasan & Rating'    => $totalUlasan,
                ]; $i = 1; ?>
                <?php foreach ($baris as $label => $jumlah): ?>
                    <tr>
                        <td style="width:50px;"><?= $i++ ?></td>
                        <td><?= $label ?></td>
                        <td class="text-end fw-semibold"><?= number_format((int) $jumlah) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Artikel per kategori -->
    <h6 class="fw-bold mb-2">2. Sebaran Artikel per Kategori</h6>
    <div class="table-responsive mb-4">
        <table class="table table-bordered">
            <thead class="table-success">
                <tr><th>Kategori</th><th class="text-end">Jumlah Artikel</th></tr>
            </thead>
            <tbody>
                <?php if (!$perKategori): ?>
                    <tr><td colspan="2" class="text-center text-muted">Belum ada data</td></tr>
                <?php endif; ?>
                <?php foreach ($perKategori as $row): ?>
                    <tr>
                        <td><?= e($row['nama_kategori']) ?></td>
                        <td class="text-end"><?= (int) $row['jumlah'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
