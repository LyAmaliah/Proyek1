<?php
/**
 * Controller cek kandungan gizi & kalori (FR-11).
 * Mendukung pencarian Makanan & Minuman dari FatSecret Platform API
 * dan database kuliner nusantara dengan deteksi kategori otomatis.
 */
class GiziController extends Controller
{
    public function index(): void
    {
        $q        = $this->input('q');
        $kategori = $this->input('kategori');
        $daftar   = [];
        $errorMsg = null;

        if ($q !== '') {
            $cleanQ = strtolower(trim($q));

            // 1. Database Kuliner & Minuman Nusantara Lengkap
            $daftarVariasi = $this->cariKulinerNusantara($cleanQ);
            foreach ($daftarVariasi as $idx => $v) {
                $kat = $v['kategori'] ?? $this->deteksiKategori($v['nama']);
                $daftar[] = [
                    'id_gizi'              => 'kuliner_' . ($idx + 1),
                    'nama_makanan_minuman' => $v['nama'],
                    'kategori'             => $kat,
                    'porsi'                => ($kat === 'Minuman') ? '100 ml' : '100 gram',
                    'kalori'               => (float) $v['kalori'],
                    'karbohidrat'          => (float) $v['karbo'],
                    'protein'              => (float) $v['protein'],
                    'lemak'                => (float) $v['lemak'],
                ];
            }

            // 2. Ambil data dari FatSecret Platform API (Makanan & Minuman)
            if (class_exists('FatSecretService')) {
                $fatsecret = new FatSecretService();
                if ($fatsecret->isConfigured()) {
                    try {
                        $res = $fatsecret->searchFoods($q, 0, 40);
                        $rawFoods = $res['foods'] ?? [];

                        foreach ($rawFoods as $item) {
                            $namaAsli = trim($item['food_name']);

                            // Bersihkan nama dari embel-embel merk/perusahaan di dalam kurung
                            $namaBersih = preg_replace('/\s*\([^)]*\)/', '', $namaAsli);
                            $namaBersih = trim($namaBersih);

                            // Saring produk yang tidak relevan (suplemen gym, bubuk whey, dll)
                            if (preg_match('/(shake|supplement|powder|capsule|stick|bar -)/i', $namaAsli)) {
                                continue;
                            }

                            if (!$this->isRelevan($namaBersih, $cleanQ)) {
                                continue;
                            }

                            // Hindari nama duplikat
                            $sudahAda = false;
                            foreach ($daftar as $d) {
                                if (strcasecmp($d['nama_makanan_minuman'], $namaBersih) === 0) {
                                    $sudahAda = true;
                                    break;
                                }
                            }
                            if ($sudahAda) {
                                continue;
                            }

                            // Deteksi otomatis apakah ini Makanan atau Minuman
                            $kat = $this->deteksiKategori($namaBersih);

                            $daftar[] = [
                                'id_gizi'              => $item['food_id'],
                                'nama_makanan_minuman' => $namaBersih,
                                'kategori'             => $kat,
                                'porsi'                => ($kat === 'Minuman') ? '100 ml' : '100 gram',
                                'kalori'               => (float) $item['kalori'],
                                'karbohidrat'          => (float) $item['karbohidrat'],
                                'protein'              => (float) $item['protein'],
                                'lemak'                => (float) $item['lemak'],
                            ];
                        }
                    } catch (Throwable $e) {
                        $rawError = $e->getMessage();
                        if (preg_match("/Invalid IP address detected:\s*'([^']+)'/i", $rawError, $m)) {
                            $detectedIp = $m[1];
                            $errorMsg = "Koneksi FatSecret: IP ($detectedIp) belum terdaftar di IP Allowlist FatSecret.";
                        }
                    }
                }
            }

            // 3. Fallback database lokal MySQL jika masih kosong
            if (empty($daftar) && class_exists('Gizi')) {
                try {
                    $local = Gizi::cari($q ?: null, $kategori ?: null);
                    if (!empty($local)) {
                        $daftar = $local;
                        $errorMsg = null;
                    }
                } catch (Throwable $dbEx) {}
            }
        }

        // Filter kategori jika pengguna memilih kategori tertentu di dropdown (Makanan / Minuman)
        if (!empty($kategori) && !empty($daftar)) {
            $daftar = array_values(array_filter($daftar, function ($item) use ($kategori) {
                return strcasecmp($item['kategori'], $kategori) === 0;
            }));
        }

        $daftarKategori = [];
        if (class_exists('Gizi')) {
            try {
                $daftarKategori = Gizi::daftarKategori();
            } catch (Throwable $e) {}
        }
        if (empty($daftarKategori)) {
            $daftarKategori = ['Makanan', 'Minuman'];
        }

        $this->view('gizi/index', [
            'pageTitle'     => 'Cek Gizi & Kalori',
            'activeNav'     => 'gizi',
            'daftar'        => $daftar,
            'kategori'      => $daftarKategori,
            'q'             => $q,
            'kategoriAktif' => $kategori,
            'errorMsg'      => $errorMsg,
        ]);
    }

