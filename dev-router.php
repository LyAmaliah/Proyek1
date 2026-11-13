<?php
/**
 * Router untuk PHP Built-in Server (opsional, hanya untuk pengembangan
 * tanpa Apache). Jalankan dari folder proyek:
 *
 *    php -S localhost:8000 dev-router.php
 *
 * Untuk produksi/localhost XAMPP tetap gunakan Apache + .htaccess.
 */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$file = __DIR__ . $path;

// Layani aset statis apa adanya (CSS, JS, gambar, upload)
if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
