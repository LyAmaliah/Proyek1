<?php
/** Model untuk tabel `users` (gabungan admin + pengguna). */
class User extends Model
{
    protected static string $table = 'users';
    protected static string $primaryKey = 'id_pengguna';

    public static function findByUsername(string $username): ?array
    {
        $st = static::db()->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $st->execute([$username]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function usernameExists(string $username, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE username = ?';
        $params = [$username];
        if ($exceptId) {
            $sql .= ' AND id_pengguna != ?';
            $params[] = $exceptId;
        }
        $st = static::db()->prepare($sql);
        $st->execute($params);
        return (int) $st->fetchColumn() > 0;
    }

    public static function count(): int
    {
        return (int) static::db()->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
    }

    public static function terbaru(int $limit = 5): array
    {
        $st = static::db()->prepare(
            "SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT " . (int) $limit
        );
        $st->execute();
        return $st->fetchAll();
    }

    public static function cari(?string $q = null): array
    {
        $sql = "SELECT * FROM users WHERE role = 'user'";
        $params = [];
        if ($q !== null && $q !== '') {
            $sql .= ' AND (nama_lengkap LIKE ? OR username LIKE ?)';
            $params = ["%$q%", "%$q%"];
        }
        $sql .= ' ORDER BY id_pengguna ASC';
        $st = static::db()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }
}
