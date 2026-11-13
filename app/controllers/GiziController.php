<?php
/**
 * Controller cek kandungan gizi & kalori (FR-11).
 *
 * Pencarian dilakukan di server (GET), perhitungan per gramasi
 * dilakukan di sisi klien (assets/js/main.js) berdasarkan nilai per 100 gram.
 */
class GiziController extends Controller
{
    public function index(): void
    {
        $q        = $this->input('q');
        $kategori = $this->input('kategori');

        $this->view('gizi/index', [
            'pageTitle' => 'Cek Gizi & Kalori',
            'activeNav' => 'gizi',
            'daftar'    => Gizi::cari($q ?: null, $kategori ?: null),
            'kategori'  => Gizi::daftarKategori(),
            'q'         => $q,
            'kategoriAktif' => $kategori,
        ]);
    }
}
