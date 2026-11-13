<?php /** Form Registrasi Pengguna — FR-08 */ ?>
<div class="auth-wrap">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-7">

                <div class="auth-card">
                    <div class="text-center mb-4">
                        <span class="brand-icon mx-auto mb-2" style="width:52px;height:52px;font-size:1.5rem;">
                            <i class="bi bi-person-plus-fill"></i>
                        </span>
                        <h2 class="h4 mb-1">Registrasi Pengguna Baru</h2>
                        <p class="text-muted small mb-0">
                            Sudah punya akun?
                            <a href="<?= BASE_URL ?>/auth/login" class="fw-semibold">Login di sini</a>
                        </p>
                    </div>

                    <form action="<?= BASE_URL ?>/auth/register" method="post" autocomplete="off">
                        <?= csrf_field() ?>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="nama_lengkap" class="form-label fw-semibold">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                       placeholder="mis. Andi Pratama" minlength="3" required
                                       value="<?= e($_POST['nama_lengkap'] ?? '') ?>">
                            </div>

                            <div class="col-md-6">
                                <label for="username" class="form-label fw-semibold">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                       placeholder="4–20 karakter" pattern="[A-Za-z0-9_]{4,20}" required
                                       value="<?= e($_POST['username'] ?? '') ?>">
                                <div class="form-text">Huruf, angka, underscore (4–20 karakter).</div>
                            </div>

                            <div class="col-md-6">
                                <label for="umur" class="form-label fw-semibold">Umur (tahun)</label>
                                <input type="number" class="form-control" id="umur" name="umur"
                                       min="5" max="100" placeholder="mis. 25" required
                                       value="<?= e($_POST['umur'] ?? '') ?>">
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                       minlength="6" placeholder="minimal 6 karakter" required>
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirm" class="form-label fw-semibold">Ulangi Password</label>
                                <input type="password" class="form-control" id="password_confirm"
                                       name="password_confirm" minlength="6" required>
                            </div>

                            <div class="col-md-6">
                                <label for="berat_badan" class="form-label fw-semibold">Berat Badan (kg)</label>
                                <input type="number" step="0.1" class="form-control" id="berat_badan"
                                       name="berat_badan" min="20" max="300" placeholder="mis. 62.5" required
                                       value="<?= e($_POST['berat_badan'] ?? '') ?>">
                            </div>

                            <div class="col-md-6">
                                <label for="tinggi_badan" class="form-label fw-semibold">Tinggi Badan (cm)</label>
                                <input type="number" step="0.1" class="form-control" id="tinggi_badan"
                                       name="tinggi_badan" min="50" max="250" placeholder="mis. 170" required
                                       value="<?= e($_POST['tinggi_badan'] ?? '') ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold d-block">Jenis Kelamin</label>
                                <div class="btn-group w-100" role="group" aria-label="Jenis kelamin">
                                    <input type="radio" class="btn-check" name="jenis_kelamin"
                                           id="jkL" value="L" <?= (($_POST['jenis_kelamin'] ?? '') === 'L' ? 'checked' : '') ?>>
                                    <label class="btn btn-outline-success" for="jkL">
                                        <i class="bi bi-gender-male me-1"></i>Laki-laki</label>

                                    <input type="radio" class="btn-check" name="jenis_kelamin"
                                           id="jkP" value="P" <?= (($_POST['jenis_kelamin'] ?? '') === 'P' ? 'checked' : '') ?>>
                                    <label class="btn btn-outline-success" for="jkP">
                                        <i class="bi bi-gender-female me-1"></i>Perempuan</label>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-light border mt-4 small mb-3">
                            <i class="bi bi-info-circle text-success me-1"></i>
                            Data berat &amp; tinggi badan dipakai untuk
                            <b>hitung BMI otomatis</b> saat registrasi selesai.
                        </div>

                        <button type="submit" class="btn btn-green w-100 fw-semibold py-2">
                            <i class="bi bi-check2-circle me-1"></i>Daftar Sekarang
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
