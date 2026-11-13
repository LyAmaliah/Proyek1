<?php
/**
 * Cetak/ekspor PDF laporan statistik — FR-07.
 *
 * Ekspor PDF memakai dialog cetak browser ("Save as PDF").
 * Cukup tekan Ctrl+P → Destination: Save as PDF.
 */
class AdminLaporanController extends Controller
{
    /** Halaman siap cetak — sekaligus menyimpan snapshot laporan. */
    public function cetak(): void
    {
        Auth::requireAdmin();

        $rekap = LaporanStatistik::rekap();
        LaporanStatistik::simpanSnapshot(Auth::userId(), $rekap);

        $this->view('admin/laporan/cetak', [
            'pageTitle'  => 'Cetak Laporan Statistik',
            'activeNav'  => 'dashboard',
            'rekap'      => $rekap,
            'perKategori' => Artikel::perKategori(),
            'admin'      => Auth::user(),
            'tanggal'    => date('Y-m-d H:i:s'),
            'totalUlasan' => Ulasan::count(),
            'totalGizi'   => Gizi::count(),
        ], 'guest');
    }
}
