<?php
/** Model untuk tabel `kategori_artikel`. */
class KategoriArtikel extends Model
{
    protected static string $table = 'kategori_artikel';
    protected static string $primaryKey = 'id_kategori_artikel';

    public static function semua(): array
    {
        return static::all('id_kategori_artikel ASC');
    }

    /** Nama kategori berdasarkan ID (dengan fallback). */
    public static function nama($id): string
    {
        $row = static::find($id);
        return $row ? $row['nama_kategori'] : 'Tanpa Kategori';
    }
}
