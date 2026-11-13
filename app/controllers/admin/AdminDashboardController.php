<?php
/**
 * Dashboard admin — ringkasan statistik sistem (FR-07).
 */
class AdminDashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();

        $rekap = LaporanStatistik::rekap();

        $this->view('admin/dashboard', [
            'pageTitle'  => 'Dashboard Admin',
            'activeNav'  => 'dashboard',
            'rekap'      => $rekap,
            'riwayat'    => LaporanStatistik::riwayat(10),
            'artikelBaru' => Artikel::terbaru(5),
            'perKategori' => Artikel::perKategori(),
            'totalUlasan' => Ulasan::count(),
            'totalGizi'   => Gizi::count(),
        ], 'admin');
    }
}
