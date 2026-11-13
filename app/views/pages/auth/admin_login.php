<?php
/**
 * Form Login Admin — FR-01
 * Tampilan sengaja disamakan dengan form Login Pengguna (layout situs).
 */
?>
<div class="auth-wrap">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <div class="auth-card">
                    <div class="text-center mb-4">
                        <span class="brand-icon mx-auto mb-2" style="width:52px;height:52px;font-size:1.5rem;">
                            <i class="bi bi-shield-lock-fill"></i>
                        </span>
                        <h2 class="h4 mb-1">Login Admin</h2>
                        <p class="text-muted small mb-0">
                            Panel pengelolaan <b><?= e(APP_NAME) ?></b>
                        </p>
                    </div>

                    <form action="<?= BASE_URL ?>/admin/login" method="post" autocomplete="off">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-person-badge"></i></span>
                                <input type="text" class="form-control" id="username" name="username"
                                       placeholder="mis. admin" value="<?= e($_POST['username'] ?? '') ?>"
                                       required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="••••••••" required>
                                <button class="input-group-text bg-white" type="button" id="togglePassword"
                                        aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-green w-100 fw-semibold py-2">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                        </button>
                    </form>

                    <hr class="my-4">
                    <p class="text-center small text-muted mb-2">Login sebagai pengguna?</p>
                    <a href="<?= BASE_URL ?>/auth/login" class="btn btn-soft-green w-100 fw-semibold">
                        <i class="bi bi-person-circle me-1"></i>Login Pengguna
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // Tampilkan/sembunyikan password
    document.getElementById('togglePassword')?.addEventListener('click', function () {
        const pwd = document.getElementById('password');
        const icon = this.querySelector('i');
        const tampil = pwd.type === 'password';
        pwd.type = tampil ? 'text' : 'password';
        icon.className = tampil ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
</script>
