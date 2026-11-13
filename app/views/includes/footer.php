<?php
/**
 * includes/footer.php — Footer Situs + Bootstrap JS Bundle
 * Berlaku untuk layout utama (main) dan dashboard admin.
 */
?>
</main>

<!-- ======================= FOOTER ======================= -->
<footer class="site-footer bg-white border-top mt-5">
    <div class="container py-4">
        <div class="row g-4 align-items-start">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <img src="<?= BASE_URL ?>/uploads/logo/logo_healthyhabit.png" alt="Logo HealthyHabit" width="40" height="40" class="d-inline-block align-text-top">
                    <span class="fw-bold fs-5">Healthy<span class="text-accent">Habit</span></span>
                </div>
                <p class="text-muted small mb-2"><?= e(APP_TAGLINE) ?></p>
                <p class="small text-muted mb-0">
                    <i class="bi bi-geo-alt me-1"></i>Indramayu, Jawa Barat<br>
                    <i class="bi bi-envelope me-1"></i>halo@healthyhabit.test
                </p>
            </div>
            <div class="col-6 col-md-2">
                <h6 class="fw-semibold mb-3">Menu</h6>
                <ul class="list-unstyled small footer-links">
                    <li class="mb-2"><a href="<?= BASE_URL ?>/artikel">Artikel Kesehatan</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/gizi">Cek Gizi &amp; Kalori</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/resep">Resep Sehat</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/tempat">Tempat Sehat</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-2">
                <h6 class="fw-semibold mb-3">Akun</h6>
                <ul class="list-unstyled small footer-links">
                    <li class="mb-2"><a href="<?= BASE_URL ?>/auth/register">Registrasi</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/auth/login">Login</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/profil">Profil &amp; BMI</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL ?>/admin/login">Login Admin</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="fw-semibold mb-3">Tips Hari Ini</h6>
                <div class="tip-box">
                    <i class="bi bi-lightbulb-fill text-accent me-2"></i>
                    Minum minimal 8 gelas air putih setiap hari dan sisipkan 30 menit aktivitas
                    fisik untuk menjaga metabolisme tubuh tetap sehat.
                </div>
            </div>
        </div>
        <hr class="my-3">
        <div class="d-flex justify-content-center align-items-center">
            <small class="text-muted">&copy; <?= date('Y') ?> <?= e(APP_NAME) ?> — Kebiasaan Sehat, Hidup Lebih Baik.</small>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Script aplikasi -->
<script src="<?= BASE_URL ?>/assets/js/main.js?v=<?= APP_VERSION ?>"></script>
</body>
</html>
