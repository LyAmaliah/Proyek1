<?php
/**
 * Model untuk tabel `tips_sehat` (31 Tips Kesehatan Harian).
 */
class Tips extends Model
{
    protected static string $table = 'tips_sehat';
    protected static string $primaryKey = 'id_tips';

    /**
     * Ambil tips sehat untuk hari ini berdasarkan tanggal berjalan (1 - 31).
     *
     * @return array{id_tips: int, hari_ke: int, judul: string, isi_tips: string, kategori: string}
     */
    public static function hariIni(): array
    {
        $hari = (int) date('j'); // Tanggal 1 sampai 31
        $rows = static::where('hari_ke', $hari);
        if (!empty($rows)) {
            return $rows[0];
        }

        // Fallback jika belum ada data
        return [
            'id_tips'  => 0,
            'hari_ke'  => $hari,
            'judul'    => 'Tips Sehat Hari Ini',
            'isi_tips' => 'Minum minimal 8 gelas air putih setiap hari dan sisipkan 30 menit aktivitas fisik untuk menjaga metabolisme tubuh tetap sehat.',
            'kategori' => 'Umum',
        ];
    }

    /**
     * Ambil seluruh 31 data tips harian (urut hari_ke 1 - 31).
     */
    public static function semua(): array
    {
        return static::all('hari_ke ASC');
    }
}