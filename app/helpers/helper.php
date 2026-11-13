<?php
/**
 * Kumpulan helper global aplikasi HealthyHabit
 */

/* ================= Escaping & Redirect ================= */

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/* ================= CSRF Token ================= */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['_token'] ?? '';
    if (!is_string($token) || $token === '' || !hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit('Token CSRF tidak valid atau kedaluwarsa. Muat ulang halaman lalu coba lagi.');
    }
}

/* ================= Flash Message ================= */

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/** @return array<int, array{type:string, message:string}> */
function get_flash(): array
{
    $flash = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flash;
}

/* ================= Format Teks & Angka ================= */

function rupiah($number): string
{
    return 'Rp ' . number_format((float) $number, 0, ',', '.');
}

function tanggal_indo(?string $date, bool $withTime = false): string
{
    if (!$date) {
        return '-';
    }
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];
    $ts = strtotime($date);
    $hasil = date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    if ($withTime) {
        $hasil .= ', ' . date('H:i', $ts) . ' WIB';
    }
    return $hasil;
}

function excerpt(?string $html, int $length = 140): string
{
    $text = trim(strip_tags((string) $html));
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . '…';
}

/* ================= Upload Gambar ================= */

/**
 * Upload gambar ke uploads/<subdir>/.
 * Mengembalikan nama file baru, atau null jika tidak ada file yang diupload
 * (artinya gambar lama dipertahankan).
 *
 * @throws RuntimeException jika validasi gagal.
 */
function upload_image(?array $file, string $subdir): ?string
{
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null; // tidak ada file baru
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload gagal (kode error: ' . $file['error'] . ').');
    }
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        throw new RuntimeException('Ukuran gambar maksimal 2 MB.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, UPLOAD_ALLOWED, true)) {
        throw new RuntimeException('Format gambar harus JPG, JPEG, PNG, atau WEBP.');
    }

    // Validasi MIME type sungguhan
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mime, $allowedMime, true)) {
        throw new RuntimeException('File yang diupload bukan gambar yang valid.');
    }

    $dir = UPLOAD_PATH . '/' . $subdir;
    if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
        throw new RuntimeException('Gagal membuat direktori upload.');
    }

    $name = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        throw new RuntimeException('Gagal menyimpan file ke server.');
    }
    return $name;
}

function delete_image(?string $filename, string $subdir): void
{
    if ($filename) {
        $path = UPLOAD_PATH . '/' . $subdir . '/' . basename($filename);
        if (is_file($path)) {
            @unlink($path);
        }
    }
}

/** URL gambar dengan placeholder fallback. */
function img_url(?string $filename, string $subdir): string
{
    if ($filename && is_file(UPLOAD_PATH . '/' . $subdir . '/' . basename($filename))) {
        return UPLOAD_URL . '/' . $subdir . '/' . basename($filename);
    }
    return BASE_URL . '/assets/img/placeholder.svg';
}

/* ================= BMI & Estimasi Kalori ================= */

/** BMI = berat (kg) / tinggi (m)^2 — dibulatkan 1 desimal. */
function hitung_bmi($berat, $tinggi): float
{
    $tinggiM = ((float) $tinggi) / 100;
    if ($tinggiM <= 0) {
        return 0.0;
    }
    return round(((float) $berat) / ($tinggiM * $tinggiM), 1);
}

/** Kategori BMI versi WHO untuk Asia Tenggara (Kemenkes RI). */
function kategori_bmi(float $bmi): string
{
    if ($bmi < 18.5) {
        return 'Kurus';
    }
    if ($bmi < 25) {
        return 'Normal';
    }
    if ($bmi < 30) {
        return 'Kelebihan Berat';
    }
    return 'Obesitas';
}

function warna_kategori_bmi(string $kategori): string
{
    return [
        'Kurus'          => 'warning',
        'Normal'         => 'success',
        'Kelebihan Berat' => 'orange',
        'Obesitas'       => 'danger',
    ][$kategori] ?? 'secondary';
}

/**
 * Estimasi kebutuhan kalori harian — rumus Mifflin-St Jeor × faktor aktivitas.
 * $aktivitas: 1.2 ringan, 1.55 sedang, 1.725 aktif.
 */
function estimasi_kalori($berat, $tinggi, $umur, $jenisKelamin, float $aktivitas = 1.55): int
{
    $bmr = (10 * (float) $berat)
        + (6.25 * (float) $tinggi)
        - (5 * (float) $umur);
    $bmr = (strtoupper((string) $jenisKelamin) === 'L') ? $bmr + 5 : $bmr - 161;
    return (int) round($bmr * $aktivitas);
}

function opsi_aktivitas(): array
{
    return [
        1.2   => 'Ringan (jarang olahraga)',
        1.55  => 'Sedang (olahraga 3–5× seminggu)',
        1.725 => 'Aktif (olahraga 6–7× seminggu)',
    ];
}
