<?php
/**
 * Controller beranda & halaman 404 (situs publik).
 */
class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index', [
            'pageTitle'  => 'Beranda',
            'activeNav'  => 'beranda',
            'statistik'  => [
                'pengguna' => User::count(),
                'artikel'  => Artikel::count(),
                'resep'    => Resep::count(),
                'tempat'   => Tempat::count(),
            ],
            'artikel' => Artikel::terbaru(3),
            'resep'   => array_slice(Resep::cari(null, 'dipublikasikan'), 0, 3),
            // Catatan: daftar "Rekomendasi Tempat Sehat" sengaja tidak lagi
            // dimuat di beranda (direktori tempat tetap ada di /tempat).
        ]);
    }

    public function notFound(): void
    {
        http_response_code(404);
        $this->view('home/404', [
            'pageTitle' => 'Halaman Tidak Ditemukan',
            'activeNav' => '',
        ]);
    }
}
