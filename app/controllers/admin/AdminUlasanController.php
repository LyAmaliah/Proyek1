<?php
class AdminUlasanController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();
        $this->view('admin/ulasan/index', [
            'pageTitle' => 'Ulasan Pengguna',
            'activeNav' => 'ulasan',
            'ulasan'    => Ulasan::semuaUntukAdmin(),
        ]);
    }

    public function destroy(string $id): void
    {
        Auth::requireAdmin();
        verify_csrf();
        Ulasan::hapusById((int) $id);
        set_flash('success', 'Ulasan berhasil dihapus.');
        $this->redirect(BASE_URL . '/admin/ulasan');
    }
}