<?php
/** Model untuk tabel `menu`. */
class Menu extends Model
{
    protected static string $table = 'menu';
    protected static string $primaryKey = 'id_menu';

    /** Semua menu milik 1 tempat, beserta nama kategorinya. */
    public static function byTempat(int $idTempat): array
    {
        $st = static::db()->prepare(
            'SELECT m.*, k.nama_kategori
             FROM menu m
             LEFT JOIN menu_categories k ON k.id_kategori_menu = m.id_kategori_menu
             WHERE m.id_tempat = ?
             ORDER BY m.nama_menu ASC'
        );
        $st->execute([$idTempat]);
        return $st->fetchAll();
    }

    /** Pencarian menu di seluruh tempat (opsional). */
    public static function cari(?string $q = null): array
    {
        $sql = 'SELECT m.*, t.nama_tempat, k.nama_kategori
                FROM menu m
                JOIN tempat_sehat t ON t.id_tempat = m.id_tempat
                LEFT JOIN menu_categories k ON k.id_kategori_menu = m.id_kategori_menu';
        $params = [];
        if ($q !== null && $q !== '') {
            $sql .= ' WHERE m.nama_menu LIKE ? OR t.nama_tempat LIKE ? OR k.nama_kategori LIKE ?';
            $params = ["%$q%", "%$q%", "%$q%"];
        }
        $sql .= ' ORDER BY m.nama_menu ASC';
        $st = static::db()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }
}
