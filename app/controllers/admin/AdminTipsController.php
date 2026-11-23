<?php
class AdminTipsController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();
        $this->view('admin/tips/index', [
            'pageTitle' => 'Kelola Tips Harian (31 Hari)',
            'activeNav' => 'tips',
            'daftar'    => Tips::semua(),
            'hariIni'   => (int) date('j'),
        ], 'admin');
    }

    public function update(int $id): void
    {
        Auth::requireAdmin();
        $judul   = $this->input('judul');
        $isiTips = $this->input('isi_tips');
        $kat     = $this->input('kategori');

        Tips::update($id, [
            'judul'    => $judul,
            'isi_tips' => $isiTips,
            'kategori' => $kat,
        ]);

        set_flash('success', 'Tips sehat berhasil diperbarui.');
        $this->redirect(BASE_URL . '/admin/tips');
    }
}