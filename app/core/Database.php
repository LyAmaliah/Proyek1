<?php
/**
 * Koneksi database MySQL (PDO) — singleton.
 * Konfigurasi diambil dari app/config/config.php
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $dsn = 'mysql:host=' . DB_HOST
            . ';port=' . DB_PORT
            . ';dbname=' . DB_NAME
            . ';charset=' . DB_CHARSET;

        try {
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $ex) {
            http_response_code(500);
            exit(
                '<div style="font-family:Poppins,Arial,sans-serif;max-width:640px;margin:60px auto;padding:24px;border:1px solid #e0e0e0;border-radius:12px">'
                . '<h2 style="color:#27824B">Koneksi database gagal</h2>'
                . '<p>Pastikan <b>MySQL (XAMPP)</b> sudah berjalan, lalu impor file '
                . '<b>database/healthyhabit.sql</b> ke phpMyAdmin.</p>'
                . '<p style="color:#c0392b"><code>' . e($ex->getMessage()) . '</code></p>'
                . '</div>'
            );
        }

        return self::$pdo;
    }

    /** Cegah instansiasi langsung. */
    private function __construct()
    {
    }
}
