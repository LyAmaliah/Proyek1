<?php
/**
 * Uji end-to-end HealthyHabit (PHP built-in server + MySQL).
 * Jalankan: php hh_smoke.php
 */

const BASE = 'http://127.0.0.1:8123';

$lulus = 0;
$gagal = 0;
$cookieDir = sys_get_temp_dir();

// Bersihkan cookie sesi sisa eksekusi sebelumnya
foreach (glob(sys_get_temp_dir() . '/hh_*.txt') ?: [] as $f) {
    @unlink($f);
}

function buatHandle(string $nama): CurlHandle
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_COOKIEJAR      => sys_get_temp_dir() . "/hh_{$nama}.txt",
        CURLOPT_COOKIEFILE     => sys_get_temp_dir() . "/hh_{$nama}.txt",
        CURLOPT_TIMEOUT        => 20,
    ]);
    return $ch;
}

function minta(CurlHandle $ch, string $path, ?array $post = null): array
{
    curl_setopt($ch, CURLOPT_URL, BASE . $path);
    curl_setopt($ch, CURLOPT_POST, $post !== null);
    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    $raw  = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $hdr  = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($raw, $hdr);
    $loc  = '';
    if (preg_match('/^Location:\s*(.+)$/mi', substr($raw, 0, $hdr), $m)) {
        $loc = trim($m[1]);
    }
    return ['code' => $code, 'body' => $body, 'loc' => $loc];
}

function token(string $body): string
{
    return preg_match('/name="_token" value="([^"]+)"/', $body, $m) ? $m[1] : '';
}

function cek(string $judul, bool $kondisi, string $detail = ''): void
{
    global $lulus, $gagal;
    if ($kondisi) {
        $lulus++;
        echo "  [OK]   $judul\n";
    } else {
        $gagal++;
        echo "  [GAGAL] $judul" . ($detail ? " -> $detail" : '') . "\n";
    }
}

function adaError(string $body): string
{
    foreach (['Fatal error', 'Parse error', 'Warning:', 'Notice:', 'Deprecated:', 'Uncaught'] as $pola) {
        if (stripos($body, $pola) !== false) {
            $pos = stripos($body, $pola);
            return $pola . ': ' . substr(strip_tags(substr($body, $pos, 220)), 0, 200);
        }
    }
    return '';
}

echo "=== UJI HALAMAN PUBLIK ===\n";
$publik = [
    '/'            => 'beranda',
    '/artikel'     => 'daftar artikel',
    '/artikel/1'   => 'detail artikel',
    '/gizi'        => 'cek gizi',
    '/gizi?q=jus'  => 'cek gizi + pencarian',
    '/resep'       => 'daftar resep',
    '/resep/1'     => 'detail resep',
    '/tempat'      => 'daftar tempat',
    '/tempat/1'    => 'detail tempat + ulasan',
    '/auth/login'  => 'form login',
    '/auth/register' => 'form registrasi',
    '/bmi'         => 'kalkulator BMI',
    '/admin/login' => 'form login admin',
    '/halaman-tidak-ada' => 'halaman 404',
];
$chTamu = buatHandle('tamu');
foreach ($publik as $path => $label) {
    $r = minta($chTamu, $path);
    $err = adaError($r['body']);
    $harap = ($path === '/halaman-tidak-ada') ? 404 : 200;
    cek("$label ($path) status $harap", $r['code'] === $harap, 'dapat ' . $r['code']);
    if ($err) {
        cek("$label tanpa error PHP", false, $err);
    }
}

echo "\n=== UJI AKSES TERLINDUNG (belum login) ===\n";
foreach (['/profil', '/admin', '/admin/artikel', '/admin/laporan'] as $path) {
    $r = minta(buatHandle('tamu2'), $path);
    cek("$path dialihkan ke login", $r['code'] === 302, 'dapat ' . $r['code']);
}

echo "\n=== UJI LOGIN + PROFIL + BMI (PENGGUNA) ===\n";
$chUser = buatHandle('user');
$r = minta($chUser, '/auth/login');
$t = token($r['body']);
$r = minta($chUser, '/auth/login', ['_token' => $t, 'username' => 'andi_sehat', 'password' => 'user123']);
cek('login pengguna berhasil (302 ke /profil)', $r['code'] === 302 && str_contains($r['loc'], '/profil'), $r['loc']);

$r = minta($chUser, '/profil');
cek('halaman profil memuat data pengguna', $r['code'] === 200 && str_contains($r['body'], 'Andi Pratama'));
cek('BMI terakhir tertampil di profil', str_contains($r['body'], 'Normal'));
cek('estimasi kalori tertampil', str_contains($r['body'], 'kkal / hari'));

