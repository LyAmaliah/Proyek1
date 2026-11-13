<?php
/** Model untuk tabel `artikel_kesehatan`. */
class Artikel extends Model
{
    protected static string $table = 'artikel_kesehatan';
    protected static string $primaryKey = 'id_artikel';

    private const BASE_SELECT = '
        SELECT a.*, k.nama_kategori, ad.nama_lengkap AS penulis
        FROM artikel_kesehatan a
        LEFT JOIN kategori_artikel k ON k.id_kategori_artikel = a.id_kategori_artikel
        LEFT JOIN users ad ON ad.id_pengguna = a.id_admin';

    /** Daftar artikel + join kategori & admin, dengan filter opsional. */
    public static function cari(?string $q = null, ?int $idKategori = null): array
    {
        $sql = self::BASE_SELECT;
        $where = [];
        $params = [];

        if ($q !== null && $q !== '') {
            $where[] = '(a.judul_artikel LIKE ? OR a.isi_artikel LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        if ($idKategori) {
            $where[] = 'a.id_kategori_artikel = ?';
            $params[] = $idKategori;
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY a.tanggal_terbit DESC, a.id_artikel DESC';

        $st = static::db()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    /** Detail 1 artikel. */
    public static function detail(int $id): ?array
    {
        $st = static::db()->prepare(self::BASE_SELECT . ' WHERE a.id_artikel = ? LIMIT 1');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    /** Artikel terbaru (untuk beranda). */
    public static function terbaru(int $limit = 3): array
    {
        $st = static::db()->prepare(
            self::BASE_SELECT . ' ORDER BY a.tanggal_terbit DESC, a.id_artikel DESC LIMIT ' . (int) $limit
        );
        $st->execute();
        return $st->fetchAll();
    }

    /** Artikel terkait (satu kategori), kecuali artikel itu sendiri. */
    public static function terkait(int $idArtikel, $idKategori, int $limit = 3): array
    {
        $st = static::db()->prepare(
            self::BASE_SELECT
            . ' WHERE a.id_artikel != ? AND a.id_kategori_artikel = ?'
            . ' ORDER BY a.tanggal_terbit DESC LIMIT ' . (int) $limit
        );
        $st->execute([$idArtikel, $idKategori]);
        return $st->fetchAll();
    }

    /** Rekap jumlah artikel per kategori (grafik dashboard admin). */
    public static function perKategori(): array
    {
        $st = static::db()->query(
            'SELECT k.nama_kategori, COUNT(a.id_artikel) AS jumlah
             FROM kategori_artikel k
             LEFT JOIN artikel_kesehatan a ON a.id_kategori_artikel = k.id_kategori_artikel
             GROUP BY k.id_kategori_artikel, k.nama_kategori
             ORDER BY jumlah DESC'
        );
        return $st->fetchAll();
    }
}
