<?php
/**
 * Controller cek kandungan gizi & kalori (FR-11).
 * Frontend super ringkas — seluruh logika FatSecret & database ada di Backend (Model Gizi).
 */
class GiziController extends Controller
{
    public function index(): void
    {
        $q        = $this->input('q');
        $kategori = $this->input('kategori');
        $daftar   = [];

        if ($q !== '' || $kategori !== '') {
            $daftar = Gizi::cariDenganApi($q, $kategori);
        }

        $this->view('gizi/index', [
            'pageTitle'     => 'Cek Gizi & Kalori',
            'activeNav'     => 'gizi',
            'daftar'        => $daftar,
            'kategori'      => Gizi::daftarKategori(),
            'q'             => $q,
            'kategoriAktif' => $kategori,
            'errorMsg'      => null,
        ]);
    }
}