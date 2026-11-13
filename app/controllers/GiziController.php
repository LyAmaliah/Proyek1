<?php
/**
 * Controller cek kandungan gizi & kalori (FR-11).
 * Mengambil data dari FatSecret Platform API (Generic) dan variasi kuliner,
 * lalu mengirimkannya ke tampilan terbaru gizi/index.php.
 */
class GiziController extends Controller
{
    public function index(): void
    {
        $q        = $this->input('q');
        $kategori = $this->input('kategori');
        $daftar   = [];

        if ($q !== '') {
            $cleanQ = strtolower(trim($q));

            // 1. Variasi jenis hidangan nusantara (nilai standar per 100 gram)
            $daftarVariasi = $this->getVariasiKuliner($cleanQ);
            foreach ($daftarVariasi as $idx => $v) {
                $daftar[] = [
                    'id_gizi'              => 'var_' . ($idx + 1),
                    'nama_makanan_minuman' => $v['nama'],
                    'kategori'             => 'Makanan',
                    'porsi'                => '100 gram',
                    'kalori'               => (float) $v['kalori'],
                    'karbohidrat'          => (float) $v['karbo'],
                    'protein'              => (float) $v['protein'],
                    'lemak'                => (float) $v['lemak'],
                ];
            }

            // 2. Ambil data dari FatSecret Platform API (Generic only, tanpa brand/penjual)
            if (class_exists('FatSecretService')) {
                $fatsecret = new FatSecretService();
                if ($fatsecret->isConfigured()) {
                    try {
                        $res = $fatsecret->searchFoods($q, 0, 30);
                        $rawFoods = $res['foods'] ?? [];

                        foreach ($rawFoods as $item) {
                            // Abaikan produk brand atau produk toko/kemasan tertentu
                            if (($item['food_type'] ?? '') === 'Brand' || !empty($item['brand_name'])) {
                                continue;
                            }

                            $nama = trim($item['food_name']);

                            // Hindari duplikasi jika sudah ada di variasi
                            $sudahAda = false;
                            foreach ($daftar as $d) {
                                if (strcasecmp($d['nama_makanan_minuman'], $nama) === 0) {
                                    $sudahAda = true;
                                    break;
                                }
                            }
                            if ($sudahAda) {
                                continue;
                            }

                            // Pastikan nama makanan relevan
                            if (!$this->isRelevan($nama, $cleanQ)) {
                                continue;
                            }

                            $daftar[] = [
                                'id_gizi'              => $item['food_id'],
                                'nama_makanan_minuman' => $nama,
                                'kategori'             => 'Makanan',
                                'porsi'                => '100 gram',
                                'kalori'               => (float) $item['kalori'],
                                'karbohidrat'          => (float) $item['karbohidrat'],
                                'protein'              => (float) $item['protein'],
                                'lemak'                => (float) $item['lemak'],
                            ];
                        }
                    } catch (Throwable $e) {
                        // Jika koneksi FatSecret bermasalah, lanjutkan ke database lokal
                    }
                }
            }

            // 3. Fallback jika masih kosong, ambil dari database lokal
            if (empty($daftar) && class_exists('Gizi')) {
                try {
                    $local = Gizi::cari($q ?: null, $kategori ?: null);
                    if (!empty($local)) {
                        $daftar = $local;
                    }
                } catch (Throwable $dbEx) {
                    // DB offline
                }
            }
        }

        // Ambil daftar kategori dari database lokal untuk dropdown pencarian
        $daftarKategori = [];
        if (class_exists('Gizi')) {
            try {
                $daftarKategori = Gizi::daftarKategori();
            } catch (Throwable $e) {}
        }

        $this->view('gizi/index', [
            'pageTitle'     => 'Cek Gizi & Kalori',
            'activeNav'     => 'gizi',
            'daftar'        => $daftar,
            'kategori'      => $daftarKategori,
            'q'             => $q,
            'kategoriAktif' => $kategori,
        ]);
    }

    private function isRelevan(string $nama, string $cleanQ): bool
    {
        $namaLower = strtolower($nama);
        $words = preg_split('/\s+/', $cleanQ);
        foreach ($words as $w) {
            if (strlen($w) >= 3 && str_contains($namaLower, $w)) {
                return true;
            }
        }
        return false;
    }

