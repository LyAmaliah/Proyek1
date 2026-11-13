<?php
/**
 * Cek Kandungan Gizi & Kalori — FR-11
 * Pencarian di server, perhitungan gramasi di client (main.js).
 *
 * @var array $daftar @var array $kategori @var string $q @var string $kategoriAktif
 *
 * Hasil pencarian & panel perhitungan hanya ditampilkan setelah pengguna
 * benar-benar mengisi kata kunci atau memilih kategori.
 */
$adaPencarian = trim((string) $q) !== '' || trim((string) $kategoriAktif) !== '';
$tampilkanHasil = $adaPencarian && !empty($daftar);
?>
<div class="container mt-4 mb-5">
    <div class="text-center mb-4">
        <h1 class="section-title d-inline-block">Cek Kandungan Gizi &amp; Kalori</h1>
        <p class="text-muted mt-2 mb-0">
            Seluruh nilai gizi disajikan <b>per 100 gram/ml</b>. Masukkan porsi konsumsi Anda untuk menghitung total kalori dan nutrisi secara otomatis.
        </p>
    </div>

    <div class="row g-4">
        <!-- ===== Kolom pencarian ===== -->
        <div class="<?= $tampilkanHasil ? 'col-lg-7' : 'col-12' ?>">
            <div class="panel-white mb-4">
                <h5 class="mb-3"><i class="bi bi-search me-2 text-success"></i>Cari Makanan / Minuman</h5>
                <form action="<?= BASE_URL ?>/gizi" method="get" class="row g-2">
                    <div class="col-md-7">
                        <input type="search" class="form-control" name="q" id="q"
                               placeholder="mis. nasi, jus, ayam…" value="<?= e($q) ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="kategori" class="form-select" aria-label="Kategori">
                            <option value="">Semua</option>
                            <?php foreach ($kategori as $kat): ?>
                                <option value="<?= e($kat) ?>" <?= $kategoriAktif === $kat ? 'selected' : '' ?>>
                                    <?= e($kat) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-green" type="submit"><i class="bi bi-funnel"></i></button>
                    </div>
                </form>
            </div>

            <div class="panel-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Hasil Pencarian</h5>
                    <?php if ($adaPencarian): ?>
                        <span class="tag"><?= count($daftar) ?> data</span>
                    <?php endif; ?>
                </div>

                <?php if (!$adaPencarian): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-search fs-2 d-block mb-2"></i>
                        Belum ada pencarian.<br>
                        Ketik nama makanan atau minuman pada kolom di atas, lalu tekan tombol cari.
                    </div>
                <?php elseif (!$daftar): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-emoji-neutral fs-2 d-block mb-2"></i>
                        Data tidak ditemukan. Coba kata kunci lain.
                    </div>
                <?php else: ?>
                    <!-- Pilih makanan untuk dihitung -->
                    <label for="giziMakanan" class="form-label small fw-semibold">1. Pilih makanan/minuman</label>
                    <select id="giziMakanan" class="form-select mb-3">
                        <option value="">— Pilih Makanan / Minuman —</option>
                        <?php foreach ($daftar as $d): ?>
                            <option value="<?= (int) $d['id_gizi'] ?>"
                                    data-kalori="<?= (float) $d['kalori'] ?>"
                                    data-karbohidrat="<?= (float) $d['karbohidrat'] ?>"
                                    data-protein="<?= (float) $d['protein'] ?>"
                                    data-lemak="<?= (float) $d['lemak'] ?>">
                                <?= e($d['nama_makanan_minuman']) ?> (<?= e($d['kategori']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="giziGram" class="form-label small fw-semibold">2. Masukkan gramasi (gram)</label>
                    <div class="input-group mb-3" style="max-width:260px;">
                        <input type="number" class="form-control" id="giziGram"
                               min="1" max="5000" step="1" value="100">
                        <span class="input-group-text bg-white">gram</span>
                    </div>

                    <!-- Tabel data (per 100 g) -->
                    <div class="table-responsive">
                        <table class="table table-custom align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th class="text-end">Kalori</th>
                                    <th class="text-end">Karbo</th>
                                    <th class="text-end">Protein</th>
                                    <th class="text-end">Lemak</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($daftar, 0, 12) as $d): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-semibold"><?= e($d['nama_makanan_minuman']) ?></span><br>
                                            <span class="tag gray"><?= e($d['porsi']) ?></span>
                                        </td>
                                        <td class="text-end"><?= number_format((float) $d['kalori'], 1) ?></td>
                                        <td class="text-end"><?= number_format((float) $d['karbohidrat'], 1) ?> g</td>
                                        <td class="text-end"><?= number_format((float) $d['protein'], 1) ?> g</td>
                                        <td class="text-end"><?= number_format((float) $d['lemak'], 1) ?> g</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($daftar) > 12): ?>
                        <small class="text-muted d-block mt-2">
                            Menampilkan 12 dari <?= count($daftar) ?> data — persempit pencarian untuk melihat lebih banyak.
                        </small>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- ===== Panel hasil perhitungan (hanya jika ada hasil) ===== -->
        <?php if ($tampilkanHasil): ?>
        <div class="col-lg-5">
            <div class="calc-panel position-sticky" style="top:86px;">
                <h4 class="mb-1"><i class="bi bi-calculator me-2"></i>Hasil Perhitungan</h4>
                <p class="small opacity-75 mb-3" id="giziHasilNama">Pilih makanan &amp; isi gramasi…</p>

                <div id="giziHasil" class="d-none">
                    <div class="calc-result text-center mb-3">
                        <div class="small text-uppercase opacity-75">Total Kalori</div>
                        <div class="big text-warning" id="giziKalori">0</div>
                        <div class="small opacity-75">kkal</div>
                    </div>

                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="calc-result py-2">
                                <div class="small opacity-75">Karbohidrat</div>
                                <div class="fw-bold" id="giziKarbohidrat">0</div>
                                <div class="small opacity-75">gram</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="calc-result py-2">
                                <div class="small opacity-75">Protein</div>
                                <div class="fw-bold" id="giziProtein">0</div>
                                <div class="small opacity-75">gram</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="calc-result py-2">
                                <div class="small opacity-75">Lemak</div>
                                <div class="fw-bold" id="giziLemak">0</div>
                                <div class="small opacity-75">gram</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-light mt-3 mb-0 small">
                    <i class="bi bi-info-circle text-success me-1"></i>
                    Perhitungan otomatis: <b>(nilai per 100 g × gramasi) ÷ 100</b>.
                    Kebutuhan kalori harianmu? Cek di
                    <a href="<?= BASE_URL ?>/bmi" class="fw-semibold text-success">Kalkulator BMI</a>.
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
