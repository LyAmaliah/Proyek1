<?php
/**
 * CRUD Tempat Sehat + submenu makanan/minumannya — FR-05.
 * Studi kasus: RealJuice Healthy Café.
 */
class AdminTempatController extends Controller
{
    /* ==================== TEMPAT ==================== */

    public function index(): void
    {
        Auth::requireAdmin();

        $q = $this->input('q');

        $this->view('admin/tempat/index', [
            'pageTitle' => 'Kelola Tempat Sehat',
            'activeNav' => 'tempat',
            'tempat'    => Tempat::denganRating($q ?: null),
            'q'         => $q,
            'kategoriMenu' => MenuCategory::semua(),
        ], 'admin');
    }

    public function create(): void
    {
        Auth::requireAdmin();

        $this->view('admin/tempat/form', [
            'pageTitle' => 'Tambah Tempat Sehat',
            'activeNav' => 'tempat',
            'tempat'    => null,
        ], 'admin');
    }

    public function store(): void
    {
        Auth::requireAdmin();

        $errors = $this->validasi();
        $fotoTempat = null;
        try {
            $fotoTempat = upload_image($_FILES['foto_tempat'] ?? null, 'tempat');
        } catch (RuntimeException $ex) {
            $errors[] = $ex->getMessage();
        }

        if ($errors) {
            foreach ($errors as $err) {
                set_flash('danger', $err);
            }
            $this->redirect(BASE_URL . '/admin/tempat/create');
        }

        Tempat::insert([
            'id_admin'   => Auth::userId(),
            'nama_tempat' => $this->input('nama_tempat'),
            'alamat'     => $this->input('alamat'),
            'deskripsi'  => $this->input('deskripsi'),
            'kontak'     => $this->input('kontak'),
            'foto_tempat' => $fotoTempat,
            'link_maps'  => $this->input('link_maps'),
            'link_instagram'  => $this->input('link_instagram'),
            'link_grabfood'   => $this->input('link_grabfood'),
            'link_shopeefood' => $this->input('link_shopeefood'),
        ]);

        set_flash('success', 'Tempat sehat berhasil ditambahkan.');
        $this->redirect(BASE_URL . '/admin/tempat');
    }

    public function edit(string $id): void
    {
        Auth::requireAdmin();

        $tempat = Tempat::find((int) $id);
        if (!$tempat) {
            set_flash('danger', 'Tempat tidak ditemukan.');
            $this->redirect(BASE_URL . '/admin/tempat');
        }

        $this->view('admin/tempat/form', [
            'pageTitle' => 'Edit Tempat Sehat',
            'activeNav' => 'tempat',
            'tempat'    => $tempat,
        ], 'admin');
    }

    public function update(string $id): void
    {
        Auth::requireAdmin();

        $tempat = Tempat::find((int) $id);
        if (!$tempat) {
            set_flash('danger', 'Tempat tidak ditemukan.');
            $this->redirect(BASE_URL . '/admin/tempat');
        }

        $errors = $this->validasi();
        $fotoTempatBaru = null;
        try {
            $fotoTempatBaru = upload_image($_FILES['foto_tempat'] ?? null, 'tempat');
        } catch (RuntimeException $ex) {
            $errors[] = $ex->getMessage();
        }

        if ($errors) {
            foreach ($errors as $err) {
                set_flash('danger', $err);
            }
            $this->redirect(BASE_URL . '/admin/tempat/' . $id . '/edit');
        }

        $hapusFoto = $this->input('hapus_foto_tempat') === '1';

        $fotoTempatAkhir = $tempat['foto_tempat'];
        if ($fotoTempatBaru) {
            // Ganti foto: hapus file lama, pakai file baru
            delete_image($tempat['foto_tempat'], 'tempat');
            $fotoTempatAkhir = $fotoTempatBaru;
        } elseif ($hapusFoto) {
            // Hapus foto: hapus file fisik & set kolom jadi NULL
            delete_image($tempat['foto_tempat'], 'tempat');
            $fotoTempatAkhir = null;
        }
        // Jika tidak ada file baru & tidak dicentang: foto lama dipertahankan

        Tempat::update((int) $id, [
            'nama_tempat' => $this->input('nama_tempat'),
            'alamat'     => $this->input('alamat'),
            'deskripsi'  => $this->input('deskripsi'),
            'kontak'     => $this->input('kontak'),
            'foto_tempat' => $fotoTempatAkhir,
            'link_maps'  => $this->input('link_maps'),
            'link_instagram'  => $this->input('link_instagram'),
            'link_grabfood'   => $this->input('link_grabfood'),
            'link_shopeefood' => $this->input('link_shopeefood'),
        ]);

        set_flash('success', 'Tempat sehat berhasil diperbarui.');
        $this->redirect(BASE_URL . '/admin/tempat');
    }

