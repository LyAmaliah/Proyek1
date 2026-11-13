<?php
/** Model untuk tabel `kalkulator_bmi`. */
class KalkulatorBmi extends Model
{
    protected static string $table = 'kalkulator_bmi';
    protected static string $primaryKey = 'id_bmi';

    /** Hitungan BMI terakhir milik 1 pengguna. */
    public static function terakhir(int $idPengguna): ?array
    {
        $st = static::db()->prepare(
            'SELECT * FROM kalkulator_bmi WHERE id_pengguna = ?
             ORDER BY id_bmi DESC LIMIT 1'
        );
        $st->execute([$idPengguna]);
        $row = $st->fetch();
        return $row ?: null;
    }

    /** Riwayat perhitungan BMI (untuk grafik/tabel riwayat). */
    public static function riwayat(int $idPengguna, int $limit = 10): array
    {
        $st = static::db()->prepare(
            'SELECT * FROM kalkulator_bmi WHERE id_pengguna = ?
             ORDER BY id_bmi DESC LIMIT ' . (int) $limit
        );
        $st->execute([$idPengguna]);
        return $st->fetchAll();
    }

    /**
     * Simpan hasil perhitungan BMI.
     * Nilai dihitung lengkap di sini agar konsisten (skor, kategori, estimasi kalori).
     *
     * @param array $data {umur, jenis_kelamin, berat_badan, tinggi_badan}
     * @return array Hasil hitung: [nilai_bmi, kategori_bmi, estimasi_kalori]
     */
    public static function hitungDanSimpan(int $idPengguna, array $data): array
    {
        $berat = (float) $data['berat_badan'];
        $tinggi = (float) $data['tinggi_badan'];
        $umur = (int) $data['umur'];
        $jk = strtoupper($data['jenis_kelamin']);

        $nilai = hitung_bmi($berat, $tinggi);
        $kategori = kategori_bmi($nilai);
        $kalori = estimasi_kalori($berat, $tinggi, $umur, $jk);

        static::insert([
            'id_pengguna'    => $idPengguna,
            'umur'           => $umur,
            'jenis_kelamin'  => $jk,
            'berat_badan'    => $berat,
            'tinggi_badan'   => $tinggi,
            'nilai_bmi'      => $nilai,
            'kategori_bmi'   => $kategori,
            'estimasi_kalori' => $kalori,
        ]);

        return [
            'nilai_bmi'      => $nilai,
            'kategori_bmi'   => $kategori,
            'estimasi_kalori' => $kalori,
        ];
    }
}
