<?php
/**
 * Controller autentikasi — Single Login Form (deteksi role otomatis).
 */
class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->redirect($user['role'] === 'admin' ? BASE_URL . '/admin' : BASE_URL . '/profil');
        }
        $this->view('auth/login', [
            'pageTitle' => 'Masuk',
            'activeNav'  => 'login',
        ]);
    }

    public function doLogin(): void
    {
        $username = $this->input('username');
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            set_flash('danger', 'Username dan password wajib diisi.');
            $this->redirect(BASE_URL . '/auth/login');
        }

        $user = Auth::attempt($username, $password);

        if ($user) {
            if ($user['role'] === 'admin') {
                set_flash('success', 'Selamat datang, Admin!');
                $this->redirect(BASE_URL . '/admin');
            }
            set_flash('success', 'Selamat datang, ' . e($user['nama_lengkap']) . '!');
            $this->redirect(BASE_URL . '/profil');
        }

        set_flash('danger', 'Username atau password salah.');
        $this->redirect(BASE_URL . '/auth/login');
    }

    /* ==================== REGISTRASI ==================== */

    public function register(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->redirect($user['role'] === 'admin' ? BASE_URL . '/admin' : BASE_URL . '/profil');
        }
        $this->view('auth/register', [
            'pageTitle' => 'Daftar',
            'activeNav'  => 'register',
        ]);
    }

    public function doRegister(): void
    {
        $nama      = $this->input('nama_lengkap');
        $username  = $this->input('username');
        $password  = (string) ($_POST['password'] ?? '');
        $confirm   = (string) ($_POST['password_confirm'] ?? '');
        $berat     = $this->inputFloat('berat_badan');
        $tinggi    = $this->inputFloat('tinggi_badan');
        $umur      = (int) $this->inputFloat('umur');
        $jk        = $this->input('jenis_kelamin');

        $errors = [];
        if ($nama === '' || mb_strlen($nama) < 3) {
            $errors[] = 'Nama lengkap minimal 3 karakter.';
        }
        if ($username === '' || !preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
            $errors[] = 'Username 4–20 karakter, hanya huruf, angka, dan underscore.';
        } elseif (User::usernameExists($username)) {
            $errors[] = 'Username sudah digunakan.';
        }
        if (strlen($password) < 6) {
            $errors[] = 'Password minimal 6 karakter.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Konfirmasi password tidak sama.';
        }
        if ($berat < 20 || $berat > 300) {
            $errors[] = 'Berat badan harus 20–300 kg.';
        }
        if ($tinggi < 50 || $tinggi > 250) {
            $errors[] = 'Tinggi badan harus 50–250 cm.';
        }
        if ($umur < 5 || $umur > 100) {
            $errors[] = 'Umur harus 5–100 tahun.';
        }
        if (!in_array($jk, ['L', 'P'], true)) {
            $errors[] = 'Pilih jenis kelamin.';
        }

        if ($errors) {
            foreach ($errors as $err) {
                set_flash('danger', $err);
            }
            $this->redirect(BASE_URL . '/auth/register');
        }

        $id = User::insert([
            'nama_lengkap'  => $nama,
            'username'      => $username,
            'password'      => password_hash($password, PASSWORD_DEFAULT),
            'role'          => 'user',
            'berat_badan'   => $berat,
            'tinggi_badan'  => $tinggi,
            'jenis_kelamin' => $jk,
            'umur'          => $umur,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        KalkulatorBmi::hitungDanSimpan($id, [
            'berat_badan'   => $berat,
            'tinggi_badan'  => $tinggi,
            'umur'          => $umur,
            'jenis_kelamin' => $jk,
        ]);

        Auth::login([
            'id_pengguna'  => $id,
            'username'     => $username,
            'nama_lengkap' => $nama,
            'role'         => 'user',
        ]);

        set_flash('success', 'Registrasi berhasil! BMI awal Anda telah dihitung.');
        $this->redirect(BASE_URL . '/profil');
    }

    /* ==================== LOGOUT ==================== */

    public function logout(): void
    {
        Auth::logout();
        set_flash('success', 'Anda telah keluar dari sistem.');
        $this->redirect(BASE_URL . '/');
    }
}