    public function destroy(string $id): void
    {
        Auth::requireAdmin();

        $tempat = Tempat::find((int) $id);
        if ($tempat) {
            // Hapus seluruh menu beserta fotonya
            foreach (Menu::byTempat((int) $id) as $menu) {
                delete_image($menu['foto_menu'], 'menu');
            }
            delete_image($tempat['foto_tempat'], 'tempat');
            delete_image($tempat['foto_menu'], 'tempat');
            Tempat::delete((int) $id);
            set_flash('success', 'Tempat beserta menunya berhasil dihapus.');
        }
        $this->redirect(BASE_URL . '/admin/tempat');
    }

    /* ==================== MENU (SUB-TEMPAT) ==================== */

    /** Halaman kelola menu untuk 1 tempat. */
    public function menu(string $id): void
    {
        Auth::requireAdmin();

        $tempat = Tempat::find((int) $id);
        if (!$tempat) {
            set_flash('danger', 'Tempat tidak ditemukan.');
            $this->redirect(BASE_URL . '/admin/tempat');
        }

        $editId = (int) $this->input('edit');
        $this->view('admin/tempat/menu', [
            'pageTitle' => 'Kelola Menu — ' . $tempat['nama_tempat'],
            'activeNav' => 'tempat',
            'tempat'    => $tempat,
            'menu'      => Menu::byTempat((int) $id),
            'menuEdit'  => $editId ? Menu::find($editId) : null,
            'kategori'  => MenuCategory::semua(),
        ], 'admin');
    }

    /* ==================== KATEGORI MENU ==================== */

    public function storeKategoriMenu(): void
    {
        Auth::requireAdmin();

        $nama = $this->input('nama_kategori');
        if ($nama === '' || mb_strlen($nama) < 2) {
            set_flash('danger', 'Nama kategori minimal 2 karakter.');
        } else {
            $ada = false;
            foreach (MenuCategory::semua() as $k) {
                if (strcasecmp($k['nama_kategori'], $nama) === 0) {
                    $ada = true;
                    break;
                }
            }
            if ($ada) {
                set_flash('warning', 'Kategori tersebut sudah ada.');
            } else {
                MenuCategory::insert([
                    'nama_kategori' => $nama,
                    'deskripsi'     => $this->input('deskripsi'),
                ]);
                set_flash('success', "Kategori '$nama' berhasil ditambahkan.");
            }
        }
        $this->redirect(BASE_URL . '/admin/tempat');
    }

    public function updateKategoriMenu(string $id): void
    {
        Auth::requireAdmin();

        $kategori = MenuCategory::find((int) $id);
        if (!$kategori) {
            set_flash('danger', 'Kategori tidak ditemukan.');
            $this->redirect(BASE_URL . '/admin/tempat');
        }

        $nama = $this->input('nama_kategori');
        if ($nama === '' || mb_strlen($nama) < 2) {
            set_flash('danger', 'Nama kategori minimal 2 karakter.');
        } else {
            $ada = false;
            foreach (MenuCategory::semua() as $k) {
                if ((int) $k['id_kategori_menu'] !== (int) $id && strcasecmp($k['nama_kategori'], $nama) === 0) {
                    $ada = true;
                    break;
                }
            }
            if ($ada) {
                set_flash('warning', 'Nama kategori sudah dipakai.');
            } else {
                MenuCategory::update((int) $id, [
                    'nama_kategori' => $nama,
                    'deskripsi'     => $this->input('deskripsi'),
                ]);
                set_flash('success', 'Kategori berhasil diperbarui.');
            }
        }
        $this->redirect(BASE_URL . '/admin/tempat');
    }

    public function destroyKategoriMenu(string $id): void
    {
        Auth::requireAdmin();

        $kategori = MenuCategory::find((int) $id);
        if ($kategori) {
            MenuCategory::delete((int) $id);
            set_flash('success', "Kategori '{$kategori['nama_kategori']}' berhasil dihapus.");
        } else {
            set_flash('danger', 'Kategori tidak ditemukan.');
        }
        $this->redirect(BASE_URL . '/admin/tempat');
    }

    public function storeMenu(string $id): void
    {
        Auth::requireAdmin();

        if (!Tempat::find((int) $id)) {
            set_flash('danger', 'Tempat tidak ditemukan.');
            $this->redirect(BASE_URL . '/admin/tempat');
        }

        $errors = $this->validasiMenu();
        $foto = null;
        try {
            $foto = upload_image($_FILES['foto_menu'] ?? null, 'menu');
        } catch (RuntimeException $ex) {
            $errors[] = $ex->getMessage();
        }

        if ($errors) {
            foreach ($errors as $err) {
                set_flash('danger', $err);
            }
            $this->redirect(BASE_URL . '/admin/tempat/' . $id . '/menu');
        }

        Menu::insert([
            'id_tempat'      => (int) $id,
            'id_kategori_menu' => $this->input('id_kategori_menu') !== '' ? (int) $this->input('id_kategori_menu') : null,
            'nama_menu'      => $this->input('nama_menu'),
            'deskripsi'      => $this->input('deskripsi'),
            'harga'          => (int) $this->inputFloat('harga'),
            'kalori'         => $this->inputFloat('kalori'),
            'protein'        => $this->inputFloat('protein'),
            'karbohidrat'    => $this->inputFloat('karbohidrat'),
            'lemak'          => $this->inputFloat('lemak'),
            'estimasi_waktu' => (int) $this->inputFloat('estimasi_waktu'),
            'foto_menu'      => $foto,
        ]);

        set_flash('success', 'Menu berhasil ditambahkan.');
        $this->redirect(BASE_URL . '/admin/tempat/' . $id . '/menu');
    }

