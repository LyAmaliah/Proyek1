<?php
/** Model untuk tabel `resep`. */
class Resep extends Model
{
    protected static string $table = 'resep';
    protected static string $primaryKey = 'id_resep';

    /**
     * Pencarian & filter resep (FR-12).
     *
     * @param string|null $status    'dipublikasikan' | 'draft' | null (semua)
     * @param int|null    $maksWaktu batas waktu masak (menit)
     */
    public static function cari(?string $q = null, ?string $status = 'dipublikasikan', ?int $maksWaktu = null): array
    {
        $sql = 'SELECT r.*, ad.nama_lengkap AS penulis
                FROM resep r
                LEFT JOIN users ad ON ad.id_pengguna = r.id_admin';
        $where = [];
        $params = [];

        if ($q !== null && $q !== '') {
            $where[] = '(r.judul_resep LIKE ? OR r.deskripsi LIKE ? OR r.bahan_bahan LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        if ($status !== null && $status !== '') {
            $where[] = 'r.status_publikasi = ?';
            $params[] = $status;
        }
        if ($maksWaktu) {
            $where[] = 'r.waktu_masak <= ?';
            $params[] = $maksWaktu;
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY r.id_resep ASC';

        $st = static::db()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    /** Detail 1 resep. */
    public static function detail(int $id): ?array
    {
        $st = static::db()->prepare('SELECT * FROM resep WHERE id_resep = ? LIMIT 1');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    /** Resep terbaru yang dipublikasikan (beranda). */
    public static function terbaru(int $limit = 3): array
    {
        return static::cari(null, 'dipublikasikan') === []
            ? []
            : array_slice(static::cari(null, 'dipublikasikan'), 0, $limit);
    }
}
