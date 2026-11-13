<?php
/**
 * Autentikasi & manajemen sesi — Single Login (deteksi role otomatis).
 */
class Auth
{
    /**
     * Coba login. Mengembalikan array['role'=>..., 'user'=>...] atau null.
     */
    public static function attempt(string $username, string $password): ?array
    {
        $user = User::findByUsername($username);
        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }
        self::startSession();
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id_pengguna'   => (int) $user['id_pengguna'],
            'username'      => $user['username'],
            'nama_lengkap'  => $user['nama_lengkap'] ?? $user['username'],
            'role'          => $user['role'],
        ];
        return $_SESSION['user'];
    }

    public static function login(array $data): void
    {
        self::startSession();
        session_regenerate_id(true);
        $_SESSION['user'] = $data;
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function isAdmin(): bool
    {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }

    public static function isUser(): bool
    {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'user';
    }

    public static function userId(): ?int
    {
        return isset($_SESSION['user']['id_pengguna']) ? (int) $_SESSION['user']['id_pengguna'] : null;
    }

    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            set_flash('warning', 'Anda harus login sebagai admin.');
            redirect(BASE_URL . '/auth/login');
        }
    }

    public static function requireUser(): void
    {
        if (!self::isUser()) {
            set_flash('warning', 'Silakan login terlebih dahulu.');
            redirect(BASE_URL . '/auth/login');
        }
    }

    public static function logout(): void
    {
        self::startSession();
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }

    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
