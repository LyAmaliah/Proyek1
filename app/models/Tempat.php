<?php
/** Model untuk tabel `tempat_sehat`. */
class Tempat extends Model
{
    protected static string $table = 'tempat_sehat';
    protected static string $primaryKey = 'id_tempat';

    private const BASE_SELECT = '
        SELECT t.*,
               ROUND(AVG(u.rating), 1)   AS rating_rata,
               COUNT(u.id_usulan_rating) AS jumlah_ulasan
        FROM tempat_sehat t
        LEFT JOIN ulasan_rating u ON u.id_tempat = t.id_tempat';

    private const GROUP_BY = '
        GROUP BY t.id_tempat, t.nama_tempat, t.alamat, t.deskripsi,
                 t.kontak, t.foto_tempat, t.foto_menu, t.link_maps,
                 t.link_instagram, t.link_grabfood, t.link_shopeefood, t.id_admin';

    /** Daftar tempat beserta rata-rata rating & jumlah ulasan. */
    public static function denganRating(?string $q = null): array
    {
        $sql = self::BASE_SELECT;
        $params = [];
        if ($q !== null && $q !== '') {
            $sql .= ' WHERE t.nama_tempat LIKE ? OR t.alamat LIKE ? OR t.deskripsi LIKE ?';
            $params = ["%$q%", "%$q%", "%$q%"];
        }
        $sql .= self::GROUP_BY . ' ORDER BY t.nama_tempat ASC';

        $st = static::db()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    /** Detail 1 tempat beserta rating. */
    public static function detail(int $id): ?array
    {
        $st = static::db()->prepare(
            self::BASE_SELECT . ' WHERE t.id_tempat = ? ' . self::GROUP_BY . ' LIMIT 1'
        );
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    /** Untuk dropdown admin saat memilih tempat pada form menu. */
    public static function daftar(): array
    {
        return static::all('nama_tempat ASC');
    }
}