// Update profil + hitung ulang BMI
$t = token($r['body']);
$r = minta($chUser, '/profil', [
    '_token' => $t, 'nama_lengkap' => 'Andi Pratama', 'umur' => 25,
    'berat_badan' => 70, 'tinggi_badan' => 170, 'jenis_kelamin' => 'L', 'password' => '',
]);
cek('update profil + BMI dihitung ulang (302)', $r['code'] === 302);

$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=healthyhabit;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$bmiBaru = $pdo->query('SELECT nilai_bmi, kategori_bmi, estimasi_kalori FROM kalkulator_bmi WHERE id_pengguna=1 ORDER BY id_bmi DESC LIMIT 1')->fetch();
cek('BMI tersimpan di database (70kg/170cm = 24.2)', $bmiBaru && (float) $bmiBaru['nilai_bmi'] === 24.2,
    json_encode($bmiBaru));

// Kalkulator BMI publik (tamu)
$chTamu3 = buatHandle('tamu3');
$r = minta($chTamu3, '/bmi');
$t = token($r['body']);
$r = minta($chTamu3, '/bmi', ['_token' => $t, 'berat_badan' => 80, 'tinggi_badan' => 160, 'umur' => 30, 'jenis_kelamin' => 'P']);
cek('kalkulator BMI tamu (80kg/160cm = 31.3 Obesitas)', $r['code'] === 200 && str_contains($r['body'], '31.3') && str_contains($r['body'], 'Obesitas'));

// Anti-duplikat: Andi sudah mengulas tempat 2 di data awal -> form tidak muncul
$r = minta($chUser, '/tempat/2');
cek('ulasan duplikat dicegah (form tidak dirender)', str_contains($r['body'], 'sudah mengulas'));

// Kirim ulasan tempat 2 dengan sari_dewi (belum pernah mengulas tempat ini)
$chSari = buatHandle('sari');
$r = minta($chSari, '/auth/login');
$t = token($r['body']);
$r = minta($chSari, '/auth/login', ['_token' => $t, 'username' => 'sari_dewi', 'password' => 'user123']);
cek('login pengguna kedua (sari_dewi)', $r['code'] === 302);

$r = minta($chSari, '/tempat/2');
$t = token($r['body']);
$r = minta($chSari, '/tempat/2/ulasan', ['_token' => $t, 'rating' => 4, 'komentar' => 'Pelayanan cepat dan menunya sehat sekali.']);
cek('kirim ulasan & rating (302)', $r['code'] === 302, 'dapat ' . $r['code'] . ' ' . $r['loc']);
$jmlUlasan = (int) $pdo->query('SELECT COUNT(*) FROM ulasan_rating WHERE id_tempat=2')->fetchColumn();
cek('ulasan tersimpan di database', $jmlUlasan >= 2, "jumlah: $jmlUlasan");
cek('rating rata-rata tempat 2 terhitung ulang',
    (float) $pdo->query('SELECT ROUND(AVG(rating),1) FROM ulasan_rating WHERE id_tempat=2')->fetchColumn() === 4.5);

echo "\n=== UJI ASET DESAIN (Poppins, Bootstrap 5, Palet Warna) ===\n";
$r = minta($chTamu3, '/');
foreach ([
    'Google Font Poppins'        => 'family=Poppins',
    'Bootstrap 5 CSS (CDN)'      => 'bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'Bootstrap 5 JS Bundle (CDN)' => 'bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
    'Bootstrap Icons (CDN)'      => 'bootstrap-icons@1.11.3',
    'Style kustom dimuat'        => '/assets/css/style.css',
    'JavaScript kustom dimuat'   => '/assets/js/main.js',
] as $label => $pola) {
    cek($label, str_contains($r['body'], $pola));
}
$r = minta($chTamu3, '/assets/css/style.css');
foreach ([
    'Hijau #27824B'      => '#27824B',
    'Hijau muda #E8F5E9' => '#E8F5E9',
    'Putih #FFFFFF'      => '#FFFFFF',
    'Oranye #F5A623'     => '#F5A623',
    'Abu-abu #F5F5F5'    => '#F5F5F5',
    'Media query cetak'  => '@media print',
] as $label => $pola) {
    cek("Palet $label", str_contains($r['body'], $pola));
}
cek('placeholder gambar tersedia', minta($chTamu3, '/assets/img/placeholder.svg')['code'] === 200);
$r = minta($chTamu3, '/');
cek('halaman memuat modul Responsive viewport', str_contains($r['body'], 'name="viewport"'));
cek('navbar responsif (navbar-toggler) tampil', str_contains($r['body'], 'navbar-toggler'));

echo "\n=== UJI LOGIN ADMIN + CRUD ===\n";
$chAdmin = buatHandle('admin');
$r = minta($chAdmin, '/admin/login');
$t = token($r['body']);
$r = minta($chAdmin, '/admin/login', ['_token' => $t, 'username' => 'admin', 'password' => 'admin123']);
cek('login admin berhasil', $r['code'] === 302 && str_contains($r['loc'], '/admin'), $r['loc']);

