<?php
/**
 * Konfigurasi global aplikasi HealthyHabit
 */

/* ---------------- URL & Path ---------------- */

// Deteksi base URL otomatis (mendukung deployment di root atau subfolder htdocs)
$scriptDir   = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
define('BASE_URL', rtrim($scriptDir, '/')); // '' jika di root, '/healthyhabit' jika di subfolder

define('ROOT_PATH', dirname(__DIR__, 2));   // root proyek
define('APP_PATH', ROOT_PATH . '/app');
define('VIEWS_PATH', APP_PATH . '/views');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

/* ---------------- Aplikasi ---------------- */

define('APP_NAME', 'HealthyHabit');
define('APP_TAGLINE', 'Sistem Pembentukan Kebiasaan Hidup Sehat Berbasis Web Native');
define('APP_VERSION', '1.0.0');

/* ---------------- Database (XAMPP / MySQL) ---------------- */

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'healthyhabit1');
define('DB_USER', 'root');
define('DB_PASS', '');           // XAMPP default: password kosong
define('DB_CHARSET', 'utf8mb4');

/* ---------------- Upload Gambar ---------------- */

define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024); // 2 MB
define('UPLOAD_ALLOWED', ['jpg', 'jpeg', 'png', 'webp']);

/* ---------------- Umum ---------------- */

date_default_timezone_set('Asia/Jakarta');
mb_internal_encoding('UTF-8');