    public function updateMenu(string $id, string $menu): void
    {
        Auth::requireAdmin();

        $row = Menu::find((int) $menu);
        if (!$row || (int) $row['id_tempat'] !== (int) $id) {
            set_flash('danger', 'Menu tidak ditemukan.');
            $this->redirect(BASE_URL . '/admin/tempat/' . $id . '/menu');
        }

        $errors = $this->validasiMenu();
        $fotoBaru = null;
        try {
            $fotoBaru = upload_image($_FILES['foto_menu'] ?? null, 'menu');
        } catch (RuntimeException $ex) {
            $errors[] = $ex->getMessage();
        }

        if ($errors) {
            foreach ($errors as $err) {
                set_flash('danger', $err);
            }
            $this->redirect(BASE_URL . '/admin/tempat/' . $id . '/menu?edit=' . $menu);
        }

        $fotoAkhir = $row['foto_menu'];
        $hapusFoto = $this->input('hapus_foto_menu') === '1';
        if ($fotoBaru) {
            // Ganti foto: hapus file lama, pakai file baru
            delete_image($row['foto_menu'], 'menu');
            $fotoAkhir = $fotoBaru;
        } elseif ($hapusFoto) {
            // Hapus foto: hapus file fisik & set kolom jadi NULL
            delete_image($row['foto_menu'], 'menu');
            $fotoAkhir = null;
        }
        // Jika tidak ada file baru & tidak dicentang: foto lama dipertahankan

        Menu::update((int) $menu, [
            'id_kategori_menu' => $this->input('id_kategori_menu') !== '' ? (int) $this->input('id_kategori_menu') : null,
            'nama_menu'      => $this->input('nama_menu'),
            'deskripsi'      => $this->input('deskripsi'),
            'harga'          => (int) $this->inputFloat('harga'),
            'kalori'         => $this->inputFloat('kalori'),
            'protein'        => $this->inputFloat('protein'),
            'karbohidrat'    => $this->inputFloat('karbohidrat'),
            'lemak'          => $this->inputFloat('lemak'),
            'estimasi_waktu' => (int) $this->inputFloat('estimasi_waktu'),
            'foto_menu'      => $fotoAkhir,
        ]);

        set_flash('success', 'Menu berhasil diperbarui.');
        $this->redirect(BASE_URL . '/admin/tempat/' . $id . '/menu');
    }

    public function destroyMenu(string $id, string $menu): void
    {
        Auth::requireAdmin();

        $row = Menu::find((int) $menu);
        if ($row && (int) $row['id_tempat'] === (int) $id) {
            delete_image($row['foto_menu'], 'menu');
            Menu::delete((int) $menu);
            set_flash('success', 'Menu berhasil dihapus.');
        }
        $this->redirect(BASE_URL . '/admin/tempat/' . $id . '/menu');
    }

    /* ==================== VALIDASI ==================== */

    private function validasi(): array
    {
        $errors = [];
        if (mb_strlen($this->input('nama_tempat')) < 3) {
            $errors[] = 'Nama tempat minimal 3 karakter.';
        }
        if ($this->input('alamat') === '') {
            $errors[] = 'Alamat wajib diisi.';
        }
        if ($this->input('link_maps') !== '' && !filter_var($this->input('link_maps'), FILTER_VALIDATE_URL)) {
            $errors[] = 'Link Google Maps tidak valid.';
        }
        foreach (['link_instagram', 'link_grabfood', 'link_shopeefood'] as $field) {
            $val = $this->input($field);
            if ($val !== '' && !filter_var($val, FILTER_VALIDATE_URL)) {
                $errors[] = 'Link ' . str_replace('link_', '', $field) . ' tidak valid.';
            }
        }
        return $errors;
    }

    private function validasiMenu(): array
    {
        $errors = [];
        if (mb_strlen($this->input('nama_menu')) < 2) {
            $errors[] = 'Nama menu minimal 2 karakter.';
        }
        if (!is_numeric($_POST['harga'] ?? null) || (float) $_POST['harga'] < 0) {
            $errors[] = 'Harga harus angka ≥ 0.';
        }
        foreach (['kalori', 'protein', 'karbohidrat', 'lemak'] as $field) {
            if (!is_numeric($_POST[$field] ?? null) || (float) $_POST[$field] < 0) {
                $errors[] = 'Nilai ' . $field . ' harus angka ≥ 0.';
            }
        }
        return $errors;
    }
}
