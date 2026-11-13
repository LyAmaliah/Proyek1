<?php
/**
 * Profil pengguna + ringkasan BMI — FR-09.
 * @var array $pengguna @var array|null $bmi @var array $riwayat
 */
$jk = strtoupper($pengguna['jenis_kelamin']);
?>
<div class="container mt-4 mb-5">
    <div class="text-center mb-4">
        <h1 class="section-title d-inline-block">Profil &amp; Kalkulator BMI</h1>
        <p class="text-muted mt-2 mb-0">Kelola data diri — BMI &amp; estimasi kalori dihitung ulang otomatis</p>
    </div>

    <div class="row g-4">
        <!-- ============ FORM PROFIL ============ -->
        <div class="col-lg-7">
            <div class="panel-white">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="brand-icon" style="width:56px;height:56px;font-size:1.6rem;">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <div>
                        <h5 class="mb-0"><?= e($pengguna['nama_lengkap']) ?></h5>
                        <small class="text-muted">
                            <i class="bi bi-at me-1"></i><?= e($pengguna['username']) ?> ·
                            Bergabung <?= tanggal_indo($pengguna['created_at']) ?>
                        </small>
                    </div>
                </div>

                <form action="<?= BASE_URL ?>/profil" method="post" autocomplete="off">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nama_lengkap" class="form-label fw-semibold small">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                   minlength="3" required value="<?= e($pengguna['nama_lengkap']) ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Username</label>
                            <input type="text" class="form-control" value="<?= e($pengguna['username']) ?>" disabled>
                            <div class="form-text">Username tidak dapat diubah.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="umur" class="form-label fw-semibold small">Umur (tahun)</label>
                            <input type="number" class="form-control" id="umur" name="umur"
                                   min="5" max="100" required value="<?= e($pengguna['umur']) ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="berat_badan" class="form-label fw-semibold small">Berat Badan (kg)</label>
                            <input type="number" step="0.1" class="form-control" id="berat_badan"
                                   name="berat_badan" min="20" max="300" required
                                   value="<?= e($pengguna['berat_badan']) ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="tinggi_badan" class="form-label fw-semibold small">Tinggi Badan (cm)</label>
                            <input type="number" step="0.1" class="form-control" id="tinggi_badan"
                                   name="tinggi_badan" min="50" max="250" required
                                   value="<?= e($pengguna['tinggi_badan']) ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold small d-block">Jenis Kelamin</label>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="jenis_kelamin" id="pL"
                                       value="L" <?= $jk === 'L' ? 'checked' : '' ?>>
                                <label class="btn btn-outline-success" for="pL">
                                    <i class="bi bi-gender-male me-1"></i>Laki-laki</label>

                                <input type="radio" class="btn-check" name="jenis_kelamin" id="pP"
                                       value="P" <?= $jk === 'P' ? 'checked' : '' ?>>
                                <label class="btn btn-outline-success" for="pP">
                                    <i class="bi bi-gender-female me-1"></i>Perempuan</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="password" class="form-label fw-semibold small">Password Baru (opsional)</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   minlength="6" placeholder="Kosongkan jika tidak ingin mengganti password">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4 flex-wrap">
                        <button type="submit" class="btn btn-green fw-semibold px-4">
                            <i class="bi bi-save me-1"></i>Simpan &amp; Hitung Ulang BMI
                        </button>
                        <a href="<?= BASE_URL ?>/bmi" class="btn btn-soft-green fw-semibold">
                            <i class="bi bi-speedometer2 me-1"></i>Kalkulator BMI
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============ HASIL BMI ============ -->
        <div class="col-lg-5">
            <div class="calc-panel">
                <h4 class="mb-3 text-white"><i class="bi bi-speedometer2 me-2"></i>Hasil BMI Terakhir</h4>

                <?php if ($bmi): ?>
                    <div class="calc-result text-center mb-3">
                        <div class="small text-uppercase opacity-75">Skor BMI Anda</div>
                        <div class="big text-warning"><?= number_format((float) $bmi['nilai_bmi'], 1) ?></div>
                        <span class="badge mt-2 <?= ['Kurus' => 'badge-bmi-kurus', 'Normal' => 'badge-bmi-normal',
                            'Kelebihan Berat' => 'badge-bmi-kelebihan', 'Obesitas' => 'badge-bmi-obesitas']
                            [$bmi['kategori_bmi']] ?? 'bg-light' ?>">
                            <?= e($bmi['kategori_bmi']) ?>
                        </span>
                    </div>

                    <div class="row g-2 text-center small">
                        <div class="col-4">
                            <div class="calc-result py-2">
                                <div class="opacity-75">Berat</div>
                                <div class="fw-bold"><?= number_format((float) $bmi['berat_badan'], 1) ?> kg</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="calc-result py-2">
                                <div class="opacity-75">Tinggi</div>
                                <div class="fw-bold"><?= number_format((float) $bmi['tinggi_badan'], 1) ?> cm</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="calc-result py-2">
                                <div class="opacity-75">Umur</div>
                                <div class="fw-bold"><?= (int) $bmi['umur'] ?> th</div>
                            </div>
                        </div>
                    </div>

                    <div class="calc-result mt-3 text-center">
                        <div class="small opacity-75">Estimasi Kebutuhan Kalori Harian</div>
                        <div class="fw-bold fs-5">
                            <?= number_format((int) $bmi['estimasi_kalori']) ?> kkal / hari
                        </div>
                        <div class="small opacity-75 mt-1">Rumus Mifflin-St Jeor × aktivitas sedang</div>
                    </div>

                    <p class="small opacity-75 mt-3 mb-0">
                        <i class="bi bi-arrow-repeat me-1"></i>Nilai diperbarui otomatis setiap kali profil disimpan
                    </p>
                <?php else: ?>
                    <div class="calc-result text-center">
                        <p class="mb-2 opacity-75">Belum ada perhitungan BMI.</p>
                        <a href="<?= BASE_URL ?>/bmi" class="btn btn-orange btn-sm fw-semibold">
                            Hitung BMI Sekarang
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Riwayat -->
            <?php if (count($riwayat) > 1): ?>
                <div class="panel-white mt-4">
                    <h6 class="mb-3"><i class="bi bi-clock-history me-2 text-success"></i>Riwayat BMI</h6>
                    <div class="table-responsive">
                        <table class="table table-custom align-middle mb-0 small">
                            <thead>
                                <tr><th>BMI</th><th>Kategori</th><th class="text-end">Kalori</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($riwayat as $r): ?>
                                    <tr>
                                        <td class="fw-semibold"><?= number_format((float) $r['nilai_bmi'], 1) ?></td>
                                        <td><span class="tag"><?= e($r['kategori_bmi']) ?></span></td>
                                        <td class="text-end"><?= number_format((int) $r['estimasi_kalori']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
