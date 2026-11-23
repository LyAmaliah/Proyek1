<?php
/**
 * Model untuk tabel `kandungan_gizi` (nilai per 100 gram / 100 ml).
 * Menghubungkan database lokal MySQL dengan FatSecret Platform API (Auto-Cache ke Backend).
 */
class Gizi extends Model
{
    protected static string $table = 'kandungan_gizi';
    protected static string $primaryKey = 'id_gizi';

    /**
     * Pencarian standar di database lokal MySQL.
     */
    public static function cari(?string $q = null, ?string $kategori = null): array
    {
        $sql = 'SELECT * FROM kandungan_gizi';
        $where = [];
        $params = [];

        if ($q !== null && $q !== '') {
            $where[] = 'nama_makanan_minuman LIKE ?';
            $params[] = "%$q%";
        }
        if ($kategori !== null && $kategori !== '') {
            $where[] = 'kategori = ?';
            $params[] = $kategori;
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY id_gizi ASC';

        $st = static::db()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    /**
     * Pencarian Cerdas: Cek database MySQL + FatSecret API.
     * Makanan baru dari FatSecret otomatis di-INSERT ke tabel database backend!
     */
    public static function cariDenganApi(string $q, ?string $kategori = null): array
    {
        $cleanQ = strtolower(trim($q));
        if ($cleanQ === '') {
            return [];
        }

        // 1. Ambil data yang sudah ada di database lokal
        $hasilLokal = static::cari($q, $kategori);

        // 2. Jika ada FatSecret API, tarik data dan simpan ke database MySQL
        if (class_exists('FatSecretService')) {
            $fatsecret = new FatSecretService();
            if ($fatsecret->isConfigured()) {
                try {
                    $res = $fatsecret->searchFoods($q, 0, 15);
                    $rawFoods = $res['foods'] ?? [];

                    foreach ($rawFoods as $item) {
                        $namaAsli = trim($item['food_name']);

                        // Bersihkan nama dari embel-embel merk di dalam kurung
                        $namaBersih = trim(preg_replace('/\s*\([^)]*\)/', '', $namaAsli));

                        // Saring suplemen gym yang tidak relevan
                        if (preg_match('/(shake|supplement|powder|capsule|stick|bar -)/i', $namaAsli)) {
                            continue;
                        }

                        // Saring relevansi kata kunci
                        $namaLower = strtolower($namaBersih);
                        $words = preg_split('/\s+/', $cleanQ);
                        $relevan = true;
                        foreach ($words as $w) {
                            if (strlen($w) >= 3 && !str_contains($namaLower, $w)) {
                                $relevan = false;
                                break;
                            }
                        }
                        if (!$relevan) {
                            continue;
                        }

                        $kat = static::deteksiKategori($namaBersih);

                        // Cek apakah makanan sudah ada di database MySQL agar tidak duplikat
                        $cek = static::where('nama_makanan_minuman', $namaBersih);
                        if (empty($cek)) {
                            // SIMPAN OTOMATIS KE TABEL DATABASE BACKEND!
                            static::insert([
                                'id_admin'             => 4, // ID admin
                                'nama_makanan_minuman' => $namaBersih,
                                'kategori'             => $kat,
                                'porsi'                => ($kat === 'Minuman') ? '100 ml' : '100 gram',
                                'kalori'               => (float) $item['kalori'],
                                'karbohidrat'          => (float) $item['karbohidrat'],
                                'protein'              => (float) $item['protein'],
                                'lemak'                => (float) $item['lemak'],
                            ]);
                        }
                    }

                    // Ambil ulang data dari database lokal (sudah termasuk data baru yang baru disimpan)
                    $hasilLokal = static::cari($q, $kategori);
                } catch (Throwable $e) {
                    // Jika API ada kendala IP/kuota, tetap kembalikan data dari database lokal
                }
            }
        }

        return $hasilLokal;
    }

    /**
     * Otomatis deteksi kategori Makanan atau Minuman.
     */
    public static function deteksiKategori(string $nama): string
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

    /**
     * Daftar kategori unik (Makanan / Minuman).
     */
    public static function daftarKategori(): array
    {
        $rows = static::db()->query(
            'SELECT DISTINCT kategori FROM kandungan_gizi ORDER BY kategori'
        )->fetchAll();
        return array_column($rows, 'kategori') ?: ['Makanan', 'Minuman'];
    }
}