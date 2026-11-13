<?php
/**
 * Halaman Kalkulator BMI — FR-09.
 * @var array|null $pengguna @var array|null $hasil
 */
$user = Auth::user();
?>
<div class="container mt-4 mb-5">
    <div class="text-center mb-4">
        <h1 class="section-title d-inline-block">Kalkulator BMI &amp; Estimasi Kalori</h1>
        <p class="text-muted mt-2 mb-0">
            Body Mass Index — ukuran berat badan ideal berdasarkan tinggi badan
        </p>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- ===== FORM ===== -->
        <div class="col-lg-6">
            <div class="panel-white">
                <h5 class="mb-3"><i class="bi bi-clipboard2-pulse me-2 text-success"></i>Data Diri Anda</h5>

                <form action="<?= BASE_URL ?>/bmi" method="post" id="formBmi">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="bmiBerat" class="form-label fw-semibold small">Berat Badan (kg)</label>
                            <input type="number" step="0.1" class="form-control" id="bmiBerat"
                                   name="berat_badan" min="20" max="300" required
                                   placeholder="mis. 62.5"
                                   value="<?= e($hasil['berat'] ?? $pengguna['berat_badan'] ?? '') ?>">
                        </div>
                        <div class="col-sm-6">
                            <label for="bmiTinggi" class="form-label fw-semibold small">Tinggi Badan (cm)</label>
                            <input type="number" step="0.1" class="form-control" id="bmiTinggi"
                                   name="tinggi_badan" min="50" max="250" required
                                   placeholder="mis. 170"
                                   value="<?= e($hasil['tinggi'] ?? $pengguna['tinggi_badan'] ?? '') ?>">
                        </div>
                        <div class="col-sm-6">
                            <label for="bmiUmur" class="form-label fw-semibold small">Umur (tahun)</label>
                            <input type="number" class="form-control" id="bmiUmur" name="umur"
                                   min="5" max="100" required placeholder="mis. 25"
                                   value="<?= e($hasil['umur'] ?? $pengguna['umur'] ?? '') ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small d-block">Jenis Kelamin</label>
                            <?php $jkAktif = $hasil['jk'] ?? ($pengguna['jenis_kelamin'] ?? 'L'); ?>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="jenis_kelamin" id="bL"
                                       value="L" <?= $jkAktif === 'L' ? 'checked' : '' ?>>
                                <label class="btn btn-outline-success" for="bL">Laki-laki</label>
                                <input type="radio" class="btn-check" name="jenis_kelamin" id="bP"
                                       value="P" <?= $jkAktif === 'P' ? 'checked' : '' ?>>
                                <label class="btn btn-outline-success" for="bP">Perempuan</label>
                            </div>
                        </div>
                    </div>

                    <!-- Pratinjau langsung (client-side) -->
                    <div id="bmiPreview" class="alert alert-light border d-none mt-3 mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Pratinjau langsung</span>
                            <span class="fw-bold text-success">
                                <span id="bmiPreviewNilai">0</span> —
                                <span id="bmiPreviewKategori">Normal</span>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-green w-100 fw-semibold mt-3 py-2">
                        <i class="bi bi-calculator me-1"></i>Hitung BMI Saya
                    </button>

                    <?php if (!$user): ?>
                        <p class="small text-muted text-center mt-2 mb-0">
                            <a href="<?= BASE_URL ?>/auth/register" class="fw-semibold text-success">Daftar</a>
                            untuk menyimpan riwayat BMI &amp; data profil otomatis.
                        </p>
                    <?php else: ?>
                        <p class="small text-muted text-center mt-2 mb-0">
                            <i class="bi bi-check2-circle text-success"></i>
                            Hasil akan tersimpan ke riwayat BMI Anda.
                        </p>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- ===== HASIL ===== -->
        <div class="col-lg-5">
            <?php if ($hasil): ?>
                <div class="calc-panel">
                    <h4 class="mb-3 text-white"><i class="bi bi-emoji-smile me-2"></i>Hasil Perhitungan</h4>

                    <div class="calc-result text-center mb-3">
                        <div class="small text-uppercase opacity-75">Skor BMI</div>
                        <div class="big text-warning"><?= number_format((float) $hasil['nilai_bmi'], 1) ?></div>
                        <div>
                            <span class="badge mt-2 <?= ['Kurus' => 'badge-bmi-kurus', 'Normal' => 'badge-bmi-normal',
                                'Kelebihan Berat' => 'badge-bmi-kelebihan', 'Obesitas' => 'badge-bmi-obesitas']
                                [$hasil['kategori_bmi']] ?? 'bg-light' ?>">
                                <?= e($hasil['kategori_bmi']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="calc-result mb-3">
                        <div class="small opacity-75 mb-1">Kategori BMI (WHO Asia)</div>
                        <div class="small d-flex justify-content-between">
                            <span>&lt; 18,5 — Kurus</span><span class="<?= $hasil['nilai_bmi'] < 18.5 ? 'fw-bold text-warning' : 'opacity-50' ?>">✓</span>
                        </div>
                        <div class="small d-flex justify-content-between">
                            <span>18,5 – 24,9 — Normal</span><span class="<?= $hasil['nilai_bmi'] >= 18.5 && $hasil['nilai_bmi'] < 25 ? 'fw-bold text-warning' : 'opacity-50' ?>">✓</span>
                        </div>
                        <div class="small d-flex justify-content-between">
                            <span>25 – 29,9 — Kelebihan</span><span class="<?= $hasil['nilai_bmi'] >= 25 && $hasil['nilai_bmi'] < 30 ? 'fw-bold text-warning' : 'opacity-50' ?>">✓</span>
                        </div>
                        <div class="small d-flex justify-content-between">
                            <span>≥ 30 — Obesitas</span><span class="<?= $hasil['nilai_bmi'] >= 30 ? 'fw-bold text-warning' : 'opacity-50' ?>">✓</span>
                        </div>
                    </div>

                    <div class="calc-result text-center">
                        <div class="small opacity-75">Estimasi Kebutuhan Kalori Harian</div>
                        <div class="fw-bold fs-5"><?= number_format((int) $hasil['estimasi_kalori']) ?> kkal</div>
                        <div class="small opacity-75">dengan level aktivitas sedang</div>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <a href="<?= BASE_URL ?>/gizi" class="btn btn-orange btn-sm fw-semibold flex-fill">
                            <i class="bi bi-egg-fried me-1"></i>Cek Gizi Makanan
                        </a>
                        <?php if ($user): ?>
                            <a href="<?= BASE_URL ?>/profil" class="btn btn-sm btn-light fw-semibold flex-fill">
                                <i class="bi bi-person me-1"></i>Lihat Profil
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Penjelasan -->
                <div class="panel-white h-100">
                    <h5 class="mb-3"><i class="bi bi-info-circle me-2 text-success"></i>Cara Menghitung</h5>
                    <p class="small text-muted">
                        BMI = berat badan (kg) ÷ tinggi badan (m)<sup>2</sup><br>
                        Contoh: 62 kg ÷ (1,70 m)<sup>2</sup> = <b>21,5 (Normal)</b>
                    </p>
                    <div class="table-responsive">
                        <table class="table table-custom small mb-0">
                            <thead><tr><th>Kategori</th><th>Rentang BMI</th></tr></thead>
                            <tbody>
                                <tr><td><span class="badge badge-bmi-kurus">Kurus</span></td><td>&lt; 18,5</td></tr>
                                <tr><td><span class="badge badge-bmi-normal">Normal</span></td><td>18,5 – 24,9</td></tr>
                                <tr><td><span class="badge badge-bmi-kelebihan">Kelebihan Berat</span></td><td>25,0 – 29,9</td></tr>
                                <tr><td><span class="badge badge-bmi-obesitas">Obesitas</span></td><td>≥ 30,0</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tip-box mt-3">
                        <b><i class="bi bi-lightbulb-fill me-1"></i>Tips:</b>
                        Isi form di samping lalu tekan <b>Hitung BMI Saya</b>.
                        Pratinjau skor tampil otomatis saat Anda mengetik.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
