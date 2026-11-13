<?php
/**
 * Kelola data pengguna — FR-06 (lihat, edit, hapus).
 */
class AdminPenggunaController extends Controller
{
    public function index(): void
    {
        Auth::requireAdmin();

        $q = $this->input('q');

        $this->view('admin/pengguna/index', [
            'pageTitle' => 'Kelola Pengguna',
            'activeNav' => 'pengguna',
            'pengguna'  => User::cari($q ?: null),
            'q'         => $q,
        ], 'admin');
    }

    public function edit(string $id): void
    {
        Auth::requireAdmin();

        $pengguna = User::find((int) $id);
        if (!$pengguna) {
            set_flash('danger', 'Pengguna tidak ditemukan.');
            $this->redirect(BASE_URL . '/admin/pengguna');
        }

        $this->view('admin/pengguna/form', [
            'pageTitle' => 'Edit Pengguna',
            'activeNav' => 'pengguna',
            'pengguna'  => $pengguna,
            'bmi'       => KalkulatorBmi::terakhir((int) $id),
        ], 'admin');
    }

    public function update(string $id): void
    {
        Auth::requireAdmin();

        $pengguna = User::find((int) $id);
        if (!$pengguna) {
            set_flash('danger', 'Pengguna tidak ditemukan.');
            $this->redirect(BASE_URL . '/admin/pengguna');
        }

        $nama    = $this->input('nama_lengkap');
        $username = $this->input('username');
        $berat   = $this->inputFloat('berat_badan');
        $tinggi  = $this->inputFloat('tinggi_badan');
        $umur    = (int) $this->inputFloat('umur');
        $jk      = $this->input('jenis_kelamin');
        $password = (string) ($_POST['password'] ?? '');

        $errors = [];
        if (mb_strlen($nama) < 3) {
            $errors[] = 'Nama lengkap minimal 3 karakter.';
        }
        if (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
            $errors[] = 'Username 4–20 karakter (huruf/angka/underscore).';
        } elseif (User::usernameExists($username, (int) $id)) {
            $errors[] = 'Username sudah dipakai pengguna lain.';
        }
        if ($berat < 20 || $berat > 300 || $tinggi < 50 || $tinggi > 250) {
            $errors[] = 'Berat (20–300 kg) atau tinggi (50–250 cm) tidak valid.';
        }
        if ($umur < 5 || $umur > 100 || !in_array($jk, ['L', 'P'], true)) {
            $errors[] = 'Umur atau jenis kelamin tidak valid.';
        }
        if ($password !== '' && strlen($password) < 6) {
            $errors[] = 'Password baru minimal 6 karakter.';
        }

        if ($errors) {
            foreach ($errors as $err) {
                set_flash('danger', $err);
            }
            $this->redirect(BASE_URL . '/admin/pengguna/' . $id . '/edit');
        }

        $data = [
            'nama_lengkap'  => $nama,
            'username'      => $username,
            'berat_badan'   => $berat,
            'tinggi_badan'  => $tinggi,
            'umur'          => $umur,
            'jenis_kelamin' => $jk,
        ];
        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        User::update((int) $id, $data);

        if ($berat != $pengguna['berat_badan'] || $tinggi != $pengguna['tinggi_badan']
            || $umur != $pengguna['umur'] || $jk !== $pengguna['jenis_kelamin']) {
            KalkulatorBmi::hitungDanSimpan((int) $id, [
                'berat_badan'   => $berat,
                'tinggi_badan'  => $tinggi,
                'umur'          => $umur,
                'jenis_kelamin' => $jk,
            ]);
        }

        set_flash('success', 'Data pengguna berhasil diperbarui.');
        $this->redirect(BASE_URL . '/admin/pengguna');
    }

    public function destroy(string $id): void
    {
        Auth::requireAdmin();

        $pengguna = User::find((int) $id);
        if ($pengguna) {
            User::delete((int) $id);
            set_flash('success', 'Pengguna "' . e($pengguna['nama_lengkap']) . '" berhasil dihapus.');
        } else {
            set_flash('danger', 'Pengguna tidak ditemukan.');
        }
        $this->redirect(BASE_URL . '/admin/pengguna');
    }
}
