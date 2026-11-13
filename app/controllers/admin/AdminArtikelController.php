<?php
/**
 * CRUD Artikel Kesehatan — FR-02.
 * (judul, kategori, isi, foto, tanggal terbit)
 */
class AdminArtikelController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();

        $q = $this->input('q');

        $this->view('admin/artikel/index', [
            'pageTitle' => 'Kelola Artikel',
            'activeNav' => 'artikel',
            'artikel'   => Artikel::cari($q ?: null),
            'q'         => $q,
            'kategori'  => KategoriArtikel::semua(),
        ], 'admin');
    }

    /* ==================== KATEGORI ARTIKEL ==================== */

    public function storeKategori(): void
    {
        Auth::requireAdmin();
        $nama = $this->input('nama_kategori');
        if ($nama === '' || mb_strlen($nama) < 3) {
            set_flash('danger', 'Nama kategori minimal 3 karakter.');
        } else {
            $ada = false;
            foreach (KategoriArtikel::semua() as $k) {
                if (strcasecmp($k['nama_kategori'], $nama) === 0) { $ada = true; break; }
            }
            if ($ada) {
                set_flash('warning', 'Kategori tersebut sudah ada.');
            } else {
                KategoriArtikel::insert(['nama_kategori' => $nama]);
                set_flash('success', "Kategori '$nama' berhasil ditambahkan.");
            }
        }
        $this->redirect(BASE_URL . '/admin/artikel');
    }

    public function updateKategori(string $id): void
    {
        Auth::requireAdmin();
        $kategori = KategoriArtikel::find((int) $id);
        if (!$kategori) {
            set_flash('danger', 'Kategori tidak ditemukan.');
            $this->redirect(BASE_URL . '/admin/artikel');
        }
        $nama = $this->input('nama_kategori');
        if ($nama === '' || mb_strlen($nama) < 3) {
            set_flash('danger', 'Nama kategori minimal 3 karakter.');
        } else {
            $ada = false;
            foreach (KategoriArtikel::semua() as $k) {
                if ((int) $k['id_kategori_artikel'] !== (int) $id && strcasecmp($k['nama_kategori'], $nama) === 0) {
                    $ada = true; break;
                }
            }
            if ($ada) {
                set_flash('warning', 'Nama kategori sudah dipakai.');
            } else {
                KategoriArtikel::update((int) $id, ['nama_kategori' => $nama]);
                set_flash('success', 'Kategori berhasil diperbarui.');
            }
        }
        $this->redirect(BASE_URL . '/admin/artikel');
    }

    public function destroyKategori(string $id): void
    {
        Auth::requireAdmin();
        $kategori = KategoriArtikel::find((int) $id);
        if ($kategori) {
            $st = Database::connect()->prepare('SELECT COUNT(*) FROM artikel_kesehatan WHERE id_kategori_artikel = ?');
            $st->execute([(int) $id]);
            if ((int) $st->fetchColumn() > 0) {
                set_flash('danger', 'Kategori masih dipakai oleh artikel dan tidak bisa dihapus.');
            } else {
                KategoriArtikel::delete((int) $id);
                set_flash('success', "Kategori '{$kategori['nama_kategori']}' berhasil dihapus.");
            }
        } else {
            set_flash('danger', 'Kategori tidak ditemukan.');
        }
        $this->redirect(BASE_URL . '/admin/artikel');
    }

    public function create(): void
    {
        Auth::requireAdmin();

        $this->view('admin/artikel/form', [
            'pageTitle' => 'Tambah Artikel',
            'activeNav' => 'artikel',
            'artikel'   => null,
            'kategori'  => KategoriArtikel::semua(),
        ], 'admin');
    }

    public function store(): void
    {
        Auth::requireAdmin();

        $errors = $this->validasi();
        $foto = null;
        try {
            $foto = upload_image($_FILES['foto_artikel'] ?? null, 'artikel');
        } catch (RuntimeException $ex) {
            $errors[] = $ex->getMessage();
        }

        if ($errors) {
            foreach ($errors as $err) {
                set_flash('danger', $err);
            }
            $this->redirect(BASE_URL . '/admin/artikel/create');
        }

        $id = Artikel::insert([
            'id_kategori_artikel' => (int) $this->input('id_kategori_artikel'),
            'id_admin'            => Auth::adminId(),
            'judul_artikel'       => $this->input('judul_artikel'),
            'isi_artikel'         => $_POST['isi_artikel'] ?? '',
            'foto_artikel'        => $foto,
            'tanggal_terbit'      => $this->input('tanggal_terbit') ?: date('Y-m-d'),
        ]);

        set_flash('success', 'Artikel berhasil disimpan (ID: ' . $id . ').');
        $this->redirect(BASE_URL . '/admin/artikel');
    }

    public function edit(string $id): void
    {
        Auth::requireAdmin();

        $artikel = Artikel::find((int) $id);
        if (!$artikel) {
            set_flash('danger', 'Artikel tidak ditemukan.');
            $this->redirect(BASE_URL . '/admin/artikel');
        }

        $this->view('admin/artikel/form', [
            'pageTitle' => 'Edit Artikel',
            'activeNav' => 'artikel',
            'artikel'   => $artikel,
            'kategori'  => KategoriArtikel::semua(),
        ], 'admin');
    }

    public function update(string $id): void
    {
        Auth::requireAdmin();

        $artikel = Artikel::find((int) $id);
        if (!$artikel) {
            set_flash('danger', 'Artikel tidak ditemukan.');
            $this->redirect(BASE_URL . '/admin/artikel');
        }

        $errors = $this->validasi();

        $fotoLama = $artikel['foto_artikel'];
        $fotoBaru = null;
        try {
            $fotoBaru = upload_image($_FILES['foto_artikel'] ?? null, 'artikel');
        } catch (RuntimeException $ex) {
            $errors[] = $ex->getMessage();
        }

        if ($errors) {
            foreach ($errors as $err) {
                set_flash('danger', $err);
            }
            $this->redirect(BASE_URL . '/admin/artikel/' . $id . '/edit');
        }

        $fotoAkhir = $fotoLama;
        if (!empty($_POST['hapus_gambar']) && !$fotoBaru) {
            delete_image($fotoLama, 'artikel');
            $fotoAkhir = null;
        } elseif ($fotoBaru) {
            delete_image($fotoLama, 'artikel');
            $fotoAkhir = $fotoBaru;
        }

        Artikel::update((int) $id, [
            'id_kategori_artikel' => (int) $this->input('id_kategori_artikel'),
            'judul_artikel'       => $this->input('judul_artikel'),
            'isi_artikel'         => $_POST['isi_artikel'] ?? '',
            'foto_artikel'        => $fotoAkhir,
            'tanggal_terbit'      => $this->input('tanggal_terbit') ?: date('Y-m-d'),
        ]);

        set_flash('success', 'Artikel berhasil diperbarui.');
        $this->redirect(BASE_URL . '/admin/artikel');
    }

    public function destroy(string $id): void
    {
        Auth::requireAdmin();

        $artikel = Artikel::find((int) $id);
        if ($artikel) {
            delete_image($artikel['foto_artikel'], 'artikel');
            Artikel::delete((int) $id);
            set_flash('success', 'Artikel berhasil dihapus.');
        } else {
            set_flash('danger', 'Artikel tidak ditemukan.');
        }
        $this->redirect(BASE_URL . '/admin/artikel');
    }

    /** Validasi bersama form create/update. */
    private function validasi(): array
    {
        $errors = [];
        if ($this->input('judul_artikel') === '' || mb_strlen($this->input('judul_artikel')) < 5) {
            $errors[] = 'Judul artikel minimal 5 karakter.';
        }
        if (trim((string) ($_POST['isi_artikel'] ?? '')) === '') {
            $errors[] = 'Isi artikel wajib diisi.';
        }
        if (!KategoriArtikel::find((int) $this->input('id_kategori_artikel'))) {
            $errors[] = 'Kategori artikel tidak valid.';
        }
        $tanggal = $this->input('tanggal_terbit');
        if ($tanggal && !strtotime($tanggal)) {
            $errors[] = 'Tanggal terbit tidak valid.';
        }
        return $errors;
    }
}
