<?php
/**
 * CRUD Resep Sehat — FR-04.
 * (judul, deskripsi, bahan, langkah, waktu masak, total kalori, foto, status publikasi)
 */
class AdminResepController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();

        $q = $this->input('q');

        $this->view('admin/resep/index', [
            'pageTitle' => 'Kelola Resep',
            'activeNav' => 'resep',
            'resep'     => Resep::cari($q ?: null, null),
            'q'         => $q,
        ], 'admin');
    }

    public function create(): void
    {
        Auth::requireAdmin();

        $this->view('admin/resep/form', [
            'pageTitle' => 'Tambah Resep',
            'activeNav' => 'resep',
            'resep'     => null,
        ], 'admin');
    }

    public function store(): void
    {
        Auth::requireAdmin();

        $errors = $this->validasi();
        $foto = null;
        try {
            $foto = upload_image($_FILES['foto_resep'] ?? null, 'resep');
        } catch (RuntimeException $ex) {
            $errors[] = $ex->getMessage();
        }

        if ($errors) {
            foreach ($errors as $err) {
                set_flash('danger', $err);
            }
            $this->redirect(BASE_URL . '/admin/resep/create');
        }

        Resep::insert([
            'id_admin'          => Auth::adminId(),
            'judul_resep'       => $this->input('judul_resep'),
            'deskripsi'         => $this->input('deskripsi'),
            'bahan_bahan'       => $_POST['bahan_bahan'] ?? '',
            'langkah_pembuatan' => $_POST['langkah_pembuatan'] ?? '',
            'waktu_masak'       => (int) $this->inputFloat('waktu_masak'),
            'status_publikasi'  => $this->input('status_publikasi') === 'draft' ? 'draft' : 'dipublikasikan',
            'total_kalori'      => (int) $this->inputFloat('total_kalori'),
            'foto_resep'        => $foto,
        ]);

        set_flash('success', 'Resep berhasil ditambahkan.');
        $this->redirect(BASE_URL . '/admin/resep');
    }

    public function edit(string $id): void
    {
        Auth::requireAdmin();

        $resep = Resep::find((int) $id);
        if (!$resep) {
            set_flash('danger', 'Resep tidak ditemukan.');
            $this->redirect(BASE_URL . '/admin/resep');
        }

        $this->view('admin/resep/form', [
            'pageTitle' => 'Edit Resep',
            'activeNav' => 'resep',
            'resep'     => $resep,
        ], 'admin');
    }

    public function update(string $id): void
    {
        Auth::requireAdmin();

        $resep = Resep::find((int) $id);
        if (!$resep) {
            set_flash('danger', 'Resep tidak ditemukan.');
            $this->redirect(BASE_URL . '/admin/resep');
        }

        $errors = $this->validasi();
        $fotoBaru = null;
        try {
            $fotoBaru = upload_image($_FILES['foto_resep'] ?? null, 'resep');
        } catch (RuntimeException $ex) {
            $errors[] = $ex->getMessage();
        }

        if ($errors) {
            foreach ($errors as $err) {
                set_flash('danger', $err);
            }
            $this->redirect(BASE_URL . '/admin/resep/' . $id . '/edit');
        }

        $fotoAkhir = $resep['foto_resep'];
        if (!empty($_POST['hapus_gambar']) && !$fotoBaru) {
            delete_image($resep['foto_resep'], 'resep');
            $fotoAkhir = null;
        } elseif ($fotoBaru) {
            delete_image($resep['foto_resep'], 'resep');
            $fotoAkhir = $fotoBaru;
        }

        Resep::update((int) $id, [
            'judul_resep'       => $this->input('judul_resep'),
            'deskripsi'         => $this->input('deskripsi'),
            'bahan_bahan'       => $_POST['bahan_bahan'] ?? '',
            'langkah_pembuatan' => $_POST['langkah_pembuatan'] ?? '',
            'waktu_masak'       => (int) $this->inputFloat('waktu_masak'),
            'status_publikasi'  => $this->input('status_publikasi') === 'draft' ? 'draft' : 'dipublikasikan',
            'total_kalori'      => (int) $this->inputFloat('total_kalori'),
            'foto_resep'        => $fotoAkhir,
        ]);

        set_flash('success', 'Resep berhasil diperbarui.');
        $this->redirect(BASE_URL . '/admin/resep');
    }

    public function destroy(string $id): void
    {
        Auth::requireAdmin();

        $resep = Resep::find((int) $id);
        if ($resep) {
            delete_image($resep['foto_resep'], 'resep');
            Resep::delete((int) $id);
            set_flash('success', 'Resep berhasil dihapus.');
        }
        $this->redirect(BASE_URL . '/admin/resep');
    }

    private function validasi(): array
    {
        $errors = [];
        if (mb_strlen($this->input('judul_resep')) < 4) {
            $errors[] = 'Judul resep minimal 4 karakter.';
        }
        if ($this->input('deskripsi') === '') {
            $errors[] = 'Deskripsi resep wajib diisi.';
        }
        if (trim((string) ($_POST['bahan_bahan'] ?? '')) === '') {
            $errors[] = 'Bahan-bahan wajib diisi.';
        }
        if (trim((string) ($_POST['langkah_pembuatan'] ?? '')) === '') {
            $errors[] = 'Langkah pembuatan wajib diisi.';
        }
        if (!is_numeric($_POST['waktu_masak'] ?? null) || (float) $_POST['waktu_masak'] <= 0) {
            $errors[] = 'Waktu masak harus angka > 0 (menit).';
        }
        if (!is_numeric($_POST['total_kalori'] ?? null) || (float) $_POST['total_kalori'] < 0) {
            $errors[] = 'Total kalori harus angka ≥ 0.';
        }
        return $errors;
    }
}
