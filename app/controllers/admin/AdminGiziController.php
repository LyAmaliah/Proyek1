<?php
/**
 * CRUD Kandungan Gizi & Kalori (per 100 gram) — FR-03.
 */
class AdminGiziController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();

        $q = $this->input('q');

        $this->view('admin/gizi/index', [
            'pageTitle' => 'Kelola Gizi & Kalori',
            'activeNav' => 'gizi',
            'daftar'    => Gizi::cari($q ?: null),
            'q'         => $q,
        ], 'admin');
    }

    public function create(): void
    {
        Auth::requireAdmin();

        $this->view('admin/gizi/form', [
            'pageTitle' => 'Tambah Data Gizi',
            'activeNav' => 'gizi',
            'gizi'      => null,
        ], 'admin');
    }

    public function store(): void
    {
        Auth::requireAdmin();

        $errors = $this->validasi();
        if ($errors) {
            foreach ($errors as $err) {
                set_flash('danger', $err);
            }
            $this->redirect(BASE_URL . '/admin/gizi/create');
        }

        Gizi::insert([
            'id_admin'           => Auth::adminId(),
            'nama_makanan_minuman' => $this->input('nama_makanan_minuman'),
            'kategori'           => $this->input('kategori'),
            'porsi'              => $this->input('porsi') ?: '100 gram',
            'kalori'             => $this->inputFloat('kalori'),
            'karbohidrat'        => $this->inputFloat('karbohidrat'),
            'protein'            => $this->inputFloat('protein'),
            'lemak'              => $this->inputFloat('lemak'),
        ]);

        set_flash('success', 'Data kandungan gizi berhasil ditambahkan.');
        $this->redirect(BASE_URL . '/admin/gizi');
    }

    public function edit(string $id): void
    {
        Auth::requireAdmin();

        $gizi = Gizi::find((int) $id);
        if (!$gizi) {
            set_flash('danger', 'Data gizi tidak ditemukan.');
            $this->redirect(BASE_URL . '/admin/gizi');
        }

        $this->view('admin/gizi/form', [
            'pageTitle' => 'Edit Data Gizi',
            'activeNav' => 'gizi',
            'gizi'      => $gizi,
        ], 'admin');
    }

    public function update(string $id): void
    {
        Auth::requireAdmin();

        if (!Gizi::find((int) $id)) {
            set_flash('danger', 'Data gizi tidak ditemukan.');
            $this->redirect(BASE_URL . '/admin/gizi');
        }

        $errors = $this->validasi();
        if ($errors) {
            foreach ($errors as $err) {
                set_flash('danger', $err);
            }
            $this->redirect(BASE_URL . '/admin/gizi/' . $id . '/edit');
        }

        Gizi::update((int) $id, [
            'nama_makanan_minuman' => $this->input('nama_makanan_minuman'),
            'kategori'           => $this->input('kategori'),
            'porsi'              => $this->input('porsi') ?: '100 gram',
            'kalori'             => $this->inputFloat('kalori'),
            'karbohidrat'        => $this->inputFloat('karbohidrat'),
            'protein'            => $this->inputFloat('protein'),
            'lemak'              => $this->inputFloat('lemak'),
        ]);

        set_flash('success', 'Data kandungan gizi berhasil diperbarui.');
        $this->redirect(BASE_URL . '/admin/gizi');
    }

    public function destroy(string $id): void
    {
        Auth::requireAdmin();

        if (Gizi::delete((int) $id)) {
            set_flash('success', 'Data gizi berhasil dihapus.');
        }
        $this->redirect(BASE_URL . '/admin/gizi');
    }

    private function validasi(): array
    {
        $errors = [];
        if ($this->input('nama_makanan_minuman') === '') {
            $errors[] = 'Nama makanan/minuman wajib diisi.';
        }
        if (!in_array($this->input('kategori'), ['Makanan', 'Minuman'], true)) {
            $errors[] = 'Kategori harus Makanan atau Minuman.';
        }
        foreach (['kalori', 'karbohidrat', 'protein', 'lemak'] as $field) {
            if (!is_numeric($_POST[$field] ?? null) || (float) $_POST[$field] < 0) {
                $errors[] = 'Nilai ' . $field . ' harus angka ≥ 0.';
            }
        }
        return $errors;
    }
}
