<?php
/** Model untuk tabel `laporan_statistik`. */
class LaporanStatistik extends Model
{
    protected static string $table = 'laporan_statistik';
    protected static string $primaryKey = 'id_laporan';

    /**
     * Rekapitulasi seluruh data sistem (FR-07).
     *
     * @return array{total_pengguna:int, total_artikel:int, total_kalori:int,
     *               total_resep:int, total_tempat:int, total_menu:int}
     */
    public static function rekap(): array
    {
        $db = static::db();
        $totalKalori = (int) $db->query(
            'SELECT COALESCE(SUM(total_kalori), 0) FROM resep WHERE status_publikasi = "dipublikasikan"'
        )->fetchColumn();

        return [
            'total_pengguna' => User::count(),
            'total_artikel'  => Artikel::count(),
            'total_kalori'   => $totalKalori,
            'total_resep'    => Resep::count(),
            'total_tempat'   => Tempat::count(),
            'total_menu'     => Menu::count(),
        ];
    }

    /** Simpan snapshot laporan saat tombol cetak/ekspor ditekan. */
    public static function simpanSnapshot(int $idAdmin, array $rekap): int
    {
        return static::insert([
            'id_admin'       => $idAdmin,
            'total_pengguna' => $rekap['total_pengguna'],
            'total_artikel'  => $rekap['total_artikel'],
            'total_kalori'   => $rekap['total_kalori'],
            'total_resep'    => $rekap['total_resep'],
            'total_tempat'   => $rekap['total_tempat'],
            'total_menu'     => $rekap['total_menu'],
            'tanggal_cetak'  => date('Y-m-d H:i:s'),
        ]);
    }

    /** Riwayat cetak laporan + nama admin. */
    public static function riwayat(int $limit = 10): array
    {
        $st = static::db()->prepare(
            'SELECT l.*, a.nama_lengkap AS username
             FROM laporan_statistik l
             LEFT JOIN users a ON a.id_pengguna = l.id_admin
             ORDER BY l.tanggal_cetak ASC, l.id_laporan ASC
             LIMIT ' . (int) $limit
        );
        $st->execute();
        return $st->fetchAll();
    }
}