foreach ([
    '/admin'           => 'dashboard admin',
    '/admin/artikel'   => 'kelola artikel',
    '/admin/kategori'  => 'kelola kategori',
    '/admin/gizi'      => 'kelola gizi',
    '/admin/resep'     => 'kelola resep',
    '/admin/tempat'    => 'kelola tempat',
    '/admin/tempat/1/menu' => 'kelola menu tempat',
    '/admin/pengguna'  => 'kelola pengguna',
    '/admin/laporan'   => 'laporan statistik',
    '/admin/laporan/cetak' => 'halaman cetak laporan',
    '/admin/artikel/create' => 'form tambah artikel',
    '/admin/artikel/1/edit' => 'form edit artikel',
    '/admin/gizi/1/edit'    => 'form edit gizi',
    '/admin/resep/1/edit'   => 'form edit resep',
    '/admin/tempat/1/edit'  => 'form edit tempat',
    '/admin/pengguna/1/edit' => 'form edit pengguna',
] as $path => $label) {
    $r = minta(buatHandle('admin2'), $path); // handle baru TIDAK login -> harus 302
    $err = adaError($r['body']);
    if ($err) {
        cek("$label tanpa error PHP", false, $err);
    }
}
$chAdmin = buatHandle('admin');
$r = minta($chAdmin, '/admin/login');
$t = token($r['body']);
minta($chAdmin, '/admin/login', ['_token' => $t, 'username' => 'admin', 'password' => 'admin123']);

foreach ([
    '/admin'           => 'dashboard admin',
    '/admin/artikel'   => 'kelola artikel',
    '/admin/kategori'  => 'kelola kategori',
    '/admin/gizi'      => 'kelola gizi',
    '/admin/resep'     => 'kelola resep',
    '/admin/tempat'    => 'kelola tempat',
    '/admin/tempat/1/menu' => 'kelola menu tempat',
    '/admin/pengguna'  => 'kelola pengguna',
    '/admin/laporan'   => 'laporan statistik',
    '/admin/laporan/cetak' => 'halaman cetak laporan',
    '/admin/artikel/create' => 'form tambah artikel',
    '/admin/artikel/1/edit' => 'form edit artikel',
    '/admin/gizi/1/edit'    => 'form edit gizi',
    '/admin/resep/1/edit'   => 'form edit resep',
    '/admin/tempat/1/edit'  => 'form edit tempat',
    '/admin/pengguna/1/edit' => 'form edit pengguna',
] as $path => $label) {
    $r = minta($chAdmin, $path);
    $err = adaError($r['body']);
    cek("$label ($path) status 200", $r['code'] === 200, 'dapat ' . $r['code']);
    if ($err) {
        cek("$label tanpa error PHP", false, $err);
    }
}

// --- CRUD Gizi: create -> verifikasi DB -> edit -> delete ---
$r = minta($chAdmin, '/admin/gizi/create');
$t = token($r['body']);
$r = minta($chAdmin, '/admin/gizi/create', [
    '_token' => $t, 'nama_makanan_minuman' => 'UJI OTOMATIS SAYUR KANGKUNG', 'kategori' => 'Makanan',
    'porsi' => '100 gram', 'kalori' => 40, 'karbohidrat' => 6.5, 'protein' => 3.4, 'lemak' => 0.7,
]);
cek('tambah data gizi (302)', $r['code'] === 302);
$row = $pdo->query("SELECT * FROM kandungan_gizi WHERE nama_makanan_minuman LIKE 'UJI OTOMATIS%'")->fetch();
cek('data gizi tersimpan di database', !empty($row), 'tidak ditemukan');

if ($row) {
    $id = (int) $row['id_gizi'];
    $r = minta($chAdmin, "/admin/gizi/$id/edit");
    $t = token($r['body']);
    $r = minta($chAdmin, "/admin/gizi/$id/edit", [
        '_token' => $t, 'nama_makanan_minuman' => 'UJI OTOMATIS SAYUR KANGKUNG', 'kategori' => 'Makanan',
        'porsi' => '100 gram', 'kalori' => 45, 'karbohidrat' => 6.5, 'protein' => 3.4, 'lemak' => 0.7,
    ]);
    $cekEdit = (float) $pdo->query("SELECT kalori FROM kandungan_gizi WHERE id_gizi=$id")->fetchColumn();
    cek('edit data gizi tersimpan (45 kkal)', $cekEdit === 45.0, "kalori: $cekEdit");

    $r = minta($chAdmin, "/admin/gizi/$id/delete", ['_token' => $t]);
    $sisa = (int) $pdo->query("SELECT COUNT(*) FROM kandungan_gizi WHERE id_gizi=$id")->fetchColumn();
    cek('hapus data gizi berhasil', $r['code'] === 302 && $sisa === 0, "sisa: $sisa");
}

