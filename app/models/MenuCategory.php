<?php
/** Model untuk tabel `menu_categories`. */
class MenuCategory extends Model
{
    protected static string $table = 'menu_categories';
    protected static string $primaryKey = 'id_kategori_menu';

    public static function semua(): array
    {
        return static::all('id_kategori_menu ASC');
    }

    /** Nama kategori berdasarkan ID (dengan fallback). */
    public static function nama($id): string
    {
        $row = static::find($id);
        return $row ? $row['nama_kategori'] : 'Tanpa Kategori';
    }
}
