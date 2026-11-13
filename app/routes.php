<?php
/**
 * Tabel rute aplikasi HealthyHabit.
 *
 * Pola URL : /{controller}/{aksi}/{parameter}
 * Format   : $router->get('/path/{id:\d+}', ['NamaController', 'method']);
 *
 * @var Router $router
 */

/* ==================== SITUS (PUBLIK) ==================== */

$router->get('/', ['HomeController', 'index']);
$router->get('/404', ['HomeController', 'notFound']);

/* --- Autentikasi pengguna (FR-08) --- */
$router->get('/auth/login', ['AuthController', 'login']);
$router->post('/auth/login', ['AuthController', 'doLogin']);
$router->get('/auth/register', ['AuthController', 'register']);
$router->post('/auth/register', ['AuthController', 'doRegister']);
$router->get('/auth/logout', ['AuthController', 'logout']);

/* --- Artikel kesehatan (FR-10) --- */
$router->get('/artikel', ['ArtikelController', 'index']);
$router->get('/artikel/{id:\d+}', ['ArtikelController', 'detail']);

/* --- Cek gizi & kalori (FR-11) --- */
$router->get('/gizi', ['GiziController', 'index']);

/* --- Resep sehat (FR-12) --- */
$router->get('/resep', ['ResepController', 'index']);
$router->get('/resep/{id:\d+}', ['ResepController', 'detail']);

/* --- Tempat sehat & ulasan (FR-13) --- */
$router->get('/tempat', ['TempatController', 'index']);
$router->get('/tempat/{id:\d+}', ['TempatController', 'detail']);
$router->post('/tempat/{id:\d+}/ulasan', ['TempatController', 'ulasan']);

/* --- Profil & BMI pengguna (FR-09) --- */
$router->get('/profil', ['UserController', 'profil']);
$router->post('/profil', ['UserController', 'updateProfil']);
$router->get('/bmi', ['UserController', 'bmi']);
$router->post('/bmi', ['UserController', 'hitungBmi']);

/* ==================== ADMIN ==================== */

$router->get('/admin', ['AdminDashboardController', 'index']);

/* --- Kelola artikel (FR-02) --- */
$router->get('/admin/artikel', ['AdminArtikelController', 'index']);
$router->post('/admin/artikel/kategori/store', ['AdminArtikelController', 'storeKategori']);
$router->post('/admin/artikel/kategori/{id:\d+}/update', ['AdminArtikelController', 'updateKategori']);
$router->post('/admin/artikel/kategori/{id:\d+}/delete', ['AdminArtikelController', 'destroyKategori']);
$router->get('/admin/artikel/create', ['AdminArtikelController', 'create']);
$router->post('/admin/artikel/create', ['AdminArtikelController', 'store']);
$router->get('/admin/artikel/{id:\d+}/edit', ['AdminArtikelController', 'edit']);
$router->post('/admin/artikel/{id:\d+}/edit', ['AdminArtikelController', 'update']);
$router->post('/admin/artikel/{id:\d+}/delete', ['AdminArtikelController', 'destroy']);

/* --- Kelola gizi & kalori (FR-03) --- */
$router->get('/admin/gizi', ['AdminGiziController', 'index']);
$router->get('/admin/gizi/create', ['AdminGiziController', 'create']);
$router->post('/admin/gizi/create', ['AdminGiziController', 'store']);
$router->get('/admin/gizi/{id:\d+}/edit', ['AdminGiziController', 'edit']);
$router->post('/admin/gizi/{id:\d+}/edit', ['AdminGiziController', 'update']);
$router->post('/admin/gizi/{id:\d+}/delete', ['AdminGiziController', 'destroy']);

/* --- Kelola resep (FR-04) --- */
$router->get('/admin/resep', ['AdminResepController', 'index']);
$router->get('/admin/resep/create', ['AdminResepController', 'create']);
$router->post('/admin/resep/create', ['AdminResepController', 'store']);
$router->get('/admin/resep/{id:\d+}/edit', ['AdminResepController', 'edit']);
$router->post('/admin/resep/{id:\d+}/edit', ['AdminResepController', 'update']);
$router->post('/admin/resep/{id:\d+}/delete', ['AdminResepController', 'destroy']);

/* --- Kelola tempat, menu & kategori menu (FR-05) --- */
$router->get('/admin/tempat', ['AdminTempatController', 'index']);
$router->post('/admin/tempat/kategori/store', ['AdminTempatController', 'storeKategoriMenu']);
$router->post('/admin/tempat/kategori/{id:\d+}/update', ['AdminTempatController', 'updateKategoriMenu']);
$router->post('/admin/tempat/kategori/{id:\d+}/delete', ['AdminTempatController', 'destroyKategoriMenu']);
$router->get('/admin/tempat/create', ['AdminTempatController', 'create']);
$router->post('/admin/tempat/create', ['AdminTempatController', 'store']);
$router->get('/admin/tempat/{id:\d+}/edit', ['AdminTempatController', 'edit']);
$router->post('/admin/tempat/{id:\d+}/edit', ['AdminTempatController', 'update']);
$router->post('/admin/tempat/{id:\d+}/delete', ['AdminTempatController', 'destroy']);
$router->get('/admin/tempat/{id:\d+}/menu', ['AdminTempatController', 'menu']);
$router->post('/admin/tempat/{id:\d+}/menu/create', ['AdminTempatController', 'storeMenu']);
$router->post('/admin/tempat/{id:\d+}/menu/{menu:\d+}/edit', ['AdminTempatController', 'updateMenu']);
$router->post('/admin/tempat/{id:\d+}/menu/{menu:\d+}/delete', ['AdminTempatController', 'destroyMenu']);

/* --- Kelola pengguna (FR-06) --- */
$router->get('/admin/pengguna', ['AdminPenggunaController', 'index']);
$router->get('/admin/pengguna/{id:\d+}/edit', ['AdminPenggunaController', 'edit']);
$router->post('/admin/pengguna/{id:\d+}/edit', ['AdminPenggunaController', 'update']);
$router->post('/admin/pengguna/{id:\d+}/delete', ['AdminPenggunaController', 'destroy']);

/* --- Cetak laporan (FR-07) --- */
$router->get('/admin/cetak', ['AdminLaporanController', 'cetak']);
