<?php
/**
 * Controller artikel kesehatan untuk pengguna (FR-10).
 */
class ArtikelController extends Controller
{
    public function index(): void
    {
        $q          = $this->input('q');
        $idKategori = (int) $this->input('id_kategori');

        $this->view('artikel/index', [
            'pageTitle'    => 'Artikel Kesehatan',
            'activeNav'    => 'artikel',
            'artikel'      => Artikel::cari($q ?: null, $idKategori ?: null),
            'kategori'     => KategoriArtikel::semua(),
            'q'            => $q,
            'idKategori'   => $idKategori,
        ]);
    }

    public function detail(string $id): void
    {
        $artikel = Artikel::detail((int) $id);
        if (!$artikel) {
            set_flash('danger', 'Artikel tidak ditemukan.');
            $this->redirect(BASE_URL . '/artikel');
        }

        $this->view('artikel/detail', [
            'pageTitle' => $artikel['judul_artikel'],
            'activeNav' => 'artikel',
            'artikel'   => $artikel,
            'terkait'   => Artikel::terkait(
                (int) $artikel['id_artikel'],
                $artikel['id_kategori_artikel']
            ),
        ]);
    }
}
