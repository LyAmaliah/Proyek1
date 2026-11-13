<?php
/**
 * HealthyHabit — Front Controller (titik masuk aplikasi)
 * Sistem Pembentukan Kebiasaan Hidup Sehat Berbasis Web Native
 * Semua request (kecuali file statis) diarahkan ke file ini
 * melalui modul mod_rewrite Apache (.htaccess). cekkk
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/helpers/helper.php';

/* --------------------------------------------------------------
 * Autoloader sederhana: core, models, controllers, admin controllers
 * ------------------------------------------------------------- */
spl_autoload_register(function (string $class): void {
    $candidates = [
        APP_PATH . '/core/' . $class . '.php',
        APP_PATH . '/models/' . $class . '.php',
        APP_PATH . '/controllers/' . $class . '.php',
        APP_PATH . '/controllers/admin/' . $class . '.php',
    ];
    foreach ($candidates as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

/* --------------------------------------------------------------
 * Daftar rute (route table)
 * ------------------------------------------------------------- */
$router = new Router();
require APP_PATH . '/routes.php';

$router->dispatch(
    $_SERVER['REQUEST_URI'] ?? '/',
    $_SERVER['REQUEST_METHOD'] ?? 'GET'
);