    /**
     * Otomatis mendeteksi apakah suatu menu adalah 'Minuman' atau 'Makanan'
     */
    private function deteksiKategori(string $nama): string
    {
        $kataMinuman = [
            'jus', 'juice', 'kopi', 'coffee', 'teh', 'tea', 'susu', 'milk', 'es ', 'ice ',
            'air', 'water', 'smoothie', 'smoothies', 'latte', 'cappuccino', 'espresso',
            'americano', 'wedang', 'jamu', 'cendol', 'dawet', 'kelapa', 'sirup', 'syrup',
            'cokelat panas', 'bajigur', 'bandrek', 'sekoteng', 'boba', 'mocktail', 'soda'
        ];

        $namaLower = strtolower($nama);
        foreach ($kataMinuman as $km) {
            if (str_contains($namaLower, $km)) {
                return 'Minuman';
            }
        }

        return 'Makanan';
    }

    private function isRelevan(string $nama, string $cleanQ): bool
    {
        $namaLower = strtolower($nama);
        $words = preg_split('/\s+/', $cleanQ);
        foreach ($words as $w) {
            if (strlen($w) >= 3 && !str_contains($namaLower, $w)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Database Kuliner & Minuman Nusantara
     */
    private function cariKulinerNusantara(string $query): array
    {
        $database = [
            // ================= MINUMAN SEHAT & POPULER =================
            ['nama' => 'Jus Alpukat Murni (tanpa Gula & Susu Kental Manis)', 'kategori' => 'Minuman', 'kalori' => 85.0, 'karbo' => 4.5, 'protein' => 1.2, 'lemak' => 7.5],
            ['nama' => 'Jus Alpukat Madu', 'kategori' => 'Minuman', 'kalori' => 115.0, 'karbo' => 13.0, 'protein' => 1.2, 'lemak' => 6.5],
            ['nama' => 'Jus Jeruk Peras Segar (Murni)', 'kategori' => 'Minuman', 'kalori' => 45.0, 'karbo' => 10.4, 'protein' => 0.7, 'lemak' => 0.2],
            ['nama' => 'Jus Mangga Manis Alami', 'kategori' => 'Minuman', 'kalori' => 60.0, 'karbo' => 15.0, 'protein' => 0.8, 'lemak' => 0.4],
            ['nama' => 'Jus Apel Segar', 'kategori' => 'Minuman', 'kalori' => 46.0, 'karbo' => 11.5, 'protein' => 0.3, 'lemak' => 0.1],
            ['nama' => 'Jus Semangka Dingin', 'kategori' => 'Minuman', 'kalori' => 30.0, 'karbo' => 7.5, 'protein' => 0.6, 'lemak' => 0.2],
            ['nama' => 'Air Kelapa Muda Murni', 'kategori' => 'Minuman', 'kalori' => 19.0, 'karbo' => 3.7, 'protein' => 0.7, 'lemak' => 0.2],
            ['nama' => 'Es Kelapa Muda Gula Merah', 'kategori' => 'Minuman', 'kalori' => 65.0, 'karbo' => 15.0, 'protein' => 0.8, 'lemak' => 0.4],
            ['nama' => 'Kopi Hitam / Americano (tanpa Gula)', 'kategori' => 'Minuman', 'kalori' => 2.0, 'karbo' => 0.2, 'protein' => 0.3, 'lemak' => 0.0],
            ['nama' => 'Es Kopi Susu Gula Aren', 'kategori' => 'Minuman', 'kalori' => 95.0, 'karbo' => 14.5, 'protein' => 2.2, 'lemak' => 3.5],
            ['nama' => 'Teh Hijau / Green Tea Tawar', 'kategori' => 'Minuman', 'kalori' => 1.0, 'karbo' => 0.0, 'protein' => 0.2, 'lemak' => 0.0],
            ['nama' => 'Es Teh Manis', 'kategori' => 'Minuman', 'kalori' => 45.0, 'karbo' => 11.2, 'protein' => 0.0, 'lemak' => 0.0],
            ['nama' => 'Susu Kedelai Tawar Segar', 'kategori' => 'Minuman', 'kalori' => 54.0, 'karbo' => 2.9, 'protein' => 3.3, 'lemak' => 2.0],
            ['nama' => 'Susu Sapi Segar (Full Cream)', 'kategori' => 'Minuman', 'kalori' => 61.0, 'karbo' => 4.8, 'protein' => 3.2, 'lemak' => 3.3],
            ['nama' => 'Wedang Jahe Hangat (Gula Merah)', 'kategori' => 'Minuman', 'kalori' => 48.0, 'karbo' => 12.0, 'protein' => 0.2, 'lemak' => 0.1],
            ['nama' => 'Jamu Kunyit Asam Segar', 'kategori' => 'Minuman', 'kalori' => 42.0, 'karbo' => 10.0, 'protein' => 0.4, 'lemak' => 0.1],

            // ================= MAKANAN NUSANTARA =================
            ['nama' => 'Bubur Ayam Tradisional (Cakwe, Tongcai, Kuah Kuning)', 'kategori' => 'Makanan', 'kalori' => 165.0, 'karbo' => 24.5, 'protein' => 7.2, 'lemak' => 4.2],
            ['nama' => 'Bubur Ayam Komplit (Ayam Suwir, Cakwe, Telur Rebus)', 'kategori' => 'Makanan', 'kalori' => 180.0, 'karbo' => 24.0, 'protein' => 9.5, 'lemak' => 5.5],
            ['nama' => 'Cah Kangkung Bawang Putih', 'kategori' => 'Makanan', 'kalori' => 42.0, 'karbo' => 3.0, 'protein' => 2.5, 'lemak' => 2.2],
            ['nama' => 'Cah Kangkung Terasi / Belacan', 'kategori' => 'Makanan', 'kalori' => 52.0, 'karbo' => 3.5, 'protein' => 3.2, 'lemak' => 2.8],
            ['nama' => 'Nasi Goreng Komplit (Ayam, Telur, Sosis, Kol, Bakso)', 'kategori' => 'Makanan', 'kalori' => 188.0, 'karbo' => 22.0, 'protein' => 8.5, 'lemak' => 6.8],
            ['nama' => 'Nasi Goreng Biasa / Standar', 'kategori' => 'Makanan', 'kalori' => 165.0, 'karbo' => 21.0, 'protein' => 5.5, 'lemak' => 6.0],
            ['nama' => 'Capcay Kuah Komplit', 'kategori' => 'Makanan', 'kalori' => 65.0, 'karbo' => 6.5, 'protein' => 4.8, 'lemak' => 2.4],
            ['nama' => 'Soto Ayam Lamongan (dengan Koya)', 'kategori' => 'Makanan', 'kalori' => 75.0, 'karbo' => 4.5, 'protein' => 7.2, 'lemak' => 3.2],
            ['nama' => 'Ayam Bakar Madu / Manis', 'kategori' => 'Makanan', 'kalori' => 195.0, 'karbo' => 5.0, 'protein' => 24.0, 'lemak' => 8.5],
            ['nama' => 'Gado-Gado Siram Bumbu Kacang', 'kategori' => 'Makanan', 'kalori' => 135.0, 'karbo' => 14.0, 'protein' => 5.5, 'lemak' => 6.8],
        ];

        $hasil = [];
        $words = preg_split('/\s+/', $query);

        foreach ($database as $item) {
            $namaLower = strtolower($item['nama']);
            $cocokSemua = true;
            foreach ($words as $w) {
                if (strlen($w) >= 3 && !str_contains($namaLower, $w)) {
                    $cocokSemua = false;
                    break;
                }
            }
            if ($cocokSemua) {
                $hasil[] = $item;
            }
        }

        return $hasil;
    }
}