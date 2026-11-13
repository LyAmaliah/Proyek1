<?php
/** Model untuk tabel `kandungan_gizi` ( nilai per 100 gram ). */
class Gizi extends Model
{
    protected static string $table = 'kandungan_gizi';
    protected static string $primaryKey = 'id_gizi';

    /** Pencarian makanan/minuman untuk fitur cek gizi (FR-11). */
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

    /** Daftar kategori unik (Makanan / Minuman / dll.). */
    public static function daftarKategori(): array
    {
        $rows = static::db()->query(
            'SELECT DISTINCT kategori FROM kandungan_gizi ORDER BY kategori'
        )->fetchAll();
        return array_column($rows, 'kategori');
    }
}
