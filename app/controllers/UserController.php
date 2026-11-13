<?php
/**
 * Controller profil pengguna & kalkulator BMI (FR-09).
 */
class UserController extends Controller
{
    /** Halaman profil + hasil BMI terakhir. */
    public function profil(): void
    {
        Auth::requireUser();

        $pengguna = User::find(Auth::userId());
        if (!$pengguna) {
            Auth::logout();
            $this->redirect(BASE_URL . '/auth/login');
        }

        $this->view('user/profil', [
            'pageTitle' => 'Profil & Kalkulator BMI',
            'activeNav' => 'profil',
            'pengguna'  => $pengguna,
            'bmi'       => KalkulatorBmi::terakhir((int) $pengguna['id_pengguna']),
            'riwayat'   => KalkulatorBmi::riwayat((int) $pengguna['id_pengguna'], 5),
        ]);
    }

    /** Update profil sekaligus menghitung ulang BMI & estimasi kalori (FR-09). */
    public function updateProfil(): void
    {
        Auth::requireUser();

        $pengguna = User::find(Auth::userId());
        if (!$pengguna) {
            Auth::logout();
            $this->redirect(BASE_URL . '/auth/login');
        }

        $nama     = $this->input('nama_lengkap');
        $berat    = $this->inputFloat('berat_badan');
        $tinggi   = $this->inputFloat('tinggi_badan');
        $umur     = (int) $this->inputFloat('umur');
        $jk       = $this->input('jenis_kelamin');
        $password = (string) ($_POST['password'] ?? '');

        if ($nama === '' || mb_strlen($nama) < 3) {
            set_flash('danger', 'Nama lengkap minimal 3 karakter.');
            $this->redirect(BASE_URL . '/profil');
        }
        if ($berat < 20 || $berat > 300) {
            set_flash('danger', 'Berat badan harus 20–300 kg.');
            $this->redirect(BASE_URL . '/profil');
        }
        if ($tinggi < 50 || $tinggi > 250) {
            set_flash('danger', 'Tinggi badan harus 50–250 cm.');
            $this->redirect(BASE_URL . '/profil');
        }
        if ($umur < 5 || $umur > 100) {
            set_flash('danger', 'Umur harus 5–100 tahun.');
            $this->redirect(BASE_URL . '/profil');
        }
        if (!in_array($jk, ['L', 'P'], true)) {
            set_flash('danger', 'Pilih jenis kelamin.');
            $this->redirect(BASE_URL . '/profil');
        }
        if ($password !== '' && strlen($password) < 6) {
            set_flash('danger', 'Password baru minimal 6 karakter.');
            $this->redirect(BASE_URL . '/profil');
        }

        $data = [
            'nama_lengkap'  => $nama,
            'berat_badan'   => $berat,
            'tinggi_badan'  => $tinggi,
            'umur'          => $umur,
            'jenis_kelamin' => $jk,
        ];
        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        User::update((int) $pengguna['id_pengguna'], $data);

        $hasil = KalkulatorBmi::hitungDanSimpan((int) $pengguna['id_pengguna'], [
            'berat_badan'   => $berat,
            'tinggi_badan'  => $tinggi,
            'umur'          => $umur,
            'jenis_kelamin' => $jk,
        ]);

        $_SESSION['user']['nama_lengkap'] = $nama;

        set_flash(
            'success',
            'Profil diperbarui. BMI Anda: <b>' . $hasil['nilai_bmi'] . '</b> (' .
            $hasil['kategori_bmi'] . ') — estimasi kalori harian <b>' .
            number_format($hasil['estimasi_kalori']) . ' kkal</b>.'
        );
        $this->redirect(BASE_URL . '/profil');
    }

    /** Halaman kalkulator BMI (dapat diakses tamu; tersimpan jika login). */
    public function bmi(): void
    {
        $pengguna = Auth::check() ? User::find(Auth::userId()) : null;

        $this->view('user/bmi', [
            'pageTitle' => 'Kalkulator BMI',
            'activeNav' => 'bmi',
            'pengguna'  => $pengguna,
            'hasil'     => null,
        ]);
    }

    /** Proses perhitungan BMI via form (simpan riwayat jika sudah login). */
    public function hitungBmi(): void
    {
        $berat  = $this->inputFloat('berat_badan');
        $tinggi = $this->inputFloat('tinggi_badan');
        $umur   = (int) $this->inputFloat('umur');
        $jk     = $this->input('jenis_kelamin');

        if ($berat < 20 || $berat > 300 || $tinggi < 50 || $tinggi > 250) {
            set_flash('danger', 'Masukkan berat (20–300 kg) dan tinggi (50–250 cm) yang valid.');
            $this->redirect(BASE_URL . '/bmi');
        }
        if ($umur < 5 || $umur > 100 || !in_array($jk, ['L', 'P'], true)) {
            set_flash('danger', 'Masukkan umur (5–100 tahun) dan pilih jenis kelamin.');
            $this->redirect(BASE_URL . '/bmi');
        }

        $hasil = [
            'nilai_bmi'       => hitung_bmi($berat, $tinggi),
            'kategori_bmi'    => kategori_bmi(hitung_bmi($berat, $tinggi)),
            'estimasi_kalori' => estimasi_kalori($berat, $tinggi, $umur, $jk),
            'berat'           => $berat,
            'tinggi'          => $tinggi,
            'umur'            => $umur,
            'jk'              => $jk,
        ];

        if (Auth::check()) {
            $id = Auth::userId();
            KalkulatorBmi::hitungDanSimpan($id, [
                'berat_badan'   => $berat,
                'tinggi_badan'  => $tinggi,
                'umur'          => $umur,
                'jenis_kelamin' => $jk,
            ]);
            User::update($id, [
                'berat_badan'  => $berat,
                'tinggi_badan' => $tinggi,
                'umur'         => $umur,
            ]);
            $hasil['tersimpan'] = true;
        }

        $pengguna = Auth::check() ? User::find(Auth::userId()) : null;

        $this->view('user/bmi', [
            'pageTitle' => 'Kalkulator BMI',
            'activeNav' => 'bmi',
            'pengguna'  => $pengguna,
            'hasil'     => $hasil,
        ]);
    }
}
