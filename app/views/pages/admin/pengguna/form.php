<?php
/**
 * Form edit pengguna — FR-06.
 * @var array $pengguna @var array|null $bmi
 */
$val = fn(string $k, $default = '') => $pengguna[$k] ?? $default;
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h4 mb-0">Edit Pengguna: <?= e($pengguna['nama_lengkap']) ?></h1>
    </div>
    <a href="<?= BASE_URL ?>/admin/pengguna" class="btn btn-outline-success btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <form action="<?= BASE_URL ?>/admin/pengguna/<?= (int) $pengguna['id_pengguna'] ?>/edit"
              method="post" class="panel-white">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nama_lengkap" class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                           minlength="3" required value="<?= e($val('nama_lengkap')) ?>">
                </div>
                <div class="col-md-6">
                    <label for="username" class="form-label fw-semibold">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                           pattern="[A-Za-z0-9_]{4,20}" required value="<?= e($val('username')) ?>">
                </div>
                <div class="col-md-4">
                    <label for="berat_badan" class="form-label fw-semibold">Berat (kg)</label>
                    <input type="number" step="0.1" min="20" max="300" class="form-control"
                           id="berat_badan" name="berat_badan" required
                           value="<?= e($val('berat_badan')) ?>">
                </div>
                <div class="col-md-4">
                    <label for="tinggi_badan" class="form-label fw-semibold">Tinggi (cm)</label>
                    <input type="number" step="0.1" min="50" max="250" class="form-control"
                           id="tinggi_badan" name="tinggi_badan" required
                           value="<?= e($val('tinggi_badan')) ?>">
                </div>
                <div class="col-md-4">
                    <label for="umur" class="form-label fw-semibold">Umur (tahun)</label>
                    <input type="number" min="5" max="100" class="form-control" id="umur"
                           name="umur" required value="<?= e($val('umur')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold d-block">Jenis Kelamin</label>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="jenis_kelamin" id="eL"
                               value="L" <?= $val('jenis_kelamin') === 'L' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-success" for="eL">Laki-laki</label>
                        <input type="radio" class="btn-check" name="jenis_kelamin" id="eP"
                               value="P" <?= $val('jenis_kelamin') === 'P' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-success" for="eP">Perempuan</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label fw-semibold">Password Baru</label>
                    <input type="password" class="form-control" id="password" name="password"
                           minlength="6" placeholder="Kosongkan jika tidak diganti">
                </div>
            </div>

            <div class="alert alert-light border small mt-4 mb-3">
                <i class="bi bi-info-circle text-success me-1"></i>
                Perubahan berat/tinggi/umur/jenis kelamin akan <b>menghitung ulang BMI</b>
                dan riwayatnya tersimpan otomatis.
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-green fw-semibold px-4">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
                <a href="<?= BASE_URL ?>/admin/pengguna" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>

    <!-- Info tambahan -->
    <div class="col-lg-5">
        <div class="panel-white mb-4">
            <div class="form-section-title">Info Akun</div>
            <ul class="list-unstyled small mb-0">
                <li class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">ID Pengguna</span><b><?= (int) $pengguna['id_pengguna'] ?></b>
                </li>
                <li class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Username</span><b>@<?= e($pengguna['username']) ?></b>
                </li>
                <li class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Terdaftar</span><b><?= tanggal_indo($pengguna['created_at']) ?></b>
                </li>
                <li class="d-flex justify-content-between py-1">
                    <span class="text-muted">Password</span><b>•••••••• (tersimpan ter-hash)</b>
                </li>
            </ul>
        </div>

        <div class="calc-panel">
            <h6 class="text-white mb-3"><i class="bi bi-speedometer2 me-2"></i>BMI Terakhir</h6>
            <?php if ($bmi): ?>
                <div class="calc-result text-center">
                    <div class="big text-warning"><?= number_format((float) $bmi['nilai_bmi'], 1) ?></div>
                    <span class="badge badge-bmi-normal mt-1"><?= e($bmi['kategori_bmi']) ?></span>
                    <div class="small opacity-75 mt-2">
                        Estimasi kalori: <?= number_format((int) $bmi['estimasi_kalori']) ?> kkal/hari
                    </div>
                </div>
            <?php else: ?>
                <div class="calc-result text-center small opacity-75">
                    Belum ada perhitungan BMI.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
