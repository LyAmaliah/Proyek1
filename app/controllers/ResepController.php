<?php
/**
 * Controller resep sehat untuk pengguna (FR-12).
 */
class ResepController extends Controller
{
    public function index(): void
    {
        $q         = $this->input('q');
        $maksWaktu = (int) $this->input('waktu');

        $this->view('resep/index', [
            'pageTitle'  => 'Resep Sehat',
            'activeNav'  => 'resep',
            'resep'      => Resep::cari($q ?: null, 'dipublikasikan', $maksWaktu ?: null),
            'q'          => $q,
            'maksWaktu'  => $maksWaktu,
        ]);
    }

    public function detail(string $id): void
    {
        $resep = Resep::detail((int) $id);
        if (!$resep || $resep['status_publikasi'] !== 'dipublikasikan') {
            set_flash('danger', 'Resep tidak ditemukan atau belum dipublikasikan.');
            $this->redirect(BASE_URL . '/resep');
        }

        $this->view('resep/detail', [
            'pageTitle' => $resep['judul_resep'],
            'activeNav' => 'resep',
            'resep'     => $resep,
        ]);
    }
}