    private function getVariasiKuliner(string $query): array
    {
        $kamus = [
            'nasi goreng' => [
                ['nama' => 'Nasi Goreng Komplit (Topping Sosis, Ayam, Kol, Telur, Bakso)', 'kalori' => 188.0, 'karbo' => 22.0, 'protein' => 8.5, 'lemak' => 6.8],
                ['nama' => 'Nasi Goreng Biasa / Standar', 'kalori' => 165.0, 'karbo' => 21.0, 'protein' => 5.5, 'lemak' => 6.0],
                ['nama' => 'Nasi Goreng Ayam Suwir', 'kalori' => 175.0, 'karbo' => 20.5, 'protein' => 8.0, 'lemak' => 6.2],
                ['nama' => 'Nasi Goreng Telur (Ceplok / Dadar Iris)', 'kalori' => 170.0, 'karbo' => 21.0, 'protein' => 6.8, 'lemak' => 6.4],
                ['nama' => 'Nasi Goreng Otokowok / Mawut (Campur Mie & Kol)', 'kalori' => 174.0, 'karbo' => 24.0, 'protein' => 6.5, 'lemak' => 5.8],
                ['nama' => 'Nasi Goreng Seafood (Udang, Cumi)', 'kalori' => 158.0, 'karbo' => 20.0, 'protein' => 8.2, 'lemak' => 4.8],
                ['nama' => 'Nasi Goreng Kampung (Bumbu Bawang & Teri)', 'kalori' => 168.0, 'karbo' => 22.0, 'protein' => 7.2, 'lemak' => 5.5],
                ['nama' => 'Nasi Goreng Jawa (Bumbu Manis & Sayur Kol)', 'kalori' => 172.0, 'karbo' => 23.0, 'protein' => 6.8, 'lemak' => 5.6],
                ['nama' => 'Nasi Goreng Sosis & Bakso', 'kalori' => 180.0, 'karbo' => 21.5, 'protein' => 7.8, 'lemak' => 6.9],
                ['nama' => 'Nasi Goreng Kambing', 'kalori' => 210.0, 'karbo' => 20.0, 'protein' => 9.5, 'lemak' => 9.8],
            ],
            'mie goreng' => [
                ['nama' => 'Mie Goreng Komplit (Ayam, Sosis, Bakso, Sawi)', 'kalori' => 175.0, 'karbo' => 22.0, 'protein' => 7.5, 'lemak' => 6.2],
                ['nama' => 'Mie Goreng Jawa Tradisional', 'kalori' => 165.0, 'karbo' => 23.5, 'protein' => 5.8, 'lemak' => 5.5],
                ['nama' => 'Mie Goreng Seafood', 'kalori' => 155.0, 'karbo' => 21.0, 'protein' => 8.0, 'lemak' => 4.5],
                ['nama' => 'Bihun Goreng Sayur & Telur', 'kalori' => 145.0, 'karbo' => 23.0, 'protein' => 4.5, 'lemak' => 3.8],
                ['nama' => 'Kwetiau Goreng Sapi / Ayam', 'kalori' => 160.0, 'karbo' => 21.0, 'protein' => 7.0, 'lemak' => 5.2],
            ],
            'soto' => [
                ['nama' => 'Soto Ayam Lamongan (dengan Koya)', 'kalori' => 75.0, 'karbo' => 4.5, 'protein' => 7.2, 'lemak' => 3.2],
                ['nama' => 'Soto Ayam Bening', 'kalori' => 55.0, 'karbo' => 3.0, 'protein' => 6.5, 'lemak' => 1.8],
                ['nama' => 'Soto Betawi (Kuah Santan & Susu)', 'kalori' => 135.0, 'karbo' => 4.2, 'protein' => 8.5, 'lemak' => 9.5],
                ['nama' => 'Soto Daging Sapi Bening', 'kalori' => 85.0, 'karbo' => 3.2, 'protein' => 9.0, 'lemak' => 4.0],
                ['nama' => 'Soto Mie Bogor (Daging & Risol)', 'kalori' => 110.0, 'karbo' => 12.0, 'protein' => 6.0, 'lemak' => 4.2],
                ['nama' => 'Soto Kudus', 'kalori' => 70.0, 'karbo' => 3.8, 'protein' => 7.0, 'lemak' => 2.8],
            ],
            'sate' => [
                ['nama' => 'Sate Ayam Madura (Bumbu Kacang)', 'kalori' => 185.0, 'karbo' => 7.5, 'protein' => 18.0, 'lemak' => 9.5],
                ['nama' => 'Sate Kambing (Bumbu Kecap & Bawang)', 'kalori' => 215.0, 'karbo' => 4.0, 'protein' => 22.0, 'lemak' => 12.5],
                ['nama' => 'Sate Padang (Kuah Kuning Rempah)', 'kalori' => 175.0, 'karbo' => 6.0, 'protein' => 16.5, 'lemak' => 9.0],
                ['nama' => 'Sate Taichan (Bakar Polos Sambal Jeruk)', 'kalori' => 135.0, 'karbo' => 1.0, 'protein' => 22.0, 'lemak' => 4.5],
            ],
            'ayam' => [
                ['nama' => 'Ayam Bakar Madu / Manis', 'kalori' => 195.0, 'karbo' => 5.0, 'protein' => 24.0, 'lemak' => 8.5],
                ['nama' => 'Ayam Goreng Lengkuas / Kremes', 'kalori' => 245.0, 'karbo' => 4.0, 'protein' => 23.0, 'lemak' => 15.0],
                ['nama' => 'Ayam Geprek Sambal Bawang (tanpa Nasi)', 'kalori' => 260.0, 'karbo' => 11.0, 'protein' => 21.0, 'lemak' => 14.5],
                ['nama' => 'Ayam Pop Khas Minang', 'kalori' => 165.0, 'karbo' => 0.5, 'protein' => 25.0, 'lemak' => 6.8],
                ['nama' => 'Ayam Ungkep Rebus / Kukus', 'kalori' => 150.0, 'karbo' => 0.0, 'protein' => 28.0, 'lemak' => 4.0],
            ],
            'bakso' => [
                ['nama' => 'Bakso Sapi Kuah Bening', 'kalori' => 85.0, 'karbo' => 5.0, 'protein' => 8.0, 'lemak' => 3.5],
                ['nama' => 'Bakso Urat Kuah', 'kalori' => 110.0, 'karbo' => 4.5, 'protein' => 9.5, 'lemak' => 5.8],
                ['nama' => 'Bakso Malang Komplit (Pangsit, Tahu, Siomay)', 'kalori' => 125.0, 'karbo' => 12.0, 'protein' => 7.5, 'lemak' => 5.0],
            ],
        ];

        foreach ($kamus as $key => $items) {
            if (str_contains($query, $key)) {
                return $items;
            }
        }

        return [];
    }
}