// --- CRUD Artikel (dengan kategori) ---
$r = minta($chAdmin, '/admin/artikel/create');
$t = token($r['body']);
$r = minta($chAdmin, '/admin/artikel/create', [
    '_token' => $t, 'judul_artikel' => 'UJI OTOMATIS ARTIKEL KESEHATAN', 'id_kategori_artikel' => 1,
    'isi_artikel' => 'Isi artikel percobaan otomatis untuk validasi CRUD.', 'tanggal_terbit' => date('Y-m-d'),
]);
$art = $pdo->query("SELECT * FROM artikel_kesehatan WHERE judul_artikel LIKE 'UJI OTOMATIS%'")->fetch();
cek('tambah artikel berhasil + tersimpan', $r['code'] === 302 && !empty($art));
if ($art) {
    $r = minta($chAdmin, "/admin/artikel/{$art['id_artikel']}/delete", ['_token' => $t]);
    cek('hapus artikel berhasil', $r['code'] === 302 &&
        (int) $pdo->query("SELECT COUNT(*) FROM artikel_kesehatan WHERE id_artikel={$art['id_artikel']}")->fetchColumn() === 0);
}

// --- CRUD Menu (nested) ---
$r = minta($chAdmin, '/admin/tempat/1/menu');
$t = token($r['body']);
$r = minta($chAdmin, '/admin/tempat/1/menu/create', [
    '_token' => $t, 'nama_menu' => 'UJI OTOMATIS MENU SEHAT', 'deskripsi' => 'Menu uji coba',
    'harga' => 21000, 'protein' => 5, 'karbohidrat' => 10, 'lemak' => 2, 'estimasi_waktu' => 6,
]);
$menu = $pdo->query("SELECT * FROM menu WHERE nama_menu LIKE 'UJI OTOMATIS%'")->fetch();
cek('tambah menu tempat berhasil', $r['code'] === 302 && !empty($menu));
if ($menu) {
    $r = minta($chAdmin, "/admin/tempat/1/menu/{$menu['id_menu']}/delete", ['_token' => $t]);
    cek('hapus menu berhasil', $r['code'] === 302 &&
        (int) $pdo->query("SELECT COUNT(*) FROM menu WHERE id_menu={$menu['id_menu']}")->fetchColumn() === 0);
}

// --- Uji CSRF: POST tanpa token harus ditolak (419) ---
$r = minta($chAdmin, '/admin/gizi/create', ['nama_makanan_minuman' => 'Tanpa Token', 'kategori' => 'Makanan',
    'porsi' => '100 gram', 'kalori' => 1, 'karbohidrat' => 1, 'protein' => 1, 'lemak' => 1]);
cek('POST tanpa token CSRF ditolak (419)', $r['code'] === 419, 'dapat ' . $r['code']);

// --- Registrasi pengguna baru ---
$chReg = buatHandle('reg');
$r = minta($chReg, '/auth/register');
$t = token($r['body']);
$r = minta($chReg, '/auth/register', [
    '_token' => $t, 'nama_lengkap' => 'Uji Pendaftar', 'username' => 'uji_daftar', 'password' => 'rahasia123',
    'password_confirm' => 'rahasia123', 'berat_badan' => 58, 'tinggi_badan' => 165, 'umur' => 22, 'jenis_kelamin' => 'P',
]);
$baru = $pdo->query("SELECT * FROM pengguna WHERE username='uji_daftar'")->fetch();
cek('registrasi pengguna baru + BMI awal', $r['code'] === 302 && !empty($baru));
if ($baru) {
    $cek = (int) $pdo->query("SELECT COUNT(*) FROM kalkulator_bmi WHERE id_pengguna={$baru['id_pengguna']}")->fetchColumn();
    cek('BMI awal otomatis dibuat saat registrasi', $cek === 1, "jumlah: $cek");
    $pdo->exec("DELETE FROM pengguna WHERE username='uji_daftar'");
}

// --- Laporan: snapshot tercatat saat cetak ---
$sebelum = (int) $pdo->query('SELECT COUNT(*) FROM laporan_statistik')->fetchColumn();
minta($chAdmin, '/admin/laporan/cetak');
$sesudah = (int) $pdo->query('SELECT COUNT(*) FROM laporan_statistik')->fetchColumn();
cek('cetak laporan menyimpan snapshot statistik', $sesudah === $sebelum + 1, "sebelum $sebelum, sesudah $sesudah");

echo "\n==============================\n";
echo "LULUS: $lulus | GAGAL: $gagal\n";
echo "==============================\n";
exit($gagal === 0 ? 0 : 1);
