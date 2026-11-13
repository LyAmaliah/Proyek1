<?php
/** Model untuk tabel `ulasan_rating`. */
class Ulasan extends Model
{
    protected static string $table = 'ulasan_rating';
    protected static string $primaryKey = 'id_usulan_rating';

    /** Ulasan 1 tempat + nama pengguna. */
    public static function byTempat(int $idTempat): array
    {
        $st = static::db()->prepare(
            'SELECT u.*, p.nama_lengkap, p.username
             FROM ulasan_rating u
             JOIN users p ON p.id_pengguna = u.id_pengguna
             WHERE u.id_tempat = ?
             ORDER BY u.tanggal_ulasan DESC, u.id_usulan_rating DESC'
        );
        $st->execute([$idTempat]);
        return $st->fetchAll();
    }

    /** Rata-rata rating 1 tempat (null jika belum ada ulasan). */
    public static function rataRata(int $idTempat): ?float
    {
        $st = static::db()->prepare(
            'SELECT ROUND(AVG(rating), 1) FROM ulasan_rating WHERE id_tempat = ?'
        );
        $st->execute([$idTempat]);
        $value = $st->fetchColumn();
        return $value === null || $value === false ? null : (float) $value;
    }

    /** Sebaran rating 1..5 untuk menampilkan histogram. */
    public static function sebaran(int $idTempat): array
    {
        $st = static::db()->prepare(
            'SELECT rating, COUNT(*) AS jumlah FROM ulasan_rating
             WHERE id_tempat = ? GROUP BY rating'
        );
        $st->execute([$idTempat]);
        $rows = $st->fetchAll();
        $hasil = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach ($rows as $row) {
            $hasil[(int) $row['rating']] = (int) $row['jumlah'];
        }
        return $hasil;
    }

    /** Apakah pengguna ini sudah pernah mengulas tempat tersebut? */
    public static function sudahMengulas(int $idPengguna, int $idTempat): bool
    {
        $st = static::db()->prepare(
            'SELECT COUNT(*) FROM ulasan_rating WHERE id_pengguna = ? AND id_tempat = ?'
        );
        $st->execute([$idPengguna, $idTempat]);
        return (int) $st->fetchColumn() > 0;
    }
}
