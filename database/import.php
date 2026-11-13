<?php
/**
 * Skrip bantu impor database (dijalankan sekali dari terminal):
 *   php database/import.php
 *
 * Alternatif: impor database/healthyhabit.sql melalui phpMyAdmin.
 */

$sqlFile = __DIR__ . '/healthyhabit.sql';
if (!is_file($sqlFile)) {
    exit("File SQL tidak ditemukan: $sqlFile\n");
}

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    exit('Koneksi MySQL gagal: ' . $e->getMessage() . "\n");
}

$sql = file_get_contents($sqlFile);

// Buang komentar baris agar pemisahan statement aman
$lines = preg_split('/\r\n|\r|\n/', $sql);
$bersih = [];
foreach ($lines as $line) {
    if (preg_match('/^\s*--/', $line)) {
        continue;
    }
    $bersih[] = $line;
}
$sql = implode("\n", $bersih);

$statements = array_filter(array_map('trim', explode(';', $sql)), fn($s) => $s !== '');

$ok = 0;
foreach ($statements as $stmt) {
    try {
        $pdo->exec($stmt);
        $ok++;
    } catch (PDOException $e) {
        fwrite(STDERR, "Gagal: " . substr($stmt, 0, 80) . "...\n  " . $e->getMessage() . "\n");
    }
}

echo "Selesai. $ok statement berhasil dieksekusi.\n";

// Verifikasi
$pdo->exec('USE healthyhabit');
$tabel = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
echo 'Jumlah tabel : ' . count($tabel) . "\n";
echo 'Tabel        : ' . implode(', ', $tabel) . "\n";
echo 'Artikel      : ' . $pdo->query('SELECT COUNT(*) FROM artikel_kesehatan')->fetchColumn() . "\n";
echo 'Gizi         : ' . $pdo->query('SELECT COUNT(*) FROM kandungan_gizi')->fetchColumn() . "\n";
echo 'Resep        : ' . $pdo->query('SELECT COUNT(*) FROM resep')->fetchColumn() . "\n";
echo 'Tempat       : ' . $pdo->query('SELECT COUNT(*) FROM tempat_sehat')->fetchColumn() . "\n";
echo 'Menu         : ' . $pdo->query('SELECT COUNT(*) FROM menu')->fetchColumn() . "\n";
echo 'Pengguna     : ' . $pdo->query('SELECT COUNT(*) FROM pengguna')->fetchColumn() . "\n";
echo 'Ulasan       : ' . $pdo->query('SELECT COUNT(*) FROM ulasan_rating')->fetchColumn() . "\n";
